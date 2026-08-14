<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seguridad.permiso_rol';

	protected $primaryKey = 'codigo_permiso_rol';

	public $timestamps = false;

	public function rol()
	{
		return $this->hasOne('App\Models\Seguridad\Rol', 'id', 'role_id');
	}

	public function permiso()
	{
		return $this->hasOne('App\Models\Seguridad\Permiso', 'id', 'permission_id');
	}

	public function modulo()
	{
		return $this->hasOne('App\Models\seguridad\Modulo', 'codigo_modulo', 'codigo_modulo');
	}
}
