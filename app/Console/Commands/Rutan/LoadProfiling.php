<?php

namespace App\Console\Commands\Rutan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail, Hash, TuProyecto, DateTime, Config;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\Rol,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\AsignacionJurado,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Persona,
    App\Models\Param\Institucion,
    App\Models\Core\Estado,
    App\Models\Core\ClaseEmprendimientoInstitucion,
    App\Models\Core\ClaseEmprendimiento,
    App\Models\Core\Entidad,
    App\Models\Core\DatoEntidad,
    App\Models\Core\PrivacidadContenido,
    App\Models\Core\ProfesionPersona,
    App\Models\Core\RegistroConvocatoria,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\OcupacionPersona,
    App\Console\Commands\LoadSurvey\CargaEncuesta,
    App\Console\Commands\LoadEvaluation\CargaEvaluacion,
    App\Console\Commands\LoadUsers\CargaUsuarios;

class LoadProfiling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rutan:loadProfiling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de evaluación RutaN';

    protected $LogValidateResponsesEval = '';


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
        //constants
        define("ID_INSTITUTION", Institucion::institution('rutan'));
        define("ID_SURVEY", 249); // encuesta 249
        define("ID_ANNOUNCEMENT", 634); //convocatoria 634 ruta n
        define("EMAIL_EVALUATOR", 'k.urdo@rutanmedellin.org'); //email del evaluador

        //$history guarda registro de acciones
        //para luego enviar información por correo
        $history = array();

        Excel::load('profiling_rutan.xlsx', function($reader) use($history) {
            $results = $reader->get()->toArray();

            //array log
            $log = [];
            $log['evaluacion']    = '';
            $log['postulacion']   = '';

            foreach($results as $key => $resul){

                //validate evaluation responses
                $evaluation = $this->validateData($resul);

                Log::info('Iniciando registro de perfilamiento postulación ' . $evaluation[1]['numero_registro_convocatoria']);

                //get registry_announcement
                $registry_announcement = $this->getRegistryAnnouncement((int) $evaluation[1]['numero_registro_convocatoria'], ID_ANNOUNCEMENT);

                if ($evaluation[0]) {

                    $data = new \stdClass();
                    $data->email_evaluator = EMAIL_EVALUATOR;
                    $data->codigo_institucion = ID_INSTITUTION;

                    $user_survey = new \stdClass();
                    $user_survey->codigo_usuario = $registry_announcement->codigo_usuario;

                    // validate phase
                    $id_user_record_phase = RegistroFaseUsuario::where('codigo_usuario', '=', $user_survey->codigo_usuario)->orderBy('codigo_registro_fase_usuario', 'asc')->first();

                    $record_phase = RegistroFaseUsuario::join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')
                    ->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase')->find($id_user_record_phase->codigo_registro_fase_usuario);

                    if(!$record_phase instanceof RegistroFaseUsuario) {
                        Log::critical("FATAL ERROR => No se encuentra registrado el usuario a una fase...");
                        die;
                    }

                    $assignment_jury = AsignacionJurado::where('codigo_registro_fase_usuario', '=', $record_phase->codigo_registro_fase_usuario)->first();

                    if ($assignment_jury) {

                        //upload of convocatory responses
                        $data_for_evaluation = new \stdClass();
                        $data_for_evaluation->codigo_registro_convocatoria = $registry_announcement->codigo_registro_convocatoria;
                        $data_for_evaluation->codigo_asignacion_jurado = $assignment_jury->codigo_asignacion_jurado;
                        $data_for_evaluation->id_user_evaluator = $assignment_jury->codigo_usuario;
                        $data_for_evaluation->answers_form_evaluation = $evaluation[1];
                        $data_for_evaluation->id_institution = ID_INSTITUTION;

                        CargaEvaluacion::update($data_for_evaluation);

                        $email = $this->getUser($assignment_jury->codigo_usuario);

                        Log::info('Se efectua la evaluación por: ' . $email);

                        $log['evaluacion'] = 'Se efectua la evaluación por: ' . $email;

                        CargaEvaluacion::updateToPhase($data_for_evaluation, $user_survey);

                    }else {

                        $data_for_evaluation_phase = CargaEncuesta::evaluatorAssignment($data, $user_survey);

                        $user_evaluator = $this->getEmailUsuario(EMAIL_EVALUATOR);

                        //upload of convocatory responses
                        $data_for_evaluation = new \stdClass();
                        $data_for_evaluation->codigo_registro_convocatoria = $data_for_evaluation_phase->id_record_convocatory;
                        $data_for_evaluation->codigo_asignacion_jurado = $data_for_evaluation_phase->id_assignment_jury;
                        $data_for_evaluation->id_user_evaluator = $user_evaluator->codigo_usuario;
                        $data_for_evaluation->answers_form_evaluation = $evaluation[1];
                        $data_for_evaluation->id_institution = ID_INSTITUTION;

                        CargaEvaluacion::update($data_for_evaluation);

                        Log::info('Se efectua la evaluación por: ' . EMAIL_EVALUATOR);

                        $log['evaluacion'] = 'Se efectua la evaluación por: ' . EMAIL_EVALUATOR;

                        CargaEvaluacion::updateToPhase($data_for_evaluation, $user_survey);
                    }
                }

                $history[] = [
                    'postulacion'   => $evaluation[1]['numero_registro_convocatoria'],
                    'correo'        => $evaluation[1]['correo_electronico'],
                    'evaluacion'    => $log['evaluacion']
                ];

            }

            $filename = sprintf('%s_%s.xlsx', 'reporte_cargue_perfilamiento', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s', ID_INSTITUTION, $filename);


            $file = Excel::create($filename, function($excel) use ($history) {
                $excel->sheet('Reporte evento', function($sheet)  use($history) {
                    $sheet->setAllBorders('thin');
                    $array = array(
                        'postulacion',
                        'correo',
                        'evaluacion'
                    );

                    $sheet->rows(array(
                        $array,
                    ));

                    foreach ($history as $value) {
                        $sheet->appendRow([
                            $value['postulacion'],
                            $value['correo'],
                            $value['evaluacion']
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
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send notification
            $data = [];
            $data['_inst'] = ID_INSTITUTION;
            $data['_to'] = 'juan.tabares@rutanmedellin.org';
            $data['to_copy'] = 'administracion@tuproyecto.com';
            $data['_to_name'] = 'CENTROS DEL VALLE DEL SOFTWARE';
            $data['_subject'] = 'Carga Datos Perfilamiento';
            $data['_template'] = 'emails.user.reportnotification';
            $data['_fileurl'] = $fileurl;

            SendEmail::notification($data);
        });
        \Log::info("Finalizo");
    }

    //get id from option
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
     * get ID's from option
     * @param $str_id is a string
     * @return array or null
     */
    private function getOption($str_id)
    {
        if (strlen($str_id) < 1) return null;

        $str_id = explode('-', trim($str_id));

        // return $str_id;
        return array_combine($str_id, $str_id);
    }

     //get date of birth
    private function getDateBirth($date)
    {
        if($date!= ""){
            $items = explode("-", $date);
            if(count($items)==3){
                if(!checkdate($items[1], $items[2], $items[0])){
                    $date = null;
                }
            }else{
                $date = null;
            }
            return $date;
        }
    }

    public static function getEmailUsuario($email) {
        return Usuario::where('correo_electronico', '=',  trim($email))->first();
    }

    public static function getIdentificationUser($identification)
    {
        $query = Usuario::query();
        $query->select();
        $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->where('usuario_institucion.codigo_institucion', '=', ID_INSTITUTION);
        $query->where('usuario.identificacion_usuario', '=', $identification);
        $data = $query->first();
        return $data;
    }

    public static function getusuarioInstitucion($id_user, $id_institution)
    {
		return UsuarioInstitucion::where('codigo_usuario', '=', $id_user)
                                    ->where('codigo_institucion', '=', $id_institution)
                                    ->first();
    }

    private static function getRegistryAnnouncement($registry_number, $id_announcement) {
        $query = RegistroConvocatoria::query();
        $query->where('numero_registro_convocatoria', '=', $registry_number);
        $query->where('codigo_convocatoria', '=', $id_announcement);
        //Log::info($query->first());
        return $query->first();
    }

    private function validateData($resul): array
    {
        Log::info('Validando datos de la evaluación...');
        // the keys would should the codigo_item_evaluacion
        $data_evaluation= [];
        $data_evaluation['numero_registro_convocatoria']          = trim($resul['numero_registro_convocatoria']);
        $data_evaluation['numero_documento']                      = trim($resul['numero_documento']);
        $data_evaluation['correo_electronico']                    = strtolower(trim($resul['correo_electronico']));
        //$data_evaluation['nit']                                   = trim($resul['nit']);
        $data_evaluation['nombre_entidad']                        = trim($resul['nombre_entidad']);
        $data_evaluation[255]                                     = $this->getIdOption($resul['emprendedor_reside_medellin'], 2);
        $data_evaluation[256]                                     = $this->getIdOption($resul['emprendedor_mayor_edad'], 2);
        $data_evaluation[257]                                     = $this->getIdOption($resul['base_tecnologica'], 2);
        $data_evaluation[258]                                     = $this->getIdOption($resul['participo_ruta_emprendimiento'], 2);
        $data_evaluation[259]                                     = $this->getIdOption($resul['etapa_perfilar'], 2);
        $data_evaluation[260]                                     = $this->getIdOption($resul['economia_cultural_creativa'], 2);
        $data_evaluation[261]                                     = $this->getIdOption($resul['camara_comercio_medellin'], 2);
        $data_evaluation['comentario']                            = trim($resul['comentario']) ? trim($resul['comentario']) : 'Sin Registro';

        $history_evaluation = array();

        if (empty($data_evaluation['numero_registro_convocatoria'])){
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con numero_registro_convocatoria" . "\n");
        }

        if (empty($data_evaluation['numero_documento'])){
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con numero_documento" . "\n");
        }

        if (!filter_var($data_evaluation['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " tiene un correo no válido" . "\n");
        }

       /*  if (empty($data_evaluation['nit'])){
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nit" . "\n");
        } */

        if (empty($data_evaluation['nombre_entidad'])){
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre_entidad" . "\n");
        }

        if (is_null($data_evaluation[255])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con emprendedor_reside_medellin" . "\n");
        }

        if (is_null($data_evaluation[256])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con emprendedor_mayor_edad" . "\n");
        }

        if (is_null($data_evaluation[257])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con base_tecnologica" . "\n");
        }

        if (is_null($data_evaluation[258])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con participo_ruta_emprendimiento" . "\n");
        }

        if (is_null($data_evaluation[259])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con etapa_perfilar" . "\n");
        }

        if (is_null($data_evaluation[260])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con economia_cultural_creativa" . "\n");
        }

        if (is_null($data_evaluation[261])) {
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con camara_comercio_medellin" . "\n");
        }

        $this->LogValidateResponsesEval .= implode($history_evaluation);

        Storage::disk('local')->put("LogRutaN/LoadProfiling.txt", $this->LogValidateResponsesEval);

        \Log::info("Finaliza la validacion de datos de la evaluación...");

        if (count($history_evaluation) > 0) {
            \Log::critical("FATAL ERROR => Faltan datos de la evaluación en la plantilla excel...");
            return [false, $data_evaluation ];
        }
        \Log::info("No se presentan errores en los datos de evaluación, procesando datos......");
        return [ true, $data_evaluation ];
    }

    public static function getUser($id_user) {
        $email = Usuario::where('codigo_usuario', '=',  trim($id_user))->first();
        return $email->correo_electronico;
    }
}
//este es el correcto
