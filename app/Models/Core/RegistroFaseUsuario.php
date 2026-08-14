<?php

namespace App\Models\Core;

use App\Models\Seguridad\Rol;
use Illuminate\Database\Eloquent\Model;

use DB, Auth, Input, Validator;

use App\Models;

use TuProyecto, Datatables;

use App\Models\Seguridad\Usuario;

class RegistroFaseUsuario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.registro_fase_usuario';

	protected $primaryKey = 'codigo_registro_fase_usuario';

	public $timestamps = false;

	public function isValid($data)
	{
		$rules = [
			'entity' => 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getProgramUser(Convocatoria $program, array $filters = [])
	{

		$query = RegistroFaseUsuario::query();
		$query->select(
			"fase.codigo_evaluacion_emprendimiento",
			"observaciones_registro_fase_usuario",
			"registro_fase_usuario.codigo_registro_fase_usuario",
			"registro_fase_usuario.codigo_usuario",
			"fecha_registro_fase_usuario",
			"hora_registro_fase_usuario",
			"registro_fase_usuario.codigo_fase",
			"registro_fase_usuario.visualizacion_usuario",
			"convocatoria.codigo_convocatoria",
			"codigo_orientacion_convocatoria",
			"usuario.correo_electronico as correo_electronico",
			'url_encuesta',
			'encuesta.codigo_encuesta',
			'nombre_fase',
			'numero_registro_convocatoria',
			'registro_convocatoria.codigo_usuario_encuesta',
			'identificacion_usuario',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("initcap((CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END)) as nombre_usuario")
		);

		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');

		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->leftjoin('core.pais_institucion', function($join) {
			$join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
				->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
		$query->whereNotNull('distrito_ciudad');
		$query->whereRaw("distrito_ciudad != ''");

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if(isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if(isset($filters['city']) && !empty($filters['city'])){
			$query->where('usuario.codigo_ciudad', $filters['city']);
		}

		if(isset($filters['identificacion']) && !empty($filters['identificacion'])){
			$query->where('usuario.identificacion_usuario', 'like', "%" . trim($filters['identificacion']) . "%");
		}

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

    // States filter
		$query->whereIn('registro_fase_usuario.codigo_estado', [ Estado::getState('finalized'), Estado::getState('active')	]);

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('codigo_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}

	public static function getProgramPropaceCourses(Convocatoria $program, array $filters = [])
	{
		$query = RegistroFaseUsuario::query();
		$query->select(
			"fase.codigo_evaluacion_emprendimiento",
			"observaciones_registro_fase_usuario",
			"registro_fase_usuario.codigo_registro_fase_usuario",
			"registro_fase_usuario.codigo_usuario",
			"fecha_registro_fase_usuario",
			"hora_registro_fase_usuario",
			"registro_fase_usuario.codigo_fase",
			"registro_fase_usuario.visualizacion_usuario",
			"convocatoria.codigo_convocatoria",
			"codigo_orientacion_convocatoria",
			"usuario.correo_electronico as correo_electronico",
			'url_encuesta',
			'encuesta.codigo_encuesta',
			'nombre_fase',
			'numero_registro_convocatoria',
			'registro_convocatoria.codigo_usuario_encuesta',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("initcap((CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END)) as nombre_usuario")
		);

		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');

		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->leftjoin('core.pais_institucion', function($join) {
			$join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
				->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
		$query->whereNotNull('distrito_ciudad');
		$query->whereRaw("distrito_ciudad != ''");

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if(isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if(isset($filters['city']) && !empty($filters['city'])){
			$query->where('usuario.codigo_ciudad', $filters['city']);
		}

		// States filter
		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active')
		]);

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('codigo_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}

  public static function getProgramUserAndRol(Convocatoria $program, array $filters = [])
  {
    $query = RegistroFaseUsuario::query();
    $query->select(
      "fase.codigo_evaluacion_emprendimiento",
      "observaciones_registro_fase_usuario",
      "registro_fase_usuario.codigo_registro_fase_usuario",
      "registro_fase_usuario.codigo_usuario",
      "fecha_registro_fase_usuario",
      "hora_registro_fase_usuario",
      "registro_fase_usuario.codigo_fase",
      "registro_fase_usuario.visualizacion_usuario",
      "convocatoria.codigo_convocatoria",
      "codigo_orientacion_convocatoria",
      "usuario.correo_electronico as correo_electronico",
      'url_encuesta',
      'encuesta.codigo_encuesta',
      'nombre_fase',
      'registro_convocatoria.numero_registro_convocatoria',
      'registro_convocatoria.codigo_usuario_encuesta',
      DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
      DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
      DB::raw("initcap((CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END)) as nombre_usuario"),
      DB::raw("( CASE WHEN role.role_id IS NOT NULL THEN true ELSE false END) as mentor")
    );

    $query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');

    // User Program
    $query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
    $query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
    $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
    $query->join('core.convocatoria_institucion', function($join) {
      $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
        ->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    });

    // User application
    $query->join('core.registro_convocatoria as regco', function ($join) {
      $join->on('regco.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')
        ->where('regco.codigo_estado', '=', Estado::getState('active'));
    });

    $query->leftJoin('seguridad.usuario as usrol', 'usrol.codigo_usuario', '=', 'regco.codigo_usuario');
    $query->leftJoin('seguridad.rol_usuario as role', function ($join) {
      $join->on('role.user_id', '=', 'usrol.codigo_usuario')
        ->where('role.codigo_institucion', '=', session('institution')->codigo_institucion)
          ->where('role.role_id', '=', Rol::getRol('mentor'));
    });

    $query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
    $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

    // City / Country
    $query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
    $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

    $query->leftjoin('core.pais_institucion', function($join) {
      $join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
        ->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
        ->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
    });
    $query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
    $query->whereNotNull('distrito_ciudad');
	$query->whereRaw("distrito_ciudad != ''");


    if(isset($filters['email']) && !empty($filters['email'])){
      $email = trim($filters['email']);
      $query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
    }

    if(isset($filters['number_register']) && !empty($filters['number_register'])){
      $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
    }

    if(isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
      $query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
    }

    if(isset($filters['city']) && !empty($filters['city'])){
      $query->where('usuario.codigo_ciudad', $filters['city']);
	}

    $query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

    return Datatables::of($query)
      ->addColumn('codigo_registro_fase_usuario', function ($data) {
        return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
      })
      ->addColumn('codigo_usuario', function ($data) {
        return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
      })
      ->addColumn('codigo_usuario_encuesta', function ($data) {
        return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
      })
      ->make(true);
  }

	public static function getProgramEntity(Convocatoria $program, array $filters = [])
	{
		$participants = [];
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico",
			DB::raw("initcap((CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END)) as nombre_usuario"), 'numero_registro_convocatoria','fase.codigo_fase', 'fase.nombre_fase', 'estado.nombre_estado as estado',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw("COALESCE((puntaje_registro_fase.valor_puntaje_registro_fase), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta)) as puntaje"),
			'registro_convocatoria.codigo_usuario_encuesta',
			"person.identificacion_usuario"
		);

		$query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->leftJoin('core.puntaje_registro_fase', 'puntaje_registro_fase.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');

		// User / Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

		// City / Country
		$query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query-> where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query-> where('convocatoria.codigo_estado', Estado::getState('active'));
		// States filter
		$query-> whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected')
		]);

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['emprendimiento']) && !empty($filters['emprendimiento'])) {
			$query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( isset($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->whereIn('registro_fase_usuario.codigo_fase', explode(',',$filters['phase']));
		}

		if (isset($filters['identificacion']) && $filters['identificacion'] && !empty($filters['identificacion'])) {
			$query->where('person.identificacion_usuario', 'like', "%" . trim($filters['identificacion']) . "%");
		}

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}

  public static function getProgramEntityRol(Convocatoria $program, array $filters = [])
  {
    $participants = [];
    $query = RegistroFaseUsuario::query();
    $query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico",
      DB::raw("initcap((CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END)) as nombre_usuario"), 'registro_convocatoria.numero_registro_convocatoria',
      DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
      DB::raw("( CASE WHEN role.role_id IS NOT NULL THEN true ELSE false END) as provedor"), 'encuesta.url_encuesta', 'usuario_encuesta.codigo_usuario_encuesta'
    );

    $query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
    $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
    $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

    // User / Program
    $query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
    $query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
    $query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
    $query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
    $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
    $query->join('core.convocatoria_institucion', function($join) {
      $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
        ->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    });

    // User application
    $query->join('core.registro_convocatoria as regco', function ($join) {
      $join->on('regco.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')
        ->where('regco.codigo_estado', '=', Estado::getState('active'));
    });

    $query->join('core.entidad as entuser', 'entuser.codigo_usuario', '=', 'regco.codigo_usuario');

    $query->leftJoin('seguridad.usuario as usrol', 'usrol.codigo_usuario', '=', 'entuser.seg_codigo_usuario');
    $query->leftJoin('seguridad.rol_usuario as role', function ($join) {
      $join->on('role.user_id', '=', 'usrol.codigo_usuario')
        ->where('role.codigo_institucion', '=', session('institution')->codigo_institucion)
        ->where('role.role_id', '=', Rol::getRol('seller'));
    });

    $query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
    $query->join('core.usuario_encuesta', 'usuario_encuesta.codigo_usuario_encuesta', '=', 'registro_convocatoria.codigo_usuario_encuesta');

    // City / Country
    $query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
    $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

    $query-> where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
    $query-> where('convocatoria.codigo_estado', Estado::getState('active'));
    // States filter
    $query-> whereIn('registro_fase_usuario.codigo_estado', [
      Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected')
    ]);

    if(isset($filters['email']) && !empty($filters['email'])){
      $email = trim($filters['email']);
      $query->whereRaw("trim(lower(person.correo_electronico)) = trim(lower('$email'))");
    }

    if(isset($filters['emprendimiento']) && !empty($filters['emprendimiento'])) {
      $query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
    }

    if(isset($filters['number_register']) && !empty($filters['number_register'])){
      $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
    }

    if(isset($filters['city']) && !empty($filters['city'])){
      $query->where('person.codigo_ciudad', $filters['city']);
    }

    return Datatables::of($query)
      ->addColumn('codigo_registro_fase_usuario', function ($data) {
        return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
      })
      ->addColumn('codigo_usuario_encuesta', function ($data) {
        return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
      })
      ->make(true);
  }

	public static function getRegisterPhase($registro)
	{
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario', 'registro_fase_usuario.valor_asignacion_rfu', 'registro_fase_usuario.codigo_usuario','fase.codigo_evaluacion_emprendimiento', 'evaluacion_emprendimiento.comentario_evaluacion_cerrado', 'registro_fase_usuario.codigo_registro_convocatoria',
			DB::raw("(person.nombre_usuario || ' ' || COALESCE(creador.primer_apellido_persona, '') || ' ' || COALESCE(creador.segundo_apellido_persona, '')) as nombre_usuario_creador" ),
			DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END) as nombre_usuario"),'convocatoria.codigo_convocatoria','observaciones_registro_fase_usuario', 'usuario.biografia_usuario', 'registro_convocatoria.fecha_registro_convocatoria', 'registro_convocatoria.hora_registro_convocatoria',
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'), 'nombre_fase'
		);
		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona as creador', 'person.codigo_usuario', '=', 'creador.codigo_usuario');
		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->leftJoin('core.evaluacion_emprendimiento', 'evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento');
		$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		return  $query->first();
	}

	public static function getRegisterjury($registro)
	{
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario','registro_fase_usuario.codigo_usuario','fase.codigo_evaluacion_emprendimiento',"entity.nombre_usuario as nombre_entidad", 'asignacion_especialidad',
			DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"),'convocatoria.codigo_convocatoria','observaciones_registro_fase_usuario',
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),'convocatoria.nombre_convocatoria', 'nombre_fase', 'fecha_registro_fase_usuario', 'fase.codigo_fase'
		);

		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->leftjoin('core.asignacion_jurado', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');

		 // User institution
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'entity.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query-> where('registro_fase_usuario.codigo_registro_fase_usuario', $registro);
		$query-> where('convocatoria.codigo_estado', Estado::getState('active'));

		return  $query->first();
	}

	public static function programUser(Convocatoria $program, array $filters = [])
	{
		$query = RegistroFaseUsuario::query();
		$query->select("usuario.codigo_usuario", "registro_fase_usuario.codigo_fase", "convocatoria.codigo_convocatoria", "usuario.correo_electronico as correo_electronico", "nombre_fase",	"numero_registro_convocatoria", 	"imagen_usuario", "nickname_usuario",
			DB::raw("trim(initcap(nombre_ciudad)) as nombre_ciudad"),
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END) as user_name")
		);

		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');

		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->leftjoin('core.pais_institucion', function($join) {
			$join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
				->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
		$query->whereNotNull('distrito_ciudad');
		$query->whereRaw("distrito_ciudad != ''");

		if(isset($filters['correo_electronico']) && !empty($filters['correo_electronico'])){
			$email = trim($filters['correo_electronico']);
			$query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
		}

		if( isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario']) ) {
			$query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($filters['nombre_usuario'])."%'");
		}

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

		// Get total posts
		$total_entries = $query->get()->count();

		// Get posts
		$data = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $total_entries;
		$response->items = $data;
		return $response;
	}

	/**
	* Build ParticipantProgram.
	*/
	public static function buildParticipant($partcipant)
	{
		$object = new \stdClass();
		$object->codigo_usuario = TuProyecto::encryptSrcParams([$partcipant->codigo_usuario]);
		$object->id_registro = $partcipant->codigo_registro_fase_usuario;
		$object->codigo_convocatoria = $partcipant->codigo_convocatoria;
		$object->codigo_registro_fase_usuario = TuProyecto::encryptSrcParams([$partcipant->codigo_registro_fase_usuario]);
		$object->total_evaluation = $partcipant->total_evaluation;
		$object->codigo_estado = $partcipant->codigo_estado;
		$object->nickname_usuario = $partcipant->nickname_usuario;
		$object->codigo_encuesta = $partcipant->codigo_encuesta;
		$object->nombre_usuario = $partcipant->nombre_usuario;
		$object->correo_electronico = $partcipant->correo_electronico;
		$object->nombre_entidad = $partcipant->nombre_entidad;
		$object->codigo_orientacion_convocatoria = $partcipant->codigo_orientacion_convocatoria;
		$object->visualizacion_usuario = $partcipant->visualizacion_usuario;
		$object->url_encuesta = $partcipant->url_encuesta;
		$object->fecha_registro = $partcipant->fecha_registro_fase_usuario;
		$object->hora_registro_fase_usuario = $partcipant->hora_registro_fase_usuario;
		$object->codigo_fase = $partcipant->codigo_fase;
		$object->nombre_fase = $partcipant->nombre_fase;
		$object->observaciones_registro_fase_usuario = $partcipant->observaciones_registro_fase_usuario;
		$object->documentado = $partcipant->documentado;
		$object->codigo_evaluacion_emprendimiento = $partcipant->codigo_evaluacion_emprendimiento;
		$object->asignacion_especialidad = $partcipant->asignacion_especialidad;
		$object->nombre_convocatoria = $partcipant->nombre_convocatoria;
		$object->owner =  Auth::check() && Auth::user()->codigo_usuario == $partcipant->codigo_usuario ? true : false;
		return $object;
	}
}
