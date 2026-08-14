<?php

namespace App\Http\Controllers;

use App\Models\Core\AcuerdoCompra;
use App\Models\Core\Adjunto;
use App\Models\Seguridad\Usuario;
use App\Models\Core\TipoAdjunto;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;
use DB, Log, Auth, Lang, Validator;

use App\Models\Core\Compra, App\Models\Core\EvaluacionCompra, App\Models\Core\RespuestaEvalCompra, App\Models\Core\ItemEvaluacionCompra, App\Models\Core\Estado, App\Models\Core\EntregableAcuerdo;

class EvaluacionCompraController extends Controller
{
	/**
	 * Instantiate a new Controller instance.
	 */
	public function __construct()
	{
		$this->middleware('ability:admin|super-admin|administrativo,view,marketplace/transacciones/acuerdo');
		$this->middleware('ability:admin|super-admin|administrativo,create,marketplace/transacciones/acuerdo', ['only' => ['create', 'store']]);
		$this->middleware('ability:admin|super-admin|administrativo,edit,marketplace/transacciones/acuerdo', ['only' => ['edit', 'update']]);
		$this->middleware('ability:admin|super-admin|administrativo,remove,marketplace/transacciones/acuerdo', ['only' => ['destroy']]);
		$this->middleware('ability:admin|super-admin|administrativo,export,marketplace/transacciones/acuerdo', ['only' => ['exportar']]);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(Request $request, $id)
    {
		if($request->ajax()) {
			$evaluation = EvaluacionCompra::getEvalPurchase($id);
			return response()->json(EvaluacionCompra::buildEval($evaluation));
		}

		$purchase = Compra::getPurchase($id);
		if($purchase instanceof Compra) {
      $entregables = EntregableAcuerdo::getDeliverableAttachment($purchase->codigo_acuerdo_compra);
      $proveedor = EvaluacionCompra::getEvalPurchaseItem($id, Compra::getTypeUser('seller'));
      $comprador = EvaluacionCompra::getEvalPurchaseItem($id, Compra::getTypeUser('purchaser'));
      $proveedorRecomendacion = EvaluacionCompra::getRecomendationPurchase($id);
      $proveedorAprove = EvaluacionCompra::getAprovePurchase($id);
      $entregableFinal = EntregableAcuerdo::getDeliverableFinal($purchase->codigo_acuerdo_compra);
      $entregableFactura = EntregableAcuerdo::getDeliverableBill($purchase->codigo_acuerdo_compra);
      $entregableCamara = EntregableAcuerdo::getCertificateComercio($purchase->codigo_acuerdo_compra);
      $agreement = AcuerdoCompra::getAgreement($purchase->codigo_compra);
      $score_general_service = Usuario::find($purchase->seg_codigo_usuario)->promedio_evaluacion_compra_usuario;
      // dd($proveedor->evaluacioncompra->cometario_evaluacion_compra);
    //  dd($entregables);

			return view('marketplace.evaluation.main', compact('purchase', 'entregables', 'proveedor', 'comprador', 'entregableFinal', 'entregableFactura', 'agreement', 'score_general_service', 'entregableCamara', 'proveedorRecomendacion', 'proveedorAprove'));
		}
		abort(404);
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		if($request->ajax()){
			$data = $request->all();

			$evaluacionCompra = EvaluacionCompra::getEvalPurchase($id);
			if(!$evaluacionCompra instanceof EvaluacionCompra) {
				$evaluacionCompra = new EvaluacionCompra;
			}

			if ($evaluacionCompra->isValid($data))
			{

        DB::beginTransaction();
				try {
					// Purchase Evaluation
					$evaluacionCompra->fill($data);
					$evaluacionCompra->codigo_compra 				= $request->codigo_compra;
					$evaluacionCompra->codigo_estado 				= Estado::getState('active');
					$evaluacionCompra->fecha_evaluacion_compra 		= date("Y-m-d H:i:s");
					$evaluacionCompra->tipo_comentario_evaluacion 	= EvaluacionCompra::getType('internal');
					$evaluacionCompra->tipo_evaluacion_compra 		= Compra::getTypeUser('admin');
					$evaluacionCompra->save();

					$items = ItemEvaluacionCompra::getItemEval(Compra::getTypeUser('purchaser'));
					foreach($items as $itemeval)
					{
						if($request->has("evaluacion_item_compra_$itemeval->codigo_item_evaluacion_compra")) {
							$respuestaEvalCompra = RespuestaEvalCompra::getResEval($evaluacionCompra->codigo_evaluacion_compra, $itemeval->codigo_item_evaluacion_compra);
							if(!$respuestaEvalCompra instanceof RespuestaEvalCompra){
								$respuestaEvalCompra = new RespuestaEvalCompra();
							}

							$respuestaEvalCompra->codigo_item_evaluacion_compra	= $itemeval->codigo_item_evaluacion_compra;
							$respuestaEvalCompra->codigo_evaluacion_compra 		= $evaluacionCompra->codigo_evaluacion_compra;
							$respuestaEvalCompra->valor_respuesta_eval_compra 	= $request->get("evaluacion_item_compra_$itemeval->codigo_item_evaluacion_compra");
							$respuestaEvalCompra->save();
						}
					}

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true]);
				} catch (Exception $e) {
					DB::rollback();
					Log::error($e->getMessage());
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $evaluacionCompra->errors]);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Delete Bill @backend01

        if ($request->ajax()) {
          if ($request->has('codigo_compra') && $request->has('delete_bill')) {
            $compra = $request->codigo_compra;

            AcuerdoCompra::where('codigo_compra', $compra)
              ->update(['cor_codigo_adjunto' => null]);
          }

          return response()->json(['success' => true]);
        }
    }

    public function bill(Request $request) {
      if ($request->ajax()) {
        if ($request->has('codigo_compra')) {
          $codigo_compra = $request->codigo_compra;
          return response()->json(EvaluacionCompra::getBill($codigo_compra));
        }
      }
    }

    public function billUploader(Request $request) {
      if ($request->ajax()) {
        if ($request->hasFile('file')) {
          try {
            $file = $request->file('file');
            $request->has('codigo_compra') ? $compra = $request->codigo_compra : $compra = null;
            if ($compra) {
              $result = Adjunto::upload($file->getRealPath(), $file->getClientOriginalName(), 'documents', null, $compra);

              $attachment = new Adjunto();
              $attachment->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('document');
              $attachment->direccion_adjunto = $result->file_path;
              $attachment->nombre_adjunto = $file->getClientOriginalName();
              $attachment->fecha_adjunto = date('Y-m-d H:i:s');
              $attachment->save();

              AcuerdoCompra::where('codigo_compra', $compra)
                ->update(['cor_codigo_adjunto' => $attachment->codigo_adjunto]);
            }
            return response()->json(['success' => true, 'file_path' => $result->file_path]);
          } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
          }
        }
      }
    }
}
