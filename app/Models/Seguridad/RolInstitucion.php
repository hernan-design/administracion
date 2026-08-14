<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class RolInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seguridad.rol_institucion';

	protected $primaryKey = 'codigo_rol_institucion';

	public $timestamps = false;
}
