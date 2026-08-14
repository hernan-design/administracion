<?php

namespace App\Console\Commands\LoadEvaluation;

use TuProyecto, SendEmail, Auth, DB, Log, Lang, App;

use App\Models\Core\AsignacionJurado,
    App\Models\Core\Estado,
    App\Models\Core\EvaluacionEmprendimiento,
    App\Models\Core\EvaluacionItemEmprendimiento,
    App\Models\Core\Fase,
    App\Models\Core\ItemEvaluacionEmprendimiento,
    App\Models\Core\OpcionEvaluacionPregunta,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\ResultadoEvaluacionEmprendimiento;

class CargaEvaluacion
{

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public static function update($data_for_evaluation)
	{
        Log::info("Cargando datos de evaluación...procesando....");
        $data = $data_for_evaluation->answers_form_evaluation;

		// assignment
		$assignment = AsignacionJurado::select('asignacion_jurado.*', 'fase.codigo_evaluacion_emprendimiento')
            ->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario')
            ->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase')->find($data_for_evaluation->codigo_asignacion_jurado);

        if(!$assignment instanceof AsignacionJurado) {
	        Log::critical("FATAL ERROR => No cuenta con asignación jurado.");
            die;
		}

		$assignment->codigo_estado = Estado::getState('evaluated');
		$assignment->save();

		$institution = $data_for_evaluation->id_institution;

		switch ($institution) {
			case 61: $id_evaluation_enterprenurship = 28;
			break;
			case 62:
				switch ($data_for_evaluation->id_evaluation) {
					case 30:
						$id_evaluation_enterprenurship = 30;
						break;
					default:
						$id_evaluation_enterprenurship = 29;
						break;
				}
			break;
		}

        $entrepreneurship_evaluation = EvaluacionEmprendimiento::find($id_evaluation_enterprenurship);
		if(!$entrepreneurship_evaluation instanceof EvaluacionEmprendimiento){
            Log::critical("FATAL ERROR => No existe la evaluación con código: $id_evaluation_enterprenurship");
            die;
		}

		//evaluation evalEntrepreneurship evaluation_result
        $evaluation_result = ResultadoEvaluacionEmprendimiento::where('codigo_asignacion_jurado', $assignment->codigo_asignacion_jurado)->first();

        if(!$evaluation_result instanceof ResultadoEvaluacionEmprendimiento) {
			$evaluation_result = new ResultadoEvaluacionEmprendimiento;
			$evaluation_result->codigo_estado = Estado::getState('active');
			$evaluation_result->codigo_usuario =  $data_for_evaluation->id_user_evaluator;
			$evaluation_result->codigo_asignacion_jurado = $assignment->codigo_asignacion_jurado;
			$evaluation_result->codigo_evaluacion_emprendimiento = $id_evaluation_enterprenurship;
		}

        // get items
        $items = ItemEvaluacionEmprendimiento::where('codigo_estado' , Estado::getState('active'))
												->where('codigo_evaluacion_emprendimiento', $id_evaluation_enterprenurship)
												->orderBy('codigo_item_evaluacion', 'asc')->get();

        $answers = [];
        $response = new \stdClass();
        DB::beginTransaction();
        try {
                $evaluation_result->fecha_resultado_evaluacion = date('Y-m-d');
                $evaluation_result->hora_resultado_evaluacion = date('H:i:s');
                $evaluation_result->comentario_resultado_evaluacion = $data['comentario'];
                $evaluation_result->save();

                foreach ($items as $item)
                {

                    if(array_key_exists($item->codigo_item_evaluacion, $data))
                    {
						//$choice = OpcionEvaluacionPregunta::where('codigo_opc_eval_pregunta', '=', $data[$item->codigo_item_evaluacion])->first();

                        $eval_item_entrepreneurship = EvaluacionItemEmprendimiento::where('evaluacion_item_emprendimiento.codigo_resultado_evaluacion', $evaluation_result->codigo_resultado_evaluacion)
                        												->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', $item->codigo_item_evaluacion)
																		->first();

						if ($item->codigo_tipo_evaluacion_item != 5)
						{
							$choice = OpcionEvaluacionPregunta::find($data[$item->codigo_item_evaluacion]);
							if(!$choice instanceof OpcionEvaluacionPregunta)
							{
								Log::critical("FATAL ERROR => No existen opciones de respuesta asociadas a la pregunta.");
								die;
							}

							if(!$eval_item_entrepreneurship instanceof EvaluacionItemEmprendimiento) {
								// evaluation Item Entrepreneurship
								$eval_item_entrepreneurship = new EvaluacionItemEmprendimiento();
								$eval_item_entrepreneurship->codigo_resultado_evaluacion = $evaluation_result->codigo_resultado_evaluacion;
								$eval_item_entrepreneurship->codigo_estado = $choice->codigo_estado;
								$eval_item_entrepreneurship->codigo_item_evaluacion = $item->codigo_item_evaluacion;
							}

							$eval_item_entrepreneurship->puntaje_resul_eval_empren = $choice->valor_opc_eval_pregunta;
							$eval_item_entrepreneurship->valor_evaluacion_item = $data[$item->codigo_item_evaluacion];
						} else {

							if(!$eval_item_entrepreneurship instanceof EvaluacionItemEmprendimiento) {
								// evaluation Item Entrepreneurship
								$eval_item_entrepreneurship = new EvaluacionItemEmprendimiento();
								$eval_item_entrepreneurship->codigo_resultado_evaluacion = $evaluation_result->codigo_resultado_evaluacion;
								$eval_item_entrepreneurship->codigo_estado = Estado::getState('active');
								$eval_item_entrepreneurship->codigo_item_evaluacion = $item->codigo_item_evaluacion;
							}

							$eval_item_entrepreneurship->puntaje_resul_eval_empren = 0;
							$eval_item_entrepreneurship->valor_evaluacion_item = 0;
							$eval_item_entrepreneurship->comentario_adicional = trim($data[$item->codigo_item_evaluacion]);
						}

						$eval_item_entrepreneurship->save();
                    }else {
                        continue;
                    }
                }

				// Commit Transaction
				DB::commit();
                Log::info("Se ha cargado los datos de evaluación con éxito...");

				return $evaluation_result->codigo_resultado_evaluacion;

			}catch(\Exception $e){
                    DB::rollback();
                    Log::error($e->getMessage());
            }
	}

