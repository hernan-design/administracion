<?php

namespace App\Console\Commands\LoadUsers;

use Hash, Log, TuProyecto;

use App\Models\Core\{Estado, Persona, UsuarioInstitucion};

use App\Models\Seguridad\{AceptacionTerminos, Usuario,
    RolUsuario};
use App\Models\Param\TerminosCondiciones;

class CargaUsuarios
{

    /**
     * Guarda usuarios
     * @param  array $data_user
     * @return Usuario
     */
    public static function store($data_user, $data_require = []): Usuario
    {
        try {

            // Validación del nickname.
            if ($data_user['id_institucion'] != 62) {
                $username = TuProyecto::unwantedCharacters($data_user['nombre_usuario']);
                $usernickname= Usuario::validateNinckname(str_slug(str_limit($username, 16), ""));
            }

            $user = new Usuario;
            $user->correo_electronico = $data_user['correo_electronico'];
            $user->codigo_estado = Estado::getState('active');

            if(isset($data_require['nombre_usuario']) && $data_require['nombre_usuario'])
                $user->nombre_usuario = $data_user['nombre_usuario'];

            if(isset($data_require['fecha_nacimiento']) && $data_require['fecha_nacimiento'])
                $user->fecha_nacimiento_usuario = empty($data_user['fecha_nacimiento']) ? null : $data_user['fecha_nacimiento'];

            if(isset($data_require['movil']) && $data_require['movil'])
                $user->movil_usuario = $data_user['movil'];

            if(isset($data_require['telefono']) && $data_require['telefono'])
                $user->telefono_usuario = $data_user['telefono'];

            if(isset($data_require['direccion_usuario']) && $data_require['direccion_usuario'])
                $user->direccion_usuario = $data_user['direccion_usuario'];

            if(isset($data_require['tipo_identificacion']) && $data_require['tipo_identificacion'])
                $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];

            if(isset($data_require['numero_identificacion']) && $data_require['numero_identificacion'])
                $user->identificacion_usuario = $data_user['numero_identificacion'];

            if(isset($data_require['ciudad_nacimiento']) && $data_require['ciudad_nacimiento'])
                $user->codigo_ciudad = $data_user['ciudad_nacimiento'];

            $password = str_random(8);
            $user->password = Hash::make($password);

            if ($data_user['id_institucion'] != 62) {
                $user->nickname_usuario = $usernickname;
            }

            $user->save();

            $user->password = $password;

            $n_user_institution = new UsuarioInstitucion;
            $n_user_institution->codigo_usuario = $user->codigo_usuario;
            $n_user_institution->codigo_institucion = $data_user['id_institucion'];
            $n_user_institution->codigo_estado = Estado::getState('active');
            $n_user_institution->fecha_registro_usuario = date('Y-m-d');
            $n_user_institution->hora_registro_usuario = date('H:m:i');

            // Para que no envie al usuario a step/perfil.
            if (isset($data_require['usuario_institucion']) && $data_require['usuario_institucion']) {
                $n_user_institution->fecha_registro_info_basica = date('Y-m-d H:i:s');
            }

            $n_user_institution->save();

            //$id_user_institution = $n_user_institution->codigo_usuario_institucion;

            $n_person = new Persona;
            $n_person->codigo_usuario = $user->codigo_usuario;

            if(isset($data_require['sexo']) && $data_require['sexo'])
                $n_person->genero_persona = $data_user['sexo'];

            if(isset($data_require['primer_apellido']) && $data_require['primer_apellido'])
                $n_person->primer_apellido_persona = $data_user['primer_apellido'];

            if(isset($data_require['segundo_apellido']) && $data_require['segundo_apellido'])
                $n_person->segundo_apellido_persona = $data_user['segundo_apellido'];

            if(isset($data_require['nivel_academico']) && $data_require['nivel_academico'])
                $n_person->codigo_nivel_educacion = $data_user['nivel_academico'];

            if(isset($data_require['codigo_ocupacion']) && $data_require['codigo_ocupacion'])
                $n_person->codigo_ocupacion_persona = $data_user['codigo_ocupacion'];

            if(isset($data_require['codigo_profesion']) && $data_require['codigo_profesion'])
                $n_person->codigo_profesion_persona = $data_user['codigo_profesion'];

            $n_person->save();

            $user_rol = RolUsuario::where('codigo_institucion', $data_user['id_institucion'])
                                        ->where('role_id', $data_user['id_rol_institucion'])
                                        ->where('user_id', $user->codigo_usuario)
                                        ->first();

            if(!$user_rol instanceof RolUsuario) {
                $user_rol = new RolUsuario;
                $user_rol->role_id = $data_user['id_rol_institucion'];
                $user_rol->user_id = $user->codigo_usuario;
                $user_rol->codigo_institucion = $data_user['id_institucion'];
            $user_rol->save();
            }

            $user_rol_associated = RolUsuario::where('codigo_institucion', $data_user['id_institucion'])
                                                ->where('role_id', $data_user['id_rol_associated'])
                                                ->where('user_id', $user->codigo_usuario)
                                                ->first();

            if(!$user_rol_associated instanceof RolUsuario) {
                $user_rol_associated = new RolUsuario;
                $user_rol_associated->role_id = $data_user['id_rol_associated'];
                $user_rol_associated->user_id = $user->codigo_usuario;
                $user_rol_associated->codigo_institucion = $data_user['id_institucion'];
            $user_rol_associated->save();
            }

            return $user;

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CargaUsuarios::store ', $e->getMessage()));
            throw new \Exception("Error Processing Save User");
        }
    }

    /**
     * return Usuario | string
     */
    public static function update($data_user, $data_require = [], Usuario $user, Persona $n_person)
    {
        try {

            if (
                $user->identificacion_usuario == $data_user['numero_identificacion']
                && $user->correo_electronico != $data_user['correo_electronico']
            ) {
                $correo = Usuario::where('correo_electronico', $data_user['correo_electronico'])->first();
                if ($correo instanceof Usuario) {
                    //no se actualiza correo suministrado, no continua.
                    return $msg = '- El correo_electronico suministrado ya está en uso, valide. ';
                }
            }

            if (
                $user->correo_electronico == $data_user['correo_electronico']
                && $user->identificacion_usuario != $data_user['numero_identificacion']
            ) {
                $cedula = Usuario::where('identificacion_usuario', $data_user['numero_identificacion'])->first();
                if ($cedula instanceof Usuario) {
                    //no se actualiza numero identificacion suministrado, no continua.
                    return $msg = '- El numero_identificacion suministrado ya está en uso, valide. ';
                }
            }

            $user->codigo_estado = 8; //activo
            $user->correo_electronico = $data_user['correo_electronico'];

            if(isset($data_require['nombre_usuario']) && $data_require['nombre_usuario'])
                $user->nombre_usuario = $data_user['nombre_usuario'];

            if(isset($data_require['fecha_nacimiento']) && $data_require['fecha_nacimiento'])
                $user->fecha_nacimiento_usuario = empty($data_user['fecha_nacimiento']) ? null : $data_user['fecha_nacimiento'];

            if(isset($data_require['movil']) && $data_require['movil'])
                $user->movil_usuario = $data_user['movil'];

            if(isset($data_require['telefono']) && $data_require['telefono'])
                $user->telefono_usuario = $data_user['telefono'];

            if(isset($data_require['direccion_usuario']) && $data_require['direccion_usuario'])
                $user->direccion_usuario = $data_user['direccion_usuario'];

            if(isset($data_require['tipo_identificacion']) && $data_require['tipo_identificacion'])
                $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];

            if(isset($data_require['numero_identificacion']) && $data_require['numero_identificacion'])
                $user->identificacion_usuario = $data_user['numero_identificacion'];

            if(isset($data_require['ciudad_nacimiento']) && $data_require['ciudad_nacimiento'])
                $user->codigo_ciudad = $data_user['ciudad_nacimiento'];

            // $password = str_random(8);
            $password = 'Ii1!'; //default password para innpulsa
            $user->password = Hash::make($password);
            $user->save();

            // $n_person = Persona::where('codigo_usuario', $user->codigo_usuario)->first();

            // if (!$n_person instanceof Persona) {
            //     throw new \Exception("Error en rutina, no existe codigo usuario $user->codigo_usuario en Persona");
            // }

            // $n_person->codigo_usuario = $user->codigo_usuario;

            if(isset($data_require['sexo']) && $data_require['sexo'])
                $n_person->genero_persona = $data_user['sexo'];

            if(isset($data_require['primer_apellido']) && $data_require['primer_apellido'])
                $n_person->primer_apellido_persona = $data_user['primer_apellido'];

            if(isset($data_require['segundo_apellido']) && $data_require['segundo_apellido'])
                $n_person->segundo_apellido_persona = $data_user['segundo_apellido'];

            if(isset($data_require['nivel_academico']) && $data_require['nivel_academico'])
                $n_person->codigo_nivel_educacion = $data_user['nivel_academico'];

            if(isset($data_require['codigo_ocupacion']) && $data_require['codigo_ocupacion'])
                $n_person->codigo_ocupacion_persona = $data_user['codigo_ocupacion'];

            if(isset($data_require['codigo_profesion']) && $data_require['codigo_profesion'])
                $n_person->codigo_profesion_persona = $data_user['codigo_profesion'];

            $n_person->save();

            return $user;

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CargaUsuarios::update ', $e->getMessage()));
            throw new \Exception("Error Processing Update User");
        }
    }
    /**
     * Método que permite aceptar terminos y condiciones de la plataforma
     *
     * @param Usuario $usuario
     * @param int $codigo_institucion
     * @return void
     */
    public static function acceptTerms(Usuario $usuario, $codigo_institucion)
    {
        try {
            $userInstitution = UsuarioInstitucion::where('codigo_usuario', '=',  $usuario->codigo_usuario )
                                                ->where('codigo_institucion', '=', $codigo_institucion)
                                                ->first();
            if (!$userInstitution instanceof UsuarioInstitucion) {
                throw new \Exception("Error No se encuentra UsuarioInstitucion en acceptTerms: $usuario->codigo_usuario ");
            }
            // Terms
            $terms = TerminosCondiciones::termsAndConditionsInstitution(TerminosCondiciones::getTypeTerms('terms'), $codigo_institucion);
            if($terms instanceof TerminosCondiciones) {
                $acceptanceTerms = AceptacionTerminos::where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                                    ->where('codigo_version_terminos', $terms->codigo_version_terminos)
                                                    ->first();
                if(!$acceptanceTerms instanceof AceptacionTerminos) {
                    $acceptanceTerms = new AceptacionTerminos();
                    $acceptanceTerms->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                    $acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
                    $acceptanceTerms->codigo_estado = Estado::getState('active');
                    $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                    $acceptanceTerms->save();
                }
            }

            // Privacy
            $privacy = TerminosCondiciones::termsAndConditionsInstitution(TerminosCondiciones::getTypeTerms('privacy'), $codigo_institucion);
            if($privacy instanceof TerminosCondiciones) {
                $acceptanceTerms = AceptacionTerminos::where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                                ->where('codigo_version_terminos', $privacy->codigo_version_terminos)
                                                ->first();
                if(!$acceptanceTerms instanceof AceptacionTerminos) {
                    $acceptanceTerms = new AceptacionTerminos();
                    $acceptanceTerms->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                    $acceptanceTerms->codigo_version_terminos = $privacy->codigo_version_terminos;
                    $acceptanceTerms->codigo_estado = Estado::getState('active');
                    $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                    $acceptanceTerms->save();
                }
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CargaUsuarios::acceptTerms ', $e->getMessage()));
            throw new \Exception("Error Processing acceptTerms");
        }
    }
}
