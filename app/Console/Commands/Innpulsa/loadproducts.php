<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Log, Storage, Excel, DB;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\Ciudad, 
    App\Models\Core\Entidad, 
    App\Models\Core\Persona, 
    App\Models\Core\UsuarioInstitucion,
    App\Models\Param\TerminosCondiciones,
    App\Models\Core\DatoEntidad,
    App\Models\Core\Producto,
    App\Models\Core\ActividadServicio,
    App\Models\Core\EntregableServicio,
    App\Models\Core\GrupoUsuario,
    App\Models\Core\Estado;

class loadproducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:loadproducts';

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
        Excel::load('products.xlsx', function($reader) {
            $results = $reader->get()->toArray();
            foreach($results as $resul){
                
                $numero = trim($resul['numero']);
                $radicado = trim($resul['radicado']);
                $concepto = trim($resul['concepto']);

                $nit = trim($resul['nit']);
                $proponente = trim($resul['proponente']);
                $numero_contrato = trim($resul['numero_contrato']);                                
                $direccion = trim($resul['direccion']);
                $ciudad = trim($resul['ciudad']);
                $datos_ciudad_empresa = $this->getCityByName($ciudad);
		    	if($datos_ciudad_empresa instanceof Ciudad){
                    $codigo_ciudad_empresa = $datos_ciudad_empresa->codigo_ciudad;                    
		    	}
		    	else{
                    \Log::info("La ciudad ".$ciudad." No existe \n");
		    		exit();
		    	}
                $departamento = trim($resul['departamento']);
                
                $respresentante_legal = trim($resul['respresentante_legal']);
                $correo_representante_legal = trim($resul['correo_representante_legal']);                
                $porciones = explode(" ", $respresentante_legal);
                $nombre = "";
                $primer_apellido = "";
                $segundo_apellido = "";

                if(count($porciones)==1){
                    $nombre = $porciones[0];
                }else if(count($porciones)==2){
                    $nombre = $porciones[0];
                    $primer_apellido = $porciones[1];
                }else if(count($porciones)==3){
                    $nombre = $porciones[0];
                    $primer_apellido = $porciones[1];
                    $segundo_apellido = $porciones[2];
                }else if(count($porciones)==4){
                    $nombre = $porciones[0]." ".$porciones[1];
                    $primer_apellido = $porciones[2];
                    $segundo_apellido = $porciones[3];
                }else{
                    $nombre = $porciones[0]." ".$porciones[1]." ".$porciones[2];
                    $primer_apellido = $porciones[3];
                    $segundo_apellido = $porciones[4];
                }
                $cedula = trim($resul['cedula']);
                $ciudad_expedicion_rep_legal = trim($resul['ciudad_expedicion_rep_legal']);

                $datos_ciudad = $this->getCityByName($ciudad_expedicion_rep_legal);
		    	if($datos_ciudad instanceof Ciudad){
                    $codigo_ciudad_expedicion = $datos_ciudad->codigo_ciudad;                    
		    	}
		    	else{                    
                    \Log::info("La ciudad ".$ciudad_expedicion_rep_legal." No existe \n");
		    		exit();
                }
                
                $contador_productos = 0;
                
                $usuario = $this->getEmailUsuario($correo_representante_legal);
                
                if(!$usuario instanceof Usuario){
                    
                    $data_user = [];
                    $data_user['nombre_usuario'] = $nombre;
                    $data_user['correo_electronico'] = $correo_representante_legal;                    
                    $data_user['codigo_estado'] = 8;                    
                    // $data_user['password'] = $contrase;			                    

                    if($primer_apellido!="")
                        $data_user['primer_apellido_persona'] = $primer_apellido;
                    if($segundo_apellido!="")
                        $data_user['segundo_apellido_persona'] = $segundo_apellido;
                    
                    // $nusuario = Usuario::storeApi($data_user);			

                    // $codigo_usuario = $nusuario->codigo_usuario;

                    // $nusuario_institucion = new UsuarioInstitucion;
                    // $nusuario_institucion->codigo_usuario = $codigo_usuario;
                    // $nusuario_institucion->codigo_institucion = 51;
                    // $nusuario_institucion->codigo_estado = 8;
                    // $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                    // $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                    // $nusuario_institucion->save();

                    // $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                    // $terms = TerminosCondiciones::termsAndConditions(TerminosCondiciones::getTypeTerms('terms'));
                    // if($terms instanceof TerminosCondiciones) {
                    //     $acceptanceTerms = new AceptacionTerminos();
                    //     $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                    //     $acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
                    //     $acceptanceTerms->codigo_estado = Estado::getState('active');
                    //     $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                    //     $acceptanceTerms->direccion_ip = $ip;
                    //     $acceptanceTerms->save();
                    // }

                    // // Privacy
                    // $privacy = TerminosCondiciones::termsAndConditions(TerminosCondiciones::getTypeTerms('privacy'));
                    // if($privacy instanceof TerminosCondiciones) {
                    //     $acceptanceTerms = new AceptacionTerminos();
                    //     $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                    //     $acceptanceTerms->codigo_version_terminos = $privacy->codigo_version_terminos;
                    //     $acceptanceTerms->codigo_estado = Estado::getState('active');
                    //     $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                    //     $acceptanceTerms->direccion_ip = $ip;
                    //     $acceptanceTerms->save();
                    // }
                    
                    \Log::info("CREA usuario: ".$correo_representante_legal."\n");                    
                    exit();
                }
                else{                  
                    $codigo_usuario = $usuario->codigo_usuario;
                    
                    if($cedula)
                        $usuario->identificacion_usuario = $cedula;
                    $usuario->nombre_usuario = $nombre;
                    $usuario->save();

                    $nueva_persona = Persona::where('codigo_usuario',$codigo_usuario)->first();
                    if($nueva_persona instanceof Persona){
                        if($primer_apellido!="")
                            $nueva_persona->primer_apellido_persona = $primer_apellido;
                        if($segundo_apellido!="")
                            $nueva_persona->segundo_apellido_persona = $segundo_apellido;
                        if($codigo_ciudad_expedicion!="")
                            $nueva_persona->codigo_ciudad = $codigo_ciudad_expedicion;
                        $nueva_persona->save();
                    }
                    
                    $usuario_institucion = $this->getusuarioInstitucion($codigo_usuario, 51);
                    if(!$usuario_institucion instanceof UsuarioInstitucion){

                        $nusuario_institucion = new UsuarioInstitucion;
                        $nusuario_institucion->codigo_usuario = $codigo_usuario;
                        $nusuario_institucion->codigo_institucion = 51;
                        $nusuario_institucion->codigo_estado = 8;
                        $nusuario_institucion->codigo_rol = 3;
                        $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                        $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                        $nusuario_institucion->save();
                        $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;

                        $terms = TerminosCondiciones::termsAndConditions(TerminosCondiciones::getTypeTerms('terms'));
                        if($terms instanceof TerminosCondiciones) {
                            $acceptanceTerms = new AceptacionTerminos();
                            $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                            $acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
                            $acceptanceTerms->codigo_estado = Estado::getState('active');
                            $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                            $acceptanceTerms->direccion_ip = $ip;
                            $acceptanceTerms->save();
                        }

                        // Privacy
                        $privacy = TerminosCondiciones::termsAndConditions(TerminosCondiciones::getTypeTerms('privacy'));
                        if($privacy instanceof TerminosCondiciones) {
                            $acceptanceTerms = new AceptacionTerminos();
                            $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                            $acceptanceTerms->codigo_version_terminos = $privacy->codigo_version_terminos;
                            $acceptanceTerms->codigo_estado = Estado::getState('active');
                            $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                            $acceptanceTerms->direccion_ip = $ip;
                            $acceptanceTerms->save();
                        }

                        \Log::info("VINCULO usuario: ".$correo_representante_legal."\n");
                    }
                    else{
                        $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
                        \Log::info("YA existe usuario: ".$correo_representante_legal."\n");
                    }
                }

                $rol_usuario_institucion = RolUsuario::getRolUsuarioInstitucion(51,$codigo_usuario,14);
                if(!$rol_usuario_institucion instanceof RolUsuario){	    			
                    $rol_usuario = new RolUsuario;
                    $rol_usuario->role_id = 14;
                    $rol_usuario->user_id = $codigo_usuario;
                    $rol_usuario->codigo_institucion = 51;
                    $rol_usuario->save();
                }

                $empresa = Entidad::where('entidad.seg_codigo_usuario', $codigo_usuario)->where('ent.codigo_estado',8)->where('ui.codigo_institucion',51)->join('seguridad.usuario as ent','entidad.codigo_usuario','=','ent.codigo_usuario')->join('core.usuario_institucion as ui','ent.codigo_usuario','=','ui.codigo_usuario')->first();
                if($empresa instanceof Entidad){
                    $codigo_empresa = $empresa->codigo_usuario;

                    $usuario_empresa = Usuario::find($codigo_empresa);
                    if($usuario_empresa instanceof Usuario){
                        $usuario_empresa->nombre_usuario = $proponente;
                        $usuario_empresa->identificacion_usuario = $nit;
                        $usuario_empresa->codigo_ciudad = $codigo_ciudad_empresa;
                        if($numero_contrato!="")
                            $usuario_empresa->numero_contrato = $numero_contrato;
                        $usuario_empresa->save();
                    }                    
                    \Log::info("Ya existe empresa: ".$codigo_empresa." -> ".$proponente." -> ".$empresa->nombre_usuario."\n");
                }
                else{
                    if($proponente){
                        $newnickname_entidad = substr($this->unwantedCharacters($proponente), 0, 16);
                        $nickname_entidad = $this->validateUserNickname(str_replace(" ", "", $newnickname_entidad));
                        
                        $nusuario_entidad = new Usuario;
                        $nusuario_entidad->nombre_usuario = $proponente;		    		
                        $nusuario_entidad->nickname_usuario = $nickname_entidad;
                        $nusuario_entidad->codigo_ciudad = $codigo_ciudad_empresa;		
                        $nusuario_entidad->identificacion_usuario = $nit;
                        $nusuario_entidad->tipo_usuario = 2;
                        $nusuario_entidad->codigo_estado = 8;
                        if($numero_contrato!="")
                            $nusuario_entidad->numero_contrato = $numero_contrato;
                        $nusuario_entidad->save();

                        $codigo_usuario_entidad = $nusuario_entidad->codigo_usuario;						

                        $nusuario_institucion_entidad = new UsuarioInstitucion;
                        $nusuario_institucion_entidad->codigo_usuario = $codigo_usuario_entidad;
                        $nusuario_institucion_entidad->codigo_institucion = 51;	    			
                        $nusuario_institucion_entidad->codigo_estado = 8;
                        $nusuario_institucion_entidad->save();

                        $codigo_usuario_institucion_entidad = $nusuario_institucion_entidad->codigo_usuario_institucion;
                        
                        $ndato_entidad = new DatoEntidad;
                        $ndato_entidad->codigo_usuario_institucion = $codigo_usuario_institucion_entidad;
                        $ndato_entidad->codigo_privacidad_contenido = 2;
                        $ndato_entidad->codigo_etapa_proyecto = 28;
                        $ndato_entidad->codigo_estado = 8;		    			
                        $ndato_entidad->save();
                                
                        $nentidad = new Entidad;
                        $nentidad->codigo_usuario = $codigo_usuario_entidad;
                        $nentidad->seg_codigo_usuario = $codigo_usuario;
                        $nentidad->codigo_clase_empre_insti = 2;			
                        $nentidad->save();

                        $codigo_empresa = $codigo_usuario_entidad;
                        \Log::info("CREA empresa: ".$proponente."\n");
                    }else{
                        $codigo_empresa = $codigo_usuario;
                    }		                
                }
                
                $nombre_servicio = trim($resul['nombre_servicio']);
                $categoria = trim($resul['categoria']);
                $codigo_categoria_producto_institucion = "";

                // (322,323,324, 325, 326, 327, 328, 329, 330, 16, 19)

                if($categoria == 'Servicios de Asesoría en temas financieros'){
                    $codigo_categoria_producto_institucion = 322;
                }else if($categoria == 'Servicios de Asesorías legales y/o jurídicas'){
                    $codigo_categoria_producto_institucion = 323;
                }else if($categoria == 'Servicios relacionados con el diseño y ejecución de estrategias comerciales'){
                    $codigo_categoria_producto_institucion = 324;
                }else if($categoria == 'Servicios relacionados con ajuste y afinación del modelo de negocio'){
                    $codigo_categoria_producto_institucion = 325;
                }else if($categoria == 'Servicios de Asesoría en desarrollo y sofisticación del producto/servicio'){
                    $codigo_categoria_producto_institucion = 16;
                }else if($categoria == 'Servicios de Asesoría en acceso a nuevos mercados'){
                    $codigo_categoria_producto_institucion = 19;
                }else if($categoria == 'Servicios relacionados con habilidades gerenciales/liderazgo'){
                    $codigo_categoria_producto_institucion = 327;
                }else if($categoria == 'Servicios de consultoría en tecnologías de información'){
                    $codigo_categoria_producto_institucion = 329;
                }else if($categoria == 'Servicios de mercadeo/ marketing'){
                    $codigo_categoria_producto_institucion = 330;
                }else{
                    \Log::info("No existe categoria: ".$categoria."\n");
                    exit();
                }

                $precio = trim($resul['precio']);
                $modalidad = trim($resul['modalidad']);
                $codigo_modalidad = "";
		    	if($modalidad == "Virtual"){
					$codigo_modalidad = 1;
				}else if($modalidad == "Semipresencial"){
					$codigo_modalidad = 2;
				}else if($modalidad == "Presencial"){
					$codigo_modalidad = 3;
                }else if($modalidad == "semipresencial"){
					$codigo_modalidad = 2;
                }else{
                    \Log::info("Falta modalidad: ".$modalidad."\n");
                    exit();
                }
                
                $ciudad_prestacion = trim($resul['ciudad_prestacion']);
                $plazo = trim($resul['plazo']);
                $alcance = trim($resul['alcance']);
                $resultados = trim($resul['resultados']);                

                $texto_actividades = "
                Información Adicional<br><br>
                <ul>";
                
                if($alcance != ""){
			    	$texto_actividades .= "
		    		<li>Alcance: $alcance</li>";
                }

                if($ciudad_prestacion != ""){
			    	$texto_actividades .= "
		    		<li>Ciudad de prestación: $ciudad_prestacion</li>";
			    }
                
		    	if($plazo != ""){
			    	$texto_actividades .= "
		    		<li>Plazo: $plazo</li>";
                }

                if($resultados != ""){
			    	$texto_actividades .= "
		    		<li>Resultados: $resultados</li>";
                }
		    	
		    	$texto_actividades .= "
		    	</ul><br><br>";
                
                $precio_total_servicio = round($precio);                

                $producto = Producto::where('nombre_producto',$nombre_servicio)->where('codigo_institucion',51)->where('seg_codigo_usuario',$codigo_empresa)->where('codigo_usuario',$codigo_usuario)->first();
                if(!$producto instanceof Producto){
                    $newurl_producto = $this->seoUrl($nombre_servicio);
                    $url_producto = $this->validateProductUrl($newurl_producto);                

                    $nproducto = new Producto;                
                    $nproducto->codigo_estado = 8;
                    $nproducto->codigo_clase_producto = 3;
                    $nproducto->nombre_producto = $nombre_servicio;                    
                    if(is_numeric($precio_total_servicio)){
                        $nproducto->precio_producto = $precio_total_servicio;	
                    }
                    else{
                        $nproducto->precio_producto = 0;
                    }				
                    $nproducto->iva_producto = 19;
                    $nproducto->url_producto = $url_producto;
                    $nproducto->codigo_iva_producto = 1;								
                    $nproducto->codigo_usuario = $codigo_usuario;
                    $nproducto->resumen_producto = $alcance;
                    $nproducto->codigo_categoria_prod_insti = $codigo_categoria_producto_institucion;                
                    $nproducto->modalidad_prestacion_servicio = $codigo_modalidad;                    
                    $nproducto->seg_codigo_usuario = $codigo_empresa;
                    $nproducto->codigo_institucion = 51;
                    $nproducto->alcance_servicio = $alcance;
                    $nproducto->resultados_servicio = $resultados;
                    $nproducto->producto_oferta = true;
                    $nproducto->acuerdo_producto = true;
                    
                    $nproducto->save();

                    $codigo_producto = $nproducto->codigo_producto;
                    $contador_productos++;
                    \Log::info($contador_productos." Creo el producto: ".$nombre_servicio."\n");

                }else{

                    $codigo_producto = $producto->codigo_producto;

                    $producto->codigo_estado = 8;
                    $producto->codigo_clase_producto = 3;
                    if(is_numeric($precio_total_servicio)){
                        $producto->precio_producto = $precio_total_servicio;	
                    }
                    else{
                        $producto->precio_producto = 0;
                    }				
                    $producto->iva_producto = 19;
                    $producto->codigo_iva_producto = 1;								
                    $producto->codigo_usuario = $codigo_usuario;
                    $producto->resumen_producto = $alcance;
                    $producto->codigo_categoria_prod_insti = $codigo_categoria_producto_institucion;                
                    $producto->modalidad_prestacion_servicio = $codigo_modalidad;                    
                    $producto->seg_codigo_usuario = $codigo_empresa;
                    $producto->codigo_institucion = 51;
                    $producto->alcance_servicio = $alcance;
                    $producto->resultados_servicio = $resultados;
                    $producto->producto_oferta = true;
                    $producto->acuerdo_producto = true;
                    
                    $producto->save();
                    \Log::info("Ya existe producto: ".$nombre_servicio."\n");
                }   
                
                $texto_actividades .= "
                Actividades<br><br>
                <ul>";
                            
                for($i = 1; $i<=21; $i++){

                    $a = trim($resul['a'.$i]);
                    $a_r = trim($resul['a'.$i.'_r']);
                    $a_sp = trim($resul['a'.$i.'_sp']);
                    $a_hsp = trim($resul['a'.$i.'_hsp']);
                    $a_sv = trim($resul['a'.$i.'_sv']);
                    $a_hsv = trim($resul['a'.$i.'_hsv']);
                    
                    if($a != ""){
                        if($a_r == "Asesor"){
                            $responsable = 1;
                        }else if($a_r == "Empresario - Consultor"){
                            $responsable = 2;
                        }else {
                            $responsable = 1;
                        }
                        
                        $actividad_servicio = new ActividadServicio();
                        $actividad_servicio->codigo_producto = $codigo_producto;
                        $actividad_servicio->codigo_estado = Estado::getState('active');
                        $actividad_servicio->descripcion_actividad_servicio = $a;
                        if($a_sp)
                            $actividad_servicio->sesiones_presenciales_actividad = $a_sp;
                        if($a_sv)
                            $actividad_servicio->sesiones_virtuales_actividad = $a_sv;
                        $actividad_servicio->responsable_actividad = $responsable;
                        if($a_hsp)
                            $actividad_servicio->horas_sesiones_presen_actividad = $a_hsp;
                        if($a_hsv)
                            $actividad_servicio->horas_sesiones_virtua_actividad = $a_hsv;
                        $actividad_servicio->save();

                        $texto_actividades .= "
                        <li>$a";
                        
                        if($a_hsp)
                            $texto_actividades .= " sesiones presenciales: $a_sp ";
                        
                        if($a_hsv)
                            $texto_actividades .= " sesiones virtuales $a_sv";
                        
                        $texto_actividades .= "</li>";
                    }
                }

                $texto_actividades .= "
                </ul><br><br>Entregables<br><br><ul>";

                for($j = 1; $j<=17; $j++){
                    $e = trim($resul['e'.$j]);
                    if($e != ""){
                        $entregable_servicio = new EntregableServicio();
                        $entregable_servicio->codigo_producto = $codigo_producto;
                        $entregable_servicio->codigo_estado = Estado::getState('active');
                        $entregable_servicio->descripcion_entregable_servicio = $e;                                        
                        $entregable_servicio->save();

                        $texto_actividades .= "
		    		    <li>$e</li>";
                    }                    
                }                

                $texto_actividades .= "
                </ul>";

                for($k = 1; $k<=3; $k++){
                    $p = trim($resul['p'.$k]);                    
                    $porciones = explode(" ", $p);
                    $cp = trim($resul['cp'.$k]);
                    
                    $nombre = "";
                    $primer_apellido = "";
                    $segundo_apellido = "";
                    
                    if($cp){
                        if(count($porciones)==1){
                            $nombre = $porciones[0];
                        }else if(count($porciones)==2){
                            $nombre = $porciones[0];
                            $primer_apellido = $porciones[1];
                        }else if(count($porciones)==3){
                            $nombre = $porciones[0];
                            $primer_apellido = $porciones[1];
                            $segundo_apellido = $porciones[2];
                        }else if(count($porciones)==4){
                            $nombre = $porciones[0]." ".$porciones[1];
                            $primer_apellido = $porciones[2];
                            $segundo_apellido = $porciones[3];
                        }else{
                            $nombre = $porciones[0]." ".$porciones[1]." ".$porciones[2];
                            $primer_apellido = $porciones[3];
                            $segundo_apellido = $porciones[4];
                        }

                        $usuario_asesor = $this->getEmailUsuario($cp);
                
                        if(!$usuario_asesor instanceof Usuario){                            
                            $data_user = [];
                            $data_user['nombre_usuario'] = $nombre;
                            $data_user['correo_electronico'] = $cp;
                            $data_user['codigo_estado'] = 8;

                            if($primer_apellido!="")
                                $data_user['primer_apellido_persona'] = $primer_apellido;
                            if($segundo_apellido!="")
                                $data_user['segundo_apellido_persona'] = $segundo_apellido;
                            
                            $nusuario = Usuario::storeApi($data_user);

                            $codigo_asesor = $nusuario->codigo_usuario;

                            $nusuario_institucion = new UsuarioInstitucion;
                            $nusuario_institucion->codigo_usuario = $codigo_asesor;
                            $nusuario_institucion->codigo_institucion = 51;
                            $nusuario_institucion->codigo_estado = 8;
                            $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                            $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                            $nusuario_institucion->save();

                            $codigo_usuario_grupo = $nusuario_institucion->codigo_usuario;

                            // \Log::info("Se creo usuario: ".$cp."\n");
                        }else {
                            $usuario_institucion = $this->getusuarioInstitucion($usuario_asesor->codigo_usuario, 51);
                            if(!$usuario_institucion instanceof UsuarioInstitucion){
                                $nusuario_institucion = new UsuarioInstitucion;
                                $nusuario_institucion->codigo_usuario = $usuario_asesor->codigo_usuario;
                                $nusuario_institucion->codigo_institucion = 51;
                                $nusuario_institucion->codigo_estado = 8;
                                $nusuario_institucion->codigo_rol = 3;
                                $nusuario_institucion->fecha_registro_usuario = date('Y-m-d');
                                $nusuario_institucion->hora_registro_usuario = date('H:m:i');
                                $nusuario_institucion->save();
                                $codigo_usuario_institucion = $nusuario_institucion->codigo_usuario_institucion;
                                // \Log::info("Se vinculo el usuario: ".$cp."\n");
                            }

                            $codigo_usuario_grupo = $usuario_asesor->codigo_usuario;
                        }
                        $miembro_equipo = GrupoUsuario::where('seg_codigo_usuario',$codigo_usuario_grupo)->where('codigo_usuario',$codigo_empresa)->first();
                        if(!$miembro_equipo instanceof GrupoUsuario){
                            $grupo_usuario = new GrupoUsuario;
                            $grupo_usuario->codigo_estado = Estado::getState('active');;
                            $grupo_usuario->codigo_usuario = $codigo_empresa;
                            $grupo_usuario->seg_codigo_usuario = $codigo_usuario_grupo;
                            $grupo_usuario->codigo_rol_usuario_grupo_insti = 16;                        
                            $grupo_usuario->save();
                        }                        
                    }                                        
                }  
                
                $producto_actualizar = Producto::find($codigo_producto);
                $producto_actualizar->descripcion_producto = $texto_actividades;
                $producto_actualizar->informacion_adicional_producto = $texto_actividades;                
                $producto_actualizar->save();
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
    
    public static function getEmailUsuario($email) {
        return Usuario::where('correo_electronico', '=',  trim($email)) -> first();
    }

    public static function getusuarioInstitucion($codigo_usuario,$codigo_institucion){
		return UsuarioInstitucion::where('codigo_usuario', '=', $codigo_usuario)
		->where('codigo_institucion', '=', $codigo_institucion)
		->first();
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

    public function seoUrl($string)
	{
		$string = trim($string);
		// Cut string 45 characters by words
		$limit = 45;
		if(strlen($string) > $limit) {
			if(false !== ($breakpoint = strpos($string, ' ', $limit))) {
				if($breakpoint < strlen($string) - 1) {
					$string = substr($string, 0, $breakpoint);
				}
			}
		}
		// Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
	    $string = strtolower($string);
	    // Strip accents
		$string = strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	    // Strip any unwanted characters
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    // Clean multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    // Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);
	    return $string;
    }
    
    public function validateProductUrl($url_product, $contador = 0)
	{
		$newurl = $contador > 0 ? sprintf("%s-%d",$url_product, $contador) : $url_product;
		$query = Producto::query();
		$query->select('producto.*');
		$query->whereRaw(("trim(lower(url_producto)) = trim(lower('$newurl'))"));
		$entity = $query->first();
		
		$contador ++;
		if($entity instanceof Producto){
			$newurl = $this->validateProductUrl($url_product, $contador);
		}
		return $newurl;	
	}
}
