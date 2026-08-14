<?php

namespace App\Http\Controllers;

use App\Models\Core\Compra;
use App\Models\Core\ConsumoVoucher;
use App\Models\Core\DetalleVoucher;
use App\Models\Core\DevolucionVoucher;
use App\Models\Core\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Collection as Collection;

use TuProyecto;

use DB, Lang, Log, Auth, SendEmail, App, AWS;

use App\Models\Core\AcuerdoCompra, App\Models\Core\ActividadAcuerdo, App\Models\Core\EntregableAcuerdo, App\Models\Core\UsuarioAcuerdo, App\Models\Core\Estado;

use App\Models\Seguridad\Usuario;

class AcuerdoCompraController extends Controller
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
	public function index(Request $request)
	{
		return view('marketplace.main');
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
		// Decrypt src-params
		list($codigo_acuerdo_compra) = TuProyecto::decryptSrcParams($id);
		if(!$codigo_acuerdo_compra || !is_numeric($codigo_acuerdo_compra)) {
			abort(403);
		}

		$purchaseAgreement = AcuerdoCompra::find($codigo_acuerdo_compra);


		if(!$purchaseAgreement instanceof AcuerdoCompra) {
			abort(404);
		}

		if($request->ajax()) {
      $purchasedAgreement = AcuerdoCompra::getPurchaseAgreement($purchaseAgreement->codigo_acuerdo_compra);
      if(!$purchasedAgreement instanceof AcuerdoCompra) {
          $message = 'El acuerdo de compra no es válido';
          Log::error(sprintf('%s:%s ID #%s', 'AcuerdoCompraController::show', $message, $purchaseAgreement->codigo_acuerdo_compra));
          return response()->json(['success' => false, 'errors' => $message], 500);
      }
			return response()->json(AcuerdoCompra::buildPurchaseAgreement($purchasedAgreement));
		}
		return view('marketplace.main');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Responseº
	 */
	public function edit($id)
	{
		return view('marketplace.main');
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
			DB::beginTransaction();
			try {
				// Decrypt src-params
				list($codigo_acuerdo_compra) = TuProyecto::decryptSrcParams($id);
				if(!$codigo_acuerdo_compra || !is_numeric($codigo_acuerdo_compra)) {
					DB::rollback();
					$message = 'El código de acuerdo compra no es un número';
					Log::error(sprintf('%s:%s', 'AcuerdoCompraController:update', $message));
					return response()->json(['success' => false, 'errors' => $message]);
				}

        // get purchase agreement for validation
        $purchaseAgreement = AcuerdoCompra::getPurchaseAgreement($codigo_acuerdo_compra);
        if (!$purchaseAgreement instanceof AcuerdoCompra) {
          DB::rollback();
          $message = 'El código del acuerdo compra no se encuentra en el sistema ';
          Log::error(sprintf('%s:%s', 'AcuerdoCompraController:update', $message));
          return response()->json(['success' => false, 'errors' => $message]);
        }

        // Fill agreement
        $purchaseAgreement->fill($request->all());
        $message = 'Se ha modificado satisfactoriamente el acuerdo';

        // Prepare email
        $data = [];
        $data['_template'] = 'emails.program.notification';
        $data['_inst'] = session('institution')->codigo_institucion;

        // Validation if update of date_ending_agreement
        if ($request->has('update_date_ending')) {
          if ($request->update_date_ending) {
            $purchaseAgreement->fecha_finalizacion_actividades = $request->date_enlarge;
            $purchaseAgreement->comentario_ampliacion_acuerdo = $request->comment_enlarge;

            $acta = AcuerdoCompra::documentEnlarge($codigo_acuerdo_compra,   $request->date_enlarge,  $request->comment_enlarge);

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('reports.marketplace.innpulsa.ampliacionacuerdo', $acta));

            $s3 = AWS::createClient('s3');

            $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

            $filename = sprintf('%s_%s.pdf', 'acta_ampliacion_acuerdo', $purchaseAgreement->codigo_acuerdo_compra);
            $key = sprintf('%s/%s/%s', 'documents', 'acuerdos', $filename);

            $result = $s3->putObject([
              'Bucket' =>  $bucket,
              'Key'    =>  $key,
              'Body'   =>  $pdf->output(),
              'ACL'    => 'public-read'
            ]);

            // Mail enlarge agreement

            $data['_template'] = 'emails.program.enlargenotification';
            $data['_subject'] = 'Ampliación realizada en acuerdo de negociación';
            $data['_to'] = $purchaseAgreement->correo_comprador;
            $data['comment'] = trans('program.mail.body.agreement.enlarge.purchaser.confirm', ['institution' => session('institution')->nombre_institucion,
              'purchaser_rep_leg' => $purchaseAgreement->nombre_representante_legal_comprador.' '.$purchaseAgreement->apellido_representante_legal_comprador,
              'product' => $purchaseAgreement->nombre_producto, 'seller_rep_leg' => $purchaseAgreement->nombre_representante_legal_proveedor,
              'date_final_agreement' => $request->date_enlarge,
              'commentary_final' => $purchaseAgreement->comentario_ampliacion_acuerdo]);
            $data['_attach'] = [$result['ObjectURL'] => 'Acta ampliacion de acuerdo.pdf'];
            SendEmail::notification($data);

            $data['_to'] = $purchaseAgreement->correo_vendedor;
            $data['comment'] = trans('program.mail.body.agreement.enlarge.purchaser.seller.confirm', ['institution' => session('institution')->nombre_institucion,
              'purchaser_rep_leg' => $purchaseAgreement->nombre_representante_legal_comprador.' '.$purchaseAgreement->apellido_representante_legal_comprador,
              'seller_rep_leg' => $purchaseAgreement->nombre_representante_legal_proveedor,
              'product' => $purchaseAgreement->nombre_producto,
              'date_final_agreement' => $request->date_enlarge,
              'commentary_final' =>  $request->comment_enlarge]);
            SendEmail::notification($data);
          }
        } else if ($request->has('liquidate_agreement')) {
          $consumoVoucher = ConsumoVoucher::getConsumVoucher($purchaseAgreement->codigo_compra);

          if (!$consumoVoucher instanceof ConsumoVoucher) {
            DB::rollback();
            $message = 'El código de compra no se encuentra en el sistema';
            Log::error(sprintf('%s:%s', 'AcuerdoCompraController:update', $message));
            return response()->json(['success' => false, 'errors' => $message]);
          }

          $detalleVoucher = DetalleVoucher::find($consumoVoucher->codigo_detalle_voucher);

          if (!$detalleVoucher instanceof DetalleVoucher) {
            DB::rollback();
            $message = 'El detalle del voucher no se encuentra en el sistema';
            Log::error(sprintf('%s:%s', 'AcuerdoCompraController:update', $message));
            return response()->json(['success' => false, 'errors' => $message]);
          }

          $voucher = Voucher::find($detalleVoucher->codigo_voucher);

          $purchase = Compra::find($purchaseAgreement->codigo_compra);

          $purchaseAgreement->motivo_liquidacion_acuerdo = $request->action_liquidate;
          $purchaseAgreement->comentario_liquidacion_acuerdo = $request->comment_liquidate;

          Compra::where('codigo_compra', $purchaseAgreement->codigo_compra)
            ->update(['codigo_estado' => Estado::getState('removed')]);

          $fecha = date('Y-m-d');
          $fecha_liquidacion = strtotime('+15 day', strtotime($fecha));
          $fecha_liquidacion = date('Y-m-d', $fecha_liquidacion);
          $purchaseAgreement->fecha_liquidacion_acuerdo = $fecha_liquidacion;
          $purchaseAgreement->codigo_estado = Estado::getState('inactive');
          $purchaseId = TuProyecto::encryptSrcParams([$purchaseAgreement->codigo_compra]);
          switch ($request->action_liquidate) {
            case 1:
              $devolutionResources = $purchase->valor_total_compra + $voucher->devolucion_voucher;
              $voucher->devolucion_voucher = $devolutionResources;
              $voucher->save();

              $devolucionVoucher = new DevolucionVoucher();
              $devolucionVoucher->codigo_detalle_voucher = $detalleVoucher->codigo_detalle_voucher;
              $devolucionVoucher->codigo_estado = Estado::getState('active');
              $devolucionVoucher->codigo_usuario = Auth::user()->codigo_usuario;
              $devolucionVoucher->codigo_voucher = $detalleVoucher->codigo_voucher;
              $devolucionVoucher->codigo_compra = $purchase->codigo_compra;
              $devolucionVoucher->fecha_devolucion_voucher = date('Y-m-d');
              $devolucionVoucher->motivo_devolucion_voucher = $request->comment_liquidate;
              $devolucionVoucher->valor_devolucion_voucher = $purchase->valor_total_compra;
              $devolucionVoucher->save();

              $acta = AcuerdoCompra::documentLiquidate($codigo_acuerdo_compra, $request->comment_liquidate);

              $pdf = App::make('dompdf.wrapper');
              $pdf->loadHTML(view('reports.marketplace.innpulsa.liquidaracuerdo', $acta));

              $s3 = AWS::createClient('s3');

              $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

              $filename = sprintf('%s_%s.pdf', 'acta_liquidacion_bolsa_acuerdo', $purchaseAgreement->codigo_acuerdo_compra);
              $key = sprintf('%s/%s/%s', 'documents', 'acuerdos', $filename);

              $result = $s3->putObject([
                'Bucket' =>  $bucket,
                'Key'    =>  $key,
                'Body'   =>  $pdf->output(),
                'ACL'    => 'public-read'
              ]);

              $data['_template'] = 'emails.program.liquidatereintegratenotification';
              $data['_subject'] = 'Liquidación de acuerdo de negociación en Aldea';
              $data['_to'] = $purchaseAgreement->correo_comprador;

              $data['_url_action'] = sprintf("/%s/%s", 'marketplace/compras/evaluacion', $purchaseId);
              $data['comment'] = trans('program.mail.body.agreement.liquidate.bolsa.confirm', ['institution' => session('institution')->nombre_institucion,
                'purchaser_rep_leg' => $purchaseAgreement->nombre_representante_legal_comprador.' '.$purchaseAgreement->apellido_representante_legal_comprador,
                'product' => $purchaseAgreement->nombre_producto, 'seller_rep_leg' => $purchaseAgreement->nombre_representante_legal_proveedor,
                'commentary_final' => $request->comment_liquidate,
                'company_name' => $purchaseAgreement->comprador]);
              $data['_attach'] = [$result['ObjectURL'] => 'Acta liquidacion de acuerdo.pdf'];
              SendEmail::notification($data);

              $data['_template'] = 'emails.program.liquidateseller';
              $data['_to'] = $purchaseAgreement->correo_vendedor;
              $data['_url_action'] = sprintf("/%s/%s", 'marketplace/ventas/evaluacion', $purchaseId);
              $data['comment'] = trans('program.mail.body.agreement.liquidate.seller.confirm', ['institution' => session('institution')->nombre_institucion,
                'purchaser_rep_leg' => $purchaseAgreement->nombre_representante_legal_comprador.' '.$purchaseAgreement->apellido_representante_legal_comprador,
                'seller_rep_leg' => $purchaseAgreement->nombre_representante_legal_proveedor,
                'product' => $purchaseAgreement->nombre_producto,
                'commentary_final' =>  $request->comment_liquidate]);
              SendEmail::notification($data);

              break;
            case 2:
              $devolutionResources = $purchase->valor_total_compra + $detalleVoucher->saldo_detalle_voucher;

              // Falta tabla interseccion devolucion detalle voucher

              $detalleVoucher->saldo_detalle_voucher = $devolutionResources;
              $detalleVoucher->save();

              $acta = AcuerdoCompra::documentLiquidate($codigo_acuerdo_compra, $request->comment_liquidate);

              $pdf = App::make('dompdf.wrapper');
              $pdf->loadHTML(view('reports.marketplace.innpulsa.liquidaracuerdo', $acta));

              $s3 = AWS::createClient('s3');

              $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

              $filename = sprintf('%s_%s.pdf', 'acta_liquidacion_recurso_comprador_acuerdo', $purchaseAgreement->codigo_acuerdo_compra);
              $key = sprintf('%s/%s/%s', 'documents', 'acuerdos', $filename);

              $result = $s3->putObject([
                'Bucket' =>  $bucket,
                'Key'    =>  $key,
                'Body'   =>  $pdf->output(),
                'ACL'    => 'public-read'
              ]);

              $data['_template'] = 'emails.program.liquidatebolsanotification';
              $data['_subject'] = 'Liquidación de acuerdo de negociación en Aldea';
              $data['_to'] = $purchaseAgreement->correo_comprador;
              $data['_url_action'] = sprintf("/%s/%s", 'marketplace/compras/evaluacion', $purchaseId);
              $data['comment'] = trans('program.mail.body.agreement.liquidate.reintegrate.confirm', ['institution' => session('institution')->nombre_institucion,
                'purchaser_rep_leg' => $purchaseAgreement->nombre_representante_legal_comprador.' '.$purchaseAgreement->apellido_representante_legal_comprador,
                'product' => $purchaseAgreement->nombre_producto, 'seller_rep_leg' => $purchaseAgreement->nombre_representante_legal_proveedor,
                'commentary_final' => $request->comment_liquidate,
                'company_name' => $purchaseAgreement->comprador,
                'data_liquidate' => $fecha_liquidacion]);
              $data['_attach'] = [$result['ObjectURL'] => 'Acta liquidacion de acuerdo.pdf'];
              SendEmail::notification($data);

              $data['_template'] = 'emails.program.liquidateseller';
              $data['_to'] = $purchaseAgreement->correo_vendedor;
              $data['_url_action'] = sprintf("/%s/%s", 'marketplace/ventas/evaluacion', $purchaseId);
              $data['comment'] = trans('program.mail.body.agreement.liquidate.seller.confirm', ['institution' => session('institution')->nombre_institucion,
                'purchaser_rep_leg' => $purchaseAgreement->nombre_representante_legal_comprador.' '.$purchaseAgreement->apellido_representante_legal_comprador,
                'seller_rep_leg' => $purchaseAgreement->nombre_representante_legal_proveedor,
                'product' => $purchaseAgreement->nombre_producto,
                'commentary_final' =>  $request->comment_liquidate]);
              SendEmail::notification($data);

              break;
          }
        }

        // Valid actions
        if ($request->has('action')) {
          switch ($request->action) {
            case 'required-return-to-review':
              $purchaseAgreement->codigo_estado = Estado::getState('correction');
              break;

            case 'return-to-review':
              $purchaseAgreement->codigo_estado = Estado::getState('progress');
              $message = 'El acuerdo se ha enviado a revisón correctamente.';

              // Send mail
              $data['_subject'] = 'Devolución de acuerdo de negociación';
              $data['_url_action'] = sprintf("/%s/%s/%s", 'marketplace/ventas/acuerdo', $id, 'edit');
              $data['_to'] = $purchaseAgreement->correo_vendedor;
              $data['_to_name'] = ucwords(strtolower($purchaseAgreement->nombre_vendedor));
              $data['comment'] = trans('program.mail.body.agreement.restore');
              $data['label_action'] = 'Ver acuerdo de negociación';

              SendEmail::notification($data);
              break;

            case 'confirmed':
              $message = 'El acuerdo fue aprobado correctamente.';

              // Acuerdo compra
              $purchaseAgreement->codigo_estado = Estado::getState('confirmed');
              $purchaseAgreement->fecha_acuerdo_aprobacion_compra = date('Y-m-d');
              $purchaseAgreement->codigo_usuario = Auth::user()->codigo_usuario;

              // Save agreement part I
              $purchaseAgreement->save();

              // Prepare file
              $filename = "acuerdo_de_negociacion_$purchaseAgreement->codigo_acuerdo_compra.pdf";
              $filepath = "documents/acuerdos/$filename";

              // File
              $pdf = $purchaseAgreement->pdf();
              $file = Storage::put($filepath, $pdf->output(), 'public');
              $fileurl = sprintf('http://%s/%s', config('filesystems.disks.s3.bucket'), $filepath);

              // File url
              $purchaseAgreement->url_acuerdo_compra = $fileurl;

              // Save agreement part II
              $purchaseAgreement->save();

              // File valid
              if (!Storage::has($filepath)) {
                $message = 'Error recuperando PDF acuerdo de acuerdo de negociacion';
                Log::error(sprintf('%s:%s', 'AcuerdoCompraController:update', $message));
                throw new \Exception("Error recuperando PDF acuerdo de acuerdo de negociacion Acuerdo($purchaseAgreement->codigo_acuerdo_compra) File($filepath).");
              }

              // Send mail
              $data['_subject'] = 'Aprobación de acuerdo de negociación';
              $data['_url_action'] = sprintf("/%s/%s", 'marketplace/compras/acuerdo', $id);
              $data['_to'] = $purchaseAgreement->correo_vendedor;
              $data['to_copy'] = $purchaseAgreement->correo_comprador;
              $data['comment'] = trans('program.mail.body.agreement.confirm', ['institution' => session('institution')->nombre_institucion]);
              $data['_attach'] = [$fileurl => $filename];
              SendEmail::notification($data);
              break;
          }
        }
        // Save agreement
        $purchaseAgreement->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => $message, 'codigo_estado' => $purchaseAgreement->codigo_estado]);
			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'AcuerdoCompraController:update', $e->getMessage()));
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
    public function exportar($id)
    {
        // Decrypt src-params
        list($codigo_acuerdo_compra) = TuProyecto::decryptSrcParams($id);
        if(!$codigo_acuerdo_compra || !is_numeric($codigo_acuerdo_compra)) {
            abort(403);
        }

        $purchaseAgreement = AcuerdoCompra::getPurchaseAgreement($codigo_acuerdo_compra);
        if(!$purchaseAgreement instanceof AcuerdoCompra) {
            abort(404);
        }

		// File
		$pdf = $purchaseAgreement->pdf();
		return $pdf->stream(sprintf('%s.pdf', 'Acuerdo de negociación'));
	}
}
