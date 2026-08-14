<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use TuProyecto;
use Validator;
use App\Models\Core\Compra, App\Models\Core\RespuestaEvalCompra;

class EvaluacionCompra extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.evaluacion_compra';

	protected $primaryKey = 'codigo_evaluacion_compra';

	public $timestamps = false;

	protected $fillable = ['cometario_evaluacion_compra'];

	public static function getType($type) {
		switch ($type):
		    case 'internal': return 4;
			default: return 'Not type identified';
		endswitch;
	}

	/**
	* Valid create purchase.
	*/
	public function isValid($data)
	{
		$rules = [
			'cometario_evaluacion_compra' => 'required',
			'codigo_compra' => 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getEvalPurchase($purchase)
	{
		$query = self::query();
		$query->where('tipo_evaluacion_compra', '=', Compra::getTypeUser('admin'));
		$query->where('evaluacion_compra.codigo_compra', '=', $purchase);
		return $query->first();
	}

	/**
	 * Build Attached User.
	 */
	public static function buildEval($evaluation)
	{
		$object = new \stdClass();

		if($evaluation instanceof EvaluacionCompra) {
			$object->cometario_evaluacion_compra = $evaluation->cometario_evaluacion_compra;
			$object->answers = $evaluation->answer()->get();
		}

		$object->itemsevaluacion = ItemEvaluacionCompra::getItemEval(Compra::getTypeUser('purchaser'));
		return $object;
	}

	public static function getBill($codigo_compra) {
    $query = Adjunto::query();
    $query->select('adjunto.codigo_adjunto', 'adjunto.codigo_tipo_adjunto', 'adjunto.direccion_adjunto', 'adjunto.privacidad_adjunto', 'adjunto.nombre_adjunto', 'acuerdo_compra.cor_codigo_adjunto');
    $query->leftJoin('core.acuerdo_compra','acuerdo_compra.cor_codigo_adjunto', '=', 'adjunto.codigo_adjunto');
    $query->where('acuerdo_compra.codigo_compra', '=', $codigo_compra);
    $data = $query->get();


    if(count($data) > 0) {
      foreach ($data as $item) {
        $Adjunto[] = EvaluacionCompra::buildBill($item);
      }
    }
    return isset($Adjunto) ? $Adjunto : null;

  }

  public static function buildBill($item) {
    $object = new \stdClass();
    $object->uuid = TuProyecto::encryptSrcParams([$item->codigo_adjunto]);
    $object->codigo_adjunto = $item->codigo_adjunto;
    $object->codigo_tipo_adjunto = $item->codigo_tipo_adjunto;
    $object->direccion_adjunto = $item->direccion_adjunto;
    $object->privacidad_adjunto = $item->privaciodad_adjunto;
    $object->name = $item->nombre_adjunto;

    return $object;
  }

  public static function getRecomendationPurchase($purchase) {
    $query = self::query();
    $query->join('core.tipo_comentario_evaluacion', 'tipo_comentario_evaluacion.cod_tip_comen_eval', '=', 'evaluacion_compra.tipo_comentario_evaluacion');
    $query->join('core.tipo_comen_eval_insti', 'tipo_comen_eval_insti.tipo_comentario_evaluacion', 'tipo_comentario_evaluacion.cod_tip_comen_eval');
    $query->where('codigo_compra', '=', $purchase);
    $query->where('tipo_comen_eval_insti.codigo_institucion', '=', session('institution')->codigo_institucion);

    return $query->first();
  }

  public static function getAprovePurchase($purchase) {
    $query = self::query();
    $query->join('core.tipo_comen_eval_insti', 'tipo_comen_eval_insti.tipo_comentario_evaluacion', 'evaluacion_compra.cor_tipo_comentario_evaluacion');
    $query->join('core.tipo_comentario_evaluacion', 'tipo_comentario_evaluacion.cod_tip_comen_eval', '=', 'tipo_comen_eval_insti.cod_tip_comen_eval');
    $query->where('codigo_compra', '=', $purchase);
    $query->where('tipo_comen_eval_insti.codigo_institucion', '=', session('institution')->codigo_institucion);

    return $query->first();
  }

	public static function getEvalPurchaseItem($purchase, $type)
	{
		$object = new \stdClass();

		$query = self::query();
		$query->where('codigo_compra', '=', $purchase);

		if($type == Compra::getTypeUser('seller')) {
			$query->where('tipo_evaluacion_compra', '=', Compra::getTypeUser('seller'));
		}

		if($type == Compra::getTypeUser('purchaser')) {
			$query->where('tipo_evaluacion_compra', '=', Compra::getTypeUser('purchaser'));
		}

		$evaluacionCompra = $query->first();

		if($evaluacionCompra instanceof EvaluacionCompra){
			// Items
			$query = $evaluacionCompra->answer();
			$query->join('core.item_evaluacion_compra', 'respuesta_eval_compra.codigo_item_evaluacion_compra', '=', 'item_evaluacion_compra.codigo_item_evaluacion_compra');

			$object->evaluacioncompra = $evaluacionCompra;
			$object->respuestaevalcompra = $query->get();

			return $object;
		}
	}

	public function answer()
	{
		return $this->hasMany('App\Models\Core\RespuestaEvalCompra', 'codigo_evaluacion_compra', 'codigo_evaluacion_compra');
	}
}
