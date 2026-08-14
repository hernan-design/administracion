<?php

namespace App\Console\Commands\SiinovaBolivar;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Config, DB, DateTime, Excel, Hash, Log, Storage, SendEmail, TuProyecto;

use App\Models\Core\ClaseEmprendimiento,
    App\Models\Core\ClaseEmprendimientoInstitucion,
    App\Models\Core\DatoEntidad,
    App\Models\Core\Entidad,
    App\Models\Core\Estado,
    App\Models\Core\Persona,
    App\Models\Core\OcupacionPersona,
    App\Models\Core\PrivacidadContenido,
    App\Models\Core\ProfesionPersona,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Param\Institucion,
    App\Models\Seguridad\Rol,
    App\Models\Seguridad\RolUsuario,
    App\Models\Seguridad\Usuario;

use App\Console\Commands\LoadSurvey\CargaEncuesta,
    App\Console\Commands\LoadEntities\CargaEntidad,
    App\Console\Commands\LoadEvaluation\CargaEvaluacion,
    App\Console\Commands\LoadUsers\CargaUsuarios;

class LoadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siinova:loadData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios, entidades, formulario y eveluacion';

    protected $LogValidateResponses = '';
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
        Log::info('Iniciando Cargue Siinova ....');

        define("ID_INSTITUTION", Institucion::institution('siinova'));
        define("ID_ROL_INSTITUTION", 83);
        define("ID_ROL_ASSOCIATED", 39); // rol asociado
        define("ID_ROL_USER", 3);
        define("ID_SEX", 3); //prefiero no decir
        define("ID_CITY_BIRTH", 5136); // Otro Municipio-Otro Distrito-COL
        define("ID_ENTREPRENEURSHIP_CLASS", 63); // empresa
        define("ID_IDENTIFICATION_TYPE", 3); // nit
        define("DATE", "1900-01-01"); // fecha_nacimiento || fecha_inicio_operacion
        define("ID_STAGE", 141); // etapa = inicial
        define("ID_CATEGORY", 742); // categoria = General
        define("ID_CIIU", 99999); // ciuu = No Registra
        define("ID_SURVEY", 287); // encuesta 287
        define("ID_ANNOUNCEMENT", 648); //convocatoria 648 bolivar
        define("ID_PHASE", 366); // fase registro 366
        define("EMAIL_EVALUATOR", 'monica.carrillo@ocaribe.email'); //email del evaluador

        //$history keep record of actions then, send email
        $history = array();

        Excel::load('data_bolivar.xlsx', function($reader) use($history) {
            $results = $reader->get()->toArray();
            //validate data
            $this->validateData($results);

            //array log
            $log = [];
            $log['usuario']      = '';
            $log['entidad']      = '';
            $log['convocatoria'] = '';
            $log['evaluacion']   = '';

            foreach($results as $key => $resul){
                Log::info("Registrando " . strtolower(trim($resul['correo_electronico'])));

                //user data
                $data_user = [];
                $data_user['correo_electronico']           = strtolower(trim($resul['correo_electronico'])); //user email
                $data_user['nombre_usuario']               = ucwords(trim($resul['nombre_usuario'])); //user name
                $data_user['primer_apellido']              = ucwords(trim($resul['primer_apellido'])); //user first lastname
                $data_user['segundo_apellido']             = ucwords(trim($resul['segundo_apellido'])); //user second lastname
                $data_user['sexo']                         = ID_SEX; //user sex
                $data_user['ciudad_nacimiento']            = ID_CITY_BIRTH; //city of birth
                $data_user['fecha_nacimiento']             = DATE; //user date birth
                $data_user['tipo_identificacion']          = ID_IDENTIFICATION_TYPE; //user type identification
                $data_user['movil']                        = trim($resul['celular']); //user cellphone
                $data_user['telefono']                     = trim($resul['telefono']); //user phone
                $data_user['numero_identificacion']        = trim($resul['nit']); //user id number
                $data_user['id_institucion']               = ID_INSTITUTION;
                $data_user['id_rol_institucion']           = ID_ROL_INSTITUTION;
                $data_user['id_rol_associated']            = ID_ROL_ASSOCIATED;

                //Allows you to identify the fields to be loaded by institution
                $data_require = ["correo_electronico"   => true,
                                "nombre_usuario"        => true,
                                "primer_apellido"       => true,
                                "segundo_apellido"      => true,
                                "sexo"                  => true,
                                "ciudad_nacimiento"     => true,
                                "fecha_nacimiento"      => true,
                                "tipo_identificacion"   => true,
                                "numero_identificacion" => true,
                                "movil"                 => true,
                                "telefono"              => false,
                                "nivel_academico"       => false,
                ];

                //entity data
                $data_entity = [];
                $data_entity['codigo_clase_empre_insti']    = ID_ENTREPRENEURSHIP_CLASS; //company
                $data_entity['codigo_tipo_identificacion']  = ID_IDENTIFICATION_TYPE; // nit
                $data_entity['identificacion_usuario']      = trim($resul['nit']);
                $data_entity['nombre_usuario']              = trim($resul['razon_social']);
                $data_entity['codigo_ciudad']               = ID_CITY_BIRTH;
                $data_entity['fecha_nacimiento_usuario']    = DATE; //only applies to business
                $data_entity['codigo_categoria_proyecto']   = ID_CATEGORY; //industry
                $data_entity['codigo_etapa_proyecto']       = ID_STAGE; //stage
                $data_entity['codigo_sector_interes']       = ID_CIIU; //ciuu
                $data_entity['codigo_tipo_sociedad']        = $this->getIdOption(trim($resul['tipo_sociedad']), 2);
                $data_entity['cubrimiento_entidad']         = $this->getIdOption(trim($resul['tamanio_empresa']), 2);
                $data_entity['correo_contacto']             = trim($resul['correo_contacto']);
                $data_entity['pagina_web_usuario']          = trim($resul['pagina_web']);
                $data_entity['biografia_usuario']           = "Sin Registro"; //user bio
                $data_entity['telefono_usuario']            = trim($resul['telefono']);
                $data_entity['numero_whatsapp']             = trim($resul['celular']);;

                $codigo_usuario = '';
                $codigo_usuario_institucion = "";

                DB::beginTransaction();
                try {
                        $codigo_usuario = '';
                        $password = '';
                        $user = $this->getEmailUsuario($data_user['correo_electronico']);

                        if(!$user instanceof Usuario)
                        {

                            $user_identification = $this->getIdentificationUser($data_user['numero_identificacion']);

                            if(!$user_identification instanceof Usuario) {

                                $user = CargaUsuarios::store($data_user, $data_require);
                                $codigo_usuario = $user->codigo_usuario;
                                $password = $user->password;

                                $log['usuario']      = 'Se crea el usuario';

                            }
                            else {

                                $log['usuario'] = "Usuario con número de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado";

                                $history[] = [
                                    'correo'        => $data_user['correo_electronico'],
                                    'usuario'       => $log['usuario'],
                                    'entidad'       => '',
                                    'convocatoria'  => '',
                                    'evaluacion'    => '',
                                ];

                                continue;
                            }
                        }
                        else{

                            //update user
                            $codigo_usuario = $user->codigo_usuario;

                            if($data_require['nombre_usuario'])
                                $user->nombre_usuario = $data_user['nombre_usuario'];

                            if($data_require['fecha_nacimiento'])
                                $user->fecha_nacimiento_usuario = $data_user['fecha_nacimiento'];

                            if($data_require['movil'])
                                $user->movil_usuario = $data_user['movil'];

                            if($data_require['telefono'])
                                $user->telefono_usuario = $data_user['telefono'];

                            if($data_require['tipo_identificacion'])
                                $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];

                            if($data_require['numero_identificacion'])
                                $user->identificacion_usuario = $data_user['numero_identificacion'];

                            if($data_require['ciudad_nacimiento'])
                                $user->codigo_ciudad = $data_user['ciudad_nacimiento'];

                            $password = str_random(8);
                            $user->password = Hash::make($password);
                            $user->save();

                            $user_institution = $this->getusuarioInstitucion($codigo_usuario, ID_INSTITUTION);

                            if(!$user_institution instanceof UsuarioInstitucion){

                                $user_identification = $this->getIdentificationUser($data_user['numero_identificacion']);

                                if (!$user_identification instanceof Usuario) {

                                    $n_user_institution = new UsuarioInstitucion;
                                    $n_user_institution->codigo_usuario = $codigo_usuario;
                                    $n_user_institution->codigo_institucion = ID_INSTITUTION;
                                    $n_user_institution->codigo_estado = Estado::getState('active');
                                    $n_user_institution->codigo_rol = ID_ROL_USER;
                                    $n_user_institution->fecha_registro_usuario = date('Y-m-d');
                                    $n_user_institution->hora_registro_usuario = date('H:m:i');
                                    $n_user_institution->save();

                                    $id_user_institution = $n_user_institution->codigo_usuario_institucion;

                                    $user_rol = RolUsuario::where('codigo_institucion', ID_INSTITUTION)
                                                                ->where('role_id', ID_ROL_INSTITUTION)
                                                                ->where('user_id', $codigo_usuario)
                                                                ->first();

                                    if(!$user_rol instanceof RolUsuario) {
                                        $user_rol = new RolUsuario;
                                        $user_rol->role_id = ID_ROL_INSTITUTION;
                                        $user_rol->user_id = $codigo_usuario;
                                        $user_rol->codigo_institucion = ID_INSTITUTION;
                                        $user_rol->save();
                                    }

                                    $user_rol_associated = RolUsuario::where('codigo_institucion', ID_INSTITUTION)
                                                                        ->where('role_id', ID_ROL_ASSOCIATED)
                                                                        ->where('user_id', $codigo_usuario)
                                                                        ->first();

                                    if(!$user_rol_associated instanceof RolUsuario) {
                                        $user_rol_associated = new RolUsuario;
                                        $user_rol_associated->role_id = ID_ROL_ASSOCIATED;
                                        $user_rol_associated->user_id = $codigo_usuario;
                                        $user_rol_associated->codigo_institucion = ID_INSTITUTION;
                                        $user_rol_associated->save();
                                    }

                                    $log['usuario'] = "Se actualiza el usuario";
                                }
                                else {
                                    $log['usuario'] = "Usuario con número de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado en la institución";
                                    $history[] = [
                                        'correo'        => $data_user['correo_electronico'],
                                        'usuario'       => $log['usuario'],
                                        'entidad'       => '',
                                        'convocatoria'  => '',
                                        'evaluacion'  => '',
                                    ];
                                }
                            }
                            else {

                                $id_user_institution = $user_institution->codigo_usuario_institucion;
                                $log['usuario'] = "Se actualiza el usuario";
                            }
                        }

                        //accept terms
                        CargaUsuarios::acceptTerms($user, ID_INSTITUTION);

                        //user id of new entity
                        $codigo_usuario_entidad = '';

                        //create entity
                        if (isset($codigo_usuario) && !empty($codigo_usuario)) {

                            $response = CargaEntidad::store($codigo_usuario, $data_entity, ID_INSTITUTION);
                            Log::info($response[1]);
                            $log['entidad'] = $response[1];

                            $codigo_usuario_entidad = $response[2];

                            if (!$response[0]) {

                                DB::rollback();
                                $log['convocatoria'] = 'No se inscribió a la convocatoria, revisar respuestas';
                                $log['evaluacion'] = 'No se efectua la evaluación';

                                $history[] = [
                                    'correo'        => $data_user['correo_electronico'],
                                    'usuario'       => $log['usuario'],
                                    'entidad'       => $log['entidad'],
                                    'convocatoria'  => $log['convocatoria'],
                                    'evaluacion'    => $log['evaluacion'],
                                ];

                                continue;
                            }
                        }

                        //validate responses convocatory for register
                        $inscription = $this->validateResponsesOfConvocatory($resul);

                        if ($inscription[0]) {

                            //upload of convocatory responses
                            $data_for_inscription = new \stdClass();
                            $data_for_inscription->codigo_usuario = $codigo_usuario_entidad;
                            $data_for_inscription->email_evaluator = EMAIL_EVALUATOR;
                            $data_for_inscription->codigo_encuesta = ID_SURVEY;
                            $data_for_inscription->codigo_convocatoria = ID_ANNOUNCEMENT;
                            $data_for_inscription->codigo_institucion = ID_INSTITUTION;
                            $data_for_inscription->id_phase = ID_PHASE;
                            $data_for_inscription->has_evaluation = true;
                            $data_for_inscription->answers_form_convocatory = $inscription[1];

                            if ($data_for_inscription->has_evaluation) {
                                $data_for_evaluation_phase = CargaEncuesta::store($data_for_inscription);
                            }else {
                                CargaEncuesta::store($data_for_inscription);
                            }

                            Log::info("Se inscribió a la convocatoria");

                            $log['convocatoria'] = 'Se inscribió a la convocatoria';
                        }
                        else {
                            $log['convocatoria'] = 'No se inscribió a la convocatoria, revisar respuestas';
                        }

                        //validate responses convocatory for register
                        $evaluation = $this->validateResponsesEvaluation($resul);

                        if ($evaluation[0]) {

                            $user_evaluator = $this->getEmailUsuario(EMAIL_EVALUATOR);

                            //upload of convocatory responses
                            $data_for_evaluation = new \stdClass();
                            $data_for_evaluation->codigo_asignacion_jurado = $data_for_evaluation_phase->id_assignment_jury;
                            $data_for_evaluation->id_user_evaluator = $user_evaluator->codigo_usuario;
                            $data_for_evaluation->answers_form_evaluation = $evaluation[1];
                            $data_for_evaluation->id_institution = ID_INSTITUTION;


                            CargaEvaluacion::update($data_for_evaluation);

                            Log::info('Se efectua la evaluación por: ' . EMAIL_EVALUATOR);

                            $log['evaluacion'] = 'Se efectua la evaluación por: ' . EMAIL_EVALUATOR;

                        }
                        else {
                            $log['evaluacion'] = 'No se efectua la evaluación';
                        }


                        // Send notification to new user
                        /* $data = [];
                        $data['_inst'] = ID_INSTITUTION;
                        $data['_to'] = $data_user['correo_electronico'];
                        $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                        $data['_subject'] = 'Confirmación de registro plataforma';
                        $data['_template'] = 'emails.user.restorepassword';
                        $data['_current_password'] = $password;
                        $data['_change_password']  = '/password/email';
                        SendEmail::notification($data); */

                        DB::commit();

                    }
                    catch (\Exception $e) {
                        DB::rollback();
                        Log::info('Error catch LoadData Siinova');
                        Log::error($e->getMessage());
                        die;
                    }

                $history[] = [
                    'correo'        => $data_user['correo_electronico'],
                    'usuario'       => $log['usuario'],
                    'entidad'       => $log['entidad'],
                    'convocatoria'  => $log['convocatoria'],
                    'evaluacion'    => $log['evaluacion']
                ];
            }

            $filename = sprintf('%s_%s.xlsx', 'reporte_cargue_datos', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s', ID_INSTITUTION, $filename);


            $file = Excel::create($filename, function($excel) use ($history) {
                $excel->sheet('Reporte_Cargue_Bolivar', function($sheet)  use($history) {
                    $sheet->setAllBorders('thin');
                    $array = array(
                        'Correo',
                        'Usuario',
                        'Entidad',
                        'Convocatoria',
                        'Evaluación'
                    );

                    $sheet->rows(array(
                        $array,
                    ));

                    foreach ($history as $value) {
                        $sheet->appendRow([
                            $value['correo'],
                            $value['usuario'],
                            $value['entidad'],
                            $value['convocatoria'],
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
            $data['_to'] = 'monica.carrillo@ocaribe.email';
            $data['to_copy'] = ['administracion@tuproyecto.com', "testertuproyecto@gmail.com"];
            $data['_to_name'] = 'BOLIVAR SIINOVA';
            $data['_subject'] = 'Carga de datos';
            $data['_template'] = 'emails.user.reportnotification';
            $data['_fileurl'] = $fileurl;

            SendEmail::notification($data);

        });
        \Log::info("Finalizo");
    }

    private static function validateData($results): void
    {
        Log::info('Validando datos...');

        $history = array();

        foreach($results as $key => $resul)
        {
            //user data
            $data_user = [];
            $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //OBLIGAROTIO
            $data_user['nombre_usuario']        = ucwords(trim($resul['nombre_usuario'])); //OBLIGAROTIO
            $data_user['primer_apellido']       = ucwords(trim($resul['primer_apellido'])); //OBLIGAROTIO
            $data_user['segundo_apellido']      = ucwords(trim($resul['segundo_apellido']));
            $data_user['numero_identificacion'] = trim($resul['nit']); ////OBLIGAROTIO
            $data_user['celular']               = trim($resul['celular']); //OBLIGAROTIO

            if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                array_push($history, "- La fila " . ($key + 2). " tiene correo incorecto" . "\n");
            }

            if (empty($data_user['nombre_usuario'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene nombre_usuario" . "\n");
            }

            if (empty($data_user['primer_apellido'])) {
                array_push($history, "- La fila " . ($key + 2). "  no tiene primer_apellido" . "\n");
            }

            if (empty($data_user['numero_identificacion'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene numero_identificacion" . "\n");
            }

            if (empty($data_user['celular'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene celular" . "\n");
            }

            //data entity
            $data_entity = [];
            $data_entity['identificacion_usuario']     = trim($resul['nit']);
            $data_entity['nombre_usuario']             = trim($resul['razon_social']);
            $data_entity['codigo_tipo_sociedad']       =  self::getIdOption(trim($resul['tipo_sociedad']), 2);
            $data_entity['cubrimiento_entidad']        =  self::getIdOption(trim($resul['tamanio_empresa']), 2);

            if (empty($data_entity['identificacion_usuario'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene nit" . "\n");
            }

            if (empty($data_entity['nombre_usuario'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene razon_social" . "\n");
            }

            if (is_null($data_entity['codigo_tipo_sociedad'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene tipo_sociedad o el dato es incorrecto" . "\n");
            }

            if (is_null($data_entity['cubrimiento_entidad'])) {
                array_push($history, "- La fila " . ($key + 2). " no tiene tamanio_empresa o el dato es incorrecto" . "\n");
            }

        }

        $content = implode($history);

            Storage::disk('local')->put('LogSiinova/LoadData.txt', $content);
            \Log::info("Finalizó la validación de datos...");

            if (count($history) > 0) {
                \Log::info("Hay datos incorrectos en el excel, valida el archivo Log en Storage/app/LogSiinova/LoadData.txt");
                die;
            }
            \Log::info("No hay errrores en la validacion...");
            return;
    }

    private function validateResponsesOfConvocatory($resul): array
    {
        Log::info('Validando datos del formulario de inscripción...');

        $name = ucwords(trim($resul['nombre_usuario']));
        $first_lastname = ucwords(trim($resul['primer_apellido']));
        $second_lastname = ucwords(trim($resul['segundo_apellido']));

        $history = array();
        $homologate = array();

        $data_survey = [];
        $data_survey[6319] = $name . " " . $first_lastname . " " . $second_lastname;
        $data_survey[6320] = trim($resul['direccion']);
        $data_survey[6321] = trim($resul['celular']);
        $data_survey[6322] = $this->getIdOption($resul['red_social'], 2);
        $data_survey[6323] = trim($resul['usuario_red_social']);
        $data_survey[6324] = $this->getIdOption($resul['zodes_municipio'], 3);
        //[6325] pregunta integrantes 1-4, [13730] integrante 1 columna [13727] nombre fila
        $data_survey[6325][13730][13727] = trim($resul['nombre_integrante_1']);
        $data_survey[6325][13730][13728] = trim($resul['telefono_integrante_1']);
        $data_survey[6325][13730][13729] = trim($resul['correo_integrante_1']);
        $data_survey[6325][13731][13727] = trim($resul['nombre_integrante_2']);
        $data_survey[6325][13731][13728] = trim($resul['telefono_integrante_2']);
        $data_survey[6325][13731][13729] = trim($resul['correo_integrante_2']);
        $data_survey[6325][13732][13727] = trim($resul['nombre_integrante_3']);
        $data_survey[6325][13732][13728] = trim($resul['telefono_integrante_3']);
        $data_survey[6325][13732][13729] = trim($resul['correo_integrante_3']);
        $data_survey[6325][13733][13727] = trim($resul['nombre_integrante_4']);
        $data_survey[6325][13733][13728] = trim($resul['telefono_integrante_4']);
        $data_survey[6325][13733][13729] = trim($resul['correo_integrante_4']);
        $data_survey[6326][13737][13734] = trim($resul['nombre_integrante_5']);
        $data_survey[6326][13737][13735] = trim($resul['telefono_integrante_5']);
        $data_survey[6326][13737][13736] = trim($resul['correo_integrante_5']);
        $data_survey[6326][13738][13734] = trim($resul['nombre_integrante_6']);
        $data_survey[6326][13738][13735] = trim($resul['telefono_integrante_6']);
        $data_survey[6326][13738][13736] = trim($resul['correo_integrante_6']);
        $data_survey[6326][13739][13734] = trim($resul['nombre_integrante_7']);
        $data_survey[6326][13739][13735] = trim($resul['telefono_integrante_7']);
        $data_survey[6326][13739][13736] = trim($resul['correo_integrante_7']);
        $data_survey[6326][13740][13734] = trim($resul['nombre_integrante_8']);
        $data_survey[6326][13740][13735] = trim($resul['telefono_integrante_8']);
        $data_survey[6326][13740][13736] = trim($resul['correo_integrante_8']);
        $data_survey[6327][13744][13741] = trim($resul['nombre_integrante_9']);
        $data_survey[6327][13744][13742] = trim($resul['telefono_integrante_9']);
        $data_survey[6327][13744][13743] = trim($resul['correo_integrante_9']);
        $data_survey[6327][13745][13741] = trim($resul['nombre_integrante_10']);
        $data_survey[6327][13745][13742] = trim($resul['telefono_integrante_10']);
        $data_survey[6327][13745][13743] = trim($resul['correo_integrante_10']);
        $data_survey[6327][13746][13741] = trim($resul['nombre_integrante_11']);
        $data_survey[6327][13746][13742] = trim($resul['telefono_integrante_11']);
        $data_survey[6327][13746][13743] = trim($resul['correo_integrante_11']);
        $data_survey[6327][13747][13741] = trim($resul['nombre_integrante_12']);
        $data_survey[6327][13747][13742] = trim($resul['telefono_integrante_12']);
        $data_survey[6327][13747][13743] = trim($resul['correo_integrante_12']);

            if (empty($data_survey[6320])) {
                Log::info("6320");
                array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con dirección" . "\n");
            }

            if (empty($data_survey[6321])) {
                Log::info("6321");
                array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono" . "\n");
            }

            if (empty($data_survey[6323])) {
                $data_survey[6323] = "Sin Registro";
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con usuario_red_social" . "\n");
            }

            if (is_null($data_survey[6324])) {
                array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con zodes-municipio" . "\n");
            }

            if (empty($data_survey[6325][13730][13727])) {
                $data_survey[6325][13730][13727] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 1" . "\n");
            }

            if (empty($data_survey[6325][13730][13728])) {
                $data_survey[6325][13730][13728] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 1" . "\n");
            }

            if (empty($data_survey[6325][13730][13729])) {
                $data_survey[6325][13730][13729] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 1" . "\n");
            }

            if (empty($data_survey[6325][13731][13727])) {
                $data_survey[6325][13731][13727] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 2" . "\n");
            }

            if (empty($data_survey[6325][13731][13728])) {
                $data_survey[6325][13731][13728] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 2" . "\n");
            }

            if (empty($data_survey[6325][13731][13729])) {
                $data_survey[6325][13731][13729] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 2" . "\n");
            }

            if (empty($data_survey[6325][13732][13727])) {
                $data_survey[6325][13732][13727] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 3" . "\n");
            }

            if (empty($data_survey[6325][13732][13728])) {
                $data_survey[6325][13732][13728] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 3" . "\n");
            }

            if (empty($data_survey[6325][13732][13729])) {
                $data_survey[6325][13732][13729] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 3" . "\n");
            }

            if (empty($data_survey[6325][13733][13727])) {
                $data_survey[6325][13733][13727] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 4" . "\n");
            }

            if (empty($data_survey[6325][13733][13728])) {
                $data_survey[6325][13733][13728] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 4" . "\n");
            }

            if (empty($data_survey[6325][13733][13729])) {
                $data_survey[6325][13733][13729] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 4" . "\n");
            }

            if (empty($data_survey[6326][13737][13734])) {
                $data_survey[6326][13737][13734] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 5" . "\n");
            }

            if (empty($data_survey[6326][13737][13735])) {
                $data_survey[6326][13737][13735] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 5" . "\n");
            }

            if (empty($data_survey[6326][13737][13736])) {
                $data_survey[6326][13737][13736] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 5" . "\n");
            }

            if (empty($data_survey[6326][13738][13734])) {
                $data_survey[6326][13738][13734] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 6" . "\n");
            }

            if (empty($data_survey[6326][13738][13735])) {
                $data_survey[6326][13738][13735] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 6" . "\n");
            }

            if (empty($data_survey[6326][13738][13736])) {
                $data_survey[6326][13738][13736] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 6" . "\n");
            }

            if (empty($data_survey[6326][13739][13734])) {
                $data_survey[6326][13739][13734] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 7" . "\n");
            }

            if (empty($data_survey[6326][13739][13735])) {
                $data_survey[6326][13739][13735] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 7" . "\n");
            }

            if (empty($data_survey[6326][13739][13736])) {
                $data_survey[6326][13739][13736] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 7" . "\n");
            }

            if (empty($data_survey[6326][13740][13734])) {
                $data_survey[6326][13740][13734] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 8" . "\n");
            }

            if (empty($data_survey[6326][13740][13735])) {
                $data_survey[6326][13740][13735] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 8" . "\n");
            }

            if (empty($data_survey[6326][13740][13736])) {
                $data_survey[6326][13740][13736] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 8" . "\n");
            }

            if (empty($data_survey[6327][13744][13741])) {
                $data_survey[6327][13744][13741] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 9" . "\n");
            }

            if (empty($data_survey[6327][13744][13742])) {
                $data_survey[6327][13744][13742] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 9" . "\n");
            }

            if (empty($data_survey[6327][13744][13743])) {
                $data_survey[6327][13744][13743] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 9" . "\n");
            }

            if (empty($data_survey[6327][13745][13741])) {
                $data_survey[6327][13745][13741] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 10" . "\n");
            }

            if (empty($data_survey[6327][13745][13742])) {
                $data_survey[6327][13745][13742] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 10" . "\n");
            }

            if (empty($data_survey[6327][13745][13743])) {
                $data_survey[6327][13745][13743] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 10" . "\n");
            }
            if (empty($data_survey[6327][13746][13741])) {
                $data_survey[6327][13746][13741] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 11" . "\n");
            }

            if (empty($data_survey[6327][13746][13742])) {
                $data_survey[6327][13746][13742] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 11" . "\n");
            }

            if (empty($data_survey[6327][13746][13743])) {
                $data_survey[6327][13746][13743] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 11" . "\n");
            }

            if (empty($data_survey[6327][13747][13741])) {
                $data_survey[6327][13747][13741] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con nombre en integrante 12" . "\n");
            }

            if (empty($data_survey[6327][13747][13742])) {
                $data_survey[6327][13747][13742] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con telefono en integrante 12" . "\n");
            }

            if (empty($data_survey[6327][13747][13743])) {
                $data_survey[6327][13747][13743] = 'Sin Registro';
                array_push($homologate, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con correo en integrante 12" . "\n");
            }

            $this->LogValidateResponses .= implode($history);

            Storage::disk('local')->put("LogSiinova/LoadUserResponses.txt", $this->LogValidateResponses);

            \Log::info("Finaliza la validacion de datos formulario de convocatoria...");

            if (count($history) > 0) {
                \Log::info("Hay datos erroneas en plantilla excel...");
                return [ false, $data_survey ];
            }

            if (count($homologate) > 0) {
                \Log::info("No hay errrores en la validacion de datos formulario de convocatoria...");
                \Log::warning("Hay datos homologados por \"Sin Registro\" de la plantilla excel, procesando datos...");
                return [ true, $data_survey ];
            }

            \Log::info("No hay errrores en la validacion de datos formulario de convocatoria...");
            return [ true, $data_survey ];
        // }
    }

    private function validateResponsesEvaluation($resul): array
    {
        Log::info('Validando datos de la evaluación...');
        // the keys would should the codigo_item_evaluacion
        $history_evaluation = array();
        $data_evaluation= [];
        $data_evaluation[262] = $this->getIdOption($resul['estado_rues'], 2);
        $data_evaluation[263] = $this->getIdOption($resul['habilitacion_mipyme'], 2);
        $data_evaluation[264] = $this->getIdOption($resul['etapa_proyecto'], 2);
        $data_evaluation[265] = $this->getIdOption($resul['continuidad'], 2);
        $data_evaluation[266] = $this->getIdOption($resul['foco_paed'], 2);
        $data_evaluation[267] = $this->getIdOption($resul['inscripcion'], 2);
        $data_evaluation[268] = $this->getIdOption($resul['cohorte_postulante_1'], 2);
        $data_evaluation[269] = $this->getIdOption($resul['aprobacion_etrenamiento_1'], 2);
        $data_evaluation[270] = $this->getIdOption($resul['aprobacion_talleres_1'], 2);
        $data_evaluation[271] = $this->getIdOption($resul['cohorte_postulante_2'], 2);
        $data_evaluation[272] = $this->getIdOption($resul['aprobacion_etrenamiento_2'], 2);
        $data_evaluation[273] = $this->getIdOption($resul['aprobacion_talleres_2'], 2);
        $data_evaluation[274] = $this->getIdOption($resul['cohorte_postulante_3'], 2);
        $data_evaluation[275] = $this->getIdOption($resul['aprobacion_etrenamiento_3'], 2);
        $data_evaluation[276] = $this->getIdOption($resul['aprobacion_talleres_3'], 2);
        $data_evaluation[277] = $this->getIdOption($resul['cohorte_postulante_4'], 2);
        $data_evaluation[278] = $this->getIdOption($resul['aprobacion_etrenamiento_4'], 2);
        $data_evaluation[279] = $this->getIdOption($resul['aprobacion_talleres_4'], 2);
        $data_evaluation[280] = $this->getIdOption($resul['cohorte_postulante_5'], 2);
        $data_evaluation[281] = $this->getIdOption($resul['aprobacion_etrenamiento_5'], 2);
        $data_evaluation[282] = $this->getIdOption($resul['aprobacion_talleres_5'], 2);
        $data_evaluation[283] = $this->getIdOption($resul['cohorte_postulante_6'], 2);
        $data_evaluation[284] = $this->getIdOption($resul['aprobacion_etrenamiento_6'], 2);
        $data_evaluation[285] = $this->getIdOption($resul['aprobacion_talleres_6'], 2);
        $data_evaluation[286] = $this->getIdOption($resul['cohorte_postulante_7'], 2);
        $data_evaluation[287] = $this->getIdOption($resul['aprobacion_etrenamiento_7'], 2);
        $data_evaluation[288] = $this->getIdOption($resul['aprobacion_talleres_7'], 2);
        $data_evaluation[289] = $this->getIdOption($resul['cohorte_postulante_8'], 2);
        $data_evaluation[290] = $this->getIdOption($resul['aprobacion_etrenamiento_8'], 2);
        $data_evaluation[291] = $this->getIdOption($resul['aprobacion_talleres_8'], 2);
        $data_evaluation[292] = $this->getIdOption($resul['cohorte_postulante_9'], 2);
        $data_evaluation[293] = $this->getIdOption($resul['aprobacion_etrenamiento_9'], 2);
        $data_evaluation[294] = $this->getIdOption($resul['aprobacion_talleres_9'], 2);
        $data_evaluation[295] = $this->getIdOption($resul['cohorte_postulante_10'], 2);
        $data_evaluation[296] = $this->getIdOption($resul['aprobacion_etrenamiento_10'], 2);
        $data_evaluation[297] = $this->getIdOption($resul['aprobacion_talleres_10'], 2);
        $data_evaluation[298] = $this->getIdOption($resul['entrenamiento_especializado_empresa'], 2);
        $data_evaluation[299] = $this->getIdOption($resul['aprobacion_talleres_empresa'], 2);
        $data_evaluation[300] = $this->getIdOption($resul['aprobacion_proyecto_empresa'], 2);
        $data_evaluation[301] = $this->getIdOption($resul['diagnostico_innovacion'], 2);
        $data_evaluation[302] = $this->getIdOption($resul['puntaje'], 2);
        $data_evaluation[303] = $this->getIdOption($resul['concepto_evaluador'], 2);
        $data_evaluation[304] = $this->getIdOption($resul['ejecucion_vaucher'], 2);
        $data_evaluation[305] = $this->getIdOption($resul['cohorte_postulante_11'], 2);
        $data_evaluation[306] = $this->getIdOption($resul['aprobacion_etrenamiento_11'], 2);
        $data_evaluation[307] = $this->getIdOption($resul['aprobacion_talleres_11'], 2);
        $data_evaluation[308] = $this->getIdOption($resul['cohorte_postulante_12'], 2);
        $data_evaluation[309] = $this->getIdOption($resul['aprobacion_etrenamiento_12'], 2);
        $data_evaluation[310] = $this->getIdOption($resul['aprobacion_talleres_12'], 2);
        $data_evaluation['comentario'] = trim($resul['observacion']) ? trim($resul['observacion']) : 'Sin Registro';

        if (is_null($data_evaluation[262])) {
            $data_evaluation[262] = 746; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con estado_rues" . "\n");
        }

        if (is_null($data_evaluation[263])) {
            $data_evaluation[263] = 747; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con habilitacion_mipyme" . "\n");

        }

        if (is_null($data_evaluation[264])) {
            $data_evaluation[264] = 748; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con etapa_proyecto" . "\n");
        }

        if (is_null($data_evaluation[265])) {
            $data_evaluation[265] = 749; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con continuidad" . "\n");
        }

        if (is_null($data_evaluation[266])) {
            $data_evaluation[266] = 750; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con foco_paed" . "\n");
        }

        if (is_null($data_evaluation[267])) {
            $data_evaluation[267] = 751; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con inscripcion" . "\n");
        }

        if (is_null($data_evaluation[268])) {
            $data_evaluation[268] = 752; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_1" . "\n");
        }

        if (is_null($data_evaluation[269])) {
            $data_evaluation[269] = 753; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_1" . "\n");
        }

        if (is_null($data_evaluation[270])) {
            $data_evaluation[270] = 754; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_1" . "\n");
        }

        if (is_null($data_evaluation[271])) {
            $data_evaluation[271] = 755; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_2" . "\n");
        }

        if (is_null($data_evaluation[272])) {
            $data_evaluation[272] = 756; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_2" . "\n");
        }

        if (is_null($data_evaluation[273])) {
            $data_evaluation[273] = 757; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_2" . "\n");
        }

        if (is_null($data_evaluation[274])) {
            $data_evaluation[274] = 758; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_3" . "\n");
        }

        if (is_null($data_evaluation[275])) {
            $data_evaluation[275] = 759; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_3" . "\n");
        }

        if (is_null($data_evaluation[276])) {
            $data_evaluation[276] = 760; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_3" . "\n");
        }

        if (is_null($data_evaluation[277])) {
            $data_evaluation[277] = 761; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_4" . "\n");
        }

        if (is_null($data_evaluation[278])) {
            $data_evaluation[278] = 762; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_4" . "\n");
        }

        if (is_null($data_evaluation[279])) {
            $data_evaluation[279] = 763; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_4" . "\n");
        }

        if (is_null($data_evaluation[280])) {
            $data_evaluation[280] = 764; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_5" . "\n");
        }

        if (is_null($data_evaluation[281])) {
            $data_evaluation[281] = 765; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_5" . "\n");
        }

        if (is_null($data_evaluation[282])) {
            $data_evaluation[282] = 766; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_5" . "\n");
        }

        if (is_null($data_evaluation[283])) {
            $data_evaluation[283] = 767; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_6" . "\n");
        }

        if (is_null($data_evaluation[284])) {
            $data_evaluation[284] = 768; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_6" . "\n");
        }

        if (is_null($data_evaluation[285])) {
            $data_evaluation[285] = 769; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_6" . "\n");
        }

        if (is_null($data_evaluation[286])) {
            $data_evaluation[286] = 770; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_7" . "\n");
        }

        if (is_null($data_evaluation[287])) {
            $data_evaluation[287] = 771; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_7" . "\n");
        }

        if (is_null($data_evaluation[288])) {
            $data_evaluation[288] = 772; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_7" . "\n");
        }

        if (is_null($data_evaluation[289])) {
            $data_evaluation[289] = 773; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_8" . "\n");
        }

        if (is_null($data_evaluation[290])) {
            $data_evaluation[290] = 774; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_8" . "\n");
        }

        if (is_null($data_evaluation[291])) {
            $data_evaluation[291] = 775; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_8" . "\n");
        }

        if (is_null($data_evaluation[292])) {
            $data_evaluation[292] = 776; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_9" . "\n");
        }

        if (is_null($data_evaluation[293])) {
            $data_evaluation[293] = 777; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_9" . "\n");
        }

        if (is_null($data_evaluation[294])) {
            $data_evaluation[294] = 778; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_9" . "\n");
        }

        if (is_null($data_evaluation[295])) {
            $data_evaluation[295] = 779; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_10" . "\n");
        }

        if (is_null($data_evaluation[296])) {
            $data_evaluation[296] = 780; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_10" . "\n");
        }

        if (is_null($data_evaluation[297])) {
            $data_evaluation[297] = 781; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_10" . "\n");
        }

        if (is_null($data_evaluation[298])) {
            $data_evaluation[298] = 782; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con entrenamiento_especializado_empresa" . "\n");
        }

        if (is_null($data_evaluation[299])) {
            $data_evaluation[299] = 783; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_empresa" . "\n");
        }

        if (is_null($data_evaluation[300])) {
            $data_evaluation[300] = 784; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_proyecto_empresa" . "\n");
        }

        if (is_null($data_evaluation[301])) {
            $data_evaluation[301] = 785; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con diagnostico_innovacion" . "\n");
        }

        if (is_null($data_evaluation[302])) {
            $data_evaluation[302] = 786; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con puntaje" . "\n");
        }

        if (is_null($data_evaluation[303])) {
            $data_evaluation[303] = 788; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con concepto_evaluador" . "\n");
        }

        if (is_null($data_evaluation[304])) {
            $data_evaluation[304] = 789; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con ejecucion_vaucher" . "\n");
        }

        if (is_null($data_evaluation[305])) {
            $data_evaluation[305] = 795; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_11" . "\n");
        }

        if (is_null($data_evaluation[306])) {
            $data_evaluation[306] = 798; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_11" . "\n");
        }

        if (is_null($data_evaluation[307])) {
            $data_evaluation[307] = 801; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_11" . "\n");
        }
        if (is_null($data_evaluation[308])) {
            $data_evaluation[308] = 807; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con cohorte_postulante_12" . "\n");
        }

        if (is_null($data_evaluation[309])) {
            $data_evaluation[309] = 810; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_etrenamiento_12" . "\n");
        }

        if (is_null($data_evaluation[310])) {
            $data_evaluation[310] = 813; //Sin Registro
            array_push($history_evaluation, "- La fila con correo " . ($resul['correo_electronico']). " no cuenta con aprobacion_talleres_12" . "\n");
        }

        $this->LogValidateResponsesEval .= implode($history_evaluation);

        Storage::disk('local')->put("LogSiinova/LoadUserEvaluation.txt", $this->LogValidateResponsesEval);

        \Log::info("Finaliza la validacion de datos de la evaluación...");

        if (count($history_evaluation) > 0) {
            \Log::warning("Hay datos homologados por \"Sin Registro\" de la plantilla excel, procesando datos...");
            return [ true, $data_evaluation ];
        }
        \Log::info("No se han homologado datos de la plantilla excel, procesando datos......");
        return [ true, $data_evaluation ];
    }

    //get id from option
    private static function getIdOption($str_id, int $count)
    {
        if (strlen($str_id) < 1) return null;

        $str_id = explode('-', trim($str_id));
        if (count($str_id) == $count) {
            return (int) $str_id[0];
        }
        return null;
    }

    private static function getEmailUsuario($email) {
        return Usuario::where('correo_electronico', '=',  trim($email))->first();
    }

    private static function getIdentificationUser($identification)
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
}
//este es el correcto
