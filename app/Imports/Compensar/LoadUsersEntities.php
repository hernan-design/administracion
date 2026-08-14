<?php

namespace App\Imports\Compensar;

use App\Imports\EntitiesUsersCompensar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Persona,
    App\Models\Param\Institucion;

use App\Console\Commands\LoadUsers\CargaUsuarios;
use App\Console\Commands\LoadEntities\CargaEntidad;

use App\Imports\Utilities;

class LoadUsersEntities extends Utilities
{
    private const ID_INSTITUTION     = 30;
    private const ID_ROL_INSTITUTION = 53;
    private const ID_ROL_ASSOCIATED  = 39;
    private const ID_ROL_USER        = 3;

    public static function upload()
    {
        \Log::info('leyendo');


        $filePath = 'compensar/cargue_usuarios_entidades_compensar.xlsx';
        $import = new EntitiesUsersCompensar();
        Excel::import($import, Storage::disk('local')->path($filePath));
        $history = $import->isFinalReached();
        array_push($history, "Finalizó");
        // Send notification
        $data = [];
        $data['_inst'] = self::ID_INSTITUTION;
        $data['_to'] = 'fomentoempresarial@compensar.com';
        $data['to_copy'] = ['administracion@tuproyecto.com', 'testertuproyecto@gmail.com'];
        $data['_to_name'] = 'Fomento Empresarial';
        $data['_subject'] = "Carga de datos";
        $data['_template'] = 'emails.user.notificationdata';
        $data['_history'] = $history;

        SendEmail::notification($data);
        \Log::info("Finalizo");
    }

    /**
     * return null or int
     */
    public static function getGender($genero = "")
    {
        $genero = mb_strtolower(trim($genero));

        $codigo_genero = [
            'm' => 1,
            'f' => 2,
        ];

        if (array_key_exists($genero, $codigo_genero))
            return $codigo_genero[$genero];

        return null;
    }

    private static function getEntity($entity = "")
    {
        $entity = mb_strtolower(trim($entity));

        $entities = [
            'empresa' => 37,
            'proyecto o idea' => 36
        ];

        if (array_key_exists($entity, $entities)) {
            return $entities[$entity];
        }

        return null;
    }

    private static function getIdentificationCompany($type = "")
    {
        $type = mb_strtolower(trim($type));

        $array = [
            'nit'=>3,
        ];

        if (array_key_exists($type, $array)) {
            return $array[$type];
        }

        return null;
    }

