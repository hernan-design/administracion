<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
use App\Exports\ReportsCustomExport;

use Log, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
    App\Models\Aprende\Curso,
    App\Models\Param\Institucion,
    App\Models\Core\Estado,
    App\Models\Seguridad\Rol,
    App\Models\Aprende\AdjuntoTema;

class CursoIndividual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:curso_individual {user} {institution} {codigo_curso}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte en excel de usuarios inscritos a un curso';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $header = $data = $matriz = [];

            // Valid user
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                $message = sprintf(' %s: %s %s', 'App\Console\Commands\Reports\Curso', 'Error recuperando usuario id', $this->argument('institution'));
                throw new \Exception($message);
            }

            $institucion = Institucion::find($this->argument('institution'));
            if(!$institucion instanceof Institucion) {
                $message = sprintf(' %s: %s %s', 'App\Console\Commands\Reports\Curso', 'Error recuperando institucion id', $this->argument('institution'));
                throw new \Exception($message);
            }

            $curso = Curso::getCourseUsers($institucion, $this->argument('codigo_curso'));

            $getSectionsOfCourse = Curso::select('curso.codigo_curso', 'tema_curso.nombre_tema_curso', 'tema_curso.codigo_tema_curso')
                                    ->join('aprende.tema_curso as tema_curso',
                                        'tema_curso.codigo_curso','=','curso.codigo_curso')
                                    ->join('aprende.adjunto_tema',
                                        'adjunto_tema.codigo_tema_curso','=','tema_curso.codigo_tema_curso')
                                    ->where('curso.codigo_curso','=', $this->argument('codigo_curso'))
                                    ->where('curso.codigo_estado','=', Estado::getState('active'))
                                    ->groupBy('curso.codigo_curso', 'tema_curso.nombre_tema_curso', 'tema_curso.codigo_tema_curso')
                                    ->get();


            // Prepare header
            $header = [];
            $header[] = 'Nombre Usuario';
            $header[] = 'Primer Apellido';
            $header[] = 'Segundo Apellido';
            $header[] = 'Tipo de Identificacion';
            $header[] = 'Identificacion';
            $header[] = 'Correo Electronico';
            $header[] = 'Movil';
            $header[] = 'Telefono';
          //  $header[] = 'Curso';
            $header[] = 'Ciudad';
            $header[] = 'Fecha Inscripcion';
            $header[] = 'Hora Inscripcion';

            if ($getSectionsOfCourse->count() > 0) {
                foreach ($getSectionsOfCourse as $key => $value) {
                    $header[] = $value->nombre_tema_curso;
                }
            }

            $header[] = 'Porcentaje de avance total';


            //Prepare data
            $data = collect([]);
            foreach ($curso as $register)
            {
                unset($record);
                $record[] = $register->nombre_persona;
                $record[] = $register->primer_apellido;
                $record[] = $register->segundo_apellido;
                $record[] = $register->tipo_identificacion_usuario;
                $record[] = $register->identificacion_usuario;
                $record[] = $register->correo_electronico;
                $record[] = $register->movil_usuario;
                $record[] = $register->telefono_usuario;
               // $record[] = $register->nombre_curso;
                $record[] = $register->nombre_ciudad;
                $record[] = $register->fecha_inscripcion;
                $record[] = $register->hora_inscripcion;

                if ($getSectionsOfCourse->count() > 0) {
                    foreach ($getSectionsOfCourse as $key => $value) {

                        $porcentaje_avance = $this->getPercentageTemaOfCourse($value->codigo_tema_curso, $register->codigo_usuario);
                        $record[] =  $porcentaje_avance . "% completado";
                    }
                }

                $record[] = $register->porcentaje_progreso;

                $data[] = $record;
            }


            // Prepare file
            $filename = sprintf('%s_%s_%s_%s.xlsx', 'Curso', $curso[0]->nombre_curso, $curso[0]->codigo_curso, date('Ymd'));
            $filepath = sprintf('reports/%s/%s/%s', $institucion->codigo_institucion, $user->codigo_usuario, $filename);

            $export = new ReportsCustomExport([
              $header], [$data]
            );

            // Generar el archivo Excel y guardarlo en storage/app/reports
            Excel::store($export, $filepath, 's3', null, [
                'visibility' => 'public',
            ]);

            // Obtener la URL del archivo almacenado
            $fileUrl = Storage::disk('s3')->url($filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = 'Reporte de curso - cod: ' . $curso[0]->codigo_curso;
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institucion->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            $this->error($e->getMessage());
            Log::error(sprintf('%s:%s', 'ReportsCurso:Command', $e->getMessage()));
            abort(500);
        }
    }

    public static function getPercentageTemaOfCourse(int $codigo_tema_curso, int $register)
    {

        $getPercentageTemaOfCourse = DB::select(
            "SELECT ((count(estado_adjunto_usuario.codigo_estado_adjunto) * 100) / count(adjunto_tema.codigo_adjunto)) as porcentaje
            FROM aprende.adjunto_tema
            LEFT JOIN aprende.estado_adjunto_usuario
                on adjunto_tema.codigo_tema_curso = estado_adjunto_usuario.codigo_tema_curso
                and adjunto_tema.codigo_adjunto = estado_adjunto_usuario.codigo_adjunto
                and estado_adjunto_usuario.codigo_usuario =$register
                and estado_adjunto_usuario.codigo_estado = " . Estado::getState('active') . "
            WHERE adjunto_tema.codigo_tema_curso=$codigo_tema_curso"
        );
       /* $getPercentageTemaOfCourse = AdjuntoTema::select(DB::raw('(count(estado_adjunto_usuario.codigo_estado_adjunto) * 100) / count(adjunto_tema.codigo_adjunto)'))
            ->leftJoin('aprende.estado_adjunto_usuario', function ($join) {
            $join->on('adjunto_tema.codigo_tema_curso', '=', 'estado_adjunto_usuario.codigo_tema_curso')
                    ->where('adjunto_tema.codigo_adjunto', '=', 'estado_adjunto_usuario.codigo_adjunto')
                    ->where('estado_adjunto_usuario.codigo_usuario', '=', $this->register)
                    ->where('estado_adjunto_usuario.codigo_estado', '=', Estado::getState('active'));
            })
            ->where('estado_adjunto_usuario.codigo_tema_curso', '=', $codigo_tema_curso)
            ->get();*/

        return $getPercentageTemaOfCourse[0]->porcentaje;
    }

}
