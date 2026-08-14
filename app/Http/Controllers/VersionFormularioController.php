<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use App\Models\Core\VersionFormulario, App\Models\Core\Estado;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class VersionFormularioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return response()->json(VersionFormulario::getFormVersionsByForm( $request->all() ));
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
				$formVersion = new VersionFormulario;
				$formVersion->codigo_encuesta = $request->codigo_encuesta;
				$formVersion->codigo_estado = Estado::getState('active');
				$formVersion->fecha_version_encuesta = date('Y-m-d');
				$formVersion->comentario_version_encuesta = $request->comentario_version_encuesta;
				$formVersion->version_encuesta_editable = TRUE;
				$formVersion->save();

				VersionFormulario::where('codigo_version_encuesta', '<>', $formVersion->codigo_version_encuesta)
					->where('codigo_encuesta', $request->codigo_encuesta)
					->where('codigo_estado', Estado::getState('active'))
					->where('version_encuesta_editable', TRUE)
					->update(['version_encuesta_editable' => FALSE]);

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'La nueva versión del formulario ha sido creada correctamente.', 'codigo_version_encuesta' => $formVersion->codigo_version_encuesta]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'VersionFormularioController:store', $e->getMessage()));
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
    public function destroy($id)
    {
        //
    }
}
