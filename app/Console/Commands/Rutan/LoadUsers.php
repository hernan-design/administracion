<?php

namespace App\Console\Commands\Rutan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail, Hash, TuProyecto, DateTime, Config;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\Rol,
    App\Models\Seguridad\RolUsuario,
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
    App\Models\Core\OcupacionPersona,
    App\Console\Commands\LoadSurvey\CargaEncuesta,
    App\Console\Commands\LoadEvaluation\CargaEvaluacion,
    App\Console\Commands\LoadUsers\CargaUsuarios;

class LoadUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rutan:loadusersEntities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios y entidades de RutaN';

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
        Log::info('leyendo');
        //constants
        define("ID_INSTITUTION", Institucion::institution('rutan'));
        define("ID_ROL_INSTITUTION", 77);
        define("ID_ROL_ASSOCIATED", 39);
        define("ID_ROL_USER", 3);
        define("CODIGO_ENCUESTA", 249);
        define("CODIGO_CONVOCATORIA", 634);
        define("ID_PHASE", 366); // fase registro 366
        define("EMAIL_EVALUATOR", 'k.urdo@rutanmedellin.org'); //email del evaluador

        //$history guarda registro de acciones
        //para luego enviar información por correo
        $history = array();

        Excel::load('users_entity_rutan.xlsx', function($reader) use($history) {
            $results = $reader->get()->toArray();
            //Valicación de datos
            $this->validateData($results);

            //array log
            $log = [];
            $log['usuario']      = '';
            $log['entidad']      = '';
            $log['convocatoria'] = '';
            $log['evaluacion']   = '';

            foreach($results as $key => $resul){
                Log::info('registro');
                //user data
                $data_user = [];
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //user email
                $data_user['nombre_usuario']        = trim($resul['nombre_usuario']); //user name
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); //user first lastname
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']); //user second lastname
                $data_user['sexo']                  = $this->getIdOption(trim($resul['sexo']), 2); //user sex
                $data_user['ciudad_nacimiento']     =  $this->getIdOption(trim($resul['ciudad_nacimiento']), 4); //city of birth
                $data_user['fecha_nacimiento']      =  substr(trim($resul['fecha_nacimiento']),0,10); //user date birth
                $data_user['tipo_identificacion']   =  $this->getIdOption(trim($resul['tipo_identificacion']), 2); //user type identification
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //user id number
                $data_user['movil']                 = trim($resul['celular']); //user cellphone
                $data_user['telefono']              = trim($resul['telefono']); //user phone
                $data_user['nivel_academico']       =   $this->getIdOption(trim($resul['nivel_academico']), 2);//user id academic level
                $data_user['id_institucion']        = ID_INSTITUTION;
                $data_user['id_rol_institucion']    = ID_ROL_INSTITUTION;
                $data_user['id_rol_associated']     = ID_ROL_ASSOCIATED;

                //Allows you to identify the fields to be loaded by institution
                $data_require = ["correo_electronico" => true,
                                    "nombre_usuario" => true,
                                    "primer_apellido" => true,
                                    "segundo_apellido" => true,
                                    "sexo" => true,
                                    "ciudad_nacimiento" => true,
                                    "fecha_nacimiento" => true,
                                    "tipo_identificacion" => true,
                                    "numero_identificacion" => true,
                                    "movil" => true,
                                    "telefono" => true,
                                    "nivel_academico" => true,
                    ];

                //datos entidad
                $data_entity = [];
                $data_entity['codigo_clase_empre_insti'] = $this->getIdOption(trim($resul['tipo_entidad']), 2); //empresa o proyecto
                $data_entity['codigo_tipo_identificacion'] = $this->getIdOption(trim($resul['tipo_identificacion_entidad']), 2);
                $data_entity['identificacion_usuario'] = trim($resul['numero_iden_entidad']);
                $data_entity['nombre_usuario'] = trim($resul['nombre_entidad']);
                $data_entity['codigo_ciudad'] = $this->getIdOption(trim($resul['ciudad_entidad']), 4);
                $data_entity['fecha_nacimiento_usuario'] = trim($resul['fecha_inicio_operacion']); //solo empresa
                $data_entity['codigo_categoria_proyecto'] = $this->getIdOption(trim($resul['macro_micro_sector']), 3); //empresa y proyecto
                $data_entity['codigo_etapa_proyecto'] =  $this->getIdOption(trim($resul['tipo_empresa']), 2);
                $data_entity['codigo_sector_interes'] =  $this->getIdOption(trim($resul['acti_economica_entidad']), 2);
                $data_entity['codigo_tipo_sociedad'] =  $this->getIdOption(trim($resul['naturaleza_juridica']), 2);
                $data_entity['cubrimiento_entidad'] =  $this->getIdOption(trim($resul['tamanio_empresa']), 2);
                $data_entity['correo_contacto'] = trim($resul['correo_entidad']);
                $data_entity['biografia_usuario'] = trim($resul['descripcion_entidad']); //biografia usuario
                $data_entity['pagina_web_usuario'] = trim($resul['pagina_web']);
                $data_entity['facebook_usuario'] = trim($resul['facebook']);
                $data_entity['twitter_usuario'] = trim($resul['twitter']);
                $data_entity['linkedin_usuario'] = trim($resul['linkedin']);
                $data_entity['instagram_usuario'] = trim($resul['instagram']);
                $data_entity['telefono_usuario'] = trim($resul['telefono_entidad']);
                $data_entity['numero_whatsapp'] = trim($resul['celular_entidad']);

                $codigo_usuario = '';
                $codigo_usuario_institucion = "";

                DB::beginTransaction();
                try {
                        $codigo_usuario = '';
                        $password = '';
                        $user = $this->getEmailUsuario($data_user['correo_electronico']);
                        if(!$user instanceof Usuario){
                            $user_identification = $this->getIdentificationUser($data_user['numero_identificacion']);
                            if (!$user_identification instanceof Usuario) {

                                $user = CargaUsuarios::store($data_user, $data_require);
                                $codigo_usuario = $user->codigo_usuario;
                                $password = $user->password;

                                // array_push($history, "- Se crea el usuario: " . $data_user['correo_electronico'] ."\n");
                                $log['usuario'] = "Se crea el usuario";
                                // \Log::info("Creo el usuario: " . $data_user['correo_electronico'] . "\n");
                            } else {
                                DB::rollback();
                                // array_push($history, "- Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado \n");
                                $log['usuario'] = "Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado";
                                $history[] = [
                                    'correo'        => $data_user['correo_electronico'],
                                    'usuario'       => $log['usuario'],
                                    'entidad'       => '',
                                    'convocatoria'  => '',
                                    'evaluacion'    => '',
                                ];
                                // \Log::info("Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado \n");
                                continue;
                            }
                        }else{
                            //UPDATE USER
                            $codigo_usuario = $user->codigo_usuario;

                            if($data_user['telefono'] != "")
                                $user->telefono_usuario = $data_user['telefono'];
                            if($data_user['tipo_identificacion'] != "")
                                $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];
                            if($data_user['movil'] != "")
                                $user->movil_usuario = $data_user['movil'];
                            if($data_user['numero_identificacion'] != "")
                                $user->identificacion_usuario = $data_user['numero_identificacion'];
                            if($data_user['ciudad_nacimiento'] != "")
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

                                    // array_push($history, "- Se actualiza el usuario: " . $data_user['correo_electronico'] . "\n");
                                    $log['usuario'] = "Se actualiza el usuario";
                                    // \Log::info("Actualiza el usuario: " . $data_user['correo_electronico'] . "\n");
                                } else {
                                    //DB::rollback();
                                    // array_push($history, "Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado \n");
                                    $log['usuario'] = "Usuario con número de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado en la institución";
                                    $history[] = [
                                        'correo'        => $data_user['correo_electronico'],
                                        'usuario'       => $log['usuario'],
                                        'entidad'       => '',
                                        'convocatoria'  => '',
                                        'evaluacion'  => '',
                                    ];
                                    // \Log::info("Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado UPDATE \n");
                                    //continue;
                                }
                            } else {
                                $id_user_institution = $user_institution->codigo_usuario_institucion;
                                // array_push($history, "- Usuario ya existe: " . $data_user['correo_electronico'] . "\n");
                                $log['usuario'] = "Se actualiza el usuario";
                                // \Log::info("Usuario ya existe: " . $data_user['correo_electronico'] . "\n");
                                //DB::rollback();
                                $history[] = [
                                    'correo'        => $data_user['correo_electronico'],
                                    'usuario'       => $log['usuario'],
                                    'entidad'       => '',
                                    'convocatoria'  => '',
                                    'evaluacion'    => '',
                                ];
                                //continue;
                            }
                        }

                        //Aceptar terminos y condiciones
                        CargaUsuarios::acceptTerms($user, ID_INSTITUTION);

                        //codigo usuario de la entidad creada
                        $codigo_usuario_entidad = '';
                        //create entity
                        if (isset($codigo_usuario) && !empty($codigo_usuario)) {
                            $response = $this->createEntity($codigo_usuario, $data_entity, ID_INSTITUTION);
                            // array_push($history, $response[1] . "\n");
                            Log::warning($response[1]);
                            $log['entidad'] = $response[1];

                            $codigo_usuario_entidad = $response[2];

                            if (!$response[0]) {
                                DB::rollback();
                                $history[] = [
                                    'correo'        => $data_user['correo_electronico'],
                                    'usuario'       => $log['usuario'],
                                    'entidad'       => $log['entidad'],
                                    'convocatoria'  => '',
                                    'evaluacion'    => '',
                                ];
                                continue;
                            }
                        }

                        //validate responses convocatory for register
                        $inscription = $this->validateResponsesOfConvocatory($resul);

                        if ($inscription[0]) {
                            // Log::info("message true");

                            //upload of convocatory responses
                            $data_for_inscription = new \stdClass();
                            $data_for_inscription->codigo_usuario = $codigo_usuario_entidad;
                            $data_for_inscription->email_evaluator = EMAIL_EVALUATOR;
                            $data_for_inscription->codigo_encuesta = CODIGO_ENCUESTA;
                            $data_for_inscription->codigo_convocatoria = CODIGO_CONVOCATORIA;
                            $data_for_inscription->codigo_institucion = ID_INSTITUTION;
                            $data_for_inscription->id_phase = ID_PHASE;
                            $data_for_inscription->has_evaluation = true;
                            $data_for_inscription->answers_form_convocatory = $inscription[1];

                            if ($data_for_inscription->has_evaluation) {
                                $data_for_evaluation_phase = CargaEncuesta::store($data_for_inscription);
                            }else {
                                CargaEncuesta::store($data_for_inscription);
                            }

                            // array_push($history, 'Se inscribió a la convocatoria' . "\n");
                            $log['convocatoria'] = 'Se inscribió a la convocatoria';
                            // Log::info("Se inscribió a la convocatoria'");
                        } else {

                            // array_push($history, 'No Se inscribió a la convocatoria por alguna respuesta' . "\n");
                            $log['convocatoria'] = 'No Se inscribió a la convocatoria por alguna respuesta';
                            // Log::info('No Se inscribió a la convocatoria por alguna respuesta');
                        }

                        //validate responses convocatory for register
                        $evaluation = $this->validateResponsesEvaluation($resul);

                        if ($evaluation[0]) {

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

                            CargaEvaluacion::updateToPhase($data_for_evaluation, $data_for_inscription);

                        } else {
                                $log['evaluacion'] = 'No se efectua la evaluación';
                            }

                        // Send notification to new User
                        $data = [];
                        $data['_inst'] = ID_INSTITUTION;
                        $data['_to'] = $data_user['correo_electronico'];
                        $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                        $data['_subject'] = 'Confirmación de registro plataforma';
                        $data['_template'] = 'emails.user.restorepassword';
                        $data['_current_password'] = $password;
                        $data['_change_password']  = '/password/email';
                        SendEmail::notification($data);

                        DB::commit();

                } catch (\Exception $e) {
                    DB::rollback();
                    Log::info('error catch LoadUser Rutan');
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
            // array_push($history, "Finalizo");

            $filename = sprintf('%s_%s.xlsx', 'reporte_cargue usuarios entidades respuestas', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s', ID_INSTITUTION, $filename);


            $file = Excel::create($filename, function($excel) use ($history) {
                $excel->sheet('Reporte evento', function($sheet)  use($history) {
                    $sheet->setAllBorders('thin');
                    $array = array(
                        'correo',
                        'usuario',
                        'entidad',
                        'convocatoria',
                        'evaluacion'
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
            $data['_to'] = 'juan.tabares@rutanmedellin.org';
            $data['to_copy'] = 'administracion@tuproyecto.com';
            $data['_to_name'] = 'CENTROS DEL VALLE DEL SOFTWARE';
            $data['_subject'] = 'Carga de datos';
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

    private function createEntity($codigo_usuario, $data, $codigo_institucion)
    {
        //User
        $user = new Usuario;
        $user->codigo_estado = Estado::getState('active');
        $user->nombre_usuario = $data['nombre_usuario'];
        $user->biografia_usuario = $data['biografia_usuario'];
        $user->codigo_ciudad = $data['codigo_ciudad'];
        $user->tipo_usuario = Usuario::typeUser('entity');
        $user->imagen_usuario = Config::get('tuproyecto.no_image');
        $username = TuProyecto::unwantedCharacters($data['nombre_usuario']);
        // Validate unique nickname
        $usernickname= Usuario::validateNinckname(str_slug(str_limit($username, 16), ""));
        $user->nickname_usuario = $usernickname;
        if($data['identificacion_usuario'] != ''){
            // Valid User
            $userValid = Usuario::join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                ->where('identificacion_usuario', $data['identificacion_usuario'])
                ->where('tipo_usuario', Usuario::typeUser('entity'))
                ->where('usuario_institucion.codigo_institucion', '=', $codigo_institucion)
                ->first();

            if($userValid instanceof Usuario) {

                Log::info(sprintf('%s:%s', 'LoadUser rutan:store', 'Ya existe una empresa con esa identificacion'));
                $message = "Ya existe una entidad con esa identificación: {$data['identificacion_usuario']}";

                //retorna bool para validar si se crea usuario
                //retorna el mensaje
                //retorna null para que no dé error al intentar recuperar 3er elemento del array
                return [false, $message, null];
            }

            $user->identificacion_usuario = $data['identificacion_usuario'];
        }

        if($data['pagina_web_usuario'] != '' ){
            $user->pagina_web_usuario = $data['pagina_web_usuario'];
        }

        if($data['facebook_usuario'] != '' ){
            $user->facebook_usuario = $data['facebook_usuario'];
        }

        if($data['twitter_usuario'] != '' ){
            $user->twitter_usuario = $data['twitter_usuario'];
        }

        if($data['linkedin_usuario'] != '' ){
            $user->linkedin_usuario = $data['linkedin_usuario'];
        }

        if($data['instagram_usuario'] != '' ){
            $user->instagram_usuario = $data['instagram_usuario'];
        }

        if($data['telefono_usuario'] != '' ){
            $user->telefono_usuario = $data['telefono_usuario'];
        }

        if($data['numero_whatsapp'] != '' ){
            $user->numero_whatsapp = $data['numero_whatsapp'];
        }

        if($data['fecha_nacimiento_usuario'] && preg_replace("/[^a-zA-Z0-9]/", "", $data['fecha_nacimiento_usuario']) != '' ){
            $user->fecha_nacimiento_usuario = $data['fecha_nacimiento_usuario'];
        }

        $user->save();

        //user Institution
        $userInstitution = new UsuarioInstitucion;
        $userInstitution->codigo_usuario = $user->codigo_usuario;
        $userInstitution->codigo_institucion = $codigo_institucion;
        $userInstitution->codigo_estado = Estado::getState('active');
        $userInstitution->fecha_registro_usuario = date('Y-m-d');
        $userInstitution->hora_registro_usuario = date('H:i:s');
        $userInstitution->codigo_rol = Rol::getRoleId('user');
        $userInstitution->save();

        // Entity
        $entity = new Entidad;
        $entity->fill($data);
        $entity->codigo_usuario = $user->codigo_usuario;
        $entity->seg_codigo_usuario = $codigo_usuario;
        $entity->codigo_clase_empre_insti = $data['codigo_clase_empre_insti'];
        $entity->codigo_tipo_identificacion = $data['codigo_tipo_identificacion'];

        if ( $data['correo_contacto'] != '' ) {
            $entity->correo_contacto_entidad = $data['correo_contacto'];
        }

        $entity->save();

        //Data Entity
        $dataEntity = new DatoEntidad;
        $dataEntity->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
        $dataEntity->codigo_privacidad_contenido = PrivacidadContenido::getPrivacy('public');
        $dataEntity->codigo_estado = Estado::getState('active');
        if($data['codigo_categoria_proyecto'] != 0){
            $dataEntity->codigo_categoria_proyecto = $data['codigo_categoria_proyecto'];
        }

        $classEntrepreneurship = ClaseEmprendimientoInstitucion::find($entity->codigo_clase_empre_insti);
        if(!$classEntrepreneurship instanceof ClaseEmprendimientoInstitucion) {
            Log::info('No se encuentra ClaseEmprendimientoInstitucion');
            die;
        }

        switch ($classEntrepreneurship->codigo_clase_emprendimiento) {
            case ClaseEmprendimiento::getClass('project'):
                if($data['codigo_etapa_proyecto'] != ''){
                    $dataEntity->codigo_etapa_proyecto = $data['codigo_etapa_proyecto'];
                }
            break;

            case ClaseEmprendimiento::getClass('enterprise'):
                if($data['codigo_etapa_proyecto']){
                    $dataEntity->codigo_etapa_proyecto = $data['codigo_etapa_proyecto'];
                }
            break;
        }

        $dataEntity->save();
        $message = "Se creó la entidad: " . $data['nombre_usuario'];

        //retorna bool para validar si se crea usuario
        //retorna el mensaje
        //retorna codigo_usuario de la entidad creada
        return [true, $message, $user->codigo_usuario];
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function validateData($results): void
    {
        Log::info('validando datos');
        $history = array();
        foreach($results as $key => $resul){
            //user data
            $data_user = [];
            $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //OBLIGAROTIO
            $data_user['nombre_usuario']        = trim($resul['nombre_usuario']); //OBLIGAROTIO
            $data_user['primer_apellido']       = trim($resul['primer_apellido']); //OBLIGAROTIO
            $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);
            $user_sex                           = trim($resul['sexo']); //OBLIGAROTIO
            $data_user['sexo']                  = $this->getIdOption($user_sex, 2);
            $user_city_birth                    = trim($resul['ciudad_nacimiento']); //OBLIGAROTIO
            $data_user['ciudad_nacimiento']     =  $this->getIdOption($user_city_birth, 4);
            //$date_birth                       = substr(trim($resul['fecha_nacimiento']),0,10);//OBLIGAROTIO
            //$data_user['fecha_nacimiento'] =  $this->getDateBirth($date_birth);
            $data_user['fecha_nacimiento']   =  substr(trim($resul['fecha_nacimiento']),0,10);
            $user_identification_type           = trim($resul['tipo_identificacion']);//OBLIGAROTIO
            $data_user['tipo_identificacion']   =  $this->getIdOption($user_identification_type, 2);
            $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); ////OBLIGAROTIO
            $data_user['celular']               = trim($resul['celular']); //OBLIGAROTIO
            $data_user['telefono']              = trim($resul['telefono']);
            $user_academic_level                = trim($resul['nivel_academico']);//OBLIGAROTIO
            $data_user['nivel_academico']       = $this->getIdOption($user_academic_level, 2);//OBLIGAROTIO

            if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                array_push($history, "- La fila " . ($key + 2). " tiene correo incorecto" . "\n");
            }

            if (empty($data_user['nombre_usuario']) or empty($data_user['primer_apellido'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene nombre o pellidos incompletos" . "\n");
            }

            if (is_null($data_user['sexo'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene sexo incorrecto" . "\n");
            }

            if (is_null($data_user['ciudad_nacimiento'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene ciudad incorrecta" . "\n");
            }

            if (!$this->validateDate($data_user['fecha_nacimiento'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene fecha_nacimiento incorrecta" . "\n");
            }

            if (is_null($data_user['tipo_identificacion'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene tipo_identificacion incompleto" . "\n");
            }

            if (empty($data_user['numero_identificacion'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion incompleto" . "\n");
            }

            if (empty($data_user['celular'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene celular incompleto" . "\n");
            }

            if (is_null($data_user['nivel_academico'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene nivel_academico incorrecto" . "\n");
            }

            //datos entidad
            $data_entity = [];
            $data_entity['codigo_clase_empre_insti']   = $this->getIdOption(trim($resul['tipo_entidad']), 2); //empresa o proyecto
            $data_entity['codigo_tipo_identificacion'] = $this->getIdOption(trim($resul['tipo_identificacion_entidad']), 2);
            $data_entity['identificacion_usuario']     = trim($resul['numero_iden_entidad']);
            $data_entity['nombre_usuario']             = trim($resul['nombre_entidad']);
            $data_entity['codigo_ciudad']              = $this->getIdOption(trim($resul['ciudad_entidad']), 4);
            $data_entity['fecha_nacimiento_usuario']   = trim($resul['fecha_inicio_operacion']); //solo empresa
            $data_entity['codigo_categoria_proyecto']  = $this->getIdOption(trim($resul['macro_micro_sector']), 3); //empresa y proyecto
            $data_entity['codigo_etapa_proyecto']      =  $this->getIdOption(trim($resul['tipo_empresa']), 2);
            $data_entity['codigo_sector_interes']      =  $this->getIdOption(trim($resul['acti_economica_entidad']), 2);
            $data_entity['codigo_tipo_sociedad']       =  $this->getIdOption(trim($resul['naturaleza_juridica']), 2);
            $data_entity['cubrimiento_entidad']        =  $this->getIdOption(trim($resul['tamanio_empresa']), 2);
            $data_entity['correo_contacto']            = trim($resul['correo_entidad']);
            $data_entity['biografia_usuario']          = trim($resul['descripcion_entidad']); //biografia usuario
            $data_entity['telefono_usuario']           = trim($resul['telefono_entidad']);
            $data_entity['numero_whatsapp']            = trim($resul['celular_entidad']);

            if (is_null($data_entity['codigo_clase_empre_insti'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene tipo_entidad incorrecto" . "\n");
            } else {
                if ($data_entity['codigo_clase_empre_insti'] == 61) { //empresa = 61

                    if (is_null($data_entity['codigo_tipo_identificacion'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene tipo_identificacion_entidad incorrecto" . "\n");
                    }

                    if (empty($data_entity['identificacion_usuario'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene numero_iden_entidad incompleto" . "\n");
                    }

                    if (empty($data_entity['fecha_nacimiento_usuario'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene fecha_inicio_operacion incompleto" . "\n");
                    }


                    if (is_null($data_entity['codigo_sector_interes'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene acti_economica_entidad incorrecto" . "\n");
                    }

                    if (is_null($data_entity['codigo_tipo_sociedad'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene naturaleza_juridica incorrecto" . "\n");
                    }

                    if (is_null($data_entity['cubrimiento_entidad'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene tamanio_empresa incorrecto" . "\n");
                    }

                    if (empty($data_entity['numero_whatsapp'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene celular_entidad incompleto" . "\n");
                    }

                    if (empty($data_entity['telefono_usuario'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene telefono_entidad incompleto" . "\n");
                    }

                    if (empty($data_entity['correo_contacto'])) {
                        array_push($history, "- La fila " . ($key + 2). " tiene correo_entidad incompleto" . "\n");
                    }
                }
            }

            if (empty($data_entity['nombre_usuario'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene nombre_entidad incompleto" . "\n");
            }


            if (is_null($data_entity['codigo_categoria_proyecto'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene macro_micro_sector incorrecto" . "\n");
            }

            if (is_null($data_entity['codigo_ciudad'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene ciudad_entidad incorrecto" . "\n");
            }

            if (is_null($data_entity['codigo_etapa_proyecto'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene tipo_empresa incorrecto" . "\n");
            }

            if (empty($data_entity['biografia_usuario'])) {
                array_push($history, "- La fila " . ($key + 2). " tiene descripcion_entidad incompleto" . "\n");
            }

        }

        $content = implode($history);

            Storage::disk('local')->put('LogRutaN/LoarUserEntities.txt', $content);

            \Log::info("Fin validacion datos");

            if (count($history) > 0) {
                \Log::info("Hay datos incorrectos en excel, valida archivo Log en Storage/app/LogRutaN/LoarUserEntities.txt");
                die;
            }
            \Log::info("No hay errrores en la validacion");
            return;
    }

    private function validateResponsesOfConvocatory($resul): array
    {
        Log::info('validando datos respuestas');
        $history = array();
        // foreach($results as $key => $resul){
            //user data
            //$data_user[codigo_pregunta] = respuesta_usuario
            $data_user = [];
            $data_user[6026] = $this->getIdOption($resul['vive_medellin'], 2); //OBLIGAROTIO
            $data_user[6027] = $this->getIdOption($resul['barrio_vereda_comuna_zona_ciudadana'], 4); //OBLIGAROTIO
            $data_user[6032] = $this->getIdOption($resul['nomenclatura_ciudadana'], 2); //OBLIGAROTIO
            $data_user[6033] = trim($resul['direccion_ciudadana']); //OBLIGAROTIO
            $data_user[6034] = trim($resul['factura_servicios']); //OBLIGAROTIO carga de archivo
            $data_user[6035] = $this->getIdOption($resul['estrato_socieconomico'], 2); //OBLIGAROTIO
            $data_user[6037] = $this->getIdOption($resul['caracterizacion_ciudadana'], 2); //OBLIGAROTIO
            $data_user[6038] = $this->getIdOption($resul['empleado_actualmente'], 2); //OBLIGAROTIO
            $data_user[6039] = trim($resul['nombre_empresa_labora']); //OBLIGAROTIO texto
            $data_user[6040] = $this->getIdOption($resul['estado_civil'], 2); //OBLIGAROTIO
            $data_user[6041] = $this->getIdOption($resul['cabeza_hogar'], 2); //OBLIGAROTIO
            $data_user[6043] = $this->getOption($resul['caracteristicas_diferenciales']); //OBLIGAROTIO
            $data_user[6044] = $this->getIdOption($resul['empresa_registrada'], 2); //OBLIGAROTIO
            $data_user[6045] = trim($resul['camara_comercio']); //OBLIGAROTIO carga de archivo
            $data_user[6046] = $this->getIdOption($resul['naturaleza_juridica_convocatoria'], 2); //OBLIGAROTIO
            $data_user[6047] = $this->getIdOption($resul['tamanio_empresa_convocatoria'], 2); //OBLIGAROTIO
            $data_user[6048] = trim($resul['productos_servicios_ofrece']); //OBLIGAROTIO
            $data_user[6049] = trim($resul['numero_empleados']); //OBLIGAROTIO
            $data_user[6051] = $this->getIdOption($resul['importa'], 2); //OBLIGAROTIO
            $data_user[6052] = $this->getIdOption($resul['exporta'], 2); //OBLIGAROTIO
            $data_user[6053] = $this->getIdOption($resul['asociada_red'], 2); //OBLIGAROTIO
            $data_user[6054] = $this->getIdOption($resul['asociada_unidad_productiva'], 2); //OBLIGAROTIO
            $data_user[6055] = $this->getIdOption($resul['pertenece_cluster'], 2); //OBLIGAROTIO
            $data_user[6057] = $this->getIdOption($resul['nomenclatura_empresa'], 2); //OBLIGAROTIO
            $data_user[6058] = trim($resul['direccion_empresa']); //OBLIGAROTIO
            $data_user[6059] = $this->getIdOption($resul['barrio_vereda_comuna_zona_empresa'], 4); //OBLIGAROTIO
            $data_user[6173] = $this->getIdOption($resul['pertenece_sector_creativo_cultural'], 2); //OBLIGAROTIO
            $data_user[6060] = $this->getIdOption($resul['identificacion_iniciativa_negocio'], 2); //OBLIGAROTIO
            $data_user[6061] = $this->getIdOption($resul['fortalecimiento_pertinente'], 2); //OBLIGAROTIO - CONDICIONADO
            $data_user[6062] = $this->getIdOption($resul['componente_tecnologico_1'], 2); //OBLIGAROTIO
            $data_user[6063] = $this->getIdOption($resul['componente_tecnologico_2'], 2); //OBLIGAROTIO
            $data_user[6064] = trim($resul['evidencia_produc_servic']); //OBLIGAROTIO carga archivo
            $data_user[6065] = $this->getIdOption($resul['componente_tecnologico_3'], 2); //OBLIGAROTIO
            $data_user[6066] = trim($resul['problema_resolvemos']); //OBLIGAROTIO
            $data_user[6067] = trim($resul['nuestra_solucion']); //OBLIGAROTIO
            $data_user[6068] = trim($resul['logo']); //carga archivo- string
            $data_user[6069] = trim($resul['perfil']); //
            $data_user[6070] = trim($resul['describe_mercado']); //OBLIGAROTIO
            $data_user[6071] = $this->getIdOption($resul['sectores_aplicacion'], 2); //OBLIGAROTIO
            $data_user[6072] = $this->getIdOption($resul['subsectores_aplicacion'], 2); //OBLIGAROTIO
            $data_user[6073] = trim($resul['tamanio_mercado']); //OBLIGAROTIO
            $data_user[6074] = trim($resul['estrategia_monetizacion']); //OBLIGAROTIO
            $data_user[6075] = trim($resul['competencia']); //OBLIGAROTIO
            $data_user[6076] = trim($resul['ventaja_competitiva']); //OBLIGAROTIO
            $data_user[6077] = $this->getIdOption($resul['tipo_propiedad_intelectual'], 2); //OBLIGAROTIO
            $data_user[6078] = trim($resul['acceso_mercados']); //OBLIGAROTIO
            $data_user[6079] = trim($resul['necesidades_comercializacion']); //OBLIGAROTIO
            $data_user[6080] = $this->getIdOption($resul['integrante_1'], 2); //OBLIGAROTIO
            $data_user[6081] = trim($resul['nombre_integrante_1']); //OBLIGAROTIO
            $data_user[6082] = trim($resul['rol_integrante_1']); //OBLIGAROTIO
            $data_user[6083] = trim($resul['describe_perfil_integrante_1']); //OBLIGAROTIO
            $data_user[6084] = $this->getIdOption($resul['integrante_2'], 2); //OBLIGAROTIO
            $data_user[6085] = trim($resul['nombre_integrante_2']); //OBLIGAROTIO
            $data_user[6086] = trim($resul['rol_integrante_2']); //OBLIGAROTIO
            $data_user[6087] = trim($resul['describe_perfil_integrante_2']); //OBLIGAROTIO
            $data_user[6088] = $this->getIdOption($resul['integrante_3'], 2); //OBLIGAROTIO
            $data_user[6089] = trim($resul['nombre_integrante_3']); //OBLIGAROTIO
            $data_user[6090] = trim($resul['rol_integrante_3']); //OBLIGAROTIO
            $data_user[6091] = trim($resul['describe_perfil_integrante_3']); //OBLIGAROTIO
            $data_user[6092] = $this->getIdOption($resul['integrante_4'], 2); //OBLIGAROTIO
            $data_user[6093] = trim($resul['nombre_integrante_4']); //OBLIGAROTIO
            $data_user[6094] = trim($resul['rol_integrante_4']); //OBLIGAROTIO
            $data_user[6095] = trim($resul['describe_perfil_integrante_4']); //OBLIGAROTIO
            $data_user[6096] = $this->getIdOption($resul['cuenta_con_ventas'], 2); //OBLIGAROTIO
            $data_user[6097] = trim($resul['ventas_anuales']); //OBLIGAROTIO
            $data_user[6098] = trim($resul['unidades_vendidas']); //OBLIGAROTIO
            $data_user[6099] = trim($resul['tasa_crecimiento']); //OBLIGAROTIO
            $data_user[6100] = trim($resul['clientes_cautivos']); //OBLIGAROTIO
            $data_user[6101] = trim($resul['porcentaje_recurrencia_compra']); //OBLIGAROTIO
            $data_user[6103] = $this->getIdOption($resul['tiempo_salida_mercado'], 2); //OBLIGAROTIO
            $data_user[6104] = $this->getIdOption($resul['capitalizaciones_previas'], 2); //OBLIGAROTIO
            $data_user[6105] = trim($resul['indica_valor']); //OBLIGAROTIO
            $data_user[6106] = trim($resul['numero_inversionistas_socios']); //OBLIGAROTIO
            $data_user[6107] = trim($resul['capital_requerido']); //OBLIGAROTIO
            $data_user[6170] = $this->getIdOption($resul['forma_inscripcion'], 2); //OBLIGAROTIO
            $data_user[6171] = $this->getIdOption($resul['sede_inscripcion'], 2); //OBLIGAROTIO
            $data_user[6172] = $this->getIdOption($resul['como_se_entero'], 2); //OBLIGAROTIO

            if (is_null($data_user[6026]) || $data_user[6026] == 11846) {
                array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " no vive en medellín, no se registra a convocatoria" . "\n");
            } elseif ($data_user[6026] == 11845){

                if (is_null($data_user[6027])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene barrio_vereda_comuna_zona_ciudadana incorrecto" . "\n");
                }

                if (is_null($data_user[6032])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene nomenclatura_ciudadana incorrecto" . "\n");
                }

                if (empty($data_user[6033])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene direccion_ciudadana incorrecto" . "\n");
                }

                if (empty($data_user[6034])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene factura_servicios incorrecto" . "\n");
                }

                if (is_null($data_user[6035])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene estrato_socieconomico incorrecto" . "\n");
                }

                if (is_null($data_user[6037])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene caracterizacion_ciudadana incorrecto" . "\n");
                } else {
                    if ($data_user[6037] == 12237) { //12237 emprendedor

                        if (is_null($data_user[6038])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene empleado_actualmente incorrecto" . "\n");
                        } elseif($data_user[6038]==12238) {//12238 = si empleado actualmente
                            if (is_null($data_user[6039])) {
                                array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene nombre_empresa_labora incorrecto" . "\n");
                            }
                        }
                        
                    }
                }

                if (is_null($data_user[6040])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene estado_civil incorrecto" . "\n");
                }

                if (is_null($data_user[6041])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene cabeza_hogar incorrecto" . "\n");
                }

                if (is_null($data_user[6043])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene caracteristicas_diferenciales incorrecto" . "\n");
                }

                if (is_null($data_user[6173])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene pertenece_sector_creativo_cultural incorrecto" . "\n");
                }

                if (is_null($data_user[6044])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene empresa_registrada incorrecto" . "\n");
                } elseif($data_user[6044] == 12261) { //12261 = Si

                    if (empty($data_user[6045])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene camara_comercio incorrecto" . "\n");
                    }

                    if (is_null($data_user[6046])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene naturaleza_juridica_convocatoria incorrecto" . "\n");
                    }

                    if (is_null($data_user[6047])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene tamanio_empresa_convocatoria incorrecto" . "\n");
                    }

                    if (empty($data_user[6048])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene productos_servicios_ofrece incorrecto" . "\n");
                    }

                    if (empty($data_user[6049])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene numero_empleados incorrecto" . "\n");
                    }

                    if (is_null($data_user[6051])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene importa incorrecto" . "\n");
                    }

                    if (is_null($data_user[6052])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene exporta incorrecto" . "\n");
                    }

                    if (is_null($data_user[6053])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene asociada_red incorrecto" . "\n");
                    }

                    if (is_null($data_user[6054])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene asociada_unidad_productiva incorrecto" . "\n");
                    }

                    if (is_null($data_user[6055])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene pertenece_cluster incorrecto" . "\n");
                    }

                    if (is_null($data_user[6057])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene nomenclatura_empresa incorrecto" . "\n");
                    }

                    if (empty($data_user[6058])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene direccion_empresa incorrecto" . "\n");
                    }

                    if (is_null($data_user[6059])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene barrio_vereda_comuna_zona_empresa incorrecto" . "\n");
                    }
                }

                if (is_null($data_user[6060])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene identificacion_iniciativa_negocio incorrecto" . "\n");
                } else {

                    //si identificacion_iniciativa_negocio es 12669-Tengo una idea que quiero comenzar a explorar
                    //entra a validar esta respuesta, fortalecimiento_pertinente
                    //tambien entra a validar respuesta componente_tecnologico_1
                    if ($data_user[6060] == 12669) {

                        if (is_null($data_user[6061])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene fortalecimiento_pertinente incorrecto" . "\n");
                        }

                        if (is_null($data_user[6062])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene componente_tecnologico_1 incorrecto" . "\n");
                        }
                    }


                    if ($data_user[6060] == 12670) {

                        if (is_null($data_user[6063])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene componente_tecnologico_2 incorrecto" . "\n");
                        }
                    }

                    if ($data_user[6060] == 12671) {

                        if (empty($data_user[6064])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene evidencia_produc_servic incorrecto" . "\n");
                        }

                        if (is_null($data_user[6065])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene componente_tecnologico_3 incorrecto" . "\n");
                        }

                        if (empty($data_user[6066])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene problema_resolvemos incorrecto" . "\n");
                        }

                        if (empty($data_user[6067])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene nuestra_solucion incorrecto" . "\n");
                        }

                        if (empty($data_user[6070])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene describe_mercado incorrecto" . "\n");
                        }

                        if (is_null($data_user[6071])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene sectores_aplicacion incorrecto" . "\n");
                        }

                        if (is_null($data_user[6072])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene subsectores_aplicacion incorrecto" . "\n");
                        }

                        if (empty($data_user[6073])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene tamanio_mercado incorrecto" . "\n");
                        }

                        if (empty($data_user[6074])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene estrategia_monetizacion incorrecto" . "\n");
                        }

                        if (empty($data_user[6075])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene competencia incorrecto" . "\n");
                        }

                        if (empty($data_user[6076])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene ventaja_competitiva incorrecto" . "\n");
                        }

                        if (is_null($data_user[6077])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene tipo_propiedad_intelectual incorrecto" . "\n");
                        }


                        if (empty($data_user[6078])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene acceso_mercados incorrecto" . "\n");
                        }

                        if (empty($data_user[6079])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene necesidades_comercializacion incorrecto" . "\n");
                        }


                    }

                }

                if (is_null($data_user[6096])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene cuenta_con_ventas incorrecto" . "\n");
                } else {

                    if ($data_user[6096] == 13031) { //13031 = No
                        if (is_null($data_user[6103])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene tiempo_salida_mercado incorrecto" . "\n");
                        }
                    } elseif($data_user[6096] == 13030) {  //13030 = Si
                        if (empty($data_user[6097])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene ventas_anuales incorrecto" . "\n");
                        }

                        if (empty($data_user[6098])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene unidades_vendidas incorrecto" . "\n");
                        }

                        if (empty($data_user[6099])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene tasa_crecimiento incorrecto" . "\n");
                        }

                        if (empty($data_user[6100])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene clientes_cautivos incorrecto" . "\n");
                        }

                        if (empty($data_user[6101])) {
                            array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene porcentaje_recurrencia_compra incorrecto" . "\n");
                        }
                    }
                }

                if (is_null($data_user[6104])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene capitalizaciones_previas incorrecto" . "\n");
                } else {
                    if (empty($data_user[6105])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene indica_valor incorrecto" . "\n");
                    }

                    if (empty($data_user[6106])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene numero_inversionistas_socios incorrecto" . "\n");
                    }

                    if (empty($data_user[6107])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene capital_requerido incorrecto" . "\n");
                    }
                }

                if (is_null($data_user[6170])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene forma_inscripcion incorrecto" . "\n");
                } elseif($data_user[6170] == 13115) {
                    if (is_null($data_user[6171])) {
                        array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene sede_inscripcion incorrecto" . "\n");
                    }
                }

                if (is_null($data_user[6172])) {
                    array_push($history, "- La fila con correo " . ($resul['correo_electronico']). " tiene como_se_entero incorrecto" . "\n");
                }

            }

            $this->LogValidateResponses .= implode($history);

            Storage::disk('local')->put("LogRutaN/LoarUserResponses.txt", $this->LogValidateResponses);

            \Log::info("Fin validacion respuestas");

            if (count($history) > 0) {
                \Log::info("Hay respuestas erroneas en excel");
                return [ false, $data_user ];
            }
            \Log::info("No hay errrores en la validacion respuestas convocatoria");
            return [ true, $data_user ];

        // }
    }

    private function validateResponsesEvaluation($resul): array
    {
        Log::info('Validando datos de la evaluación...');
        // the keys would should the codigo_item_evaluacion
        $history_evaluation = array();
        $data_evaluation= [];
        $data_evaluation[255] = $this->getIdOption($resul['emprendedor_reside_medellin'], 2);
        $data_evaluation[256] = $this->getIdOption($resul['emprendedor_mayor_edad'], 2);
        $data_evaluation[257] = $this->getIdOption($resul['base_tecnologica'], 2);
        $data_evaluation[258] = $this->getIdOption($resul['participo_ruta_emprendimiento'], 2);
        $data_evaluation[259] = $this->getIdOption($resul['etapa_perfilar'], 2);
        $data_evaluation[260] = $this->getIdOption($resul['economia_cultural_creativa'], 2);
        $data_evaluation[261] = $this->getIdOption($resul['camara_comercio_medellin'], 2);
        $data_evaluation['comentario'] = trim($resul['observaciones']) ? trim($resul['observaciones']) : 'Sin Registro';

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

        Storage::disk('local')->put("LogRutaN/LoadUserEvaluation.txt", $this->LogValidateResponsesEval);

        \Log::info("Finaliza la validacion de datos de la evaluación...");

        if (count($history_evaluation) > 0) {
            \Log::critical("FATAL ERROR => Faltan datos de la evaluación en la plantilla excel...");
            return [false, $data_evaluation ];
        }
        \Log::info("No se presentan errores en los datos de evaluación, procesando datos......");
        return [ true, $data_evaluation ];
    }

}
//este es el correcto
