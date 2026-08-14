<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class EntregaBeneficio extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.entrega_beneficio';

   	protected $primaryKey = 'codigo_entrega_beneficio';

	public $timestamps = false;
}
