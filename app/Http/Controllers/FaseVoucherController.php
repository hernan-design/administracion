<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log, View, Auth, Datatables, TuProyecto, stdClass, SendEmail;

use App\Models\Core\CategoriaProducto,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\RecomProductoEval,
    App\Models\Core\Estado,
    App\Models\Core\Entidad,
    App\Models\Core\ConvocatoriaMentoria,
    App\Models\Seguridad\Usuario,
    App\Models\Core\ComenVoucher,
    App\Models\Aprende\Mentoria,
    App\Models\Core\CategoriaProductoSesion,
    App\Models\Core\DetalleVoucher,
    App\Models\Core\Voucher;

use App\Custom\Rutan;

class FaseVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

	    list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);
        $codigo_mentoria = $request->codigo_mentoria;
        $query = CategoriaProducto::select(
            'cpi.codigo_categoria_producto',
            'nombre_categoria_producto',
            'rpe.codigo_categoria_producto as categoria_producto',
            'cv.comentario as observaciones'
        );
        $query->join('core.categoria_producto_insti as cpi', 'cpi.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');
        $query->leftJoin('core.recom_producto_eval as rpe', function ($join ) use ($codigo_registro_fase_usuario, $codigo_mentoria) {
            $join->on('rpe.codigo_categoria_producto', '=', 'cpi.codigo_categoria_producto')
                ->where('rpe.codigo_registro_fase_usuario', '=', $codigo_registro_fase_usuario)
                ->where('rpe.codigo_mentoria', '=', $codigo_mentoria);
        });
        $query->leftJoin('core.comen_voucher as cv', function ($join ) use ($codigo_registro_fase_usuario, $codigo_mentoria) {
            $join->on('cv.codigo_registro_fase_usuario', '=', 'rpe.codigo_registro_fase_usuario')
                ->where('cv.codigo_mentoria', '=', $codigo_mentoria)
                ->where('cv.codigo_grupo_item_eval', '=', 64);
        });
        $query->where('cpi.codigo_institucion', 61);
        $query->where('cpi.codigo_estado',  8);
        $query = $query->get();
        return Datatables::of($query)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {//dd('show', $id);

        return view('program.assignmentvoucher');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * guarda/actualiza vouchers asignados
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	    list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);

        if (is_numeric($request->codigo_categoria_producto)) {
            $codigo_categoria_producto = array($request->codigo_categoria_producto);
        } elseif (is_array($request->codigo_categoria_producto)) {
            $codigo_categoria_producto = $request->codigo_categoria_producto;
        } elseif (empty($request->codigo_categoria_producto)) {
            $codigo_categoria_producto = array();
        }else {
            return response()->json(['success' => false, 'errors' => 'Intentalo nuevamente o contacta al admin.']);
        }

        $registerPhase = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
        if(!$registerPhase instanceof RegistroFaseUsuario) {
            abort(404);
        }

        //validamos si hay un proceso abierto, el cual deb cerrarse al momento de 
        //aprobar entregables por admin
        $categoriaProductoSesion = CategoriaProductoSesion::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
                                        ->where('codigo_estado', Estado::getState('active'))
                                        ->first();

        if ($categoriaProductoSesion instanceof CategoriaProductoSesion) {
            return response()->json(['success' => false, 'errors' => 'Hay un proceso activo.']);
        }

        DB::beginTransaction();
        try {

                //guardar Observación y comentarios de vouchers
                if($request->has("comentario")) {

                    $comentDoc = ComenVoucher::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
                                                    ->where('codigo_mentoria', $request->sessionmentoria)
                                                    ->where('codigo_grupo_item_eval', 64) //observaciones
                                                    ->first();

                    if($comentDoc instanceof ComenVoucher) {
                        // Update Coment Document
                        ComenVoucher::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
                                            ->where('codigo_mentoria', $request->input('sessionmentoria'))
                                            ->where('codigo_grupo_item_eval', 64) //observaciones
                                            ->update([
                                                    'comentario' => $request->input('comentario'),
                                                ]);
                    }else{
                        // Coment Documenter
                        $comentDoc = new ComenVoucher();
                        $comentDoc->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
                        $comentDoc->comentario = $request->input('comentario');
                        $comentDoc->codigo_mentoria = $request->input('sessionmentoria');
                        $comentDoc->codigo_grupo_item_eval = 64;//observaciones
                        $comentDoc->save();
                    }

                }

                $categories = $this->getCategories();

				RecomProductoEval::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->delete();
                //actualizo CategoriaProductoSesion y asi poder mostrar los voucher asignados
                //en las distintas sesiones
                // CategoriaProductoSesion::where('codigo_estado', 13)
                                        // ->update([
                                        //     'codigo_registro_fase_usuario' => $registerPhase->codigo_registro_fase_usuario,
                                        //     'codigo_mentoria' => $request->input('sessionmentoria')
                                        // ]);

                $categoria_sesion = new CategoriaProductoSesion;
                $categoria_sesion->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
                $categoria_sesion->codigo_estado = Estado::getState('active');
                $categoria_sesion->codigo_mentoria = $request->input('sessionmentoria');
                
                $vouchers = [];
                $id_category_product = [];
				foreach ($categories as $item) {

                    foreach ($codigo_categoria_producto as $cat_item) {
                        if($cat_item == $item->codigo_categoria_producto){

                            $vouchers[] = $item->nombre_categoria_producto;
                            $id_category_product[] = $item->codigo_categoria_producto;

                            $recom = new RecomProductoEval;
							$recom->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
							$recom->codigo_estado = Estado::getState('active');
							$recom->codigo_categoria_producto = (int) $cat_item;
							$recom->codigo_mentoria = $request->input('sessionmentoria');
							$recom->save();

                            // save CategoriaProductoSesion
                            $categoria_sesion->codigo_categoria_producto = (int) $cat_item;
                            $categoria_sesion->save();

						}
					}
				}

                //si no guarda categorias CategoriaProductoSesion
                //con codigo_categoria_producto=null
                if (empty($vouchers)) {
                    $categoria_sesion->save();
                }

                $mentoria = Mentoria::query();
                $mentoria->join('core.comen_voucher', 'comen_voucher.codigo_mentoria', '=', 'mentoria.codigo_mentoria');
                $mentoria->where('codigo_registro_fase_usuario',  $registerPhase->codigo_registro_fase_usuario);
                $mentoria->where('mentoria.codigo_mentoria', $request->input('sessionmentoria'));
                $mentoria = $mentoria->first();

                if (!empty($vouchers)) {

                    $usuario_entidad = Usuario::getUserByEntity($registerPhase->codigo_usuario);
                    DetalleVoucher::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
                                                        ->where('codigo_usuario', $usuario_entidad->owner_id)
                                                        ->update([
                                                            'codigo_estado' => Estado::getState('inactive')
                                                        ]);

                    $detalleVoucher = new DetalleVoucher;
                    $detalleVoucher->codigo_estado = Estado::getState('active');
                    $detalleVoucher->codigo_usuario = $usuario_entidad->owner_id;
                    $detalleVoucher->codigo_voucher = Voucher::getVoucher('rutan');
                    $detalleVoucher->fecha_detalle_voucher = date('Y-m-d');
                    $detalleVoucher->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
                    $detalleVoucher->valor_detalle_voucher = count($vouchers);
                    $detalleVoucher->saldo_detalle_voucher = count($vouchers);
                    $detalleVoucher->save();

                    $manager_voucher = Rutan::getManagerVoucher(Auth::user()->codigo_usuario);
                    $name_manager_vocuher = $manager_voucher->nombre_usuario;
                    $fir_last_name_manager_vocuher = " " . $manager_voucher->primer_apellido_persona;
                    $sec_last_name_manager_vocuher = $manager_voucher->segundo_apellido_persona ? " " . $manager_voucher->segundo_apellido_persona :  "";
                    $full_name_manager_voucher = $name_manager_vocuher . $fir_last_name_manager_vocuher . $sec_last_name_manager_vocuher;

                    $providers = Rutan::getProviders($id_category_product);

                    //send email to emprendedor
                    $data = [];
                    $data['_template'] = 'emails.program.rutan.notificationvoucherentrepreneur';
                    $data['_subject'] = Lang::get('rutan.emails.voucher', ['entity' => $usuario_entidad->entity_name]);
                    $data['_to'] = $mentoria->usuario->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($mentoria->usuario->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['_entity_name'] = $usuario_entidad->entity_name;
                    $data['_manager_voucher_name'] = $full_name_manager_voucher;
                    $data['_manager_voucher_email'] = $manager_voucher->correo_electronico;
                    $data['_manager_voucher_celphone'] = $manager_voucher->movil_usuario;
                    //$data['vouchers'] = $vouchers;
                    $data['_providers'] = $providers;
                    SendEmail::notification($data);

                    //send email to mentor
                    $data = [];
                    $data['_template'] = 'emails.program.rutan.notificationvouchermentor';
                    $data['_subject'] = 'Asignación de voucher';
                    $data['_to'] = $mentoria->mentor->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($mentoria->mentor->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['vouchers'] = $vouchers;
                    SendEmail::notification($data);
                }

                $response = new stdClass;
				$response->success = true;
                $response->message = 'Vouchers actualizados con éxito.';

				// Commit Transaction
				DB::commit();
				return response()->json($response);
            } catch (\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'Actualizacion vouchers', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => 'Ocurrió un error al momento de al actualizar vouchers']);
			}
    }

    /**
     * obtener caterogiras de Rutan
     *
     * @param  int  $id
     * @return CategoriaProducto
     */
    private function getCategories()
    {
        $query = CategoriaProducto::select(
            'categoria_producto_insti.codigo_categoria_producto',
            'nombre_categoria_producto'
        );
        $query->join('core.categoria_producto_insti', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');
        $query->where('codigo_institucion', 61);
        $query->where('codigo_estado',  8);
        return $query->get();
    }

	/**
	 * obtener procesos de mentorias
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public static function getMentorignsPhase(Request $request)
    {

		if ($request->ajax()) {

            list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);
            $registerPhase = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
            if(!$registerPhase instanceof RegistroFaseUsuario) {
                abort(404);
            }

            $user = Usuario::find($registerPhase->codigo_usuario);
			if(!$user instanceof Usuario) {
				abort(404);
			}

			$entity = Entidad::getEntityUrl($user->nickname_usuario);
			if(!$entity instanceof Entidad) {
				abort(404);
			}

			// $user = Usuario::find($entity->seg_codigo_usuario);
			// if(!$user instanceof Usuario) {
			// 	abort(404);
			// }

            //esta consulta solo muestra procesos de mentorias finalizados y
            //donde se recomendó voucher
			$query = ConvocatoriaMentoria::select(
													'convocatoria_mentoria.codigo_usuario',
													'convocatoria_mentoria.seg_codigo_usuario',
													'convocatoria_mentoria.codigo_asesoria',
													'fase.nombre_fase',
													'asesoria_institucion.codigo_estado',
                                                    'mentoria.codigo_mentoria',
                                                    'mentoria.codigo_estado as estado_sesion',
                                                    'cps.codigo_categoria_producto_sesion',
                                                    'observacion.comentario as observacion'
												)
                                            ->join('core.fase', 'fase.codigo_fase', '=', 'convocatoria_mentoria.codigo_fase')
                                            ->join('aprende.asesoria_institucion', 'asesoria_institucion.codigo_asesoria', '=', 'convocatoria_mentoria.codigo_asesoria')
                                            ->leftJoin('aprende.mentoria', 'mentoria.codigo_asesoria', '=', 'convocatoria_mentoria.codigo_asesoria')
                                            ->leftJoin('core.categoria_producto_sesion as cps', 'cps.codigo_mentoria', '=', 'mentoria.codigo_mentoria')
                                            ->leftJoin('core.comen_voucher as observacion', function ($join) {
                                                    $join->on('observacion.codigo_mentoria', '=', 'mentoria.codigo_mentoria')
                                                        ->where('observacion.codigo_grupo_item_eval', 64);
                                            })
                                            ->where('convocatoria_mentoria.codigo_convocatoria', 634)
											->where('convocatoria_mentoria.codigo_usuario', $entity->codigo_usuario)
											->where('mentoria.codigo_estado', Estado::getState('closed'))
											->whereNotNull('mentoria.asignacion_voucher')
                                            ->whereRaw(" mentoria.asignacion_voucher != '' ")
											->orderBy('codigo_convocatoria_mentoria', 'desc')
											->orderBy('mentoria.codigo_mentoria', 'desc')
                                            // ->limit(1)
											->get();
                                            //dd($query);
			return Datatables::of($query)
				->addColumn('nombre_mentor', function ($data) {
					return $data->mentor->nombre_usuario . ' ' . $data->mentor->persona->primer_apellido_persona;
				})
				->addColumn('correo_mentor', function ($data) {
					return $data->mentor->correo_electronico;
				})
				->make(true);
		}
	}

    /**
     * guarda comentario cuando no se seigna voucher
	 * @param  \Illuminate\Http\Request  $request
     *
     */
    public function storeComment(Request $request)
    {

        list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);
        $registerPhase = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
        if(!$registerPhase instanceof RegistroFaseUsuario) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            if($request->has("comentario_19")) {

                $comentDoc = ComenVoucher::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
                                                    ->where('codigo_grupo_item_eval', 63) //comentarios
                                                    ->where('codigo_mentoria', $request->sessionmentoria)
                                                    ->first();

                if($comentDoc instanceof ComenVoucher) {
                    // Update Coment Document
                    ComenVoucher::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
                                            ->where('codigo_mentoria', $request->input('sessionmentoria'))
                                            ->where('codigo_grupo_item_eval', 63) //comentarios
                                            ->update([
                                                    'comentario' => $request->input('comentario_19')
                                                ]);
                }else{
                    // Coment Documenter
                    $comentDoc = new ComenVoucher();
                    $comentDoc->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
                    $comentDoc->codigo_grupo_item_eval = 63; //comentarios
                    $comentDoc->comentario = $request->input('comentario_19');
                    $comentDoc->codigo_mentoria = $request->input('sessionmentoria');
                    $comentDoc->save();
                }

                $mentoria = Mentoria::query();
                $mentoria->join('core.comen_voucher', 'comen_voucher.codigo_mentoria', '=', 'mentoria.codigo_mentoria');
                $mentoria->where('codigo_registro_fase_usuario',  $registerPhase->codigo_registro_fase_usuario);
                $mentoria->where('mentoria.codigo_mentoria', $request->input('sessionmentoria'));
                $mentoria = $mentoria->first();
                \Log::info('repite');
                //send email to mentor
                $data = [];
                $data['_template'] = 'emails.program.rutan.notificationnovoucher';
                $data['_subject'] = 'Asignación de voucher';
                $data['_to'] = $mentoria->mentor->correo_electronico;
                $data['_to_name'] = ucwords(strtolower($mentoria->mentor->nombre_usuario));
                $data['_inst'] = session('institution')->codigo_institucion;
                $data['comment'] = $request->input('comentario_19');
                SendEmail::notification($data);

            }

            // Commit Transaction
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Se envió correo con el comentario al mentor']);
        } catch (\Exception $e){
            DB::rollback();
            Log::error(sprintf('%s:%s', 'storeComment error', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => 'Ocurrió un error al momento de guardar el comentario']);
        }
    }

}
