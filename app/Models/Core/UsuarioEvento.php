<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\UsuarioInstitucion,
	App\RegistroSesion,
	App\Models\Seguridad\Usuario;
use DB;

class UsuarioEvento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.usuario_evento';

   	protected $primaryKey = 'codigo_usuario_evento';

	public $timestamps = false;

	public static function usuariosEvento(array $filters = [])
	{
		$query = UsuarioEvento::query();
		$query->select('identificacion_usuario', 'movil_usuario', 'correo_electronico', 'fecha_usuario_evento', 'usuario.codigo_usuario', 'usuario_evento.codigo_usuario_evento',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as nombre_usuario"),
			(isset($filters['numero_sesion']) && !empty($filters['numero_sesion']) ?
				DB::raw("(CASE WHEN codigo_asistencia_sesion IS NULL THEN false ELSE true END) AS assist_to_event")
			: DB::raw('false as assist_to_event') ),
			(isset($filters['numero_sesion']) && !empty($filters['numero_sesion']) ? 'codigo_asistencia_sesion' : DB::raw('null as codigo_asistencia_sesion') )
		);
		$query->join('core.usuario_institucion', function($join){
			$join->on('usuario_evento.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion')
				->where('codigo_institucion', session('institution')->codigo_institucion);
		});

		$query->join('seguridad.usuario', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

		if(isset($filters['numero_sesion']) && !empty($filters['numero_sesion'])){
			$query->leftJoin('core.asistencia_sesion', function($join) use($filters){
				$join->on('usuario_evento.codigo_usuario_evento', '=', 'asistencia_sesion.codigo_usuario_evento')
					->where('codigo_sesion_evento', $filters['numero_sesion']);
			});
		}

		if(isset($filters['correo_electronico']) && !empty($filters['correo_electronico'])){
			$query->whereRaw("usuario.correo_electronico ilike '%".trim($filters['correo_electronico'])."%'");
		}

		if(isset($filters['identificacion_usuario']) && !empty($filters['identificacion_usuario'])){
			$query->where('identificacion_usuario', $filters['identificacion_usuario']);
		}

		$query->where('codigo_evento', $filters['codigo_evento']);
		$query->orderBy('fecha_usuario_evento', 'desc');
		$query->distinct();
		return $query->get();
	}

	public static function getEventsForUser(array $filters = [])
	{
		$query = Usuario::query();
		$query->select('codigo_usuario');
		if(isset($filters['identificacion_usuario']) && !empty($filters['identificacion_usuario'])){
			$query->where('identificacion_usuario', $filters['identificacion_usuario']);
			$query = $query->first();
		} elseif(isset($filters['email_usuario']) && !empty($filters['email_usuario'])){
			$query->where('correo_electronico', $filters['email_usuario']);
			$query = $query->first();
		} elseif(isset($filters['codigo_usuario']) && !empty($filters['codigo_usuario'])){
			$query->where('codigo_usuario', $filters['codigo_usuario']);
			$query = $query->first();
		}
		$usuario = $query;
		if(!$usuario instanceof Usuario){
			unset($usuario);
			$codigo_usuario = 0;
		} else {
			$codigo_usuario = $usuario->codigo_usuario;
		}
		
		$userInstitution = UsuarioInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
												->where('codigo_usuario', $codigo_usuario)->first();
		//dd('epa', $userInstitution);
		if(!$userInstitution instanceof UsuarioInstitucion){
			return collect([]);
		}

		$query = UsuarioEvento::query();
		$query->select(
			'evento.codigo_evento',
			'evento.nombre_evento',
			'evento.fecha_evento',
			'evento.fecha_fin_evento',
            DB::raw("
						(
							CASE
								WHEN ( count(sesion_evento.codigo_sesion_evento) > 0 AND count(sesion_evento.codigo_sesion_evento) = count(asistencia_sesion.codigo_asistencia_sesion) ) THEN true
								ELSE false
							END
						) as asistio"
						),
			'usuario_evento.certificado_evento',
			'usuario_evento.fecha_certificado',
			'usuario_evento.codigo_usuario_evento',
			'evento.factura_evento'
		);
		$query->join('core.usuario_institucion', function ($join) {
			$join->on('usuario_institucion.codigo_usuario_institucion', '=', 'usuario_evento.codigo_usuario_institucion')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.evento', 'evento.codigo_evento', '=', 'usuario_evento.codigo_evento');

		$query->leftJoin('core.sesion_evento', 'sesion_evento.codigo_evento', '=', 'evento.codigo_evento');

		$query->leftJoin('core.asistencia_sesion', function ($join) {
			$join->on('asistencia_sesion.codigo_usuario_evento', 'usuario_evento.codigo_usuario_evento')
				 ->whereRaw('sesion_evento.codigo_sesion_evento = asistencia_sesion.codigo_sesion_evento');
		});

		$query->where('usuario_institucion.codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion);

		$query->groupBy(
			'evento.codigo_evento', 'evento.nombre_evento', 'evento.fecha_evento',
			'evento.fecha_fin_evento', 'usuario_evento.codigo_usuario_evento'
		);
		$query->orderBy('usuario_evento.codigo_usuario_evento','desc');
		return $query->get();
		//$var = $query->get();
		//dd($var);
	}
}
