<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB, Log, TuProyecto;
use App\Http\Controllers\Controller;
use App\Models\Core\ActividadAcuerdo, App\Models\Core\AcuerdoCompra, App\Models\Core\Estado;

class ActividadAcuerdoController extends Controller
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
		if($request->ajax()){
			$data = $request->all();

			// Decrypt src-params
			list($codigo_acuerdo_compra) = TuProyecto::decryptSrcParams($request->codigo_acuerdo_compra);
			if(!$codigo_acuerdo_compra || !is_numeric($codigo_acuerdo_compra)) {
				return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra no es un número ']);
			}

			$purchaseAgreement = AcuerdoCompra::find($codigo_acuerdo_compra);
			if(!$purchaseAgreement instanceof AcuerdoCompra) {
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
			}

			DB::beginTransaction();
            try {
				// Insert Activity Agreement
				$activityAgreement = new ActividadAcuerdo();
				$activityAgreement->fill($data);
				$activityAgreement->codigo_acuerdo_compra = $purchaseAgreement->codigo_acuerdo_compra;
				$activityAgreement->codigo_estado = Estado::getState('active');
				$activityAgreement->resp_comprador_actividad = $request->resp_actividad_acuerdo == 'resp_comprador_actividad' ? true : false;
				$activityAgreement->resp_proveedor_actividad = $request->resp_actividad_acuerdo == 'resp_proveedor_actividad' ? true : false;
				$activityAgreement->save();

				// Update state agreement
				$purchaseAgreement->codigo_estado = Estado::getState('correction');
				$purchaseAgreement->save();

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Se ha creado satisfactoriamente la actividad', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado, 'codigo_actividad_acuerdo' => $activityAgreement->codigo_actividad_acuerdo, 'codigo_acuerdo_compra' => $purchaseAgreement->codigo_acuerdo_compra]);
            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'Nueva actividad acuerdo', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => 'Ocurrio un error al momento de crear una actividad de acuerdo']);
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
		if($request->ajax()){
			$data = $request->all();

			$codigo_acuerdo_compra = $data['codigo_acuerdo_compra'];
			if(!$codigo_acuerdo_compra || !is_numeric($codigo_acuerdo_compra)) {
				return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra no es un número ']);
			}

			$purchaseAgreement = AcuerdoCompra::find($codigo_acuerdo_compra);
			if(!$purchaseAgreement instanceof AcuerdoCompra) {
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
			}

			$actividadacuerdo = ActividadAcuerdo::find($id);
			if(!$actividadacuerdo instanceof ActividadAcuerdo){
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
			}

			if( !empty($data['resp_actividad_acuerdo']) ){
				$data['resp_comprador_actividad'] = ( $data['resp_actividad_acuerdo'] == 'resp_comprador_actividad' ) ? true : false;

				$data['resp_proveedor_actividad'] = ($data['resp_actividad_acuerdo'] == 'resp_proveedor_actividad') ? true : false;
			}

			if ($actividadacuerdo->isValid($data)) {
				DB::beginTransaction();
	            try {
					$actividadacuerdo->fill($data);
					$actividadacuerdo->save();

					// update state agreement
					$purchaseAgreement->codigo_estado = Estado::getState('correction');
					$purchaseAgreement->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'Actividad editada correctamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'Edición cambio de actividad acuerdo', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => 'Ocurrio un error al momento de editar la actividad']);
				}
			}
			return response()->json(['success' => false, 'errors' => $actividadacuerdo->errors]);
		}
		abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
	 * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
		$actividadacuerdo = ActividadAcuerdo::find($id);
		if(!$actividadacuerdo instanceof ActividadAcuerdo){
			return response()->json(['success' => false, 'errors' => 'El código de la actividad no se encuentra.']);
		}

		$purchaseAgreement = AcuerdoCompra::find($actividadacuerdo->codigo_acuerdo_compra);
		if(!$purchaseAgreement instanceof AcuerdoCompra) {
			return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra.']);
		}

		DB::beginTransaction();
		try {
			// State activity
			$actividadacuerdo->codigo_estado = Estado::getState('removed');
			$actividadacuerdo->save();

			// State agreement
			$purchaseAgreement->codigo_estado = Estado::getState('correction');
			$purchaseAgreement->save();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Actividad eliminada satisfactoriamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'Edición cambio de actividad acuerdo', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
    }
}
