<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
use App\Exports\ReportsCustomExport;
use Illuminate\Support\Facades\Validator;
use Log, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario, App\Models\Aprende\Curso, App\Models\Param\Institucion, App\Models\Core\Estado, App\Models\Seguridad\Rol;

class Cursos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:cursos {user} {institution} {--startAt=} {--endAt=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte en xlsx de cursos y usuarios inscritos en ellos';

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
                $message = sprintf(' %s: %s %s', 'App\Console\Commands\Reports\Cursos', 'Error recuperando usuario id', $this->argument('institution'));
                throw new \Exception($message);
            }

            $institucion = Institucion::find($this->argument('institution'));
            if(!$institucion instanceof Institucion) {
                $message = sprintf(' %s: %s %s', 'App\Console\Commands\Reports\Cursos', 'Error recuperando institucion id', $this->argument('institution'));
                throw new \Exception($message);
            }

            // Prepare data
            $query = Curso::query();
            $startAt = $this->option('startAt');
            $endAt = $this->option('endAt');

            $validator = Validator::make([
              'startAt' => $startAt,
              'endAt' => $endAt
            ], [
              'startAt' => 'required|date_format:Y-m-d',
              'endAt' => 'required|required_with:startAt|date_format:Y-m-d|after_or_equal:startAt'
            ]);

            if (!$validator->fails()) {
              $query->whereBetween('inscripcion_curso.fecha_inscripcion_curso', [$startAt, $endAt]);
            }

            $query->select('curso.nombre_curso as nombre_curso', 'usuario.identificacion_usuario', 'usuario.correo_electronico as correo_electronico', 'usuario.movil_usuario as movil_usuario', 'usuario.telefono_usuario as telefono_usuario','inscripcion_curso.fecha_inscripcion_curso as fecha_inscripcion', 'inscripcion_curso.hora_inscripcion_curso as hora_inscripcion',
                DB::raw('tipo_identificacion.nombre_tipo_identificacion as tipo_identificacion_usuario'),
                DB::raw('initcap(usuario.nombre_usuario) as nombre_persona'),
                DB::raw('initcap(persona.primer_apellido_persona) as primer_apellido'),
                DB::raw('initcap(persona.segundo_apellido_persona) as segundo_apellido'),
                DB::raw('initcap(ciudad.nombre_ciudad) as nombre_ciudad'),
                DB::raw('COALESCE(inscripcion_curso.porcentaje_progreso, 0) AS porcentaje_progreso')
                );
            $query->join('seguridad.usuario as creador','creador.codigo_usuario','=','curso.codigo_usuario');
            $query->join('aprende.curso_institucion as curso_institucion', function($join) use($institucion){
                $join->on('curso_institucion.codigo_curso','=','curso.codigo_curso')
                    ->where('curso_institucion.codigo_institucion','=',  $institucion->codigo_institucion )
                    ->where('curso_institucion.codigo_estado','=',  Estado::getState('active') );
            });
            $query->join('aprende.inscripcion_curso as inscripcion_curso', function($join) use($institucion) {
                $join->on('inscripcion_curso.codigo_curso','=','curso.codigo_curso')
                    ->where('inscripcion_curso.codigo_institucion','=',$institucion->codigo_institucion)
                    ->where('inscripcion_curso.codigo_estado','=',  Estado::getState('active') );
            });
            $query->join('seguridad.usuario','usuario.codigo_usuario','=','inscripcion_curso.codigo_usuario');
			      $query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
            $query->leftJoin('core.tipo_identificacion','tipo_identificacion.codigo_tipo_identificacion','=','usuario.tipo_identificacion_usuario');
            $query->leftJoin('core.ciudad','ciudad.codigo_ciudad','=','usuario.codigo_ciudad');
            $query->where('curso.codigo_estado','=', Estado::getState('active'));
            $query->orderBy('fecha_inscripcion','desc');
            $query->orderBy('hora_inscripcion','desc');

            $cursos = $query->get();

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
            $header[] = 'Curso';
            $header[] = 'Ciudad';
            $header[] = 'Fecha Inscripcion';
            $header[] = 'Hora Inscripcion';
            $header[] = 'Porcentaje completado';

            //Prepare data
            $data = collect([]);
            foreach ($cursos as $register)
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
                $record[] = $register->nombre_curso;
                $record[] = $register->nombre_ciudad;
                $record[] = $register->fecha_inscripcion;
                $record[] = $register->hora_inscripcion;
                $record[] = $register->porcentaje_progreso;

                $data[] = $record;
            }


            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'reporte_cursos', date('YmdHms'));
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
            $data['_subject'] = 'Reporte cursos';
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institucion->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            $this->error($e->getMessage());
            Log::error(sprintf('%s:%s', 'ReportsCursos:Command', $e->getMessage()));
            abort(500);
        }
    }
}
