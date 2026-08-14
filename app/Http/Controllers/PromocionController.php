<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, DB, SendEmail;

use App\Models\Core\Promocion, App\Models\Core\DetallePromocion, App\Models\Core\Estado, App\Models\Seguridad\Usuario;

class PromocionController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
			return Promocion::promotions( $request->all() );
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('pending')], $filter = null);
        return view('marketplace.promotion.index', compact('states'));
    }

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
   $promotion = Promocion::find($id);
   if(!$promotion instanceof Promocion) {
    throw new \Exception("Promoción no encontrada {$id}");
   }

   Log::info("Promotion update: id=$id, tipo_promocion=" . $promotion->tipo_promocion);

   $detailPromotion = DetallePromocion::where('codigo_promocion', $promotion->codigo_promocion)->first();
   Log::info("Detail exists: " . ($detailPromotion ? 'yes' : 'no'));

   if( $promotion->tipo_promocion == Promocion::getTypePromo('promotion')) {
    if(!$detailPromotion instanceof DetallePromocion) {
    	throw new \Exception("Detalle Promoción no encontrado {$id}");
    }
   }

			$user = Usuario::join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')->where('codigo_usuario_institucion', $promotion->codigo_usuario_institucion)->first();
			if(!$user instanceof Usuario){
				throw new Exception("El usuario institución no existe {$promotion->codigo_usuario_institucion}");
			}

            DB::beginTransaction();
            try {
				if($promotion->tipo_promocion == Promocion::getTypePromo('promotion')){
					$tipo = 'La promoción';
				}

				if($promotion->tipo_promocion == Promocion::getTypePromo('advertisement')){
					$tipo = 'El anuncio';
				}

				if($request->has('change_state')){
					switch($request->get('action')){
						case 'aprobar':
							$message = 'aprobado';
							$codigo_estado = Estado::getState('active');
							$activityLog = 'activitylog.promotion.approve';
							break;
						case 'rechazar':
							$message = 'rechazado';
							$codigo_estado = Estado::getState('pending');
							$activityLog = 'activitylog.promotion.back';

							// Send mail refused product
                            $data = [];
                            $data['_template'] = 'emails.marketplace.promotionrefused';
                            $data['_inst'] = session('institution')->codigo_institucion;
                            $data['_subject'] = 'Devolución producto';
                            $data['_to'] = $user->correo_electronico;
                            $data['_to_name'] = $user->nombre_usuario;
                            $data['_promotion'] = $promotion->codigo_promocion;
                            $data['_comment'] = $request->comment_refused_promotion;
                            SendEmail::notification($data);
							break;
						case 'activar':
							$message = 'activado';
							$codigo_estado = Estado::getState('active');
							$activityLog = 'activitylog.promotion.activate';
						break;
						case 'desactivar':
							$message = 'desactivado';
							$codigo_estado = Estado::getState('inactive');
							$activityLog = 'activitylog.promotion.inactive';
						break;
					}
				}

				$promotion->codigo_estado = $codigo_estado;
				$promotion->save();
				Log::info("Saved promotion estado: " . $promotion->codigo_estado);

				if ($detailPromotion instanceof DetallePromocion) {
					$detailPromotion->codigo_estado = $codigo_estado;
					$detailPromotion->save();
					Log::info("Saved detail estado: " . $detailPromotion->codigo_estado);
				}

				activity()
					->performedOn($promotion)
					->withProperties([
						'attributes' => [
							'title' => $tipo,
							'promotion_id' => $promotion->codigo_promocion
						] ])
					->log($activityLog);

                // Commit Transaction
                DB::commit();
                $estado = $detailPromotion instanceof DetallePromocion ? $detailPromotion->codigo_estado : $promotion->codigo_estado;
                return response()->json(['success' => true, 'message' => "{$tipo} se ha {$message} correctamente.", 'codigo_estado' => $estado]);            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'PromocionController:update', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        abort(404);
    }

	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		if(!is_numeric($id)){
			abort(404);
		}

		$promotion = Promocion::find($id);
		if(!$promotion instanceof Promocion) {
			abort(404);
		}

		if($promotion->tipo_promocion == Promocion::getTypePromo('promotion')){
			$type = 'Promoción';
		}

		if($promotion->tipo_promocion == Promocion::getTypePromo('advertisement')){
			$type = 'Anuncio';
		}

        return view('marketplace.promotion.detail', compact('promotion', 'type'));
    }
}
