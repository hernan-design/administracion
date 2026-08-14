<?php

namespace App\Console\Commands\Innpulsa;

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
    App\Models\Param\TerminosCondiciones,
    App\Models\Seguridad\AceptacionTerminos,
    App\Console\Commands\LoadUsers\CargaUsuarios;

class LoadUsersEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:load_users_entitys {path_archivo?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios y entidades de Innpulsa';

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
        define("ID_INSTITUTION", Institucion::institution('innpulsa'));
        define("ID_ROL_INSTITUTION", 22);
        define("ID_ROL_ASSOCIATED", 39);
        define("ID_ROL_USER", 3);

        $history = array();

        $path = $this->argument('path_archivo');
        $path_archivo = isset($path) ? $path : './cargue_usuarios_innpulsa.xlsx';

        Excel::load($path_archivo, function($reader) use($history) {
            $results = $reader->get()->toArray();
            //Valicación de datos
            $this->validateData($results);
            \Log::info('pasa validacion');
            foreach($results as $key => $resul){
                //si los campos obligatorias están vacios se entiende que es el final
                //del archivo
                if ($this->isFinal($resul)) break;

                $data_user = [];
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //OBLIGAROTIO
                $data_user['nombre_usuario']        = trim($resul['nombre_usuario']); //OBLIGAROTIO
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); //OBLIGAROTIO
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);
                $data_user['sexo']                  = $this->getIdOption(trim($resul['sexo']), 2);//OBLIGAROTIO
                $data_user['ciudad_nacimiento']     = $this->getIdOption(trim($resul['ciudad']), 4);//OBLIGAROTIO
                $data_user['fecha_nacimiento']      =  substr(trim($resul['fecha_de_nacimiento']),0,10);
                $data_user['tipo_identificacion']   =  $this->getIdOption(trim($resul['tipo_identificacion']), 2);//OBLIGAROTIO
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGAROTIO
                $data_user['movil']                 = trim($resul['celular']); //OBLIGAROTIO
                $data_user['telefono']              = trim($resul['telefono']);
                $data_user['nivel_academico']       = $this->getIdOption(trim($resul['nivel_educacion']), 2);//OBLIGAROTIO
                $data_user['codigo_ocupacion']      = $this->getIdOption(trim($resul['ocupacion']), 2); //ocupación (persona) obligatorio
                $data_user['codigo_profesion']      = $this->getIdOption(trim($resul['profesion']), 2); //profesión (persona) obligatorio
                $data_user['id_institucion']        = ID_INSTITUTION;
                $data_user['id_rol_institucion']    = ID_ROL_INSTITUTION;
                $data_user['id_rol_associated']     = ID_ROL_ASSOCIATED;

                //Allows you to identify the fields to be loaded by institution
                //deben ser los mismos que data_user 
                $data_require = [
                    "tipo_identificacion" => true,
                    "numero_identificacion" => true,
                    "nombre_usuario" => true,
                    "primer_apellido" => true,
                    "segundo_apellido" => true,
                    "fecha_nacimiento" => true,
                    "telefono" => true,
                    "movil" => true,
                    "correo_electronico" => true,
                    // "direccion_usuario" => false,
                    "sexo" => true,
                    "nivel_academico" => true,
                    "ciudad_nacimiento" => true,
                    "codigo_ocupacion" => true,
                    "codigo_profesion" => true,
                    // "usuario_institucion" => false,
                ];

                //datos entidad
                $data_entity = [];
                $data_entity['codigo_clase_empre_insti'] = $this->getIdOption(trim($resul['tipo_entidad']), 2); //obligatorio
                $data_entity['codigo_tipo_identificacion'] = $this->getIdOption(trim($resul['tipo_identificacion_empresa']), 2); //obligatorio
                $data_entity['identificacion_usuario'] = trim($resul['numero_identificacion_empresa']); //obligatorio
                $data_entity['nombre_usuario'] = trim($resul['nombre_entidad']); //obligatorio
                $data_entity['codigo_ciudad'] = $this->getIdOption(trim($resul['ciudad_entidad']), 4); //obligatorio
                $data_entity['fecha_nacimiento_usuario'] = trim($resul['fecha_ini_operacion_empresa']); //obligatorio
                $data_entity['codigo_categoria_proyecto'] = $this->getIdOption(trim($resul['industria_entidad']), 2); //obligatorio
                $data_entity['codigo_etapa_proyecto'] =  $this->getIdOption(trim($resul['etapa_entidad']), 2); //obligatorio
                $data_entity['codigo_sector_interes'] =  $this->getIdOption(trim($resul['acti_economica_ciuu_empresa']), 2); //obligatorio
                $data_entity['codigo_tipo_sociedad'] =  $this->getIdOption(trim($resul['tipo_sociedad_empresa']), 2); //obligatorio
                $data_entity['cubrimiento_entidad'] =  $this->getIdOption(trim($resul['cubrimiento_empresa']), 2);
                $data_entity['correo_contacto'] = trim($resul['correo_empresa']);
                $data_entity['biografia_usuario'] = trim($resul['descripcion_entidad']); //biografia usuario
                $data_entity['pagina_web_usuario'] = trim($resul['pagina_web_entidad']);
                $data_entity['facebook_usuario'] = trim($resul['facebook_entidad']);
                $data_entity['twitter_usuario'] = trim($resul['twitter_entidad']);
                $data_entity['linkedin_usuario'] = trim($resul['linkedin_entidad']);
                $data_entity['instagram_usuario'] = trim($resul['instagram_entidad']);
                $data_entity['telefono_usuario'] = trim($resul['telefono_empresa']);
                $data_entity['numero_whatsapp'] = trim($resul['whatsapp_empresa']);

                $password = 'Ii1!'; //default password
                $user = $this->getUser($data_user['correo_electronico'], $data_user['numero_identificacion']);
                try {
                    if(!$user instanceof Usuario){
                            $user = CargaUsuarios::store($data_user, $data_require);
                            $user->password = Hash::make($password);
                            $user->save();

                            $codigo_usuario = $user->codigo_usuario;


                            array_push($history, "- Se crea el usuario: " . $data_user['correo_electronico'] ."\n");
                            \Log::info("Creo el usuario: " . $data_user['correo_electronico'] . "\n");
                    } else {
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
                        if($data_user['ciudad_nacimiento']  != "")
                            $user->codigo_ciudad = $data_user['ciudad_nacimiento'] ;

                        $user->profesion_usuario = $this->getProfesion($data_user['codigo_profesion']);

                        // $password = str_random(8);
                        $user->password = Hash::make($password);
                        $user->save();

                        $user_institution = $this->getusuarioInstitucion($codigo_usuario, ID_INSTITUTION);
                        if(!$user_institution instanceof UsuarioInstitucion){
                            $user_identification = $this->getIdentificationUser($data_user['numero_identificacion']);
                            if (!$user_identification instanceof Usuario) {
                                $n_user_institution = new UsuarioInstitucion;
                                $n_user_institution->codigo_usuario = $codigo_usuario;
                                $n_user_institution->codigo_institucion = ID_INSTITUTION;
                                $n_user_institution->codigo_estado = 8;
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

                                array_push($history, "- Se crea el usuario en la institucion: " . $data_user['correo_electronico'] . "\n");
                                \Log::info("- Se crea el usuario en la institucion:: " . $data_user['correo_electronico'] . "\n");
                            } else {
                                // DB::rollback();
                                array_push($history, "- Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra en la institución \n");
                                \Log::info("- Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado en la institución \n");
                                continue;
                            }
                        }
                        else{
                            array_push($history, "- Usuario ya existe, se actualiza:: " . $data_user['correo_electronico'] . "\n");
                            \Log::info("- Usuario ya existe, se actualiza: " . $data_user['correo_electronico'] . "\n");
                            // DB::rollback();
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error(sprintf('%s:%s', 'Error en rutina ', $e->getMessage()));
                    throw new \Exception("Error en rutina");
                }


                //create entity
                if (isset($user->codigo_usuario) && !empty($user->codigo_usuario)) {
                    $response = $this->createEntity($user->codigo_usuario, $data_entity, ID_INSTITUTION);
                    array_push($history, $response[1] . "\n");
                    if (!$response[0]) {
                        Log::info("245");
                        // DB::rollback();
                        continue;
                    }
                }

                // Envía notificación a nuevo usuario => pendiente por confirmar proceso con innpulsa
                /*$data = [];
                $data['_inst'] = 61;
                $data['_to'] = $data_user['correo_electronico'];
                $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                $data['_subject'] = 'Confirmación de registro plataforma';
                $data['_template'] = 'emails.user.restorepassword';
                $data['_current_password'] = $password;
                $data['_change_password']  = '/password/email';
                SendEmail::notification($data);*/

                //Aceptar terminos y condiciones
                CargaUsuarios::acceptTerms($user, ID_INSTITUTION);
            }

            array_push($history, "Finalizo");

            // Send notification
            //Envía notificación a administradores
            $data = [];
            $data['_inst'] = ID_INSTITUTION;
            $data['_to'] = 'administracion@tuproyecto.com';
            $data['to_copy'] = ['ruben.gantiva@innpulsacolombia.com' , 'jorge.uribe@innpulsacolombia.com'];
            $data['_to_name'] = 'INNPULSA';
            $data['_subject'] = 'Carga de Usuario & Entidades Innpulsa';
            $data['_template'] = 'emails.user.notificationdata';
            $data['_history'] = $history;

            SendEmail::notification($data);
        });
        \Log::info("Finalizo");
    }

    //get id from option
    private function getIdOption(string $str_id, int $count)
    {
        $str_id = explode('-', trim($str_id));
        if (count($str_id) == $count) {
            return (int) $str_id[0];
        }
        return null;
    }

    public static function getUser($email, $numero_identificacion) {
        return Usuario::where('correo_electronico', '=',  $email)
        ->orWhere('identificacion_usuario', $numero_identificacion)
        ->first();
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

                Log::info(sprintf('%s:%s', 'LoadUser Innpulsa:store', 'Ya existe una empresa con esa identificacion'));
                $message = "Ya existe una entidad con esa identificación: {$data['identificacion_usuario']}" . " \n";
                return [false, $message];
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
        Log::info($entity->codigo_tipo_identificacion);
        Log::info($data['codigo_tipo_identificacion']);
        // dd($entity->codigo_tipo_identificacion);
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
        $message = "Se creó la entidad: " . $data['nombre_usuario'] . "\n";
        return [true, $message];
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
        $history = $this->validateIndex($results, $history);

        if (!count($history) > 0) {
            foreach($results as $key => $resul){
                //si los campos obligatorias están vacios se entiende que es el final
                //del archivo
                if ($this->isFinal($resul)) break;

                //user data
                $data_user = [];
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //OBLIGAROTIO
                $data_user['nombre_usuario']        = trim($resul['nombre_usuario']); //OBLIGAROTIO
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); //OBLIGAROTIO
                $data_user['sexo']                  = $this->getIdOption(trim($resul['sexo']), 2);//OBLIGAROTIO
                $data_user['ciudad']                = $this->getIdOption(trim($resul['ciudad']), 4);//OBLIGAROTIO
                $data_user['fecha_nacimiento']   =  substr(trim($resul['fecha_de_nacimiento']),0,10);
                $data_user['tipo_identificacion']   =  $this->getIdOption(trim($resul['tipo_identificacion']), 2);//OBLIGAROTIO
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGAROTIO
                $data_user['celular']               = trim($resul['celular']); //OBLIGAROTIO
                $data_user['telefono']              = trim($resul['telefono']);
                $data_user['nivel_educacion']       = $this->getIdOption(trim($resul['nivel_educacion']), 2);//OBLIGAROTIO
                $data_user['ocupacion']             = $this->getIdOption(trim($resul['ocupacion']), 2); //ocupación (persona) obligatorio
                $data_user['profesion']             = $this->getIdOption(trim($resul['profesion']), 2); //profesión (persona) obligatorio

                if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                    array_push($history, "- La fila " . ($key + 2). " tiene correo incorecto" . "\n");
                }

                if (empty($data_user['nombre_usuario']) or empty($data_user['primer_apellido'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene nombre o pellidos incompletos" . "\n");
                }

                if (is_null($data_user['sexo'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene sexo incorrecto" . "\n");
                }

                if (is_null($data_user['ciudad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene ciudad incorrecta" . "\n");
                }

                if (!$this->validateDate($data_user['fecha_nacimiento'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene fecha_de_nacimiento incorrecta" . "\n");
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

                if (is_null($data_user['nivel_educacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene nivel_educacion incorrecto" . "\n");
                }

                if (is_null($data_user['ocupacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene ocupacion incorrecto" . "\n");
                }

                if (is_null($data_user['profesion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene profesion incorrecto" . "\n");
                }

                //datos entidad
                $data_entity = [];
                $data_entity['codigo_clase_empre_insti'] = $this->getIdOption(trim($resul['tipo_entidad']), 2); //obligatorio
                $data_entity['codigo_tipo_identificacion'] = $this->getIdOption(trim($resul['tipo_identificacion_empresa']), 2); //obligatorio
                $data_entity['identificacion_usuario'] = trim($resul['numero_identificacion_empresa']); //obligatorio
                $data_entity['nombre_usuario'] = trim($resul['nombre_entidad']); //obligatorio
                $data_entity['codigo_ciudad'] = $this->getIdOption(trim($resul['ciudad_entidad']), 4); //obligatorio
                $data_entity['fecha_nacimiento_usuario'] = trim($resul['fecha_ini_operacion_empresa']); //obligatorio
                $data_entity['codigo_categoria_proyecto'] = $this->getIdOption(trim($resul['industria_entidad']), 2); //obligatorio
                $data_entity['codigo_etapa_proyecto'] =  $this->getIdOption(trim($resul['etapa_entidad']), 2); //obligatorio
                $data_entity['codigo_sector_interes'] =  $this->getIdOption(trim($resul['acti_economica_ciuu_empresa']), 2); //obligatorio
                $data_entity['codigo_tipo_sociedad'] =  $this->getIdOption(trim($resul['tipo_sociedad_empresa']), 2); //obligatorio
                $data_entity['cubrimiento_entidad'] =  $this->getIdOption(trim($resul['cubrimiento_empresa']), 2);
                //$data_entity['correo_contacto'] = trim($resul['correo_empresa']);
                $data_entity['biografia_usuario'] = trim($resul['descripcion_entidad']); //biografia usuario

                if (is_null($data_entity['codigo_clase_empre_insti'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene tipo_entidad incorrecto" . "\n");
                } else {
                    if ($data_entity['codigo_clase_empre_insti'] == 2) {

                        if (is_null($data_entity['codigo_tipo_identificacion'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene tipo_identificacion_empresa incorrecto" . "\n");
                        }
            
                        if (empty($data_entity['identificacion_usuario'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion_empresa incompleto" . "\n");
                        }
                        
                        if (empty($data_entity['fecha_nacimiento_usuario'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene fecha_ini_operacion_empresa incompleto" . "\n");
                        }
            
                        if (is_null($data_entity['codigo_categoria_proyecto'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene industria_entidad incorrecto" . "\n");
                        }
            
                        if (is_null($data_entity['codigo_sector_interes'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene acti_economica_ciuu_empresa incorrecto" . "\n");
                        }
            
                        if (is_null($data_entity['codigo_tipo_sociedad'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene tipo_sociedad_empresa incorrecto" . "\n");
                        }
            
                        if (is_null($data_entity['cubrimiento_entidad'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene cubrimiento_empresa incorrecto" . "\n");
                        }



                    }
                }


                if (empty($data_entity['nombre_usuario'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene nombre_entidad incompleto" . "\n");
                }

                if (is_null($data_entity['codigo_ciudad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene ciudad_entidad incorrecto" . "\n");
                }

                if (is_null($data_entity['codigo_etapa_proyecto'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene etapa_entidad incorrecto" . "\n");
                }

                // if (!empty($data_entity['correo_contacto']) && !filter_var($data_entity['correo_contacto'], FILTER_VALIDATE_EMAIL)) {
                //     array_push($history, "- La fila " . ($key + 2). " tiene correo_empresa incompleto" . "\n");
                // }

                if (empty($data_entity['biografia_usuario'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene descripcion_entidad incompleto" . "\n");
                }

            }
        }
        $content = implode($history);

        Storage::disk('local')->put('innpulsa/errores_archivo.txt', $content);

        \Log::info("Fin validacion datos");

        if (count($history) > 0) {
            throw new \Exception("Hay errores en el excel");
            // die;
        }
        \Log::info("No hay errrores en la validacion");
        return;
    }

    /**
     * validacion de encabezados del excel
     */
    private function validateIndex($results, $history)
    {   
        foreach ($results as $key => $resul) {
            //validamos index/encabezados del excel
            if (!array_key_exists('correo_electronico', $resul) )
                array_push($history, "- No está definido el encabezado correo_electronico" . "\n");

            if (!array_key_exists('nombre_usuario', $resul) )
                array_push($history, "- No está definido el encabezado nombre_usuario" . "\n");

            if (!array_key_exists('primer_apellido', $resul) )
                array_push($history, "- No está definido el encabezado primer_apellido" . "\n");

            if (!array_key_exists('segundo_apellido', $resul) )
                array_push($history, "- No está definido el encabezado segundo_apellido" . "\n");

            if (!array_key_exists('sexo', $resul) )
                array_push($history, "- No está definido el encabezado sexo" . "\n");

            if (!array_key_exists('ciudad', $resul) )
                array_push($history, "- No está definido el encabezado ciudad" . "\n");

            if (!array_key_exists('fecha_de_nacimiento', $resul) )
                array_push($history, "- No está definido el encabezado fecha_de_nacimiento" . "\n");

            if (!array_key_exists('tipo_identificacion', $resul) )
                array_push($history, "- No está definido el encabezado tipo_identificacion" . "\n");

            if (!array_key_exists('numero_identificacion', $resul) )
                array_push($history, "- No está definido el encabezado numero_identificacion" . "\n");

            if (!array_key_exists('celular', $resul) )
                array_push($history, "- No está definido el encabezado celular" . "\n");

            if (!array_key_exists('telefono', $resul) )
                array_push($history, "- No está definido el encabezado telefono" . "\n");

            if (!array_key_exists('nivel_educacion', $resul) )
                array_push($history, "- No está definido el encabezado nivel_educacion" . "\n");

            if (!array_key_exists('ocupacion', $resul) )
                array_push($history, "- No está definido el encabezado ocupacion" . "\n");

            if (!array_key_exists('profesion', $resul) )
                array_push($history, "- No está definido el encabezado profesion" . "\n");

            if (!array_key_exists('tipo_entidad', $resul) )
                array_push($history, "- No está definido el encabezado tipo_entidad" . "\n");

            if (!array_key_exists('nombre_entidad', $resul) )
                array_push($history, "- No está definido el encabezado nombre_entidad" . "\n");

            if (!array_key_exists('ciudad_entidad', $resul) )
                array_push($history, "- No está definido el encabezado ciudad_entidad" . "\n");

            if (!array_key_exists('industria_entidad', $resul) )
                array_push($history, "- No está definido el encabezado industria_entidad" . "\n");

            if (!array_key_exists('etapa_entidad', $resul) )
                array_push($history, "- No está definido el encabezado etapa_entidad" . "\n");

            if (!array_key_exists('descripcion_entidad', $resul) )
                array_push($history, "- No está definido el encabezado descripcion_entidad" . "\n");

            if (!array_key_exists('facebook_entidad', $resul) )
                array_push($history, "- No está definido el encabezado facebook_entidad" . "\n");

            if (!array_key_exists('twitter_entidad', $resul) )
                array_push($history, "- No está definido el encabezado twitter_entidad" . "\n");

            if (!array_key_exists('instagram_entidad', $resul) )
                array_push($history, "- No está definido el encabezado instagram_entidad" . "\n");

            if (!array_key_exists('linkedin_entidad', $resul) )
                array_push($history, "- No está definido el encabezado linkedin_entidad" . "\n");

            if (!array_key_exists('pagina_web_entidad', $resul) )
                array_push($history, "- No está definido el encabezado pagina_web_entidad" . "\n");

            if (!array_key_exists('tipo_identificacion_empresa', $resul) )
                array_push($history, "- No está definido el encabezado tipo_identificacion_entidad" . "\n");

            if (!array_key_exists('numero_identificacion_empresa', $resul) )
                array_push($history, "- No está definido el encabezado numero_identificacion_empresa" . "\n");

            if (!array_key_exists('telefono_empresa', $resul) )
                array_push($history, "- No está definido el encabezado telefono_empresa" . "\n");

            if (!array_key_exists('whatsapp_empresa', $resul) )
                array_push($history, "- No está definido el encabezado whatsapp_empresa" . "\n");

            if (!array_key_exists('fecha_ini_operacion_empresa', $resul) )
                array_push($history, "- No está definido el encabezado fecha_ini_operacion_empresa" . "\n");

            if (!array_key_exists('acti_economica_ciuu_empresa', $resul) )
                array_push($history, "- No está definido el encabezado acti_economica_ciuu_empresa" . "\n");

            if (!array_key_exists('tipo_sociedad_empresa', $resul) )
                array_push($history, "- No está definido el encabezado tipo_sociedad_empresa" . "\n");

            if (!array_key_exists('cubrimiento_empresa', $resul) )
                array_push($history, "- No está definido el encabezado cubrimiento_empresa" . "\n");

            if (!array_key_exists('correo_empresa', $resul) )
                array_push($history, "- No está definido el encabezado correo_empresa" . "\n");

            break;
        }

        return $history;
    }

    private function isFinal($resul): bool
    {
        //si los campos obligatorias están vacios se entiende que es el final
        //del archivo
        if (
               $resul['correo_electronico'] == null
            && $resul['nombre_usuario'] == null
            && $resul['primer_apellido'] == null
            && $resul['sexo'] == null
            && $resul['ciudad'] == null
            && $resul['fecha_de_nacimiento'] == null
            && $resul['tipo_identificacion'] == null
            && $resul['numero_identificacion'] == null
            && $resul['celular'] == null
            && $resul['telefono'] == null
            && $resul['nivel_educacion'] == null
            && $resul['ocupacion'] == null
            && $resul['profesion'] == null
        ) {
            \Log::info('isFinal');
            return true;
        }

        return false;
    }

    private function getProfesion(int $codigo_profesion_persona): string
    {
        $profesion = ProfesionPersona::where('codigo_profesion_persona', $codigo_profesion_persona)
                                    ->where("codigo_estado", Estado::getState('active'))
                                    ->get();

        return $profesion[0]->nombnre_profesion_persona? : '';
    }

    private function getOcupacion(int $codigo_ocupacion_persona): string
    {
        $ocupacion = OcupacionPersona::where('codigo_ocupacion_persona', $codigo_ocupacion_persona)
                                    ->where("codigo_estado", Estado::getState('active'))
                                    ->get();
        return $ocupacion[0]->nombnre_profesion_persona? : '';
    }
}
