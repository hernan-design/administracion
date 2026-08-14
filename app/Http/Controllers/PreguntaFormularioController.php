<?php

namespace App\Http\Controllers;

use App\Models\Core\PaginaEncuesta;
use App\Models\Core\TipoCampoPregunta;
use App\Models\Core\TipoPreguntaEncuesta;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Log, DB;

use App\Models\Core\PreguntaEncuesta, App\Models\Core\OpcionPreguntaEncuesta, App\Models\Core\VerPregEncuesta, App\Models\Core\SubpreguntaEncuesta, App\Models\Core\Estado;

class PreguntaFormularioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			if($request->codigo_pregunta_encuesta && !$request->options && !$request->edit){
        dd('aqui');
				return response()->json(PreguntaEncuesta::getQuestions( $request->all() ));
			} else if ($request->options && $request->codigo_pregunta_encuesta && !$request->edit) {
        return response()->json(PreguntaEncuesta::getOptionsQuestion( $request->all() ));
      } else if ($request->codigo_pregunta_encuesta && $request->edit) {
        return response()->json(PreguntaEncuesta::getQuestionAll($request->all()));
      }
			return response()->json(PreguntaEncuesta::getQuestionsByForm( $request->all() ));
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
        list($tipo_pregunta_encuesta, $tipo_campo_pregunta) = explode('_', $request->tipo_pregunta_encuesta[1]);
				$pages = PaginaEncuesta::where('codigo_encuesta', '=', $request->codigo_encuesta)->get();
        $code_page = $request->codigo_pagina_encuesta == count($pages) ? $pages = $pages->last()->codigo_pagina_encuesta : $request->codigo_pagina_encuesta;
        $tipo_pregunta = $request->tipo_pregunta_encuesta[0];
        $lastQuestion = PreguntaEncuesta::orderBy('codigo_pregunta_encuesta', 'DESC')->first();
				if ($tipo_pregunta == "1") {
          $question = new PreguntaEncuesta;
          $question->codigo_pregunta_encuesta = $lastQuestion->codigo_pregunta_encuesta + 1;
          $question->codigo_encuesta = $request->codigo_encuesta;
          $question->codigo_grupo_pregunta_encuesta = $request->codigo_grupo_pregunta_encuesta ? $request->codigo_grupo_pregunta_encuesta : null;
          $question->texto_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->texto_pregunta_encuesta);
          $question->texto_opcional_pregunta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->texto_opcional_pregunta);
          $question->tipo_campo_pregunta = $tipo_campo_pregunta != '' ? $tipo_campo_pregunta : null;
          $question->tipo_pregunta_encuesta = $tipo_pregunta_encuesta;
          $question->longitud_pregunta_encuesta = $request->longitud_pregunta_encuesta ? $request->longitud_pregunta_encuesta : null;
          $question->periodo_pregunta_encuesta = $request->periodo_pregunta_encuesta ? $request->periodo_pregunta_encuesta : null;
          $question->opcional_pregunta_encuesta = $request->opcional_pregunta_encuesta ? TRUE : FALSE;
          $question->puntaje_pregunta_encuesta = $request->puntaje_pregunta_encuesta ? TRUE : FALSE;
          $question->subpregunta_pregunta = $request->pregunta_padre_check ? TRUE : FALSE;
          $question->codigo_pagina_pregunta_encuesta = $code_page;
          $question->codigo_estado = Estado::getState('active');
          $question->save();

          $version = new VerPregEncuesta;
          $version->codigo_version_encuesta = $request->codigo_version_encuesta;
          $version->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
          $version->numero_pregunta_version = $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null;
          $version->clase_pregunta_encuesta = $question->clase_pregunta_encuesta ? $question->clase_pregunta_encuesta : 1;
          $version->codigo_estado = Estado::getState('active');
          $version->save();

          if (!is_null($request->options)) {
            foreach ($request->options as $value) {
              $option = new OpcionPreguntaEncuesta;
              $option->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
              $option->texto_opcion_encuesta = $value['texto_opcion_encuesta'];
              $option->orden_opcion_encuesta = $value['orden_opcion_encuesta'] ? $value['orden_opcion_encuesta'] : null;
              $option->tipo_campo_opcion = $tipo_campo_pregunta != '' ? $tipo_campo_pregunta : null;
              $option->codigo_estado = Estado::getState('active');

              if (isset($value['matrizOption']) ) {
                $option->eje_opcion = $value['tipo_pregunta_matriz'] ? $value['tipo_pregunta_matriz'] : '';
              } else {
                $option->puntaje_opcion_encuesta = $value['puntaje_opcion_encuesta'] ? $value['puntaje_opcion_encuesta'] : null;
              }

              $option->save();
            }
          }

          $response = $question;
          $response->numero_pregunta_version = $version->numero_pregunta_version;
          $response->success = true;
          $response->message = 'La pregunta ha sido creada correctamente!!';
        } else {
          // dd($request->all());
          $question = new PreguntaEncuesta;
          $question->codigo_pregunta_encuesta = $lastQuestion->codigo_pregunta_encuesta + 1;
          $question->codigo_encuesta = $request->codigo_encuesta;
          $question->codigo_grupo_pregunta_encuesta = $request->codigo_grupo_pregunta_encuesta ? $request->codigo_grupo_pregunta_encuesta : null;
          $question->texto_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->texto_pregunta_encuesta);
          $question->texto_opcional_pregunta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->texto_opcional_pregunta);
          $question->tipo_campo_pregunta = $tipo_campo_pregunta != '' ? $tipo_campo_pregunta : null;
          $question->tipo_pregunta_encuesta = $tipo_pregunta_encuesta;
          $question->longitud_pregunta_encuesta = $request->longitud_pregunta_encuesta ? $request->longitud_pregunta_encuesta : null;
          $question->periodo_pregunta_encuesta = $request->periodo_pregunta_encuesta ? $request->periodo_pregunta_encuesta : null;
          $question->opcional_pregunta_encuesta = $request->opcional_pregunta_encuesta ? TRUE : FALSE;
          $question->puntaje_pregunta_encuesta = $request->puntaje_pregunta_encuesta ? TRUE : FALSE;
          $question->subpregunta_pregunta = $request->pregunta_padre_check ? TRUE : FALSE;
          $question->clase_pregunta_encuesta_v1 = 2;
          $question->codigo_pagina_pregunta_encuesta = $code_page;
          $question->codigo_estado = Estado::getState('active');
          $question->save();

          $version = new VerPregEncuesta;
          $version->codigo_version_encuesta = $request->codigo_version_encuesta;
          $version->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
          $version->numero_pregunta_version = $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null;
          $version->clase_pregunta_encuesta = $question->clase_pregunta_encuesta ? $question->clase_pregunta_encuesta : 2;
          $version->codigo_estado = Estado::getState('active');
          $version->save();

          if( $request->has('opciones_padre') ){
            if(!is_array($request->opciones_padre)){
              $subquestion = new SubpreguntaEncuesta;
              $subquestion->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
              $subquestion->cor_codigo_pregunta_encuesta = $request->cor_codigo_pregunta_encuesta;
              $subquestion->codigo_estado = Estado::getState('active');
              $subquestion->codigo_opcion_encuesta = $request->opciones_padre;
              $subquestion->orden_subpregunta_encuesta = $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null;
              $subquestion->codigo_version_encuesta = $request->codigo_version_encuesta;
              $subquestion->save();
            } else {
              foreach ($request->opciones_padre as $option) {
                $subquestion = new SubpreguntaEncuesta;
                $subquestion->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
                $subquestion->cor_codigo_pregunta_encuesta = $request->cor_codigo_pregunta_encuesta;
                $subquestion->codigo_estado = Estado::getState('active');
                $subquestion->codigo_opcion_encuesta = $option;
                $subquestion->orden_subpregunta_encuesta = $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null;
                $subquestion->codigo_version_encuesta = $request->codigo_version_encuesta;
                $subquestion->save();
              }
            }
          }


          if (!is_null($request->options)) {
            foreach ($request->options as $value) {
              $option = new OpcionPreguntaEncuesta;
              $option->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
              $option->texto_opcion_encuesta = $value['texto_opcion_encuesta'];
              $option->orden_opcion_encuesta = $value['orden_opcion_encuesta'] ? $value['orden_opcion_encuesta'] : null;
              $option->tipo_campo_opcion = $tipo_campo_pregunta != '' ? $tipo_campo_pregunta : null;
              $option->codigo_estado = Estado::getState('active');

              if (isset($value['matrizOption']) ) {
                $option->eje_opcion = $value['tipo_pregunta_matriz'] ? $value['tipo_pregunta_matriz'] : '';
              } else {
                $option->puntaje_opcion_encuesta = $value['puntaje_opcion_encuesta'] ? $value['puntaje_opcion_encuesta'] : null;
              }

              $option->save();
            }
          }

          $response = $question;
          $response->pregunta_padre = $request->cor_codigo_pregunta_encuesta;;
          $response->version_encuesta = $request->codigo_version_encuesta;
          $response->success = true;
          $response->message = 'La pregunta ha sido creada correctamente!!';
        }

				// Commit Transaction
				DB::commit();
				return response()->json($response);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PreguntaFormularioController:store', $e->getMessage()));
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
		// return $request->all();
		if($request->ajax()){
			$question = PreguntaEncuesta::find($id);
			if(!$question instanceof PreguntaEncuesta){
				throw new Exception("La pregunta no se encuentra", $id);
      }
      // dd($request->all());
			DB::beginTransaction();
			try {
        if ($question->subpregunta_pregunta) {
          list($tipo_pregunta, $campo_pregunta) = explode('_', $request->tipo_pregunta_encuesta[1]);
          if($question->tipo_campo_pregunta != $campo_pregunta) {
            $subquestions = SubpreguntaEncuesta::getSubQuestionsOfFather($id);
            $question->subpregunta_pregunta = null;

            if (count($subquestions) > 0) {
              foreach ($subquestions as $item) {
                PreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
                VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado'  =>  Estado::getState('inactive')]);
              }
            }


            SubpreguntaEncuesta::where('cor_codigo_pregunta_encuesta', '=', $question->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
          }
        }

				is_array($request->tipo_pregunta_encuesta) ? list($tipo_pregunta_encuesta, $tipo_campo_pregunta) = explode('_', $request->tipo_pregunta_encuesta[1]) : list($tipo_pregunta_encuesta, $tipo_campo_pregunta) = array($request->tipo_pregunta_encuesta, $request->tipo_campo_pregunta);
				$question->codigo_grupo_pregunta_encuesta = $request->codigo_grupo_pregunta_encuesta ? $request->codigo_grupo_pregunta_encuesta : null;
				$question->texto_pregunta_encuesta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->texto_pregunta_encuesta);
				$question->texto_opcional_pregunta = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->texto_opcional_pregunta);
				$question->tipo_campo_pregunta = $tipo_campo_pregunta ? $tipo_campo_pregunta : null;
        $question->tipo_pregunta_encuesta = $tipo_pregunta_encuesta;
				$question->longitud_pregunta_encuesta = $request->longitud_pregunta_encuesta ? $request->longitud_pregunta_encuesta : null ;
				$question->periodo_pregunta_encuesta = $request->periodo_pregunta_encuesta ? $request->periodo_pregunta_encuesta : null ;
				$question->opcional_pregunta_encuesta = $request->opcional_pregunta_encuesta ? TRUE : FALSE;
				$question->puntaje_pregunta_encuesta = $request->puntaje_pregunta_encuesta ? TRUE : FALSE;
        $question->codigo_estado = Estado::getState('active');
				$question->save();

				VerPregEncuesta::where('codigo_version_encuesta', '=', $request->codigo_version_encuesta)
					->where('codigo_pregunta_encuesta', '=', $id)
          ->update(['codigo_version_encuesta' => $request->codigo_version_encuesta, 'codigo_pregunta_encuesta' => $question->codigo_pregunta_encuesta, 'numero_pregunta_version' => $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null, 'clase_pregunta_encuesta' => $question->clase_pregunta_encuesta_v1 ? $question->clase_pregunta_encuesta_v1 : 1, 'codigo_estado' => Estado::getState('active') ]);

				if( $request->has('opciones_padre') ){
          $arSubquestions = collect($request->opciones_padre);
					if(!is_array($request->opciones_padre)){
            SubpreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $request->codigo_pregunta_encuesta)
            ->update(['codigo_estado' => Estado::getState('inactive')]);

            $subquestionNew = new SubpreguntaEncuesta;
            $subquestionNew->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
            $subquestionNew->cor_codigo_pregunta_encuesta = $request->cor_codigo_pregunta_encuesta;
            $subquestionNew->codigo_estado = Estado::getState('active');
            $subquestionNew->codigo_opcion_encuesta = $request->opciones_padre;
            $subquestionNew->orden_subpregunta_encuesta = $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null;
            $subquestionNew->codigo_version_encuesta = $request->codigo_version_encuesta;
            $subquestionNew->save();

            $arSubquestions[] = $subquestionNew->codigo_opcion_encuesta;
					}else{
						foreach ($request->opciones_padre as $option) {
              $subquestion = SubpreguntaEncuesta::getSubquestion($request->all(), $question, $option);

							if(!$subquestion instanceof SubpreguntaEncuesta){
								$subquestionNew = new SubpreguntaEncuesta;
								$subquestionNew->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
								$subquestionNew->cor_codigo_pregunta_encuesta = $request->cor_codigo_pregunta_encuesta;
								$subquestionNew->codigo_estado = Estado::getState('active');
								$subquestionNew->codigo_opcion_encuesta = $option;
								$subquestionNew->orden_subpregunta_encuesta = $request->numero_pregunta_encuesta ? $request->numero_pregunta_encuesta : null;
								$subquestionNew->codigo_version_encuesta = $request->codigo_version_encuesta;
								$subquestionNew->save();

								$arSubquestions[] = $subquestionNew->codigo_opcion_encuesta;
							}
						}
					}
          $rSubquestions = $arSubquestions->filter(function ($item) { return $item != NULL; });

          // dd($rSubquestions);

          if ($request->has('cor_codigo_pregunta_encuesta')) {
            SubpreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $request->codigo_pregunta_encuesta)
              ->where('codigo_estado', '=', Estado::getState('active'))
              ->update(['cor_codigo_pregunta_encuesta' => $request->cor_codigo_pregunta_encuesta]);
          }

					// $rSubquestions = $arSubquestions);
					// SubpreguntaEncuesta::whereIn('codigo_opcion_encuesta', $rSubquestions)
					// 	->where('codigo_pregunta_encuesta', '=', $question->codigo_pregunta_encuesta)
					// 	->where('cor_codigo_pregunta_encuesta', '<>', $request->cor_codigo_pregunta_encuesta)
					// 	->where('codigo_estado', '=', Estado::getState('active'))
					// 	->where('codigo_version_encuesta', '=', $request->codigo_version_encuesta)
					// 	->update(['codigo_estado' => Estado::getState('inactive')]);
				}

				// return data
				$response = $question;
				$response->numero_pregunta_encuesta = $request->numero_pregunta_encuesta;
				$response->success = true;
				$response->message = 'La pregunta ha sido editada correctamente!!';

				// Commit Transaction
				DB::commit();
				return response()->json($response);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PreguntaFormularioController:update', $e->getMessage()));
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
        DB::beginTransaction();
        try {
          $pregunta_encuesta = PreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $id)->first();

          if ($pregunta_encuesta->clase_pregunta_encuesta_v1 == 1) {
            if ($pregunta_encuesta->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('select')) {

              if ($pregunta_encuesta->subpregunta_pregunta) {
                $subquestions = SubpreguntaEncuesta::getSubQuestionsOfFather($pregunta_encuesta->codigo_pregunta_encuesta);
                if (count($subquestions) >= 1) {
                  foreach ($subquestions as $item) {
                    PreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
                    SubpreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
                    VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado'  =>  Estado::getState('inactive')]);
                  }
                }
              }

              $optionsQuestions = OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $id)->get();
              if ( count($optionsQuestions) > 0 ) {
                  foreach ($optionsQuestions as $item) {
                    OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
                  }
              }

              VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $id)->update(['codigo_estado'  =>  Estado::getState('inactive')]);
              $pregunta_encuesta->codigo_estado = Estado::getState('inactive');
              $pregunta_encuesta->save();

            } else if ($pregunta_encuesta->tipo_campo_pregunta != TipoCampoPregunta::getTypeQuestion('select')) {
              VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $id)->update(['codigo_estado'  =>  Estado::getState('inactive')]);
              $pregunta_encuesta->codigo_estado = Estado::getState('inactive');
              $pregunta_encuesta->save();
            }
          } else if ($pregunta_encuesta->clase_pregunta_encuesta_v1 == 2) {
            if ($pregunta_encuesta->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('select')) {

              $optionsQuestions = OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $id)->get();
              if ( count($optionsQuestions) > 0 ) {
                  foreach ($optionsQuestions as $item) {
                    OpcionPreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $item->codigo_pregunta_encuesta)->update(['codigo_estado' => Estado::getState('inactive')]);
                  }
              }

              SubpreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $id)->update(['codigo_estado' => Estado::getState('inactive')]);
              VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $id)->update(['codigo_estado'  =>  Estado::getState('inactive')]);
              $pregunta_encuesta->codigo_estado = Estado::getState('inactive');
              $pregunta_encuesta->save();
            } else {

              SubpreguntaEncuesta::where('codigo_pregunta_encuesta', '=', $id)->update(['codigo_estado' => Estado::getState('inactive')]);
              VerPregEncuesta::where('codigo_pregunta_encuesta', '=', $id)->update(['codigo_estado'  =>  Estado::getState('inactive')]);
              $pregunta_encuesta->codigo_estado = Estado::getState('inactive');
              $pregunta_encuesta->save();
            }
          }

          // Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'message' => 'Eliminado correctamente', 'class_question' => $pregunta_encuesta->clase_pregunta_encuesta_v1]);
        } catch (\Exception $e) {
          DB::rollback();
          Log::error(sprintf('%s:%s', 'PreguntaFormularioController:destroy', $e->getMessage()));
          return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }
    }
}
