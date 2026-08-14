<?php

namespace App\Console\Commands\Compensar;

use Illuminate\Console\Command;

use Log, Storage, Excel, DB, SendEmail;

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
    App\Models\Core\Estado,
    App\Models\Core\CategoriaProyecto;

class LoadProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compensar:loadproducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de productos y proveedores de compensar';

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
        $history = array();
        Excel::load('products.xlsx', function($reader) use($history){
            $results = $reader->get()->toArray();
            foreach($results as $resul){
                $codigo_institucion = 30;                                                
                
                $nit = trim($resul['nit']);
                $proponente = trim($resul['proponente']);
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
                // $departamento = trim($resul['departamento']);
                
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
                
                $numero_celular = trim($resul['numero_celular']);
                $fecha_nacimiento = trim($resul['fecha_nacimiento']);
                
                $fecha_nacimiento = substr(trim($resul['fecha_nacimiento']),0,10);
                
                if($fecha_nacimiento!= ""){
                    $porciones = explode("-", $fecha_nacimiento);
                    if(count($porciones)==3){
                        if(!checkdate($porciones[1], $porciones[2], $porciones[0])){
                            $fecha_nacimiento = null;
                        }
                    }else{
                        $fecha_nacimiento = null;
                    }            
                }

                $genero = trim($resul['sexo']);
                $codigo_genero = "";
                if($genero == 'M'){
                    $codigo_genero = 1;
                }else if($genero == 'F'){
                    $codigo_genero = 2;
                }else{
                    $codigo_genero = null;
                }
                
                $tipo_identificacion = trim($resul['tipo_identificacion']);
                $codigo_tipo_identificacion = "";
                if($tipo_identificacion == 'Cédula'){
                    $codigo_tipo_identificacion = 1;
                }else if($tipo_identificacion == 'Permiso especial de permanencia'){
                    $codigo_tipo_identificacion = 9;
                }else if($tipo_identificacion == 'Cédula de extranjería'){
                    $codigo_tipo_identificacion = 2;
                }else if($tipo_identificacion == 'Cédula de otro país'){
                    $codigo_tipo_identificacion = 11;
                }else if($tipo_identificacion == 'Pasaporte'){
                    $codigo_tipo_identificacion = 5;
                }else if($tipo_identificacion == 'Tarjeta de Identidad'){
                    $codigo_tipo_identificacion = 4;
                }else if($tipo_identificacion == 'Documento nacional de identificación'){
                    $codigo_tipo_identificacion = 12;
                }else if($tipo_identificacion == 'Nit'){
                    $codigo_tipo_identificacion = 3;
                }else if($tipo_identificacion == 'Registro Civil'){
                    $codigo_tipo_identificacion = 6;
                }else{
                    $codigo_tipo_identificacion = null;
                }

                $contador_productos = 0;
                
                $usuario = $this->getEmailUsuario($correo_representante_legal);
                
                if (isset($correo_representante_legal) && empty($correo_representante_legal)) {
                    Log::info('salto iteracion');
                    break;
                }
                if(!$usuario instanceof Usuario){
                    $data_user = [];
                    $data_user['nombre_usuario'] = $nombre;
                    $data_user['correo_electronico'] = $correo_representante_legal;
                    $data_user['codigo_estado'] = 8;
                    $data_user['movil_usuario'] = $numero_celular;
                    $data_user['fecha_nacimiento_usuario'] = $fecha_nacimiento;                    
                    $data_user['tipo_identificacion_usuario'] = $codigo_tipo_identificacion;
                    // $data_user['password'] = $contrase;			                    
                    
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

                    // $terms = TerminosCondiciones::termsAndConditionsInstitution(TerminosCondiciones::getTypeTerms('terms'), $codigo_institucion);
                    // if($terms instanceof TerminosCondiciones) {
                    //     $acceptanceTerms = new TerminosCondiciones();
                    //     $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                    //     $acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
                    //     $acceptanceTerms->codigo_estado = Estado::getState('active');
                    //     $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                    //     $acceptanceTerms->save();
                    // }

                    // // Privacy
                    // $privacy = TerminosCondiciones::termsAndConditionsInstitution(TerminosCondiciones::getTypeTerms('privacy'), $codigo_institucion);
                    // if($privacy instanceof TerminosCondiciones) {
                    //     $acceptanceTerms = new TerminosCondiciones();
                    //     $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                    //     $acceptanceTerms->codigo_version_terminos = $privacy->codigo_version_terminos;
                    //     $acceptanceTerms->codigo_estado = Estado::getState('active');
                    //     $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                    //     $acceptanceTerms->save();
                    // }

                    $npersona = new Persona;
                    $npersona->codigo_usuario = $codigo_usuario;
                    $npersona->genero_persona = $codigo_genero;
                    if($primer_apellido!="")
                        $npersona->primer_apellido_persona = $primer_apellido;
                    if($segundo_apellido!="")
                        $npersona->segundo_apellido_persona = $segundo_apellido;
                   
                    $npersona->save();

                    // Send notification
                    $data = [];
                    $data['_inst'] = 30;
                    $data['_to'] = $correo_representante_legal;
                    $data['_to_name'] = ucwords(strtolower($nombre));
                    $data['_subject'] = "Confirmación de registro marketplace";
                    $data['_template'] = 'emails.user.createusermarketplace';
                    $data['_change_password']  = "/password/email";
                    $data['_marketplace']  = "/marketplace";

                    SendEmail::notification($data);
                    
                    \Log::info("CREA usuario: ".$correo_representante_legal."\n");
                    array_push($history, "Creo el usuario: ".$correo_representante_legal."\n");                
                } else{                  
                    $codigo_usuario = $usuario->codigo_usuario;                    
                    if($cedula)
                        $usuario->identificacion_usuario = $cedula;
                    $usuario->nombre_usuario = $nombre;
                    $usuario->movil_usuario = $numero_celular;
                    $usuario->fecha_nacimiento_usuario = $fecha_nacimiento;
                    $usuario->tipo_identificacion_usuario = $codigo_tipo_identificacion;
                    $usuario->save();

                    $nueva_persona = Persona::where('codigo_usuario',$codigo_usuario)->first();
                    if($nueva_persona instanceof Persona){
                        if($primer_apellido!="")
                            $nueva_persona->primer_apellido_persona = $primer_apellido;
                        if($segundo_apellido!="")
                            $nueva_persona->segundo_apellido_persona = $segundo_apellido;
                        if($codigo_ciudad_expedicion!="")
                            $nueva_persona->codigo_ciudad = $codigo_ciudad_expedicion;
                        $nueva_persona->genero_persona = $codigo_genero;
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

                        // $terms = TerminosCondiciones::termsAndConditionsInstitution(TerminosCondiciones::getTypeTerms('terms'), $codigo_institucion);
                        // if($terms instanceof TerminosCondiciones) {
                        //     $acceptanceTerms = new TerminosCondiciones();
                        //     $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                        //     $acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
                        //     $acceptanceTerms->codigo_estado = Estado::getState('active');
                        //     $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                        //     $acceptanceTerms->direccion_ip = $ip;
                        //     $acceptanceTerms->save();
                        // }

                        // // Privacy
                        // $privacy = TerminosCondiciones::termsAndConditionsInstitution(TerminosCondiciones::getTypeTerms('privacy'), $codigo_institucion);
                        // if($privacy instanceof TerminosCondiciones) {
                        //     $acceptanceTerms = new TerminosCondiciones();
                        //     $acceptanceTerms->codigo_usuario_institucion = $codigo_usuario_institucion;
                        //     $acceptanceTerms->codigo_version_terminos = $privacy->codigo_version_terminos;
                        //     $acceptanceTerms->codigo_estado = Estado::getState('active');
                        //     $acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
                        //     $acceptanceTerms->direccion_ip = $ip;
                        //     $acceptanceTerms->save();
                        // }

                        \Log::info("VINCULO usuario: ".$correo_representante_legal."\n");

                        // Send notification
                        $data = [];
                        $data['_inst'] = 30;
                        $data['_to'] = $correo_representante_legal;
                        $data['_to_name'] = ucwords(strtolower($nombre));
                        $data['_subject'] = "Confirmación de registro marketplace";
                        $data['_template'] = 'emails.user.createusermarketplace';
                        $data['_change_password']  = "/password/email";
                        $data['_marketplace']  = "/marketplace";

                        SendEmail::notification($data);
                        array_push($history, "Creo el usuario: ".$correo_representante_legal."\n");
                    }
                    else{
                        $codigo_usuario_institucion = $usuario_institucion->codigo_usuario_institucion;
                        \Log::info("YA existe usuario: ".$correo_representante_legal."\n");
                        array_push($history, "YA existe el usuario: ".$correo_representante_legal."\n");
                    }
                }

                /*$rol_usuario_institucion = RolUsuario::getRolUsuarioInstitucion($codigo_institucion,$codigo_usuario,14);
                if(!$rol_usuario_institucion instanceof RolUsuario){	    			
                    $rol_usuario = new RolUsuario;
                    $rol_usuario->role_id = 14;
                    $rol_usuario->user_id = $codigo_usuario;
                    $rol_usuario->codigo_institucion = $codigo_institucion;
                    $rol_usuario->save();
                }*/

                $telefono_empresa = trim($resul['telefono_empresa']);
                $direccion_empresa = trim($resul['direccion_empresa']);
                $inicio_operacion = substr(trim($resul['inicio_operacion']),0,10);
                
                if($inicio_operacion!= ""){
                    $porciones = explode("-", $inicio_operacion);
                    if(count($porciones)==3){
                        if(!checkdate($porciones[1], $porciones[2], $porciones[0])){
                            $inicio_operacion = null;
                        }
                    }else{
                        $inicio_operacion = null;
                    }            
                }
                
                $industria = trim($resul['industria']);
                $dato_industria = CategoriaProyecto::where('nombre_categoria_proyecto','=',$industria)->first();
                if($dato_industria instanceof CategoriaProyecto){
                    $codigo_industria = $dato_industria->codigo_categoria_proyecto;
                }else {
                    $codigo_industria = null;
                }
                
                $etapa = trim($resul['etapa']);
                $codigo_etapa = "";
                if($etapa == 'Idea'){
                    $codigo_etapa = 70;
                }else if($etapa == 'En marcha'){
                    $codigo_etapa = 71;
                }else{
                    $codigo_etapa = null;
                }
                
                $cubrimiento = trim($resul['cubrimiento']);
                $codigo_cubrimiento = "";
                if($cubrimiento == 'Local'){
                    $codigo_cubrimiento = 1;
                }else if($cubrimiento == 'Regional'){
                    $codigo_cubrimiento = 2;
                }else if($cubrimiento == 'Nacional'){
                    $codigo_cubrimiento = 3;
                }else if($cubrimiento == 'Internacional'){
                    $codigo_cubrimiento = 4;
                }else{
                    $codigo_cubrimiento = null;
                }
                
                $resumen_empresa = trim($resul['resumen_empresa']);
                $facebook = trim($resul['facebook']);
                $twitter = trim($resul['twitter']);
                $instagram = trim($resul['instagram']);
                $linkedin = trim($resul['linkedin']);
                $web = trim($resul['web']);
                $codigo_tipo_identificacion_entidad = 3;

                $empresa = Entidad::where('entidad.seg_codigo_usuario', $codigo_usuario)->where('ent.codigo_estado',8)->where('ui.codigo_institucion',$codigo_institucion)->join('seguridad.usuario as ent','entidad.codigo_usuario','=','ent.codigo_usuario')->join('core.usuario_institucion as ui','ent.codigo_usuario','=','ui.codigo_usuario')->first();
                if($empresa instanceof Entidad){
                    $codigo_empresa = $empresa->codigo_usuario;

                    $usuario_empresa = Usuario::find($codigo_empresa);
                    if($usuario_empresa instanceof Usuario){
                        $usuario_empresa->nombre_usuario = $proponente;
                        $usuario_empresa->identificacion_usuario = $nit;
                        $usuario_empresa->codigo_ciudad = $codigo_ciudad_empresa;
                        $usuario_empresa->telefono_usuario = $telefono_empresa;
                        $usuario_empresa->movil_usuario = $telefono_empresa;
                        if($resumen_empresa!="")
                            $usuario_empresa->biografia_usuario = $resumen_empresa;
                        $usuario_empresa->direccion_usuario = $direccion_empresa;
                        if($inicio_operacion!=null)
                            $usuario_empresa->fecha_nacimiento_usuario = $inicio_operacion;
                        $usuario_empresa->pagina_web_usuario = $web;
                        $usuario_empresa->save();
                    }   
                                        
                    $empresa->codigo_tipo_identificacion = $codigo_tipo_identificacion_entidad;
                    if($codigo_cubrimiento!="")
                        $empresa->cubrimiento_entidad = $codigo_cubrimiento;
                    if($resumen_empresa!="")
                        $empresa->descripcion_entidad = $resumen_empresa;
                    if($facebook!="")
                        $empresa->facebook_entidad = $facebook;
                    if($twitter!="")
                        $empresa->twitter_entidad = $twitter;
                    if($instagram!="")
                        $empresa->instagram_entidad = $instagram;
                    if($linkedin!="")
                        $empresa->linkedin_entidad = $linkedin;                    
                    $empresa->save();

                    \Log::info("Ya existe empresa: ".$codigo_empresa." -> ".$proponente." -> ".$empresa->nombre_usuario."\n");
                    array_push($history, "Ya existe empresa: ".$empresa->nombre_usuario."\n");
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
                        if($resumen_empresa!="")
                            $nusuario_entidad->biografia_usuario = $resumen_empresa;
                        if($telefono_empresa!="")                        
                            $nusuario_entidad->telefono_usuario = $telefono_empresa;
                        if($telefono_empresa!="")
                            $nusuario_entidad->movil_usuario = $telefono_empresa;
                        if($direccion_empresa!="")
                            $nusuario_entidad->direccion_usuario = $direccion_empresa;
                        if($inicio_operacion!=null)
                            $nusuario_entidad->fecha_nacimiento_usuario = $inicio_operacion;                        
                        $nusuario_entidad->tipo_usuario = 2;                        
                        $nusuario_entidad->codigo_estado = 8;
                        if($web!="")
                            $nusuario_entidad->pagina_web_usuario = $web;
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
                        $nentidad->codigo_clase_empre_insti = 37;			
                        $nentidad->codigo_tipo_identificacion = $codigo_tipo_identificacion_entidad;
                        $nentidad->cubrimiento_entidad = $codigo_cubrimiento;
                        $nentidad->descripcion_entidad = $resumen_empresa;
                        $nentidad->facebook_entidad = $facebook;
                        $nentidad->twitter_entidad = $twitter;
                        $nentidad->instagram_entidad = $instagram;
                        $nentidad->linkedin_entidad = $linkedin;
                        $nentidad->save();

                        $codigo_empresa = $codigo_usuario_entidad;
                        \Log::info("CREA empresa: ".$proponente."\n");
                        array_push($history, "CREA empresa: ".$proponente."\n");
                    }else{
                        $codigo_empresa = $codigo_usuario;
                    }		                
                }
                
                $nombre_servicio = trim($resul['nombre_producto']);
                $categoria = trim($resul['categoria']);
                $resumen_producto = trim($resul['resumen_producto']);
                $unidades_disponibles = trim($resul['unidades_disponibles']);
                if($unidades_disponibles == ""){
                    $unidades_disponibles = 100;
                }
                
                $codigo_categoria_producto_institucion = "";

                // (322,323,324, 325, 326, 327, 328, 329, 330, 16, 19)

                if($categoria == "MANTENIMIENTO Y CONSTRUCCIÓN"){
                    $codigo_categoria_producto_institucion = 337;
                }else if($categoria == "ALIMENTOS"){
                    $codigo_categoria_producto_institucion = 338;
                }else if($categoria == "SERVICIOS"){
                    $codigo_categoria_producto_institucion = 339;
                }else if($categoria == "ROPA Y/O CALZADO"){
                    $codigo_categoria_producto_institucion = 350;
                }else if($categoria == "PAPELERIA"){
                    $codigo_categoria_producto_institucion = 351;
                }else if($categoria == "ELEMENTOS DE PROTECCIÓN"){
                    $codigo_categoria_producto_institucion = 352;
                }else if($categoria == "PRODUCTOS DEPORTIVOS"){
                    $codigo_categoria_producto_institucion = 353;
                }else if($categoria == "EMPAQUE"){
                    $codigo_categoria_producto_institucion = 354;
                }else if($categoria == "ACCESORIOS PARA EL HOGAR"){
                    $codigo_categoria_producto_institucion = 355;
                }else if($categoria == "EQUIPOS Y ACCESORIOS DE COCINA"){
                    $codigo_categoria_producto_institucion = 356;
                }else if($categoria == "LICORES"){
                    $codigo_categoria_producto_institucion = 357;
                }else if($categoria == "SOLUCIONES Y REACTIVOS"){
                    $codigo_categoria_producto_institucion = 358;
                }else if($categoria == "SEGURIDAD VIAL"){
                    $codigo_categoria_producto_institucion = 359;
                }else if($categoria == "EQUIPOS AUDIOVISUALES"){
                    $codigo_categoria_producto_institucion = 360;
                }else if($categoria == "MUEBLES O ENSERES"){
                    $codigo_categoria_producto_institucion = 361;
                }else if($categoria == "MANTENIMIENTO"){
                    $codigo_categoria_producto_institucion = 362;
                }else if($categoria == "ASEO"){
                    $codigo_categoria_producto_institucion = 363;
                }else if($categoria == "EQUIPOS DE GYM"){
                    $codigo_categoria_producto_institucion = 364;
                }else if($categoria == "JUGUETES"){
                    $codigo_categoria_producto_institucion = 365;
                }else if($categoria == "ARTESANÍAS"){
                    $codigo_categoria_producto_institucion = 372;
                }else{
                    $codigo_categoria_producto_institucion = 338;
                }
                
                $precio = trim($resul['precio']);
                $imagen = trim($resul['imagen']);              
                                           
                $texto_actividades = trim($resul['descripcion_producto']);                
                
                $precio_total_servicio = round($precio);                

                /*$producto = Producto::where('nombre_producto',$nombre_servicio)->where('codigo_institucion',$codigo_institucion)->where('seg_codigo_usuario',$codigo_empresa)->where('codigo_usuario',$codigo_usuario)->first();
                if(!$producto instanceof Producto){
                    $newurl_producto = $this->seoUrl($nombre_servicio);
                    $url_producto = $this->validateProductUrl($newurl_producto);                

                    $nproducto = new Producto;                
                    $nproducto->codigo_estado = 8;
                    $nproducto->codigo_clase_producto = 1;
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
                    $nproducto->resumen_producto = $resumen_producto;
                    $nproducto->codigo_categoria_prod_insti = $codigo_categoria_producto_institucion;                
                    $nproducto->seg_codigo_usuario = $codigo_empresa;
                    $nproducto->codigo_institucion = $codigo_institucion;
                    $nproducto->producto_oferta = false;
                    $nproducto->acuerdo_producto = false;
                    $nproducto->descripcion_producto = $texto_actividades;                    
                    $nproducto->cantidad_producto = $unidades_disponibles;
                    $nproducto->imagen_producto = $imagen;
                    $nproducto->save();

                    $codigo_producto = $nproducto->codigo_producto;
                    $contador_productos++;
                    \Log::info($contador_productos." Creo el producto: ".$nombre_servicio."\n");
                    array_push($history, "Creo el producto: ".$nombre_servicio."\n");
                }else{

                    $codigo_producto = $producto->codigo_producto;

                    $producto->codigo_estado = 8;
                    $producto->codigo_clase_producto = 1;
                    if(is_numeric($precio_total_servicio)){
                        $producto->precio_producto = $precio_total_servicio;	
                    }
                    else{
                        $producto->precio_producto = 0;
                    }				
                    $producto->iva_producto = 19;
                    $producto->codigo_iva_producto = 1;								
                    $producto->codigo_usuario = $codigo_usuario;
                    $producto->resumen_producto = $resumen_producto;
                    $producto->codigo_categoria_prod_insti = $codigo_categoria_producto_institucion;                
                    $producto->seg_codigo_usuario = $codigo_empresa;
                    $producto->codigo_institucion = $codigo_institucion;
                    $producto->producto_oferta = false;
                    $producto->acuerdo_producto = false;
                    $producto->descripcion_producto = $texto_actividades;
                    $producto->cantidad_producto = $unidades_disponibles;
                    
                    $producto->save();
                    \Log::info("Ya existe producto: ".$nombre_servicio."\n");
                    array_push($history, "Ya existe producto: ".$nombre_servicio."\n");
                }*/
            }
            array_push($history, "Finalizo");

            // Send notification
            $data = [];
            $data['_inst'] = 30;
            $data['_to'] = 'emprendimiento@compensar.com';
            $data['to_copy'] = ['administracion@tuproyecto.com', 'testertuproyecto@gmail.com'];
            $data['_to_name'] = 'Fomento Empresarial';
            $data['_subject'] = "Carga de proveedores";
            $data['_template'] = 'emails.user.notificationdata';
            $data['_history'] = $history;

            SendEmail::notification($data);         
        });
        \Log::info("Finalizo rutina");
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
