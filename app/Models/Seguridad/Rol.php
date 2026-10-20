<?php

namespace App\Models\Seguridad;

use Input, Cache, Validator, Auth, DB, Log;

use Laratrust\Models\Role as LaratrustRole;

use App\Traits\AppLogsActivity;

use App\Models\Core\Estado, App\Models\BaseModel;

class Rol extends LaratrustRole
{
    use AppLogsActivity;

	public $timestamps = false;

	public static $admin = 2;

    protected static $logAttributes = ['title', 'description'];

	public function getDescriptionForEvent(string $eventName): string
    {
    	return "activitylog.role.$eventName";
    }

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'title'			=> 'required',
			'description'	=> 'required|max:200'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getRol($role) {
    	switch ($role) :
		    case 'superadmin': return 1;
		    case 'admin': return 2;
			case 'user': return 3;
			case 'mentor': return 7;
			case 'evaluador': return 9;
			case 'seller': return 14;
			case 'associated': return 39;
			default: return 'Not role identified';
		endswitch;
    }

	public static function getTypeRole($type) {
    	switch ($type) :
		    case 'platform': return 1;
		    case 'administrator': return 2;
			default: return 'Not role identified';
		endswitch;
    }

	public static function getRole($id)
	{

		$query = Rol::query();
		$query->join('seguridad.rol_institucion', function($join){
			$join->on('rol.id', '=', 'rol_institucion.role_id')
				->where('rol_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('rol_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->where('rol.id', '<>', self::getRol('superadmin'));
		$query->where('rol.id', $id);
		return $query->first();
	}

  public static function getRoleId($role) {
    switch ($role) :
      case 'superadmin': return 1;
      case 'admininstitution': return 2;
      case 'user': return 3;
      case 'mentor': return 7;
      case 'seller': return 14;
      case 'evaluator': return 9;
      default: return 'Not role identified';
    endswitch;
  }

	public static function getRoles(array $filters = [])
	{

		$query = Rol::query();
		$query->select('id', 'title', 'description', DB::raw( "(CASE WHEN rol.tipo_rol = ".self::getTypeRole('administrator')." THEN 'Administrador' ELSE 'Plataforma' END) as type"), 'rol_institucion.codigo_estado');
		$query->join('seguridad.rol_institucion', function($join){
			$join->on('rol.id', '=', 'rol_institucion.role_id')
				->where('rol_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		if( isset($filters['title']) && $filters['title'] ) {
			$query->whereRaw("title ilike '%".trim($filters['title'])."%'");
		}

		if( isset($filters['codigo_estado']) && $filters['codigo_estado'] ) {
			$query->where("rol_institucion.codigo_estado", $filters['codigo_estado']);
		}

		$query->where('rol.id', '<>', self::getRol('superadmin'));
		$query->orderby('title', 'asc');
		return $query->get();
	}

	public static function getUsersByRole($role)
	{
		$query = Rol::query();
		$query->select('usuario.codigo_usuario', 'usuario.biografia_usuario as biografia_usuario', 'imagen_usuario', 'ciudad.nombre_ciudad', DB::raw("(usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) as user_name"), 'nickname_usuario', 'direccion_usuario', 'profesion_usuario', 'correo_electronico', 'fecha_nacimiento_usuario', 'telefono_usuario', 'movil_usuario');

		$query->join('seguridad.rol_usuario', 'rol.id', '=', 'rol_usuario.role_id');
		$query->join('seguridad.usuario', 'rol_usuario.user_id', '=', 'usuario.codigo_usuario');
		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->join('core.ciudad','usuario.codigo_ciudad','=','ciudad.codigo_ciudad');

		$query->where('id', $role);
		$query->where('rol_usuario.codigo_institucion', session('institution')->codigo_institucion);

		$query->orderBy('user_name', 'asc');

		// Get total posts
		$total_entries = $query->get()->count();

		$data = $query->skip(Input::get('offset'))->take(Input::get('limit'))->get();
		$response = new \stdClass();
		$response->total_entries = $data->count() > 0 ? $total_entries: 0;
		$response->items = $data;
   		return $response;
	}

	public static function getRolesByUser($codigo_usuario)
	{
		$query = Rol::query();
		$query->select('id', 'title', 'description');
		$query->join('seguridad.rol_usuario', 'rol.id', '=', 'rol_usuario.role_id');
		$query->where('rol.id', '<>', self::getRol('superadmin'));
		$query->where('rol_usuario.user_id', $codigo_usuario);
		$query->where('rol_usuario.codigo_institucion', '=', session('institution')->codigo_institucion);
		return $query->get();
	}

    //Big block of caching functionality.
    public function cachedPermissions($module = null)
    {
        $rolePrimaryKey = $this->primaryKey;
        $modulePrimaryKey = $module ?: 'general';
        $cacheKey = "entrust_permissions_for_role_{$this->$rolePrimaryKey}_$modulePrimaryKey";

        return Cache::tags(config('laratrust.tables.permission_role'))->remember($cacheKey, config('cache.ttl'), function () use($module) {
        	$query = $this->perms();
        	if( !is_null($module) ) {
            	$query->where('modulo.url_modulo', $module);
        	}
        	return $query->get();
        });
    }

    /**
     * Many-to-Many relations with the permission model.
     * Named "perms" for backwards compatibility. Also because "perms" is short and sweet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function perms()
    {
        return $this->belongsToMany(config('laratrust.models.permission'), config('laratrust.tables.permission_role'), 'role_id', 'permission_id')->leftJoin('seguridad.modulo', 'modulo.codigo_modulo', '=', 'permiso_rol.codigo_modulo')->where(function($where){
				$where->where('modulo.codigo_tipo_modulo', '=', Modulo::getTypeModule('administrator'));
				$where->orWhereNull('permiso_rol.codigo_modulo');
		});
    }

	/**
     * Attach permission to current role.
     *
     * @param object|array $permission
     *
     * @return void
     */
    public function attachPermission($permission, $params = [])
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->perms()->attach($permission, $params);
    }
}
