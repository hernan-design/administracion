<?php

namespace App\Models\Core;


use Illuminate\Database\Eloquent\Model;

use Datatables, DB, Input, Validator, Lang;

use App\Models\Seguridad\Usuario, App\Models\Seguridad\Rol, App\Models\Core\Convocatoria;

class Entidad extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.entidad';

   	protected $primaryKey = 'codigo_entidad';

   	public $timestamps = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
		'codigo_usuario',
		'codigo_categoria_clase_espacio',
		'video_entidad',
		'seg_codigo_usuario',
		'descripcion_entidad',
		'url_entidad',
		'contador_comentario',
		'contador_like',
		'facebook_entidad',
		'twitter_entidad',
		'linkedin_entidad',
		'instagram_entidad',
		'entidad_apoyo_emprendimiento',
		'codigo_clase_empre_insti',
		'entidad_internacional',
		'nombre_representante_legal',
		'identificacion_representate_legal',
		'codigo_tipo_sociedad',
		'cubrimiento',
		'fecha_inicio_operacion',
		'codigo_sector_interes',
		'cubrimiento_entidad',
		'codigo_tipo_identificacion',
		'correo_contacto_entidad'
	];

	public static $cover = [1 => 'Local', 2 => 'Regional', 3 => 'Nacional', 4 => 'Internacional', 5 => 'Sin registro'];

	public static function getEntity($entity)
	{
		$query = Entidad::query();
        $query->select('entidad.*', 'usuario.*');
		$query->join('seguridad.usuario', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->join('core.usuario_institucion', function($join){
        $join->on('entidad.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
            ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
        });
        $query->join('param.institucion', 'institucion.codigo_institucion', '=', 'usuario_institucion.codigo_institucion');
        $query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');

        // Founder City / Country
        $query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');

        // User City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->where('entidad.codigo_usuario', '=', $entity);
        $query->where('usuario.codigo_estado', '=', Estado::getState('active'));

		return $query->first();
	}

	public static function getEntitys($entity)
	{
		$query = Entidad::query();
        $query->select('entidad.*', 'usuario.*');
		$query->join('seguridad.usuario', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->join('core.usuario_institucion', function($join){
        $join->on('entidad.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
            ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
        });
		$query->join('core.clase_emprendimiento_insti', function($join) {
            $join->on('entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti')
                ->where('clase_emprendimiento_insti.codigo_institucion', '=',session('institution')->codigo_institucion)
                ->where('clase_emprendimiento_insti.codigo_estado', '=', Estado::getState('active'));
        });
        $query->join('param.institucion', 'institucion.codigo_institucion', '=', 'usuario_institucion.codigo_institucion');
        $query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');

        // Founder City / Country
        $query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');

        // User City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		//$query->where('entidad.codigo_usuario', '=', $entity);
		$query->where('usuario.nickname_usuario', '=', $entity);
        $query->where('usuario.codigo_estado', '=', Estado::getState('active'));

		return $query->first();
	}

	/**
	 * Type user list
	 *
	 * @var array
	 */
	public static $typeEntityList = [1 => 'Empresa', 2  => 'Proyecto'];


	public static function entities(array $filters = [])
	{
		$query = Usuario::query();
		$query->select(
			'usuario.codigo_usuario', 
			'usuario.nickname_usuario', 
			'usuario.codigo_estado',
			'usuario.identificacion_usuario',
			DB::raw("initcap(CASE WHEN usuario.tipo_usuario = ".Usuario::typeUser('person')." THEN ( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) ELSE usuario.nombre_usuario END) AS nombre_usuario"), 'founder.codigo_usuario as codigo_founder', 'founder.nickname_usuario as nickname_founder', 'founder.imagen_usuario as imagen_founder',
			DB::raw('initcap(etapa_proyecto.nombre_etapa_proyecto) as nombre_etapa_proyecto'),
			DB::raw('initcap(categoria_proyecto.nombre_categoria_proyecto) as nombre_categoria_proyecto'),
			DB::raw("trim(initcap(ciudadf.nombre_ciudad)) as ciudad_founder"),
			DB::raw("COALESCE(initcap(ciudadf.distrito_ciudad), '') as distrito_ciudad_founder"), 'paisf.nombre_pais as pais_founder',
			DB::raw("initcap(CASE WHEN founder.tipo_usuario = ".Usuario::typeUser('person')." THEN (trim(founder.nombre_usuario) || ' ' || trim(personaf.primer_apellido_persona) || ' ' || trim(personaf.segundo_apellido_persona)) ELSE founder.nombre_usuario END) as nombre_founder")
		);

		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')])
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');

		// Founder City / Country
		$query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');
		$query->leftjoin('core.ciudad as ciudadf', 'founder.codigo_ciudad', '=', 'ciudadf.codigo_ciudad');
		$query->leftjoin('core.pais as paisf', 'ciudadf.codigo_pais', '=', 'paisf.codigo_pais');
		$query->leftjoin('core.persona as personaf', 'personaf.codigo_usuario', '=', 'founder.codigo_usuario');

		$query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
		$query->leftjoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
		$query->leftjoin('core.etapa_proyecto', 'dato_entidad.codigo_etapa_proyecto', '=', 'etapa_proyecto.codigo_etapa_proyecto');

		if(isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario'])){

			$cadena = trim($filters['nombre_usuario']);

			$patrones = [];
			$patrones[0] = '/\"/';
			$patrones[1] = '/\'/';

			$cadena = preg_replace($patrones, "", $cadena);
			
			$query->whereRaw(
				"(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || 
				  COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || 
				  COALESCE(trim(persona.segundo_apellido_persona), '')) 
				  ilike '%". $cadena ."%'"
			);
		}

		if(isset($filters['codigo_etapa_proyecto']) && !empty($filters['codigo_etapa_proyecto'])){
			$query->where('dato_entidad.codigo_etapa_proyecto', '=', $filters['codigo_etapa_proyecto']);
		}

		if(isset($filters['codigo_categoria_proyecto']) && !empty($filters['codigo_categoria_proyecto'])){
			$query->where('categoria_proyecto.codigo_categoria_proyecto', '=', $filters['codigo_categoria_proyecto']);
		}

		if(isset($filters['correo_electronico_emprendedor']) && !empty($filters['correo_electronico_emprendedor'])){
			$query->where("founder.correo_electronico", '=', $filters['correo_electronico_emprendedor']);
		}

		if(isset($filters['identificacion_usuario']) && !empty($filters['identificacion_usuario'])){
			$query->where("usuario.identificacion_usuario", 'like', $filters['identificacion_usuario'].'%');
		}

		$query->whereIn('usuario.codigo_estado', [Estado::getState('active'), Estado::getState('inactive')]);

		$query->distinct();
		$query->orderBy('nombre_usuario', 'asc');

		return Datatables::of( $query )->make(true);
	}

	public static function getEntitiesForProgram() {
		$query = Convocatoria::query();
		$query->select('usuario.codigo_usuario as id',  DB::raw('initcap(usuario.nombre_usuario) as text'));
		$query->join('core.convocatoria_institucion', 'convocatoria_institucion.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('seguridad.usuario', 'convocatoria.seg_codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		return $query->distinct()->get();
	}

	public static function getEntities(array $filters = [])
	{
		$query = Usuario::query();
		$query->select('usuario.codigo_usuario as id',
			DB::raw("initcap(CASE WHEN usuario.tipo_usuario = ".Usuario::typeUser('person')." THEN ( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) ELSE usuario.nombre_usuario END) AS text")
		);

		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')])
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

		switch (isset($filters['data']) ?? $filters['data']) {
			case Rol::getRol('seller'):
				$query->join('core.producto', function($join){
					$join->on('usuario.codigo_usuario', '=', 'producto.seg_codigo_usuario')
					->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);
				});
				$query->leftJoin('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');

				if(isset($filters['q']) && !empty($filters['q'])){
					$query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($filters['q'])."%'");
				}

			break;
		}

		$query->distinct();
		$query->orderBy('text', 'asc');
		return $query->take(50)->get();
	}

	/**
	* Entity data detail from url.
	*/
	public static function getEntityUrl($nickname)
	{
        $query = Entidad::query();
		$query->select('entidad.*', 'usuario.descripcion_adicional_usuario','usuario.fecha_nacimiento_usuario', 'usuario.telefono_usuario',
			'founder.identificacion_usuario as cedula_founder', 'founder.correo_electronico as correo_founder', 'founder.movil_usuario as movil_founder',
			DB::raw("trim(initcap(ciudad.nombre_ciudad)) as nombre_ciudad"),
			DB::raw("COALESCE(initcap(ciudad.distrito_ciudad), '') as distrito_ciudad"), 'clase_emprendimiento_insti.codigo_clase_empre_insti', 'pais.nombre_pais', 'nombre_privacidad_contenido', 'icono_privacidad_contenido', 'usuario.nombre_usuario', 'usuario.imagen_usuario', 'usuario.biografia_usuario', 'usuario.facebook_usuario', 'usuario.twitter_usuario', 'usuario.linkedin_usuario', 'usuario.pagina_web_usuario', 'usuario.nickname_usuario', 'usuario.tipo_identificacion_usuario' , 'usuario.identificacion_usuario', 'dato_entidad.codigo_etapa_proyecto',  'dato_entidad.codigo_privacidad_contenido', 'descripcion_entidad','usuario.codigo_ciudad', 'nombre_clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', 'usuario.cargo_entidad', 'founder.codigo_usuario as codigo_founder', 'founder.nickname_usuario as nickname_founder', 'founder.imagen_usuario as imagen_founder',  DB::raw("trim(initcap(ciudadf.nombre_ciudad)) as ciudad_founder"), DB::raw("COALESCE(initcap(ciudadf.distrito_ciudad), '') as distrito_ciudad_founder"), 'paisf.nombre_pais as pais_founder', DB::raw("(CASE WHEN founder.tipo_usuario = ".Usuario::typeUser('person')." THEN (trim(founder.nombre_usuario) || ' ' || trim(personaf.primer_apellido_persona) || ' ' || trim(personaf.segundo_apellido_persona)) ELSE founder.nombre_usuario END) as nombre_founder"), 'dato_entidad.codigo_categoria_proyecto', 'categoria_proyecto.nombre_categoria_proyecto', 'dato_entidad.cor_codigo_categoria_proyecto', 'scategoria.nombre_categoria_proyecto as cor_nombre_categoria_proyecto', 'modelo_negocio_institucion');

		// User City / Country
		$query->join('seguridad.usuario', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		// Founder City / Country
		$query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');
		$query->leftjoin('core.ciudad as ciudadf', 'founder.codigo_ciudad', '=', 'ciudadf.codigo_ciudad');
		$query->leftjoin('core.pais as paisf', 'ciudadf.codigo_pais', '=', 'paisf.codigo_pais');
		$query->leftjoin('core.persona as personaf', 'personaf.codigo_usuario', '=', 'founder.codigo_usuario');

		$query->join('core.usuario_institucion', function($join){
       		$join->on('entidad.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
      	});
        $query->join('param.institucion', 'institucion.codigo_institucion', '=', 'usuario_institucion.codigo_institucion');

		$query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
		$query->join('core.clase_emprendimiento_insti', function($join) {
            $join->on('entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti')
                ->where('clase_emprendimiento_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('clase_emprendimiento_insti.codigo_estado', '=', Estado::getState('active'));
        });
		$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');

		$query->leftjoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
		$query->leftjoin('core.categoria_proyecto as scategoria', 'dato_entidad.cor_codigo_categoria_proyecto', '=', 'scategoria.codigo_categoria_proyecto');
		$query->leftjoin('core.privacidad_contenido', 'dato_entidad.codigo_privacidad_contenido', '=', 'privacidad_contenido.codigo_privacidad_contenido');

	  $query->whereRaw("trim(lower(usuario.nickname_usuario)) = trim(lower('$nickname'))");
		return $query->first();
	}

  /**
   * Valid create Entity.
   */
  public function isValid($data)
  {
    $rules = [
        'nombre_usuario' => 'required',
        'telefono_usuario' => 'required',
        'biografia_usuario'=>'required',
        'codigo_clase_empre_insti' =>'required',
        'codigo_ciudad' => 'required',
        'imagen_usuario' => 'required'
    ];

    $validator = Validator::make($data, $rules);
    if ($validator->passes()) {
      return true;
    }
    $this->errors = $validator->errors();
    return false;
  }

	/**
	* Build Entity.
	*/
   	public static function buildEntity($entity)
   	{
		$object = new \stdClass();
		$object->codigo_entidad = $entity->codigo_entidad;
		$object->codigo_usuario = $entity->codigo_usuario;
		$object->imagen_usuario = $entity->imagen_usuario;
		$object->descripcion_adicional_usuario = $entity->descripcion_adicional_usuario;
		$object->nombre_usuario = ucfirst(strtolower($entity->nombre_usuario));
		$object->telefono_usuario = $entity->telefono_usuario;
		$object->biografia_usuario = $entity->biografia_usuario;
		$object->nickname_usuario = $entity->nickname_usuario;
		$object->tipo_identificacion_usuario = $entity->tipo_identificacion_usuario;
		$object->identificacion_usuario = $entity->identificacion_usuario;
		$object->codigo_etapa_proyecto = $entity->codigo_etapa_proyecto;
		$object->cor_codigo_categoria_proyecto = $entity->cor_codigo_categoria_proyecto;
		$object->codigo_categoria_proyecto = $entity->codigo_categoria_proyecto;
		$object->codigo_privacidad_contenido = $entity->codigo_privacidad_contenido;
		$object->facebook_usuario = $entity->facebook_usuario;
		$object->twitter_usuario = $entity->twitter_usuario;
		$object->linkedin_usuario = $entity->linkedin_usuario;
		$object->instagram_usuario = $entity->instagram_usuario;
		$object->pagina_web_usuario = $entity->pagina_web_usuario;
		$object->cedula_founder = $entity->cedula_founder;
		$object->correo_founder = $entity->correo_founder;
		$object->movil_founder  = $entity->movil_founder;

		$object->codigo_clase_emprendimiento = $entity->codigo_clase_emprendimiento;
		$object->codigo_clase_empre_insti = $entity->codigo_clase_empre_insti;
		$object->entidad_apoyo_emprendimiento = $entity->entidad_apoyo_emprendimiento;
		$object->codigo_ciudad = $entity->codigo_ciudad;
		$object->nombre_ciudad = $entity->nombre_ciudad;
		$object->distrito_ciudad = $entity->distrito_ciudad;
		$object->nombre_pais = $entity->nombre_pais;
		$object->descripcion_entidad=  $entity->descripcion_entidad;
		$object->codigo_sector_interes = $entity->codigo_sector_interes;
		$object->codigo_tipo_sociedad = $entity->codigo_tipo_sociedad;
		$object->cubrimiento_entidad = $entity->cubrimiento_entidad;

		return $object;
   	}
}
