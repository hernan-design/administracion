<?php

namespace App\Imports\Compensar;

use App\Imports\UsersCompensar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Persona,
    App\Models\Param\Institucion;

use App\Console\Commands\LoadUsers\CargaUsuarios;
use App\Imports\Utilities;

class LoadUsers extends Utilities
{
    private const ID_INSTITUTION     = 30;
    private const ID_ROL_INSTITUTION = 53;
    private const ID_ROL_ASSOCIATED  = 39;
    private const ID_ROL_USER        = 3;

    public static function upload()
    {
        \Log::info('leyendo');
        $filePath = 'compensar/cargue_usuarios_compensar.xlsx';
        $import = new UsersCompensar();
        Excel::import($import, Storage::disk('local')->path($filePath));
        $history = $import->isFinalReached();
        array_push($history, "Finalizó");
          $data = [];
      $data['_inst'] = self::ID_INSTITUTION;
      $data['_to'] = 'mcarriong@compensar.com';
      $data['to_copy'] = ['administracion@tuproyecto.com', 'testertuproyecto@gmail.com'];
      $data['_to_name'] = 'Fomento Empresarial';
      $data['_subject'] = "Carga de datos";
      $data['_template'] = 'emails.user.notificationdata';
      $data['_history'] = $history;

      SendEmail::notification($data);
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

            }
        }

        $content = implode($history);

        Storage::disk('local')->put('compensar/errores_archivo.txt', $content);

        \Log::info("Fin validacion datos");

        if (count($history) > 0) {
            // die;
            throw new \Exception("Hay errores en el excel");
        }

        unset($content, $history, $data_user);

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

            break;
        }

        return $history;
    }

    private static function isFinal($resul)
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
