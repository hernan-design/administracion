<?php

namespace App\Imports\Propace;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail;


use App\Console\Commands\LoadUsers\CargaUsuarios;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Persona,
    App\Models\Param\Institucion;

use App\Imports\Utilities;

class LoadUsersPropace extends Utilities
{
    private const ID_INSTITUTION     = 75;
    private const ID_ROL_INSTITUTION = 17;
    private const ID_ROL_ASSOCIATED  = 39;
    private const ID_ROL_USER        = 3;

    public static function upload()
    {
        Log::info('Inicio.');

        $history = array();

        Excel::load('/storage/app/propace/cargue_usuarios_propace.xlsx', function($reader) use($history) {
            $results = $reader->get()->toArray();

            self::validateData($results);

            foreach($results as $resul){

                if (self::isFinal($resul)) break;

                //user data
                $data_user = [];
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); // *
                $data_user['nombre_usuario']        = trim($resul['nombre_completo']); // *
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); // *
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);
                $data_user['tipo_identificacion']   = self::getOptionIdentification($resul['tipo_identificacion']);
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); // *
                $data_user['movil']                 = trim($resul['telefono_contacto']); // *
                $data_user['id_institucion']        = self::ID_INSTITUTION;
                $data_user['id_rol_institucion']    = self::ID_ROL_INSTITUTION;
                $data_user['id_rol_associated']     = self::ID_ROL_ASSOCIATED;


                //Identifica que datos requiere almacenar.
                $data_require = [
                    "tipo_identificacion" => true,
                    "numero_identificacion" => true,
                    "nombre_usuario" => true,
                    "primer_apellido" => true,
                    "segundo_apellido" => true,
                    "fecha_nacimiento" => false,
                    "telefono" => false,
                    "movil" => true,
                    "correo_electronico" => true,
                    "direccion_usuario" => false,
                    "sexo" => false,
                    "nivel_academico" => false,
                    "ciudad_nacimiento" => false,
                    "usuario_institucion" => true,
                ];

                $user = self::getUser($data_user['correo_electronico'], $data_user['numero_identificacion']);

                if ((!$user instanceof Usuario)) {
                    $user = CargaUsuarios::store($data_user, $data_require);

                    //Envía notificación.
                    $data = [];
                    $data['_inst'] = self::ID_INSTITUTION;
                    $data['_to'] = $data_user['correo_electronico'];
                    $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                    $data['_subject'] = "Confirmación de registro plataforma";
                    $data['_template'] = 'emails.user.restorepassword';
                    $data['_change_password']  = "/password/email";
                    $data['_current_password']  = $user->password;
                    SendEmail::notification($data);

                    array_push($history, "Creo el usuario: ".$data_user['correo_electronico']."\n");
                    Log::info("Creo el usuario: ".$data_user['correo_electronico']."\n");
                } else {

                    if($data_user['nombre_usuario'] != "")
                    $user->nombre_usuario = $data_user['nombre_usuario'];
                    if($data_user['tipo_identificacion'] != "")
                            $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];
                    if($data_user['numero_identificacion'] != "")
                            $user->identificacion_usuario = $data_user['numero_identificacion'];
                    if($data_user['numero_identificacion'] != "")
                            $user->movil_usuario = $data_user['movil'];

                    $user->save();

                    $person = self::getPerson($user->codigo_usuario);
                    if($data_user['primer_apellido'] != "")
                        $person->primer_apellido_persona = $data_user['primer_apellido'];

                    if($data_user['segundo_apellido'] != "")
                        $person->segundo_apellido_persona = $data_user['segundo_apellido'];

                    $person->save();

