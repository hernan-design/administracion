<?php
namespace App\Imports;

use App\Console\Commands\LoadUsers\CargaUsuarios;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use SendEmail, Lang, Storage, Excel, DB;
use App\Models\Seguridad\Usuario,
App\Models\Seguridad\RolUsuario,
App\Models\Core\UsuarioInstitucion,
App\Models\Core\Persona,
App\Models\Param\Institucion;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use App\Imports\Utilities;

class UsersCompensar extends Utilities implements ToModel, WithHeadingRow
{

  public $history = [];
  private const ID_INSTITUTION = 30;
  private const ID_ROL_INSTITUTION = 53;
  private const ID_ROL_ASSOCIATED = 39;
  private const ID_ROL_USER = 3;
  public function model(array $row)
  {
    \Log::info('leyendo fila');
    // $history = array();
    // Validar datos
    $results = [$row]; // Convertir la fila en un array para que coincida con tu lógica actual
    self::validateData($results);
    foreach ($results as $resul) {

      //si los campos obligatorias están vacios se entiende que es el final
      //del archivo
      if (self::isFinal($resul))
        break;
      //user data
      $data_user = [];
      $data_user['tipo_identificacion'] = self::getOptionIdentification($resul['tipo_identificacion']);
      $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGATORIO
      $data_user['nombre_usuario'] = trim($resul['primer_nombre']) . ' ' . trim($resul['segundo_nombre']);
      $data_user['primer_apellido'] = trim($resul['primer_apellido']); //OBLIGATORIO
      $data_user['segundo_apellido'] = trim($resul['segundo_apellido']); //NO SOBLIGATORIO
      $data_user['fecha_nacimiento'] = substr(trim(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($resul['fecha_de_nacimiento'])->format('Y-m-d')), 0, 10); //OBLIGATORIO
      $data_user['telefono'] = trim($resul['telefono']); //NO OBLIGATORIO
      $data_user['movil'] = trim($resul['celular']); //OBLIGATORIO
      $data_user['correo_electronico'] = strtolower(trim($resul['correo_electronico'])); //NO OBLIGATORIO
      $data_user['direccion_usuario'] = strtolower(trim($resul['direccion'])); //OBLIGATORIO
      $data_user['sexo'] = self::getGender($resul['genero']); //OBLIGATORIO
      $data_user['nivel_academico'] = self::getLevelAcademic($resul['nivel_academico']); //NO OBLIGATORIO
      $data_user['ciudad_nacimiento'] = 2257; //Bogotá por fecto, necesario para que no redirija al step/perfil al iniciar sesión
      $data_user['id_institucion'] = self::ID_INSTITUTION;
      $data_user['id_rol_institucion'] = self::ID_ROL_INSTITUTION;
      $data_user['id_rol_associated'] = self::ID_ROL_ASSOCIATED;


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
        "ciudad_nacimiento" => true,
        "usuario_institucion" => true,
      ];

      $user = self::getUser($data_user['correo_electronico'], $data_user['numero_identificacion']);
      if ((!$user instanceof Usuario)) {
        $user = CargaUsuarios::store($data_user, $data_require);

        // Send notification
        $data = [];
        $data['_inst'] = self::ID_INSTITUTION;
        $data['_to'] = $data_user['correo_electronico'];
        $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
        $data['_subject'] = "Confirmación de registro plataforma";
        $data['_template'] = 'emails.user.restorepassword';
        $data['_change_password'] = "/password/email";
        $data['_current_password'] = $user->password;
        SendEmail::notification($data);

        array_push($this->history, "Creo el usuario: " . $data_user['correo_electronico'] . "\n");
        Log::info("Creo el usuario: " . $data_user['correo_electronico'] . "\n");
      } else {

        if ($data_user['telefono'] != "")
          $user->telefono_usuario = $data_user['telefono'];
        if ($data_user['tipo_identificacion'] != "")
          $user->tipo_identificacion_usuario = $data_user['tipo_identificacion'];
        if ($data_user['movil'] != "")
          $user->movil_usuario = $data_user['movil'];
        if ($data_user['numero_identificacion'] != "")
          $user->identificacion_usuario = $data_user['numero_identificacion'];
        if ($data_user['direccion_usuario'] != "")
          $user->direccion_usuario = $data_user['direccion_usuario'];

        $user->save();

        $usuario_institucion = self::getUsuarioInstitucion($user->codigo_usuario, self::ID_INSTITUTION);
        if (!$usuario_institucion instanceof UsuarioInstitucion) {
          $usuarioIdentificacion = self::getIdentificationUser($data_user['numero_identificacion'], self::ID_INSTITUTION);
          if (!$usuarioIdentificacion instanceof Usuario) {
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
            if (!$userRol instanceof RolUsuario) {
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
            if (!$userRolAsociado instanceof RolUsuario) {
              $userRolAsociado = new RolUsuario;
              $userRolAsociado->role_id = self::ID_ROL_ASSOCIATED;
              $userRolAsociado->user_id = $user->codigo_usuario;
              $userRolAsociado->codigo_institucion = self::ID_INSTITUTION;
              $userRolAsociado->save();
            }

            // Send notification
            $data = [];
            $data['_inst'] = self::ID_INSTITUTION;
            $data['_to'] = $data_user['correo_electronico'];
            $data['_to_name'] = ucwords(strtolower($data_user['nombre_usuario']));
            $data['_subject'] = "Confirmación de registro plataforma";
            $data['_template'] = 'emails.user.restorepassword';
            $data['_change_password'] = "/password/email";

            SendEmail::notification($data);
            array_push($this->history, "Se crea el usuario en la institucion: " . $data_user['correo_electronico'] . "\n");
            \Log::info("Se crea el usuario en la institucion: " . $data_user['correo_electronico'] . "\n");
          } else {
            array_push($this->history, "Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado en la institucion, se actualiza. \n");
            \Log::info("Usuario con codigo de identificacion : " . $data_user['numero_identificacion'] . " ya se encuentra registrado  en la institucion\n");
            continue;
          }
        } else {
          array_push($this->history, "Usuario ya existe, se actualiza: " . $data_user['correo_electronico'] . "\n");
          \Log::info("Usuario ya existe, se actualiza: " . $data_user['correo_electronico'] . "\n");
          continue;
        }
      }

      //Aceptar terminos y condiciones
      // CargaUsuarios::acceptTerms($user, self::ID_INSTITUTION);

      // Devuelve null para continuar con la siguiente fila
      // return null;
    }
    // if ($final){

      // array_push($history, "Finalizo");
      // // dd($history);
      // // Send notification
      // $data = [];
      // $data['_inst'] = self::ID_INSTITUTION;
      // $data['_to'] = 'mcarriong@compensar.com';
      // $data['to_copy'] = ['administracion@tuproyecto.com', 'testertuproyecto@gmail.com'];
      // $data['_to_name'] = 'Fomento Empresarial';
      // $data['_subject'] = "Carga de datos";
      // $data['_template'] = 'emails.user.notificationdata';
      // $data['_history'] = $history;

      // SendEmail::notification($data);
    // }

    // return null;
  }

