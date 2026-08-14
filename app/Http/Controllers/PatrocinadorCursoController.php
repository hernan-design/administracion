<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log;
use Datatables;

use App\Models\Aprende\PatrocinadorCurso;
use App\Models\Aprende\Patrocinador;


class PatrocinadorCursoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evento');
        $this->middleware('ability:admin|super-admin|administrativo,create,evento', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evento', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,evento', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){

            if( isset($request->all()['codigo_curso']) ){

                $query = PatrocinadorCurso::select()
                                        ->join('aprende.patrocinador', 'patrocinador_curso.codigo_patrocinador', '=', 'patrocinador.codigo_patrocinador')
                                        ->where('codigo_curso', $request->all()['codigo_curso'] )
                                        ->get();

                return Datatables::of( $query )->make(true);
            }

		}
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
				$organizer = new PatrocinadorCurso;
				$organizer->codigo_patrocinador = $request->codigo_patrocinador;
				$organizer->codigo_curso = $request->codigo_curso;
				$organizer->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El organizador se agregó correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PatrocinadorController:store', $e->getMessage()));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($request->ajax()){
			DB::beginTransaction();
			try {

                $organizer = PatrocinadorCurso::find($id)->delete();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Organizador removido del curso']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PatrocinadorController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);

    }
}
