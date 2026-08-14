<?php

namespace App\Http\Controllers;

use App\Models\Core\Estado;
use App\Models\Core\OpcionPreguntaEncuesta;
use App\Models\Core\PaginaEncuesta;
use App\Models\Core\PreguntaEncuesta;
use App\Models\Core\VerPregEncuesta;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Log, DB;

use App\Models\Core\GrupoPreguntaEncuesta;

class GrupoPreguntaEncuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){
        if ($request->has('formulario')) {
          return response()->json(GrupoPreguntaEncuesta::getGroups($request));
        } else if ($request->has('grupo')) {
          return response()->json(GrupoPreguntaEncuesta::getGroup($request->grupo));
        } else {
          dd($request->all());
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
      $data = $request->all();
			$group = new GrupoPreguntaEncuesta;
			if ($group->isValid($data)) {
				DB::beginTransaction();
				try {
          $group->fill($data);
          $group->desc_grupo_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->desc_grupo_pregunta_encuesta);
          $group->nombre_grupo_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->nombre_grupo_pregunta_encuesta);
          $group->codigo_pagina_encuesta = $data['pagina_grupo_pregunta'];
          $group->pagina_grupo_pregunta = $data['num_page'];
          $group->save();

					$response = $group;
					$response->success = true;
					$response->message = 'Se ha creado el grupo correctamente!!';

					// Commit Transaction
					DB::commit();
					return response()->json($response);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'GrupoPreguntaEncuestaController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $group->errors]);
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
        dd('Entra');
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
          $group = GrupoPreguntaEncuesta::find($id);
          if ($group->isValid($data)) {
            DB::beginTransaction();
            try {
              $group->fill($data);
              $group->desc_grupo_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->desc_grupo_pregunta_encuesta);
              $group->nombre_grupo_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->nombre_grupo_pregunta_encuesta);
              $group->pagina_grupo_pregunta = $request->pagina_grupo_pregunta ? $request->pagina_grupo_pregunta : null;
              $group->save();

              $response = $group;
              $response->success = true;
              $response->message = 'Se ha editado el grupo correctamente!!';

              // Commit Transaction
              DB::commit();
              return response()->json($response);

            }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'GrupoPreguntaEncuestaController:update', $e->getMessage()));
              return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
          }
			  return response()->json(['success' => false, 'errors' => $group->errors]);
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

      $group = GrupoPreguntaEncuesta::find($id);
      if(!$group instanceof GrupoPreguntaEncuesta){
        throw new Exception("No se encontró el grupo {$id}");
      }

      $group->codigo_estado = Estado::getState('inactive');
      $group->save();

      $questionsGroup = PreguntaEncuesta::where('codigo_grupo_pregunta_encuesta', '=', $id)->where('codigo_estado', '=', Estado::getState('active'))->get();

      if (count($questionsGroup) >= 1) {
        foreach ($questionsGroup as $item) {
          VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);

          $optionsQuestion = OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->get();

          if ( count($optionsQuestion) > 0) {
            OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
          }

        }

        PreguntaEncuesta::where('codigo_grupo_pregunta_encuesta', '=', $id)->update(['codigo_estado' => Estado::getState('inactive')]);
      }

      return response()->json(['success' => true, 'message' => 'Se ha eliminado el grupo correctamente!!']);
    }
}
