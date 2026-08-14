<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;
use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
App\Models\Core\UsuarioInstitucion,
App\Models\Core\Persona,
App\Models\Param\Institucion,
App\Models\Core\Estado,
App\Models\Core\Ciudad,
App\Models\Core\Entidad,
App\Models\Core\DatoEntidad,
App\Models\Core\UsuarioEncuesta,
App\Models\Core\CategoriaProducto,
App\Models\Core\CategoriaProductoInsti,
App\Models\Core\Evento,
App\Models\Core\EventoInstitucion,
App\Models\Core\Convocatoria,
App\Models\Core\ConvocatoriaInstitucion,
App\Models\Core\CategoriaProceso,
App\Models\Core\CatContentEvento,
App\Models\Core\EtapaContEvent,
App\Models\Core\ConvocatoriaCategoria,
App\Models\Core\ConvocatoriaEtapa,
App\Models\Core\RespuestaEncuesta,
App\Models\Core\RegistroFaseUsuario;

class LoadExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:loadexcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load excel data';

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
        Excel::load('radicados.xlsx', function($reader){
            $results = $reader->get()->toArray();
            foreach($results as $resul){
                $postulacion = trim($resul['numero']);
                $radicado = trim($resul['radicado']);

                if($postulacion!="" && $radicado!=""){                    
                    $registor_fase_usuario = RegistroFaseUsuario::join('core.registro_convocatoria','registro_convocatoria.codigo_registro_convocatoria','=','registro_fase_usuario.codigo_registro_convocatoria')->where('registro_convocatoria.numero_registro_convocatoria',$postulacion)->where('codigo_fase',249)->first();
                    if($registor_fase_usuario instanceof RegistroFaseUsuario){
                        \Log::info("Numero: ".$postulacion." -> ".$radicado." -> ".$registor_fase_usuario->codigo_registro_fase_usuario);
                        $registor_fase_usuario->numero_radicado = $radicado;
                        $registor_fase_usuario->save();
                    } else {
                        dd("error con el numero ".$postulacion);
                    }
                    
                }                
                
            }
        });
        \Log::info("Finalizo");
    }

    public function loadEvents() {
      Excel::load('eventos.xlsx', function($reader){
        $results = $reader->get()->toArray();
        foreach($results as $resul){
          $nombre = trim($resul['nombre']);
          $fecha_inicio = trim($resul['fecha_inicio']);
          $hora_inicio = trim($resul['hora_inicio']);
          $fecha_fin = trim($resul['fecha_fin']);
          $hora_fin = trim($resul['hora_fin']);
          $lugar = trim($resul['lugar']);
          $url_organizador_evento = trim($resul['url_evento']);
          $pago = trim($resul['pago']);
          $ciudad = trim($resul['ciudad']);
          $resumen = trim($resul['resumen']);
          $url_imagen = trim($resul['url_imagen']);
          $descripcio_evento = trim($resul['descripcio_evento']);
          $entidad_nombre = trim($resul['entidad_nombre']);
          $entidad_descripcion = trim($resul['entidad_descripcion']);
          $entidad_identificacion = trim($resul['entidad_identificacion']);
          $imagen_entidad = "https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/svg/no-image-770x460.svg";
          $entidad_ciudad = trim($resul['entidad_ciudad']);
          $etapa_asociada = trim($resul['etapa_asociada']);
          $codigo_etapa_evanto = "";
          if($etapa_asociada=='Arranque'){
            $codigo_etapa_evanto = 57;
          }else if($etapa_asociada=='Crecimiento'){
            $codigo_etapa_evanto = 58;
          }else if($etapa_asociada=='Ideación'){
            $codigo_etapa_evanto = 56;
          }else if($etapa_asociada=='Consolidación'){
            $codigo_etapa_evanto = 67;
          }else if($etapa_asociada=='consolidación'){
            $codigo_etapa_evanto = 67;
          }
          $categoria_1 = trim($resul['categoria_1']);
          $categoria_2 = trim($resul['categoria_2']);

          \Log::info("Evento: ".$nombre."\n");

          $codigo_ciudad = 2258;
          $codigo_usuario = 296057;
          $tipo_usuario = 2;

          // \Log::info("Registro entidad: ".$nombre."\n");

          ######## Creacion de entidad ######

          $razon_social = $entidad_nombre;
          $empresa = $this->getUserEntityByName($razon_social, $tipo_usuario);

          if(!$empresa instanceof Usuario){
            $newnickname_entidad = substr($this->unwantedCharacters($razon_social), 0, 16);
            $nickname_entidad = $this->validateUserNickname(str_replace(" ", "", $newnickname_entidad));

            $nusuario_entidad = new Usuario;
            $nusuario_entidad->nombre_usuario = $razon_social;
            $nusuario_entidad->nickname_usuario = $nickname_entidad;
            if($entidad_identificacion)
              $nusuario_entidad->identificacion_usuario = $entidad_identificacion;
            $nusuario_entidad->tipo_usuario = 2;
            $nusuario_entidad->codigo_estado = 8;
            if($codigo_ciudad)
              $nusuario_entidad->codigo_ciudad = $codigo_ciudad;
            if($entidad_descripcion)
              $nusuario_entidad->biografia_usuario = $entidad_descripcion;
            $nusuario_entidad->imagen_usuario = $imagen_entidad;
            $nusuario_entidad->save();

            $codigo_usuario_entidad = $nusuario_entidad->codigo_usuario;

            $nusuario_institucion_entidad = new UsuarioInstitucion;
            $nusuario_institucion_entidad->codigo_usuario = $codigo_usuario_entidad;
            $nusuario_institucion_entidad->codigo_institucion = 69;
            $nusuario_institucion_entidad->codigo_estado = 8;
            $nusuario_institucion_entidad->save();

            $codigo_usuario_institucion_entidad = $nusuario_institucion_entidad->codigo_usuario_institucion;

            $ndato_entidad = new DatoEntidad;
            $ndato_entidad->codigo_usuario_institucion = $codigo_usuario_institucion_entidad;
            $ndato_entidad->codigo_privacidad_contenido = 2;
            $ndato_entidad->codigo_estado = 8;
            $ndato_entidad->save();

            $nentidad = new Entidad;
            $nentidad->codigo_usuario = $codigo_usuario_entidad;
            $nentidad->seg_codigo_usuario = $codigo_usuario;
            $nentidad->codigo_clase_empre_insti = 31;
            $nentidad->save();

            $codigo_prestador_servicio = $codigo_usuario_entidad;
            \Log::info("Registro entidad: ".$codigo_prestador_servicio."\n");
          }
          else{
            $codigo_prestador_servicio = $empresa->codigo_usuario;
            if($empresa->imagen_usuario == "")
              $empresa->imagen_usuario = $imagen_entidad;
            $empresa->codigo_ciudad = $codigo_ciudad;
            $empresa->save();

            $usuario_institucion = UsuarioInstitucion::where('codigo_usuario',$codigo_prestador_servicio)->where('codigo_institucion',69)->first();
            if($usuario_institucion instanceof UsuarioInstitucion){

            }
            else{
              $nusuario_institucion_entidad = new UsuarioInstitucion;
              $nusuario_institucion_entidad->codigo_usuario = $codigo_prestador_servicio;
              $nusuario_institucion_entidad->codigo_institucion = 69;
              $nusuario_institucion_entidad->codigo_estado = 8;
              $nusuario_institucion_entidad->save();
            }

            $entidad = Entidad::where('codigo_usuario',$codigo_prestador_servicio)->first();
            if($entidad instanceof Entidad){

            }
            else{
              $entidad = new Entidad;
              $entidad->codigo_usuario = $codigo_prestador_servicio;
              $entidad->seg_codigo_usuario = $codigo_usuario;
              $entidad->codigo_clase_empre_insti = 2;
              $entidad->save();
            }
            \Log::info("Actualizo entidad: ".$codigo_prestador_servicio."\n");
          }

          #Creacion de eventos

          $evento = new Evento;

          $url_evento = slugtp($nombre, $evento->getTable(), 'url_evento');

          $evento->codigo_usuario = $codigo_usuario;
          $evento->codigo_estado = 8;
          $evento->nombre_evento = $nombre;
          $evento->descripcion_evento = $descripcio_evento;
          $evento->fecha_evento = $fecha_inicio;
          $evento->privacidad_evento = 2;
          $evento->lugar_evento = $lugar;
          $evento->hora_inicio_evento = $hora_inicio;
          $evento->hora_fin_evento = $hora_fin;
          $evento->codigo_calendario = 20;
          $evento->fecha_fin_evento = $fecha_fin;
          $evento->codigo_ciudad = $codigo_ciudad;
          $evento->url_evento = $url_evento;
          $evento->url_evento_organizador = $url_organizador_evento;
          $evento->codigo_privacidad_contenido = 2;
          $evento->resumen_evento = $resumen;
          // $evento->imagen_evento = $url_imagen;
          $evento->sync_algolia = 1;
          if($pago!="")
            $evento->pago_evento = $pago;
          $evento->seg_codigo_usuario = $codigo_prestador_servicio;
          // if($codigo_eje)
          //     $evento->codigo_eje = $codigo_eje;
          $evento->save();

          // \Log::info("Registro convocatoria: ".$nombre."\n");

          $codigo_evento = $evento->codigo_cevento;

          $evento_institucion = new EventoInstitucion;
          $evento_institucion->codigo_evento = $codigo_evento;
          $evento_institucion->codigo_institucion = 69;
          $evento_institucion->codigo_estado = 8;
          $evento_institucion->save();

          // \Log::info("Registro convocatoria institucion: ".$codigo_convcocatoria."\n");

          $categoria_evento1 = CategoriaContenido::where('nombre_categoria_contenido', 'ilike', "%".$categoria_1."%")->first();
          if($categoria_evento1 instanceof CategoriaContenido){
            $codigo_categoria1 = $categoria_evento1->codigo_categoria_contenido;

            $evento_categoria = new CatContentEvento;
            $evento_categoria->codigo_evento = $codigo_convcocatoria;
            $evento_categoria->codigo_categoria_contenido = $codigo_categoria1;
            $evento_categoria->codigo_estado = 8;
            $evento_categoria->save();
          }

          $categoria_evento2 = CategoriaContenido::where('nombre_categoria_contenido', 'ilike', "%".$categoria_2."%")->first();
          if($categoria_evento2 instanceof CategoriaContenido){
            $codigo_categoria2 = $categoria_evento2->codigo_categoria_contenido;

            $evento_categoria = new CatContentEvento;
            $evento_categoria->codigo_evento = $codigo_convcocatoria;
            $evento_categoria->codigo_categoria_contenido = $codigo_categoria2;
            $evento_categoria->codigo_estado = 8;
            $evento_categoria->save();
          }

          if($codigo_etapa_evanto != ""){
            $evento_etapa = new EtapaContEvent;
            $evento_etapa->codigo_evento = $convocatoria->codigo_convocatoria;
            $evento_etapa->codigo_etapa_contenido = $codigo_etapa_evanto;
            $evento_etapa->codigo_estado = 8;
            $evento_etapa->save();
          }
        }
      });
      \Log::info("Finalizo");
    }

    

    public static function loadPrograms(){
        Excel::load('programas.xlsx', function($reader){
            $results = $reader->get()->toArray();
		    foreach($results as $resul){

                // Programa
                $nombre = trim($resul['nombre']);
                $interna_externa = trim($resul['interna_externa']);
                $permanente = trim($resul['permanente']);
                $fecha_cierre = trim($resul['fecha_cierre']);
                $etapa_asociada = trim($resul['etapa_asociada']);
                $codigo_etapa_proyecto = "";
                if($etapa_asociada=='Arranque'){
                    $codigo_etapa_proyecto = 57;
                }else if($etapa_asociada=='Crecimiento'){
                    $codigo_etapa_proyecto = 58;
                }else if($etapa_asociada=='Ideación'){
                    $codigo_etapa_proyecto = 56;
                }else if($etapa_asociada=='Consolidación'){
                    $codigo_etapa_proyecto = 67;
                }else if($etapa_asociada=='consolidación'){
                    $codigo_etapa_proyecto = 67;
                }
                $eje = trim($resul['eje']);
                $codigo_eje = "";
                if($eje=='Operativo'){
                    $codigo_eje = 3;
                }else if($eje=='Táctica'){
                    $codigo_eje = 2;
                }else if($eje=='Estrategia'){
                    $codigo_eje = 1;
                }
                $resumen = trim($resul['resumen']);
                $url_imagen = trim($resul['url_imagen']);
                $descripcion = trim($resul['descripcion']);
                $terminos_condiciones = trim($resul['terminos_condiciones']);
                $categoria_1 = trim($resul['categoria_1']);
                $categoria_2 = trim($resul['categoria_2']);
                $url_programa = trim($resul['url_programa']);

                // Entidad
                $entidad_nombre = trim($resul['entidad_nombre']);
                $entidad_descripcion = trim($resul['entidad_descripcion']);
                $entidad_identificacion = trim($resul['entidad_identificacion']);
                $imagen_entidad = "https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/svg/no-image-770x460.svg";
                $entidad_ciudad = trim($resul['entidad_ciudad']);
                $datos_ciudad = $this->getCityByName($entidad_ciudad);
		    	// if($datos_ciudad instanceof Ciudad){
                //     $codigo_ciudad = $datos_ciudad->codigo_ciudad;
		    	// }
		    	// else{
		    	// 	echo "La ciudad ".$ciudad."No existe \n";
		    	// 	exit();
		    	// }
                $codigo_ciudad = 2258;
                $codigo_usuario = 296057;
                $tipo_usuario = 2;

                // \Log::info("Registro entidad: ".$nombre."\n");

                ######## Creacion de entidad ######

                $razon_social = $entidad_nombre;
                $empresa = $this->getUserEntityByName($razon_social, $tipo_usuario);

                if(!$empresa instanceof Usuario){
                    $newnickname_entidad = substr($this->unwantedCharacters($razon_social), 0, 16);
                    $nickname_entidad = $this->validateUserNickname(str_replace(" ", "", $newnickname_entidad));

                    $nusuario_entidad = new Usuario;
                    $nusuario_entidad->nombre_usuario = $razon_social;
                    $nusuario_entidad->nickname_usuario = $nickname_entidad;
                    if($entidad_identificacion)
                        $nusuario_entidad->identificacion_usuario = $entidad_identificacion;
                    $nusuario_entidad->tipo_usuario = 2;
                    $nusuario_entidad->codigo_estado = 8;
                    if($codigo_ciudad)
                        $nusuario_entidad->codigo_ciudad = $codigo_ciudad;
                    if($entidad_descripcion)
                        $nusuario_entidad->biografia_usuario = $entidad_descripcion;
                    $nusuario_entidad->imagen_usuario = $imagen_entidad;
                    $nusuario_entidad->save();

                    $codigo_usuario_entidad = $nusuario_entidad->codigo_usuario;

                    $nusuario_institucion_entidad = new UsuarioInstitucion;
                    $nusuario_institucion_entidad->codigo_usuario = $codigo_usuario_entidad;
                    $nusuario_institucion_entidad->codigo_institucion = 69;
                    $nusuario_institucion_entidad->codigo_estado = 8;
                    $nusuario_institucion_entidad->save();

                    $codigo_usuario_institucion_entidad = $nusuario_institucion_entidad->codigo_usuario_institucion;

                    $ndato_entidad = new DatoEntidad;
                    $ndato_entidad->codigo_usuario_institucion = $codigo_usuario_institucion_entidad;
                    $ndato_entidad->codigo_privacidad_contenido = 2;
                    $ndato_entidad->codigo_estado = 8;
                    $ndato_entidad->save();

                    $nentidad = new Entidad;
                    $nentidad->codigo_usuario = $codigo_usuario_entidad;
                    $nentidad->seg_codigo_usuario = $codigo_usuario;
                    $nentidad->codigo_clase_empre_insti = 31;
                    $nentidad->save();

                    $codigo_prestador_servicio = $codigo_usuario_entidad;
                    \Log::info("Registro entidad: ".$codigo_prestador_servicio."\n");
                }
                else{
                    $codigo_prestador_servicio = $empresa->codigo_usuario;
                    if($empresa->imagen_usuario == "")
                        $empresa->imagen_usuario = $imagen_entidad;
                    $empresa->codigo_ciudad = $codigo_ciudad;
                    $empresa->save();

                    $usuario_institucion = UsuarioInstitucion::where('codigo_usuario',$codigo_prestador_servicio)->where('codigo_institucion',69)->first();
                    if($usuario_institucion instanceof UsuarioInstitucion){

                    }
                    else{
                        $nusuario_institucion_entidad = new UsuarioInstitucion;
                        $nusuario_institucion_entidad->codigo_usuario = $codigo_prestador_servicio;
                        $nusuario_institucion_entidad->codigo_institucion = 69;
                        $nusuario_institucion_entidad->codigo_estado = 8;
                        $nusuario_institucion_entidad->save();
                    }

                    $entidad = Entidad::where('codigo_usuario',$codigo_prestador_servicio)->first();
                    if($entidad instanceof Entidad){

                    }
                    else{
                        $entidad = new Entidad;
                        $entidad->codigo_usuario = $codigo_prestador_servicio;
                        $entidad->seg_codigo_usuario = $codigo_usuario;
                        $entidad->codigo_clase_empre_insti = 2;
                        $entidad->save();
                    }
                    \Log::info("Actualizo entidad: ".$codigo_prestador_servicio."\n");
                }

                // creacion de convocatoria

                $convocatoria = Convocatoria::where('nombre_convocatoria','=',$nombre)->where('codigo_institucion','=',69)->first();
                if($convocatoria instanceof Convocatoria){
                    if($convocatoria->codigo_convocatoria){
                        // $codigo_etapa_proyecto = "";

                        // if($codigo_etapa_proyecto != ""){
                        //     $convocatoria_etapa = new ConvocatoriaEtapa;
                        //     $convocatoria_etapa->codigo_convocatoria = $convocatoria->codigo_convocatoria;
                        //     $convocatoria_etapa->codigo_etapa_proyecto = $codigo_etapa_proyecto;
                        //     $convocatoria_etapa->codigo_estado = 8;
                        //     $convocatoria_etapa->save();
                        // }

                        $convocatoria->sync_algolia = 1;
                        $convocatoria->url_externa_convocatoria = $url_programa;
                        $convocatoria->save();

                        \Log::info("Actualizo: ".$url_programa."\n");
                    }
                }else{
                    \Log::info("Registro: ".$nombre."\n");
                    // $convocatoria = new Convocatoria;

                    // $url_entidad = slugtp($nombre, $convocatoria->getTable(), 'url_convocatoria');

                    // $convocatoria->codigo_usuario = $codigo_usuario;
                    // $convocatoria->codigo_estado = 8;
                    // $convocatoria->nombre_convocatoria = $nombre;
                    // $convocatoria->fecha_creacion_convocatoria = date('Y-m-d');
                    // $convocatoria->descripcion_convocatoria = $descripcion;
                    // $convocatoria->hora_creacion_convocatoria = date('H:m:i');
                    // $convocatoria->terminos_convocatoria = $terminos_condiciones;
                    // $convocatoria->imagen_convocatoria = $url_imagen;
                    // $convocatoria->url_convocatoria = $url_entidad;
                    // $convocatoria->codigo_orientacion_convocatoria = 1;
                    // $convocatoria->url_externa_convocatoria = $url_programa;
                    // $convocatoria->tipo_convocatoria = 2;
                    // $convocatoria->codigo_privacidad_contenido = 2;
                    // $convocatoria->resumen_convocatoria = $resumen;
                    // $convocatoria->codigo_institucion = 69;
                    // $convocatoria->texto_boton = 'Participar';
                    // $convocatoria->permanente_convocatoria = true;
                    // $convocatoria->seg_codigo_usuario = $codigo_prestador_servicio;
                    // $convocatoria->fecha_cierre_convocatoria = '2019-12-31';
                    // $convocatoria->sync_algolia = 1;
                    // if($codigo_eje)
                    //     $convocatoria->codigo_eje = $codigo_eje;
                    // $convocatoria->save();

                    // \Log::info("Registro convocatoria: ".$nombre."\n");

                    // $codigo_convcocatoria = $convocatoria->codigo_convocatoria;

                    // $convocatoria_institucion = new ConvocatoriaInstitucion;
                    // $convocatoria_institucion->codigo_convocatoria = $codigo_convcocatoria;
                    // $convocatoria_institucion->codigo_institucion = 69;
                    // $convocatoria_institucion->codigo_estado = 8;
                    // $convocatoria_institucion->save();

                    // \Log::info("Registro convocatoria institucion: ".$codigo_convcocatoria."\n");

                    // $categoria_proceso1 = CategoriaProceso::where('nombre_categoria_proceso', 'ilike', "%".$categoria_1."%")->first();
                    // if($categoria_proceso1 instanceof CategoriaProceso){
                    //     $codigo_categoria1 = $categoria_proceso1->codigo_categoria_proceso;

                    //     $convocatoria_categoria = new ConvocatoriaCategoria;
                    //     $convocatoria_categoria->codigo_convocatoria = $codigo_convcocatoria;
                    //     $convocatoria_categoria->codigo_categoria_proceso = $codigo_categoria1;
                    //     $convocatoria_categoria->codigo_estado = 8;
                    //     $convocatoria_categoria->save();
                    // }

                    // $categoria_proceso2 = CategoriaProceso::where('nombre_categoria_proceso', 'ilike', "%".$categoria_2."%")->first();
                    // if($categoria_proceso2 instanceof CategoriaProceso){
                    //     $codigo_categoria2 = $categoria_proceso2->codigo_categoria_proceso;

                    //     $convocatoria_categoria = new ConvocatoriaCategoria;
                    //     $convocatoria_categoria->codigo_convocatoria = $codigo_convcocatoria;
                    //     $convocatoria_categoria->codigo_categoria_proceso = $codigo_categoria2;
                    //     $convocatoria_categoria->codigo_estado = 8;
                    //     $convocatoria_categoria->save();
                    // }

                    // if($codigo_etapa_proyecto != ""){
                    //     $convocatoria_etapa = new ConvocatoriaEtapa;
                    //     $convocatoria_etapa->codigo_convocatoria = $convocatoria->codigo_convocatoria;
                    //     $convocatoria_etapa->codigo_etapa_proyecto = $codigo_etapa_proyecto;
                    //     $convocatoria_etapa->codigo_estado = 8;
                    //     $convocatoria_etapa->save();
                    // }
                }
            }
        });
    }

    public static function getUserEntityByName($user_name, $tipo_usuario){
        return Usuario::where('nombre_usuario', 'like', "%".$user_name."%")->where('tipo_usuario',$tipo_usuario)->first();
    }

    public static function updateCategory(){
        Excel::load('catalogo.xlsx', function($reader){
            $results = $reader->get()->toArray();
		    foreach($results as $resul){
                $categoria = trim($resul['tipo']);
                if($categoria){
                    $categoria_producto = new CategoriaProducto;

                    $url_categoria = slugtp($categoria, $categoria_producto->getTable(), 'url_categoria_producto');

                    $categoria_producto->nombre_categoria_producto = $categoria;
                    $categoria_producto->url_categoria_producto = $url_categoria;
                    $categoria_producto->save();

                    $codigo_categoria_producto = $categoria_producto->codigo_categoria_producto;

                    $categoria_producto_institucion = new CategoriaProductoInsti;
                    $categoria_producto_institucion->codigo_categoria_producto = $codigo_categoria_producto;
                    $categoria_producto_institucion->codigo_institucion = 29;
                    $categoria_producto_institucion->codigo_estado = 8;
                    $categoria_producto_institucion->save();

                    \Log::info("Categoria:  ".$categoria." -> ".$url_categoria);
                }
            }
        });
        \Log::info("Finalizo");
    }

    public static function getCityByName($city_name)
    {
      $query = Ciudad::query();
      $query->whereRaw("nombre_ciudad ilike '%$city_name%'");
      return $query->first();
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

    

    public static function getusuarioInstitucion($codigo_usuario,$codigo_institucion){
      return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
      ->where('codigo_institucion', '=', $codigo_institucion)
      ->first();
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

    // public static function getUserEntityByName($user_name, $tipo_usuario){
    //     return Usuario::where('nombre_usuario', '=', $user_name)->where('tipo_usuario',$tipo_usuario)->where('codigo_estado',8)->first();
    // }

    public function cargueBolivar(){
        Excel::load('bolivar.xlsx', function($reader){
            $results = $reader->get()->toArray();
		    foreach($results as $resul){
                // Datos de usuarios
                $nombre = trim($resul['nombre']);
                $apellido = trim($resul['apellido']);
                $correo_usuario = trim($resul['correo_usuario']);
                $celular = trim($resul['celular']);
                $ciudad = trim($resul['ciudad']);
                $datos_ciudad = $this->getCityByName($ciudad);
		    	if($datos_ciudad instanceof Ciudad){
                    $codigo_ciudad = $datos_ciudad->codigo_ciudad;
		    	}
		    	else{
		    		echo "La ciudad ".$ciudad."No existe \n";
		    		exit();
		    	}
                // Fin datos de usuarios
                // Datos de empresa
                $razon_social = trim($resul['empresa']);
                $pagina_empresa = trim($resul['pagina_empresa']);
                // Fin datos de empresa
                // Encuesta
                $ventas_2015 = trim($resul['ventas_2015']); //3252
                $ventas_2016 = trim($resul['ventas_2016']); //3253
                $ventas_2017 = trim($resul['ventas_2017']); //3254
                $proyeccion_2018 = trim($resul['proyeccion_2018']); //3255
                $empleados_2015 = trim($resul['empleados_2015']); //3256
                $empleados_2016 = trim($resul['empleados_2016']); //3257
                $empleados_2017 = trim($resul['empleados_2017']); //3258
                $modelo_negocio = trim($resul['modelo_negocio']); //3259
                $valor_producto = trim($resul['valor_producto']); //3260
                $cliestes_destacados = trim($resul['cliestes_destacados']); //3261
                $mercado_objetivo = trim($resul['mercado_objetivo']); //3262
                $participacion_mercado = trim($resul['participacion_mercado']); //3263
                $aspectos_resaltar = trim($resul['aspectos_resaltar']); //3264
                $retos_empresa = trim($resul['retos_empresa']); //3265
                $experiencia_emprendedor = trim($resul['experiencia_emprendedor']); //3266
                $trayectoria_emprendedor = trim($resul['trayectoria_emprendedor']); //3267

                $datos_encuesta = array();

                $datos_encuesta[] = array(
                    'respuesta' => $ventas_2015,
                    'pregunta' => 3252
                );
                $datos_encuesta[] = array(
                    'respuesta' => $ventas_2016,
                    'pregunta' => 3253
                );
                $datos_encuesta[] = array(
                    'respuesta' => $ventas_2017,
                    'pregunta' => 3254
                );
                $datos_encuesta[] = array(
                    'respuesta' => $proyeccion_2018,
                    'pregunta' => 3255
                );
                $datos_encuesta[] = array(
                    'respuesta' => $empleados_2015,
                    'pregunta' => 3256
                );
                $datos_encuesta[] = array(
                    'respuesta' => $empleados_2016,
                    'pregunta' => 3257
                );
                $datos_encuesta[] = array(
                    'respuesta' => $empleados_2017,
                    'pregunta' => 3258
                );
                $datos_encuesta[] = array(
                    'respuesta' => $modelo_negocio,
                    'pregunta' => 3259
                );
                $datos_encuesta[] = array(
                    'respuesta' => $valor_producto,
                    'pregunta' => 3260
                );
                $datos_encuesta[] = array(
                    'respuesta' => $cliestes_destacados,
                    'pregunta' => 3261
                );
                $datos_encuesta[] = array(
                    'respuesta' => $mercado_objetivo,
                    'pregunta' => 3262
                );
                $datos_encuesta[] = array(
                    'respuesta' => $participacion_mercado,
                    'pregunta' => 3263
                );
                $datos_encuesta[] = array(
                    'respuesta' => $aspectos_resaltar,
                    'pregunta' => 3264
                );
                $datos_encuesta[] = array(
                    'respuesta' => $retos_empresa,
                    'pregunta' => 3265
                );
                $datos_encuesta[] = array(
                    'respuesta' => $experiencia_emprendedor,
                    'pregunta' => 3266
                );
                $datos_encuesta[] = array(
                    'respuesta' => $trayectoria_emprendedor,
                    'pregunta' => 3267
                );

                // Fin Encuesta

                // ######## Creacion de usuario ######

                $codigo_usuario = "";
                $codigo_usuario_institucion = "";
                $usuario = $this->getEmailUsuario($correo_usuario);
                if(!$usuario instanceof Usuario){

                    // $newnickname = substr($this->unwantedCharacters($nombre), 0, 16);
                    // $nickname_usuario = $this->validateUserNickname(str_replace(" ", "", $newnickname));

                    $nusuario = new Usuario;
                    $nusuario->nombre_usuario = $nombre;
                    // $nusuario->nickname_usuario = $nickname_usuario;
                    $nusuario->correo_electronico = $correo_usuario;
                    if($celular!="")
                        $nusuario->movil_usuario = $celular;
                    $nusuario->codigo_estado = 8;
                    $nusuario->codigo_ciudad = $codigo_ciudad;
                    $nusuario->save();

                    $codigo_usuario = $nusuario->codigo_usuario;

                    $nusuario_institucion = new UsuarioInstitucion;
                    $nusuario_institucion->codigo_usuario = $codigo_usuario;
                    $nusuario_institucion->codigo_institucion = 38;
                    $nusuario_institucion->codigo_estado = 14;
                    $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                    $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                    $nusuario_institucion->save();

                    $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                    $npersona = new Persona;
                    $npersona->codigo_usuario = $codigo_usuario;
                    if($apellido!="")
                        $npersona->primer_apellido_persona = $apellido;
                    $npersona->save();
                }
                else{
                    $codigo_usuario = $usuario->codigo_usuario;

                    if($celular!=""){
                        $usuario->movil_usuario = $celular;
                        $usuario->save();
                    }

                    $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, 38);
                    if(!$usuario_institucion instanceof UsuarioInstitucion){
                        $nusuario_institucion = new UsuarioInstitucion;
                        $nusuario_institucion->codigo_usuario = $codigo_usuario;
                        $nusuario_institucion->codigo_institucion = 38;
                        $nusuario_institucion->codigo_estado = 14;
                        $nusuario_institucion->codigo_rol = 3;
                        $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                        $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                        $nusuario_institucion->save();
                        $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;
                    }
                    else{
                        $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
                    }
                }
                ######## Creacion de usuario ######
                ######## Creacion de entidad ######
                $empresa = $this->getUserEntityByName($razon_social, 2);

                if(!$empresa instanceof Usuario){
                    $newnickname_entidad = substr($this->unwantedCharacters($razon_social), 0, 16);
                    $nickname_entidad = $this->validateUserNickname(str_replace(" ", "", $newnickname_entidad));

                    $nusuario_entidad = new Usuario;
                    $nusuario_entidad->nombre_usuario = $razon_social;
                    $nusuario_entidad->nickname_usuario = $nickname_entidad;
                    $nusuario_entidad->pagina_web_usuario = $pagina_empresa;
                    $nusuario_entidad->tipo_usuario = 2;
                    $nusuario_entidad->codigo_estado = 8;
                    $nusuario_entidad->save();

                    $codigo_usuario_entidad = $nusuario_entidad->codigo_usuario;

                    $nusuario_institucion_entidad = new UsuarioInstitucion;
                    $nusuario_institucion_entidad->codigo_usuario = $codigo_usuario_entidad;
                    $nusuario_institucion_entidad->codigo_institucion = 38;
                    $nusuario_institucion_entidad->codigo_estado = 8;
                    $nusuario_institucion_entidad->save();

                    $codigo_usuario_institucion_entidad = $nusuario_institucion_entidad->codigo_usuario_institucion;

                    $ndato_entidad = new DatoEntidad;
                    $ndato_entidad->codigo_usuario_institucion = $codigo_usuario_institucion_entidad;
                    $ndato_entidad->codigo_privacidad_contenido = 2;
                    $ndato_entidad->codigo_estado = 8;
                    $ndato_entidad->save();

                    $nentidad = new Entidad;
                    $nentidad->codigo_usuario = $codigo_usuario_entidad;
                    $nentidad->seg_codigo_usuario = $codigo_usuario;
                    $nentidad->codigo_clase_empre_insti = 35;
                    $nentidad->save();

                    $codigo_prestador_servicio = $codigo_usuario_entidad;
                }
                else{
                    $codigo_prestador_servicio = $empresa->codigo_usuario;

                    $empresa->pagina_web_usuario = $pagina_empresa;
                    $empresa->save();

                    $entidad = Entidad::where('codigo_usuario',$codigo_prestador_servicio)->first();
                    if($entidad instanceof Entidad){

                    }
                    else{
                        $entidad = new Entidad;
                        $entidad->codigo_usuario = $codigo_prestador_servicio;
                        $entidad->seg_codigo_usuario = $codigo_usuario;
                        $entidad->codigo_clase_empre_insti = 2;
                        $entidad->save();
                    }
                }

                $codigo_encuesta = 95;
                $codigo_version = 89;
                $codigo_modulo = 26;
                $codigo_convocatoria = 201;

                $usuario_encuesta = new UsuarioEncuesta;
                $usuario_encuesta->codigo_encuesta = $codigo_encuesta;
                $usuario_encuesta->codigo_usuario = $codigo_prestador_servicio;
                $usuario_encuesta->codigo_estado = 8;
                $usuario_encuesta->fecha_usuario_encuesta = date('Y-m-d');
                $usuario_encuesta->hora_usuario_encuesta = date('H:m:i');
                $usuario_encuesta->codigo_modulo = $codigo_modulo;
                $usuario_encuesta->id_recurso = $codigo_convocatoria;
                $usuario_encuesta->save();

                $codigo_usuario_encuesta = $usuario_encuesta->codigo_usuario_encuesta;

                foreach($datos_encuesta as $dato){
                    $respuestas_encuesta = new RespuestaEncuesta;
                    $respuestas_encuesta->codigo_pregunta_encuesta = $dato['pregunta'];
                    $respuestas_encuesta->codigo_estado = 8;
                    $respuestas_encuesta->codigo_usuario = $codigo_prestador_servicio;
                    $respuestas_encuesta->texto_respuesta_encuesta = $dato['respuesta'];
                    $respuestas_encuesta->fecha_respuesta_encuesta = date('Y-m-d');
                    $respuestas_encuesta->hora_respuesta_encuesta = date('H:m:i');
                    $respuestas_encuesta->codigo_usuario_encuesta = $codigo_usuario_encuesta;
                    $respuestas_encuesta->save();
                }

                \Log::info("Se creo el usuario  ".$correo_usuario);
            }
        });
        dd("Fin");
    }
}
