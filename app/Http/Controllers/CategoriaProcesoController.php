<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Datatables, Log, DB;

use App\Models\Core\CategoriaProceso, App\Models\Core\CategoriaProcesoInsti, App\Models\Core\Estado;

class CategoriaProcesoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,convocatoria/categoria');
        $this->middleware('ability:admin|super-admin|administrativo,create,convocatoria/categoria', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,convocatoria/categoria', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return Datatables::of( CategoriaProceso::getProcessCategory( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
		return view('program.category.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('program.category.create-edit');
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
			$category = new CategoriaProceso;
			if($category->isValid($data)){
				DB::beginTransaction();
				try {
					$category->nombre_categoria_proceso = $request->nombre_categoria_proceso;
					$category->descripcion_categoria_proceso = $request->descripcion_categoria_proceso;
					$category->save();

					$categoryInstitution = new CategoriaProcesoInsti();
					$categoryInstitution->codigo_institucion = session('institution')->codigo_institucion;
					$categoryInstitution->codigo_categoria_proceso = $category->codigo_categoria_proceso;
					$categoryInstitution->codigo_estado = Estado::getState('active');
					$categoryInstitution->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'La categoría ha sido creada correctamente!!', 'codigo_categoria_proceso' => $category->codigo_categoria_proceso, 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'CategoriaProcesoController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $category->errors]);
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

		$category = CategoriaProceso::find($id);
		if(!$category instanceof CategoriaProceso){
			abort(404);
		}

		return view('program.category.create-edit', compact('category'));
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
			$category = CategoriaProceso::find($id);
			if(!$category instanceof CategoriaProceso){
				throw new \Exception("Categoría no encontrada {$id}");
			}

			$categoryInstitution = CategoriaProcesoInsti::where('codigo_categoria_proceso', '=', $category->codigo_categoria_proceso)
				->where('codigo_institucion', '=', session('institution')->codigo_institucion)->first();
			if(!$categoryInstitution instanceof CategoriaProcesoInsti){
				throw new \Exception("Categoría institución no encontrada {$id}");
			}

			DB::beginTransaction();
			try {
				$codigo_estado = $request->has('change_state') ? $categoryInstitution->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $categoryInstitution->codigo_estado;

				if ( !$request->has('change_state') ) {
					if($category->isValidUpdate($data)){
						$category->nombre_categoria_proceso = $request->nombre_categoria_proceso;
						$category->descripcion_categoria_proceso = $request->descripcion_categoria_proceso;
						$category->save();
					}else{
						return response()->json(['success' => false, 'errors' => $category->errors]);
					}
				}else{
					$categoryInstitution->codigo_estado = $codigo_estado;
					$categoryInstitution->save();
				}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La Categoría ha sido editada correctamente!!', 'codigo_categoria_proceso' => $category->codigo_categoria_proceso, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CategoriaProcesoController:update', $e->getMessage()));
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
