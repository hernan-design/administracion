<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoIdInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_id_institucion';

	protected $primaryKey = 'codigo_tipo_id_institucion';

	public $timestamps = false;

}
