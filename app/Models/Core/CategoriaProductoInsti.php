<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CategoriaProductoInsti extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.categoria_producto_insti';

   	protected $primaryKey = 'codigo_categoria_prod_insti';

   	public $timestamps = false;
}
