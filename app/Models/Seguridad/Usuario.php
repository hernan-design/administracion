<?php

namespace App\Models\Seguridad;

use App\Models\Core\UsuarioInstitucion;
use App\Models\Param\Institucion;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Greatsami\Entrust\Traits\EntrustUserTrait;

use DB, Cache, Config, Validator, Auth;
use App\Models\Param\TerminosCondiciones, App\Models\Param\Documento, App\Models\Core\Estado,
  App\Models\Core\Especialidad, App\Models\Core\Convocatoria, App\Models\Core\SectorEconomico, App\Models\Core\Persona, App\Models\Core\ItemEvaluacionCompra;

class Usuario extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use Authenticatable, CanResetPassword, EntrustUserTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seguridad.usuario';

	protected $primaryKey = 'codigo_usuario';

	public $timestamps = false;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_ciudad', 'nombre_usuario', 'fecha_nacimiento_usuario', 'movil_usuario',
    'correo_electronico', 'identificacion_usuario', 'tipo_identificacion_usuario', 'codigo_localidad_ciudad',
    'facebook_usuario', 'twitter_usuario', 'linkedin_usuario', 'instagram_usuario',
    'codigo_barrio_localidad', 'telefono_usuario','imagen_usuario','biografia_usuario', 'pagina_web_usuario'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_ciudad'	=> 'required',
			'nombre_usuario'	=> 'required',
			'fecha_nacimiento_usuario'	=> 'required',
			'movil_usuario'		=> 'required',
			'correo_electronico'	=> 'required',
			'identificacion_usuario'	=> 'required',
			'tipo_identificacion_usuario'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	* Valid create content.
	*/
	public function isValidMentor($data)
	{
		$rules = [
			//'codigo_especialidad_destaca'=> 'required',
			'experiencia_usuario'	=> 'required',
			'forma_ayuda_mentoria'	=> 'required',
			'stage_mentoring'		=> 'required',
		];

		$messages = [
			//'codigo_especialidad_destaca.required' => 'Debe seleccionar una especialidad destacada',
			'experiencia_usuario.required'	=> 'El campo "experiencia" es obligatorio',
			'forma_ayuda_mentoria.required'	=> 'El campo "Forma de ayuda de la mentoría" es obligatorio',
			'stage_mentoring.required'		=> 'Debe seleccionar un nivel de desarrollo ',
		];


		$validator = Validator::make($data, $rules, $messages);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public function isValidApi($data)
	{
		$rules = [
			'nombre_usuario'	=> 'required',
			'correo_electronico'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	 * Marital status
	 *
	 * @var array
	 */
	public static $maritalStatus = [1 => 'Soltero', 2 => 'Casado', 3 => 'Divorciado', 4 => 'Viudo', 5 => 'Union Libre'];

	/**
	 * Gender
	 *
	 * @var array
	 */
	public static $gender = [1 => 'Hombre', 2  => 'Mujer', 3 => 'Prefiero no decirlo'];


	public static function getGender($gender)
	{
		if(in_array($gender, array_keys(Usuario::$gender))) {
			return Usuario::$gender[$gender];
		}
		return '';
	}

	public static function getMaritalStatus($status)
    {
        if(in_array($status, array_keys(Usuario::$maritalStatus))) {
            return Usuario::$maritalStatus[$status];
        }
        return '';
	}

	public function getAveragePurchase($type)
   	{
   		$query = ItemEvaluacionCompra::query();
		$query->select('nombre_item_evaluacion_compra', 'item_evaluacion_compra.codigo_item_evaluacion_compra', 'item_evaluacion_compra.descripcion_item_eval_compra',  DB::raw("COALESCE(valor_promedio, 0) as valor_promedio "));

		$query->leftjoin('core.promedio_compra', function($join) {
			$join->on('item_evaluacion_compra.codigo_item_evaluacion_compra', '=', 'promedio_compra.codigo_item_evaluacion_compra');
			$join->where('promedio_compra.codigo_usuario', '=', $this->codigo_usuario);
        });

        $query->where('item_evaluacion_compra.codigo_estado', '=', Estado::getState('active'));
        $query->where('item_evaluacion_compra.tipo_item_evaluacion_compra', '=', $type);
        $query->where('item_evaluacion_compra.codigo_institucion', '=', session('institution')->codigo_institucion);
   		$query->orderby('nombre_item_evaluacion_compra','asc');

   		return $query->get()->toArray();
   	}


	public static function typeUser($type)
	{
		switch ($type) :
			case 'person': return 1;
			case 'entity': return 2;
			default: return 'Not type user identified';
		endswitch;
	}

	public static function getUserByEntity($user_code)
	{
		return Usuario::select(
				'usuario.identificacion_usuario as entity_id',
				'usuario.biografia_usuario as entity_description',
				'usuario.nombre_usuario as entity_name',
				DB::raw("(owner.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) as user_name"),
				'usuario.codigo_usuario',
				'usuario.nickname_usuario',
				'ciudad.nombre_ciudad',
				'e_ciudad.distrito_ciudad',
				'owner.correo_electronico',
				'persona.genero_persona',
				'owner.fecha_nacimiento_usuario',
				'owner.codigo_usuario as owner_id',
				'owner.direccion_usuario as direccion',
				'owner.nombre_usuario as short_name'
			)
			->join('core.entidad','usuario.codigo_usuario','=','entidad.codigo_usuario')
			->join('seguridad.usuario as owner','entidad.seg_codigo_usuario','=','owner.codigo_usuario')
			->join('core.persona','owner.codigo_usuario','=','persona.codigo_usuario')
			->join('core.ciudad','owner.codigo_ciudad','=','ciudad.codigo_ciudad')
			->join('core.ciudad as e_ciudad','usuario.codigo_ciudad','=','e_ciudad.codigo_ciudad')
			->where('usuario.codigo_usuario', '=', $user_code)
			->first();
	}

	public static function getPersonByUser($user_code)
	{
		return Usuario::select(
				DB::raw("initcap( (usuario.nombre_usuario || ' ' || persona.primer_apellido_persona || ' ' || persona.segundo_apellido_persona) ) as user_fullname"),
				'usuario.codigo_usuario', 'usuario.codigo_usuario', 'usuario.correo_electronico', 'telefono_usuario', 'direccion_usuario', 'movil_usuario',
				'usuario.nickname_usuario', 'persona.genero_persona', DB::raw('(CASE WHEN mentor.role_id IS NOT NULL THEN true ELSE false END) AS mentor'),
				'usuario.fecha_nacimiento_usuario', 'usuario.imagen_usuario', 'usuario.profesion_usuario', 'usuario.identificacion_usuario', 'usuario_institucion.ultimo_ingreso_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'usuario.nombre_usuario'
			)
			->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario')
      // User institution
		  ->join('core.usuario_institucion', function($join) {
      $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
        ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
      })

      	// Mentor
		  ->leftjoin('seguridad.rol_usuario as mentor', function($join) {
        $join->on('mentor.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
          ->on('mentor.user_id', '=', 'usuario_institucion.codigo_usuario')
          ->where('mentor.role_id', '=', Rol::getRol('mentor'));
      })
			->where('usuario.codigo_usuario', '=', $user_code)
			->first();
	}

	public static function storeApi($data)
	{
		try
		{
			$user = new Usuario;
			// Basic data validate
			if (!$user->isValidApi($data)) {
				throw new \Exception($user->errors);
			}

			// User
			$user->fill($data);
			$user->codigo_estado = Estado::getState('confirm');
			$user->tipo_usuario = Usuario::typeUser('person');
			$user->save();

			// Basic person validate
			$person = new Persona;
			if (!$person->isValid($data)) {
				throw new \Exception($person->errors);
			}
			// Person
			$person->fill($data);
			$person->codigo_usuario = $user->codigo_usuario;
			$person->save();

			// Return
			return $user;
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	public function getName()
   	{
   		$name = $this->attributes['nombre_usuario'];
   		if($this->attributes['tipo_usuario'] == self::typeUser('person')) {
   			$person = $this->persona;
   			if($person->primer_apellido_persona){
				return sprintf('%s %s', $name, $person->primer_apellido_persona);
   			}
   			if($person->segundo_apellido_persona){
				return sprintf('%s %s', $name, $person->segundo_apellido_persona);
   			}
   		}
   		return $name;
	}

	public static function getUserMail(array $filters = [])
	{
		$query = Usuario::query();
		$query->select('usuario.*',  DB::raw('initcap(nombre_ciudad) as nombre_ciudad'), DB::raw("(COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as user_name"));

		// User institution
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		if(isset($filters['role']) && !empty($filters['role']) ){

			switch ($filters['role']) {
				case 'mentor':
					$query->join('seguridad.rol_usuario as mentor', function($join) use($filters){
						$join->on('mentor.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
							->on('mentor.user_id', '=', 'usuario_institucion.codigo_usuario')
							->where('mentor.role_id', '=', Rol::getRol( $filters['role'] ));
					});
					break;

				default:
					break;
			};

		};

		$query->whereRaw("trim(lower(correo_electronico)) = trim(lower('$filters[email]'))");
		$query->whereRaw("trim(lower(correo_electronico)) != ''");
		$query->whereNotNull('correo_electronico');

		return $query->first();
	}

	/**
	 * Validate mail
	 */
	public static function validateMail(array $filters = [])
	{
		$query = Usuario::query();
		$query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$filters[correo_electronico]'))");
		$query->whereNotNull('usuario.correo_electronico');
		$query->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);

		return $query->first();
	}

  public static function validateUser(array $filters = [])
  {
    $query = Usuario::query();
    $query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$filters[correo_electronico]'))");
    $query->whereNotNull('usuario.correo_electronico');

    return $query->first();
  }

  public static function validateuserIdentification($user_identification)
  {
    $query = Usuario::query();
    $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
    $query->where("usuario.identificacion_usuario", "=", $user_identification);
    $query->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->whereNotNull('identificacion_usuario');

    return $query->first();
  }

  public static function getUsersEvaluators($user_identification) {
    $query = Usuario::query();
    $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
    $query->join('seguridad.rol_usuario', 'rol_usuario.user_id', '=', 'usuario.codigo_usuario');
    $query->where('rol_usuario.role_id', '=', Rol::getRoleId('evaluator'));
    $query->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('usuario.identificacion_usuario', '=', $user_identification);
    $query->whereNotNull('identificacion_usuario');

    return $query->first();
  }

  public static function getUsers(array $filters = [])
	{
		$query = Usuario::query();
		$query->select(
			'usuario.codigo_usuario', DB::raw('lower(usuario.correo_electronico) as correo_electronico'), 'telefono_usuario', 'direccion_usuario', 'movil_usuario', 'usuario.nickname_usuario', 'persona.genero_persona', 'usuario.identificacion_usuario', 'aceptacion_terminos.fecha_aceptacion_terminos',
			'usuario.fecha_nacimiento_usuario', DB::raw('initcap(nombre_ciudad) as nombre_ciudad'), 'usuario_institucion.promedio_evaluacion_mentor', DB::raw('initcap(distrito_ciudad) nombre_departamento'),
			DB::raw("initcap( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) as nombre_usuario"),
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as ciudad"),
			(( isset($filters['role']) && $filters['role']) ? DB::raw('COALESCE(mentor.codigo_estado, 0) AS codigo_estado') : DB::raw('0 AS codigo_estado'))
		);

		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		//  User institution
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
		});

		$query->join('seguridad.aceptacion_terminos', 'aceptacion_terminos.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');
		$query->join('param.version_terminos', 'version_terminos.codigo_version_terminos', '=', 'aceptacion_terminos.codigo_version_terminos');
		$query->join('param.terminos_condiciones', 'terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones');

		if( isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario']) ) {
      		$query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($filters['nombre_usuario'])."%'");
		}

		if( isset($filters['correo_electronico']) && !empty($filters['correo_electronico']) ) {
			$query->whereRaw("usuario.correo_electronico ilike '%".trim($filters['correo_electronico'])."%'");
		}

		if( isset($filters['nombre_ciudad']) && !empty($filters['nombre_ciudad']) ) {
			$query->where("usuario.codigo_ciudad", '=', $filters['nombre_ciudad']);
		}

		if( isset($filters['nickname_usuario']) && !empty($filters['nickname_usuario']) ) {
			$query->whereRaw("usuario.nickname_usuario ilike '%".trim($filters['nickname_usuario'])."%'");
		}

		if( isset($filters['identificacion_usuario']) && !empty($filters['identificacion_usuario']) ) {
			$query->where("usuario.identificacion_usuario", '=', $filters['identificacion_usuario']);
		}

		if (isset($filters['identificacion_usuario']) && !empty($filters['identificacion_usuario'])) {
      		$query->where("usuario.identificacion_usuario", "=", $filters['identificacion_usuario']);
    }

		// Filter Role
		if( isset($filters['role']) && !empty($filters['role']) ) {
			$query->join('seguridad.rol_usuario as mentor', function($join) use($filters) {
				$join->on('mentor.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
					->on('mentor.user_id', '=', 'usuario_institucion.codigo_usuario')
					->where('mentor.role_id', '=', is_numeric($filters['role']) ? $filters['role'] : Rol::getRol($filters['role']) )
          ->where('mentor.codigo_estado', '!=', Estado::getState('inactive'));
	        });

			if( isset($filters['codigo_estado']) && !empty($filters['codigo_estado']) ) {
				$query->where('mentor.codigo_estado', '=', $filters['codigo_estado']);
			}

			$query->orderBy(DB::raw("(CASE mentor.codigo_estado WHEN ".Estado::getState('review')." THEN mentor.codigo_estado END)"));
		}



    	$query->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('terms'));
		$query->whereRaw('aceptacion_terminos.fecha_aceptacion_terminos is not null');
		$query->where('usuario.tipo_usuario', '=',  self::typeUser('person'));

		// Filter Order
		$order = null;
		$columns = null;
		$dir = 'asc';
		if( isset($filters['order']) && !empty($filters['order']) && isset($filters['columns']) && !empty($filters['columns']) ) {
			$order = $filters['order'];
			$columns = $filters['columns'];
			$dir = (isset($order[0]['dir']) && !empty($order[0]['dir'])) ? $order[0]['dir'] : 'asc';
			if(isset($order[0]['column']) && !empty($order[0]['column']) && is_numeric($order[0]['column']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data']) ){
				$query->orderBy($columns[$order[0]['column']]['data'], $dir);
			}else {
				$query->orderBy('usuario.nombre_usuario', 'asc');
			}
		}else{
			$query->orderBy('usuario.nombre_usuario', 'asc');
		}

		return $query;
	}

	public static function getMentors(array $filters = [])
    {
        $query = Usuario::query();
        $query->select(
            'usuario.codigo_usuario', DB::raw('lower(usuario.correo_electronico) as correo_electronico'), 'telefono_usuario', 'direccion_usuario', 'movil_usuario', 'usuario.nickname_usuario', 'persona.genero_persona', 'usuario.identificacion_usuario', 'aceptacion_terminos.fecha_aceptacion_terminos',
            'usuario.fecha_nacimiento_usuario', DB::raw('initcap(nombre_ciudad) as nombre_ciudad'), 'usuario_institucion.promedio_evaluacion_mentor', DB::raw('initcap(distrito_ciudad) nombre_departamento'),
            DB::raw("initcap( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) as nombre_usuario"),
            DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as ciudad"),
            (( isset($filters['role']) && $filters['role']) ? DB::raw('COALESCE(mentor.codigo_estado, 0) AS codigo_estado') : DB::raw('0 AS codigo_estado'))
        );

        $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
        $query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
        $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

        //  User institution
        $query->join('core.usuario_institucion', function($join) {
            $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
        });

        $query->join('seguridad.aceptacion_terminos', 'aceptacion_terminos.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');
        $query->join('param.version_terminos', 'version_terminos.codigo_version_terminos', '=', 'aceptacion_terminos.codigo_version_terminos');
        $query->join('param.terminos_condiciones', function($join) {
            $join->on('terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones')
                ->where('terminos_condiciones.codigo_estado', '=', Estado::getState('active'))
                ->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('mentoring'));
        });

        if( isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario']) ) {
            $query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($filters['nombre_usuario'])."%'");
        }

        if( isset($filters['correo_electronico']) && !empty($filters['correo_electronico']) ) {
            $query->whereRaw("usuario.correo_electronico ilike '%".trim($filters['correo_electronico'])."%'");
        }

        if( isset($filters['nombre_ciudad']) && !empty($filters['nombre_ciudad']) ) {
            $query->where("usuario.codigo_ciudad", '=', $filters['nombre_ciudad']);
        }

        if( isset($filters['nickname_usuario']) && !empty($filters['nickname_usuario']) ) {
            $query->whereRaw("usuario.nickname_usuario ilike '%".trim($filters['nickname_usuario'])."%'");
		}

		if ( isset($filters['cedula_usuario']) && !empty($filters['cedula_usuario']) ) {
			$query->where("usuario.identificacion_usuario", '=', $filters['cedula_usuario']);
		}

        if (isset($filters['identificacion_usuario']) && !empty($filters['identificacion_usuario'])) {
            $query->where("usuario.identificacion_usuario", "=", $filters['identificacion_usuario']);
    }

        // Filter Role
        if( isset($filters['role']) && !empty($filters['role']) ) {
            $query->join('seguridad.rol_usuario as mentor', function($join) use($filters) {
                $join->on('mentor.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
                    ->on('mentor.user_id', '=', 'usuario_institucion.codigo_usuario')
                    ->where('mentor.role_id', '=', is_numeric($filters['role']) ? $filters['role'] : Rol::getRol($filters['role']) );
            });

            if( isset($filters['codigo_estado']) && !empty($filters['codigo_estado']) ) {
                $query->where('mentor.codigo_estado', '=', $filters['codigo_estado']);
            }

            $query->orderBy(DB::raw("(CASE mentor.codigo_estado WHEN ".Estado::getState('review')." THEN mentor.codigo_estado END)"));
        }

        $query->whereRaw('aceptacion_terminos.fecha_aceptacion_terminos is not null');
        $query->where('usuario.tipo_usuario', '=',  self::typeUser('person'));

        // Filter Order
        $order = null;
        $columns = null;
        $dir = 'asc';
        if( isset($filters['order']) && !empty($filters['order']) && isset($filters['columns']) && !empty($filters['columns']) ) {
            $order = $filters['order'];
            $columns = $filters['columns'];
            $dir = (isset($order[0]['dir']) && !empty($order[0]['dir'])) ? $order[0]['dir'] : 'asc';
            if(isset($order[0]['column']) && !empty($order[0]['column']) && is_numeric($order[0]['column']) && isset($columns[$order[0]['column']]['data']) && !empty($columns[$order[0]['column']]['data']) ){
                $query->orderBy($columns[$order[0]['column']]['data'], $dir);
            }else {
                $query->orderBy('usuario.nombre_usuario', 'asc');
            }
        }else{
            $query->orderBy('usuario.nombre_usuario', 'asc');
        }

        return $query;
    }

	public static function userEntityAll(array $filters = []) {
	  $query = UsuarioInstitucion::query();
	  $query->select('usuario.nombre_usuario', 'usuario.nickname_usuario', 'ciudad.distrito_ciudad as ciudad', 'entidad.codigo_usuario');
	  $query->join('param.institucion', 'institucion.codigo_institucion', '=', 'usuario_institucion.codigo_institucion');
	  $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario');
	  $query->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
	  $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');

    if( isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario']) ) {
      $query->whereRaw("usuario.nombre_usuario ilike '%".trim($filters['nombre_usuario'])."%'" );
    }

    if( isset($filters['nickname_usuario']) && !empty($filters['nickname_usuario']) ) {
      $query->whereRaw("usuario.nickname_usuario ilike '%".trim($filters['nickname_usuario'])."%'" );
    }

    if( isset($filters['nombre_ciudad']) && !empty($filters['nombre_ciudad']) ) {
      $query->where("usuario.codigo_ciudad", '=', $filters['nombre_ciudad']);
    }

    if( isset($filters['identificacion_entidad']) && !empty($filters['identificacion_entidad']) ) {
      $query->where("usuario.codigo_usuario", '=', $filters['identificacion_entidad']);
    }

	  $query->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
	  $query->where('usuario.tipo_usuario', '=', self::typeUser('entity'));
    $query->orderBy('usuario.nombre_usuario', 'asc');

    return $query;
  }

  public static function userEntityForAnnouncement(array $filters = []) {
    $query = UsuarioInstitucion::query();
    $query->select('usuario.nombre_usuario', 'usuario.nickname_usuario', 'ciudad.distrito_ciudad as ciudad', 'entidad.codigo_usuario');
    $query->join('param.institucion', 'institucion.codigo_institucion', '=', 'usuario_institucion.codigo_institucion');
    $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario');
    $query->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
    $query->join('core.convocatoria', 'convocatoria.seg_codigo_usuario', '=', 'entidad.codigo_usuario');
    $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');

    if( isset($filters['codigo_convocatoria']) && !empty($filters['codigo_convocatoria']) ) {
      $query->where('convocatoria.codigo_convocatoria', '=', $filters['codigo_convocatoria']);
    }
    $query->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('usuario.tipo_usuario', '=', self::typeUser('entity'));
    $query->orderBy('usuario.nombre_usuario', 'asc');

    return $query;
  }

	public static function usersList(array $filters = [])
	{
		$query = Usuario::query();
		$query->select('identificacion_usuario', 'movil_usuario', 'usuario.codigo_usuario', DB::raw('lower(usuario.correo_electronico) as correo_electronico'),
			DB::raw("initcap( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) as nombre_usuario"),
			DB::raw('(CASE WHEN usuario_evento.codigo_usuario_evento IS NOT NULL THEN TRUE ELSE FALSE END) AS assist_to_event'),
			DB::raw('(CASE WHEN usuario_evento.codigo_usuario_institucion = usuario_institucion.codigo_usuario_institucion THEN true ELSE false END) AS invitation_event'),
			'usuario_evento.codigo_usuario_evento'
		);

		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->leftJoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftJoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		//  User institution
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
		});

		$query->leftJoin('core.usuario_evento', function($join) use($filters){
			$join->on('usuario_institucion.codigo_usuario_institucion', '=', 'usuario_evento.codigo_usuario_institucion')
				->where('usuario_evento.codigo_evento', '=', $filters['codigo_evento']);
		});

		// $query->join('seguridad.aceptacion_terminos', 'aceptacion_terminos.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');
		// $query->join('param.version_terminos', 'version_terminos.codigo_version_terminos', '=', 'aceptacion_terminos.codigo_version_terminos');
		// $query->join('param.terminos_condiciones', function ($join) {
		//   $join->on('terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones')
		//   ->where('terminos_condiciones.codigo_institucion', '=', session('institution')->codigo_institucion)
		//   ->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('terms'));
		// });

		if ( isset($filters['identificacion_usuario_search']) && !empty($filters['identificacion_usuario_search']) ) {
			$query->where('usuario.codigo_usuario', '=', $filters['identificacion_usuario_search']);
    	}

		if ( isset($filters['phase']) && !empty($filters['phase']) ) {
			$query->join('core.entidad', 'entidad.seg_codigo_usuario', '=', 'usuario.codigo_usuario');
			$query->join('core.registro_fase_usuario', function($join) {
				$join->on('registro_fase_usuario.codigo_usuario', '=', 'entidad.codigo_usuario')
					->where('registro_fase_usuario.codigo_estado', '=', Estado::getState('active'));
			});
			$query->join('core.fase', function($join) use($filters) {
				$join->on('fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase')
					->where('fase.codigo_fase', '=', $filters['phase']);
			});
			$query->join('core.convocatoria', 'convocatoria.codigo_convocatoria', '=', 'fase.codigo_convocatoria');
			$query->where('convocatoria.codigo_convocatoria', '=', $filters['codigo_convocatoria']);
		}

		if (isset($filters['codigo_usuario']) && !empty($filters['codigo_usuario'])) {
			$query->where('usuario.codigo_usuario', '=', $filters['codigo_usuario']);
    	}

		if( isset($filters['fecha_nacimiento_usuario']) && !empty($filters['fecha_nacimiento_usuario']) ) {
			$query->where("usuario.fecha_nacimiento_usuario", '=', $filters['fecha_nacimiento_usuario']);
		}

		if( isset($filters['codigo_nivel_educacion']) && !empty($filters['codigo_nivel_educacion']) ) {
			$query->where("persona.codigo_nivel_educacion", '=', $filters['codigo_nivel_educacion']);
		}

		if( isset($filters['codigo_lista_ocupacion']) && !empty($filters['codigo_lista_ocupacion']) ) {
			$query->where("persona.codigo_lista_ocupacion", '=', $filters['codigo_lista_ocupacion']);
		}

		if( isset($filters['codigo_ciudad']) && !empty($filters['codigo_ciudad']) ) {
			$query->where("usuario.codigo_ciudad", '=', $filters['codigo_ciudad']);
		}

		if( isset($filters['codigo_localidad_ciudad']) && !empty($filters['codigo_localidad_ciudad']) ) {
			$query->where("usuario.codigo_localidad_ciudad", '=', $filters['codigo_localidad_ciudad']);
		}

		if( isset($filters['codigo_barrio_localidad']) && !empty($filters['codigo_barrio_localidad']) ) {
			$query->where("usuario.codigo_barrio_localidad", '=', $filters['codigo_barrio_localidad']);
		}

		if( isset($filters['codigo_estrato']) && !empty($filters['codigo_estrato']) ) {
			$query->where("persona.codigo_estrato", '=', $filters['codigo_estrato']);
		}

		if( isset($filters['codigo_tipo_vivienda']) && !empty($filters['codigo_tipo_vivienda']) ) {
			$query->where("persona.codigo_tipo_vivienda", '=', $filters['codigo_tipo_vivienda']);
		}

		if( isset($filters['jefe_hogar']) && !empty($filters['jefe_hogar']) ) {
			$query->where("persona.jefe_hogar", '=', $filters['jefe_hogar']);
		}

		if( isset($filters['codigo_sector_trabajo']) && !empty($filters['codigo_sector_trabajo']) ) {
			$query->where("persona.codigo_sector_trabajo", '=', $filters['codigo_sector_trabajo']);
		}

		if( isset($filters['enterprise']) && !empty($filters['enterprise']) ) {
			if($filters['enterprise'] == 'true'){
                $query->whereIn('usuario.codigo_usuario', function($query)
                {
                    $query->select(DB::raw('distinct "usuario"."codigo_usuario"
                    from "core"."entidad"
                    inner join "seguridad"."usuario" on "entidad"."seg_codigo_usuario" = "usuario"."codigo_usuario"'));
                });
            }

            if($filters['enterprise'] == 'false'){
                $query->whereNotIn('usuario.codigo_usuario', function($query)
                {
                    $query->select(DB::raw('distinct "usuario"."codigo_usuario"
                    from "core"."entidad"
                    inner join "seguridad"."usuario" on "entidad"."seg_codigo_usuario" = "usuario"."codigo_usuario"'));
                });
            }
		}

		if( isset($filters['codigo_autodenominacion']) && !empty($filters['codigo_autodenominacion']) ) {
			$query->where("persona.codigo_autodenominacion", '=', $filters['codigo_autodenominacion']);
		}

		if( isset($filters['codigo_grupo_poblacional']) && !empty($filters['codigo_grupo_poblacional']) ) {
			$query->where("persona.codigo_grupo_poblacional", '=', $filters['codigo_grupo_poblacional']);
		}

		if( isset($filters['codigo_grupo_comunitario']) && !empty($filters['codigo_grupo_comunitario']) ) {
			$query->where("persona.codigo_grupo_comunitario", '=', $filters['codigo_grupo_comunitario']);
		}

		if( isset($filters['codigo_grupo_social']) && !empty($filters['codigo_grupo_social']) ) {
			$query->where("persona.codigo_grupo_social", '=', $filters['codigo_grupo_social']);
		}

		if( isset($filters['disability']) && !empty($filters['disability']) ) {
			$query->where("persona.discapacidad", '=', $filters['disability']);
		}

		if( isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario']) ) {
			$query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($filters['nombre_usuario'])."%'");
		}

		// $query->where('usuario.tipo_usuario', '=',  self::typeUser('person'));
		$query->orderBy('usuario.nombre_usuario', 'asc');
		return $query;
	}

	/**
	 * Get the institutions user.
	 */
	public function institutions()
	{
		return $this->hasMany('App\Models\Core\UsuarioInstitucion', 'codigo_usuario', 'codigo_usuario');
	}

	/**
	 * Many-to-Many relations with Role.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function roles()
	{
		return $this->belongsToMany(config('entrust.role'), config('entrust.role_user_table'), 'user_id', 'role_id')
			->where('codigo_institucion', '=', session('institution')->codigo_institucion);
	}

	//Big block of caching functionality.
	public function cachedRoles()
	{
		$userPrimaryKey = $this->primaryKey;
		$cacheKey = "entrust_roles_for_user_{$this->$userPrimaryKey}_".session('institution')->codigo_institucion;
		return Cache::tags(Config::get('entrust.role_user_table'))->remember($cacheKey, Config::get('cache.ttl'), function () {
			return $this->roles()->get();
		});
	}

	/**
	 * Checks role(s) and permission(s).
	 *
	 * @param string|array $roles       Array of roles or comma separated string
	 * @param string|array $permissions Array of permissions or comma separated string.
	 * @param array        $options     validate_all (true|false) or return_type (boolean|array|both)
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return array|bool
	 */
	public function ability($roles, $permissions, $options = [])
	{
		// Convert string to array if that's what is passed in.
		if (!is_array($roles)) {
			$roles = explode(',', $roles);
		}
		if (!is_array($permissions)) {
			$permissions = explode(',', $permissions);
		}

		// Set up default values and validate options.
		if (!isset($options['validate_all'])) {
			$options['validate_all'] = false;
		} else {
			if ($options['validate_all'] !== true && $options['validate_all'] !== false) {
				throw new InvalidArgumentException();
			}
		}
		if (!isset($options['return_type'])) {
			$options['return_type'] = 'boolean';
		} else {
			if ($options['return_type'] != 'boolean' &&
				$options['return_type'] != 'array' &&
				$options['return_type'] != 'both') {
				throw new InvalidArgumentException();
			}
		}

		// Loop through roles and permissions and check each.
		$checkedRoles = [];
		$checkedPermissions = [];
		foreach ($roles as $role) {
			$checkedRoles[$role] = $this->hasRole($role);
		}
		foreach ($permissions as $permission) {
			$checkedPermissions[$permission] = $this->can($permission, isset($options['module']) ? $options['module'] : null);
		}

		// If validate all and there is a false in either
		// Check that if validate all, then there should not be any false.
		// Check that if not validate all, there must be at least one true.
		if(($options['validate_all'] && !(in_array(false,$checkedRoles) || in_array(false,$checkedPermissions))) ||
			(!$options['validate_all'] && (in_array(true,$checkedRoles) || in_array(true,$checkedPermissions)))) {
			$validateAll = true;
		} else {
			$validateAll = false;
		}

		// Return based on option
		if ($options['return_type'] == 'boolean') {
			return $validateAll;
		} elseif ($options['return_type'] == 'array') {
			return ['roles' => $checkedRoles, 'permissions' => $checkedPermissions];
		} else {
			return [$validateAll, ['roles' => $checkedRoles, 'permissions' => $checkedPermissions]];
		}
	}

	/**
	 * Check if user has a permission by its name.
	 *
	 * @param string|array $permission Permission string or array of permissions.
	 * @param bool         $requireAll All permissions in the array are required.
	 *
	 * @return bool
	 */
	public function can($permission, $module = null, $requireAll = false)
	{
		if (is_array($permission)) {
			foreach ($permission as $permName) {
				$hasPerm = $this->can($permName, $module);

				if ($hasPerm && !$requireAll) {
					return true;
				} elseif (!$hasPerm && $requireAll) {
					return false;
				}
			}

			// If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
			// If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
			// Return the value of $requireAll;
			return $requireAll;
		} else {
			foreach ($this->cachedRoles() as $role) {
				// Validate against the Permission table
				foreach ($role->cachedPermissions($module) as $perm) {
					if (str_is( $permission, $perm->name) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Alias to eloquent many-to-many relation's attach() method.
	 *
	 * @param mixed $role
	 */
	public function attachRole($role)
	{
		if(is_object($role)) {
			$role = $role->getKey();
		}

		if(is_array($role)) {
			$role = $role['id'];
		}

		$this->roles()->wherePivot('codigo_institucion',session('institution')->codigo_institucion)
		->attach($role, ['codigo_institucion' => session('institution')->codigo_institucion]);
	}

  /**
   * Alias to eloquent many-to-many relation's attach() method.
   * @param mixed $speciality
   */
  public function attachSpeciality($speciality)
  {
    if(is_object($speciality)) {
      $speciality = $speciality->getKey();
    }

    if(is_array($speciality)) {
      $speciality = $speciality['id'];
    }

    $this->specialties()->attach($speciality, ['codigo_estado' => Estado::getState('active')]);
  }

	/**
	 * Alias to eloquent many-to-many relation's detach() method.
	 *
	 * @param mixed $role
	 */
	public function detachRole($role)
	{
		if (is_object($role)) {
			$role = $role->getKey();
		}

		if (is_array($role)) {
			$role = $role['id'];
		}

		$this->roles()->wherePivot('codigo_institucion',session('institution')->codigo_institucion)
		->detach($role, ['codigo_institucion' => session('institution')->codigo_institucion]);
	}

  /**
   * Alias to eloquent many-to-many relation's detach() method.
   *
   * @param mixed $speciality
   */
  public function detachSpeciality($speciality)
  {
    if (is_object($speciality)) {
      $speciality = $speciality->getKey();
    }

    if (is_array($speciality)) {
      $speciality = $speciality['id'];
    }

    $this->specialties()->detach($speciality, ['codigo_estado' => Estado::getState('active')]);
  }

	public static function getMentor($codigo_usuario)
   	{
    $query = Usuario::query();
		$query->select('usuario.codigo_usuario', 'usuario.imagen_usuario', 'usuario.correo_electronico',
      'usuario.biografia_usuario' , 'usuario.experiencia_usuario', 'usuario.fecha_nacimiento_usuario',
      'usuario.pagina_web_usuario', 'usuario.profesion_usuario', 'usuario.codigo_ciudad', 'usuario.codigo_concepto_institucion',
      'usuario.cargo_entidad', 'usuario.twitter_usuario', 'usuario.facebook_usuario', 'usuario.linkedin_usuario',
      'usuario.tipo_usuario', 'codigo_persona', 'promedio_evaluacion_mentor',
      DB::raw("initcap( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) as nombre_usuario"),
      'genero_persona', 'estado_civil_persona', 'codigo_nivel_educacion',
      DB::raw('trim(lower(nickname_usuario)) as nickname_usuario'), 'nombre_ciudad', 'nombre_pais',
      'usuario_institucion.codigo_usuario_institucion', 'forma_ayuda_mentoria','nombre_especialidad',
      'persona.codigo_especialidad', 'persona.codigo_profesion_persona', 'rol_usuario_institucion',
      DB::raw("(SELECT CASE WHEN aceptacion_terminos.fecha_aceptacion_terminos IS NULL THEN FALSE ELSE TRUE END  FROM seguridad.aceptacion_terminos
  				JOIN param.version_terminos ON aceptacion_terminos.codigo_version_terminos = version_terminos.codigo_version_terminos
  				JOIN param.terminos_condiciones ON version_terminos.codigo_terminos_condiciones = terminos_condiciones.codigo_terminos_condiciones
  				JOIN core.usuario_institucion ON usuario_institucion.codigo_usuario = usuario.codigo_usuario
  				AND usuario_institucion.codigo_institucion = ".session('institution')->codigo_institucion."
  				WHERE terminos_condiciones.tipo_terminos ='".TerminosCondiciones::getTypeTerms('mentoring')."'
  				AND usuario_institucion.codigo_usuario_institucion = aceptacion_terminos.codigo_usuario_institucion) AS terminos_aceptados"),
      DB::raw('(CASE WHEN mentor.role_id IS NOT NULL THEN true ELSE false END) AS mentor')
		);

		// City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->leftjoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->leftjoin('core.especialidad', 'persona.codigo_especialidad', '=', 'especialidad.codigo_especialidad');

		// User institution
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		// Mentor
		$query->leftjoin('seguridad.rol_usuario as mentor', function($join) {
			$join->on('mentor.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
				->on('mentor.user_id', '=', 'usuario_institucion.codigo_usuario')
				->where('mentor.role_id', '=', Rol::getRol('mentor'));
        });

		$query->where('usuario.codigo_usuario', '=', $codigo_usuario);

		return $query->first();
	}

	/**
	* User data validate from nickname.
	*/
	public static function validateNinckname($nickname, $contador = 0)
	{
		if(isset($nickname) && !empty($nickname)){

			$newnickname = $contador > 0 ? (strlen($nickname) >= 16) ? sprintf("%s%d",substr($nickname,0,-strlen($contador)),$contador) : sprintf("%s%d",$nickname, $contador) : $nickname;

			$query = Usuario::query();
			$query->select('usuario.*');
			$query->whereRaw("trim(lower(nickname_usuario)) ilike trim(lower('$newnickname'))");
			$user = $query->first();

			$contador ++;
			if($user instanceof Usuario){
				$newnickname = Usuario::validateNinckname($nickname, $contador);
			}
			return $newnickname;
		}
		return str_random(16);
	}

	/**
	 * Build User.
	*/
   	public static function buildUser($user)
   	{
		$object = new \stdClass();
		$object->codigo_persona = $user->codigo_persona;
		$object->codigo_usuario = $user->codigo_usuario;
		$object->nombre_usuario = $user->nombre_usuario;
		$object->nickname_usuario = $user->nickname_usuario;
		$object->imagen_usuario = $user->imagen_usuario;
		$object->correo_electronico = $user->correo_electronico;
		$object->biografia_usuario = $user->biografia_usuario;
		$object->experiencia_usuario = $user->experiencia_usuario;
		$object->fecha_nacimiento_usuario = $user->fecha_nacimiento_usuario;
		$object->forma_ayuda_mentoria = $user->forma_ayuda_mentoria;
		$object->pagina_web_usuario = $user->pagina_web_usuario;
		$object->profesion_usuario = $user->profesion_usuario;
		$object->codigo_ciudad = $user->codigo_ciudad;
		$object->nombre_ciudad = $user->nombre_ciudad;
		$object->nombre_pais = $user->nombre_pais;
		$object->codigo_usuario_institucion = $user->codigo_usuario_institucion;
		$object->rol_usuario_institucion = $user->rol_usuario_institucion;
		$object->codigo_concepto_institucion = $user->codigo_concepto_institucion;
		$object->cargo_entidad = $user->cargo_entidad;
		$object->codigo_nivel_educacion = $user->codigo_nivel_educacion;
		$object->codigo_profesion_persona = $user->codigo_profesion_persona;
		$object->promedio_evaluacion_mentor = $user->promedio_evaluacion_mentor;

		$object->twitter_usuario = $user->twitter_usuario;
		$object->facebook_usuario = $user->facebook_usuario;
		$object->linkedin_usuario = $user->linkedin_usuario;

		$object->mentor = $user->mentor;
		$object->terminos_aceptados = $user->terminos_aceptados;
		$object->owner = false;

		$object->stage_mentoring = [];
		$object->economic_sector_person = [];

		if($user->mentor) {
			// specialties
			$object->specialties = $user->specialties()->get();
			$object->economic_sector_person = $user->persona->economicsectors;
      $object->especialidades_usuario = Especialidad::getSpecialitiesByUser($user->codigo_usuario);
      $object->sectores_economicos_usuario = SectorEconomico::getSectorsByPerson($user->codigo_persona);
      $object->codigo_especialidad_destaca = $user->persona->codigo_especialidad;
      // stages mentoring
			$object->stage_mentoring = $user->etapamentoria()->get()->pluck('codigo_etapa_proyecto')->all();

			// attachment
			$hoja_vida = $user->adjuntos()->where('codigo_documento', '=', Documento::getTypeDocument('curriculum'))->pluck('direccion_adjunto');
			if(count($hoja_vida)>0){
				$hoja_vida = $hoja_vida[0];
			}
			else{
				$hoja_vida = "";
			}
			$object->attached_curriculum = $hoja_vida;
 		}
		$object->tipo_usuario = $user->tipo_usuario;

		return $object;
   	}

	public function persona()
	{
		return $this->hasOne('App\Models\Core\Persona', 'codigo_usuario', 'codigo_usuario');
	}

	public function specialties()
    {
       return $this->belongsToMany('App\Models\Core\Especialidad', 'core.especialidad_usuario', 'codigo_usuario', 'codigo_especialidad')
	   		->select(DB::raw('(CASE especialidad.codigo_especialidad WHEN '.Especialidad::getSpecialty("other").' THEN descripcion_especialidad ELSE nombre_especialidad END) as nombre_especialidad'))
			->join('core.especialidad_institucion', function($join){
				$join->on('especialidad.codigo_especialidad', '=', 'especialidad_institucion.codigo_especialidad')
					->where('especialidad_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
			})
			->where('especialidad_usuario.codigo_estado', '=', Estado::getState('active'));
    }


  public function getAllowedUsers()
  {
    // by default, the creator of the resource is allowed
    $allowed = [$this->attributes['codigo_usuario']];

    // entity session
    if(Auth::check() && $this->attributes['tipo_usuario'] == $this->typeUser('entity')){
      $allowed[] = $this->entidad->seg_codigo_usuario;
    }
    return $allowed;
  }
	public function etapamentoria()
    {
        return $this->belongsToMany('App\Models\Core\EtapaProyecto','core.etapa_mentoria','codigo_usuario', 'codigo_etapa_proyecto');
    }

	public function adjuntos()
    {
       return $this->belongsToMany('App\Models\Core\Adjunto', 'core.adjunto_usuario', 'codigo_usuario', 'codigo_adjunto');
	}

	 /**
   * Sync Algolia index
   */
  public function syncIndex(){

	\Artisan::queue('algolia:sync');


    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      $errormsg = "App\Models\Core\Convocatoria@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
      Log::error(['error'=> $errormsg]);
      throw new \Exception($errormsg);
    }

    /*activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'convocatoria' => $this->nombre_convocatoria
        ]
      ])
      ->log('activitylog.call.sync');*/
  }

   /**
   * Desync Algolia index
   */
  public function desyncIndex(){

	\Artisan::queue('algolia:desyncitem', [
		'type' => 'mentor',
		'id' => $this->codigo_usuario
	]);

	$this->sync_algolia = 1;
	$this->save();

	\Artisan::queue('algolia:sync');


    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      $errormsg = "App\Models\Core\Convocatoria@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
      Log::error(['error'=> $errormsg]);
      throw new \Exception($errormsg);
    }

    /*activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'convocatoria' => $this->nombre_convocatoria
        ]
      ])
      ->log('activitylog.call.desync');*/
  }
  	/**
	 * Obtiene datos del usuario (persona) mediante codigo_usuario.
	 *
	 * @access public
	 * @static function
	 * @param int $id_user
	 * @return object $data
	 */
	public static function getDataUserById(int $id_user)
	{
		$query = Usuario::select('usuario1.*', 'persona.*' , 'entidad.*')
			->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario')
			->join('core.persona as persona', 'persona.codigo_usuario', '=', 'entidad.seg_codigo_usuario')
			->join('seguridad.usuario as usuario1', 'usuario1.codigo_usuario', '=', 'persona.codigo_usuario')
			->where('usuario.codigo_usuario', $id_user);
		return $data = $query->first();
	}
	/**
	 * Obtiene datos del usuario (persona) mediante correo_electronico.
	 *
	 * @access public
	 * @static function
	 * @param string $email
	 * @return object $data
	 */
	public static function getUserByEmail(string $email)
	{
		$query = Usuario::select('usuario1.*', 'persona.*' , 'entidad.*')
			->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario')
			->join('core.persona as persona', 'persona.codigo_usuario', '=', 'entidad.seg_codigo_usuario')
			->join('seguridad.usuario as usuario1', 'usuario1.codigo_usuario', '=', 'persona.codigo_usuario')
			->where('usuario1.correo_electronico', $email);
		return $data = $query->first();

	}
	/**
	 * Obtiene datos del usuario (entidad) mediante correo_electronico.
	 *
	 * @access public
	 * @static function
	 * @param int $id_user
	 * @return object $data
	 */
	public static function getUserEntity(int $id_user)
	{
		$query = Usuario::query()
			->where('usuario.codigo_usuario', $id_user);

		return $data = $query->first();
	}

    public function asignacionEvaluadores()
    {
        return $this->hasMany('App\Models\Core\AsignacionEvaluadores', 'codigo_usuario');
    }
}
