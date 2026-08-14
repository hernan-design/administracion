<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Input, Auth, DB, Lang, App, URL, Request, Cache;

use Tuproyecto;

use Custom\Innpulsa;

use Illuminate\Support\Collection as Collection;

use App\Models\Seguridad\Usuario, App\Models\Core\UsuarioEncuesta, App\Models\Core\Entidad, App\Models\Core\RespuestaEncuesta, App\Models\Core\PuntajeRegistroFase;

class ResultadoEvaluation extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.resultado_evaluacion_emprendimiento';

	protected $primaryKey = 'codigo_resultado_evaluacion';

	public $timestamps = false;


	public static function getResultEvaluations(RegistroFaseUsuario $registro)
    {
        $results = [];
        $query = AsignacionJurado::query();
        $query->select('asignacion_jurado.codigo_usuario','codigo_resultado_evaluacion', 'comentario_resultado_evaluacion', 'nombre_especialidad',
 		DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END) as nombre_usuario")
        	);

        $query->join('seguridad.usuario', 'asignacion_jurado.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
        $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
        $query->leftJoin('core.especialidad', 'asignacion_jurado.codigo_especialidad', '=', 'especialidad.codigo_especialidad');
        $query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

        // Get Results Evaluation
		$query->leftjoin('core.resultado_evaluacion_emprendimiento', function($join) use($registro) {
			$join->on('resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado')
				->on('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento')
				->where('resultado_evaluacion_emprendimiento.codigo_estado', '=', Estado::getState('active'));
        });

        $query->where('asignacion_jurado.codigo_registro_fase_usuario', $registro->codigo_registro_fase_usuario );
        $query-> where('asignacion_jurado.codigo_estado', '!=', Estado::getState('removed') );

        // Get posts
		$data = $query->get();

		if(count($data) > 0) {
			foreach ($data as $result) {

				$dataresult = new \stdClass();
				$dataresult->codigo_resultado_evaluacion = $result->codigo_resultado_evaluacion;
				$dataresult->comentario_resultado_evaluacion = $result->comentario_resultado_evaluacion;
				$dataresult->nombre_usuario = $result->nombre_usuario;
				$dataresult->nombre_especialidad = $result->nombre_especialidad;

				$results[] = $dataresult;
			}
		}
		return $results;

	}
	
	/**
	 * Build result list program
	 */

	public static function buildResultEvaluationProgram($program) {
		$object = new \stdClass();
		$object->response = [ResultadoEvaluation::getCityOfEntrepreneurship($program->codigo_usuario),
		ResultadoEvaluation::getTotalScoreMomentThree($program->codigo_registro_convocatoria),
		ResultadoEvaluation::getTotalScoreMomentFour($program->codigo_registro_fase_usuario),
		ResultadoEvaluation::getUserEntity($program->codigo_usuario),
		ResultadoEvaluation::getTotal($program)];

		return $object;
	}

	public static function getTotal($program) {
		$total = 0;
		$total += ResultadoEvaluation::getCityOfEntrepreneurship($program->codigo_usuario)->puntaje;
		$total += ResultadoEvaluation::getTotalScoreMomentThree($program->codigo_registro_convocatoria)->puntaje;
		$total += ResultadoEvaluation::getTotalScoreMomentFour($program->codigo_registro_fase_usuario)->puntaje;
		$total += ResultadoEvaluation::getUserEntity($program->codigo_usuario)->puntaje;

		$object = new \stdClass();
		$object->texto_pregunta_encuesta = 'Total';
		$object->respuesta = 'Ponderado Total';
		$object->puntaje = round($total, 2);

		return $object;
	}

	public static function getUserEntity($user) {
		$entidad = Entidad::select('sector_interes.codigo_sector_interes', 'nombre_sector_interes')->where('codigo_usuario', $user)->join('core.sector_interes', 'sector_interes.codigo_sector_interes', '=', 'entidad.codigo_sector_interes')->first();

		$object = new \stdClass();
		$object->texto_pregunta_encuesta = 'Codigo CIIU';
		$object->respuesta = $entidad instanceof Entidad ? $entidad->codigo_sector_interes.' - '.$entidad->nombre_sector_interes : 'CIIU no encontrado';
		$object->puntaje = $entidad instanceof Entidad ? Innpulsa::getScoreDocumenter__CIIU__aldea20($entidad->codigo_sector_interes) : 0;

		return $object;
	}

	public static function getCityOfEntrepreneurship($user) {
		$userSurvey = UsuarioEncuesta::where('codigo_usuario', $user)->where('codigo_encuesta', 126)->first();
		if(!$userSurvey instanceof UsuarioEncuesta) {
			abort(404);
		}

		$query = RespuestaEncuesta::query();
		$query->select('pregunta_encuesta.texto_pregunta_encuesta', DB::raw("CONCAT(ciudad.nombre_ciudad, ' - ', ciudad.distrito_ciudad) as respuesta"), 'respuesta_encuesta.texto_respuesta_encuesta');
		$query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.ciudad', 'ciudad.codigo_ciudad', '=', DB::raw('respuesta_encuesta.texto_respuesta_encuesta::INTEGER'));
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey->codigo_usuario_encuesta);
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4287);
		$data = $query->first();

		$object = new \stdClass();
		$object->texto_pregunta_encuesta = $data->texto_pregunta_encuesta;
		$object->respuesta = $data->respuesta;
		$object->puntaje = Innpulsa::getScoreDocumenter__4287__aldea20($data->texto_respuesta_encuesta);

		return $object;
	}

	public static function getTotalScoreMomentThree($user) {
		$registerPhase = RegistroFaseUsuario::where('codigo_registro_convocatoria', $user)->where('codigo_fase', Fase::getIdPhase('aldea__20_challenge__1_3'))->first();
		$query = PuntajeRegistroFase::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->first();

		$object = new \stdClass();
		$object->texto_pregunta_encuesta = 'Puntaje obtenido reto 1.3';
		$object->respuesta = $query instanceof PuntajeRegistroFase ? round($query->valor_puntaje_registro_fase, 2) : null;
		$object->puntaje = Innpulsa::getScoreDocumenter__Moment_Three_aldea20($object->respuesta);

		return $object;
	}

	public static function getTotalScoreMomentFour($user) {
		$query = PuntajeRegistroFase::where('codigo_registro_fase_usuario', $user)->first();

		$object = new \stdClass();
		$object->texto_pregunta_encuesta = 'Puntaje obtenido reto 2';
		$object->respuesta = $query instanceof PuntajeRegistroFase ? $query->valor_puntaje_registro_fase : null;
		$object->puntaje = $object->puntaje = Innpulsa::getScoreDocumenter__Moment_Four_aldea20($object->respuesta);

		return $object;
	}

    /**
	 * Build Ressult Evaluation.
	*/
   	public static function buildResultEvaluation($result)
   	{
   		$object = new \stdClass();
		$object->codigo_usuario = $result->codigo_usuario;
        $object->codigo_registro_fase_usuario = $result->codigo_registro_fase_usuario;
		$object->nombre_usuario =  $result->nombre_usuario;
		$object->biografia_usuario =  $result->biografia_usuario;
		$object->fecha_registro_convocatoria =  $result->fecha_registro_convocatoria;
		$object->hora_registro_convocatoria =  $result->hora_registro_convocatoria;
		$object->codigo_convocatoria =  $result->codigo_convocatoria;
		$object->codigo_evaluacion_emprendimiento = $result->codigo_evaluacion_emprendimiento;
		$object->observaciones_registro_fase_usuario = $result->observaciones_registro_fase_usuario;
		$object->documentado = $result->documentado;
		$object->comentario_cerrado = $result->comentario_evaluacion_cerrado;
		if($result->comentario_evaluacion_cerrado) {
			$comment_evaluation = ComentarioEvaluacionEmprendimiento::where('codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento)->get();
			if(count($comment_evaluation) > 0) {
				$object->comentarios_evaluacion = $comment_evaluation;
			}

			$comment_general = ComentDefDocument::where('tipo_coment_def_doc', ComentDefDocument::getTypeComment('general'))
				->where('codigo_registro_fase_usuario', $result->codigo_registro_fase_usuario)->first();
			if($comment_general instanceof ComentDefDocument) {
				$object->comentario_general = $comment_general->codigo_coment_eval_emprendi;
			}

			$comment_secondary = ComentDefDocument::where('tipo_coment_def_doc', ComentDefDocument::getTypeComment('secondary'))
				->where('codigo_registro_fase_usuario', $result->codigo_registro_fase_usuario)->first();
			if($comment_secondary instanceof ComentDefDocument) {
				$object->comentario_secundario = $comment_secondary->codigo_coment_eval_emprendi;
			}

		}
		$object->llaves_acceso = doubleval($result->valor_asignacion_rfu);

		$object->adjunto_carta_notificacion = null;
		$attachedrfu = AdjuntoRegistroFaseUsuario::join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'adjunto_rfu.codigo_adjunto')->where('codigo_registro_fase_usuario', $result->codigo_registro_fase_usuario)->first();
		if($attachedrfu instanceof AdjuntoRegistroFaseUsuario) {
			$attached = new \stdClass();
			$attached->uuid = $attachedrfu->codigo_adjunto;
			$attached->codigo_tipo_adjunto = $attachedrfu->codigo_tipo_adjunto;
			$attached->direccion_adjunto = $attachedrfu->direccion_adjunto;
			$attached->name = $attachedrfu->nombre_adjunto;

			$object->adjunto_carta_notificacion = $attached;
		}

		// Get Items Evatualition
		$query = ItemEvaluacionEmprendimiento::query();
		$query->select('item_evaluacion_emprendimiento.codigo_grupo_item_eval','grupo_item_eval.nombre_grupo_item_eval', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', 'item_evaluacion_emprendimiento.codigo_tipo_evaluacion_item','item_evaluacion_emprendimiento.codigo_grup_opc_eval_pregunta', 'item_evaluacion_emprendimiento.nombre_item_evaluacion', 'nombre_grup_opc_eval_pregunta');
		$query->leftJoin('core.grup_opc_eval_pregunta', 'item_evaluacion_emprendimiento.codigo_grup_opc_eval_pregunta', '=', 'grup_opc_eval_pregunta.codigo_grup_opc_eval_pregunta');
		$query->leftJoin('core.grupo_item_eval', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval', '=', 'grupo_item_eval.codigo_grupo_item_eval');
		$query->where('item_evaluacion_emprendimiento.codigo_estado', '=', Estado::getState('active'));
		$query->where('codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento);
		$query->orderBy('item_evaluacion_emprendimiento.orden_item_eval_emprendi','asc');
		$arItems = $query->get();

		// Groups Item Evaluation
		$query = GrupoItemEval::query();
		$query->select('texto_comentario_documentador','item_evaluacion_emprendimiento.codigo_grupo_item_eval','nombre_grupo_item_eval', 'promedio_conmutado');
		$query->join('core.item_evaluacion_emprendimiento', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');

		$query->leftjoin('core.comen_documentador', function($join) use($result){
			$join->on('comen_documentador.codigo_grupo_item_eval', '=', 'grupo_item_eval.codigo_grupo_item_eval')
				->where('comen_documentador.codigo_estado', '=', Estado::getState('active'))
				->where('comen_documentador.codigo_registro_fase_usuario', '=', $result->codigo_registro_fase_usuario);
        });

		$query->where('codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento);
		$query->where('item_evaluacion_emprendimiento.codigo_estado', Estado::getState('active'));
		$query->orderBy('grupo_item_eval.orden_grupo_item_eval','asc');
		$query->groupBy('grupo_item_eval.codigo_grupo_item_eval','item_evaluacion_emprendimiento.codigo_grupo_item_eval','nombre_grupo_item_eval','texto_comentario_documentador');
		$groups = $query->get();

		$query = EvaluacionItemEmprendimiento::query();
		$query->select('asignacion_jurado.codigo_usuario', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', 'valor_evaluacion_item', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval', DB::raw('COALESCE(opc_eval_pregunta.valor_opc_eval_pregunta, 0) AS valor_opc_eval_pregunta'));
		$query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
		$query->leftJoin('core.opc_eval_pregunta', 'evaluacion_item_emprendimiento.valor_evaluacion_item', '=', 'opc_eval_pregunta.codigo_opc_eval_pregunta');
        $query->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
		$query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
		$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento);
		$query->where('registro_convocatoria.codigo_registro_convocatoria', $result->codigo_registro_convocatoria);
		$califications = $query->get();

		$query = AsignacionJurado::query();
		$query->select('asignacion_jurado.codigo_usuario', 'usuario.nombre_usuario', 'fecha_resultado_evaluacion', 'hora_resultado_evaluacion');
		$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
		$query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
		$query->where('registro_fase_usuario.codigo_registro_fase_usuario',  $result->codigo_registro_fase_usuario);
		$query->where('asignacion_jurado.codigo_estado', '!=',  Estado::getState('removed'));
		$experts = $query->get();
		$object->experts = $experts;

		$object->promedio_total = 0;
		$groupsItem = collect([]);
		$total = 0;
		foreach ($groups as $group) {
			$itemgroup = new \stdClass();
			$itemgroup->codigo_grupo_item_eval = $group->codigo_grupo_item_eval;
			switch ($itemgroup->codigo_grupo_item_eval) {
				case GrupoItemEval::getGroup('enterprising_team'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 40%"; break;
				case GrupoItemEval::getGroup('market'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 25%"; break;
				case GrupoItemEval::getGroup('product'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 25%"; break;
				case GrupoItemEval::getGroup('productive_chains'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 10%"; break;
				case GrupoItemEval::getGroup('innovation_component'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 32%"; break;
				case GrupoItemEval::getGroup('market_2020'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 33%"; break;
				case GrupoItemEval::getGroup('enterprising_team_2020'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 15%"; break;
				case GrupoItemEval::getGroup('objetives'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 12%"; break;
				case GrupoItemEval::getGroup('recomendations'): $itemgroup->nombre_grupo_item_eval = "$group->nombre_grupo_item_eval - Peso en evaluación 8%"; break;
				default: $itemgroup->nombre_grupo_item_eval = $group->nombre_grupo_item_eval; break;
			}

			$itemgroup->texto_comentario_documentador = $group->texto_comentario_documentador;
			$promedio_grupo = GrupoItemEval::generalAverageGroup($group, $result->codigo_registro_fase_usuario);
			$itemgroup->promedio_total_grupo = sprintf('%s %s', $promedio_grupo, '%');;

			if( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('enterprising_team')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 40);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('market'), GrupoItemEval::getGroup('product')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 25);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('productive_chains')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 10);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('innovation_component')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 32);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('market_2020')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 33);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('enterprising_team_2020')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 15);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('objetives')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 12);
			}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('recomendations')]) ) {
				$object->promedio_total += ( ($promedio_grupo / 100) * 8);
			}

			// Calificación
			$itemgroup->totales = [];
			foreach ($experts as $expert) {
				$valort_experto = 0;
				$itemt = new \stdClass();
				$itemt->codigo_usuario = $expert->codigo_usuario;
				$itemt->valor_experto = GrupoItemEval::averageGroup($group, $result->codigo_registro_fase_usuario, $expert->codigo_usuario);
				$itemgroup->totales[] = $itemt;
			}

			$itemgroup->items = [];
			$groupoc = [];
			$itemsEval = $arItems->where('codigo_grupo_item_eval', $group->codigo_grupo_item_eval);
			if($itemsEval->count() > 0) {
				foreach ($itemsEval as $itemeval) {
					$item = new \stdClass();
					$item->codigo_item_evaluacion = $itemeval->codigo_item_evaluacion;
					$item->nombre_item_evaluacion = $itemeval->nombre_item_evaluacion;
					$item->codigo_tipo_evaluacion_item = $itemeval->codigo_tipo_evaluacion_item;
					$item->codigo_grup_opc_eval_pregunta = $itemeval->codigo_grup_opc_eval_pregunta;
					$item->nombre_grup_opc_eval_pregunta = $itemeval->nombre_grup_opc_eval_pregunta;
					// Porcentajes
					foreach ($experts as $expert) {
						unset($calification);

						$itemx = new \stdClass();
						$itemx->codigo_usuario = $expert->codigo_usuario;
						$calification = $califications->where('codigo_usuario', $expert->codigo_usuario)->where('codigo_item_evaluacion', $itemeval->codigo_item_evaluacion)->where('codigo_grupo_item_eval', $group->codigo_grupo_item_eval)->first();
						$valor_experto = 0;

						if($calification instanceof EvaluacionItemEmprendimiento) {
							if( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('enterprising_team')]) ) {
								$valor_experto = round(($calification->valor_opc_eval_pregunta * 100) / 8);
							}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('market')]) ) {
								$valor_experto = round(($calification->valor_opc_eval_pregunta * 100) / 8.33);
							}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('product')]) ) {
								$valor_experto = round(($calification->valor_opc_eval_pregunta * 100) / 6.25);
							}elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('productive_chains')]) ) {
								$valor_experto = round(($calification->valor_opc_eval_pregunta * 100) / 10);
							}
							elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('innovation_component')]) ) {
								$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.32) * 10), 2);
								$total += $valor_experto;
							}
							elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('market_2020')]) ) {
								$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.11) * 10), 2);
								$total += $valor_experto;
							}
							elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('enterprising_team_2020')]) ) {
								switch ($calification->codigo_item_evaluacion) {
									case 182:
										$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.10) * 10), 2);
									break;
									case 183:
										$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.05) * 10), 2);
									break;
								}
								$total += $valor_experto;
							}
							elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('objetives')]) ) {
								$valor_experto = round(((($calification->valor_opc_eval_pregunta * 0.12) * 100) / 12), 2);
								$total += $valor_experto;
							}
							elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('recomendations')]) ) {
								$valor_experto = round(((($calification->valor_opc_eval_pregunta * 0.08) * 100) / 8), 2);
								$total += $valor_experto;
							}
						}
						$itemx->valor_experto = "$valor_experto %";
						$item->answers[] = $itemx;
					}

					// Add item
					$groupoc[] = $item;
				}
			}
			$itemgroup->items = $groupoc;
			// Add itemgroup
			$groupsItem[] = $itemgroup;
		}
		$total = $total / 3;

		$object->grupos = $groupsItem;
		return $object;
   	}

}
