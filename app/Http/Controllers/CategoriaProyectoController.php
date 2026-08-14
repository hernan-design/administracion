<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables, Log, DB;
use App\Models\Core\CategoriaProyecto,
    App\Models\Core\Estado,
    App\Models\Seguridad\Usuario;

class CategoriaProyectoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,entidad/industria');
        $this->middleware('ability:admin|super-admin|administrativo,create,entidad/industria', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,entidad/industria', ['only' => ['edit', 'update']]);
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
				return response()->json( CategoriaProyecto::industries( $request->all() ) );
			}
			return Datatables::of( CategoriaProyecto::getIndustries( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('project.industry.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('project.industry.create-edit');
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
			$industry = new CategoriaProyecto();
			if($industry->isValid($data)){
				DB::beginTransaction();
				try {
					$industry->nombre_categoria_proyecto = $request->nombre_categoria_proyecto;
					$industry->descripcion_categoria_proyecto = $request->descripcion_categoria_proyecto;
					$industry->codigo_institucion = session('institution')->codigo_institucion;
					$industry->codigo_estado = Estado::getState('active');
					$industry->save();

					// Commit Transaction
					DB::commit();
					return response()->json([
              'success' => true,
              'message' => 'La industria ha sido creada correctamente!!',
              'codigo_categoria_proyecto' => $industry->codigo_categoria_proyecto,
              'redirect' => true
          ]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'CategoriaProyectoController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $industry->errors]);
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

		$industry = CategoriaProyecto::getIndustry($id);
		if(!$industry instanceof CategoriaProyecto){
			abort(404);
		}

		return view('project.industry.create-edit', compact('industry'));
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
			$industry = CategoriaProyecto::getIndustry($id);
			if(!$industry instanceof CategoriaProyecto){
				throw new \Exception("Industria no encontrada {$id}");
			}

			DB::beginTransaction();
			try {
				$codigo_estado = $request->has('change_state') ? $industry->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $industry->codigo_estado;

				if( !$request->has('change_state') ) {
					if($industry->isValid($data)){
						$industry->nombre_categoria_proyecto = $request->nombre_categoria_proyecto;
						$industry->descripcion_categoria_proyecto = $request->descripcion_categoria_proyecto;
					}else{
						return response()->json(['success' => false, 'errors' => $industry->errors]);
					}
				}else{
					$industry->codigo_estado = $codigo_estado;
				}
				$industry->save();

        if($codigo_estado == Estado::getState('inactive')){
          $log = 'activitylog.industry.deactivated';
        }elseif($codigo_estado == Estado::getState('active')){
          $log = 'activitylog.industry.activated';
        }

        $user = Usuario::find(\Auth::user()->codigo_usuario);

				activity()
				->performedOn($user)
				->withProperties([
					'attributes' => [
					    'industry' => $industry->nombre_categoria_proyecto
					]
				])
				->log($log);

				// Commit Transaction
				DB::commit();
				return response()->json([
            'success' => true,
            'message' => 'La industria ha sido editada correctamente!!',
            'codigo_categoria_proyecto' => $industry->codigo_categoria_proyecto,
            'codigo_estado' => $codigo_estado
        ]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CategoriaProyectoController:update', $e->getMessage()));
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
