<?php

namespace App\Console\Commands\Propace;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
App\Models\Seguridad\RolUsuario,
App\Models\Core\UsuarioInstitucion,
App\Models\Core\Persona,
App\Models\Param\Institucion,
App\Models\Core\Estado,
App\Models\Core\Pago,
App\Console\Commands\LoadUsers\CargaUsuarios;

class LoadUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'propace:loadusers {path_archivo?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios archivo propace';

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
        define("ID_INSTITUTION", Institucion::institution('propace'));
        define("ID_ROL_INSTITUTION", 17);
        define("ID_ROL_ASSOCIATED", 39);


        $history = array();
        $path = $this->argument('path_archivo');
        $path_archivo = isset($path) ? $path : './cargue_usuarios_propace.xlsx';

        Excel::load($path_archivo, function($reader) use($history) {
            $results = $reader->get()->toArray();

            $this->validateData($results);

            foreach($results as $resul){
                //si los campos obligatorias están vacios se entiende que es el final
                //del archivo
                if ($this->isFinal($resul)) break;


                // $cursos = trim($resul['curso']);
                // $array_cursos = explode(",", $cursos);

                //user data
                $data_user = [];
                $data_user['tipo_identificacion']   = $this->getOptionIdentification($resul['tipo_identificacion']);
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGATORIO
                // $data_user['primer_nombre']      = trim($resul['primer_nombre']); //OBLIGATORIO
                // $data_user['segundo_nombre']     = trim($resul['segundo_nombre']); // NO OBLIGATORIO
                $data_user['nombre_usuario']        = trim($resul['primer_nombre']) . ' ' . trim($resul['segundo_nombre']);
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); //OBLIGATORIO
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);//NO SOBLIGATORIO
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

                //Allows you to identify the fields to be loaded by institution
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
                    "direccion_usuario" => true,
                    "sexo" => true,
                    "nivel_academico" => true,
                    "usuario_institucion" => false,
                ];

                $codigo_usuario = "";
                $user = $this->getUser($data_user['correo_electronico'], $data_user['numero_identificacion']);
                // $usuarioIdentificacion = $this->getIdentificationUser($data_user['numero_identificacion']);
                if(!$user instanceof Usuario){

                    $user = CargaUsuarios::store($data_user, $data_require);
                    // $codigo_usuario = $user->codigo_usuario;
                    // $password = $user->password;

                    // for($i = 1; $i < count($array_cursos); $i++){
                    //     if($array_cursos[$i] != ""){
                    //         $pago = new Pago;
                    //         $pago->codigo_estado = 8;
                    //         $pago->codigo_institucion = 75;
                    //         $pago->codigo_usuario_institucion = $codigo_usuario_institucion;
                    //         $pago->fecha_pago = date('Y-m-d');
                    //         $pago->tipo_recurso = 1;
                    //         $pago->codigo_recurso = trim($array_cursos[$i]);
                    //         $pago->save();

                    //         \Log::info("El curso ".trim($array_cursos[$i])." \n");
                    //     }
                    // }

                    // Send notification
                    $data = [];
                    $data['_inst'] = ID_INSTITUTION;
                    $data['_to'] = $data_user['correo_electronico'];
                    $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                    $data['_subject'] = "Confirmación de registro plataforma";
                    $data['_template'] = 'emails.user.restorepassword';
                    $data['_change_password']  = "/password/email";

                    SendEmail::notification($data);

                    array_push($history, "Creo el usuario: ".$data_user['correo_electronico']."\n");
                    \Log::info("Creo el usuario: ".$data_user['correo_electronico']."\n");
                } else {
                    $codigo_usuario = $user->codigo_usuario;

                    if($data_user['telefono'] !="")
                        $user->telefono_usuario = $data_user['telefono'];
                    if($data_user['tipo_identificacion'] !="")
                        $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];
                    if($data_user['movil'] !="")
                        $user->movil_usuario = $data_user['movil'];
                    if($data_user['numero_identificacion'] !="")
                        $user->identificacion_usuario = $data_user['numero_identificacion'];
                    if($data_user['direccion_usuario'] !="")
                        $user->direccion_usuario = $data_user['direccion_usuario'];

                    $user->save();

                    $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, ID_INSTITUTION);
                    $usuarioIdentificacion = $this->getIdentificationUser($data_user['numero_identificacion']);
                    if (!$usuario_institucion instanceof UsuarioInstitucion) {
                        if (!$usuarioIdentificacion instanceof Usuario) {
                            $nusuario_institucion = new UsuarioInstitucion;
                            $nusuario_institucion->codigo_usuario = $codigo_usuario;
                            $nusuario_institucion->codigo_institucion = ID_INSTITUTION;
                            $nusuario_institucion->codigo_estado = 8;
                            $nusuario_institucion->codigo_rol = 3;
                            $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                            $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                            $nusuario_institucion->save();
                            $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                            $userRol = RolUsuario::where('codigo_institucion', 75)
                                                    ->where('role_id', 17)
                                                    ->where('user_id', $codigo_usuario)
                                                    ->first();
                            if(!$userRol instanceof RolUsuario) {
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
                            if(!$userRolAsociado instanceof RolUsuario) {
                                $userRolAsociado = new RolUsuario;
                                $userRolAsociado->role_id = ID_ROL_ASSOCIATED;
                                $userRolAsociado->user_id = $codigo_usuario;
                                $userRolAsociado->codigo_institucion = ID_INSTITUTION;
                                $userRolAsociado->save();
                            }

                            // for($i = 1; $i < count($array_cursos); $i++){
                            //     if($array_cursos[$i] != ""){
                            //         $pago = new Pago;
                            //         $pago->codigo_estado = 8;
                            //         $pago->codigo_institucion = 75;
                            //         $pago->codigo_usuario_institucion = $codigo_usuario_institucion;
                            //         $pago->fecha_pago = date('Y-m-d');
                            //         $pago->tipo_recurso = 1;
                            //         $pago->codigo_recurso = trim($array_cursos[$i]);
                            //         $pago->save();

                            //         \Log::info("El curso ".trim($array_cursos[$i])." \n");
                            //     }                        
                            // }

                            // Send notification
                            $data = [];
                            $data['_inst'] = ID_INSTITUTION;
                            $data['_to'] = $data_user['correo_electronico'];
                            $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                            $data['_subject'] = "Confirmación de registro plataforma";
                            $data['_template'] = 'emails.user.restorepassword';
                            $data['_change_password']  = "/password/email";

                            SendEmail::notification($data);
                            array_push($history, "Creo el usuario: ".$data_user['correo_electronico']."\n");
                            \Log::info("Creo el usuario: ".$data_user['correo_electronico']."\n");
                        } else {
                            array_push($history, "Usuario con codigo de identificacion : ".$data_user['numero_identificacion']." ya se encuentra registrado \n");
                            \Log::info("Usuario con codigo de identificacion : ".$data_user['numero_identificacion']." ya se encuentra registrado \n");
                            continue;
                        }
                    } else {
                        $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
                        array_push($history, "Usuario ya existe, se actualiza: ".$data_user['correo_electronico']."\n");
                        \Log::info("Usuario ya existe, se actualiza: ".$data_user['correo_electronico']."\n");
                        continue;
                    }
                }

                //Aceptar terminos y condiciones
                // CargaUsuarios::acceptTerms($user, ID_INSTITUTION);
            }
            array_push($history, "Finalizo");

            // Send notification
            $data = [];
            $data['_inst'] = ID_INSTITUTION;
            $data['_to'] = 'administracion@propace.org';
            $data['to_copy'] = 'administracion@tuproyecto.com';
            $data['_to_name'] = 'Plataforma propace';
            $data['_subject'] = "Carga de datos";
            $data['_template'] = 'emails.user.notificationdata';
            $data['_history'] = $history;

            SendEmail::notification($data);
        });
        \Log::info("Finalizo");
    }

    public static function getUser($email, $numero_identificacion) {
        return Usuario::where('correo_electronico', '=',  $email)
                    ->orWhere('identificacion_usuario', $numero_identificacion)
                    ->first();
    }

    public static function getIdentificationUser($identification) {
        $query = Usuario::query();
        $query->select('*');
        $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->where('usuario_institucion.codigo_institucion', '=', '75');
        $query->where('usuario.identificacion_usuario', '=', trim($identification));
        $data = $query->first();
        return $data;
    }

    public static function getusuarioInstitucion($codigo_usuario,$codigo_institucion){
		return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
		->where('codigo_institucion', '=', $codigo_institucion)
		->first();
    }

    private function getGender($genero)
    {
        $genero = trim($genero);

        $codigo_genero = [
            'MASCULINO' => 1,
            'FEMENINO' => 2,
        ];

        if (array_key_exists($genero, $codigo_genero)) {
            return $codigo_genero[$genero];
        }

        return null;
    }

    private function getLevelAcademic($nivel_academico = "")
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
     * obtiene tipo de identificacion
     * $tipo_identificacion string
     */
    private function getOptionIdentification($tipo_identificacion)
    {

        if (is_null($tipo_identificacion)) return null ;

        $tipo_identificacion = trim($tipo_identificacion);
        $codigo_tipo_identificacion = [
            'Cédula' => 1,
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

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * validacion de datos del excel
     * antes de correr rutina
     */
    public function validateData($results)
    {
        Log::info('validando datos');
        $history = array();
        $history = $this->validateIndex($results, $history);

        if (!count($history) > 0) {
            foreach($results as $key => $resul){
                \Log::info($resul);

                //si los campos obligatorias están vacios se entiende que es el final
                //del archivo
                if ($this->isFinal($resul)) break;

                //user data
                $data_user = [];
                $data_user['tipo_identificacion']   = $this->getOptionIdentification($resul['tipo_identificacion']);
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGATORIO
                $data_user['primer_nombre']         = trim($resul['primer_nombre']); //OBLIGATORIO
                $data_user['segundo_nombre']        = trim($resul['segundo_nombre']); // NO OBLIGATORIO
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); //OBLIGATORIO
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);//NO SOBLIGATORIO
                $data_user['fecha_nacimiento']      = substr(trim($resul['fecha_de_nacimiento']),0,10); //OBLIGATORIO
                $data_user['telefono']              = trim($resul['telefono']); //NO OBLIGATORIO
                $data_user['celular']               = trim($resul['celular']); //OBLIGATORIO
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //OBLIGATORIO
                $data_user['sexo']                  = $this->getGender($resul['genero']); //OBLIGATORIO
                $data_user['nivel_academico']       = $this->getLevelAcademic($resul['nivel_academico']);//NO OBLIGATORIO

                if (is_null($data_user['tipo_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene tipo_identificacion incompleto" . "\n");
                }

                if (empty($data_user['numero_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion incompleto" . "\n");
                }

                if (empty($data_user['primer_nombre'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene primer_nombre incompleto" . "\n");
                }

                if (empty($data_user['primer_apellido'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene primer_apellido incompleto" . "\n");
                }

                if (!$this->validateDate($data_user['fecha_nacimiento'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene fecha_nacimiento incorrecta" . "\n");
                }

                if (empty($data_user['celular'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene celular incompleto" . "\n");
                }

                if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                    array_push($history, "- La fila " . ($key + 2). " tiene correo incorecto" . "\n");
                }

                if (is_null($data_user['sexo'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene sexo incorrecto" . "\n");
                }

            }
        }

        $content = implode($history);

        Storage::disk('local')->put('propace/errores_archivo.txt', $content);

        \Log::info("Fin validacion datos");

        if (count($history) > 0) {
            // die;
            throw new \Exception("Hay errores en el excel");

        }
    }

    /**
     * validacion de encabezados del excel
     */
    private function validateIndex($results, $history): array
    {
        foreach ($results as $key => $resul) {
            //validamos index/encabezados del excel
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

            break;
        }

        return $history;
    }

    private function isFinal($resul)
    {
        //si los campos obligatorias están vacios se entiende que es el final
        //del archivo
        if (
            $resul['tipo_identificacion']   == null
            && $resul['numero_identificacion'] == null
            && $resul['primer_nombre']         == null
            && $resul['primer_apellido']       == null
            && $resul['fecha_de_nacimiento']      == null
            && $resul['celular']               == null
            && $resul['correo_electronico']    == null
            && $resul['genero']                  == null
        ) {
            \Log::info('isFinal');
            return true;
        }

        return false;
    }
}
