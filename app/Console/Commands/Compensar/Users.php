<?php

namespace App\Console\Commands\Compensar;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
  App\Models\Core\UsuarioInstitucion,
  App\Models\Core\Persona;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

use Illuminate\Console\Command;

class Users extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compensar:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
//        $sftp = new Filesystem(new SftpAdapter([
//          'host' => 'ftps.compensar.co',
//          'port' => 1015,
//          'username' => 'SVCFTPTUPROYECTO',
//          'password' => 'SVCFTPTUPROYECTO',
//          'timeout' => 10,
//        ]));

//      $ftp = Storage::createftpDriver([
//        'host'     => 'ftps.compensar.co',
//        'username' => 'SVCFTPTUPROYECTO',
//        'password' => 'Pr0y3ct0.2019',
//        'port'     => '1015',
          //‘root’     => ‘’,
//        'passive' => true,
//        'ssl'      => true,
//        'ignorePassiveAddress' => false,
//      ]);

      $filename = 'Archivos Emprendimiento/Emprendimiento_30072019.xlsx';
//      $filecontent = $ftp->get($filename);

        Storage::disk('ftp')->exists('Prueba');

//      dd($filecontent);

      Excel::load('', function($reader){
//        $results = $reader->get()->toArray();
//        foreach($results as $resul){
//          $mail_rep_legal = trim($resul['correo_electronico']);
//          $tipo_identificacion = trim($resul['tipo_identificacion']);
//          $codigo_tipo_identificacion = "";
//          if($tipo_identificacion == 'Cédula'){
//            $codigo_tipo_identificacion = 1;
//          }else if($tipo_identificacion == 'Permiso Especial de Permanecia'){
//            $codigo_tipo_identificacion = 9;
//          }else if($tipo_identificacion == 'Cédula Extranjeria'){
//            $codigo_tipo_identificacion = 2;
//          }
//          $documento_representante = trim($resul['numero_identificacion']);
//          $primer_nombre = trim($resul['primer_nombre']);
//          $segundo_nombre = trim($resul['segundo_nombre']);
//          $nombre_usuario = $primer_nombre." ".$segundo_nombre;
//          $primer_apellido_usuario = trim($resul['primer_apellido']);
//          $segundo_apellido_usuario = trim($resul['segundo_apellido']);
//          // $fecha_de_nacimiento = trim($resul['fecha_de_nacimiento']);
//          $telefono = trim($resul['telefono']);
//          $tel_rep_legal = trim($resul['celular']);
//          $direccion = trim($resul['direccion']);
//          $genero = trim($resul['genero']);
//          $codigo_genero = "";
//          if($genero == 'Másculino'){
//            $codigo_genero = 1;
//          }else if($genero == 'Femenino'){
//            $codigo_genero = 2;
//          }
//          // $nivel_académico = trim($resul['nivel_académico']);
//
//          if($mail_rep_legal != ""){
//            $codigo_usuario = "";
//            $codigo_usuario_institucion = "";
//            $usuario =$this->getEmailUsuario($mail_rep_legal);
//            if(!$usuario instanceof Usuario){
//              $newnickname = substr($this->unwantedCharacters($nombre_usuario), 0, 16);
//              $nickname_usuario = $this->validateUserNickname(str_replace(" ", "", $newnickname));
//
//              $nusuario = new Usuario;
//              $nusuario->nombre_usuario = $nombre_usuario;
//              $nusuario->nickname_usuario = $nickname_usuario;
//              $nusuario->correo_electronico = $mail_rep_legal;
//              if($tel_rep_legal!="")
//                $nusuario->movil_usuario = $tel_rep_legal;
//              if($codigo_tipo_identificacion!="")
//                $nusuario->tipo_identificacion_usuario = $codigo_tipo_identificacion;
//              if($documento_representante!="")
//                $nusuario->identificacion_usuario = $documento_representante;
//              $nusuario->codigo_estado = 8;
//              // $nusuario->codigo_estado = 8; revisar genero
//              $nusuario->save();
//
//              $codigo_usuario = $nusuario->codigo_usuario;
//
//              $nusuario_institucion = new UsuarioInstitucion;
//              $nusuario_institucion->codigo_usuario = $codigo_usuario;
//              $nusuario_institucion->codigo_institucion = 30;
//              $nusuario_institucion->codigo_estado = 14;
//              $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
//              $nusuario_institucion->hora_registro_usuario = date('H:m:i');
//              $nusuario_institucion->save();
//
//              $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;
//
//              $npersona = new Persona;
//              $npersona->codigo_usuario = $codigo_usuario;
//              if($primer_apellido_usuario!="")
//                $npersona->primer_apellido_persona = $primer_apellido_usuario;
//              if($segundo_apellido_usuario!="")
//                $npersona->segundo_apellido_persona = $segundo_apellido_usuario;
//              $npersona->save();
//
//              $this->info("Creo el usuario: ".$mail_rep_legal."\n");
//            }
//            else{
//              $codigo_usuario = $usuario->codigo_usuario;
//
//              if($tel_rep_legal!="")
//                $usuario->movil_usuario = $tel_rep_legal;
//
//              if($documento_representante!="")
//                $usuario->identificacion_usuario = $documento_representante;
//              $usuario->save();
//
//              $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, 51);
//              if(!$usuario_institucion instanceof UsuarioInstitucion){
//                $nusuario_institucion = new UsuarioInstitucion;
//                $nusuario_institucion->codigo_usuario = $codigo_usuario;
//                $nusuario_institucion->codigo_institucion = 51;
//                $nusuario_institucion->codigo_estado = 14;
//                $nusuario_institucion->codigo_rol = 3;
//                $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
//                $nusuario_institucion->hora_registro_usuario = date('H:m:i');
//                $nusuario_institucion->save();
//                $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;
//              }
//              else{
//                $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
//              }
//              $this->info("Edito el usuario: ".$mail_rep_legal."\n");
//            }
//          }
//        }
      });

      $this->info("Finalizo");
    }


  public static function unwantedCharacters($string)
  {
    $string = trim($string);
    // Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
    $string = strtolower($string);
    // Strip accents
    $string = strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    // Strip any unwanted characters
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    return $string;
  }

  public static function getEmailUsuario($email) {
    // return Usuario::where('correo_electronico', 'ilike',  "%".trim($email)."%") -> first();
    return Usuario::where('correo_electronico', '=',  trim($email)) -> first();
  }

  public function validateUserNickname($nombre_categoria, $contador = 0)
  {
    if(isset($nombre_categoria) && !empty($nombre_categoria)){

      $url_categoria_producto = $contador > 0 ? (strlen($nombre_categoria) >= 16) ? sprintf("%s%d",substr($nombre_categoria,0,-strlen($contador)),$contador) : sprintf("%s%d",$nombre_categoria, $contador) : $nombre_categoria;

      $query = Usuario::query();
      $query->select('usuario.*');
      $query->whereRaw("trim(lower(nickname_usuario)) ilike trim(lower('$url_categoria_producto'))");
      $categoria_producto = $query->first();

      $contador ++;
      if($categoria_producto instanceof Usuario){
        $url_categoria_producto = $this->validateUserNickname($nombre_categoria, $contador);
      }
      return $url_categoria_producto;
    }
    return str_random(16);
  }

  public static function getusuarioInstitucion($codigo_usuario,$codigo_institucion){
    return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
      ->where('codigo_institucion', '=', $codigo_institucion)
      ->first();
  }

}
