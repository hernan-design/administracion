<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ItemEvaluacionEmprendimiento extends Model
{

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.item_evaluacion_emprendimiento';

	protected $primaryKey = 'codigo_item_evaluacion';

	public $timestamps = false;

  public static function getForm($id)
	{
		$query = ItemEvaluacionEmprendimiento::query();
		$query->select('item_evaluacion_emprendimiento.*');
    $query->where('item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', $id);
		return $query->get();
	}

  /**
	 * Build Evaluation.
	*/
  public static function getItemEmprendimientoInformation($itemEvaluation)
  {
  $object = new \stdClass();
  $object->codigo_item_evalucaion = $itemEvaluation->codigo_item_evalucaion;
  $object->codigo_tipo_evaluacion_item = $itemEvaluation->codigo_tipo_evaluacion_item;
  $object->codigo_evaluacion_emprendimiento = $itemEvaluation->codigo_evaluacion_emprendimiento;
  $object->codigo_estado = $itemEvaluation->codigo_estado;
  $object->nombre_item_evaluacion = $itemEvaluation->nombre_item_evaluacion;
  $object->descripcion_item_evaluacion = $itemEvaluation->descripcion_item_evaluacion;
  $object->codigo_grup_opc_eval_pregunta = $itemEvaluation->codigo_grup_opc_eval_pregunta;
  $object->codigo_pregunta_encuesta = $itemEvaluation->codigo_pregunta_encuesta;
  $object->porcentaje_item_eval_emprendi = $itemEvaluation->porcentaje_item_eval_emprendi;
  $object->orden_item_eval_emprendi = $itemEvaluation->orden_item_eval_emprendi;
  $object->codigo_grupo_item_eval = $itemEvaluation->codigo_grupo_item_eval;

 return $object;
  }
}