                    $usuario_institucion = self::getUsuarioInstitucion($user->codigo_usuario, self::ID_INSTITUTION);
                    if(!$usuario_institucion instanceof UsuarioInstitucion)
                    {
                        $usuarioIdentificacion = self::getIdentificationUser($data_user['numero_identificacion'], self::ID_INSTITUTION);
                        if (!$usuarioIdentificacion instanceof Usuario)
                        {
                            $nusuario_institucion = new UsuarioInstitucion;
                            $nusuario_institucion->codigo_usuario = $user->codigo_usuario;
                            $nusuario_institucion->codigo_institucion = self::ID_INSTITUTION;
                            $nusuario_institucion->codigo_estado = 8;
                            $nusuario_institucion->codigo_rol = 3;
                            $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                            $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                            $nusuario_institucion->save();

                            $userRol = RolUsuario::where('codigo_institucion', self::ID_INSTITUTION)
                                                ->where('role_id', self::ID_ROL_INSTITUTION)
                                                ->where('user_id', $user->codigo_usuario)
                                                ->first();

                            if(!$userRol instanceof RolUsuario)
                            {
                                $userRol = new RolUsuario;
                                $userRol->role_id = self::ID_ROL_INSTITUTION;
                                $userRol->user_id = $user->codigo_usuario;
                                $userRol->codigo_institucion = self::ID_INSTITUTION;
                                $userRol->save();
                            }

                            $userRolAsociado = RolUsuario::where('codigo_institucion', self::ID_INSTITUTION)
                                                    ->where('role_id', self::ID_ROL_ASSOCIATED)
                                                    ->where('user_id', $user->codigo_usuario)
                                                    ->first();

                            if(!$userRolAsociado instanceof RolUsuario)
                            {
                                $userRolAsociado = new RolUsuario;
                                $userRolAsociado->role_id = self::ID_ROL_ASSOCIATED;
                                $userRolAsociado->user_id = $user->codigo_usuario;
                                $userRolAsociado->codigo_institucion = self::ID_INSTITUTION;
                                $userRolAsociado->save();
                            }

                            //Envía notificación.
                            $data = [];
                            $data['_inst'] = self::ID_INSTITUTION;
                            $data['_to'] = $data_user['correo_electronico'];
                            $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
                            $data['_subject'] = "Confirmación de registro plataforma";
                            $data['_template'] = 'emails.user.restorepassword';
                            $data['_change_password']  = "/password/email";

                            SendEmail::notification($data);
                            array_push($history, "Se crea el usuario en la institucion: ".$data_user['correo_electronico']."\n");
                            Log::info("Se crea el usuario en la institucion: ".$data_user['correo_electronico']."\n");
                        }
                        else
                        {
                            array_push($history, "Usuario con codigo de identificacion : ".$data_user['numero_identificacion']." ya se encuentra registrado en la institucion, se actualiza. \n");
                            Log::info("Usuario con codigo de identificacion : ".$data_user['numero_identificacion']." ya se encuentra registrado  en la institucion\n");
                            continue;
                        }
                    }
                    else
                    {
                        array_push($history, "Usuario ya existe, se actualiza: ".$data_user['correo_electronico']."\n");
                        Log::info("Usuario ya existe, se actualiza: ".$data_user['correo_electronico']."\n");
                        continue;
                    }
                }

                //Acepta terminos y condiciones.
                CargaUsuarios::acceptTerms($user, self::ID_INSTITUTION);
            }
            array_push($history, "Finalizo");

            //Envía notificación.
            $data = [];
            $data['_inst']     = self::ID_INSTITUTION;
            $data['_to']       = 'administracion@propace.org';
            $data['to_copy']   = 'administracion@tuproyecto.com';
            $data['_to_name']  = 'Fomento Empresarial';
            $data['_subject']  = "Carga de datos";
            $data['_template'] = 'emails.user.notificationdata';
            $data['_history']  = $history;

            SendEmail::notification($data);
        });
        Log::info("Finalizo");
    }

    /**
     * Valida datos del excel.
     *
     * @access public
     * @static function
     * @param array $results
     */
    public static function validateData($results)
    {
        Log::info('Inicia validación de datos');

        $history = array();
        $history = self::validateIndex($results, $history);

        if (!count($history) > 0)
        {
            foreach($results as $key => $resul)
            {

                if (self::isFinal($resul)) break;

                // Datos del usuario.
                $data_user = [];
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); // *
                $data_user['nombre_usuario']        = trim($resul['nombre_completo']); // *
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); // *
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);
                $data_user['tipo_identificacion']   = self::getOptionIdentification($resul['tipo_identificacion']);
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); // *
                $data_user['movil']                 = trim($resul['telefono_contacto']); // *


                if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                    array_push($history, "- La fila " . ($key + 2). " no tiene correo o es incorecto" . "\n");
                }

                if (empty($data_user['nombre_usuario'])) {
                    array_push($history, "- La fila " . ($key + 2). " no tiene nombre_completo" . "\n");
                }

                if (empty($data_user['primer_apellido'])) {
                    array_push($history, "- La fila " . ($key + 2). " no tiene primer_apellido" . "\n");
                }

                if (is_null($data_user['tipo_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " no tiene tipo_identificacion o es incorecto" . "\n");
                }

                if (empty($data_user['numero_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " no tiene numero_identificacion" . "\n");
                }elseif ( !is_numeric($data_user['numero_identificacion'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene numero_identificacion con dato incorrecto, debe ser numerico" . "\n");
                }

                if (empty($data_user['movil'])) {
                    array_push($history, "- La fila " . ($key + 2). " no tiene telefono_contacto" . "\n");
                }elseif ( !is_numeric($data_user['movil'])) {
                    array_push($history, "- La fila " . ($key + 2). " tiene telefono_contacto con dato incorrecto, debe ser numerico" . "\n");
                }
            }
        }

        $content = implode($history);

        Storage::disk('local')->put('propace/errores_archivo.txt', $content);
        Log::info("Finaliza validación de datos.");

        if (count($history) > 0) {
            throw new \Exception("Hay errores en el archivo cargado.");
        }

        unset($content, $history, $data_user);
        Log::info("No hay errrores en la validacion de datos");
    }

    /**
     * Valida nomenclatura encabezados del excel.
     *
     * @access private
     * @static function
     * @param mixed $results, $history
     * @return array $history
     */
    private static function validateIndex($results, $history)
    {
        foreach ($results as $key => $resul) {

            if (!array_key_exists('correo_electronico', $resul) )
            array_push($history, "- No está definido el encabezado correo_electronico" . "\n");

            if (!array_key_exists('nombre_completo', $resul) )
            array_push($history, "- No está definido el encabezado primer_nombre" . "\n");

            if (!array_key_exists('primer_apellido', $resul) )
            array_push($history, "- No está definido el encabezado primer_apellido" . "\n");

            if (!array_key_exists('segundo_apellido', $resul) )
            array_push($history, "- No está definido el encabezado segundo_apellido" . "\n");

            if (!array_key_exists('tipo_identificacion', $resul) )
                array_push($history, "- No está definido el encabezado tipo_identificacion" . "\n");

            if (!array_key_exists('numero_identificacion', $resul) )
                array_push($history, "- No está definido el encabezado numero_identificacion" . "\n");

            if (!array_key_exists('telefono_contacto', $resul) )
                array_push($history, "- No está definido el encabezado telefono_contacto" . "\n");

            break;
        }

        return $history;
    }

    /**
     * Valida si es final de los registros.
     *
     * @Annotation: Si los campos obligatorias están vacios se entiende que es el final del archivo.
     * @access private
     * @static function
     * @param array $resul
     * @return boolean true|false
     */
    private static function isFinal($resul)
    {
        if (
            $resul['tipo_identificacion']      == null
            && $resul['numero_identificacion'] == null
            && $resul['nombre_completo']         == null
            && $resul['primer_apellido']       == null
            && $resul['correo_electronico']    == null
            && $resul['segundo_apellido']       == null

        ) {
            Log::info('Final de los registros.');
            return true;
        }

        return false;
    }
}