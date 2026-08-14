<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class AsistenciaSesion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.asistencia_sesion';

   	protected $primaryKey = 'codigo_asistencia_sesion';

	public $timestamps = false;

	public function benefitsAssist()
	{
		return $this->hasMany('App\Models\Core\EntregaBeneficio', 'codigo_asistencia_sesion', 'codigo_asistencia_sesion');
	}
}
