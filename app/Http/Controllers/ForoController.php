<?php

namespace App\Http\Controllers;

use App\Models\Core\Foro;
use Illuminate\Http\Request;

use DB, Auth, Log, Datatables;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Core\Estado, App\Models\Core\CategoriaForo;

class ForoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,curso', ['except' => ['search']]);
        $this->middleware('ability:admin|super-admin|administrativo,create,curso', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,curso', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,curso', ['only' => ['destroy']]);
    }

       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){

			return Datatables::of( Foro::getForums( $request->all() ))->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive'), Estado::getState('removed')], $filter = null);
        $forumCategories = CategoriaForo::getForumCategoriesList();
        return view('forum.index', compact('states','forumCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
		if(!is_numeric($id)) {
            abort(404);
        }

		$foro = Foro::getForum($id);
		if(!$foro instanceof Curso){
			abort(404);
		}


		if($request->ajax()){
			return response()->json($foro);
		}

		// return view('forum.detail', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

			$foro = Foro::find($id);
			if(!$foro instanceof Foro) {
				return response()->json(['success' => false, 'errors' => 'El código del curso no se encuentra en el sistema ']);
			}


			DB::beginTransaction();
      try {

				$codigo_estado = $request->has('change_state') ? ($foro->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active')) : $foro->codigo_estado;
        $foro->codigo_estado = $codigo_estado;
				$foro->save();

        // Commit Transaction
        DB::commit();
        return response()->json(['success' => true, 'message' => 'El foro ha sido editado correctamente!!', 'codigo_foro' => $foro->codigo_foro, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'ForoController:update', $e->getMessage()));
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
