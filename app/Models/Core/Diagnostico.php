<?php

namespace App\Models\Core;

use App\Models\BaseModel;

use App\Models\Seguridad\Usuario;
use DateTime;
use DB, Validator, Auth;

use	App\Models\Aprende\Mentoria,
	App\Models\Core\Estado,
	App\Models\Core\TemaPrincipalEvento,
	App\Models\Seguridad\Modulo,
    App\Models\Aprende\MentoriaInstitucion,
	App\Models\Core\ClaseTipoEvento,
	App\Models\Aprende\ObjetivoMentoria;
use TuProyecto;

class Diagnostico extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.diagnostico';

   	protected $primaryKey = 'codigo_diagnostico';

   	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'nombre_diagnostico',
		'descripcion_diagnostico',
		'codigo_institucion',
		'codigo_encuesta',

	];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_diagnostico'	=> 'required|max:512',
			'descripcion_diagnostico'	=> 'required',
			'codigo_encuesta'		=> 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getDiagnostics(array $filters = [])
	{


		$query = Diagnostico::query();
		$query->select('diagnostico.codigo_diagnostico', 'nombre_diagnostico', 'descripcion_diagnostico', 'diagnostico.codigo_institucion', 'diagnostico.codigo_encuesta', 'diagnostico.estado');
		$query->join('param.institucion', function ($join){
			$join->on('diagnostico.codigo_institucion', '=', 'institucion.codigo_institucion')
				->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.encuesta', function($join){
			$join->on('diagnostico.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		});

		// if(isset($filters['nombre_evento']) && !empty($filters['nombre_evento'])){
		// 	$query->whereRaw("nombre_evento  ilike '%".$filters['nombre_evento']."%'");
		// }

		// if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
		// 	$query->where('evento_institucion.codigo_estado', $filters['codigo_estado']);
		// }

		// if (session('institution')->contenido_usuario) {
		// 	$query->where('evento.codigo_usuario', '=', Auth::user()->codigo_usuario);
		// }

		$query->orderBy('diagnostico.codigo_diagnostico','desc');
		return $query->get();
	}

	public static function getDiagnostic($diagnostic_code)
	{
		$query = Diagnostico::query();
		$query->select(
			'diagnostico.codigo_diagnostico',
			'diagnostico.nombre_diagnostico',
			'diagnostico.descripcion_diagnostico',
			'diagnostico.codigo_institucion',
			'diagnostico.codigo_encuesta',
      'diagnostico.estado'
		);
		$query->join('param.institucion', function ($join){
			$join->on('diagnostico.codigo_institucion', '=', 'institucion.codigo_institucion')
				->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.encuesta', function($join){
			$join->on('diagnostico.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		});

    $query->where('diagnostico.codigo_diagnostico', '=', $diagnostic_code);

		return $query->first();
	}

	public static function getDiagnosticsList(array $filters = [])
	{
		$query = Diagnostico::query();
		$query->select('diagnostico.codigo_diagnostico as id', DB::raw('initcap(nombre_diagnostico) as text'));
		$query->join('param.institucion', function ($join){
			$join->on('diagnostico.codigo_institucion', '=', 'institucion.codigo_institucion')
				->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		// // Filters
		// if(isset($filters['q']) && !empty($filters['q'])) {
 		// 	$query->whereRaw("nombre_evento ilike '%".$filters['q']."%'");
 		// }

		// Order
		$query->orderBy('diagnostico.codigo_diagnostico','desc');
		return $query->get();
	}

	public static function buidDiagnostic($diagnostic)
	{
		$object = new \stdClass();
		$object->codigo_diagnostico = $diagnostic->codigo_diagnostico;
		$object->nombre_diagnostico = $diagnostic->nombre_diagnostico;
		$object->descripcion_diagnostico = $diagnostic->descripcion_diagnostico;
		$object->codigo_institucion = $diagnostic->codigo_institucion;
		$object->codigo_encuesta = $diagnostic->codigo_encuesta;
    $object->estado = $diagnostic->estado;

		return $object;
	}

	public static function getOrganizerPrincipal($event) {
    $query = Evento::query();
    $query->select( 'usuario.nombre_usuario', 'usuario.nickname_usuario', 'ciudad.distrito_ciudad as ciudad', 'evento.seg_codigo_usuario as codigo_usuario');
    $query->join('core.evento_institucion', 'evento_institucion.codigo_evento', '=', 'evento.codigo_evento');
    $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'evento.seg_codigo_usuario');
    $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');
    $query->where('evento_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('evento.codigo_evento', '=', $event);

    return $query;
  }

  public static function getResponsableEvent($event) {
    $query = Evento::query();
    $query->select( 'usuario.nombre_usuario', 'usuario.nickname_usuario', 'ciudad.distrito_ciudad as ciudad', 'evento.codigo_usuario as codigo_usuario');
    $query->join('core.evento_institucion', 'evento_institucion.codigo_evento', '=', 'evento.codigo_evento');
    $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'evento.codigo_usuario');
    $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');
    $query->where('evento_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('evento.codigo_evento', '=', $event);

    return $query;
  }

  public static function getAllOrganizers(array $filters = []) {
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

    $query->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('usuario.tipo_usuario', '=', Usuario::typeUser('entity'));
    $query->orderBy('usuario.nombre_usuario', 'asc');

    return $query;
  }

  /**
   * Get Type events
   */

	public static function getTypeEvent($calendar) {
		$query = TipoEvento::query();
		$query->select('tipo_evento_calendario.codigo_tipo_evento_calendario', 'nombre_tipo_evento');
		$query->join('core.tipo_evento_calendario', 'tipo_evento_calendario.codigo_tipo_evento', '=', 'tipo_evento.codigo_tipo_evento');
		$query->where('tipo_evento_calendario.codigo_calendario', $calendar);

		return $query->get();
	}

  /**
   * Sync Algolia index
   */
  public function syncIndex(){

	\Artisan::queue('algolia:syncitem', [
		'type' => 'event',
		'id' => $this->codigo_evento
	]);

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      $errormsg = "App\Models\Core\Evento@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
      Log::error(['error'=> $errormsg]);
      throw new \Exception($errormsg);
    }
    /*activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'evento' => $this->nombre_evento
        ]
      ])
      ->log('activitylog.event.sync');*/
  }

   /**
   * Desync Algolia index
   */
  public function desyncIndex(){

	\Artisan::queue('algolia:desyncitem', [
		'type' => 'event',
		'id' => $this->codigo_evento
	]);

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      $errormsg = "App\Models\Core\Evento@syncIndex: Error recuperando usuario ".\Auth::user()->codigo_usuario;
      Log::error(['error'=> $errormsg]);
      throw new \Exception($errormsg);
    }
    /*activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'evento' => $this->nombre_evento
        ]
      ])
      ->log('activitylog.event.desync');*/
  }

	public static function createMeet($data) {
		$userData = Usuario::find($data['user']);
		if (!$userData instanceof Usuario) {
			abort(404);
		}

		$eventData = Evento::find($data['event']);
		if (!$eventData instanceof Evento) {
			abort(404);
		}

		\Artisan::call('googleapi:makemeetconference', [
			'user' => $userData,
			'event' => $eventData,
			'institution' => session('institution')->codigo_institucion,
		]);
	}

	/**
	 * @param Event $evento
	 * @param int $codigo_usuario del emprendedor
	 * @param int $codigo_asesoria
	 * @param array $request contiene datos del formulario de creación
	 * 				de sesión masiva
	 *
	 * @return void
	 */
	public static function createMentoring(
		Evento $event,
		int $codigo_usuario,
		int $codigo_asesoria,
		array $request,
		int $codigo_sesion_masiva
	){

		DB::beginTransaction();
		try {

			$event->codigo_estado = Estado::getState('confirm');
			$event->save();

			$mentoring = new Mentoria;
			$mentoring->codigo_usuario 		 = $codigo_usuario; //este codigo_usuario es del emprendedor
			$mentoring->codigo_estado 		 = Estado::getState('pending');
			$mentoring->descripcion_mentoria = $request['descripcion_evento'];
			$mentoring->fecha_mentoria 		 = date('Y-m-d');
			$mentoring->hora_mentoria 		 = date('H:i:s');
			$mentoring->codigo_evento 		 = $event->codigo_evento;
			$mentoring->seg_codigo_usuario 	 = $event->codigo_usuario;
			$mentoring->codigo_asesoria 	 = $codigo_asesoria;
			$mentoring->codigo_sesion_masiva = $codigo_sesion_masiva;
			$mentoring->save();

			$mentoringInstitution = new MentoriaInstitucion;
			$mentoringInstitution->codigo_mentoria	  = $mentoring->codigo_mentoria;
			$mentoringInstitution->codigo_institucion = session('institution')->codigo_institucion;
			$mentoringInstitution->codigo_estado 	  = Estado::getState('active');
			$mentoringInstitution->save();

			$themeEvent = TemaPrincipalEvento::find($request['codigo_tema_principal_evento']);

			$omentoring = new ObjetivoMentoria;
			$omentoring->codigo_estado 		= Estado::getState('active');
			$omentoring->codigo_mentoria 	= $mentoring->codigo_mentoria;
			$omentoring->descripcion_objetivo_mentoria = $themeEvent->nombre_tema_principal_evento;
			$omentoring->save();
			// Commit Transaction
			DB::commit();
			\Log::info('La solicitud ha sido agendada');
			return;
		} catch (\Exception $e) {
			DB::rollback();
			\Log::error(sprintf('%s:%s', 'Evento createMentoring', $e->getMessage()));
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * @param Evento $evento
	 * @return void
	 */
	public static function confirmMentoring(Evento $event)
	{
		$mentoring = Mentoria::getMentoringEvent($event->codigo_evento);
		if($mentoring instanceof Mentoria) {
			$mentoring->codigo_estado = Estado::getState('active');
			$mentoring->save();

			$event->codigo_estado = Estado::getState('confirmed');
			$event->save();

			return;
		}
		throw new \Exception('Mentoring not found');
	}

	public function institutions ()
	{
		return $this->hasMany('App\Models\Core\EventoInstitucion', 'codigo_evento', 'codigo_evento');
	}

	public function tags()
    {
        return $this->belongsToMany('App\Models\Core\Tag', 'core.tag_evento', 'codigo_evento', 'codigo_tag');
    }

	public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
    }
}
