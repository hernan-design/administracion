<?php

/**
 * Rutina cargue de datos (diagnóstico) para Bolivar.
 *
 * @author Harold Rodríguez <hartuproyecto@gamil.com>
 */

namespace App\Console\Commands\SiinovaBolivar;

use Illuminate\Console\Command;

use App, AWS, DB, Excel, Log, Storage, SendEmail;

use App\Models\Core\AsignacionJurado,
    App\Models\Core\Ciudad,
    App\Models\Core\Encuesta,
    App\Models\Core\Entidad,
    App\Models\Core\Estado,
    App\Models\Core\ItemOpcionEncuesta,
    App\Models\Core\NivelEducacion,
    App\Models\Core\OpcionEncuesta,
    App\Models\Core\OpcionPreguntaEncuesta,
    App\Models\Core\PreguntaEncuesta,
    App\Models\Core\RegistroConvocatoria,
    App\Models\Core\RespuestaEncuesta,
    App\Models\Core\SectorInteres,
    App\Models\Core\ResultadoEvaluacionEmprendimiento,
    App\Models\Core\TipoCampoPregunta,
    App\Models\Core\UsuarioEncuesta,
    App\Models\Core\VerPregEncuesta;

use App\Models\Param\Institucion;

use App\Models\Seguridad\Usuario;

use App\Console\Commands\LoadSurvey\CargaEncuesta;

use App\Console\Commands\LoadEvaluation\CargaEvaluacion;

