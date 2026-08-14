<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class EvaluacionAsesoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.evaluacion_asesoria';

   	protected $primaryKey = 'codigo_evaluacion_asesoria';

	public $timestamps = false;

	public static function typeEvaluation($type)
	{
		switch ($type) {
			case 'adviser': return 1;
            case 'entrepreneur': return 2;
            default: return 'Not type identified';
		}
	}

	public static function evaluacionAsesoria(array $filters = [])
	{
		$object = new \stdClass();

		$query = self::query();
		$query->join('core.tipo_comentario_evaluacion', 'evaluacion_asesoria.tipo_comentario_evaluacion', '=', 'tipo_comentario_evaluacion.cod_tip_comen_eval');
		$query->where('codigo_asesoria', $filters['codigo_asesoria']);

		if($filters['type'] == self::typeEvaluation('adviser')){
			$query->where('tipo_evaluacion_asesoria', $filters['type']);
		}

		if($filters['type'] == self::typeEvaluation('entrepreneur')){
			$query->where('tipo_evaluacion_asesoria', $filters['type']);
		}

		$evaluationAdvice = $query->first();
		if($evaluationAdvice instanceof EvaluacionAsesoria){
			$cumpliItemAsesoria = $evaluationAdvice->cumpliItemAsesoria();
			$cumpliItemAsesoria->join('aprende.item_general_mentoria', 'cumpli_items_asesoria.codigo_item_general_mentoria', '=', 'item_general_mentoria.codigo_item_general_mentoria');
			$cumpliItemAsesoria->orderBy('nombre_item_general_mentoria', 'asc');

			$object->evaluacionasesoria = $evaluationAdvice;
			$object->respuestaevalasesoria = $cumpliItemAsesoria->get();

			return $object;
		}
	}

	public function cumpliItemAsesoria()
	{
		return $this->hasMany('App\Models\Aprende\CumpliItemsAsesoria', 'codigo_evaluacion_asesoria', 'codigo_evaluacion_asesoria');
	}
}