    public static function updateToPhase($data_for_evaluation, $data_for_inscription)
	{

		DB::beginTransaction();
		try {
			//retrieves last phase registered for the user
			$register_phase = RegistroFaseUsuario::join('core.registro_convocatoria',
														'registro_convocatoria.codigo_registro_convocatoria',
														'=', 'registro_fase_usuario.codigo_registro_convocatoria'
														)
													->where('registro_fase_usuario.codigo_usuario', $data_for_inscription->codigo_usuario)
													->where('registro_fase_usuario.codigo_registro_convocatoria', $data_for_evaluation->codigo_registro_convocatoria)
													->orderBy('codigo_registro_fase_usuario', 'desc')
													->first();

			$phase = Fase::find($register_phase->codigo_fase);
			if(!$phase instanceof Fase) {
				DB::rollback();
                Log::info("FALTAL ERROR => No se encontró fase");
                die;
			}

			if(!$register_phase instanceof RegistroFaseUsuario) {
				DB::rollback();
                Log::info("FATAL ERROR => El código de registro fase ($register_phase->codigo_registro_fase_usuario) no se encuentra en el sistema.");
                die;
			}

			//update phase
			$register_phase->codigo_estado = Estado::getState('finalized');
			$register_phase->visualizacion_usuario = true;
			$register_phase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
			$register_phase->notificacion_registro_fase_usuario = true;
			$register_phase->seg_codigo_usuario = $data_for_evaluation->id_user_evaluator;
			$register_phase->save();

			//create phase
			$register = new RegistroFaseUsuario;
			$register->codigo_fase = self::getIdPhase($data_for_evaluation->answers_form_evaluation[259]);
			$register->codigo_usuario = $register_phase->codigo_usuario;
			$register->fecha_registro_fase_usuario = date('Y-m-d');
			$register->hora_registro_fase_usuario = date('H:i:s');
			$register->codigo_estado = Estado::getState('active');
			$register->codigo_registro_convocatoria = $register_phase->codigo_registro_convocatoria;
			$register->visualizacion_usuario = false;
			$register->save();

			// Commit Transaction
			DB::commit();
		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'Actualizar perfilamiento: ', $e->getMessage()));
			return response()->json([
										'success' => false,
										'errors' => 'Ocurrio un error al momento de actualizar phase en perfilamiento.'
									]);
		}
	}

    private static function getIdPhase(string $phase): int
	{
    	switch ($phase) :
		    case 608: return 350; // Ideación
		    case 609: return 351; // Preincubación
		    case 611: return 352; // Incubación
		    case 612: return 353; // Aceleración
		    case 613: return 354; // Consolidación
		    case 621: return 355; // Sin base tecnologica
		    case 619: return 356; // No aplica por edad
		    case 620: return 357; // No aplica por ciudad de residencia
		    case 614: return 364; // Laboratorio de innovación
		    case 623: return 363; // No aplica por no tener Cámara de Comercio en Medellín
			default: return null;
		endswitch;
    }
}
//este es el correcto
