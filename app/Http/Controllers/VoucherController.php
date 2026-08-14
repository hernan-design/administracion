<?php

namespace App\Http\Controllers;

use App\Models\Core\AcuerdoCompra;
use App\Models\Core\Persona;
use App\Models\Core\Producto;
use App\Models\Core\Voucher;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;
use DB, Lang, App, Log, View, Auth;

use App\Models\Core\DetalleVoucher, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado, App\Models\Core\ResultadoEvaluation, App\Models\Core\Compra;

use App\Models\Seguridad\Usuario;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        if($request->ajax()) {
            // Decrypt src-params
            list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);
            if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
                abort(403);
            }

            DB::beginTransaction();
            try {

                $registerPhase = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
                if(!$registerPhase instanceof RegistroFaseUsuario) {
                    DB::rollback();
                    return response()->json(['success' => false, 'errors' => 'El código de registro fase no se encuentra en el sistema ']);
                }
                $userEntity = Usuario::getUserByEntity($registerPhase->codigo_usuario);

                if(!$userEntity instanceof Usuario){
                    DB::rollback();
                    return response()->json(['success' => false, 'errors' => 'Entidad no asociada']);
                }

                // Insert Detail Voucher
                $detailVoucher = new DetalleVoucher;
                $detailVoucher->codigo_usuario =  $userEntity->owner_id;
                $detailVoucher->codigo_estado = Estado::getState('active');
                $detailVoucher->codigo_voucher = 'V20180213-1';
                $detailVoucher->valor_detalle_voucher = floatval($request->llaves_acceso);
                $detailVoucher->saldo_detalle_voucher = floatval($request->llaves_acceso);
                $detailVoucher->fecha_detalle_voucher = date('Y-m-d');

                $detailVoucher->save();

                // Prepare response
                $response = ResultadoEvaluation::buildResultEvaluation(RegistroFaseUsuario::getRegisterPhase($registerPhase->codigo_registro_fase_usuario));
                $response->success = true;

                // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
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
        //
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

      DB::beginTransaction();
      try {

        // Codigo_voucher => codigo_detalle_voucher ( ONLY IN THIS CASE )

        $code_voucher = $request->codigo_voucher;
        $code_purchase = $request->purchase;

        $purchase = Compra::find($code_purchase);
        $agreement = AcuerdoCompra::where('codigo_compra', $code_purchase)->first();
        $detailVoucher = DetalleVoucher::find($code_voucher);

        if (!$purchase instanceof Compra) {
          abort(404);
          return response()->json(['success' => false, 'message' => "Compra no encontrada - ( Codigo Compra ) {$code_purchase}"]);
        }

        if (!$detailVoucher instanceof DetalleVoucher) {
          abort(404);
          return response()->json(['success' => false, 'message' => "Detalle voucher no encontrado - ( Codigo detalle voucher ) {$code_voucher}"]);
        }

        if ($agreement instanceof AcuerdoCompra) {
          $agreement->codigo_estado = Estado::getState('inactive');
          $agreement->save();
        }

        $detailVoucher->saldo_detalle_voucher += $purchase->valor_total_compra;
        $detailVoucher->save();

        $purchase->codigo_estado = Estado::getState('removed');
        $purchase->save();

        Voucher::sendEmailCancelTransaction($purchase->codigo_producto, $code_purchase, $purchase->seg_codigo_usuario, number_format($purchase->valor_total_compra));

        activity()
          ->performedOn(Auth::user())
          ->withProperties([
            'attributes' => [
              'user' => Auth::user()->nombre_usuario,
              'user_id' => Auth::user()->codigo_usuario,
              'purchase' => $code_purchase,
            ]
          ])
          ->log('activitylog.voucher');

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Transaccion cancelada correctamente']);
      } catch (\Exception $e) {
        DB::rollback();
        Log::error(sprintf('%s:%s', 'VoucherController:update', $e->getMessage()));
        return response()->json(['success' => false, 'errors' => trans('app.exception')]);
      }

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
}
