<?php

namespace App\Console\Commands\LoadEntities;

use Config, Log, TuProyecto;

use App\Models\Core\{ClaseEmprendimiento, ClaseEmprendimientoInstitucion,
    DatoEntidad, Entidad,
    Estado, PrivacidadContenido,
    UsuarioInstitucion};

use App\Models\Seguridad\{Rol, Usuario};

class CargaEntidad
{

    /**
     * Crea usuario (tipo entidad).
     *
     * @access public
     * @param array $data_entity
     * @param int $id_user, $id_institution
     * @return mixed
     */
    public static function store($id_user, $data_entity = [], $id_institution)
    {
        try {

            //Datos del usuario.
            $user = new Usuario;
            $user->codigo_estado              = Estado::getState('active');
            $user->nombre_usuario             = $data_entity['nombre_usuario'];
            $user->biografia_usuario          = $data_entity['biografia_usuario'];
            $user->codigo_ciudad              = $data_entity['codigo_ciudad'];
            $user->tipo_usuario               = Usuario::typeUser('entity');
            $user->imagen_usuario             = Config::get('tuproyecto.no_image');
            $user->telefono_usuario           = $data_entity['telefono_usuario'];
            $user->numero_whatsapp            = $data_entity['numero_whatsapp'];
            $user->fecha_nacimiento_usuario   = empty($data_entity['fecha_nacimiento_usuario']) ? null : $data_entity['fecha_nacimiento_usuario'];
            $user->pagina_web_usuario         = $data_entity['pagina_web_usuario'];
            $user->facebook_usuario           = $data_entity['facebook_usuario'];
            $user->twitter_usuario            = $data_entity['twitter_usuario'];
            $user->linkedin_usuario           = $data_entity['linkedin_usuario'];
            $user->instagram_usuario          = $data_entity['instagram_usuario'];
            
            $username = TuProyecto::unwantedCharacters($data_entity['nombre_usuario']);

            // Genera nickname.
            $usernickname= Usuario::validateNinckname(str_slug(str_limit($username, 16), ""));
            $user->nickname_usuario = $usernickname;

            if($data_entity['identificacion_usuario'] != ''){

                // Validate User
                $validate_user= Usuario::join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                    ->where('identificacion_usuario', $data_entity['identificacion_usuario'])
                    ->where('tipo_usuario', Usuario::typeUser('entity'))
                    ->where('usuario_institucion.codigo_institucion', '=', $id_institution)
                    ->first();

                if($validate_user instanceof Usuario) {

                    $message = "Ya existe una entidad con esa identificación: {$data_entity['identificacion_usuario']}";

                    /*Retorna boolean para validar si se crea usuario, mensaje y  null para que no dé error al intentar recuperar 3er elemento del array */

                    return [false, $message, null];
                }

                $user->identificacion_usuario = $data_entity['identificacion_usuario'];
            }

            $user->save();

            // Crea relación entre modelo Usuario e Institución.
            $userInstitution = new UsuarioInstitucion;
            $userInstitution->codigo_usuario = $user->codigo_usuario;
            $userInstitution->codigo_institucion = $id_institution;
            $userInstitution->codigo_estado = Estado::getState('active');
            $userInstitution->fecha_registro_usuario = date('Y-m-d');
            $userInstitution->hora_registro_usuario = date('H:i:s');
            $userInstitution->codigo_rol = Rol::getRoleId('user');
            $userInstitution->save();

            // Crea entidad.
            $entity = new Entidad;
            //$entity->fill($data_entity);
            $entity->codigo_usuario = $user->codigo_usuario;
            $entity->seg_codigo_usuario = $id_user;
            $entity->codigo_clase_empre_insti = $data_entity['codigo_clase_empre_insti'];
            $entity->codigo_tipo_identificacion = $data_entity['codigo_tipo_identificacion'];
            $entity->correo_contacto_entidad = $data_entity['correo_contacto'];
            $entity->save();

            //Crea datos de la entidad.
            $dataEntity = new DatoEntidad;
            $dataEntity->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
            $dataEntity->codigo_privacidad_contenido = PrivacidadContenido::getPrivacy('public');
            $dataEntity->codigo_estado = Estado::getState('active');
            $dataEntity->codigo_categoria_proyecto = $data_entity['codigo_categoria_proyecto'];

            $classEntrepreneurship = ClaseEmprendimientoInstitucion::find($entity->codigo_clase_empre_insti);
            if(!$classEntrepreneurship instanceof ClaseEmprendimientoInstitucion) {
                Log::info('No se encuentra ClaseEmprendimientoInstitucion');
                throw new \Exception("Error Processing Save Entity");
                die;
            }

            switch ($classEntrepreneurship->codigo_clase_emprendimiento) {
                case ClaseEmprendimiento::getClass('project'):
                    if($data_entity['codigo_etapa_proyecto'] != ''){
                        $dataEntity->codigo_etapa_proyecto = $data_entity['codigo_etapa_proyecto'];
                    }
                break;

                case ClaseEmprendimiento::getClass('enterprise'):
                    if($data_entity['codigo_etapa_proyecto']){
                        $dataEntity->codigo_etapa_proyecto = $data_entity['codigo_etapa_proyecto'];
                    }
                break;
            }

            $dataEntity->save();
            $message = "Se creó la entidad: " . $data_entity['nombre_usuario'];

            /*Retorna boolean para validar si se crea usuario, mensaje y $user->codigo_usuario*/

            return [true, $message, $user->codigo_usuario];

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CargaEntidad::store ', $e->getMessage()));
            throw new \Exception("Error Processing Save Entity");
        }
    }
}
