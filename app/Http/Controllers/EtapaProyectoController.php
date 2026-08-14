<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables, Log, DB;
use App\Models\Core\EtapaProyecto,
    App\Models\Core\Estado,
    App\Models\Seguridad\Usuario;

class EtapaProyectoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,entidad/etapa');
        $this->middleware('ability:admin|super-admin|administrativo,create,entidad/etapa', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,entidad/etapa', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			if($request->has('data') && !empty($request->data)){
				return response()->json( EtapaProyecto::stages( $request->all() ) );
			}
			return Datatables::of( EtapaProyecto::getStages( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
		return view('project.stage.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('project.stage.create-edit');
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
			$stage = new EtapaProyecto();
			if($stage->isValid($data)){
				DB::beginTransaction();
				try {
					$stage->nombre_etapa_proyecto = $request->nombre_etapa_proyecto;
					$stage->descripcionetapa_proyecto = $request->descripcionetapa_proyecto;
					$stage->codigo_institucion = session('institution')->codigo_institucion;
					$stage->codigo_estado = Estado::getState('active');
					$stage->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'La etapa ha sido creada correctamente!!', 'codigo_etapa_proyecto' => $stage->codigo_etapa_proyecto, 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'EtapaProyectoController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $stage->errors]);
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

		$stage = EtapaProyecto::getStage($id);
		if(!$stage instanceof EtapaProyecto){
			abort(404);
		}

		return view('project.stage.create-edit', compact('stage'));
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
			$stage = EtapaProyecto::getStage($id);
			if(!$stage instanceof EtapaProyecto){
				throw new \Exception("Etapa no encontrada {$id}");
			}

			DB::beginTransaction();
			try {
				$codigo_estado = $request->has('change_state') ? $stage->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $stage->codigo_estado;

				if ( !$request->has('change_state') ) {
					if($stage->isValid($data)){
						$stage->nombre_etapa_proyecto = $request->nombre_etapa_proyecto;
						$stage->descripcionetapa_proyecto = $request->descripcionetapa_proyecto;
					}else{
						return response()->json(['success' => false, 'errors' => $stage->errors]);
					}
				}else{
					$stage->codigo_estado = $codigo_estado;
				}

				$stage->save();

        if ($codigo_estado == Estado::getState('inactive')) {
          $log = 'activitylog.etapa.deactivated';
        } elseif ($codigo_estado == Estado::getState('active')) {
          $log = 'activitylog.etapa.activated';
        }

        $user = Usuario::find(\Auth::user()->codigo_usuario);

				activity()
				    ->performedOn($user)
				    ->withProperties([
					      'attributes' => [
					          'etapa' => $stage->nombre_etapa_proyecto
					      ]
				    ])
				    ->log($log);

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La etapa ha sido editada correctamente!!', 'codigo_etapa_proyecto' => $stage->codigo_etapa_proyecto, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'EtapaProyectoController:update', $e->getMessage()));
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
