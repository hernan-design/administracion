<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\PreguntaEncuesta;

class SubpreguntaEncuesta extends Model
{
	protected $table = 'core.subpregunta_encuesta';

    protected $primaryKey = 'codigo_subpregunta_encuesta';

    public $timestamps = false;

	public static function getSubquestion(array $filters = [], PreguntaEncuesta $question, $option)
	{
		$query = SubpreguntaEncuesta::query();
		$query->where('codigo_pregunta_encuesta', '=', $question->codigo_pregunta_encuesta);
		$query->where('codigo_estado', '=', Estado::getState('active'));
		$query->where('codigo_version_encuesta', '=', $filters['codigo_version_encuesta']);
		$query->where('codigo_opcion_encuesta', '=', $option);
		return $query->first();
	}

	public static function getAllSubquestions(array $filters = []) {
		$query = PreguntaEncuesta::query();
		$query->select('pregunta_encuesta.codigo_pregunta_encuesta', 'pregunta_encuesta.tipo_pregunta_encuesta', 'pregunta_encuesta.texto_opcional_pregunta', 'pregunta_encuesta.tipo_campo_pregunta', 'pregunta_encuesta.opcional_pregunta_encuesta', 'pregunta_encuesta.codigo_grupo_pregunta_encuesta', 'subpregunta_encuesta.cor_codigo_pregunta_encuesta', 'pregunta_encuesta.codigo_pagina_pregunta_encuesta', 'pregunta_encuesta.texto_pregunta_encuesta', 'pregunta_encuesta.numero_pregunta_encuesta',
		'pregunta_encuesta.tipo_pregunta_encuesta', 'pregunta_encuesta.tipo_campo_pregunta', 'pregunta_encuesta.opcional_pregunta_encuesta', 'pregunta_encuesta.texto_opcional_pregunta');
		$query->join('core.subpregunta_encuesta', 'subpregunta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->where('pregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('subpregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('pregunta_encuesta.clase_pregunta_encuesta_v1', '=', 2);
		$query->where('pregunta_encuesta.codigo_encuesta', '=', $filters['codigo_encuesta']);
		$data = $query->distinct()->get(); 

		return self::buildSubQuestions($data, $filters);
	}

	public static function buildSubQuestions($subquestions, $filters) {	

		$subquestionsItems = [];
		foreach ($subquestions as $item) {
			$object = new \stdClass();

			$object->codigo_pregunta_encuesta = $item->codigo_pregunta_encuesta;
			$object->codigo_grupo_pregunta_encuesta = $item->codigo_grupo_pregunta_encuesta;
			$object->cor_codigo_pregunta_encuesta = $item->cor_codigo_pregunta_encuesta;
			$object->codigo_pagina_pregunta_encuesta = $item->codigo_pagina_pregunta_encuesta;
			$object->texto_pregunta_encuesta = $item->texto_pregunta_encuesta;
			$object->numero_pregunta_encuesta = $item->numero_pregunta_encuesta;
			$object->tipo_pregunta_encuesta = $item->tipo_pregunta_encuesta;
			$object->opcional_pregunta_encuesta = $item->opcional_pregunta_encuesta;
			$object->texto_opcional_pregunta = $item->texto_opcional_pregunta;
			$object->tipo_campo_pregunta = $item->tipo_campo_pregunta;

			$filters['codigo_pregunta_encuesta'] = $item->codigo_pregunta_encuesta;
			$object->options = PreguntaEncuesta::getOptionsQuestion($filters);
			$object->opciones = [];

			$subquestionsItems[] = $object;
		}
		
		return $subquestionsItems;
	}

	public static function getSubQuestionsOfFather($father) {
		$query = SubpreguntaEncuesta::query();
		$query->select('subpregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'subpregunta_encuesta.codigo_pregunta_encuesta');
		$query->where('subpregunta_encuesta.cor_codigo_pregunta_encuesta', '=', $father);
		$query->where('subpregunta_encuesta.codigo_estado', '=', Estado::getState('active'));

		return $query->get();
	
	}
}
