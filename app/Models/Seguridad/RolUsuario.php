<?php

namespace App\Models\Seguridad;

use DB;

use App\Models\Seguridad\Rol;
use App\Models\Core\Estado;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seguridad.rol_usuario';

	public $incrementing = false;

	public $timestamps = false;

	public static function getRoleUser(array $filters = [])
	{
		$query = RolUsuario::query();

		if(isset($filters['codigo_usuario']) && !empty($filters['codigo_usuario'])){
			$query->where('user_id', '=', $filters['codigo_usuario']);
		}

		if(isset($filters['role']) && !empty($filters['role'])){
			$query->where('role_id', '=', $filters['role']);
		}

		$query->where('codigo_institucion', session('institution')->codigo_institucion);

		return $query->first();
	}

	public static function getRolesByUser($codigo_usuario)
	{
		$query = Rol::query();
		$query->select('rol.id', 'rol.title', 'rol.description', DB::raw("(CASE WHEN rol_usuario.role_id IS NOT NULL THEN true ELSE false END) as comparator"));
		$query->join('seguridad.rol_institucion', 'rol.id', '=', 'rol_institucion.role_id');

		$query->leftJoin('seguridad.rol_usuario', function($join) use($codigo_usuario) {
			$join->on('rol.id', '=', 'rol_usuario.role_id')
				->on('rol_usuario.codigo_institucion', '=', 'rol_institucion.codigo_institucion')
				->where('rol_usuario.user_id', '=', $codigo_usuario);
		});

		$query->where('rol.id', '<>', Rol::getRol('superadmin'));
		$query->where('rol_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('rol_institucion.codigo_estado', Estado::getState('active'));
		$query->orderby('rol.title', 'asc');
		return $query->get();
	}

	public static function getRolUsuarioInstitucion($codigo_institucion, $codigo_usuario, $codigo_rol)
	{
		$query = RolUsuario::query();
		$query->where("codigo_institucion",$codigo_institucion);
		$query->where("user_id",$codigo_usuario);
		$query->where("role_id",$codigo_rol);
		return $query->first();
	}
}
