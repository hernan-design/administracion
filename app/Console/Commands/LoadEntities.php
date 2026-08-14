<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
App\Models\Seguridad\RolUsuario,
App\Models\Core\Ciudad, 
App\Models\Core\Entidad, 
App\Models\Core\Persona, 
App\Models\Core\UsuarioInstitucion,
App\Models\Core\DatoEntidad,
App\Models\Core\Estado,
App\Models\Core\CategoriaProyecto;;

class LoadEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:loadentities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargar entidades en una institución';

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
        Excel::load('entidades.xlsx', function($reader){
            $results = $reader->get()->toArray();
            $codigo_institucion = 79;
            foreach($results as $resul){
                $identificacion_usuario = trim($resul['identificacion_usuario']);
                $nombre_persona = trim($resul['nombre_usuario']);
                $primer_apellido_persona = trim($resul['primer_apellido_persona']);
                $movil_persona = trim($resul['movil_usuario']);
                $correo_electronico = strtolower(trim($resul['correo_electronico']));
                $tipo_identificacion_persona = trim($resul['tipo_identificacion_usuario']);
                if($tipo_identificacion_persona == 'Cédula'){
                    $tipo_identificacion_persona = 1;
                }else if($tipo_identificacion_persona == 'Permiso especial de permanencia'){
                    $tipo_identificacion_persona = 9;
                }else if($tipo_identificacion_persona == 'Cédula de extranjería'){
                    $tipo_identificacion_persona = 2;
                }else if($tipo_identificacion_persona == 'Cédula de otro país'){
                    $tipo_identificacion_persona = 11;
                }else if($tipo_identificacion_persona == 'Pasaporte'){
                    $tipo_identificacion_persona = 5;
                }else if($tipo_identificacion_persona == 'Tarjeta de Identidad'){
                    $tipo_identificacion_persona = 4;
                }else if($tipo_identificacion_persona == 'Documento nacional de identificación'){
                    $tipo_identificacion_persona = 12;
                }else if($tipo_identificacion_persona == 'Nit'){
                    $tipo_identificacion_persona = 3;
                }else if($tipo_identificacion_persona == 'Registro Civil'){
                    $tipo_identificacion_persona = 6;
                }else{
                    $tipo_identificacion_persona = 1;
                }
                
                $identificion_entidad = trim($resul['identificacion_entidad']);
                $nombre_entidad = trim($resul['nombre']);
                $etapa_entidad = trim($resul['etapa']);
                $actividad_economica_entidad = trim($resul['actividad_economica']);
                $sector_entidad = trim($resul['sector']);
                $fecha_inicio_operaciones_entidad = trim($resul['inicio_operacion']);
                $descripcion_entidad = trim($resul['biografia_usuario']);
                $ciudad_entidad = trim($resul['nombre_ciudad']);
                $cobrimiento_entidad = trim($resul['cubrimiento']);
                $video_entidad = trim($resul['video_entidad']);
                $facebook_entidad = trim($resul['facebook_usuario']);
                $twitter_entidad = trim($resul['twitter_usuario']);
                $linkedin_entidad = trim($resul['linkedin_usuario']);
                $pagina_entidad = trim($resul['pagina_web']);
                $tipo_entidad = trim($resul['tipo_entidad']);
                $tipo_sociedad_entidad = trim($resul['tipo_sociedad']);
                
                if($correo_electronico){

                    $usuario = $this->getEmailUsuario($correo_electronico);                
                    if(!$usuario instanceof Usuario){
                        
                        $data_user = [];
                        $data_user['nombre_usuario'] = $nombre_persona;
                        $data_user['correo_electronico'] = $correo_electronico;
                        $data_user['codigo_estado'] = 8;
                        $data_user['movil_usuario'] = $movil_persona;
                        $data_user['tipo_identificacion_usuario'] = $tipo_identificacion_persona;
                        $data_user['identificacion_usuario'] = $identificacion_usuario;
                        
                        $nusuario = Usuario::storeApi($data_user);
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
                        if($primer_apellido_persona!="")
                            $npersona->primer_apellido_persona = $primer_apellido_persona;                    
                        $npersona->save();

                        // Send notification
                        $data = [];
                        $data['_inst'] = $codigo_institucion;
                        $data['_to'] = $correo_electronico;
                        $data['_to_name'] = ucwords(strtolower($nombre_persona));
                        $data['_subject'] = "Confirmación de registro";
                        $data['_template'] = 'emails.user.createusermarketplace';
                        $data['_change_password']  = "/password/email";
                        $data['_marketplace']  = "/marketplace";

                        SendEmail::notification($data);
                        
                        \Log::info("CREA usuario: ".$correo_electronico."\n");                    
                    } else {                  
                        $codigo_usuario = $usuario->codigo_usuario;                    

                        if($identificacion_usuario)
                            $usuario->identificacion_usuario = $identificacion_usuario;
                        $usuario->nombre_usuario = $nombre_persona;
                        $usuario->movil_usuario = $movil_persona;
                        $usuario->tipo_identificacion_usuario = $tipo_identificacion_persona;
                        $usuario->save();

                        $nueva_persona = Persona::where('codigo_usuario',$codigo_usuario)->first();
                        if($nueva_persona instanceof Persona){
                            if($primer_apellido_persona!="")
                                $nueva_persona->primer_apellido_persona = $primer_apellido_persona;
                            $nueva_persona->save();
                        }
                        
                        $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, $codigo_institucion);
                        if(!$usuario_institucion instanceof UsuarioInstitucion){

                            $nusuario_institucion = new UsuarioInstitucion;
                            $nusuario_institucion->codigo_usuario = $codigo_usuario;
                            $nusuario_institucion->codigo_institucion = $codigo_institucion;
                            $nusuario_institucion->codigo_estado = 8;
                            $nusuario_institucion->codigo_rol = 3;
                            $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                            $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                            $nusuario_institucion->save();
                            $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                            \Log::info("VINCULO usuario: ".$correo_electronico."\n");

                            // Send notification
                            $data = [];
                            $data['_inst'] = $codigo_institucion;
                            $data['_to'] = $correo_electronico;
                            $data['_to_name'] = ucwords(strtolower($nombre_persona));
                            $data['_subject'] = "Confirmación de registro";
                            $data['_template'] = 'emails.user.createusermarketplace';
                            $data['_change_password']  = "/password/email";
                            $data['_marketplace']  = "/marketplace";

                            SendEmail::notification($data);
                        }
                        else{
                            $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
                            \Log::info("YA existe usuario: ".$correo_electronico."\n");
                        }
                    }

                    $rol_usuario_institucion = RolUsuario::getRolUsuarioInstitucion($codigo_institucion,$codigo_usuario,14);
                    if(!$rol_usuario_institucion instanceof RolUsuario){	    			
                        $rol_usuario = new RolUsuario;
                        $rol_usuario->role_id = 14;
                        $rol_usuario->user_id = $codigo_usuario;
                        $rol_usuario->codigo_institucion = $codigo_institucion;
                        $rol_usuario->save();
                    }

                    $fecha_inicio_operaciones_entidad = substr(trim($fecha_inicio_operaciones_entidad),0,10);
                    
                    if($fecha_inicio_operaciones_entidad!= ""){
                        $porciones = explode("-", $fecha_inicio_operaciones_entidad);
                        if(count($porciones)==3){
                            if(!checkdate($porciones[1], $porciones[2], $porciones[0])){
                                $fecha_inicio_operaciones_entidad = null;
                            }
                        }else{
                            $fecha_inicio_operaciones_entidad = null;
                        }            
                    }

                    $dato_industria = CategoriaProyecto::where('nombre_categoria_proyecto','=',$sector_entidad)->where('codigo_institucion',$codigo_institucion)->first();
                    if($dato_industria instanceof CategoriaProyecto){
                        $codigo_industria = $dato_industria->codigo_categoria_proyecto;
                    }else {
                        $codigo_industria = 374;
                    }
                    
                    $codigo_etapa = "";
                    if($etapa_entidad == 'Ventas'){
                        $codigo_etapa = 115;
                    }else{
                        $codigo_etapa = 115;
                    }
                    
                    $codigo_cubrimiento = "";
                    if($cobrimiento_entidad == 'Local'){
                        $codigo_cubrimiento = 1;
                    }else if($cobrimiento_entidad == 'Regional'){
                        $codigo_cubrimiento = 2;
                    }else if($cobrimiento_entidad == 'Nacional'){
                        $codigo_cubrimiento = 3;
                    }else if($cobrimiento_entidad == 'Internacional'){
                        $codigo_cubrimiento = 4;
                    }else{
                        $codigo_cubrimiento = null;
                    }

                    $datos_ciudad_empresa = $this->getCityByName($ciudad_entidad);
                    if($datos_ciudad_empresa instanceof Ciudad){
                        $codigo_ciudad_empresa = $datos_ciudad_empresa->codigo_ciudad;                    
                    }
                    else{
                        \Log::info("La ciudad ".$ciudad." No existe \n");
                        exit();
                    }
                    
                    // $tipo_sociedad_entidad = trim($resul['tipo_sociedad']);
                   
                    $codigo_tipo_identificacion_entidad = 3;

                    $codigo_clase_emprendimiento_insti = null;
                    if($tipo_entidad == 'Empresa'){
                        $codigo_clase_emprendimiento_insti = 53;    
                    } else{
                        $codigo_clase_emprendimiento_insti = 52;
                    }
                    

                    $empresa = Entidad::where('entidad.seg_codigo_usuario', $codigo_usuario)->where('ent.codigo_estado',8)->where('ui.codigo_institucion',$codigo_institucion)->join('seguridad.usuario as ent','entidad.codigo_usuario','=','ent.codigo_usuario')->join('core.usuario_institucion as ui','ent.codigo_usuario','=','ui.codigo_usuario')->first();
                    if($empresa instanceof Entidad){
                        $codigo_empresa = $empresa->codigo_usuario;

                        $usuario_empresa = Usuario::find($codigo_empresa);
                        if($usuario_empresa instanceof Usuario){
                            $usuario_empresa->nombre_usuario = $nombre_entidad;
                            $usuario_empresa->identificacion_usuario = $identificion_entidad;
                            $usuario_empresa->codigo_ciudad = $codigo_ciudad_empresa;
                            
                            if($descripcion_entidad!="")
                                $usuario_empresa->biografia_usuario = $descripcion_entidad;
                            if($fecha_inicio_operaciones_entidad!=null)
                                $usuario_empresa->fecha_nacimiento_usuario = $fecha_inicio_operaciones_entidad;
                            $usuario_empresa->pagina_web_usuario = $pagina_entidad;
                            $usuario_empresa->save();
                        }   
                                            
                        $empresa->codigo_tipo_identificacion = $codigo_tipo_identificacion_entidad;
                        if($codigo_cubrimiento!="")
                            $empresa->cubrimiento_entidad = $codigo_cubrimiento;
                        if($descripcion_entidad!="")
                            $empresa->descripcion_entidad = $descripcion_entidad;
                        if($facebook_entidad!="")
                            $empresa->facebook_entidad = $facebook_entidad;
                        if($twitter_entidad!="")
                            $empresa->twitter_entidad = $twitter_entidad;
                        if($linkedin_entidad!="")
                            $empresa->linkedin_entidad = $linkedin_entidad;    
                        if($video_entidad!="")
                            $empresa->video_entidad = $video_entidad;
                        $empresa->save();

                        \Log::info("Ya existe empresa: ".$codigo_empresa." -> ".$nombre_entidad." -> ".$empresa->nombre_usuario."\n");
                    }
                    else{
                        if($nombre_entidad){
                            $newnickname_entidad = substr($this->unwantedCharacters($nombre_entidad), 0, 16);
                            $nickname_entidad = $this->validateUserNickname(str_replace(" ", "", $newnickname_entidad));
                            
                            $nusuario_entidad = new Usuario;
                            $nusuario_entidad->nombre_usuario = $nombre_entidad;		    		
                            $nusuario_entidad->nickname_usuario = $nickname_entidad;
                            $nusuario_entidad->codigo_ciudad = $codigo_ciudad_empresa;		
                            $nusuario_entidad->identificacion_usuario = $identificion_entidad;
                            if($descripcion_entidad!="")
                                $nusuario_entidad->biografia_usuario = $descripcion_entidad;
                            if($fecha_inicio_operaciones_entidad!=null)
                                $nusuario_entidad->fecha_nacimiento_usuario = $fecha_inicio_operaciones_entidad;                        
                            $nusuario_entidad->tipo_usuario = 2;                        
                            $nusuario_entidad->codigo_estado = 8;
                            if($pagina_entidad!="")
                                $nusuario_entidad->pagina_web_usuario = $pagina_entidad;
                            $nusuario_entidad->save();

                            $codigo_usuario_entidad = $nusuario_entidad->codigo_usuario;						

                            $nusuario_institucion_entidad = new UsuarioInstitucion;
                            $nusuario_institucion_entidad->codigo_usuario = $codigo_usuario_entidad;
                            $nusuario_institucion_entidad->codigo_institucion = $codigo_institucion;	    			
                            $nusuario_institucion_entidad->codigo_estado = 8;
                            $nusuario_institucion_entidad->save();

                            $codigo_usuario_institucion_entidad = $nusuario_institucion_entidad->codigo_usuario_institucion;
                            
                            $ndato_entidad = new DatoEntidad;
                            $ndato_entidad->codigo_usuario_institucion = $codigo_usuario_institucion_entidad;
                            $ndato_entidad->codigo_privacidad_contenido = 2;
                            $ndato_entidad->codigo_etapa_proyecto = $codigo_etapa;
                            $ndato_entidad->codigo_categoria_proyecto = $codigo_industria;                        
                            $ndato_entidad->codigo_estado = 8;		    			
                            $ndato_entidad->save();
                                    
                            $nentidad = new Entidad;
                            $nentidad->codigo_usuario = $codigo_usuario_entidad;
                            $nentidad->seg_codigo_usuario = $codigo_usuario;
                            $nentidad->codigo_clase_empre_insti = $codigo_clase_emprendimiento_insti;	
                            $nentidad->codigo_tipo_identificacion = $codigo_tipo_identificacion_entidad;
                            $nentidad->cubrimiento_entidad = $codigo_cubrimiento;
                            $nentidad->descripcion_entidad = $descripcion_entidad;
                            $nentidad->facebook_entidad = $facebook_entidad;
                            $nentidad->twitter_entidad = $twitter_entidad;
                            $nentidad->linkedin_entidad = $linkedin_entidad;
                            if($video_entidad!="")
                                $nentidad->video_entidad = $video_entidad;
                            $nentidad->save();

                            $codigo_empresa = $codigo_usuario_entidad;
                            \Log::info("CREA empresa: ".$nombre_entidad."\n");
                        }else{
                            $codigo_empresa = $codigo_usuario;
                        }		                
                    }
                }                
            }
        });
        \Log::info("Finalizo");
    }

    public static function getEmailUsuario($email) {
        return Usuario::where('correo_electronico', '=',  trim($email)) -> first();
    }

    public static function getusuarioInstitucion($codigo_usuario,$codigo_institucion){
		return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
		->where('codigo_institucion', '=', $codigo_institucion)
		->first();
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
}
