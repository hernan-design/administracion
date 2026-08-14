<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class AsociacionTipoPreguntaEncuesta extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.aso_tipo_preg_encuesta';

	protected $primaryKey = 'codigo_aso_tip_pre_en';

	public $timestamps = false;
}
