<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Datatables, DB, Log;

use App\Models\Core\CategoriaCanal, App\Models\Core\Estado;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoriaCanalController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,comunidad/categoria');
        $this->middleware('ability:admin|super-admin|administrativo,create,comunidad/categoria', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,comunidad/categoria', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,comunidad/categoria', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return Datatables::of( CategoriaCanal::getChannelCategories( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);

		return view('comunity.category.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('comunity.category.create-edit');
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
			$channelCategory = new CategoriaCanal();
			DB::beginTransaction();
			try {
				$channelCategory->codigo_estado = Estado::getState('active');
				$channelCategory->nombre_categoria_canal = $request->nombre_categoria_canal;
				$channelCategory->codigo_institucion = session('institution')->codigo_institucion;
				$channelCategory->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'la categoría ha sido creada correctamente!!', 'codigo_categoria_canal' => $channelCategory->codigo_categoria_canal]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CategoriaCanalController:store', $e->getMessage()));
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
        if(!is_numeric($id)){
			abort(404);
		}

		$channelCategory = CategoriaCanal::getCategoryById($id);
		if(!$channelCategory instanceof CategoriaCanal){
			abort(404);
		}

		return view('comunity.category.create-edit', compact('channelCategory'));
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
			$channelCategory = CategoriaCanal::getCategoryById($id);
			if(!$channelCategory instanceof CategoriaCanal){
				return response()->json(['success' => false, 'errors' => 'El código de la categoría no se encuentra en el sistema ']);
			}
			DB::beginTransaction();
			try {
				if($request->has('change_state') && !empty($request->change_state)){
					$channelCategory->codigo_estado = $channelCategory->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');

					$event = $channelCategory->codigo_estado == Estado::getState('active') ? 'activada' : 'inactivada';
				}else {
					$channelCategory->nombre_categoria_canal = $request->nombre_categoria_canal;

					$event = 'editada';
				}
				$channelCategory->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => trans('app.rest.success', ['resource' => 'La Categoría', 'event' => $event]), 'codigo_categoria_canal' => $channelCategory->codigo_categoria_canal, 'codigo_estado' => $channelCategory->codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CategoriaCanalController:update', $e->getMessage()));
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

	public function categories(Request $request)
	{
		if($request->ajax()){
			return CategoriaCanal::getCategoriesAll( $request->all() );
		}
	}
}
