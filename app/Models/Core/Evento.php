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

class Evento extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.evento';

   	protected $primaryKey = 'codigo_evento';

   	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'nombre_evento',
		'descripcion_evento',
		'fecha_evento',
		'hora_inicio_evento',
		'hora_fin_evento',
    	'codigo_calendario',
		'fecha_fin_evento',
		'codigo_ciudad',
		'codigo_calendario_usuario',
		'codigo_privacidad_contenido',
    	'url_evento_organizador',
		'lugar_evento',
		'resumen_evento',
		'imagen_evento',
		'sync_algolia',
		'facilitator',
	];

	protected $nullable = ['hora_inicio_evento', 'hora_fin_evento', 'fecha_fin_evento', 'codigo_ciudad'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_evento'	=> 'required|max:100',
			'descripcion_evento'	=> 'required',
			'resumen_evento'	=> 'required',
			'fecha_evento'		=> 'required',
			'codigo_calendario'	=> 'required',
			'codigo_privacidad_contenido'	=> 'required',
			'codigo_tipo_evento_calendario'	=> 'required',
			//'facilitator' => 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getDatesCertificate($data) {
		$event = Evento::find($data['codigo_evento']);
		if (!$event instanceof Evento) {
		abort(403);
		}

		$query = Usuario::query();
		$query->select('usuario.nombre_usuario', 'usuario.identificacion_usuario',
						'usuario.correo_electronico','persona.primer_apellido_persona',
						'persona.segundo_apellido_persona', 'tipo_identificacion.nombre_tipo_identificacion');
		$query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.tipo_identificacion', 'tipo_identificacion.codigo_tipo_identificacion', '=', 'usuario.tipo_identificacion_usuario');
		$query->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		$query->where('usuario.codigo_usuario', '=', $data['codigo_usuario']);
		$user = $query->first();

		$data_certificate = [];

		$month = TuProyecto::dateFormat(date("Ymd"), "month");
		$monthName = explode(" ", $month);


		$data_certificate['title'] = session('institution')->nombre_institucion;
		$data_certificate['nombre_gerente'] = session('institution')->nombre_gerente;
		$data_certificate['firma_gerente'] = session('institution')->firma_gerente;
		$data_certificate['nombre_usuario_completo'] = $user->nombre_usuario.' '.$user->primer_apellido_persona.' '.$user->segundo_apellido_persona;
		$data_certificate['nombre_usuario'] = $user->nombre_usuario.' '.$user->primer_apellido_persona;
		$data_certificate['identificacion_usuario'] = $user->identificacion_usuario;
		$data_certificate['nombre_tipo_identificacion'] = $user->nombre_tipo_identificacion;
		$data_certificate['correo_electronico'] = $user->correo_electronico;
		$data_certificate['nombre_evento'] = $event->nombre_evento;
		$data_certificate['fecha_evento'] = $event->fecha_evento;
		$data_certificate['fecha_fin_evento'] = $event->fecha_fin_evento;
		$data_certificate['resumen_evento'] = $event->resumen_evento;
		$data_certificate['cantidad_horas'] = $event->cantidad_horas;
		$data_certificate['nombre_mes'] = $monthName[1];

		return $data_certificate;
  }

	public static function getEvents(array $filters = [])
	{

		
		$query = Evento::query();
		$query->select('evento.codigo_evento', 'nombre_evento', 'fecha_evento', 'evento_institucion.codigo_estado', 'evento.codigo_usuario');
		$query->join('core.evento_institucion', function ($join){
			$join->on('evento.codigo_evento', '=', 'evento_institucion.codigo_evento')
				->where('evento_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.tipo_evento_calendario', function($join){
			$join->on('evento_institucion.codigo_tipo_evento_calendario', '=', 'tipo_evento_calendario.codigo_tipo_evento_calendario')
				->where('tipo_evento_calendario.codigo_clase_tipo_evento', '=', ClaseTipoEvento::getClass('platform'));
		});

		if(isset($filters['nombre_evento']) && !empty($filters['nombre_evento'])){
			$query->whereRaw("nombre_evento  ilike '%".$filters['nombre_evento']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where('evento_institucion.codigo_estado', $filters['codigo_estado']);
		}

		if (session('institution')->contenido_usuario) {
			$query->where('evento.codigo_usuario', '=', Auth::user()->codigo_usuario);
		}

		$query->orderBy('evento.codigo_evento','desc');
		return $query->get();
	}

	public static function getEvent($event_code)
	{
		$query = Evento::query();
		$query->select(
			'evento.codigo_evento', 
			'evento.factura_evento',
			'evento.costo_factura_evento',
			'evento.url_meet_evento',
			'evento.others_urls_meeting',
			'nombre_evento',
			'facilitator',
			'descripcion_evento',
			'fecha_evento',
			'privacidad_evento',
			'evento.cantidad_horas',
			'sync_algolia',
			'pago_evento',
			'registro_interes_evento',
			'lugar_evento',
			'asistencia_evento',
			'hora_inicio_evento',
			'hora_fin_evento',
			'fecha_fin_evento',
			'evento.codigo_calendario',
			'url_evento',
			'codigo_privacidad_contenido',
			'resumen_evento',
			'evento.imagen_evento',
			'evento.codigo_ciudad',
			'evento_institucion.codigo_estado',
			'url_evento_organizador',
			'codigo_tipo_evento',
			'tipo_evento_calendario.codigo_tipo_evento_calendario',
			'seo.*'
		);
		$query->join('core.evento_institucion', function ($join){
			$join->on('evento.codigo_evento', '=', 'evento_institucion.codigo_evento')
				->where('evento_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->join('core.tipo_evento_calendario', function($join){
			$join->on('evento_institucion.codigo_tipo_evento_calendario', '=', 'tipo_evento_calendario.codigo_tipo_evento_calendario');
				//->where('tipo_evento_calendario.codigo_clase_tipo_evento', '=', ClaseTipoEvento::getClass('platform'));
		});

		$query->leftJoin('core.seo', function($join){
			$join->on('evento.codigo_evento', '=', 'seo.id_recurso')
				->where('seo.codigo_modulo', Modulo::getModule('calendar'));
		});

		$query->where('evento.codigo_evento', '=', $event_code);
		if (session('institution')->contenido_usuario) {
			$query->where('evento.codigo_usuario', '=', Auth::user()->codigo_usuario);
		}
		return $query->first();
	}

	public static function getEventsList(array $filters = [])
	{
		$query = Evento::query();
		$query->select('evento.codigo_evento as id', DB::raw('initcap(nombre_evento) as text'));
		$query->join('core.evento_institucion', function ($join){
			$join->on('evento.codigo_evento', '=', 'evento_institucion.codigo_evento')
				->where('evento_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		// Filters
		if(isset($filters['q']) && !empty($filters['q'])) {
 			$query->whereRaw("nombre_evento ilike '%".$filters['q']."%'");
 		}

		// Order
		$query->orderBy('evento.codigo_evento','desc');
		return $query->get();
	}

	public static function buidEvent($event)
	{
		$object = new \stdClass();
		$object->codigo_evento = $event->codigo_evento;
		$object->nombre_evento = $event->nombre_evento;
		$object->descripcion_evento = $event->descripcion_evento;
		$object->fecha_evento = $event->fecha_evento;
		$object->fecha_fin_evento = $event->fecha_fin_evento;
		$object->privacidad_evento = $event->privacidad_evento;
		$object->pago_evento = $event->pago_evento;
		$object->registro_interes_evento = $event->registro_interes_evento;
		$object->lugar_evento = $event->lugar_evento;
		$object->asistencia_evento = $event->asistencia_evento;
		$object->hora_inicio_evento = $event->hora_inicio_evento;
		$object->hora_fin_evento = $event->hora_fin_evento;
		$object->codigo_calendario = $event->codigo_calendario;
		$object->codigo_tipo_evento = $event->codigo_tipo_evento;
		$object->url_evento = $event->url_evento;
		$object->url_evento_organizador = $event->url_evento_organizador;
		$object->codigo_ciudad = $event->codigo_ciudad;
		$object->cantidad_horas = $event->cantidad_horas;
		$object->resumen_evento = $event->resumen_evento;
		$object->imagen_evento = $event->imagen_evento;
		$object->codigo_estado = $event->codigo_estado;
		$object->codigo_privacidad_contenido = $event->codigo_privacidad_contenido;
		$object->codigo_tipo_evento_calendario = $event->codigo_tipo_evento_calendario;
		$object->meet = $event->url_meet_evento;
		$object->others_urls_meeting = $event->others_urls_meeting;
		$object->factura = $event->factura_evento;
		$object->costo_factura = $event->costo_factura_evento;
		$object->facilitator = $event->facilitator;

		// SEO
		$object->nombre_facebook = $event->nombre_facebook;
		$object->nombre_twitter = $event->nombre_twitter;
		$object->descripcion_facebook = $event->descripcion_facebook;
		$object->descripcion_twitter = $event->descripcion_twitter;
		$object->imagen_facebook = $event->imagen_facebook;
		$object->imagen_twitter = $event->imagen_twitter;
		$object->url_canonica = $event->url_canonica;
		$object->enlaces_motores_busqueda = $event->enlaces_motores_busqueda;
		$object->indexar_motores = $event->indexar_motores;
		$object->tags = $event->tags()->get()->implode('texto_tag', ',');
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