    //get id from option
    private static function getIdOption($str_id, int $count)
    {
        $str_id = explode('-', trim($str_id));
        if (count($str_id) == $count) {
            return (int) $str_id[0];
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
    private static function getCategoryProject($industria_entidad)
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
     * validacion de datos del excel
     * antes de correr rutina
     */
    public static function validateData($results)
    {
        Log::info('validando datos');
        $history = array();
        $history = self::validateIndex($results, $history);

        if (!count($history) > 0) {
            foreach($results as $key => $resul){

                //si los campos obligatorias están vacios se entiende que es el final
                //del archivo
                if (self::isFinal($resul)) break;

                //user data
                $data_user = [];
                $data_user['tipo_identificacion']   = self::getOptionIdentification($resul['tipo_identificacion']);
                $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGATORIO
                $data_user['primer_nombre']         = trim($resul['primer_nombre']); //OBLIGATORIO
                $data_user['segundo_nombre']        = trim($resul['segundo_nombre']); // NO OBLIGATORIO
                $data_user['primer_apellido']       = trim($resul['primer_apellido']); //OBLIGATORIO
                $data_user['segundo_apellido']      = trim($resul['segundo_apellido']);//NO SOBLIGATORIO
                $data_user['fecha_nacimiento']      = substr(trim($resul['fecha_de_nacimiento']),0,10); //OBLIGATORIO
                $data_user['telefono']              = trim($resul['telefono']); //NO OBLIGATORIO
                $data_user['celular']               = trim($resul['celular']); //OBLIGATORIO
                $data_user['correo_electronico']    = strtolower(trim($resul['correo_electronico'])); //OBLIGATORIO
                $data_user['sexo']                  = self::getGender($resul['genero']); //OBLIGATORIO
                $data_user['nivel_academico']       = self::getLevelAcademic($resul['nivel_academico']);//NO OBLIGATORIO

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

                if (!self::validateDate($data_user['fecha_nacimiento'])) {
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

                //Datos del usuario (entidad).
                $data_entity = [];
                $data_entity['tipo_entidad']                         = self::getEntity($resul['tipo_entidad']); // * (codigo_clase_empre_insti)
                $data_entity['tipo_identificacion_empresa']          = self::getIdentificationCompany($resul['tipo_identificacion_empresa']); // * (codigo_tipo_identificacion)
                $data_entity['numero_identificacion_empresa']        = trim($resul['numero_identificacion_empresa']); // *
                $data_entity['nombre_entidad']                       = trim($resul['nombre_entidad']); // * (nombre_usuario)
                $data_entity['telefono_empresa']                     = trim($resul['telefono_empresa']); // (telefono_usuario)
                $data_entity['numero_whatsapp_empresa']              = trim($resul['numero_whatsapp_empresa']); // (numero_whatsapp)
                $data_entity['fecha_inicio_operacion_empresa']       = substr(trim($resul['fecha_inicio_operacion_empresa']), 0, 10); // * (fecha_nacimiento)
                $data_entity['actividad_economica_ciuu_empresa']     = self::getSectorInteres($resul['actividad_economica_ciuu_empresa']);
                $data_entity['tipo_sociedad_empresa']                = self::getTypeSociety($resul['tipo_sociedad_empresa']); // * (codigo_tipo_sociedad)
                $data_entity['cubrimiento_empresa']                  = self::getEntityCoverage($resul['cubrimiento_empresa']); // * (cubrimiento_entidad)
                $data_entity['correo_contacto_empresa']              = trim($resul['correo_contacto_empresa']); // (correo_contacto)
                $data_entity['ciudad_entidad']                       = self::getIdOption(trim($resul['ciudad_entidad']), 4); // * (codigo_ciudad)
                $data_entity['industria_entidad']                    = self::getCategoryProject($resul['industria_entidad']); // * (codigo_categoria_proyecto) y proyecto
                $data_entity['etapa_entidad']                        = self::getPhase(trim($resul['etapa_entidad']), 2); // * (codigo_etapa_proyecto)
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

                        if (!self::validateDate($data_entity['fecha_inicio_operacion_empresa'])) {
                            array_push($history, "- La fila " . ($key + 2). " tiene fecha_inicio_operacion_empresa con tipo de dato incorrecto" . "\n");
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
                    array_push($history, "- La fila " . ($key + 2). " tiene ciudad_entidad vacío o el dato es incorrecto." . "\n");
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

        Storage::disk('local')->put('compensar/errores_cargue_entidades.txt', $content);

        \Log::info("Fin validacion datos");

        if (count($history) > 0) {
            // die;
            throw new \Exception("Hay errores en el excel");
        }

        unset($content, $history, $data_user, $data_entity);

        \Log::info("No hay errrores en la validacion");
        return;
    }

    /**
     * validacion de encabezados del excel
     */
    private static function validateIndex($results, $history)
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

    private static function isFinal($resul)
    {
        //si los campos obligatorias están vacios se entiende que es el final
        //del archivo
        if (
            $resul['tipo_identificacion'] == null
            && $resul['numero_identificacion'] == null
            && $resul['primer_nombre'] == null
            && $resul['primer_apellido'] == null
            && $resul['fecha_de_nacimiento'] == null
            && $resul['celular'] == null
            && $resul['correo_electronico'] == null
            && $resul['direccion'] == null
            && $resul['genero'] == null
        ) {
            \Log::info('isFinal');
            return true;
        }

        return false;
    }
}
