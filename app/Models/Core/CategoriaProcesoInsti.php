<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CategoriaProcesoInsti extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.categoria_proceso_insti';

   	protected $primaryKey = 'codigo_cat_proceso_insti';

   	public $timestamps = false;
}
