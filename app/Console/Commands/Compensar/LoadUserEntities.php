<?php
/**
 * Rutina de cargue de datos usuario y entidades compensar.
 *
 * @author Ayriel Noriega <ayrieltuproyecto@gmail.com>
 * @author Harold Rodríguez <hartuproyecto@gmail.com>
 */
namespace App\Console\Commands\Compensar;

use DB, Excel, Log, SendEmail, Storage;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use App\Console\Commands\LoadUsers\CargaUsuarios;
use App\Console\Commands\LoadEntities\CargaEntidad;

use App\Models\Core\{Ciudad, Estado,
    UsuarioInstitucion};

use App\Models\Seguridad\{RolUsuario, Usuario};

use App\Models\Param\Institucion;



class LoadUserEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compensar:loadusersentities {path_archivo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios archivo FTP Compensar';

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
        $history = array();
        $path_archivo = $this->argument('path_archivo');

        define("ID_INSTITUTION", Institucion::institution('compensar'));
        define("ID_ROL_INSTITUTION", 53);
        define("ID_ROL_ASSOCIATED", 39);
        define("ID_ROL_USER", 3);

        // Excel::load('users_compensar.xlsx', function($reader) use($history) {
        Excel::load($path_archivo, function($reader) use($history) {

            $results = $reader->get()->toArray();

            $this->validateData($results);

            foreach($results as $resul){

                //Datos de usuario (persona).
                $data_user = [];
                $data_user['tipo_identificacion']   = $this->getOptionIdentification($resul['tipo_identificacion']); // *
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); // *
                $data_user['nombre_usuario']        = trim($resul['primer_nombre']) . ' ' . trim($resul['segundo_nombre']);
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); // *
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);
                $data_user['fecha_nacimiento']      = substr(trim($resul['fecha_de_nacimiento']),0,10); //OBLIGATORIO
                $data_user['telefono']              = trim($resul['telefono']); //NO OBLIGATORIO
                $data_user['movil']                 = trim($resul['celular']); //OBLIGATORIO
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //NO OBLIGATORIO
                $data_user['direccion_usuario']     = strtolower(trim($resul['direccion'])); //OBLIGATORIO
                $data_user['sexo']                  = $this->getGender($resul['genero']); //OBLIGATORIO
                $data_user['nivel_academico']       = $this->getLevelAcademic($resul['nivel_academico']);//NO OBLIGATORIO
                $data_user['id_institucion']        = ID_INSTITUTION;
                $data_user['id_rol_institucion']    = ID_ROL_INSTITUTION;
                $data_user['id_rol_associated']     = ID_ROL_ASSOCIATED;

                //Permite identificar los datos requeridos.
                $data_require = [
                    "tipo_identificacion"   => true,
                    "numero_identificacion" => true,
                    "nombre_usuario"        => true,
                    "primer_apellido"       => true,
                    "segundo_apellido"      => true,
                    "fecha_nacimiento"      => true,
                    "telefono"              => true,
                    "movil"                 => true,
                    "correo_electronico"    => true,
                    "direccion_usuario"     => true,
                    "sexo"                  => true,
                    "nivel_academico"       => true,
                    "usuario_institucion"   => true,
                ];

                $codigo_usuario = "";

                $user = $this->getEmailUsuario($data_user['correo_electronico']);

                $usuarioIdentificacion = $this->getIdentificationUser($data_user['numero_identificacion']);

                if((!$user instanceof Usuario) && (!$usuarioIdentificacion instanceof Usuario))
                {

                    $user = CargaUsuarios::store($data_user, $data_require);
                    $codigo_usuario = $user->codigo_usuario;
                    $password = $user->password;

                    // Correo notificación.
                    $data = [];
                    $data['_inst'] = ID_INSTITUTION;
                    $data['_to'] = $data_user['correo_electronico'];
                    $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                    $data['_subject'] = "Confirmación de registro plataforma";
                    $data['_template'] = 'emails.user.restorepassword';
                    $data['_change_password']  = "/password/email";
                    //$data['_current_password']  = $password;
                    SendEmail::notification($data);

                    array_push($history, "Creo el usuario: ".$data_user['correo_electronico']."\n");
                    Log::info("Creo el usuario: ".$data_user['correo_electronico']."\n");
                }
                else{
                    $codigo_usuario = $user->codigo_usuario;

                    if($data_user['telefono'] != "")
                        $user->telefono_usuario = $data_user['telefono'];
                    if($data_user['tipo_identificacion'] != "")
                            $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];
                    if($data_user['movil'] != "")
                        $user->movil_usuario = $data_user['movil'];
                    if($data_user['numero_identificacion'] != "")
                            $user->identificacion_usuario = $data_user['numero_identificacion'];
                    if($data_user['direccion_usuario'] != "")
                        $user->direccion_usuario = $data_user['direccion_usuario'];

