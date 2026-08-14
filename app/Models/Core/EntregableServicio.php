<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache, DB;
class EntregableServicio extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.entregable_servicio';

   	protected $primaryKey = 'codigo_entregable_servicio';

	public $timestamps = false;	
}
