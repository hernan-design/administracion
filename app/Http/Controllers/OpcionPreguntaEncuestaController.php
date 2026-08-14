<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log;

use App\Models\Core\OpcionPreguntaEncuesta, App\Models\Core\Estado;

class OpcionPreguntaEncuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return response()->json( OpcionPreguntaEncuesta::getOptionsByQuestion( $request->all() ) );
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
		//
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
		if($request->ajax()){
			$data = $request->all();

			$option = new OpcionPreguntaEncuesta;
			if ($option->isValid($data)) {
				DB::beginTransaction();
				try {
					$option->fill($data);
					$option->puntaje_opcion_encuesta = $request->puntaje_opcion_encuesta ? $request->puntaje_opcion_encuesta : null;
					$option->orden_opcion_encuesta = $request->orden_opcion_encuesta ? $request->orden_opcion_encuesta : null;
					$option->codigo_estado = Estado::getState('active');
					$option->save();

					$response = $option;
					$response->success = true;
					$response->message = 'La opción ha sido asignada a la pregunta correctamente!!';

					// Commit Transaction
					DB::commit();
					return response()->json($response);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'OpcionPreguntaEncuestaController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $option->errors]);
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
		if(!is_numeric($id)){
			abort(404);
		}

		$option = OpcionPreguntaEncuesta::find($id);
		if(!$option instanceof OpcionPreguntaEncuesta){
			abort(404);
		}

		$option->codigo_estado = Estado::getState('inactive');
		$option->save();

		return response()->json(['success' => true, 'message' => 'La opción ha sido removida correctamente!!']);
    }
}