                    $user->save();

                    $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, ID_INSTITUTION);
                    $usuarioIdentificacion = $this->getIdentificationUser($data_user['numero_identificacion']);

                    if(!$usuario_institucion instanceof UsuarioInstitucion)
                    {
                        if (!$usuarioIdentificacion instanceof Usuario)
                        {
                            $nusuario_institucion = new UsuarioInstitucion;
                            $nusuario_institucion->codigo_usuario = $codigo_usuario;
                            $nusuario_institucion->codigo_institucion = ID_INSTITUTION;
                            $nusuario_institucion->codigo_estado = 8;
                            $nusuario_institucion->codigo_rol = 3;
                            $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                            $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                            $nusuario_institucion->save();

                            $userRol = RolUsuario::where('codigo_institucion', ID_INSTITUTION)
                                                ->where('role_id', ID_ROL_INSTITUTION)
                                                ->where('user_id', $codigo_usuario)
                                                ->first();

                            if(!$userRol instanceof RolUsuario)
                            {
                                $userRol = new RolUsuario;
                                $userRol->role_id = ID_ROL_INSTITUTION;
                                $userRol->user_id = $codigo_usuario;
                                $userRol->codigo_institucion = ID_INSTITUTION;
                                $userRol->save();
                            }

                            $userRolAsociado = RolUsuario::where('codigo_institucion', ID_INSTITUTION)
                                                    ->where('role_id', ID_ROL_ASSOCIATED)
                                                    ->where('user_id', $codigo_usuario)
                                                    ->first();

                            if(!$userRolAsociado instanceof RolUsuario)
                            {
                                $userRolAsociado = new RolUsuario;
                                $userRolAsociado->role_id = ID_ROL_ASSOCIATED;
                                $userRolAsociado->user_id = $codigo_usuario;
                                $userRolAsociado->codigo_institucion = ID_INSTITUTION;
                                $userRolAsociado->save();
                            }

                             // Correo notificación.
                            $data = [];
                            $data['_inst'] = ID_INSTITUTION;
                            $data['_to'] = $data_user['correo_electronico'];
                            $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                            $data['_subject'] = "Confirmación de registro plataforma";
                            $data['_template'] = 'emails.user.restorepassword';
                            $data['_change_password']  = "/password/email";
                            SendEmail::notification($data);

                            array_push($history, "Se crea el usuario en la institucion: ".$data_user['correo_electronico']."\n");
                            Log::info("Se crea el usuario en la institucion: ".$data_user['correo_electronico']."\n");

                        } else {
                            array_push($history, "Usuario con codigo de identificacion : ".$data_user['numero_identificacion']." ya se encuentra registrado en la institucion, se actualiza. \n");
                            Log::info("Usuario con codigo de identificacion : ".$data_user['numero_identificacion']." ya se encuentra registrado  en la institucion\n");
                        }
                    }
                    else{
                        array_push($history, "Usuario ya existe, se actualiza datos de: ".$data_user['correo_electronico']."\n");
                        Log::info("Usuario ya existe, se actualiza: ".$data_user['correo_electronico']."\n");
                    }
                }

                //Aceptar terminos y condiciones.
                CargaUsuarios::acceptTerms($user, ID_INSTITUTION);

                //Datos de la entidad.
                $data_entity = [];
                $data_entity['codigo_clase_empre_insti']   = $this->getEntityType($resul['tipo_entidad']); // * (codigo_clase_empre_insti)
                $data_entity['codigo_tipo_identificacion'] = $this->getIdentificationType($resul['tipo_identificacion_empresa']); // * (codigo_tipo_identificacion)
                $data_entity['identificacion_usuario']     = trim($resul['numero_identificacion_empresa']); // * (identificacion_usuario)
                $data_entity['nombre_usuario']             = trim($resul['nombre_entidad']); // * (nombre_usuario)
                $data_entity['codigo_ciudad']              = $this->getCity($resul['ciudad_entidad']); // * (codigo_ciudad)
                $data_entity['fecha_nacimiento_usuario']   = substr(trim($resul['fecha_inicio_operacion_empresa']),0,10); // * (fecha_nacimiento)
                $data_entity['codigo_categoria_proyecto']  = $this->getCategoryProject($resul['industria_entidad']); // * (codigo_categoria_proyecto)
                $data_entity['codigo_etapa_proyecto']      = $this->getStage(trim($resul['etapa_entidad']), 2); // * (codigo_etapa_proyecto)
                $codigo_sector_interes                     = $this->getIdOption($resul['actividad_economica_ciuu_empresa'], 2); // * (codigo_sector_interes)
                $data_entity['codigo_sector_interes']      = $codigo_sector_interes;
                $data_entity['codigo_tipo_sociedad']       = $this->getSocietyType($resul['tipo_sociedad_empresa']); // * (codigo_tipo_sociedad)
                $data_entity['cubrimiento_entidad']        = $this->getCoverageType($resul['cubrimiento_empresa']); // * (cubrimiento_entidad)
                $data_entity['correo_contacto']            = trim($resul['correo_contacto_empresa']); // (correo_contacto)
                $data_entity['biografia_usuario']          = trim($resul['descripcion_entidad']); // * (biografia_usuario)
                $data_entity['telefono_usuario']           = trim($resul['telefono_empresa']); // (telefono_usuario)
                $data_entity['numero_whatsapp']            = trim($resul['numero_whatsapp_empresa']); // (numero_whatsapp)
                $data_entity['pagina_web_usuario']         = trim($resul['pagina_web_empresa']);
                $data_entity['facebook_usuario']           = trim($resul['facebook_entidad']);
                $data_entity['twitter_usuario']            = trim($resul['twitter_entidad']);
                $data_entity['linkedin_usuario']           = trim($resul['instagram_entidad']);
                $data_entity['instagram_usuario']          = trim($resul['linkedin_entidad']);

                $codigo_usuario_entidad = '';

                //Crea entidad si tiene codigo_usuario
                if (isset($codigo_usuario) && !empty($codigo_usuario)) {

                    $response = CargaEntidad::store($codigo_usuario, $data_entity, ID_INSTITUTION);

                    Log::info($response[1]);

                    array_push($history, $response[1] ."\n");

                    if (!$response[0]) {
                        DB::rollback();
                        continue;
                    }
                }
            }

            array_push($history, "Finalizo");

            // Correo notificación administradores.
            $data = [];
            $data['_inst'] = ID_INSTITUTION;
            $data['_to'] = 'mcarriong@compensar.com';
            $data['to_copy'] = ['administracion@tuproyecto.com', 'testertuproyecto@gmail.com'];
            $data['_to_name'] = 'Fomento Empresarial';
            $data['_subject'] = "Carga de datos";
            $data['_template'] = 'emails.user.notificationdata';
            $data['_history'] = $history;
            SendEmail::notification($data);
        });

        Log::info("Finalizo");
    }
    /**
     * Consulta el usuario (persona) mediante el correo electronico.
     *
     * @access public
     * @param string $email
     * @return Usuario
     */
    public static function getEmailUsuario($email)
    {
        return Usuario::where('correo_electronico', '=',  trim($email)) -> first();
    }
    /**
     * Consulta el usuario mediante número de identificación.
     *
     * @access public
     * @param int $identification
     * @return Usuario $data
     */
    public static function getIdentificationUser($identification)
    {
        $query = Usuario::query();
        $query->select('*');
        $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->where('usuario_institucion.codigo_institucion', '=', '30');
        $query->where('usuario.identificacion_usuario', '=', trim($identification));
        $data = $query->first();
        return $data;
    }
    /**
     * Consulta relación entre usuario e institución.
     *
     * @access public
     * @param int $codigo_usuario, $codigo_institucion
     * @return UsuarioInstitucion
     */
    public static function getusuarioInstitucion($codigo_usuario, $codigo_institucion)
    {
		return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
		->where('codigo_institucion', '=', $codigo_institucion)
		->first();
    }
    /**
     * Obtiene tipo de identificación
     *
     * @access private
     * @param string $tipo_identificacion
     * @return null|int
     */
    private function getOptionIdentification($tipo_identificacion)
    {
        if (is_null($tipo_identificacion)) return null ;

        $tipo_identificacion = trim($tipo_identificacion);
        $codigo_tipo_identificacion = [
            'Cédula de Ciudadanía' => 1,
            'Permiso especial de permanencia' => 9,
            'Cédula de extranjería' => 2,
            'Cédula de otro país' => 11,
            'Pasaporte' => 5,
            'Tarjeta de Identidad' => 4,
            'Documento nacional de identificación' => 12,
            'Nit' => 3,
            'Registro Civil' => 6,
        ];

        if (array_key_exists($tipo_identificacion, $codigo_tipo_identificacion)) {
            return $codigo_tipo_identificacion[$tipo_identificacion];
        }

        return null;
    }
    /**
     * Obtiene el género del usuario (persona).
     *
     * @access private
     * @param string $genero
     * @return null|int
     */
    private function getGender($genero)
    {
        $genero = trim($genero);

        $codigo_genero = [
            'M' => 1,
            'F' => 2,
        ];

        if (array_key_exists($genero, $codigo_genero)) {
            return $codigo_genero[$genero];
        }

        return null;
    }
    /**
     * Obtiene el nivel académico del usuario (persona).
     *
     * @access private
     * @param string $nivel_academico
     * @return null|int
     */
    private function getLevelAcademic($nivel_academico)
    {
        $nivel_academico = mb_strtolower(trim($nivel_academico));

        $codigo_nivel_academico = [
            'bachiller' => 8,
            'básica secundaria' => 14,
            'doctorado' => 5,
            'especialización' => 4,
            'maestría' => 11,
            'primaria' => 13,
            'profesional' => 3,
            'sin título' => 12,
            'técnico laboral' => 1,
            'técnico profesional' => 15,
            'tecnológica' => 9,
        ];

        if (array_key_exists($nivel_academico, $codigo_nivel_academico)) {
            return $codigo_nivel_academico[$nivel_academico];
        }

        return null;

    }
    /**
     * Obtiene el tipo de entidad.
     *
     * @access private
     * @param string $tipo_entidad
     * @return null|int
     */
    private function getEntityType($tipo_entidad)
    {
        $tipo_entidad = mb_strtolower(trim($tipo_entidad));

        $codigo_clase_empre_insti = [
            'empresa' => 37,
            'proyecto o idea' => 36
        ];

        if (array_key_exists($tipo_entidad, $codigo_clase_empre_insti)) {
            return $codigo_clase_empre_insti[$tipo_entidad];
        }

        return null;
    }
    /**
     * Obtiene el tipo de identificación de la entidad.
     *
     * @access private
     * @param string $tipo_identificacion_empresa
     * @return null|int
     */
    private function getIdentificationType($tipo_identificacion_empresa)
    {
        $tipo_identificacion_empresa = mb_strtolower(trim($tipo_identificacion_empresa));

        $codigo_tipo_identificacion = [
            'nit' => 3
        ];

        if (array_key_exists($tipo_identificacion_empresa, $codigo_tipo_identificacion)) {
            return $codigo_tipo_identificacion[$tipo_identificacion_empresa];
        }

        return null;
    }
    /**
     * Obtiene el tipo de sociedad de la entidad.
     *
     * @access private
     * @param string $tipo_identificacion_empresa
     * @return null|int
     */
    private function getSocietyType($tipo_sociedad_empresa)
    {
        $tipo_sociedad_empresa = mb_strtolower(trim($tipo_sociedad_empresa));

        $codigo_tipo_sociedad = [
            'sociedad por acciones simplificadas(s.a.s)' => 1,
            'sociedad anónima(s.a)' => 2,
            'ltda' => 3,
            'persona natural' => 4,
            'persona jurídica' => 5,
            'cooperativa' => 6,
            'cooperativa de trabajo asociado(c.t.a)' => 7,
            'fundación' => 8,
            'empresa unipersonal(e.u)' => 9,
            'persona natural con establecimiento de comercio' => 10,
            'sociedad anónima simplificada' => 11,
            'régimen simplificado' => 12,
            'otra' => 13,
            'sociedad comercial' => 15,
            'entidad sin ánimo de lucro' => 16,
            'persona natural con establecimiento comercial' => 17,
            'economía solidaria' => 18
        ];

        if (array_key_exists($tipo_sociedad_empresa, $codigo_tipo_sociedad)) {
            return $codigo_tipo_sociedad[$tipo_sociedad_empresa];
        }

        return null;
    }
    /**
     * Obtiene el tipo de cubrimiento de la entidad.
     *
     * @access private
     * @param string $cubrimiento_empresa
     * @return null|int
     */
    private function getCoverageType($cubrimiento_empresa)
    {
        $cubrimiento_empresa = mb_strtolower(trim($cubrimiento_empresa));

        $cubrimiento_entidad = [
            'local' => 1,
            'regional' => 2,
            'nacional' => 3,
            'internacional' => 4
        ];

        if (array_key_exists($cubrimiento_empresa, $cubrimiento_entidad)) {
            return $cubrimiento_entidad[$cubrimiento_empresa];
        }

        return null;
    }
        /**
     * Obtiene la categoria de la entidad.
     *
     * @access private
     * @param string $industria_entidad
     * @return null|int
     */
    private function getCategoryProject($industria_entidad)
    {
        $industria_entidad = mb_strtolower(trim($industria_entidad));

        $codigo_categoria_proyecto = [
            'agricultura, ganadería, caza, silvicultura y pesca' => 317,
            'minero y energético' => 318,
            'energía eléctrica y bienes conexos' => 319,
            'transporte, almacenamiento y logística' => 320,
            'alojamiento y servicios de comida' => 333,
            'tics (tecnología de la información y comunicaciones)' => 334,
            'actividades financieras y de seguros' => 335,
            'actividades inmobiliarias' => 336,
            'actividades de servicios administrativos y de apoyo' => 337,
            'educacion' => 338,
            'salud' => 339,
            'agroindustria' => 340,
            'elaboración de productos alimenticios y bebidas' => 341,
            'textil y confecciones' => 342,
            'diseño y moda' => 343,
            'cuero, calzado y marroquinería' => 344,
            'industria editorial y comunicación gráfica' => 345,
            'químicos, farmaceuticos' => 346,
            'caucho y plástico' => 348,
            'metalmecánica, siderurgica' => 349,
            'automotriz-autopartes' => 350,
            'electrónica' => 351,
            'actividades de saneamiento ambiental' => 352,
            'construcción' => 353,
            'comercio al por menor' => 354,
            'comercio al por mayor' => 355,
            'correo y servicios de mensajería' => 356,
            'biotecnología' => 357,
            'turismo' => 358,
            'servicios de consultoría' => 359,
            'actividades de recreación y deporte' => 360,
            'productos y servicios para consumo animal y mascotas' => 361,
            'entretenimiento' => 362,
            'productos ecologicos' => 363,
            'aeronáutico' => 364,
            'servicios de lavandería' => 365,
            'actividades culturales y creativas' => 366,
            'servicios funerarios' => 367,
            'cosmética y belleza' => 347,
            'joyería y accesoríos' => 396,
            'fotografía' => 397,
            'jardinería' => 419,
            'seguridad electrónica' => 743
        ];

        if (array_key_exists($industria_entidad, $codigo_categoria_proyecto)) {
            return $codigo_categoria_proyecto[$industria_entidad];
        }

        return null;
    }
    /**
     * Obtiene la etapa de la entidad.
     *
     * @access private
     * @param string $etapa_entidad
     * @return null|int
     */
    private function getStage($etapa_entidad)
    {
        $etapa_entidad = mb_strtolower(trim($etapa_entidad));

        $codigo_etapa_proyecto = [
            'idea' => 70,
            'en marcha' => 71
        ];

        if (array_key_exists($etapa_entidad, $codigo_etapa_proyecto)) {
            return $codigo_etapa_proyecto[$etapa_entidad];
        }

        return null;
    }
    /**
     * Obtiene la ciudad de la entidad.
     *
     * @access private
     * @param string $cubrimiento_empresa
     * @return null|int
     */
    private function getCity($ciudad_entidad)
    {
        $query = Ciudad::query();
        $query->select('codigo_ciudad');
		$query->whereRaw("nombre_ciudad ilike '%$ciudad_entidad%'");
        $query->where('codigo_estado', Estado::getState('active'));
        $city = $query->first();
		return (int) $city->codigo_ciudad;
    }
    /**
     * Obtiene id principal de opción de respuesta.
     *
     * @access private
     * @param string $str_id
     * @param int $count
     * @return int|null
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
     * Valida el formato de la fecha ingresada en .xslx .
     *
     * @access private
     * @param string $date
     * @param string $format
     * @return bool
     */
    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    /**
     * Validación de datos contenidos en .xslx
     *
     * @Annotation valida datos y encabezados.
     * @access public
     * @param array $results
     * @return void
     */
    public function validateData($results)
    {
        Log::info('Validando datos');

        $history = array();
        $history = $this->validateIndex($results, $history);

        //Valida que no se hallan encontrado errores en la validación de encabezados.
        if (!count($history) > 0) {
            foreach($results as $key => $resul){

                //Datos del usuario (persona).
                $data_user = [];
                $data_user['tipo_identificacion']   = $this->getOptionIdentification($resul['tipo_identificacion']);
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //*
                $data_user['primer_nombre']         = trim($resul['primer_nombre']); // *
                $data_user['segundo_nombre']        = trim($resul['segundo_nombre']);
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); // *
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);//
                $data_user['fecha_nacimiento']      = substr(trim($resul['fecha_de_nacimiento']),0,10); // *
                $data_user['telefono']              = trim($resul['telefono']);
                $data_user['celular']               = trim($resul['celular']); // *
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); // *
                $data_user['sexo']                  = $this->getGender($resul['genero']); // *
                $data_user['nivel_academico']       = $this->getLevelAcademic($resul['nivel_academico']);

                if (is_null($data_user['tipo_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene tipo_identificacion vacío o el dato es incorrecto" . "\n");
                }

                if (empty($data_user['numero_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion vacío" . "\n");
                } elseif ( !is_numeric($data_user['numero_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion con dato incorrecto, debe ser numerico" . "\n");
                }

                if (empty($data_user['primer_nombre'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene primer_nombre vacío" . "\n");
                }

                if (empty($data_user['primer_apellido'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene primer_apellido vacío" . "\n");
                }

                if (!$this->validateDate($data_user['fecha_nacimiento'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene fecha_nacimiento incorrecta" . "\n");
                }

                if (empty($data_user['celular'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene celular vacío" . "\n");
                }

                if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                    array_push($history, "- La fila " . ($key + 2). " tiene correo vacío o el dato es incorecto" . "\n");
                }

                if (is_null($data_user['sexo'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene genero vacío o el dato es incorrecto" . "\n");
                }

                //Datos del usuario (entidad).
                $data_entity = [];
                $data_entity['tipo_entidad']                         = $this->getEntityType($resul['tipo_entidad']); // * (codigo_clase_empre_insti)
                $data_entity['tipo_identificacion_empresa']          = $this->getIdentificationType($resul['tipo_identificacion_empresa']); // * (codigo_tipo_identificacion)
                $data_entity['numero_identificacion_empresa']        = trim($resul['numero_identificacion_empresa']); // *
                $data_entity['nombre_entidad']                       = trim($resul['nombre_entidad']); // * (nombre_usuario)
                $data_entity['telefono_empresa']                     = trim($resul['telefono_empresa']); // (telefono_usuario)
                $data_entity['numero_whatsapp_empresa']              = trim($resul['numero_whatsapp_empresa']); // (numero_whatsapp)
                $data_entity['fecha_inicio_operacion_empresa']       = substr(trim($resul['fecha_inicio_operacion_empresa']),0,10); // * (fecha_nacimiento)
                $codigo_sector_interes                               = $this->getIdOption($resul['actividad_economica_ciuu_empresa'], 2); // * (codigo_sector_interes)
                $data_entity['actividad_economica_ciuu_empresa']     = $codigo_sector_interes;
                $data_entity['tipo_sociedad_empresa']                = $this->getSocietyType($resul['tipo_sociedad_empresa']); // * (codigo_tipo_sociedad)
                $data_entity['cubrimiento_empresa']                  = $this->getCoverageType($resul['cubrimiento_empresa']); // * (cubrimiento_entidad)
                $data_entity['correo_contacto_empresa']              = trim($resul['correo_contacto_empresa']); // (correo_contacto)
                $data_entity['ciudad_entidad']                       = $this->getCity($resul['ciudad_entidad']); // * (codigo_ciudad)
                $data_entity['industria_entidad']                    = $this->getCategoryProject($resul['industria_entidad']); // * (codigo_categoria_proyecto) y proyecto
                $data_entity['etapa_entidad']                        = $this->getStage(trim($resul['etapa_entidad']), 2); // * (codigo_etapa_proyecto)
                $data_entity['descripcion_entidad']                  = trim($resul['descripcion_entidad']); // * (biografia_usuario)

                if (is_null($data_entity['tipo_entidad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene tipo_entidad vacío o el dato es incorrecto" . "\n");
                } else {
                    if ($data_entity['tipo_entidad'] == 37) { // Empresa => codigo_clase_empre_insti = 37

                        if (is_null($data_entity['tipo_identificacion_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene tipo_identificacion_empresa vacío o el dato es incorrecto" . "\n");
                        }

                        if (empty($data_entity['numero_identificacion_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion_empresa vacío" . "\n");
                        } elseif ( !is_numeric(($data_entity['numero_identificacion_empresa']))) {
                            array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion_empresa con tipo de dato incorrecto, debe ser numerico" . "\n");
                        }

                        if (!empty($data_entity['telefono_empresa'] && !is_numeric($data_entity['telefono_empresa']))) {
                            array_push($history, "- La fila " . ($key + 2). " tiene telefono_entidad con tipo de dato incorrecto, debe ser numerico" . "\n");
                        }

                        if (!empty($data_entity['numero_whatsapp_empresa'] && !is_numeric($data_entity['numero_whatsapp_empresa']))) {
                            array_push($history, "- La fila " . ($key + 2). " tiene numero_whatsapp_empresa con tipo de dato incorrecto, debe ser numerico" . "\n");
                        }

                        if (!$this->validateDate($data_entity['fecha_inicio_operacion_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene fecha_inicio_operacion_empresa con tipo de dato incorrecta" . "\n");
                        }

                        if (is_null($data_entity['actividad_economica_ciuu_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene actividad_economica_ciuu_empresa vacío o el dato es incorrecto" . "\n");
                        }

                        if (is_null($data_entity['tipo_sociedad_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene tipo_sociedad_empresa vacío o el dato es incorrecto" . "\n");
                        }

                        if (is_null($data_entity['cubrimiento_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene cubrimiento_empresa vacío o el dato es incorrecto" . "\n");
                        }

                        if (!empty($data_entity['correo_contacto_empresa']) && !filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                            array_push($history, "- La fila " . ($key + 2). " tiene correo_contacto_empresa con tipo de dato incorrecto" . "\n");
                        }

                        if (is_null($data_entity['industria_entidad'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene industria_entidad vacío o el dato es incorrecto" . "\n");
                        }

                    }
                }

                if (empty($data_entity['nombre_entidad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene nombre_entidad vacío" . "\n");
                }

                if (is_null($data_entity['ciudad_entidad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene ciudad_entidad vacío o el dato es incorrecto (verifique tíldes en caso de que aplique) " . "\n");
                }

                if (is_null($data_entity['etapa_entidad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene etapa_entidad vacío o el dato es incorrecto" . "\n");
                }

                if (empty($data_entity['descripcion_entidad'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene descripcion_entidad vacío" . "\n");
                }
            }
        }

        $content = implode($history);

        Storage::disk('local')->put('compensar/errores_archivo_entidades.txt', $content);

        Log::info("Fin validacion datos");

        if (count($history) > 0) {
            throw new \Exception("Hay errores en el archivo .xslx . Valida la información a cargar.");
        }
    }
    /**
     * Valida que se mantengan los encabezados requeridos en el .xslx.
     *
     * @Annotation el arreglo $history almacenrá los errores encontrados.
     * @access private
     * @param array $results, $history
     * @return array|void $history
     */
    private function validateIndex($results, $history) 
    {
        foreach ($results as $key => $resul) {

            if (!array_key_exists('tipo_identificacion', $resul) )
                array_push($history, "- No está definido el encabezado tipo_identificacion" . "\n");

            if (!array_key_exists('numero_identificacion', $resul) )
                array_push($history, "- No está definido el encabezado numero_identificacion" . "\n");

            if (!array_key_exists('primer_nombre', $resul) )
                array_push($history, "- No está definido el encabezado primer_nombre" . "\n");

            if (!array_key_exists('segundo_nombre', $resul) )
                array_push($history, "- No está definido el encabezado segundo_nombre" . "\n");

            if (!array_key_exists('primer_apellido', $resul) )
                array_push($history, "- No está definido el encabezado primer_apellido" . "\n");

            if (!array_key_exists('segundo_apellido', $resul) )
                array_push($history, "- No está definido el encabezado segundo_apellido" . "\n");

            if (!array_key_exists('fecha_de_nacimiento', $resul) )
                array_push($history, "- No está definido el encabezado fecha_de_nacimiento" . "\n");

            if (!array_key_exists('telefono', $resul) )
                array_push($history, "- No está definido el encabezado telefono" . "\n");

            if (!array_key_exists('celular', $resul) )
                array_push($history, "- No está definido el encabezado celular" . "\n");

            if (!array_key_exists('correo_electronico', $resul) )
                array_push($history, "- No está definido el encabezado correo_electronico" . "\n");

            if (!array_key_exists('direccion', $resul) )
                array_push($history, "- No está definido el encabezado direccion" . "\n");

            if (!array_key_exists('genero', $resul) )
                array_push($history, "- No está definido el encabezado genero" . "\n");

            if (!array_key_exists('nivel_academico', $resul) )
                array_push($history, "- No está definido el encabezado nivel_academico" . "\n");

            if (!array_key_exists('tipo_entidad', $resul) )
                array_push($history, "- No está definido el encabezado tipo_entidad" . "\n");

            if (!array_key_exists('tipo_identificacion_empresa', $resul) )
                array_push($history, "- No está definido el encabezado tipo_identificacion_empresa" . "\n");

            if (!array_key_exists('numero_identificacion_empresa', $resul) )
                array_push($history, "- No está definido el encabezado numero_identificacion_empresa" . "\n");

            if (!array_key_exists('nombre_entidad', $resul) )
                array_push($history, "- No está definido el encabezado nombre_entidad" . "\n");

            if (!array_key_exists('telefono_empresa', $resul) )
                array_push($history, "- No está definido el encabezado telefono_empresa" . "\n");

            if (!array_key_exists('numero_whatsapp_empresa', $resul) )
                array_push($history, "- No está definido el encabezado numero_whatsapp_empresa" . "\n");

            if (!array_key_exists('fecha_inicio_operacion_empresa', $resul) )
                array_push($history, "- No está definido el encabezado fecha_inicio_operacion_empresa" . "\n");

            if (!array_key_exists('actividad_economica_ciuu_empresa', $resul) )
                array_push($history, "- No está definido el encabezado actividad_economica_ciuu_empresa" . "\n");

            if (!array_key_exists('tipo_sociedad_empresa', $resul) )
                array_push($history, "- No está definido el encabezado tipo_sociedad_empresa" . "\n");

            if (!array_key_exists('cubrimiento_empresa', $resul) )
                array_push($history, "- No está definido el encabezado cubrimiento_empresa" . "\n");

            if (!array_key_exists('correo_contacto_empresa', $resul) )
                array_push($history, "- No está definido el encabezado correo_contacto_empresa" . "\n");

            if (!array_key_exists('pagina_web_empresa', $resul) )
                array_push($history, "- No está definido el encabezado pagina_web_empresa" . "\n");

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
            
            break;
        }

        return $history;
    }
}