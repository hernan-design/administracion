<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class DetalleNotificacionCanal extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.detalle_noti_canal';

	protected $primaryKey = 'codigo_det_noti_canal';

	public $timestamps = false;
}
