<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use DB, Log;

use App\Models\Core\Estado, App\Models\Core\PreguntaEncuesta;

class RespuestaEncuesta extends Model
{
    protected $table = 'core.respuesta_encuesta';

	protected $primaryKey = 'codigo_respuesta_encuesta';

	public $timestamps = false;

    public static function buildAnswer(Collection $arAnswers, $tipo_pregunta, $codigo_pregunta)
    {
        $respuesta = null;
        switch ($tipo_pregunta) {
            case PreguntaEncuesta::getType('open'):
                $filter = $arAnswers->where('codigo_pregunta_encuesta', $codigo_pregunta)->first();
                $respuesta = $filter['texto_respuesta_encuesta'];
            break;

            case PreguntaEncuesta::getType('single'):
                $filter = $arAnswers->where('codigo_pregunta_encuesta', $codigo_pregunta)->first();
                $respuesta = $filter['codigo_opcion_encuesta'];
            break;

            case PreguntaEncuesta::getType('multiple'):
                $answers = [];
                $filter = $arAnswers->where('codigo_pregunta_encuesta', $codigo_pregunta);
                foreach ($filter as $answer) {
                    $answers[] = $answer->codigo_opcion_encuesta;
                }
                $respuesta = $answers;
            break;

            case PreguntaEncuesta::getType('upload'):
                $filter = $arAnswers->where('codigo_pregunta_encuesta', $codigo_pregunta)->first();
                $respuesta = $filter['texto_respuesta_encuesta'];
            break;

            case PreguntaEncuesta::getType('matriz'):
                $answers = [];
                $filter = $arAnswers->where('codigo_pregunta_encuesta', $codigo_pregunta);
                foreach ($filter as $answer) {
                    $answers["{$answer->codigo_opcion_encuesta}_{$answer->cor_codigo_opcion_encuesta}"] = $answer->texto_respuesta_encuesta;
                }
                $respuesta = $answers;
            break;
        }
        return $respuesta;
    }

