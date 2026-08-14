<?php

namespace App\Models\Core;

use DB;

use Illuminate\Database\Eloquent\Model;

use App\Models\Seguridad\Usuario, App\Models\Core\Estado;

class CanalUsuario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.canal_usuario';

   	protected $primaryKey = 'codigo_canal_usuario';

   	public $timestamps = false;

	protected static $logAttributes = ['codigo_canal', 'codigo_usuario', 'codigo_estado', 'codigo_usuario_institucion'];

	public static function getUsersChannel(array $filters = [])
	{
		$query = Usuario::query();
		$query->select('usuario.codigo_usuario', 'usuario.imagen_usuario', 'usuario.correo_electronico', 'usuario.nickname_usuario', DB::raw('initcap(nombre_ciudad) as nombre_ciudad'), DB::raw("(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as user_name"), 'canal_usuario.codigo_canal_usuario', 'canal_usuario.codigo_estado');
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
				// ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.canal_usuario', function($join) use($filters) {
			$join->on('usuario_institucion.codigo_usuario_institucion', '=', 'canal_usuario.codigo_usuario_institucion')
				->where('canal_usuario.codigo_canal', '=', $filters['channel']);
		});

		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');

		if(isset($filters['correo_electronico']) && !empty($filters['correo_electronico'])){
			$query->whereRaw("correo_electronico ilike '%".$filters['correo_electronico']."%'");
		}

		if(isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario'])){
			$query->whereRaw("nombre_usuario ilike '%".$filters['nombre_usuario']."%'");
		}

		// Get total posts
		$total_entries = $query->count();

		$data = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $data->count() > 0 ? $total_entries: 0;
		$response->items = $data;
   		return $response;
	}

	public static function getUserchannel($cahnnel, $user, $institution)
	{
		$query = CanalUsuario::query();
		$query->select('codigo_canal', 'codigo_usuario', 'canal_usuario.codigo_estado', 'canal_usuario.codigo_usuario_institucion', 'codigo_rol');
		$query->join('core.usuario_institucion', 'canal_usuario.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');
		$query->where('usuario_institucion.codigo_usuario_institucion', '=', $institution);
		$query->where('usuario_institucion.codigo_usuario', '=', $user);
		$query->where('canal_usuario.codigo_canal', '=', $cahnnel);
		return $query->first();
	}

	public static function usersChannelNotification(array $filters = []) 
	{
		$query = Canalusuario::query();
		$query->select(DB::raw('distinct usuario.codigo_usuario'));

		$query->join('core.usuario_institucion', function($join) use($filters) {
			$join->on('canal_usuario.codigo_usuario_institucion', '=', 'canal_usuario.codigo_usuario_institucion')
				->whereIn('canal_usuario.codigo_canal', is_array($filters['channels']) ? $filters['channels']: [$filters['channels']]);
		});

		$query->join('seguridad.usuario', function($join) use($filters){
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', $filters['codigo_institucion']);
		});

		if(isset($filters['users']) && !empty($filters['users'])){
			$query->whereNotIn('usuario.codigo_usuario', $filters['users']);
		}
		
		return $query->pluck('usuario.codigo_usuario')->toArray();
	}
}
