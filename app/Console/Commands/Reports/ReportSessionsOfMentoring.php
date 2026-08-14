<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
use App\Models\Core\Estado,
	App\Models\Aprende\Mentoria,
    App\Models\Aprende\TareaMentoria,
    App\Models\Aprende\TagSesionMentoria,
    App\Models\Core\Asesoria,
    App\Models\Seguridad\Usuario,
    App\Models\Param\Institucion;
    use App\Exports\ReportsExport;
use Log, Storage, Excel, DB, SendEmail;

class ReportSessionsOfMentoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:sessionsmentoring {user} {institution} {codigo_asesoria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de sesiones de mentoria para toda la app';

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

            $codigo_asesoria = $this->argument('codigo_asesoria');

            if(!isset($codigo_asesoria)) {
                throw new \Exception("Error recuperando codigo_asesoria");
            }
            // Valid arguments
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            \Log::info("Inicia Consulta \n");
            //
            $query = Mentoria::query();
            $query->select(
                'mentoria.codigo_mentoria AS codigo_sesion',
                'mentoria.duration_mentoring',
                'mentoria.codigo_asesoria',
                'mentoria.codigo_estado',
                'mentoria.fecha_finalizo_mentoria',
                'mentoria.fecha_mentoria',
                'mentoria.descripcion_mentoria',
                'mentoria.objetivo_mentor AS observaciones',
                'evento.nombre_evento',
                'evento.fecha_evento',
                'evento.hora_inicio_evento',
                'evento.fecha_fin_evento',
                'tema_principal_evento.nombre_tema_principal_evento'
            );

            //hora evento o sesion
            $query->join('core.evento AS evento', 'evento.codigo_evento', '=', 'mentoria.codigo_evento');

            //tema principal
            $query->leftjoin('core.tema_principal_evento', 'tema_principal_evento.codigo_tema_principal_evento',
                '=', 'evento.codigo_tema_principal_evento');

            $query->where('mentoria.codigo_asesoria', $codigo_asesoria);
            $mentorings = $query->get();

            $mentor = Asesoria::find($codigo_asesoria);

            // $attached_advisory;
            if ( $mentor instanceof Asesoria){
                $attached = $mentor->attachedAdvice()->get();

                if ($attached->count() > 0) {

                    foreach ($attached as $value) {
                        $output[] = trim($value->direccion_adjunto);
                    }

                    if ($attached->count() > 1) {
                        $attached_advisory = implode(" , \n", $output);
                    } else {
                        $attached_advisory = $output;
                    }

                } else {
                    $attached_advisory = 'Sin adjuntos';
                }

            }else{
                $attached_advisory = 'Sin adjuntos';
            }

            // Prepare header
            $header = [];
            $header[] = 'Código sesión';
            $header[] = 'Titulo de sesión';
            $header[] = 'Fecha programada de sesión';
            $header[] = 'Fecha de finalización';
            $header[] = 'Fecha de aceptación';
            $header[] = 'Estado';
            $header[] = 'Descripción de sesión';
            $header[] = 'Tema';
            $header[] = 'Acuerdos/Plan acción';
            $header[] = 'Información emprendimiento';
            $header[] = 'Observaciones';
            $header[] = 'Horas de duración';
            $header[] = 'Archivos adjuntos en la Asesoria';

             //Prepare data
            $data = collect([]);
            foreach ($mentorings as $mentoring)
            {

                if (isset($mentoring->duration_mentoring)) {

                    $hours = explode(',', trim($mentoring->duration_mentoring));

                    if ($hours[0] == '') {
                        $hours[0] = 0;
                    }
                    if ($hours[1] == '') {
                        $hours[1] = 1;
                    }

                    $text_hours = $hours[0] > 1 ? 'horas' : 'hora';

                    $mentoring->duration_mentoring = "$hours[0] $text_hours y $hours[1] minutos";
                } else {
                    $mentoring->duration_mentoring = '---';
                }

                $acuerdos = TareaMentoria::select('descripcion_tarea_mentoria')
                                  ->where('codigo_mentoria', $mentoring->codigo_sesion )
                                  ->get();
                $infoEmpren = TagSesionMentoria::select(DB::raw("CONCAT( nombre_tag_sesion_mentoria, ': ', valor_tag_sesion_mentoria ) as informacion_emprendimiento"))
                                  ->where('codigo_mentoria', $mentoring->codigo_sesion )
                                  ->get();

                if (count($acuerdos) > 0) {
                    $acuerdos_plan_accion = [];
                    foreach ($acuerdos as $value) {
                        $acuerdos_plan_accion[] = $value->descripcion_tarea_mentoria;
                    }
                    $acuerdos_plan_accion1 = "- " . implode("\n- ", $acuerdos_plan_accion);
                    $mentoring->acuerdos_plan_accion = $acuerdos_plan_accion1;

                }else {
                    $mentoring->acuerdos_plan_accion = '---';
                }

                if (count($infoEmpren) > 0) {
                    $informacion_emprendimiento = [];
                    foreach ($infoEmpren as $value) {
                        $informacion_emprendimiento[] = $value->informacion_emprendimiento;
                    }
                    $informacion_emprendimiento1 = "- " . implode("\n- ", $informacion_emprendimiento);
                    $mentoring->informacion_emprendimiento = $informacion_emprendimiento1;

                }else {
                    $mentoring->informacion_emprendimiento = '---';
                }


                unset($record);
                $record[] = $mentoring->codigo_sesion;
                $record[] = $mentoring->nombre_evento;
                $record[] = $mentoring->fecha_evento;
                $record[] = $mentoring->fecha_fin_evento;
                $record[] = $mentoring->fecha_finalizo_mentoria;
                $record[] = Estado::getNameState($mentoring->codigo_estado);
                $record[] = $mentoring->descripcion_mentoria;
                $record[] = $mentoring->nombre_tema_principal_evento;
                $record[] = $mentoring->acuerdos_plan_accion;
                $record[] = $mentoring->informacion_emprendimiento;
                $record[] = $mentoring->observaciones;
                $record[] = $mentoring->duration_mentoring;
               // $record[] = $mentoring->attached;


                $data[] = $record;
            }

            \Log::info("Fin Consulta \n");
            // Prepare file

            $filename = sprintf('%s_%s_%s.xlsx', 'Reporte de sesiones de mentoria', $codigo_asesoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            // $file = Excel::create($filename, function($excel) use($header, $data, $attached_advisory) {
            //     $excel->sheet('Aplicaciones', function($sheet) use($header, $data, $attached_advisory) {
            //         // Titles
            //         $sheet->appendRow($header);
            //         // Data
            //         $sheet->rows($data);

            //        $sheet->cell('M2', function($cell) use ($attached_advisory) {
            //         // manipulate the cell
            //             $cell->setValue($attached_advisory);
            //         });

            //         // Titles
            //        // $sheet->row('COLUMNA ADD');

            //      });
            // })->string('xlsx');

            $export = new ReportsExport([
              $header], [$data]
            );

            // Genera el archivo Excel y guárdalo en storage/app/reports
            $file = Excel::store($export,$filepath,'s3', null, [
              'visibility' => 'public',
          ]);

          $fileUrl = Storage::disk('s3')->url($filepath);

            \Log::info("Creo excel \n");

            // Store file to s3
            // Storage::put($filepath, $file, 'public');

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte sesiones de mentoria $codigo_asesoria";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

            \Log::info("Envio correo \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportSessionsOfMentoring:command', $e->getMessage()));
            abort(500);
        }
    }
}
