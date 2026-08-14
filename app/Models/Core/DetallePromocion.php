<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class DetallePromocion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.detalle_promocion';

	protected $primaryKey = 'codigo_detalle_promocion';

	public $timestamps = false;
}