    public static function getUserResponseSurvey($user_code, $survey_code){
    	return RespuestaEncuesta::select(
    			'pe.codigo_pregunta_encuesta as question_id',
    			'pe.tipo_pregunta_encuesta as question_type',
    			'pe.clase_pregunta_encuesta as question_class',
    			'pe.tipo_campo_pregunta as field_type',
    			'pe.texto_pregunta_encuesta as question',
    			'respuesta_encuesta.texto_respuesta_encuesta as answer',
    			'ro.texto_opcion_encuesta as text_option_y',
                'ro.puntaje_opcion_encuesta as score_option',
    			'oex.texto_opcion_encuesta as text_option_x',
                'respuesta_encuesta.codigo_opcion_encuesta as option_code_y',
                'respuesta_encuesta.cor_codigo_opcion_encuesta as option_code_x'
    		)
    		->join('core.pregunta_encuesta as pe','respuesta_encuesta.codigo_pregunta_encuesta','=','pe.codigo_pregunta_encuesta')
    		->leftjoin('core.opcion_encuesta as ro','respuesta_encuesta.codigo_opcion_encuesta','=','ro.codigo_opcion_encuesta')
    		->leftjoin('core.opcion_encuesta as oex','respuesta_encuesta.cor_codigo_opcion_encuesta','=','oex.codigo_opcion_encuesta')
            ->where('respuesta_encuesta.codigo_usuario', '=', $user_code)
            ->where('pe.codigo_encuesta', '=', $survey_code)
            ->where('pe.codigo_estado', '=', Estado::getState('active'))
            ->orderby('pe.numero_pregunta_encuesta','ASC')
            ->get();
    }
    public static function getUserResponseSurveyCustom(UsuarioEncuesta $userSurvey)
    {
    	return RespuestaEncuesta::select(
    			'pe.codigo_pregunta_encuesta as question_id',
    			'pe.tipo_pregunta_encuesta as question_type',
    			'pe.tipo_campo_pregunta as field_type',
    			'pe.texto_pregunta_encuesta as question',
    			'respuesta_encuesta.texto_respuesta_encuesta as answer',
    			'ro.texto_opcion_encuesta as text_option_y',
                'ro.puntaje_opcion_encuesta as score_option',
    			'oex.texto_opcion_encuesta as text_option_x',
                'respuesta_encuesta.codigo_opcion_encuesta as option_code_y',
                'respuesta_encuesta.cor_codigo_opcion_encuesta as option_code_x'
    		)
    		->join('core.pregunta_encuesta as pe','respuesta_encuesta.codigo_pregunta_encuesta','=','pe.codigo_pregunta_encuesta')
    		->leftjoin('core.opcion_encuesta as ro','respuesta_encuesta.codigo_opcion_encuesta','=','ro.codigo_opcion_encuesta')
    		->leftjoin('core.opcion_encuesta as oex','respuesta_encuesta.cor_codigo_opcion_encuesta','=','oex.codigo_opcion_encuesta')
            ->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey->codigo_usuario_encuesta)
            ->where('pe.codigo_estado', '=', Estado::getState('active'))
            ->orderby('pe.numero_pregunta_encuesta','ASC')
        ->get();
    }

    public static function buildReportHeaders(Collection $arQuestions, $header, $matriz)
    {
		$object = new \stdClass();
    	foreach ($arQuestions as $question) {
			if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')])) {

				switch ($question->tipo_pregunta_encuesta) {

					case PreguntaEncuesta::getType('open'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						// Tipo ciudad
						if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')){
							$header[] = 'Departamento';
						}
					break;

					case PreguntaEncuesta::getType('single'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
					break;

					case PreguntaEncuesta::getType('matriz'):
						// Choices closed questions
						$matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						$matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
							   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$header[] = strip_tags("{$question->texto_pregunta_encuesta} {$choice->texto_opcion_encuesta} {$choicey->texto_opcion_encuesta}");
								}
							}
						}
					break;

					default:
						$header[] = strip_tags($question->texto_pregunta_encuesta);
					break;
				}
			}
		}

		$object->header = $header;
		$object->matriz = $matriz;

		return $object;
    }

    public static function buildReportAnswers($users)
    {
    	$query = RespuestaEncuesta::query();
		$query->select('texto_opcion_encuesta','puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $users );
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		$allAnswers = $query->get();

		$answers = [];
		foreach ($allAnswers as $answer) {
			switch ($answer->tipo_pregunta_encuesta) {
				case PreguntaEncuesta::getType('open'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('single'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('multiple'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
				break;

				case PreguntaEncuesta::getType('upload'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('matriz'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
				break;
			}
		}
		return $answers;
    }

    public static function buildAnswers(Collection $arQuestions, $answers, $matriz, $cities, $sectors, $levels, $codigo_usuario_encuesta, $record, $show_puntaje_pregunta = true)
    {
		// Questions Survey
		foreach ($arQuestions as $question)
		{
			if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
			{
				switch ($question->tipo_pregunta_encuesta)
				{
					case PreguntaEncuesta::getType('open'):

						$respuesta = '';
						$key = "{$codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
						$answer = isset($answers[$key]) ? $answers[$key] : '';
						if($answer instanceof RespuestaEncuesta){
							$respuesta = $answer->texto_respuesta_encuesta;
						}

						// Tipo ciudad
						if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')){
							$key = $answer instanceof RespuestaEncuesta ? "{$answer->texto_respuesta_encuesta}" : '';
							$city = isset($cities[$key]) ? $cities[$key] : '';
							if($city instanceof Ciudad) {
								$respuesta = $city->distrito_ciudad;
								$record[] = $city->nombre_ciudad;
							}else{
								$record[] = '';
							}
						}
						// Tipo Sector interes
						if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
							$respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';

						}
						// Tipo nivel educación
						if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')){
							$respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
						}
						$record[] = $respuesta;
					break;

					case PreguntaEncuesta::getType('single'):

						$respuesta = '';
						$key = "{$codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
						$answer = isset($answers[$key]) ? $answers[$key] : '';
						if($answer instanceof RespuestaEncuesta){
							$respuesta = $answer->texto_opcion_encuesta;
						}
						$record[] = $respuesta;

						if($show_puntaje_pregunta) {

							if($question->puntaje_pregunta_encuesta) {
								$puntajeOpcion = "";
								if($answer instanceof RespuestaEncuesta) {
									$puntajeOpcion = $answer->puntaje_opcion_encuesta;
								}
								$record[] = $puntajeOpcion;
							}
						}

					break;

					case PreguntaEncuesta::getType('multiple'):
						$respuesta = '';
						$key = "{$codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
						$answerop = isset($answers[$key]) ? $answers[$key] : [];
						foreach ($answerop as $option) {
							if($option instanceof RespuestaEncuesta){
								$respuesta.= "{$option->texto_opcion_encuesta} ";
							}
						}
						$record[] = $respuesta;
					break;

					case PreguntaEncuesta::getType('upload'):
						$respuesta = '';
						$key = "{$codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
						$answer = isset($answers[$key]) ? $answers[$key] : '';
						if($answer instanceof RespuestaEncuesta){
							$respuesta = $answer->texto_respuesta_encuesta;
						}
						$record[] = $respuesta;
					break;

					case PreguntaEncuesta::getType('matriz'):
						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
								foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$respuesta = '';
									$key = "{$codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
									$answer = isset($answers[$key]) ? $answers[$key] : '';
									if($answer instanceof RespuestaEncuesta){
										$respuesta = $answer->texto_respuesta_encuesta;
									}

									if($choice->tipo_campo_opcion == TipoCampoPregunta::getTypeQuestion('select')) {
										$nombre_item_x = ItemOpcionEncuesta::where('codigo_item_opcion_encuesta', $answer instanceof RespuestaEncuesta ? (int)$answer->texto_respuesta_encuesta : null)->first();
										if($nombre_item_x instanceof ItemOpcionEncuesta) {
											$respuesta = $nombre_item_x->nombre_item_opcion_encuesta;
										}
									}

									if ($choicey->tipo_campo_opcion == TipoCampoPregunta::getTypeQuestion('select')) {
										$nombre_item = ItemOpcionEncuesta::where('codigo_item_opcion_encuesta', $answer instanceof RespuestaEncuesta ? (int)$answer->texto_respuesta_encuesta : null )->first();
										if($nombre_item instanceof ItemOpcionEncuesta) {
											$respuesta = $nombre_item->nombre_item_opcion_encuesta;
										}
									}

									$record[] = $respuesta;
								}
							}
						}
					break;
				}
			}
		}

		return $record;
    }

	public static function getResponseNivelEducacion($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select();
		//$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->whereIn('respuesta_encuesta.codigo_pregunta_encuesta', [5867, 5902]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->whereIn('respuesta_encuesta.texto_respuesta_encuesta', ['4', '5', '6', '10', '11']);
		$scoring_5867_5902 = $query->count();

		if ($scoring_5867_5902 > 0) {
			$scoring_total_5867_5902 = 8;
		} else {
			$scoring_total_5867_5902 = 0;
		}
		return $scoring_total_5867_5902;
	}

	public static function getResponseExperienciaLaboral($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select();
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->whereIn('respuesta_encuesta.codigo_pregunta_encuesta', [5880, 5912]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->where(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::INTEGER'), '>=', 5);
		$scoring_5880_5912 = $query->count();

		if ($scoring_5880_5912 > 0) {
			return $scoring_total_5880_5912 = 8;
		} else {
			return $scoring_total_5880_5912 = 0;
		}
	}

	public static function getResponseDedicacion($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select();
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->whereIn('respuesta_encuesta.codigo_pregunta_encuesta', [5881, 5913]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->whereIn('respuesta_encuesta.codigo_opcion_encuesta', [11526, 11596]);
		$scoring_5881_5913 = $query->count();

		if ($scoring_5881_5913 > 0) {
			return $scoring_total_5881_5913 = 8;
		} else {
			return $scoring_total_5881_5913 = 0;
		}
	}

	public static function getResponseEmpleos($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('(
							CASE
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11321 THEN 1
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11322 THEN 5
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11323 THEN 10
								ELSE 0
							END	) as count_5806'));
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', 5806);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$scoring_5806 = $query->first();
		return isset($scoring_5806) ? $scoring_5806->count_5806 : 0;
	}

	public static function getResponseUsuariosActivos($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select('texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', 5809);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$scoring_5809 = $query->first();

		if (isset($scoring_5809) && isset($scoring_5809->texto_respuesta_encuesta)) {
			if ($scoring_5809->texto_respuesta_encuesta > 1 && $scoring_5809->texto_respuesta_encuesta <= 59) {
				return 1;
			} elseif ($scoring_5809->texto_respuesta_encuesta > 60 && $scoring_5809->texto_respuesta_encuesta <= 100) {
				return 7;
			} elseif ($scoring_5809->texto_respuesta_encuesta > 100) {
				return 14;
			} else {
				return 0;
			}

		} else {
			return 0;
		}
	}

	public static function getResponseVentas($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select();
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', 5812);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->where(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::INTEGER'), '>=', 3000000);
		$scoring_5812 = $query->count();

		if ($scoring_5812 > 0) {
			return $scoring_total_5812 = 10;
		} else {
			return $scoring_total_5812 = 0;
		}
	}

	public static function getResponseFinanciacion($codigo_usuario_encuesta):int
	{
		$query = RespuestaEncuesta::query();
		$query->select();
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', 5819);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 11354);
		$scoring_5819 = $query->count();

		if ($scoring_5819 > 0) {
			return $scoring_total_5819 = 0;
		} else {
			return $scoring_total_5819 = 8;
		}
	}

	public static function getResponseTipoMercado($codigo_usuario_encuesta): int
	{
		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('(
							CASE
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11394 THEN 1
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11396 THEN 1
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11397 THEN 5
								WHEN respuesta_encuesta.codigo_opcion_encuesta=11398 THEN 10
								ELSE 0
							END	) AS count_5832'));
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', 5832);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$scoring_5832 = $query->first();
		return isset($scoring_5832) ? $scoring_5832->count_5832 : 0;
	}
	/**
	 * Obtiene respuestas de evaluaciòn mediante el codigo_usuario_encuesta.
	 *
	 * @access public
	 * @static function
	 * @param int $id_user_survey
	 * @return object $answers
	 */
	public static function getAnswers($id_user_survey)
	{
		$query = RespuestaEncuesta::query();
        $query->where('codigo_usuario_encuesta', $id_user_survey);
        $query->where('codigo_estado', Estado::getState('active'));
        $query->orderBy('codigo_pregunta_encuesta', 'asc');
        $answers = $query->get();

		return $answers;
	}
}
