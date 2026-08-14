<?php

namespace App\Custom;

use DB, Lang, App, Log, SendEmail, Response, Auth, TuProyecto, AWS, Datatables;

use App\Models\Core\{Convocatoria, CategoriaProducto,
    Estado, Fase,
    RegistroFaseUsuario, RespuestaEncuesta,
    Pago, TipoRecursoFase,
    UsuarioInstitucion};
    
use App\Models\Seguridad\{Rol, Usuario};

use App\Models\Aprende\InscripcionCurso;

class Rutan
{
    public static function registerToPhase(Fase $phase, Usuario $dataUser, RegistroFaseUsuario $registerPhase, $request)
    {
        switch ($phase->codigo_fase) {
            case self::getIdPhase('ideacion_ruta_emprendimiento'):
                if($request->turn || $request->data['turn']){
                    //update current phase
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert new Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = self::getIdPhase('ideacion_salida_ruta_emprendimiento');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();

                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case self::getIdPhase('preincubacion_ruta_emprendimiento'):
                if($request->turn || $request->data['turn']){
                    //update current phase
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert new Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = self::getIdPhase('preincubacion_salida_ruta_emprendimiento');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();

                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case self::getIdPhase('incubacion_ruta_emprendimiento'):
                if($request->turn || $request->data['turn']){
                    //update current phase
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert new Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = self::getIdPhase('incubacion_salida_ruta_emprendimiento');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();

                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case self::getIdPhase('aceleracion_ruta_emprendimiento'):
                if($request->turn || $request->data['turn']){
                    //update current phase
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert new Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = self::getIdPhase('aceleracion_salida_ruta_emprendimiento');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();

                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case self::getIdPhase('consolidacion_ruta_emprendimiento'):
                if($request->turn || $request->data['turn']){
                    //update current phase
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert new Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = self::getIdPhase('consolidacion_salida_ruta_emprendimiento');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();

                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;

            default:
                if($request->turn || $request->data['turn']){
                    return 'No hay mas fases';
                }
        }
    }

    public static function getIdPhase(string $phase): int
	{
    	switch ($phase) :
		    case 'registro_ruta_emprendimiento': return 345;
            //phases de entrada
		    case 'ideacion_ruta_emprendimiento': return 350;
		    case 'preincubacion_ruta_emprendimiento': return 351;
		    case 'incubacion_ruta_emprendimiento': return 352;
		    case 'aceleracion_ruta_emprendimiento': return 353;
		    case 'consolidacion_ruta_emprendimiento': return 354;
            
		    case 'sin_base_tecnologica_ruta_emprendimiento': return 355;
		    case 'na_edad_ruta_emprendimiento': return 356;
		    case 'na_ciudad_ruta_emprendimiento': return 357;
		    case 'laboratorio_innovacion_ruta_emprendimiento': return 364;
            //phases de salida
		    case 'ideacion_salida_ruta_emprendimiento': return 358;
		    case 'preincubacion_salida_ruta_emprendimiento': return 359;
		    case 'incubacion_salida_ruta_emprendimiento': return 360;
		    case 'aceleracion_salida_ruta_emprendimiento': return 361;
		    case 'consolidacion_salida_ruta_emprendimiento': return 362;
			default: return 'Not phase identified';
		endswitch;
    }
    /**
     * Obtiene datos del gestor del voucher.
     * @access public
     * @param int $id_user
     * @return object Usuario&Persona
     */
    public static function getManagerVoucher(int $id_user)
    {
        $query = Usuario::query();
        $query->join('core.persona as p', 'usuario.codigo_usuario', '=', 'p.codigo_usuario');
        $query->where('usuario.codigo_usuario', $id_user);
        $manager_voucher = $query->first();
        return $manager_voucher;

    }
        /**
     * Obtiene proveedores.
     * @access public
     * @param array $id_category_product
     * @return object Usuario&CategoriaProducto
     */
    public static function getProviders(array $id_category_product)
    {
        $query = CategoriaProducto::query();
        $query->select('u.nombre_usuario', 'categoria_producto.nombre_categoria_producto');
        $query->join('core.categoria_producto_insti as ctpi', 'categoria_producto.codigo_categoria_producto', '=', 'ctpi.codigo_categoria_producto');
        $query->leftJoin('core.producto as p', 'ctpi.codigo_categoria_prod_insti', '=', 'p.codigo_categoria_prod_insti');
        $query->leftJoin('seguridad.usuario as u', 'p.seg_codigo_usuario', 'u.codigo_usuario' );
        $query->whereIn('categoria_producto.codigo_categoria_producto', $id_category_product);
        $data = $query->get();

        return $data;
    }
}