class LoadDataDiagnosis extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'siinova:loadDiagnosis';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Cargue De Diagnóstico Y Evaluación';

    protected $LogValidateResponses = '';
    protected $LogValidateResponsesEval = '';

    public static $id_group = [58, 59, 60, 61, 62, 69];


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
        Log::info('Iniciando Cargue Diagnóstico Siinova ....');

        define("ID_INSTITUTION", Institucion::institution('siinova'));
        define("ID_SURVEY", 292); //Encuesta diagnóstico 292.
        define("ID_SURVEY_INIT", 287); //Encuesta datos básicos 287.
        define("ID_ANNOUNCEMENT", 652); //Convocatoria 652 diagnóstico bolivar.
        define("ID_ANNOUNCEMENT_INIT", 648); //Convocatoria inicial 648 bolivar.
        define("ID_PHASE", 367); //Fase registro 367 convocatoria diagnóstico.
        define("EMAIL_EVALUATOR", 'angelicasaumeth2@gmail.com'); //Email del evaluador.
        define("ID_EVALUATION", 30); //Codigo evaluacion.


        //Variable $history guarda las acciones ejecutadas en la rutina.
        $history = array();

        Excel::load('diagnosis_bolivar.xlsx', function($reader) use($history) {
            $results = $reader->get()->toArray();

            //array log
            $log = [];
            $log['postulacion'] = '';
            $log['diagnostico'] = '';
            $log['evaluacion']  = '';

            foreach($results as $key => $resul){

                if (self::isFinal($resul)) break;

                Log::info("Registrando postulación: " . $resul['postulacion']);

                $log['postulacion'] = $resul['postulacion'];

                $query = RegistroConvocatoria::query();
                $query->join('core.registro_fase_usuario as rfu', 'rfu.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
                $query->where('registro_convocatoria.numero_registro_convocatoria', $resul['postulacion']);
                $query->where('registro_convocatoria.codigo_convocatoria', ID_ANNOUNCEMENT_INIT);
                $registers = $query->get();

                DB::beginTransaction();
                try {
                        $codigo_usuario_entidad = $registers[0]['codigo_usuario'];
                        //Consulta datos del usuario (entidad).
                        $user_entity = $this->getUserEntity($codigo_usuario_entidad);

                        //Valida respuestas de la convocatoria 652 diagnóstico bolivar.
                        $inscription = $this->validateResponsesOfConvocatory($resul);

                        //Valida si cumple con la estructura de los datos.
                        if ($inscription[0]) {

                            //Carga respuestas de la convocatoria 652 diagnóstico bolivar.
                            $data_for_inscription = new \stdClass();
                            $data_for_inscription->codigo_usuario = $codigo_usuario_entidad;
                            $data_for_inscription->email_evaluator = EMAIL_EVALUATOR;
                            $data_for_inscription->codigo_encuesta = ID_SURVEY;
                            $data_for_inscription->codigo_convocatoria = ID_ANNOUNCEMENT;
                            $data_for_inscription->codigo_institucion = ID_INSTITUTION;
                            $data_for_inscription->id_phase = ID_PHASE;
                            $data_for_inscription->has_evaluation = true;
                            $data_for_inscription->answers_form_convocatory = $inscription[1];

                            //Valida si se realizará una evaluación relacionada a la fase de la convocatoria.
                            if($data_for_inscription->has_evaluation) {
                                $data_for_evaluation_phase = CargaEncuesta::store($data_for_inscription);
                            }else {
                                CargaEncuesta::store($data_for_inscription);
                            }

                            Log::info("Ha realizado el diagnóstico");

                            $log['diagnostico'] = 'Ha realizado el diagnóstico';
                        }
                        else {
                            $log['diagnostico'] = 'No ha realizado el diagnóstico, revisar respuestas';
                        }

                        //Valida respuestas de la evaluación 30 diagnóstico bolivar.
                        $evaluation = $this->validateResponsesEvaluation($resul);

                        //Valida si cumple con la estructura de los datos.
                        if ($evaluation[0]) {

                            $user_evaluator = $this->getUserEvaluator(EMAIL_EVALUATOR);

                            //Carga respuestas de la evaluación 30 diagnóstico bolivar.
                            $data_for_evaluation = new \stdClass();
                            $data_for_evaluation->codigo_asignacion_jurado = $data_for_evaluation_phase->id_assignment_jury;
                            $data_for_evaluation->id_user_evaluator = $user_evaluator->codigo_usuario;
                            $data_for_evaluation->answers_form_evaluation = $evaluation[1];
                            $data_for_evaluation->id_institution = ID_INSTITUTION;
                            $data_for_evaluation->id_evaluation = ID_EVALUATION;

                            $id_answer_evaluation = CargaEvaluacion::update($data_for_evaluation);

                            Log::info('Se efectua la evaluación por: ' . EMAIL_EVALUATOR);

                            $log['evaluacion'] = 'Se efectua la evaluación por: ' . EMAIL_EVALUATOR;

                        }
                        else {
                            $log['evaluacion'] = 'No se efectua la evaluación';
                        }

                        //Consulta datos del usuario (persona).
                        $user_person = $this->getUserPerson($user_entity->codigo_usuario);

                        //Envía notificación cambio de contraseña al usuario (persona) registrado.
                        $data = [];
                        $data['_inst']             = ID_INSTITUTION;
                        $data['_to']               = $user_person->correo_electronico;
                        $data['_to_name']          = mb_strtoupper($user_person->full_name);
                        $data['_subject']          = 'REGISTRO USUARIO';
                        $data['_template']         = 'emails.user.restorepassword';
                        $data['_change_password']  = '/password/email';
                        SendEmail::notification($data);

                        DB::commit();

                        //Consulta los puntajes de la evaluación 30 diagnóstico bolivar.
                        $answers = $this->getScoring($id_answer_evaluation);
                        //Espera durante 1 segundos con el fin de no saturar las consultas.
                        sleep(0.1);
                        //Consulta respuestas de la convocatoria inicial datos básicos 287.
                        $answers_survey_init = $this->getAnswerSurvey(ID_SURVEY_INIT, $codigo_usuario_entidad);
                        sleep(0.1);
                        //C0nsulta respuestas de la evaluación 30 diagnóstico bolivar que se cargó en esta rutina.
                        $answers_evaluation_ini = $this->getAnswerEvaluation($registers[0]['codigo_registro_fase_usuario']);
                        sleep(0.1);
                        //Consulta respuestas de la convocatoria 652 diagnóstico bolivar que se cargó en esta rutina..
                        $data_answers_announcement = $this->getAnswerAnnouncement($data_for_evaluation_phase->id_user_survey);
                        sleep(0.1);
                        //Valida que tipo de scoring genera, sirve para adecuar espacio margin-top.
                        if ($inscription[1][6337] == 13762) {
                            $type_scoring = true; //Aplica cuando es producto.
                        } else {
                            $type_scoring = false; //Aplica cuando es servicio.
                        }

                        //Preparando los datos que contendrá el pdf.
                        $data_pdf = [];
                        $data_pdf['legal_representative'] = mb_convert_case($inscription[1][6480], MB_CASE_TITLE, "UTF-8");
                        $data_pdf['legal_repre_phone_number'] = $inscription[1][6481];
                        $data_pdf['legal_repre_email'] = $inscription[1][6482];
                        $data_pdf['_type_scoring'] = $type_scoring;
                        $data_pdf['_answers_eval'] = $answers;
                        $data_pdf['institution_logo'] = $this->getInstituionLogo(ID_INSTITUTION);
                        $data_pdf['name_entity'] = $user_entity->nombre_usuario;
                        $data_pdf['nit'] = $user_entity->identificacion_usuario;
                        $data_pdf['name_contact'] = $answers_survey_init[6319];
                        $data_pdf['address'] = $answers_survey_init[6320];
                        $data_pdf['phone'] = $answers_survey_init[6321];
                        $data_pdf['municipality'] = $answers_survey_init[6324]['municipio'];
                        $data_pdf['province'] = $answers_survey_init[6324]['departamento'];
                        $data_pdf['email_contact'] = $user_person->correo_electronico;
                        $data_pdf['economic_sector'] = $answers_evaluation_ini[266];
                        $data_pdf['web_page'] = $user_entity->pagina_web_usuario ? $user_entity->pagina_web_usuario : 'No';
                        $data_pdf['title'] = $data_answers_announcement['title'];
                        $data_pdf['survey'] = $data_answers_announcement['survey'];
                        $data_pdf['levels'] = $data_answers_announcement['levels'];
                        $data_pdf['matriz'] = $data_answers_announcement['matriz'];
                        $data_pdf['cities'] = $data_answers_announcement['cities'];
                        $data_pdf['sectors'] = $data_answers_announcement['sectors'];
                        $data_pdf['userSurvey'] =  $data_answers_announcement['userSurvey'];
                        $data_pdf['answers'] =  $data_answers_announcement['answers'];
                        $data_pdf['questions'] = $data_answers_announcement['questions'];
                        $data_pdf['subquestions'] = $data_answers_announcement['subquestions'];
                        $data_pdf['groupings'] = $data_answers_announcement['groupings'];

                        //Prepara pdf
                        $pdf = App::make('dompdf.wrapper');

                        //Valida si la consulta de la evalaución 30 diagnóstico bolivar tiene registros.
                        if ( !is_bool($answers) ) {
                            $pdf->loadHTML(view('emails.diagnosis.siinovapdf.layout_1', $data_pdf));

                            $s3 = AWS::createClient('s3');
                            $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');
                            if(!$s3->doesBucketExist($bucket)) {
                                $s3->createBucket(['Bucket' => $bucket]);
                            }

                            $file_name = sprintf('%s_%s_%s.pdf', 'diagnostico', $user_person->codigo_usuario.'_', ID_INSTITUTION);
                            $key = sprintf('%s/%s/%s/%s', 'documents', 'diagnosticos', 'siinova', $file_name);

                            $result = $s3->putObject([
                                'Bucket' => $bucket,
                                'Key'    => $key,
                                'Body'   => $pdf->output(),
                                'ACL'    => 'public-read'
                            ]);

                            $url_pdf = $result['ObjectURL'];

                            //Envía correo con diagnóstico adjunto .pdf .
                            $data = [];
                            $data['_inst']     = ID_INSTITUTION;
                            $data['_to']       = $user_person->correo_electronico;
                            $data['_to_name']  = mb_strtoupper($user_person->full_name);
                            $data['_subject']  = 'REPORTE DIAGNÓSTICO';
                            $data['_template'] = 'emails.user.diagnosissiinova';
                    		$data['_id_eval_result']  = $id_answer_evaluation;
                            $data['_fileurl'] = $url_pdf;
                            SendEmail::notification($data);

                            $log['notificacion'] = 'Se envío correo del diagnositco en pdf al emprendedor';
                            Log::info('Se envío correo del diagnositco en pdf al emprendedor');
                        }
                        else {
                            $log['notificacion'] = 'No envío correo del diagnositco en pdf al emprendedor';
                            Log::info('No envío correo del diagnositco en pdf al emprendedor');
                        }
                    }
                    catch (\Exception $e) {
                        DB::rollback();
                        Log::info('Error catch LoadDataDiagnosis Siinova');
                        Log::error($e->getMessage());
                        die;
                    }

                $history[] = [
                    'correo'          => $user_person->correo_electronico,
                    'postulacion'     => $log['postulacion'],
                    'diagnostico'     => $log['diagnostico'],
                    'evaluacion'      => $log['evaluacion'],
                    'notificacion'    => $log['notificacion']
                ];
            }

            $filename = sprintf('%s_%s.xlsx', 'reporte_cargue_datos', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s', ID_INSTITUTION, $filename);


            $file = Excel::create($filename, function($excel) use ($history) {
                $excel->sheet('data', function($sheet)  use($history) {
                    $sheet->setAllBorders('thin');
                    $array = array(
                        'Correo',
                        'Postulacion Inicial',
                        'Diagnostico',
                        'Evaluacion',
                        'Notificación Diagnóstico'
                    );

                    $sheet->rows(array(
                        $array,
                    ));

                    foreach ($history as $value) {
                        $sheet->appendRow([
                            $value['correo'],
                            $value['postulacion'],
                            $value['diagnostico'],
                            $value['evaluacion'],
                            $value['notificacion']
                        ]);
                    }

                });
            })->string('xlsx');

            // Store file to s3
            Storage::put($filepath, $file, 'public');
            // File valid
            if(!Storage::has($filepath)) {
            throw new \Exception("Error recuperando xls reporte ($filepath).");
            }
            // Get url
            $fileurl = AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            //Envía notificación a los administradores de bolivar con copia a tuproyecto.
            $data = [];
            $data['_inst'] = ID_INSTITUTION;
            $data['_to'] = 'monica.carrillo@ocaribe.email';
            $data['to_copy'] = ['administracion@tuproyecto.com', "testertuproyecto@gmail.com"];
            $data['_to_name'] = 'BOLIVAR SIINOVA';
            $data['_subject'] = 'CARGA DE DATOS DIAGNOSTICO';
            $data['_template'] = 'emails.user.reportnotification';
            $data['_fileurl'] = $fileurl;

            SendEmail::notification($data);

        });
        Log::info("Finalizo");
    }
    /**
     * Valida las respuestas adjuntas en .xslx de la convocatoria.
     * @access private
     * @param array $resul
     * @return array
     */
    private function validateResponsesOfConvocatory($resul): array
    {
        Log::info('Validando datos de la convocatoria...');

        $history = array();
        $homologate = array();

        $data_survey = [];
        $data_survey[6336] = trim($resul['descripcion_actividad_economica']);
        $data_survey[6337] = $this->getIdOption($resul['actividad_economica'], 2);
        $data_survey[6338] = trim($resul['servicio_principal']);
        $data_survey[6339] = trim($resul['producto_principal']);
        $data_survey[6340] = $this->getIdOption($resul['produce_subproducto'], 2);
        $data_survey[6341] = trim($resul['indique_subproducto']);
        $data_survey[6342] = $this->getIdOption($resul['centros_acopio'], 2);
        $data_survey[6343] = $this->getIdOption($resul['planta_transformacion'], 2);
        $data_survey[6344] = $this->getIdOption($resul['planea_produccion'], 2);
        $data_survey[6345] = $this->getIdOption($resul['linea_proceso'], 2);
        $data_survey[6346] = $this->getIdOption($resul['obstaculos_linea'], 2);
        $data_survey[6347] = $this->getIdOption($resul['acciones_mejora'], 2);
        $data_survey[6348] = $this->getIdOption($resul['conoce_calidad_minima'], 2);
        $data_survey[6349] = $this->getIdOption($resul['conoce_calidad_diferencial'], 2);
        $data_survey[6350] = $this->getIdOption($resul['implemento_mejoras'], 2);
        $data_survey[6351] = $this->getIdOption($resul['producto_cumple'], 2);
        $data_survey[6352] = $this->getIdOption($resul['materias_primas_cumplen'], 2);
        $data_survey[6353] = $this->getIdOption($resul['conoce_tramites_permisos'], 2);
        $data_survey[6354] = trim($resul['comentario_tramites_permisos']);
        $data_survey[6355] = trim($resul['requisitos_no_cumple']);
        $data_survey[6356] = $this->getIdOption($resul['identifca_proveedores'], 2);
        $data_survey[6357] = $this->getIdOption($resul['acuerdos_proveedores'], 2);
        $data_survey[6358] = $this->getIdOption($resul['nueva_organizacion_21_22'], 2);
        $data_survey[6359] = $this->getIdOption($resul['nuevo_bien_servicio_nacional'], 2);
        $data_survey[6360] = $this->getIdOption($resul['nuevo_bien_servicio_internacional'], 2);
        $data_survey[6361] = $this->getIdOption($resul['indetifica_recursos'], 2);
        $data_survey[6365] = $this->getIdOption($resul['realiza_control_calidad'], 2);
        $data_survey[6362] = trim($resul['indique_control']);
        $data_survey[6363] = $this->getIdOption($resul['implementa_adecuaciones'], 2);
        $data_survey[6364] = trim($resul['adecuaciones_requeridas']);
        $data_survey[6366] = $this->getIdOption($resul['controla_tiempos_produccion'], 2);
        $data_survey[6367] = $this->getIdOption($resul['controles_en_proceso'], 2);
        $data_survey[6368] = $this->getIdOption($resul['reduce_residuos'], 2);
        $data_survey[6369] = $this->getIdOption($resul['tiene_estructura_organizativa'], 2);
        $data_survey[6370] = $this->getIdOption($resul['empleados_afiliados_aportes'], 2);
        $data_survey[6371] = $this->getIdOption($resul['evalua_caja_capital'], 2);
        $data_survey[6372] = $this->getIdOption($resul['desarrollo_proyecto_innovacion'], 2);
        $data_survey[6374] = trim($resul['describa_proyecto_innovacion']);

        $resp_6373 = $this->getIdOption($resul['estado_proyecto'], 2);
        $data_survey[6373] = $resp_6373 ?: '14406';

        if ($resul['areas_innovacion_producto_servicio'] == '14034-Mejoramiento de producto(s) y/o servicio(s) (calidad, funcionalidad, presentación).') {
            $data_survey[6464] = $this->getOption('14034');
        }elseif ($resul['areas_innovacion_producto_servicio'] == '14035-Desarrollo de nuevos productos y servicios exitosos.') {
            $data_survey[6464] = $this->getOption('14035');
        }elseif ($resul['areas_innovacion_producto_servicio'] == '14034-Mejoramiento de producto(s) y/o servicio(s) (calidad, funcionalidad, presentación).14035-Desarrollo de nuevos productos y servicios exitosos.') {
            $data_survey[6464] = $this->getOption('14034-14035');
        } else {
            $data_survey[6464] = $this->getOption('14405');
        }

        if ($resul['areas_innovacion_mercado'] == '14036-Mantener su nivel de participación en el mercado.') {
            $data_survey[6465] = $this->getOption('14036');
        }elseif ($resul['areas_innovacion_mercado'] == '14037-Ingresar a nuevos nichos de mercado.') {
            $data_survey[6465] = $this->getOption('14037');
        }elseif ($resul['areas_innovacion_mercado'] == '14036-Mantener su nivel de participación en el mercado.14037-Ingresar a nuevos nichos de mercado.') {
            $data_survey[6465] = $this->getOption('14036-14037');
        } else {
            $data_survey[6465] = $this->getOption('14404');
        }

        $data_survey[6466] = $resul['areas_innovacion_procesos'] ? $this->getOption($resul['areas_innovacion_procesos']) : $this->getOption('14403');
        $data_survey[6467] = trim($resul['otros_impactos']);

        //[6375]pregunta [13839]columna [13829]fila
        $data_survey[6375][13839][13829] = trim($resul['personas_doctorado_2021']);
        $data_survey[6375][13840][13829] = trim($resul['personas_doctorado_2022']);
        $data_survey[6375][13839][13830] = trim($resul['personas_maestria_2021']);
        $data_survey[6375][13840][13830] = trim($resul['personas_maestria_2022']);
        $data_survey[6375][13839][13831] = trim($resul['personas_especializacion_2021']);
        $data_survey[6375][13840][13831] = trim($resul['personas_especializacion_2022']);
        $data_survey[6375][13839][13832] = trim($resul['personas_universitario_2021']);
        $data_survey[6375][13840][13832] = trim($resul['personas_universitario_2022']);
        $data_survey[6375][13839][13833] = trim($resul['personas_tecnologo_2021']);
        $data_survey[6375][13840][13833] = trim($resul['personas_tecnologo_2022']);
        $data_survey[6375][13839][13834] = trim($resul['personas_tecnico_2021']);
        $data_survey[6375][13840][13834] = trim($resul['personas_tecnico_2022']);
        $data_survey[6375][13839][13835] = trim($resul['personas_secundaria_2021']);
        $data_survey[6375][13840][13835] = trim($resul['personas_secundaria_2022']);
        $data_survey[6375][13839][13836] = trim($resul['personas_primaria_2021']);
        $data_survey[6375][13840][13836] = trim($resul['personas_primaria_2022']);
        $data_survey[6375][13839][13837] = trim($resul['personas_ninguno_2021']);
        $data_survey[6375][13840][13837] = trim($resul['personas_ninguno_2022']);
        //Suma el total de personas 2021.
        $data_survey[6375][13839][13838] =  $data_survey[6375][13839][13829] +
        $data_survey[6375][13839][13830] + $data_survey[6375][13839][13831] + $data_survey[6375][13839][13832] + $data_survey[6375][13839][13833] +
        $data_survey[6375][13839][13834] + $data_survey[6375][13839][13835] + $data_survey[6375][13839][13836] + $data_survey[6375][13839][13837];

        //Suma el total de personas 2022.
        $data_survey[6375][13840][13838] = $data_survey[6375][13840][13829] +
        $data_survey[6375][13840][13830] + $data_survey[6375][13840][13831] + $data_survey[6375][13840][13832] + $data_survey[6375][13840][13833] +
        $data_survey[6375][13840][13834] + $data_survey[6375][13840][13835] + $data_survey[6375][13840][13836] + $data_survey[6375][13840][13837];

        $data_survey[6376][13850][13841] = trim($resul['inversion_tecnica_consultoria_2021']);
        $data_survey[6376][13851][13841] = trim($resul['inversion_tecnica_consultoria_2022']);
        $data_survey[6376][13850][13842] = trim($resul['inversion_formacion_2021']);
        $data_survey[6376][13851][13842] = trim($resul['inversion_formacion_2022']);
        $data_survey[6376][13850][13843] = trim($resul['inversion_sistemas_info_teleco_2021']);
        $data_survey[6376][13851][13843] = trim($resul['inversion_sistemas_info_teleco_2022']);
        $data_survey[6376][13850][13844] = trim($resul['inversion_estudios_mercado_2021']);
        $data_survey[6376][13851][13844] = trim($resul['inversion_estudios_mercado_2022']);
        $data_survey[6376][13850][13845] = trim($resul['inversion_actividades_id_2021']);
        $data_survey[6376][13851][13845] = trim($resul['inversion_actividades_id_2022']);
        $data_survey[6376][13850][13846] = trim($resul['inversion_trans_tecnologia_2021']);
        $data_survey[6376][13851][13846] = trim($resul['inversion_trans_tecnologia_2022']);
        $data_survey[6376][13850][13847] = trim($resul['inversion_ing_disenio_2021']);
        $data_survey[6376][13851][13847] = trim($resul['inversion_ing_disenio_2022']);
        $data_survey[6376][13850][13848] = trim($resul['inversion_desarrollo_prototipos_2021']);
        $data_survey[6376][13851][13848] = trim($resul['inversion_desarrollo_prototipos_2022']);
        $data_survey[6376][13850][13849] = trim($resul['inversion_registro_propiedad_2021']);
        $data_survey[6376][13851][13849] = trim($resul['inversion_registro_propiedad_2022']);

        $data_survey[6377] = $this->getOption($resul['gestiono_inovaciones']);
        $data_survey[6378] = $this->getOption($resul['relacion_actores_2021_2022']);
        $data_survey[6379] = trim($resul['relacion_actores_otros']);

        $data_survey[6380] = $this->getOption($resul['cooperacion_2021_2022']);
        $data_survey[6381] = trim($resul['otra_cooperacion_2021_2022']);

        $data_survey[6382][13889][13886] = trim($resul['inversion_capacitacion_doctorado_2021']);
        $data_survey[6382][13890][13886] = trim($resul['inversion_capacitacion_doctorado_2022']);
        $data_survey[6382][13889][13887] = trim($resul['inversion_capacitacion_maestria_2021']);
        $data_survey[6382][13890][13887] = trim($resul['inversion_capacitacion_maestria_2022']);
        $data_survey[6382][13889][13888] = trim($resul['inversion_capacitacion_especializada_2021']);
        $data_survey[6382][13890][13888] = trim($resul['inversion_capacitacion_especializada_2022']);

        $data_survey[6480] = trim($resul['nombre_representante_legal']);
        $data_survey[6481] = trim($resul['telefono_representante_legal']);
        $data_survey[6482] = trim($resul['correo_representante_legal']);



        Log::info("Finaliza la validacion de datos (convocatoria)...");

        if (count($history) > 0) {
            Log::info("Hay datos erroneos (convocatoria)...");
            die;
            return [ false, $data_survey ];
        }

        if (count($homologate) > 0) {
            Log::info("No hay datos erroneos (convocatoria)...");
            Log::warning("Hay datos homologados por \"Sin Registro\" (convocatoria)...");
            return [ true, $data_survey ];
        }

        Log::info("No hay datos erroneos (convocatoria)...");

        return [ true, $data_survey ];
    }
    /**
     * Valida las respuestas adjuntas en .xslx de la evaluación.
     * @access private
     * @param array $resul
     * @return array
     */
    private function validateResponsesEvaluation($resul): array
    {
        Log::info('Validando datos de la evaluación...');

        // the keys would should the codigo_item_evaluacion
        $history_evaluation = array();
        $data_evaluation= [];
        $data_evaluation[311] = $this->getIdOption($resul['concepto_innovacion'], 2);
        $data_evaluation[312] = $this->getIdOption($resul['estrategia_innovacion'], 2);
        $data_evaluation[313] = $this->getIdOption($resul['vision_inovacion'], 2);
        $data_evaluation[314] = $this->getIdOption($resul['objetivos_innovacion'], 2);
        $data_evaluation[315] = $this->getIdOption($resul['prioridades_innovacion'], 2);
        $data_evaluation[316] = $this->getIdOption($resul['comunicaciones_innovacion'], 2);
        $data_evaluation[317] = $this->getIdOption($resul['tiempo_innovacion'], 2);
        $data_evaluation[318] = $this->getIdOption($resul['fomento_creatividad'], 2);
        $data_evaluation[319] = $this->getIdOption($resul['formacion_innovar'], 2);
        $data_evaluation[320] = $this->getIdOption($resul['herramientas_informacion'], 2);
        $data_evaluation[321] = $this->getIdOption($resul['recepcion_ideas'], 2);
        $data_evaluation[322] = $this->getIdOption($resul['proceso_evaluacion_seleccion_ideas'], 2);
        $data_evaluation[323] = $this->getIdOption($resul['bitacora_ideas'], 2);
        $data_evaluation[324] = $this->getIdOption($resul['validacion_conceptos_negocio'], 2);
        $data_evaluation[325] = $this->getIdOption($resul['sistema_reconocimiento'], 2);
        $data_evaluation[326] = $this->getIdOption($resul['inversion_financiera_innovacion'], 2);
        $data_evaluation[327] = $this->getIdOption($resul['gestion_proyetos_innovacion'], 2);
        $data_evaluation[328] = $this->getIdOption($resul['innovacion_producto_servicio'], 2);
        $data_evaluation[329] = $this->getIdOption($resul['innovacion_procesos'], 2);
        $data_evaluation[330] = $this->getIdOption($resul['innovacion_marketing'], 2);
        $data_evaluation[331] = $this->getIdOption($resul['capacidad_innovacion'], 2);
        $data_evaluation[332] = $this->getIdOption($resul['medicion_estrategia_innovacion'], 2);
        $data_evaluation[333] = $this->getIdOption($resul['compromiso_innovacion'], 2);
        $data_evaluation[334] = $this->getIdOption($resul['responsabilidad_gestion_innovacion'], 2);
        $data_evaluation[335] = $this->getIdOption($resul['propiedad_intelectual_innovacion'], 2);
        $data_evaluation[336] = $this->getIdOption($resul['vigilancia_entorno'], 2);
        $data_evaluation[337] = $this->getIdOption($resul['proceso_innovacion_clientes'], 2);
        $data_evaluation[338] = $this->getIdOption($resul['proceso_innovacion_proveedores'], 2);
        $data_evaluation[339] = $this->getIdOption($resul['proceso_innovacion_univ_inst'], 2);
        $data_evaluation[340] = $this->getIdOption($resul['proceso_innovacion_aliados'], 2);
        $data_evaluation[341] = $this->getIdOption($resul['categoria_innovacion'], 2);
        $data_evaluation[482] = trim($resul['observacion_innovacion_conceptual_focalizada']);
        $data_evaluation[483] = trim($resul['observacion_fomento_innovacion']);
        $data_evaluation[484] = trim($resul['observacion_condicion_innovacion']);
        $data_evaluation[485] = trim($resul['observacion_innovacion_colaborativa']);
        $data_evaluation['comentario'] = trim($resul['observacion']);

        $this->LogValidateResponsesEval .= implode($history_evaluation);

        Storage::disk('local')->put("LogSiinova/LoadUserEvaluation.txt", $this->LogValidateResponsesEval);

        Log::info("Finaliza la validacion de datos de la evaluación...");

        if (count($history_evaluation) > 0) {
            Log::warning("Hay datos homologados por \"Sin Registro\" (evaluación)...");
            return [ true, $data_evaluation ];
        }
        Log::info("No se han homologado datos (evaluación)...");
        return [ true, $data_evaluation ];
    }
    /**
     * Obtiene la id concatenada a la opción de respuesta según la cantidad de "-" en la opción.
     * @access private
     * @param string&int $str_id, $count
     * @return int|null $str_id[0], null
     */
    private function getIdOption($str_id, int $count)
    {
        if (strlen($str_id) < 1) return null;

        $str_id = explode('-', trim($str_id));
        if (count($str_id) == $count) {
            return (int) $str_id[0];
        }
        return null;
    }
    /**
     * Obtiene la id concatenada a las opciones de respuesta ajuntas en .xlsx .
     * @access private
     * @param string $str_id
     * @return array|null $str_id|null
     */
    private function getOption(string $str_id)
    {
        if (strlen($str_id) < 1) return null;

        $str_id = explode('-', trim($str_id));

        // return $str_id;
        return array_combine($str_id, $str_id);
    }
    /**
     * Obtiene datos del usuario (persona) con rol evaluador.
     * @access private
     * @param string $email
     * @return object $user
     */
    private static function getUserEvaluator($email) {
        return Usuario::where('correo_electronico', '=',  trim($email))->first();
    }
    /**
     * Obtiene datos del usuario (entidad).
     * @access private
     * @param int $id_entity
     * @return object $user
     */
    private function getUserEntity(int $id_entity)
    {
        $query = Usuario::query();
        $query->where('usuario.codigo_usuario', '=', $id_entity);
        $user = $query->first();

        return $user;
    }
    /**
     * Obtiene puntaje para el diagnóstico.
     * @access private
     * @param int $id_answer_evaluation
     * @return object|boolean $answers|false
     */
    private static function getScoring($id_answer_evaluation)
    {
        if ( !is_null($id_answer_evaluation) ) {

            $query = ResultadoEvaluacionEmprendimiento::query();
            $query->select('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion',
                'opc_eval_pregunta.nombre_opc_eval_pregunta as respuesta', 'grup_opc_eval_pregunta.nombre_grup_opc_eval_pregunta',
                'opc_eval_pregunta.valor_opc_eval_pregunta as puntaje', 'item_evaluacion_emprendimiento.codigo_item_evaluacion',
                'item_evaluacion_emprendimiento.codigo_grupo_item_eval',
                'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion as observacion',
				'evaluacion_item_emprendimiento.comentario_adicional'
            );
            $query->leftJoin('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
            $query->leftJoin('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
            $query->leftJoin('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
            $query->leftJoin('core.grup_opc_eval_pregunta', 'grup_opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 'item_evaluacion_emprendimiento.codigo_grup_opc_eval_pregunta');
            $query->where('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', $id_answer_evaluation);
            $query->whereIn('item_evaluacion_emprendimiento.codigo_grupo_item_eval', self::$id_group);
            $query->orderBy('item_evaluacion_emprendimiento.codigo_item_evaluacion','ASC');
            $answers = $query->get();

            return $answers;
        } else {
            return false;
        }
	}
    /**
     * Obtiene encabezado del pdf.
     * @access private
     * @param int  $id_institution
     * @return string $data->logo_institucion
     */
    private function getInstituionLogo($id_institution)
    {
        $query = Institucion::query();
        $query->where('codigo_institucion', $id_institution);
        $data = $query->first();

        return $data->logo_institucion;
	}
    /**
     * Obtiene datos del usuario (persona).
     * @access private
     * @param int $id_entity
     * @return object $user_person
     */
    private function getUserPerson(int $id_entity) {

        $query = Entidad::query();
        $query->select('seg_codigo_usuario');
        $query->where('codigo_usuario', $id_entity);
        $id_user = $query->first();

        $query = Usuario::select(
            DB::raw("CONCAT(usuario.nombre_usuario, ' ', persona.primer_apellido_persona, ' ', persona.segundo_apellido_persona) AS full_name"),
            'usuario.correo_electronico',
            'usuario.codigo_usuario');
        $query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
        $query->where('usuario.codigo_usuario', '=', $id_user->seg_codigo_usuario);

        $user_person = $query->first();

        return $user_person;
    }
    /**
     * Obtiene respuestas de la encuesta datos básicos 287.
     * @access private
     * @param int $id_survey (287)
     * @param int $id_user
     * @return array $basic_data
     */
    private function getAnswerSurvey( int $id_survey, int $id_user) {

        $query = UsuarioEncuesta::query();
        $query->select('codigo_usuario_encuesta');
        $query->where('codigo_usuario', $id_user);
        $query->where('codigo_encuesta', $id_survey);

        $id_user_survey = $query->first();

        $query = RespuestaEncuesta::query();
        $query->where('codigo_usuario_encuesta', $id_user_survey['codigo_usuario_encuesta']);
        //$query->where('codigo_estado', Estado::getState('active'));
        $query->orderBy('codigo_pregunta_encuesta', 'asc');
        $answers = $query->get();

        $basic_data = [];

        foreach ($answers as $key => $value) {
            if ($value['codigo_pregunta_encuesta'] == 6319) {
                $basic_data [6319] = mb_convert_case(trim($value['texto_respuesta_encuesta']), MB_CASE_TITLE, "UTF-8"); //Nombre contacto
            }
            if ($value['codigo_pregunta_encuesta'] == 6320) {
                $basic_data [6320] = mb_convert_case(trim($value['texto_respuesta_encuesta']), MB_CASE_TITLE, "UTF-8"); //Dirección
            }
            if ($value['codigo_pregunta_encuesta'] == 6321) {
                $basic_data [6321] = trim($value['texto_respuesta_encuesta']); //Teléfono
            }
            if ($value['codigo_pregunta_encuesta'] == 6324) {
                $text = $this->getText((int) $value['codigo_opcion_encuesta']); //Zodes-Municipio
                $texts = explode("-", $text);
                $basic_data [6324]['municipio'] = mb_convert_case(trim($texts[0]), MB_CASE_TITLE, "UTF-8"); //Municipio
                $basic_data [6324]['departamento'] = mb_convert_case(trim($texts[1]), MB_CASE_TITLE, "UTF-8"); //Departamento
            }
        }
        return $basic_data;
    }
    /**
     * Obtiene textos de respuesta encuesta datos básicos 287.
     * @access private
     * @param int $id_code
     * @return array $text
     */
    private function getText( int $id_code) {

        $query = OpcionEncuesta::query();
        $query->select('texto_opcion_encuesta');
        $query->where('codigo_opcion_encuesta', $id_code);
        $text = $query->first();

        return $text['texto_opcion_encuesta'];
    }
    /**
     * Obtiene respuestas evaluación 29.
     * @access private
     * @param int $id_register_phase_usuariocode
     * @return array $answers
     */
    private function getAnswerEvaluation(int $id_register_phase_usuario) {

        $query = AsignacionJurado::query();
        $query->join('core.resultado_evaluacion_emprendimiento as re', 'asignacion_jurado.codigo_asignacion_jurado', '=', 're.codigo_asignacion_jurado');
        $query->join('core.evaluacion_item_emprendimiento as evi', 're.codigo_resultado_evaluacion','=', 'evi.codigo_resultado_evaluacion');
        $query->join('core.opc_eval_pregunta as opc', 'opc.codigo_opc_eval_pregunta', '=', 'evi.valor_evaluacion_item');
        $query->where('asignacion_jurado.codigo_registro_fase_usuario', $id_register_phase_usuario);
        $query->orderBy('evi.codigo_item_evaluacion', 'asc');
        $data = $query->get();

        $answers = [];
        foreach ($data as $key => $value) {
            if ($value['codigo_item_evaluacion'] == 266) {
                $answers[266] = mb_convert_case($value['nombre_opc_eval_pregunta'], MB_CASE_TITLE, "UTF-8");
            }
        }
        return $answers;
    }
    /**
     * Obtiene respuestas de la postulación convocatoria 652 diagnóstico bolivar.
     * @access private
     * @param int $id_user_survey
     * @return array $data
     */
    private function getAnswerAnnouncement($id_user_survey)
	{
        //Consulta si el usuario contestó la encuesta.
        $user_survey = UsuarioEncuesta::find($id_user_survey);
        if(!$user_survey instanceof UsuarioEncuesta){
            Log::critical('Error => No se encuentra codigo_usuario_encuesta');
            die;
        }

        //Consulta si existe la encuesta.
        $survey = Encuesta::find($user_survey->codigo_encuesta);
        if(!$survey instanceof Encuesta){
            abort(404);
        }

        //Respuestas de la encuesta.
        $query = RespuestaEncuesta::query();
        $query->select('respuesta_encuesta.*', 'opcion_encuesta.texto_opcion_encuesta','opcion_encuesta.puntaje_opcion_encuesta', 'opc_x.tipo_campo_opcion', 'opcion_encuesta.tipo_campo_opcion as tipo_campo_opcion_y', 'pregunta_encuesta.codigo_pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
        $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
        $query->leftjoin('core.opcion_encuesta as opc_x','respuesta_encuesta.cor_codigo_opcion_encuesta','=','opc_x.codigo_opcion_encuesta');
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', $user_survey->codigo_usuario_encuesta);
        $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $ar_answers = $query->get();

        //Preparar respuestas de la encuesta.
        $answers = [];
        foreach ($ar_answers as $answer) {
            switch ($answer->tipo_pregunta_encuesta) {
                case PreguntaEncuesta::getType('open'):
                    $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
                break;
                case PreguntaEncuesta::getType('single'):
                    $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
                break;
                case PreguntaEncuesta::getType('multiple'):
                    $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
                break;
                case PreguntaEncuesta::getType('upload'):
                    $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
                break;
                case PreguntaEncuesta::getType('matriz'):
                    $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;

                    if($answer->tipo_campo_opcion == TipoCampoPregunta::getTypeQuestion('select') || $answer->tipo_campo_opcion_y == TipoCampoPregunta::getTypeQuestion('select')) {
                        $item = ItemOpcionEncuesta::select('nombre_item_opcion_encuesta')->where('codigo_item_opcion_encuesta', (int)$answer->texto_respuesta_encuesta)->first();
                        $answer->texto_respuesta_encuesta = $item instanceof ItemOpcionEncuesta ? $item->nombre_item_opcion_encuesta : null;
                        $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
                    }
                break;
            }
        }

        //Preguntas de la encuesta.
        $query = $survey->questions();
        $query->select('pregunta_encuesta.*', 'ver_preg_encuesta.numero_pregunta_version as numero_pregunta_encuesta', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta', 'ver_preg_encuesta.clase_pregunta_encuesta', 'pregunta.codigo_subpregunta_encuesta', 'pregunta.orden_subpregunta_encuesta');
        $query->join('core.ver_preg_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'ver_preg_encuesta.codigo_pregunta_encuesta');
        $query->leftJoin('core.subpregunta_encuesta as pregunta', function($join) {
            $join->on('pregunta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
            $join->on('pregunta.codigo_version_encuesta', '=', 'ver_preg_encuesta.codigo_version_encuesta');
            $join->where('pregunta.codigo_estado', '=', Estado::getState('active'));
        });

        $query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('ver_preg_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('ver_preg_encuesta.codigo_version_encuesta', $user_survey->codigo_version_encuesta);
        $query->orderBy('ver_preg_encuesta.numero_pregunta_version', 'asc');
        $query->orderBy('pregunta.orden_subpregunta_encuesta', 'asc');
        $query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
        $ar_questions = $query->get();

        $matriz = $questions = $subquestions = [];
        foreach ($ar_questions as $question) {
            if(!in_array($question->tipo_campo_pregunta, [6, 7])) {
                switch ($question->clase_pregunta_encuesta) {
                    case VerPregEncuesta::surveyQuestionClass('question'):
                        if(isset($question->codigo_grupo_pregunta_encuesta) && !empty($question->codigo_grupo_pregunta_encuesta)) {
                            $questions[$question->codigo_grupo_pregunta_encuesta][$question->codigo_pregunta_encuesta] = $question;
                        }else{
                            $questions['detached'][$question->codigo_pregunta_encuesta] = $question;
                        }
                    break;
                    case VerPregEncuesta::surveyQuestionClass('subquestion'):
                        $subquestions["{$question->pregunta_padre}_{$question->codigo_opcion_encuesta}"][$question->codigo_pregunta_encuesta] = $question;
                    break;
                }
                switch ($question->tipo_pregunta_encuesta) {
                    case PreguntaEncuesta::getType('matriz'):
                        // Choices closed questions
                        $matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
                            ->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
                            ->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
                            ->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
                            ->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                            ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
                            ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                            ->orderBy('orden_opcion_encuesta', 'asc')
                            ->orderBy('codigo_opcion_encuesta', 'asc')
                            ->get();
                        $matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
                            ->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
                            ->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
                            ->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
                            ->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                            ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
                            ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                            ->orderBy('orden_opcion_encuesta', 'asc')
                            ->orderBy('codigo_opcion_encuesta', 'asc')
                            ->get();
                    break;
                }
            }
        }

        // Groups
        $groupings = $survey->groups()->select('codigo_grupo_pregunta_encuesta', 'nombre_grupo_pregunta_encuesta', 'desc_grupo_pregunta_encuesta', 'pagina_grupo_pregunta')->orderBy('pagina_grupo_pregunta', 'asc')->orderBy('codigo_grupo_pregunta_encuesta', 'asc')->get();

        // Preparar ciudades
        $cities = [];
        $citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
        foreach ($citys as $city) {
            $cities["{$city->codigo_ciudad}"] = $city;
        }

        // Preparar sectores
        $sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

        // Preparar Nivel de educación
        $levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

        //Prepara datos
        $data = [];
        $data['title'] = "$survey->nombre_encuesta";
        $data['survey'] = $survey;
        $data['levels'] = $levels;
        $data['matriz'] = $matriz;
        $data['cities'] = $cities;
        $data['sectors'] = $sectors;

        $data['userSurvey'] = $user_survey;
        $data['answers'] = $answers;
        $data['questions'] = $questions;
        $data['subquestions'] = $subquestions;
        $data['groupings'] = $groupings;

        return $data;
    }

    private static function isFinal($resul): bool
    {
        //si los campos obligatorias están vacios se entiende que es el final
        //del archivo
        if (
            $resul['postulacion'] == null
        ) {
            \Log::info('isFinal');
            return true;
        }

        return false;
    }
}
