<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CategoriaPadreProducto extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.cat_padre_producto';

	protected $primaryKey = 'codigo_cat_padre_producto';

	public $timestamps = false;

	public static function getCategoriesList()
	{
		$query = CategoriaPadreProducto::query();
		$query->select('codigo_cat_padre_producto', 'nombre_cat_padre_producto');
		return $query->get();
	}
}
