<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Models\Seguridad\Modulo;

class PermisoModulo extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seguridad.permiso_modulo';

	protected $primaryKey = 'codigo_permiso_modulo';

	public $timestamps = false;

	public static function getPermissionsByRole(array $filters = [])
	{
		$query = PermisoModulo::query();
		$query->select('modulo.codigo_modulo', 'nombre_modulo', 'permiso.id as permission_id', 'display_name', DB::raw( "(CASE WHEN permiso_rol.permission_id IS NOT NULL THEN true ELSE false END) as comparator") );
		$query->join('seguridad.modulo', 'permiso_modulo.codigo_modulo', '=', 'modulo.codigo_modulo');
		$query->join('seguridad.permiso', 'permiso_modulo.codigo_permiso', '=', 'permiso.id');
		$query->leftJoin('seguridad.permiso_rol', function($join) use($filters){
			$join->on('permiso.id', '=', 'permiso_rol.permission_id')
				->on('modulo.codigo_modulo', '=', 'permiso_rol.codigo_modulo')
				->where('permiso_rol.role_id', '=', $filters['role']);
		});

		if(isset($filters['type']) && !empty($filters['type'])){
			$query->where('modulo.codigo_tipo_modulo', '=', $filters['type']);
		}

		$query->orderBy('modulo.nombre_modulo', 'asc');
		return $query->get();
	}

	public static function getPermissionsAssignment(array $filters = [])
	{
		$query = PermisoRol::query();
		$query->where('role_id', $filters['role']);
		return $query->get();
	}
}
