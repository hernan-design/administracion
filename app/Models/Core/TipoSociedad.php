<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoSociedad extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.tipo_sociedad';

   	protected $primaryKey = 'codigo_tipo_sociedad';

   	public $timestamps = false;

}
