<?php

namespace App\Console\Commands\Rutan;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Aprende\EvaluacionAsesoria,
    App\Models\Aprende\ItemMentoria,
    App\Models\Core\Asesoria,
    App\Models\Core\Convocatoria,
    App\Models\Core\ConvocatoriaMentoria,
    App\Models\Core\Estado,
    App\Models\Param\Institucion,
    App\Models\Seguridad\Usuario;
    //App\Models\Core\RegistroFaseUsuario,
    //App\Models\Core\Fase,
    //App\Models\Core\EvaluacionEmprendimiento,
    //App\Models\Core\ResultadoEvaluacionEmprendimiento;

use Log, Storage, Excel, DB, SendEmail, DateTime;

use App\Console\Commands\Reports\Evaluation;

class MentoringReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:acompaniamientos {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de acompaniamientos';

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
      Log::info('Log_3.1 Iniciando consulta reporte sesiones');

        $evaluation = 28; //Evaluation of Ruta N

        try {
            $header = $data = [];

            // Valid arguments
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $institution = Institucion::find($this->argument('institution'));

            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria){
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            \Log::info("Inicia Consulta \n");

            $query = Asesoria::query();
            $query->select('asesoria.codigo_asesoria', 'descripcion_asesoria',
                'fecha_asesoria',
                'hora_asesoria',
                'contador_discusiones_asesoria',
                'asesoria_institucion.codigo_estado',
                'emprendimiento.nombre_usuario as nombre_emprendimiento',
                'emprendimiento.codigo_usuario as id_entity',
                DB::raw("initcap(nombre_estado) as nombre_estado"),
                DB::raw("initcap( (CASE mentor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN mentor.nombre_usuario || ' ' || COALESCE(pmentor.primer_apellido_persona, '') || ' ' || COALESCE(pmentor.segundo_apellido_persona, '') ELSE mentor.nombre_usuario END) ) as nombre_mentor"),
                DB::raw("initcap( (CASE emprendedor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN emprendedor.nombre_usuario || ' ' || COALESCE(pemprendedor.primer_apellido_persona, '') || ' ' || COALESCE(pemprendedor.segundo_apellido_persona, '') ELSE emprendedor.nombre_usuario END) ) as nombre_emprendedor"),
                DB::raw("(CURRENT_DATE - fecha_asesoria ) as dias"),
                'emprendedor.identificacion_usuario'
            );
            $query->join('aprende.asesoria_institucion', function($join) use($institution){
                $join->on('asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
                    ->where('asesoria_institucion.codigo_institucion', '=', $institution->codigo_institucion);
            });

            $query->join('core.estado', 'asesoria_institucion.codigo_estado', '=', 'estado.codigo_estado');

            $query->join('core.tipo_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tipo_asesoria.codigo_tipo_asesoria');

            // Mentor
            $query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
            $query->leftJoin('core.persona as pmentor', 'mentor.codigo_usuario', '=', 'pmentor.codigo_usuario');

            // Entrepreneur
            $query->join('seguridad.usuario as emprendedor', 'asesoria.codigo_usuario', '=', 'emprendedor.codigo_usuario');
            $query->leftJoin('core.persona as pemprendedor', 'emprendedor.codigo_usuario', '=', 'pemprendedor.codigo_usuario');

            // Entrepreneurship
            $query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
            $query->join('seguridad.usuario as emprendimiento', 'entidad.codigo_usuario', '=', 'emprendimiento.codigo_usuario');

            $query->orderby('codigo_asesoria','asc');

            $registers = $query->get();

            $questions = ItemMentoria::itemsEvaluation((int) $institution->codigo_institucion, (int) EvaluacionAsesoria::typeEvaluation('adviser'));

            Log::info('Log_4.1 finaliza consulta acompaniamientos,creando encabezados');

            // Prepare header
            $header = [];
            $header[] = 'Nº acompañamiento';
            $header[] = 'Fecha de evaluación';
            $header[] = 'Nª postulación';
            $header[] = 'Etapa del proceso';
            $header[] = 'Número documento emprendedor';
            $header[] = 'Nombre emprendedor';
            $header[] = 'Nombre entidad';
            $header[] = 'Acompañante evaluado';



           // Build headers
            foreach ($questions as $item_question) {
                if (isset($item_question->nombre_item_general_mentoria) && !empty($item_question->nombre_item_general_mentoria)){
                    $header[] = strip_tags($item_question->nombre_item_general_mentoria);
                }else {
                    $header[] = strip_tags($item_question->nombre_item_general_mentoria);
                }
            }

            $header[] = 'Observación';


            //Prepare data
            $data = collect([]);

            foreach ($registers as $register)
            {
                $answers = $this->accompanyingEvaluation((int) $register->codigo_asesoria);
                $phase = $this->getPhase($program->codigo_convocatoria, $institution->codigo_institucion, $register->codigo_asesoria, $register->id_entity);


                $date_evaluation = $comentary = '';

                if (count($answers)>0) {
                    foreach ($answers as $item) {

                        $date_evaluation = $item->fecha_evaluacion_asesoria;
                        $comentary = $item->comentario_evaluacion_asesoria;
                    }

                    unset($record);

                    $record   = [];
                    $record[] = $register->codigo_asesoria;
                    $record[] = $date_evaluation == '' ? 'Pendiente por evaluar': $date_evaluation;
                    $record[] = $phase['postulacion'];
                    $record[] = $phase['fase'];
                    $record[] = $register->identificacion_usuario;
                    $record[] = mb_convert_case($register->nombre_emprendedor, MB_CASE_TITLE, "UTF-8");
                    $record[] = $register->nombre_emprendimiento;
                    $record[] = mb_convert_case($register->nombre_mentor, MB_CASE_TITLE, "UTF-8");

                    if (count($answers)>0) {
    
                        foreach ($answers as $item_answers) {
                            if (    $item_answers->codigo_item_general_mentoria == 33
                                    || $item_answers->codigo_item_general_mentoria == 34
                                    || $item_answers->codigo_item_general_mentoria == 35
                                    || $item_answers->codigo_item_general_mentoria == 36
                                    || $item_answers->codigo_item_general_mentoria == 37
                                )
                            {
                                            $record[] = $item_answers->evaluacion_item_asesoria;
                            }
                        }
                    }
                    else {
                        for ($i=0; $i < 5; $i++) {
                            $record[] = 'Pendiente evaluación';
                        }
                    }
                    $record[] = $comentary == '' ? 'Pendiente por evaluar': $this->mb_ucfirst($comentary);

                    $data[] = $record;
                }
            }


            \Log::info("Fin Consulta \n");
            // Prepare file
            Log::info('Log_5.1 creando ruta del excel acompaniamientos');

            $filename = sprintf('%s_%s_%s_%s.xlsx', 'accompaniments', '_general', $program->url_convocatoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data) {
                $excel->sheet('data', function($sheet) use($header, $data) {
                    // Titles
                    $sheet->appendRow($header);
                    // Data
                    $sheet->rows($data);
                });
            })->string('xlsx');
            \Log::info("Creo excel \n");

            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte general convocatoria ruta emprendimiento File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
            \Log::info("Cargo excel \n");
            // Send mail
            Log::info('Log_6.1 creacion excel, preparando correo');
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "REPORTE EVALUACIÓN ACOMPAÑAMIENTOS";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

            \Log::info("Envio correo => reporte acompañamientos \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportConvocatoriasRutadelEmprendimiento:command', $e->getMessage()));
            abort(500);
        }
    }

