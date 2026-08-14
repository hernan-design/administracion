<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables, Log, DB;

use App\Models\Core\OcupacionPersona, App\Models\Core\Estado;

class OcupacionPersonaController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,usuario/ocupacion');
        $this->middleware('ability:admin|super-admin|administrativo,create,usuario/ocupacion', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,usuario/ocupacion', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
            if($request->has('data') && $request->data){
            return response()->json( OcupacionPersona::occupations( $request->all() ) );
            }
			return Datatables::of( OcupacionPersona::getOccupations( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('users.occupation.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.occupation.create-edit');
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
			$occupation = new OcupacionPersona();
			if($occupation->isValid($data)){
				DB::beginTransaction();
				try {
					$occupation->nombre_ocupacion_persona = $request->nombre_ocupacion_persona;
					$occupation->codigo_estado = Estado::getState('active');
					$occupation->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'La ocupación ha sido creada correctamente!!', 'codigo_ocupacion_persona' => $occupation->codigo_ocupacion_persona, 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'OcupacionPersonaController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $occupation->errors]);
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
		if(!is_numeric($id)){
			abort(404);
		}

		$occupation = OcupacionPersona::find($id);
		if(!$occupation instanceof OcupacionPersona){
			abort(404);
		}

		return view('users.occupation.create-edit', compact('occupation'));
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
			$occupation = OcupacionPersona::find($id);
			if(!$occupation instanceof OcupacionPersona){
				throw new \Exception("Ocupación no encontrada {$id}");
			}

			DB::beginTransaction();
			try {
				$codigo_estado = $request->has('change_state') ? $occupation->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $occupation->codigo_estado;

				if(!$request->has('change_state')){
					if($occupation->isValid($data)){
						$occupation->nombre_ocupacion_persona = $request->nombre_ocupacion_persona;
					}else{
						return response()->json(['success' => false, 'errors' => $occupation->errors]);
					}
				}else{
					$occupation->codigo_estado = $codigo_estado;
				}
				$occupation->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La ocupación ha sido editada correctamente!!', 'codigo_ocupacion_persona' => $occupation->codigo_ocupacion_persona, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'OcupacionPersonaController:update', $e->getMessage()));
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
}
