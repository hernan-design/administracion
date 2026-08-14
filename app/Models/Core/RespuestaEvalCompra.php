<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class RespuestaEvalCompra extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.respuesta_eval_compra';

   	protected $primaryKey = 'codigo_respuesta_eval_compra';

   	public $timestamps = false;

	public static function getResEval($codigo_eval_purchase, $codigo_item_evaluation_purchase)
	{
		$query = self::query();
		$query->where('codigo_evaluacion_compra', '=', $codigo_eval_purchase);
		$query->where('codigo_item_evaluacion_compra', '=', $codigo_item_evaluation_purchase);
		return $query->first();
	}

}
