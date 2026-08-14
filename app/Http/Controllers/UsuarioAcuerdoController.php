<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB, Log, TuProyecto;
use App\Http\Controllers\Controller;
use App\Models\Core\UsuarioAcuerdo, App\Models\Core\AcuerdoCompra, App\Models\Core\Estado, App\Models\Core\GrupoUsuario;

class UsuarioAcuerdoController extends Controller
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

			$userAgreement = UsuarioAcuerdo::where('codigo_grupo_usuario', $request->codigo_grupo_usuario)->where('codigo_acuerdo_compra', $purchaseAgreement->codigo_acuerdo_compra)->first();
			if($userAgreement instanceof UsuarioAcuerdo){
				if($userAgreement->codigo_estado == Estado::getState('active')){
					return response()->json(['success' => false, 'errors' => 'El asesor ya se encuentra asignado, verifique.', 'codigo_usuario_acuerdo' => $userAgreement->codigo_usuario_acuerdo]);
				}
			}else{
				$userAgreement = new UsuarioAcuerdo();
			}

			DB::beginTransaction();
            try {
				// Insert User Agreement
				$userAgreement->codigo_acuerdo_compra = $purchaseAgreement->codigo_acuerdo_compra;
				$userAgreement->codigo_estado = Estado::getState('active');
				$userAgreement->codigo_grupo_usuario = $request->codigo_grupo_usuario;
				$userAgreement->save();

				// update state agreement
				$purchaseAgreement->codigo_estado = Estado::getState('correction');
				$purchaseAgreement->save();

				$response = GrupoUsuario::buildUserWorkTeam( GrupoUsuario::getUserWorkTeam($request->codigo_grupo_usuario) );
				$response->codigo_usuario_acuerdo = $userAgreement->codigo_usuario_acuerdo;
				$response->success = true;
				$response->message = 'Asesor asignado correctamente.';
				$response->codigo_estado_acuerdo = $purchaseAgreement->codigo_estado;

                // Commit Transaction
                DB::commit();
                return response()->json($response);
            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'UsuarioAcuerdoController:store', $e->getMessage()));
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
		//
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
		$usuarioacuerdo = UsuarioAcuerdo::where('codigo_usuario_acuerdo', $id)->where('codigo_estado', Estado::getState('active'))->first();
		if(!$usuarioacuerdo instanceof UsuarioAcuerdo){
			return response()->json(['success' => false, 'errors' => 'El código del asesor no se encuentra.']);
		}

		$purchaseAgreement = AcuerdoCompra::find($usuarioacuerdo->codigo_acuerdo_compra);
		if(!$purchaseAgreement instanceof AcuerdoCompra) {
			return response()->json(['success' => false, 'errors' => 'El código del acuerdo compra no se encuentra.']);
		}

		DB::beginTransaction();
		try {
			// State advisor
			$usuarioacuerdo->codigo_estado = Estado::getState('removed');
			$usuarioacuerdo->save();

			// State agreement
			$purchaseAgreement->codigo_estado = Estado::getState('correction');
			$purchaseAgreement->save();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Asesor eliminado satisfactoriamente.', 'codigo_estado_acuerdo' => $purchaseAgreement->codigo_estado]);

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'UsuarioAcuerdo:destroy', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
    }
}
