<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoPreguntaEncuesta extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_pregunta_encuesta';

	protected $primaryKey = 'tipo_pregunta_encuesta';

	public $timestamps = false;

	public static function getTypesQuestion()
	{
		$query = TipoPreguntaEncuesta::query();
		$query->orderBy('nombre_tipo_pregunta_encuesta', 'asc');
		$types = $query->get();

		$typequestion = [];
		foreach($types as $type){
			$typequestion[] = self::buildTypesQuestion($type);
		}
		return $typequestion;
	}

	public static function getTypeQuestionMatriz() {
		$query = TipoCampoPregunta::query();
		$query->where('habilitado_matriz', '=', true);
		$types = $query->get();

		$typequestion = [];
		foreach($types as $type){
			$typequestion[] = self::buildTypeQuestionMatriz($type);
		}
		return $typequestion;
	}


	public static function buildTypeQuestionMatriz($type) {
		$objectMatriz = new \stdClass();
		$objectMatriz->codigo_tipo_campo_pregunta = $type->codigo_tipo_campo_pregunta;
		$objectMatriz->nombre_tipo_campo_pregunta = $type->nombre_tipo_campo_pregunta;
		$objectMatriz->icono_tipo_campo_pregunta = $type->icono_tipo_campo_pregunta;
		return $objectMatriz;
	}

	public static function buildTypesQuestion($type)
	{
		$object = new \stdClass();
		$object->tipo_pregunta_encuesta = $type->tipo_pregunta_encuesta;
		$object->nombre_tipo_pregunta_encuesta = $type->nombre_tipo_pregunta_encuesta;
		$object->icono_tipo_pregunta_encuesta = $type->icono_tipo_pregunta_encuesta;
		$object->campo_pregunta = $type->getTypeQuestionField()->get();
		return $object;
	}

	public function getTypeQuestionField()
	{
		return $this->belongsToMany('App\Models\Core\TipoCampoPregunta', 'core.aso_tipo_preg_encuesta', 'tipo_pregunta_encuesta', 'codigo_tipo_campo_pregunta')->orderBy('nombre_tipo_campo_pregunta', 'asc');
	}
}
