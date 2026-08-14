<?php

namespace App\Models\Core;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;

use DB, TuProyecto;

class GrupoUsuario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.grupo_usuario';

	public $timestamps = false;

	public $incrementing = false;

	public static function getWorkTeam(Request $request)
   	{
   		$users = [];
   		if($request->has('codigo_usuario')) {

			// Decrypt src-params
			list($codigo_usuario) = TuProyecto::decryptSrcParams($request->codigo_usuario);
			if(!$codigo_usuario || !is_numeric($codigo_usuario)) {
				abort(404);
			}

			$query = GrupoUsuario::query();
			$query->select('nombre_usuario as text', 'grupo_usuario.codigo_grupo_usuario as id');

			$query->join('core.entidad', 'grupo_usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
			$query->join('seguridad.usuario', 'grupo_usuario.seg_codigo_usuario', '=', 'usuario.codigo_usuario');

			$query->where('grupo_usuario.codigo_usuario', $codigo_usuario);
			$query->where('grupo_usuario.codigo_estado', Estado::getState('active'));

			$users = $query->get();
	   		return $users;
		}
		return $users;
   	}

	public static function getUserWorkTeam($codigo_grupo_usuario)
	{
		$query = GrupoUsuario::query();
		$query->select(DB::raw("(nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as nombre_usuario"), 'nombre_rol_usuario_grupo', 'correo_electronico', 'grupo_usuario.seg_codigo_usuario', 'codigo_grupo_usuario');

		$query->join('core.entidad', 'grupo_usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario', 'grupo_usuario.seg_codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftjoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->leftjoin('core.rol_usuario_grupo_insti', 'grupo_usuario.codigo_rol_usuario_grupo_insti', '=', 'rol_usuario_grupo_insti.codigo_rol_usuario_grupo_insti');
		$query->leftjoin('core.rol_usuario_grupo', 'rol_usuario_grupo_insti.codigo_rol_usuario_grupo', '=', 'rol_usuario_grupo.codigo_rol_usuario_grupo');

		$query->where('grupo_usuario.codigo_grupo_usuario', $codigo_grupo_usuario);
		$query->where('grupo_usuario.codigo_estado', Estado::getState('active'));

		$user = $query->first();
		return $user;
	}

	/**
	 * Build UserTeam.
	 */
   	public static function buildUserWorkTeam($userWorkTeam)
   	{
		$object = new \stdClass();
		$object->nombre_usuario_acuerdo = $userWorkTeam->nombre_usuario;
		$object->cargo_usuario_acuerdo = $userWorkTeam->nombre_rol_usuario_grupo;
		$object->correo_usuario_acuerdo = $userWorkTeam->correo_electronico;
		$object->codigo_grupo_usuario = $userWorkTeam->codigo_grupo_usuario;
		return $object;
	}
}
