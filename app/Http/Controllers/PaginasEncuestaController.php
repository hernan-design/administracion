<?php

namespace App\Http\Controllers;

use App\Models\Core\Estado;
use App\Models\Core\PaginaEncuesta;
use DB;
use Illuminate\Http\Request;

class PaginasEncuestaController extends Controller
{
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
        try {
            DB::beginTransaction();
            $page = new PaginaEncuesta();
            $page->codigo_estado = Estado::getState('active');
            $page->codigo_encuesta = $request->codigo_encuesta;
            $page->pagina_grupo_pregunta = $request->page;
            $page->save();

            $response = $page;
            $response->success = true;
            $response->message = 'Se ha creado la página correctamenter';


            // Commit Transaction
            DB::commit();
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PaginaEncuestaController:store', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }



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
    public function destroy($id)
    {
        //
    }
}
