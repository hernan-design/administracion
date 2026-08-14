<?php

namespace App\Http\Controllers;

use App\Models\Core\Discusion;
use App\Models\Core\DiscusionAcuerdoCompra;
use App\Models\Core\EvaluacionEmprendimiento;
use App\Models\Param\DiscusionInstitucion;
use App\Models\Seguridad\Modulo;
use App\RegistroSesion;
use Illuminate\Http\Request;
use App\Exports\ReportsDownloadExport;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log, AWS, SendEmail, Response, Auth, Excel;

use App\Models\Core\Compra, App\Models\Core\Estado, App\Models\Seguridad\Usuario, App\Models\Core\Voucher, App\Models\Param\Institucion, App\Models\Core\Producto, App\Models\Core\AcuerdoCompra;

use Custom\Innpulsa;
use TuProyecto;

class CompraController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,marketplace/transacciones');
        $this->middleware('ability:admin|super-admin|administrativo,create,marketplace/transacciones', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,marketplace/transacciones/acuerdo', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,marketplace/transacciones', ['only' => ['destroy']]);
        $this->middleware('ability:admin|super-admin|administrativo,export,marketplace/transacciones', ['only' => ['exportar']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()) {
        return Compra::getDataPurchases( $request->all() );
      }

      $statistics = [];
      switch (session('institution')->codigo_institucion) {
        case Institucion::institution('innpulsa');
          $statistics = Innpulsa::getStatisticsMarketplace();
        break;

        default:
          $statistics = [];
        break;
      }

		  return view('marketplace.main', compact('statistics'));
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
		if($request->ajax()){
			$purchase = Compra::getPurchase($id);
      $encryptPurchase = TuProyecto::encryptSrcParams([$purchase->codigo_compra]);
      $purchase['codigo_compra_encrypt'] = $encryptPurchase;
			return response()->json($purchase);
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
			$purchase = Compra::find($id);
			if(!$purchase instanceof Compra){
				throw new \Exception("Compra no encontrada {$id}");
			}

			$purchaseAgreement = AcuerdoCompra::where('codigo_compra', $purchase->codigo_compra)->first();
			if(!$purchaseAgreement instanceof AcuerdoCompra){
				throw new \Exception("Acuerdo Compra no encontrada {$purchase->codigo_compra}");
			}

			$purchaser = Usuario::find($purchase->codigo_usuario);
			if(!$purchaser instanceof Usuario){
				throw new \Exception("Empresario no encontrado {$purchase->codigo_usuario}");
			}

			$seller = Usuario::find($purchase->seg_codigo_usuario);
			if(!$seller instanceof Usuario){
				throw new \Exception("Asesor no encontrado {$purchase->seg_codigo_usuario}");
			}

			$product = Producto::find($purchase->codigo_producto);
			if(!$product instanceof Producto){
				throw new \Exception("Producto no encontrado {$purchase->codigo_producto}");
			}

			DB::beginTransaction();
			try {
				if($request->refused_deliverables){
					$purchase->codigo_estado = Estado::getState('confirmed');

					// Change state agreement
					$purchaseAgreement->codigo_estado = Estado::getState('rejection_of_deliverables');

					$module = Modulo::find($request->module);
					if(!$module instanceof Modulo) {
						abort(403);
					}

					$discussion = new Discusion();
					$discussion->codigo_estado = Estado::getState('active');
					$discussion->codigo_usuario = Auth::user()->codigo_usuario;
					$discussion->texto_discucion = $request->comment_refused_deliverables;
					$discussion->codigo_modulo = $module->codigo_modulo;
					$discussion->fecha_discucion = date('Y-m-d H:i:s');
					$discussion->save();

					$discussionInst = new DiscusionInstitucion();
					$discussionInst->codigo_discucion = $discussion->codigo_discucion;
					$discussionInst->codigo_institucion = session('institution')->codigo_institucion;
					$discussionInst->codigo_estado = Estado::getState('active');
					$discussionInst->save();

					$resourceDiscussion = new DiscusionAcuerdoCompra();
					$resourceDiscussion->codigo_acuerdo_compra = $request->codigo_acuerdo_compra;
					$resourceDiscussion->codigo_discucion = $discussion->codigo_discucion;
					$resourceDiscussion->codigo_estado = Estado::getState('active');
					$resourceDiscussion->fecha_discusion = date('Y-m-d H:i:s');
					$resourceDiscussion->save();

					$message = 'Se ha enviado la solicitud correctamente.';
					Innpulsa::sendEmailConfirmDeliverables($purchase, $purchaser, $seller, $product, $request->comment_refused_deliverables, $request->refused_deliverables, null);
				}

				if($request->approve_deliverables) {
					$queryUserAssigned = AcuerdoCompra::query();
					$queryUserAssigned->select('asignacion_marketplace.codigo_usuario');
					$queryUserAssigned->join('core.consumo_voucher', 'consumo_voucher.codigo_compra', '=', 'acuerdo_compra.codigo_compra');
					$queryUserAssigned->join('core.detalle_voucher', 'detalle_voucher.codigo_detalle_voucher', '=', 'consumo_voucher.codigo_detalle_voucher');
					$queryUserAssigned->join('core.carta_compormiso_compra', 'carta_compormiso_compra.codigo_registro_fase_usuario', '=', 'detalle_voucher.codigo_registro_fase_usuario');
					$queryUserAssigned->join('core.asignacion_marketplace', 'asignacion_marketplace.codigo_registro_fase_usuario', '=','carta_compormiso_compra.codigo_registro_fase_usuario');
					$queryUserAssigned->where('acuerdo_compra.codigo_compra', '=', $purchase->codigo_compra);
					$queryUserAssigned->where('asignacion_marketplace.codigo_estado', '=', Estado::getState('active'));
					$dataUserAssigned = $queryUserAssigned->first();

				/*	if ($dataUserAssigned instanceof AcuerdoCompra) {*/
						$purchaseAgreement->fecha_aprobacion_entregables = date('Y-m-d');
						$purchase->codigo_estado = Estado::getState('finalized');
						$purchaseAgreement->codigo_estado = Estado::getState('finalized');

						$purchaseAgreement->save();
						$encryptPurchase = TuProyecto::encryptSrcParams([$id]);

						$data = EvaluacionEmprendimiento::exportarEvaluacion($encryptPurchase);

						$fileAgreement = EvaluacionEmprendimiento::getFileAgreement($purchase->codigo_compra);

						$pdf = App::make('dompdf.wrapper');
						$pdf->loadHTML(view('reports.marketplace.innpulsa.aprobacionentregables', $data));


						$s3 = AWS::createClient('s3');

						$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

						$filename = sprintf('%s_%s.pdf', 'acta_aprobacion_entregables', $purchaseAgreement->codigo_acuerdo_compra);
						$key = sprintf('%s/%s/%s', 'documents', 'acuerdos', $filename);

						$result = $s3->putObject([
							'Bucket' => $bucket,
							'Key' => $key,
							'Body' => $pdf->output(),
							'ACL' => 'public-read'
						]);

						$message = 'Se ha aceptado los entregables correctamente.';

						if ($fileAgreement instanceof AcuerdoCompra) {

							Innpulsa::sendEmailConfirmDeliverables($purchase, $purchaser, $seller, $product, $request->comment_refused_deliverables, null, $result, $fileAgreement);

						} else {

							Innpulsa::sendEmailConfirmDeliverables($purchase, $purchaser, $seller, $product, $request->comment_refused_deliverables, null, $result, null);
						}

					/*} else {
						return response()->json(['success' => false, 'errors' => transtp('marketplace.exception_assign')]);
					}*/
				}

				if ($request->has('num_factura')) {
					if ($purchaseAgreement->codigo_estado ==  Estado::getState('finalized')) {
						$purchaseAgreement->numero_factura = $request->num_factura;
						$purchaseAgreement->fecha_radicacion_factura = $request->fecha_rad_factura;
						$purchaseAgreement->fecha_gestion_factura = $request->fecha_gestion_factura;
						$purchaseAgreement->numero_radicacion = $request->num_radic_factura;
						$purchaseAgreement->valor_factura = $request->val_factura;

						$message = 'Se ha añadido la factura correctamente.';
					}
				}

				$purchase->save();
				$purchaseAgreement->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => $message, 'codigo_estado' => $purchase->codigo_estado, 'state_agreement' => $purchaseAgreement->codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CompraController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	/**
	 * Export pdf the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function exportar(Request $request)
	{
		$user = Usuario::find(Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando usuario en sesión.");
		}

		$query = Compra::query();
		$query->select('compra.codigo_compra','nombre_producto', 'fecha_compra', 'acuerdo_compra.codigo_estado as state_agreement','estado.nombre_estado as state_purchase',
			'buyer.movil_usuario as movil_comprador', 'buyer.correo_electronico as correo_comprador', 'seller.movil_usuario as movil_vendedor', 'seller.correo_electronico as correo_vendedor',
			DB::raw("(CASE buyer.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN buyer.nombre_usuario || ' ' || COALESCE(pbuyer.primer_apellido_persona, '') || ' ' || COALESCE(pbuyer.segundo_apellido_persona, '') ELSE buyer.nombre_usuario END) as nombre_comprador"),
			DB::raw("(CASE seller.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN seller.nombre_usuario || ' ' || COALESCE(pseller.primer_apellido_persona, '') || ' ' || COALESCE(pseller.segundo_apellido_persona, '') ELSE seller.nombre_usuario END) as nombre_vendedor"), 'acuerdo_compra.codigo_acuerdo_compra','nombre_categoria_producto',
			DB::raw('(CASE WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL THEN true ELSE false END) as acuerdo_compra')
			);

		$query->join('core.estado', 'compra.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.producto', 'compra.codigo_producto', '=', 'producto.codigo_producto');

		// Users buy and sell
		$query->join('seguridad.usuario as buyer', 'compra.codigo_usuario', '=', 'buyer.codigo_usuario');
		$query->leftJoin('core.persona as pbuyer', 'buyer.codigo_usuario', '=', 'pbuyer.codigo_usuario');

		$query->join('seguridad.usuario as seller', 'compra.seg_codigo_usuario', '=', 'seller.codigo_usuario');
		$query->leftJoin('core.persona as pseller', 'seller.codigo_usuario', '=', 'pseller.codigo_usuario');

		$query->leftJoin('seguridad.usuario as entity', 'producto.seg_codigo_usuario', '=', 'entity.codigo_usuario');
		$query->leftJoin('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

		$query->leftJoin('core.categoria_producto_insti', function($join) {
			$join->on('producto.codigo_categoria_prod_insti', '=', 'categoria_producto_insti.codigo_categoria_prod_insti')
				->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
		});

		$query->leftJoin('core.categoria_producto', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');

		$query->leftJoin('core.clase_emprendimiento_insti', function($join) {
			$join->on('entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti')
				->where('clase_emprendimiento_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('clase_emprendimiento_insti.codigo_estado', '=', Estado::getState('active'));
		});

		$query->leftJoin('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');

		$query->leftJoin('core.acuerdo_compra', 'compra.codigo_compra', '=', 'acuerdo_compra.codigo_compra');

		if($request->has('name_product') && !empty($request->name_product)) {
			$query->whereRaw("nombre_producto ilike '%".trim($request->name_product)."%'");
		}

		if($request->has('category') && !empty($request->category)){
			$query->where('categoria_producto_insti.codigo_categoria_prod_insti', '=', $request->category);
		}

		if($request->has('state') && !empty($request->state)){
			if($request->get('state') == Estado::getState('active')){
				$query->where('acuerdo_compra.codigo_estado', '=', $request->state)
						->orwhereNull('acuerdo_compra.codigo_acuerdo_compra');
			}else{
				$query->where('acuerdo_compra.codigo_estado', '=', $request->state);
			}
		}

		if($request->has('state_purchase') && !empty($request->state_purchase)){
			$query->where('compra.codigo_estado', '=', $request->state_purchase);
		}

		if($request->has('number_register') && !empty($request->number_register)){
			$query->where('compra.codigo_compra', '=', $request->number_register);
		}

		$query->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);

		$transacciones = $query->get();

		// Prepare header
		$header = [];
		$header[] = 'Fecha de compra';
		$header[] = 'Número';
		$header[] = 'Comprador';
		$header[] = 'Celular comprador';
		$header[] = 'Correo comprador';
		$header[] = 'Vendedor';
		$header[] = 'Celular vendedor';
		$header[] = 'Correo vendedor';
		$header[] = 'Producto';
		$header[] = 'Categoria';
		$header[] = 'Estado compra';
		$header[] = 'Acuerdo';

		$data = collect([]);
		foreach ($transacciones as $transaction)
		{
			unset($record);
			$record[] = $transaction->fecha_compra;
			$record[] = $transaction->codigo_compra;
			$record[] = $transaction->nombre_comprador;
			$record[] = $transaction->movil_comprador;
			$record[] = $transaction->correo_comprador;
			$record[] = $transaction->nombre_vendedor;
			$record[] = $transaction->movil_vendedor;
			$record[] = $transaction->correo_vendedor;
			$record[] = $transaction->nombre_producto;
			$record[] = $transaction->nombre_categoria_producto;
			$record[] = $transaction->state_purchase;
			$record[] = $transaction->acuerdo_compra == true ? 'Si' : 'No';
			$data[] = $record;
		}

		$filename = "Transacciones";

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => $filename
				]
			])
		->log('activitylog.reports');

		return (new ReportsDownloadExport([$header], [$data]))->download('Transacciones.xlsx');
	}
}
