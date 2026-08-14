<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

Use TuProyecto;

Use DB, Input, Cache;

use App\Models\Seguridad\Usuario;

class ItemEvaluacionCompra extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.item_evaluacion_compra';

	protected $primaryKey = 'codigo_item_evaluacion_compra';

	public $timestamps = false;

	/**
    * getItemEvalPurchaseList List.
    */
    public static function getItemEvalPurchaseList()
    {
        $cache = sprintf('_%s_%s', 'itemevalpurchase_list', session('institution')->codigo_institucion);
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }

        return Cache::rememberForever($cache, function() {
            $query = ItemEvaluacionCompra::query();
            $query->where('codigo_institucion', '=', session('institution')->codigo_institucion);
            $query->where('codigo_estado', '=', Estado::getState('active'));
            $query->orderby('nombre_item_evaluacion_compra', 'asc');
            return $query->get();
        });
    }

	public static function getItemEval($type)
	{
		return self::getItemEvalPurchaseList()->where('tipo_item_evaluacion_compra', $type);
	}
}