    private function accompanyingEvaluation(int $id_advisory, int $id_type = 2, int $id_institution = 61)
	{
        $query = EvaluacionAsesoria::select('codigo_evaluacion_asesoria');
        $query->where('codigo_asesoria', $id_advisory);
        $query->orderBy('codigo_evaluacion_asesoria', 'desc');
        $id = $query->first();

        $query = EvaluacionAsesoria::query();
        $query->join('aprende.cumpli_items_asesoria as item', 'evaluacion_asesoria.codigo_evaluacion_asesoria', '=', 'item.codigo_evaluacion_asesoria');
		$query->join('aprende.item_general_mentoria as itemg', 'itemg.codigo_item_general_mentoria', '=', 'item.codigo_item_general_mentoria');
        //$query->where('evaluacion_asesoria.codigo_asesoria', $id_advisory);
        $query->where('evaluacion_asesoria.tipo_evaluacion_asesoria', $id_type);
        $query->where('itemg.codigo_institucion', $id_institution);
        $query->where('evaluacion_asesoria.codigo_evaluacion_asesoria', $id['codigo_evaluacion_asesoria']);
        $query->orderBy('itemg.codigo_item_general_mentoria', 'asc');
        $data = $query->get();

        return $data;
    }

    private function getPhase(int $id_announcement = 634, int $id_institution = 61, int $id_mentoring, int $id_entity)
	{
        $query = ConvocatoriaMentoria::query();
        $query->select('f.nombre_fase as fase', 'rc.numero_registro_convocatoria as postulacion');
        $query->leftJoin('core.fase as f', 'f.codigo_fase', '=', 'convocatoria_mentoria.codigo_fase');
        $query->leftJoin('core.registro_convocatoria as rc', 'rc.codigo_usuario', '=', 'convocatoria_mentoria.codigo_usuario');
        $query->where('convocatoria_mentoria.codigo_asesoria', $id_mentoring);
        $query->where('convocatoria_mentoria.codigo_convocatoria', $id_announcement);
        $query->where('convocatoria_mentoria.codigo_institucion', $id_institution);
        $query->where('convocatoria_mentoria.codigo_usuario', $id_entity);

        $query = $query->get();
        $count = $query->count();

        $data = [];

        if ($count > 0){

            $data['fase'] = is_null($query[0]->fase) ? 'No aplica mentoria para esta fase' : $query[0]->fase;
            $data['postulacion'] = is_null($query[0]->postulacion) ? 'No aplica mentoria para esta fase' : $query[0]->postulacion;
        } else {
            $data['fase'] = 'No aplica fase para mentoria';
            $data['postulacion'] = 'No aplica fase para mentoria';
        }

        return $data;

    }

    private function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = true) {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = "";
        if ($lower_str_end) {
          $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }
        else {
          $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
      }
}
