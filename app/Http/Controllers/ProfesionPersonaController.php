<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables, Log, DB;

use App\Models\Core\ProfesionPersona, App\Models\Core\Estado;

class ProfesionPersonaController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,usuario/profesion');
        $this->middleware('ability:admin|super-admin|administrativo,create,usuario/profesion', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,usuario/profesion', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			if( $request->has('data') && $request->data == 'list' ){
				return response()->json( ProfesionPersona::getProfessionsList( $request->all() ) );
			}
			return Datatables::of( ProfesionPersona::getProfessions( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('users.profession.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.profession.create-edit');
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
			$profession = new ProfesionPersona();
			if($profession->isValid($data)){
				DB::beginTransaction();
				try {
					$profession->nombnre_profesion_persona = $request->nombnre_profesion_persona;
					$profession->codigo_estado = Estado::getState('active');
					$profession->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'La profesión ha sido creada correctamente!!', 'codigo_profesion_persona' => $profession->codigo_profesion_persona, 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'ProfesionPersonaController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $profession->errors]);
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

		$profession = ProfesionPersona::find($id);
		if(!$profession instanceof ProfesionPersona){
			abort(404);
		}

		return view('users.profession.create-edit', compact('profession'));
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
			$profession = ProfesionPersona::find($id);
			if(!$profession instanceof ProfesionPersona){
				throw new \Exception("Profesión no encontrada {$id}");
			}

			DB::beginTransaction();
			try {
				$codigo_estado = $request->has('change_state') ? $profession->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $profession->codigo_estado;

				if ( !$request->has('change_state') ) {
					if($profession->isValid($data)){
						$profession->nombnre_profesion_persona = $request->nombnre_profesion_persona;
					}else{
						return response()->json(['success' => false, 'errors' => $profession->errors]);
					}
				}else{
					$profession->codigo_estado = $codigo_estado;
				}

				$profession->save();


				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La profesión ha sido editada correctamente!!', 'codigo_profesion_persona' => $profession->codigo_profesion_persona, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ProfesionPersonaController:update', $e->getMessage()));
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
