<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB, Log, TuProyecto;
use App\Http\Controllers\Controller;
use App\Models\Core\UsuarioEmpresario, App\Models\Core\AcuerdoCompra, App\Models\Core\Estado;

class UsuarioEmpresarioController extends Controller
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
				return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra no es un número.']);
			}

			$purchaseAgreement = AcuerdoCompra::find($codigo_acuerdo_compra);
			if(!$purchaseAgreement instanceof AcuerdoCompra) {
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra.']);
			}

			DB::beginTransaction();
			try {
				// Insert User Agreement
				$userBusinessman = new UsuarioEmpresario();
				$userBusinessman->fill($data);
				$userBusinessman->codigo_acuerdo_compra = $purchaseAgreement->codigo_acuerdo_compra;
				$userBusinessman->codigo_estado = Estado::getState('active');
				$userBusinessman->save();

				// update state agreement
				$purchaseAgreement->codigo_estado = Estado::getState('correction');
				$purchaseAgreement->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha creado satisfactoriamente el integrante', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);
			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'Nueva actividad acuerdo', $e->getMessage()));
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

			if(!$request->has('codigo_acuerdo_compra') || !is_numeric($request->codigo_acuerdo_compra)) {
				return response()->json(['success' => false, 'errors' => 'El código de acuerdo compra no es un número.']);
			}

			$purchaseAgreement = AcuerdoCompra::find($request->codigo_acuerdo_compra);
			if(!$purchaseAgreement instanceof AcuerdoCompra) {
				return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
			}

			$userBusinessman = UsuarioEmpresario::find($id);
			if(!$userBusinessman instanceof UsuarioEmpresario){
				return response()->json(['success' => false, 'errors' => 'El integrante no se encuentra en el sistema ']);
			}

			if ($userBusinessman->isValid($data)) {
				DB::beginTransaction();
				try {
					$userBusinessman->fill($data);
					$userBusinessman->save();

					// State agreement
					$purchaseAgreement->codigo_estado = Estado::getState('correction');
					$purchaseAgreement->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'Integrante editado correctamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'Edición cambio de integrante acuerdo', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $userBusinessman->errors]);
		}
		abort(404);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	 public function destroy(Request $request, $id)
	 {
		$userBusinessman = UsuarioEmpresario::find($id);
		if(!$userBusinessman instanceof UsuarioEmpresario){
			return response()->json(['success' => false, 'errors' => 'El código del integrante no se encuentra.']);
		}

		$purchaseAgreement = AcuerdoCompra::find($userBusinessman->codigo_acuerdo_compra);
		if(!$purchaseAgreement instanceof AcuerdoCompra) {
			return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra en el sistema ']);
		}

		DB::beginTransaction();
		try {
			// State advisor
			$userBusinessman->codigo_estado = Estado::getState('removed');
			$userBusinessman->save();

			// State agreement
			$purchaseAgreement->codigo_estado = Estado::getState('correction');
			$purchaseAgreement->save();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Integrante eliminado satisfactoriamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);
	 	}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'Elimiando item empresario acuerdo', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
	 }
}
