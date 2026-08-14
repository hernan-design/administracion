<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Datatables, Log, DB, Auth;

use App\Models\Core\CategoriaForo, App\Models\Core\CategoriaForoInstitucion, App\Models\Core\Estado;

class CategoriaForoController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			if($request->has('data')){
				return response()->json( CategoriaForo::getForumCategoriesList( $request->all() ) );
			}

			if($request->has('offset') && $request->has('limit')){
				return response()->json( CategoriaForo::getForumCategories( $request->all() ) );
			}
		}

		return view('forum.category.index');
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

			DB::beginTransaction();
            try {
				$category = new CategoriaForo;
				$category->codigo_estado = Estado::getState('active');
				$category->nombre_categoria_foro = $request->nombre_categoria_foro;
				$category->url_categoria_foro = slugtp($request->nombre_categoria_foro, $category->getTable(), 'url_categoria_foro');
				$category->codigo_institucion = session('institution')->codigo_institucion;
				$category->save();

				$institutionCategory = new CategoriaForoInstitucion;
				$institutionCategory->codigo_institucion = session('institution')->codigo_institucion;
				$institutionCategory->codigo_estado = Estado::getState('active');
				$institutionCategory->codigo_categoria_foro = $category->codigo_categoria_foro;
				$institutionCategory->save();

				// Commit Transaction
                DB::commit();

				$response = $category;
				$response->message = 'La categoría ha sido creada y asignada correctamente!!';
				$response->success = true;

				return response()->json($response);
			}catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'CategoriaForoController:store', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
		}
		abort(404);
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
			$message = 'La categoría se ha asignado correctamente!!';
			$category = CategoriaForo::find($id);
			if(!$category instanceof CategoriaForo){
				throw new Exception("No se encontró la categoría {$id}");
			}

			$institutionCategory = CategoriaForoInstitucion::where('codigo_categoria_foro', '=', $category->codigo_categoria_foro)->where('codigo_institucion', '=', session('institution')->codigo_institucion)->first();
			if(!$institutionCategory instanceof CategoriaForoInstitucion){
				$institutionCategory = new CategoriaForoInstitucion;
			}

			DB::beginTransaction();
            try {
				if($request->has('nombre_categoria_foro') && !empty($request->nombre_categoria_foro)){
					$category->nombre_categoria_foro = $request->nombre_categoria_foro;
					$category->save();

					$message = 'La categoría ha sido editada correctamente!!';
				}

				$institutionCategory->codigo_institucion = session('institution')->codigo_institucion;
				$institutionCategory->codigo_estado = Estado::getState('active');
				$institutionCategory->codigo_categoria_foro = $category->codigo_categoria_foro;
				$institutionCategory->save();

				// Commit Transaction
                DB::commit();

				$response = $category;
				$response->message = $message;
				$response->success = true;

				return response()->json($response);
			}catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'CategoriaForoController:store', $e->getMessage()));
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
		if(!is_numeric($id)) {
            abort(404);
        }

		$institutionCategory = CategoriaForoInstitucion::where('codigo_categoria_foro', '=', $id)->where('codigo_institucion', '=', session('institution')->codigo_institucion)->where('codigo_estado', '=', Estado::getState('active'))->first();
		if(!$institutionCategory instanceof CategoriaForoInstitucion){
			throw new Exception("La categoría {$id} no se encuentra asignada");
		}

		$institutionCategory->codigo_estado = Estado::getState('inactive');
		$institutionCategory->save();

		return response()->json(['success' => true, 'message' => 'La categoría se ha eliminado correctamente!!', 'codigo_categoria_foro' => $id]);
    }
}
