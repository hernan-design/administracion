<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Param\Institucion, 
    App\Models\Param\ItemMenu,
    App\Models\Param\MenuInstitucion,
    App\Models\Param\SubMenuInstitucion,
    App\Models\Seguridad\Rol,
    App\Models\Seguridad\PermisoRol,
    App\Models\Seguridad\RolInstitucion,
    App\Models\Core\TipoAsesoria,
    App\Models\Core\Calendario,
    App\Models\Core\CategoriaProyecto,
    App\Models\Core\EtapaProyecto,
    App\Models\Core\ClaseEmprendimientoInstitucion,
    App\Models\Core\CategoriaProductoInsti,
    App\Models\Core\CategoriaForoInstitucion,
    App\Models\Core\TipoEventoCalendario,
    App\Models\Core\PaisInstitucion,
    App\Models\Core\TipoIdInstitucion,
    App\Models\Param\TipoContInstitucion,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\ItemEvaluacionCompra,
    App\Models\Core\TipoComentEvalCompra;

class LoadInstitution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:loadinstitution';

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
        $codigo_institucion_i = 62;
        
        $permiso_rol = array();
        $permiso_rol ['25'] = array(8,2,9,16,19,18,20); // Marketplace
        $permiso_rol ['20'] = array(20); // Mentoria
        $roles = array(14,2,7,5);
        $categorias_producto = array(1,2,3);
        $tipos_evento = array(4,5,6,7,8,9,10,11);
        $clases_emprendimientos = array(2,4);
        $tipos_identificacion = array(1,3);
        $tipos_documentos = array(10,5,3);
        // 10 Hoja de vida, 1 Persona
        // 5 Imagen, 2 Entidad
        // 3 Brochure, 2 Entidad

        $activar_menu = true;
        // $activar_menu_admin = true;
        $activar_mentoria = true;
        $activar_marketplace = true; 
        $activar_foro = true; 
        $activar_eventos = true; 
        $activar_proyectos = true; 
        $activar_usuario = true;
        $activar_blog = true;
        
        $institucion = Institucion::find($codigo_institucion_i);
        if($institucion instanceof Institucion){
            $codigo_institucion = $institucion->codigo_institucion;
            \Log::info("Institucion ".$codigo_institucion." \n");
            
            // Instertar rol
            $old_role = Rol::where('name',$codigo_institucion)->first();
            \Log::info("Log ed error ".$codigo_institucion." \n");
            if(!$old_role instanceof Rol){
                $rol = new Rol;
                $rol->codigo_estado = 8;
                $rol->name = $codigo_institucion;
                $rol->tipo_rol = 2;
                $rol->title = "Rol de la institucion ".$codigo_institucion;
                $rol->description = "Definción de permisos institucion ".$codigo_institucion;
                $rol->save();

                $codigo_rol = $rol->id;
            }
            else{
                $codigo_rol = $old_role->id;
            }
            
            // Insertar permisos al rol
            foreach($permiso_rol as $key => $value) {
                $codigo_modulo = $key;
                $permisos = $value; 
                foreach($permisos as $permiso){
                    $codigo_permiso = $permiso;
                    $oldpermiso_rol = PermisoRol::where('role_id',$codigo_rol)->where('permission_id',$codigo_permiso)->where('codigo_modulo',$codigo_modulo)->first();
                    if(!$oldpermiso_rol instanceof PermisoRol){
                        $newpermiso_rol = new PermisoRol;
                        $newpermiso_rol->role_id = $codigo_rol;
                        $newpermiso_rol->permission_id = $codigo_permiso;
                        $newpermiso_rol->codigo_modulo = $codigo_modulo;
                        $newpermiso_rol->save();
                        \Log::info("Inserto permiso ".$codigo_permiso."\n");
                    }                                        
                }               
            }
            
            for($k = 0; $k < count($roles); $k++){            
                $oldrol_institucion = RolInstitucion::where('role_id',$roles[$k])->where('codigo_institucion',$codigo_institucion)->first();
                if(!$oldrol_institucion instanceof RolInstitucion){
                    $newrol_institucion = new RolInstitucion;
                    $newrol_institucion->role_id = $roles[$k];
                    $newrol_institucion->codigo_institucion = $codigo_institucion;
                    $newrol_institucion->codigo_estado = 8;
                    $newrol_institucion->save();
                    \Log::info("Inserto rol insti ".$roles[$k]."\n");
                }                
            }
            
            if($activar_menu){
                $registro_items = array();            
                array_push($registro_items, array("codigo_item_menu" => 24,"tipo" => 1,"orden" => 1,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 25,"tipo" => 1,"orden" => 2,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 26,"tipo" => 1,"orden" => 3,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 27,"tipo" => 1,"orden" => 4,"menu" => 2));

                array_push($registro_items, array("codigo_item_menu" => 10,"tipo" => 2,"orden" => 1,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 30,"tipo" => 2,"orden" => 2,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 35,"tipo" => 2,"orden" => 3,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 12,"tipo" => 2,"orden" => 1,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 29,"tipo" => 2,"orden" => 2,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 31,"tipo" => 2,"orden" => 3,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 22,"tipo" => 2,"orden" => 1,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 28,"tipo" => 2,"orden" => 2,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 32,"tipo" => 2,"orden" => 3,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 11,"tipo" => 2,"orden" => 1,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 36,"tipo" => 2,"orden" => 2,"menu" => 2));
                array_push($registro_items, array("codigo_item_menu" => 23,"tipo" => 2,"orden" => 3,"menu" => 2));

                $menu_plataforma = array();
                $menu_plataforma ['24'] = array(10,30,35); // Ecosistema
                $menu_plataforma ['25'] = array(12,29,31); // Comunidad
                $menu_plataforma ['26'] = array(22,28,32); // Conocimiento
                $menu_plataforma ['27'] = array(11,36,23); // Acciones

                foreach($menu_plataforma as $key => $value) {
                    $codigo_padre = $key;
                    $hijos = $value;                
                    $menu_institucion_padre = MenuInstitucion::where('codigo_item_menu',$codigo_padre)->where('codigo_institucion',$codigo_institucion)->where('codigo_menu',2)->first();
                    if($menu_institucion_padre instanceof MenuInstitucion){
                        $codigo_menu_institucion_padre = $menu_institucion_padre->codigo_menu_institucion;
                    }
                    else{
                        // Insero al papa
                        $pmenu_institucion = new MenuInstitucion;
                        $pmenu_institucion->codigo_institucion = $codigo_institucion;
                        $pmenu_institucion->codigo_menu = 2;
                        $pmenu_institucion->codigo_estado = 8;
                        $pmenu_institucion->codigo_item_menu = $codigo_padre;
                        $pmenu_institucion->tipo_menu_institucion = 1;
                        // $pmenu_institucion->orden_menu_institucion = $orden;
                        $pmenu_institucion->save();
                        $codigo_menu_institucion_padre = $pmenu_institucion->codigo_menu_institucion; 
                        \Log::info("Inserto el menu_institucion ".$codigo_menu_institucion_padre."\n");
                    }

                    foreach($hijos as $hijo){
                        $codigo_hijo = $hijo;
                        $menu_institucion_hijo = MenuInstitucion::where('codigo_item_menu',$codigo_hijo)->where('codigo_institucion',$codigo_institucion)->where('codigo_menu',2)->first();
                        if($menu_institucion_hijo instanceof MenuInstitucion){
                            $codigo_menu_institucion_hijo = $menu_institucion_hijo->codigo_menu_institucion;
                        }else{
                            // Inserto el hijo
                            $hmenu_institucion = new MenuInstitucion;
                            $hmenu_institucion->codigo_institucion = $codigo_institucion;
                            $hmenu_institucion->codigo_menu = 2;
                            $hmenu_institucion->codigo_estado = 8;
                            $hmenu_institucion->codigo_item_menu = $codigo_hijo;
                            $hmenu_institucion->tipo_menu_institucion = 2;
                            // $hmenu_institucion->orden_menu_institucion = $orden;
                            $hmenu_institucion->save();
                            $codigo_menu_institucion_hijo = $hmenu_institucion->codigo_menu_institucion;
                        }

                        $submenu_institucion_pladre = SubMenuInstitucion::where('par_codigo_menu_institucion',$codigo_menu_institucion_padre)->where('codigo_menu_institucion',$codigo_menu_institucion_hijo)->first();
                        if(!$submenu_institucion_pladre instanceof SubMenuInstitucion){
                            $hmenu_institucion = new SubMenuInstitucion;
                            $hmenu_institucion->par_codigo_menu_institucion = $codigo_menu_institucion_padre;
                            $hmenu_institucion->codigo_menu_institucion = $codigo_menu_institucion_hijo;
                            $hmenu_institucion->codigo_estado = 8;                        
                            $hmenu_institucion->save();
                            \Log::info("Inserto el submenu plataforma ".$codigo_padre."\n");
                        }else{
                            // \Log::info("El Submenu ".$codigo_padre." ya existe \n");
                        }
                    }
                }

                \Log::info("\n");
                \Log::info("termino la plataforma \n");

                array_push($registro_items, array("codigo_item_menu" => 51,"tipo" => 1,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 52,"tipo" => 1,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 53,"tipo" => 1,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 54,"tipo" => 1,"orden" => 4,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 56,"tipo" => 1,"orden" => 5,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 60,"tipo" => 1,"orden" => 6,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 85,"tipo" => 1,"orden" => 7,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 87,"tipo" => 1,"orden" => 8,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 86,"tipo" => 1,"orden" => 9,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 58,"tipo" => 1,"orden" => 10,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 55,"tipo" => 1,"orden" => 11,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 97,"tipo" => 1,"orden" => 12,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 59,"tipo" => 1,"orden" => 13,"menu" => 4));

                array_push($registro_items, array("codigo_item_menu" => 61,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 62,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 80,"tipo" => 2,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 81,"tipo" => 2,"orden" => 4,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 63,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 64,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 85,"tipo" => 2,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 84,"tipo" => 2,"orden" => 4,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 66,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 67,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 68,"tipo" => 2,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 69,"tipo" => 2,"orden" => 4,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 96,"tipo" => 2,"orden" => 5,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 70,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 71,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 57,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 73,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 74,"tipo" => 2,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 72,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 89,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 82,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 83,"tipo" => 2,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 88,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 113,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 77,"tipo" => 2,"orden" => 1,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 90,"tipo" => 2,"orden" => 2,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 91,"tipo" => 2,"orden" => 3,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 102,"tipo" => 2,"orden" => 4,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 110,"tipo" => 2,"orden" => 5,"menu" => 4));
                array_push($registro_items, array("codigo_item_menu" => 112,"tipo" => 2,"orden" => 6,"menu" => 4));
                
                foreach($registro_items as $value) {
                    $item = $value['codigo_item_menu'];
                    $tipo = $value['tipo'];
                    $orden = $value['orden'];
                    $menu = $value['menu'];

                    $menu_institucion = MenuInstitucion::where('codigo_item_menu',$item)->where('codigo_institucion',$codigo_institucion)->where('codigo_menu',$menu)->first();
                    if(!$menu_institucion instanceof MenuInstitucion){                                        
                        $nmenu_institucion = new MenuInstitucion;
                        $nmenu_institucion->codigo_institucion = $codigo_institucion;
                        $nmenu_institucion->codigo_menu = $menu;
                        $nmenu_institucion->codigo_estado = 8;
                        $nmenu_institucion->codigo_item_menu = $item;
                        $nmenu_institucion->tipo_menu_institucion = $tipo;
                        $nmenu_institucion->orden_menu_institucion = $orden;
                        $nmenu_institucion->save();
                        $codigo_nmenu_institucion = $nmenu_institucion->codigo_item_menu;
                        \Log::info("Inserto el menu_institucion ".$codigo_nmenu_institucion."\n");
                    }
                    else{
                        // validar si esta activo y si no lo estaa activar
                    }
                }
                
                $menu_administracion = array();
                $menu_administracion ['51'] = array(61, 62, 80, 81); // Seguridad
                $menu_administracion ['52'] = array(63, 64, 65, 84); // Convocatoria
                $menu_administracion ['53'] = array(66, 67, 68, 69, 96); // Marketplace
                $menu_administracion ['54'] = array(70,71); // Mentoria
                $menu_administracion ['56'] = array(57, 73,74); // Comunidad                
                $menu_administracion ['60'] = array(72); // Aprende
                $menu_administracion ['85'] = array(89, 82,83); // entidades        
                $menu_administracion ['87'] = array(88,113); // foros
                // $menu_administracion ['86'] = array(); // formularios
                // $menu_administracion ['58'] = array(); // Eventos
                // $menu_administracion ['55'] = array(); // Contenido
                // $menu_administracion ['97'] = array(); // Notificaciones
                $menu_administracion ['59'] = array(77, 90,91,102,110,112); // Reportes        
                                
                foreach($menu_administracion as $key_1 => $value_1) {
                    $codigo_padre = $key_1;
                    $hijos = $value_1;                
                    $menu_institucion_padre = MenuInstitucion::where('codigo_item_menu',$codigo_padre)->where('codigo_institucion',$codigo_institucion)->where('codigo_menu',4)->first();
                    if($menu_institucion_padre instanceof MenuInstitucion){
                        $codigo_menu_institucion_padre = $menu_institucion_padre->codigo_menu_institucion;
                    }
                    else{
                        // Insero al papa
                        $pmenu_institucion = new MenuInstitucion;
                        $pmenu_institucion->codigo_institucion = $codigo_institucion;
                        $pmenu_institucion->codigo_menu = 4;
                        $pmenu_institucion->codigo_estado = 8;
                        $pmenu_institucion->codigo_item_menu = $codigo_padre;
                        $pmenu_institucion->tipo_menu_institucion = 1;
                        // $pmenu_institucion->orden_menu_institucion = $orden;
                        $pmenu_institucion->save();
                        $codigo_menu_institucion_padre = $pmenu_institucion->codigo_menu_institucion;                    
                    }

                    foreach($hijos as $hijo){
                        $codigo_hijo = $hijo;
                        $menu_institucion_hijo = MenuInstitucion::where('codigo_item_menu',$codigo_hijo)->where('codigo_institucion',$codigo_institucion)->where('codigo_menu',2)->first();
                        if($menu_institucion_hijo instanceof MenuInstitucion){
                            $codigo_menu_institucion_hijo = $menu_institucion_hijo->codigo_menu_institucion;
                        }else{
                            // Inserto el hijo
                            $hmenu_institucion = new MenuInstitucion;
                            $hmenu_institucion->codigo_institucion = $codigo_institucion;
                            $hmenu_institucion->codigo_menu = 4;
                            $hmenu_institucion->codigo_estado = 8;
                            $hmenu_institucion->codigo_item_menu = $codigo_hijo;
                            $hmenu_institucion->tipo_menu_institucion = 2;
                            // $hmenu_institucion->orden_menu_institucion = $orden;
                            $hmenu_institucion->save();
                            $codigo_menu_institucion_hijo = $hmenu_institucion->codigo_menu_institucion;
                        }

                        $submenu_institucion_pladre = SubMenuInstitucion::where('par_codigo_menu_institucion',$codigo_menu_institucion_padre)->where('codigo_menu_institucion',$codigo_menu_institucion_hijo)->first();
                        if(!$submenu_institucion_pladre instanceof SubMenuInstitucion){
                            $hmenu_institucion = new SubMenuInstitucion;
                            $hmenu_institucion->par_codigo_menu_institucion = $codigo_menu_institucion_padre;
                            $hmenu_institucion->codigo_menu_institucion = $codigo_menu_institucion_hijo;
                            $hmenu_institucion->codigo_estado = 8;                        
                            $hmenu_institucion->save();
                        }else{
                            // \Log::info("El Submenu ".$codigo_padre." ya existe \n");
                        }
                    }
                }
            }            

            if($activar_mentoria){
                $tipo_asesoria = new TipoAsesoria;
                $tipo_asesoria->nombre_tipo_asesoria = "General";
                $tipo_asesoria->codigo_estado = 8;
                $tipo_asesoria->url_tipo_asesoria = "general_".$codigo_institucion;
                $tipo_asesoria->codigo_institucion = $codigo_institucion;
                $tipo_asesoria->save();                
                \Log::info("Mentoria General ".$codigo_institucion."\n");
            }
            if($activar_marketplace){

                $tipo_comentario_compra = TipoComentEvalCompra::where('codigo_institucion',$codigo_institucion)->first();
                if($tipo_comentario_compra instanceof TipoComentEvalCompra){
                    
                }

                for($k = 0; $k < count($categorias_producto); $k++){            
                    $oldcat_pro_institucion = CategoriaProductoInsti::where('codigo_categoria_producto',$categorias_producto[$k])->where('codigo_institucion',$codigo_institucion)->first();
                    if(!$oldcat_pro_institucion instanceof CategoriaProductoInsti){
                        $cat_prod_institucion = new CategoriaProductoInsti;
                        $cat_prod_institucion->codigo_institucion = $codigo_institucion;
                        $cat_prod_institucion->codigo_categoria_producto = $categorias_producto[$k];
                        $cat_prod_institucion->codigo_estado = 8;
                        $cat_prod_institucion->save();                
                        \Log::info("Categoria Producto ".$categorias_producto[$k]."\n");
                    }                
                }                
            }
            if($activar_foro){            
                $oldcat_foro_institucion = CategoriaForoInstitucion::where('codigo_categoria_foro',13)->where('codigo_institucion',$codigo_institucion)->first();
                if(!$oldcat_foro_institucion instanceof CategoriaForoInstitucion){
                    $cat_foro_institucion = new CategoriaForoInstitucion;
                    $cat_foro_institucion->codigo_institucion = $codigo_institucion;
                    $cat_foro_institucion->codigo_categoria_foro = 13;
                    $cat_foro_institucion->codigo_estado = 8;
                    $cat_foro_institucion->save();                
                    \Log::info("Categoria Producto 13\n");
                }                
            }
            if($activar_eventos){
                
                $old_calendario = Calendario::where('codigo_institucion',$codigo_institucion)->first();
                if(!$old_calendario instanceof Calendario){
                    $calendario = new Calendario;
                    $calendario->codigo_estado = 8;
                    $calendario->nombre_calendario = "Calendario ".$codigo_institucion;
                    $calendario->descripcion_calendario = "Descripcion del calendario ".$codigo_institucion;
                    $calendario->codigo_institucion = $codigo_institucion;
                    $calendario->save();

                    $codigo_calendario = $calendario->codigo_calendario;
                }
                else{
                    $codigo_calendario = $old_calendario->codigo_calendario;
                }           

                for($k = 0; $k < count($tipos_evento); $k++){            
                    $oldtipo_even_institucion = TipoEventoCalendario::where('codigo_tipo_evento',$tipos_evento[$k])->where('codigo_calendario',$codigo_calendario)->first();
                    if(!$oldtipo_even_institucion instanceof TipoEventoCalendario){
                        $tipo_even_calend = new TipoEventoCalendario;
                        $tipo_even_calend->codigo_calendario = $codigo_calendario;
                        $tipo_even_calend->codigo_tipo_evento = $tipos_evento[$k];
                        $tipo_even_calend->codigo_estado = 8;
                        $tipo_even_calend->codigo_clase_tipo_evento = 2;
                        $tipo_even_calend->save();                
                        \Log::info("Tipo evento ".$tipos_evento[$k]."\n");
                    }                
                } 
            }
            if($activar_proyectos){
                
                $categoria_proyecto = new CategoriaProyecto;
                $categoria_proyecto->nombre_categoria_proyecto = "General ".$codigo_institucion;
                $categoria_proyecto->codigo_estado = 8;
                $categoria_proyecto->codigo_institucion = $codigo_institucion;
                $categoria_proyecto->save();                
                \Log::info("Categoria proyecto \n");

                $etapa_proyecto = new EtapaProyecto;
                $etapa_proyecto->nombre_etapa_proyecto = "Ideación";
                $etapa_proyecto->codigo_estado = 8;
                $etapa_proyecto->codigo_institucion = $codigo_institucion;
                $etapa_proyecto->save();                
                \Log::info("Etapa proyecto \n");

                for($l = 0; $l < count($clases_emprendimientos); $l++){            
                    $oldtipo_even_institucion = ClaseEmprendimientoInstitucion::where('codigo_clase_emprendimiento',$clases_emprendimientos[$l])->where('codigo_institucion',$codigo_institucion)->first();
                    if(!$oldtipo_even_institucion instanceof ClaseEmprendimientoInstitucion){
                        $clase_emprendimiento = new ClaseEmprendimientoInstitucion;
                        $clase_emprendimiento->codigo_clase_emprendimiento = $clases_emprendimientos[$l];
                        $clase_emprendimiento->codigo_estado = 8;
                        $clase_emprendimiento->codigo_institucion = $codigo_institucion;
                        $clase_emprendimiento->save();                
                        \Log::info("Clase emprendimiento ".$clases_emprendimientos[$l]."\n");
                    }                
                }                

            }
            if($activar_usuario){
                $pais_institucion = PaisInstitucion::where('codigo_pais','COL')->where('codigo_institucion',$codigo_institucion)->first();                
                if(!$pais_institucion instanceof PaisInstitucion){
                    $pais_institucion = new PaisInstitucion;
                    $pais_institucion->codigo_pais = 'COL';
                    $pais_institucion->codigo_estado = 8;
                    $pais_institucion->codigo_institucion = $codigo_institucion;
                    $pais_institucion->save();
                }

                for($l = 0; $l < count($tipos_identificacion); $l++){            
                    $oldtipo_id_institucion = TipoIdInstitucion::where('codigo_tipo_identificacion',$tipos_identificacion[$l])->where('codigo_institucion',$codigo_institucion)->first();
                    if(!$oldtipo_id_institucion instanceof TipoIdInstitucion){
                        $tipo_id_institucion = new TipoIdInstitucion;
                        $tipo_id_institucion->codigo_tipo_identificacion = $tipos_identificacion[$l];
                        $tipo_id_institucion->codigo_estado = 8;
                        $tipo_id_institucion->codigo_institucion = $codigo_institucion;
                        $tipo_id_institucion->save();                
                        \Log::info("Tipo identificacion ".$tipos_identificacion[$l]."\n");
                    }                
                } 
            }

            if($activar_blog){   
                $oldtipo_cont_institucion = TipoContInstitucion::where('codigo_tipo_contenido',12)->where('codigo_institucion',$codigo_institucion)->first();
                if(!$oldtipo_cont_institucion instanceof TipoContInstitucion){             
                    $tipo_cont_institucion = new TipoContInstitucion;
                    $tipo_cont_institucion->codigo_tipo_contenido = 12;
                    $tipo_cont_institucion->codigo_estado = 8;
                    $tipo_cont_institucion->codigo_institucion = $codigo_institucion;
                    $tipo_cont_institucion->save();                
                    \Log::info("Tipo contenido 12 \n");
                }
            }

            $rol_usuario = RolUsuario::where('user_id',45379)->where('codigo_institucion',$codigo_institucion)->first();
            if(!$rol_usuario instanceof RolUsuario){
                $rol_usuario = new RolUsuario;
                $rol_usuario->role_id = 1;
                $rol_usuario->user_id = 45379;
                $rol_usuario->codigo_institucion = $codigo_institucion;
                $rol_usuario->codigo_estado = 8;                
                $rol_usuario->save();
            }

        }else{
            dd("No existe la institucion");
        }

        dd("registro el menu");
    }
}