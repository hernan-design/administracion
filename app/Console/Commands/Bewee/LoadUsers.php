<?php

namespace App\Console\Commands\Bewee;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
App\Models\Seguridad\RolUsuario,
App\Models\Core\UsuarioInstitucion,
App\Models\Core\Persona,
App\Models\Param\Institucion,
App\Models\Core\Estado;

class LoadUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bwee:loadusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios archivo Bwee';

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
        Excel::load('users_bwee.xlsx', function($reader) use($history) {
            $results = $reader->get()->toArray();
            $rol_institucion = 65;
            $codigo_institucion = 73;
            $rol_asociado = 39;
            $notificacion_externa = 'sebastian@tuproyecto.com';
            $notificacion_interna = 'diego.gomez@tuproyecto.com';
            foreach($results as $resul){
                
                $mail_rep_legal = strtolower(trim($resul['correo_electronico']));
                $tipo_identificacion = trim($resul['tipo_identificacion']);
                $codigo_tipo_identificacion = "";
                if($tipo_identificacion == 'Cédula'){
                    $codigo_tipo_identificacion = 1;
                }else if($tipo_identificacion == 'Permiso especial de permanencia'){
                    $codigo_tipo_identificacion = 9;
                }else if($tipo_identificacion == 'Cédula de extranjería'){
                    $codigo_tipo_identificacion = 2;
                }else if($tipo_identificacion == 'Cédula de otro país'){
                    $codigo_tipo_identificacion = 11;
                }else if($tipo_identificacion == 'Pasaporte'){
                    $codigo_tipo_identificacion = 5;
                }else if($tipo_identificacion == 'Tarjeta de Identidad'){
                    $codigo_tipo_identificacion = 4;
                }else if($tipo_identificacion == 'Documento nacional de identificación'){
                    $codigo_tipo_identificacion = 12;
                }else if($tipo_identificacion == 'Nit'){
                    $codigo_tipo_identificacion = 3;
                }else if($tipo_identificacion == 'Registro Civil'){
                    $codigo_tipo_identificacion = 6;
                }else{
                    $codigo_tipo_identificacion = null;
                }

                $documento_representante = trim($resul['numero_identificacion']);
                $primer_nombre = trim($resul['primer_nombre']);
                $segundo_nombre = trim($resul['segundo_nombre']);
                $nombre_usuario = $primer_nombre." ".$segundo_nombre;
                $primer_apellido_usuario = trim($resul['primer_apellido']);
                $segundo_apellido_usuario = trim($resul['segundo_apellido']);
                $fecha_de_nacimiento = substr(trim($resul['fecha_de_nacimiento']),0,10);
                if($fecha_de_nacimiento!= ""){
                    $porciones = explode("-", $fecha_de_nacimiento);
                    if(count($porciones)==3){
                        if(!checkdate($porciones[1], $porciones[2], $porciones[0])){
                            $fecha_de_nacimiento = null;
                        }
                    }else{
                        $fecha_de_nacimiento = null;
                    }            
                }
                
                $telefono = trim($resul['telefono']);
                $tel_rep_legal = trim($resul['celular']);
                $direccion = trim($resul['direccion']);
                $genero = trim($resul['genero']);
                $codigo_genero = "";
                if($genero == 'M'){
                    $codigo_genero = 1;
                }else if($genero == 'F'){
                    $codigo_genero = 2;
                }else{
                    $codigo_genero = null;
                }

                // \Log::info("Fecha: ".$fecha_de_nacimiento."\n");

                $nivel_academico = trim($resul['nivel_academico']);
                $nivel_academico_usuario = "";
                if($nivel_academico == 'Bachiller'){
                    $nivel_academico_usuario = 8;
                }else if($nivel_academico == 'Básica Secundaria'){
                    $nivel_academico_usuario = 14;
                }else if($nivel_academico == 'Doctorado'){
                    $nivel_academico_usuario = 5;
                }else if($nivel_academico == 'Especialización'){
                    $nivel_academico_usuario = 4;
                }else if($nivel_academico == 'Maestría'){
                    $nivel_academico_usuario = 11;
                }else if($nivel_academico == 'Primaria'){
                    $nivel_academico_usuario = 13;
                }else if($nivel_academico == 'Profesional'){
                    $nivel_academico_usuario = 3;
                }else if($nivel_academico == 'Sin título'){
                    $nivel_academico_usuario = 12;
                }else if($nivel_academico == 'Técnico laboral'){
                    $nivel_academico_usuario = 1;
                }else if($nivel_academico == 'Técnico Profesional'){
                    $nivel_academico_usuario = 15;
                }else if($nivel_academico == 'Tecnólogica'){
                    $nivel_academico_usuario = 9;
                }else{
                    $nivel_academico_usuario = null;
                }
                \Log::info("Aqui va 0 \n");
                if($mail_rep_legal != ""){
                    \Log::info("Aqui va \n");
                    $codigo_usuario = "";
                    $codigo_usuario_institucion = "";
                    $filter_mail = filter_var($mail_rep_legal, FILTER_VALIDATE_EMAIL);
                    if($filter_mail){
                        \Log::info("Aqui va 1 \n");
                        $usuario = $this->getEmailUsuario($mail_rep_legal);
                        if(!$usuario instanceof Usuario){
                            \Log::info("Aqui va 2 \n");
                            $usuarioIdentificacion = $this->getIdentificationUser($documento_representante, $codigo_institucion);
                            if (!$usuarioIdentificacion instanceof Usuario) {
                                $nusuario = new Usuario;
                                if($nombre_usuario!="")
                                    $nusuario->nombre_usuario = $nombre_usuario;
                                if($fecha_de_nacimiento!="")
                                    $nusuario->fecha_nacimiento_usuario = $fecha_de_nacimiento;
                                $nusuario->correo_electronico = $mail_rep_legal;
                                if($tel_rep_legal!="")
                                    $nusuario->movil_usuario = $tel_rep_legal;
                                if($telefono!="")
                                    $nusuario->telefono_usuario = $telefono;
                                if($direccion!="")
                                    $nusuario->direccion_usuario = $direccion;
                                if($codigo_tipo_identificacion!="")
                                    $nusuario->tipo_identificacion_usuario = $codigo_tipo_identificacion;
                                if($documento_representante!="")
                                    $nusuario->identificacion_usuario = $documento_representante;
                                $nusuario->codigo_estado = 8;
                                // $nusuario->codigo_estado = 8; revisar genero
                                $nusuario->save();

                                $codigo_usuario = $nusuario->codigo_usuario;

                                $nusuario_institucion = new UsuarioInstitucion;
                                $nusuario_institucion->codigo_usuario = $codigo_usuario;
                                $nusuario_institucion->codigo_institucion = $codigo_institucion;
                                $nusuario_institucion->codigo_estado = 8;
                                $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                                $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                                $nusuario_institucion->save();

                                $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                                $npersona = new Persona;
                                $npersona->codigo_usuario = $codigo_usuario;
                                $npersona->genero_persona = $codigo_genero;
                                if($primer_apellido_usuario!="")
                                    $npersona->primer_apellido_persona = $primer_apellido_usuario;
                                if($segundo_apellido_usuario!="")
                                    $npersona->segundo_apellido_persona = $segundo_apellido_usuario;
                                if($nivel_academico_usuario!="")
                                    $npersona->codigo_nivel_educacion = $nivel_academico_usuario;

                                $npersona->save();

                                $userRol = RolUsuario::where('codigo_institucion', $codigo_institucion)->where('role_id', $rol_institucion)->where('user_id', $codigo_usuario)->first();
                                if(!$userRol instanceof RolUsuario) {
                                    $userRol = new RolUsuario;
                                    $userRol->role_id = $rol_institucion;
                                    $userRol->user_id = $codigo_usuario;
                                    $userRol->codigo_institucion = $codigo_institucion;
                                    $userRol->save();
                                }

                                $userRolAsociado = RolUsuario::where('codigo_institucion', $codigo_institucion)->where('role_id', $rol_asociado)->where('user_id', $codigo_usuario)->first();
                                if(!$userRolAsociado instanceof RolUsuario) {
                                    $userRolAsociado = new RolUsuario;
                                    $userRolAsociado->role_id = $rol_asociado;
                                    $userRolAsociado->user_id = $codigo_usuario;
                                    $userRolAsociado->codigo_institucion = $codigo_institucion;
                                    $userRolAsociado->save();
                                }

                                // Send notification
                                $data = [];
                                $data['_inst'] = $codigo_institucion;
                                $data['_to'] = $mail_rep_legal;
                                $data['_to_name'] = ucwords(strtolower($nombre_usuario));
                                $data['_subject'] = "Confirmación de registro plataforma";
                                $data['_template'] = 'emails.user.restorepassword';
                                $data['_change_password']  = "/password/email";

                                SendEmail::notification($data);

                                array_push($history, "Creo el usuario: ".$mail_rep_legal."\n");
                                \Log::info("Creo el usuario: ".$mail_rep_legal."\n");
                                } else {
                                array_push($history, "Usuario con codigo de identificacion : ".$documento_representante." ya se encuentra registrado \n");
                                \Log::info("Usuario con codigo de identificacion : ".$documento_representante." ya se encuentra registrado \n");
                            }
                        }
                        else{
                            \Log::info("Aqui va 3 \n");
                            $codigo_usuario = $usuario->codigo_usuario;

                            if($telefono!="")
                            $usuario->telefono_usuario = $telefono;
                            if($direccion!="")
                            $usuario->direccion_usuario = $direccion;
                            if($codigo_tipo_identificacion!="")
                            $usuario->tipo_identificacion_usuario = $codigo_tipo_identificacion;
                            if($tel_rep_legal!="")
                            $usuario->movil_usuario = $tel_rep_legal;
                            if($documento_representante!="")
                            $usuario->identificacion_usuario = $documento_representante;
                            $usuario->save();

                            $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, $codigo_institucion);
                            if(!$usuario_institucion instanceof UsuarioInstitucion){
                                \Log::info("Aqui va 4 \n");
                                $usuarioIdentificacion = $this->getIdentificationUser($documento_representante, $codigo_institucion);
                                if (!$usuarioIdentificacion instanceof Usuario) {
                                    \Log::info("Aqui va 5 \n");
                                    $nusuario_institucion = new UsuarioInstitucion;
                                    $nusuario_institucion->codigo_usuario = $codigo_usuario;
                                    $nusuario_institucion->codigo_institucion = $codigo_institucion;
                                    $nusuario_institucion->codigo_estado = 8;
                                    $nusuario_institucion->codigo_rol = 3;
                                    $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                                    $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                                    $nusuario_institucion->save();
                                    $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                                    $userRol = RolUsuario::where('codigo_institucion', $codigo_institucion)->where('role_id', $rol_institucion)->where('user_id', $codigo_usuario)->first();
                                    if(!$userRol instanceof RolUsuario) {
                                        $userRol = new RolUsuario;
                                        $userRol->role_id = $rol_institucion;
                                        $userRol->user_id = $codigo_usuario;
                                        $userRol->codigo_institucion = $codigo_institucion;
                                        $userRol->save();
                                    }

                                    $userRolAsociado = RolUsuario::where('codigo_institucion', $codigo_institucion)->where('role_id', $rol_asociado)->where('user_id', $codigo_usuario)->first();
                                    if(!$userRolAsociado instanceof RolUsuario) {
                                        $userRolAsociado = new RolUsuario;
                                        $userRolAsociado->role_id = $rol_asociado;
                                        $userRolAsociado->user_id = $codigo_usuario;
                                        $userRolAsociado->codigo_institucion = $codigo_institucion;
                                        $userRolAsociado->save();
                                    }

                                    // Send notification
                                    $data = [];
                                    $data['_inst'] = $codigo_institucion;
                                    $data['_to'] = $mail_rep_legal;
                                    $data['_to_name'] = ucwords(strtolower($nombre_usuario));
                                    $data['_subject'] = "Confirmación de registro plataforma";
                                    $data['_template'] = 'emails.user.restorepassword';
                                    $data['_change_password']  = "/password/email";

                                    SendEmail::notification($data);
                                    array_push($history, "Creo el usuario: ".$mail_rep_legal."\n");
                                    \Log::info("Creo el usuario: ".$mail_rep_legal."\n");
                                } else {
                                    array_push($history, "Usuario con codigo de identificacion : ".$documento_representante." ya se encuentra registrado \n");
                                    \Log::info("Usuario con codigo de identificacion : ".$documento_representante." ya se encuentra registrado \n");
                                }
                            }
                            else{
                                $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
                                array_push($history, "Usuario ya existe: ".$mail_rep_legal."\n");
                                \Log::info("Usuario ya existe: ".$mail_rep_legal."\n");
                            }
                        }
                    }else{
                        array_push($history, "Correo en formato no valido: ".$mail_rep_legal."\n");
                        \Log::info("Formato no valido: ".$mail_rep_legal."\n");
                    }           
                }
            }
            array_push($history, "Finalizo");

            // Send notification
            $data = [];
            $data['_inst'] = $codigo_institucion;
            $data['_to'] = $notificacion_externa;
            $data['to_copy'] = $notificacion_interna;
            $data['_to_name'] = 'Cargue masivo de usuarios';
            $data['_subject'] = "Carga de datos";
            $data['_template'] = 'emails.user.notificationdata';
            $data['_history'] = $history;

            SendEmail::notification($data);
        });
        \Log::info("Finalizo111");
    }

    public static function getEmailUsuario($email) {
		// return Usuario::where('correo_electronico', 'ilike',  "%".trim($email)."%") -> first();
        return Usuario::where('correo_electronico', '=',  trim($email)) -> first();
    }

    public static function getIdentificationUser($identification, $codigo_institucion) {
        $query = Usuario::query();
        $query->select('*');
        $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->where('usuario_institucion.codigo_institucion', '=', $codigo_institucion);
        $query->where('usuario.identificacion_usuario', '=', trim($identification));
        $data = $query->first();
        return $data;
    }

    public static function getusuarioInstitucion($codigo_usuario,$codigo_institucion){
		return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
		->where('codigo_institucion', '=', $codigo_institucion)
		->first();
    }
}