  public function isFinalReached()
    {
        return $this->history;
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
      foreach ($results as $key => $resul) {
        //si los campos obligatorias están vacios se entiende que es el final
        //del archivo
        if (self::isFinal($resul))
          break;

        //user data
        $data_user = [];
        $data_user['tipo_identificacion'] = self::getOptionIdentification($resul['tipo_identificacion']);
        $data_user['numero_identificacion'] = trim($resul['numero_identificacion']); //OBLIGATORIO
        $data_user['primer_nombre'] = trim($resul['primer_nombre']); //OBLIGATORIO
        $data_user['segundo_nombre'] = trim($resul['segundo_nombre']); // NO OBLIGATORIO
        $data_user['primer_apellido'] = trim($resul['primer_apellido']); //OBLIGATORIO
        $data_user['segundo_apellido'] = trim($resul['segundo_apellido']); //NO SOBLIGATORIO
        $data_user['fecha_nacimiento'] = trim(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($resul['fecha_de_nacimiento'])->format('Y-m-d')); //OBLIGATORIO
        $data_user['telefono'] = trim($resul['telefono']); //NO OBLIGATORIO
        $data_user['celular'] = trim($resul['celular']); //OBLIGATORIO
        $data_user['correo_electronico'] = strtolower(trim($resul['correo_electronico'])); //OBLIGATORIO
        $data_user['sexo'] = self::getGender($resul['genero']); //OBLIGATORIO
        $data_user['nivel_academico'] = self::getLevelAcademic($resul['nivel_academico']); //NO OBLIGATORIO

        if (is_null($data_user['tipo_identificacion'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene tipo_identificacion incompleto" . "\n");
        }

        if (empty($data_user['numero_identificacion'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene numero_identificacion incompleto" . "\n");
        }

        if (empty($data_user['primer_nombre'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene primer_nombre incompleto" . "\n");
        }

        if (empty($data_user['primer_apellido'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene primer_apellido incompleto" . "\n");
        }

        if (!self::validateDate($data_user['fecha_nacimiento'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene fecha_nacimiento incorrecta" . "\n");
        }

        if (empty($data_user['celular'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene celular incompleto" . "\n");
        }

        if (!filter_var($data_user['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
          array_push($history, "- La fila " . ($key + 2) . " tiene correo incorecto" . "\n");
        }


        if (is_null($data_user['sexo'])) {
          array_push($history, "- La fila " . ($key + 2) . " tiene sexo incorrecto" . "\n");
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
      if (!array_key_exists('tipo_identificacion', $resul))
        array_push($history, "- No está definido el encabezado tipo_identificacion" . "\n");

      if (!array_key_exists('numero_identificacion', $resul))
        array_push($history, "- No está definido el encabezado numero_identificacion" . "\n");

      if (!array_key_exists('primer_nombre', $resul))
        array_push($history, "- No está definido el encabezado primer_nombre" . "\n");

      if (!array_key_exists('segundo_nombre', $resul))
        array_push($history, "- No está definido el encabezado segundo_nombre" . "\n");

      if (!array_key_exists('primer_apellido', $resul))
        array_push($history, "- No está definido el encabezado primer_apellido" . "\n");

      if (!array_key_exists('segundo_apellido', $resul))
        array_push($history, "- No está definido el encabezado segundo_apellido" . "\n");

      if (!array_key_exists('fecha_de_nacimiento', $resul))
        array_push($history, "- No está definido el encabezado fecha_de_nacimiento" . "\n");

      if (!array_key_exists('telefono', $resul))
        array_push($history, "- No está definido el encabezado telefono" . "\n");

      if (!array_key_exists('celular', $resul))
        array_push($history, "- No está definido el encabezado celular" . "\n");

      if (!array_key_exists('correo_electronico', $resul))
        array_push($history, "- No está definido el encabezado correo_electronico" . "\n");

      if (!array_key_exists('direccion', $resul))
        array_push($history, "- No está definido el encabezado direccion" . "\n");

      if (!array_key_exists('genero', $resul))
        array_push($history, "- No está definido el encabezado genero" . "\n");

      if (!array_key_exists('nivel_academico', $resul))
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
      $resul['tipo_identificacion'] == null
      && $resul['numero_identificacion'] == null
      && $resul['primer_nombre'] == null
      && $resul['primer_apellido'] == null
      && $resul['fecha_de_nacimiento'] == null
      && $resul['celular'] == null
      && $resul['correo_electronico'] == null
      && $resul['genero'] == null
    ) {
      \Log::info('isFinal');
      return true;
    }

    return false;
  }
}
