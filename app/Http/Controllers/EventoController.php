<?php

namespace App\Http\Controllers;

use App\Models\Core\CategoriaContenido;
use App\Models\Core\EtapaContenido;
use App\Models\Core\UsuarioEvento;
use Illuminate\Http\Request;
use Arturgrigio\GoogleCalendar\Event;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables, Log, DB, Queue, Auth, App, AWS, Carbon;
use App\Models\Seguridad\Usuario,
    App\Models\Core\Evento,
    App\Models\Core\EventoInstitucion,
    App\Models\Core\Estado,
    App\Models\Core\PrivacidadContenido,
    App\Models\Core\Calendario,
    App\Models\Core\TipoEvento,
    App\Models\Core\ClaseTipoEvento,
    App\Models\Core\Tag,
    App\Models\Core\Seo,
    App\Models\Seguridad\Modulo,
    App\Models\Core\NivelEducacion,
    App\Models\Core\ListaOcupacion,
    App\Models\Core\LocalidadCiudad,
    App\Models\Core\BarrioLocalidad,
    App\Models\Core\Estrato,
    App\Models\Core\TipoVivienda,
    App\Models\Core\SectorTrabajo,
    App\Models\Core\Autodenominacion,
    App\Models\Core\GrupoPoblacional,
    App\Models\Core\GrupoComunitario,
    App\Models\Core\GrupoSocial,
    App\Models\Core\RecordatorioEvento,
    App\Models\Param\Institucion;
use SendEmail;

class EventoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evento', ['except' => ['search']]);
        $this->middleware('ability:admin|super-admin|administrativo,create,evento', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evento', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,evento', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
          switch ($request->data) {
            case 'list':
              return response()->json(Evento::getEventsList( $request->all() ));
              break;
            default:
              return Datatables::of( Evento::getEvents( $request->all() ) )->make(true);
          }
		  }

      $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
      return view('event.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $stratum = Estrato::stratum();
      $zone = BarrioLocalidad::zone([]);
      $calendars = Calendario::getCalendars();
      $workSector = SectorTrabajo::workSector();
      $socialGroup = GrupoSocial::socialGroup();
      $housingType = TipoVivienda::housingType();
      $locations = LocalidadCiudad::locations([]);
      $levels = NivelEducacion::levelsEducation();
      $employment = ListaOcupacion::employmentList();
      $communityGroup = GrupoComunitario::communityGroup();
      $selfDomination = Autodenominacion::selfDomination();
      $populationGroup = GrupoPoblacional::populationGroup();
      $privacyContent = PrivacidadContenido::getContentPrivacy();
      $eventType = TipoEvento::getEventType(ClaseTipoEvento::getClass('platform'));

      return view('event.create-edit', compact('privacyContent', 'calendars', 'eventType', 'levels', 'employment', 'zone', 'stratum', 'locations', 'housingType', 'workSector', 'selfDomination', 'populationGroup', 'communityGroup', 'socialGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->ajax()){
          $data = $request->all();
          $event = new Evento;
          if ($event->isValid($data)) {
            DB::beginTransaction();
            try {
              $event->fill($data);
              $event->codigo_usuario = Auth::user()->codigo_usuario;
              $event->codigo_estado = Estado::getState('active');
              $event->url_evento = slugtp($request->nombre_evento, $event->getTable(), 'url_evento');
              $event->pago_evento = $request->pago_evento ? true : false;
              $event->registro_interes_evento = $request->participacion_evento ? true : false;
              $event->cantidad_horas = $request->cantidad_horas ? (int)$request->cantidad_horas : 0;
              $event->sync_algolia = 1;
              $event->save();

              $eventInstitucion = new EventoInstitucion;
              $eventInstitucion->codigo_evento = $event->codigo_evento;
              $eventInstitucion->codigo_institucion = session('institution')->codigo_institucion;
              $eventInstitucion->codigo_tipo_evento_calendario = $request->codigo_tipo_evento_calendario;
              $eventInstitucion->codigo_estado = Estado::getState('active');
              $eventInstitucion->save();

              if($event->codigo_privacidad_contenido != PrivacidadContenido::getPrivacy('public')){
                $event->sync_algolia = 3;
                $event->save();
                $event->desyncIndex();
              }

              //crear recordatorio
             if ($request->activar_recordatorio) {
                  $recordatorio = new RecordatorioEvento;
				  $recordatorio->codigo_evento = $event->codigo_evento;
                  $recordatorio->enviar = true;
				  $recordatorio->save();
              }

              if ($event instanceof Evento) {
                if ($request->has('inpMeet')) {
                  if ($request->inpMeet == '1') {
                    Evento::createMeet(['event' => $event->codigo_evento, 'user' => Auth::user()->codigo_usuario]);
                    // Queue::push('App\Models\Core\Evento@createMeet', ['event' => $event->codigo_evento, 'user' => Auth::user()->codigo_usuario]);
                  } elseif ($request->others_urls_meeting) {
                    Evento::where('codigo_evento', $event->codigo_evento)
                            ->update(['others_urls_meeting' => $request->others_urls_meeting]);
                  }
                }
              }

              // Commit Transaction
              DB::commit();
              $event->syncIndex();
              return response()->json(['success' => true, 'message' => 'El evento ha sido creado correctamente!!', 'codigo_evento' => $event->codigo_evento, 'redirect' => true ]);

            }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'EventoController:store', $e->getMessage()));
              return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
          }
			    return response()->json(['success' => false, 'errors' => $event->errors]);
		    }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(!is_numeric($id)){
		    	abort(404);
		    }

        $event = Evento::getEvent($id);
        if(!$event instanceof Evento){
          abort(404);
        }

        $event = Evento::buidEvent( $event );

        if($request->ajax()){
          return response()->json($event);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if(!is_numeric($id)){
        abort(404);
      }

      $event = Evento::getEvent($id);
      if(!$event instanceof Evento){
        abort(404);
      }
      $event = Evento::buidEvent( $event );
      $stratum = Estrato::stratum([]);
      $zone = BarrioLocalidad::zone([]);
      $calendars = Calendario::getCalendars();
      $workSector = SectorTrabajo::workSector();
      $socialGroup = GrupoSocial::socialGroup();
      $housingType = TipoVivienda::housingType();
      $locations = LocalidadCiudad::locations([]);
      $levels = NivelEducacion::levelsEducation();
      $employment = ListaOcupacion::employmentList();
      $communityGroup = GrupoComunitario::communityGroup();
      $selfDomination = Autodenominacion::selfDomination();
      $populationGroup = GrupoPoblacional::populationGroup();
      $privacyContent = PrivacidadContenido::getContentPrivacy();
      $eventType = TipoEvento::getEventType(ClaseTipoEvento::getClass('platform'));
      $eventCategories = CategoriaContenido::getCategories($event->codigo_evento);
      $eventStages = EtapaContenido::getStages($event->codigo_evento);
      $recordatorio = RecordatorioEvento::where('codigo_evento', $id)->get();
      return view('event.create-edit', compact(
                                          'event',
                                          'privacyContent',
                                          'calendars',
                                          'eventType',
                                          'levels',
                                          'employment',
                                          'zone',
                                          'stratum',
                                          'locations',
                                          'housingType',
                                          'workSector',
                                          'selfDomination',
                                          'populationGroup',
                                          'communityGroup',
                                          'socialGroup',
                                          'eventCategories',
                                          'eventStages',
                                          'recordatorio'
                                        ));
    }

    /*
     * Funcion que se encarga de reporte de eventos
     * @Brahian Sánchez
     */
    public function exportar($event) {
		\Artisan::queue('innpulsa:events', [
			'event' => $event,
			'user' => \Auth::user()->codigo_usuario,
      'institution' => session('institution')->codigo_institucion
		]);

		if(!is_numeric($event)){
			abort(404);
		}

		$user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando reporte eventos ".\Auth::user()->codigo_usuario);
		}

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => 'evento'
				]
			])
			->log('activitylog.reports');

		return redirect()->route('evento.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		if($request->ajax()){
            $data = $request->all();
			$event = Evento::getEvent($id);
			if(!$event instanceof Evento){
				throw new \Exception("Evento no encontrado {$id}");
			}

			$eventInstitucion = $event->institutions->where('codigo_institucion', session('institution')->codigo_institucion)->first();
			if(!$eventInstitucion instanceof EventoInstitucion){
				throw new \Exception("Evento no encontrado en evento_institucion {$id}");
			}

			if($request->fecha_fin_evento < $request->fecha_evento){
				return response()->json(['success' => false, 'errors' => 'La fecha fin no puede ser menor a la fecha de inicio, verifique.']);
			}

			DB::beginTransaction();
			try {
				//edit recordtorio
				$recordatorio = RecordatorioEvento::where('codigo_evento', $request->codigo_evento)
														->get();
				if ($recordatorio->count() > 0) {
					$recordatorio = RecordatorioEvento::find($recordatorio[0]->id);
					if ($recordatorio->enviar != $request->activar_recordatorio) {
						$recordatorio->enviar = !$recordatorio->enviar;
						$recordatorio->save();
					}
				} elseif ($request->activar_recordatorio) {
					$recordatorio = RecordatorioEvento::create([
						'codigo_evento' => $request->codigo_evento,
						'enviar' => true
					]);
				}
				//end edit recordatorios

          if ($request->has('codigo_evento') && $request->has('codigo_organizador')) {
            if (!empty($request->codigo_evento) && !empty($request->codigo_organizador)) {
              Evento::where('codigo_evento', $request->codigo_evento)
                ->update([
                  'seg_codigo_usuario' => $request->codigo_organizador
                ]);
            }
          }

          if ($request->has('codigo_evento') && $request->has('responsable')) {
            if (!empty($request->codigo_evento) && $request->identification_responsable != "" && !empty($request->identification_responsable)) {
              Evento::where('codigo_evento', $request->codigo_evento)
                ->update([
                  'codigo_usuario' => $request->identification_responsable
                ]);
            }
          }

          if ($request->has('inpMeet')) {
            if (!empty($request->inpMeet) && $request->inpMeet == '1') {
              $event = Evento::find($request->codigo_evento);
              if (!isset($event->url_meet_evento)) {
                Evento::createMeet(['event' => $event->codigo_evento, 'user' => Auth::user()->codigo_usuario]);
                // Queue::push('App\Models\Core\Evento@createMeet', ['event' => $event->codigo_evento, 'user' => Auth::user()->codigo_usuario]);
              }
            } else {
              Evento::where('codigo_evento', $request->codigo_evento)
              ->update([
                'url_meet_evento' => null
              ]);
            }
          }

          if ($request->has('numero_factura') && $request->has('precio_factura')) {
            if (!empty($request->numero_factura) !== '' && !empty($request->precio_factura) !== '' ) {
              Evento::where('codigo_evento', $request->codigo_evento)
              ->update([
                'factura_evento' => trim($request->numero_factura),
                'costo_factura_evento' => trim($request->precio_factura)
              ]);
            }
          }

          if(!$request->change_state && !$request->has('codigo_organizador') && !$request->has('responsable')){
              $event->fill($data);
              $event->pago_evento = $request->pago_evento ? true : false;
              $event->registro_interes_evento = $request->participacion_evento ? true : false;
              $event->cantidad_horas = $request->cantidad_horas;

              if($event->codigo_privacidad_contenido != PrivacidadContenido::getPrivacy('public')){
                $event->sync_algolia = 3;
                $event->save();
                $event->desyncIndex();
              }
              if(isset($event->sync_algolia) && $event->sync_algolia === 0  ){
                $event->sync_algolia = 1;
              }
              $event->save();

              if(!$request->has('seo') && !$request->has('codigo_organizador')){
                          EventoInstitucion::where('codigo_institucion', $eventInstitucion->codigo_institucion)
                    ->where('codigo_evento', $eventInstitucion->codigo_evento)
                    ->update([
                      'codigo_tipo_evento_calendario' => $request->codigo_tipo_evento_calendario
                    ]);
              }
          }

				$codigo_estado = $request->has('change_state') ? ($eventInstitucion->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active')) : $eventInstitucion->codigo_estado;

				if(!$request->has('seo') && !$request->has('certificate')){
					EventoInstitucion::where('codigo_institucion', $eventInstitucion->codigo_institucion)
						->where('codigo_evento', $eventInstitucion->codigo_evento)
						->update([
							'codigo_estado' => $codigo_estado
						]);
        }

        if($codigo_estado == Estado::getState('inactive')){
            $event->sync_algolia = 3;
            $event->save();
            $event->desyncIndex();
        }

				if($request->has('seo')){
					// Info seo
					$seo = Seo::where('id_recurso', $event->codigo_evento)->where('codigo_modulo', Modulo::getModule('calendar'))->first();
					if(!$seo instanceof Seo){
						$seo = new Seo;
					}

					$seo->fill($data);
					$seo->id_recurso = $event->codigo_evento;
					$seo->codigo_modulo = Modulo::getModule('calendar');
					$seo->codigo_estado = Estado::getState('active');

					if($request->has('seo_general') && $request->seo_general){
						$seo->indexar_motores = $request->indexar_motores ? TRUE : FALSE;
						$seo->enlaces_motores_busqueda = $request->enlaces_motores_busqueda ? TRUE : FALSE;
					}

					$seo->codigo_institucion = session('institution')->codigo_institucion;
					$seo->save();

					// Tags user
					$inputTags = [];
					if($request->update_tags){
						$inputTags = explode(',', $request->tags_evento);
						$tags = $event->tags()->get();
						foreach ($tags as $tag) {
							if(!in_array($tag->texto_tag, $inputTags)) {
								$event->tags()->detach($tag);
							}
						}
					}

					if($request->has('tags_evento') && is_array($inputTags) && count($inputTags) > 0) {
						foreach($inputTags as $item) {
							if(!$tags->contains('texto_tag', $item)){
								$tag = Tag::firstOrCreate(['texto_tag' => $item]);
								if(!$tag instanceof Tag) {
									throw new \Exception('tag not found');
								}
								$event->tags()->attach($tag);
							}
						}
					}
				}

				$user = Usuario::find(\Auth::user()->codigo_usuario);

				activity()
				->performedOn($user)
				->withProperties([
					'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'event' => $event->nombre_evento
					]
				])
				->log($codigo_estado == Estado::getState('inactive') ? 'activitylog.event.deactivated' : 'activitylog.event.activated');

				// Commit Transaction
				DB::commit();
				$event->syncIndex();
				return response()->json(['success' => true, 'message' => "El evento ha sido editado correctamente", 'codigo_evento' => $event->codigo_evento, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'EventoController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	public function search(Request $request)
	{
		if($request->ajax()){
			return response()->json(Evento::getEventsList( $request->all() ));
		}
		abort(403);
  }

  public function certificado(Request $request) {

    DB::beginTransaction();
    try {
      $data = Evento::getDatesCertificate($request->all());
      $data['usuario_evento'] = $request->codigo_usuario_evento;
      $name_institution = Institucion::getNameInstitution(session('institution')->codigo_institucion);
      $ubication_template = 'reports.event.' . $name_institution . '.certificado';

      $pdf = App::make('dompdf.wrapper');
      $pdf->loadHTML(view($ubication_template, $data))->setPaper('a4', 'landscape');
      $pdf->setEncryption($data['identificacion_usuario']);

      $user_event = UsuarioEvento::find($request->codigo_usuario_evento);
      $user_event->certificado_evento = true;
      $user_event->fecha_certificado = date('Y-m-d H:i:s');
      $user_event->save();

      $user = Usuario::find($request->codigo_usuario);

      $s3 = AWS::createClient('s3');

      $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

      $filename = sprintf('%s_%s_%s_%s.pdf', 'certificado_evento', $request->codigo_evento, $request->codigo_usuario, session('institution')->codigo_institucion);
      $key = sprintf('%s/%s/%s', 'documents', 'certificados', $filename);

      $result = $s3->putObject([
        'Bucket' =>  $bucket,
        'Key'    =>  $key,
        'Body'   =>  $pdf->output(),
        'ACL'    => 'public-read'
      ]);

      $dataMail = [];
      $dataMail['_template'] = 'emails.event.certificate';
      $dataMail['_inst'] = session('institution')->codigo_institucion;
      $dataMail['_subject'] = 'Certificación';
      $dataMail['_to'] = $data['correo_electronico'];
      $dataMail['comment'] = trans('program.mail.body.certificate', [
              'name_user' => $data['nombre_usuario'],
              'name_event' => $data['nombre_evento'],
              'email_contact' => session('institution')->correo_envio_notificaciones,
              'name_institution' => session('institution')->nombre_institucion
          ]);
      $dataMail['_url_action'] = $result['ObjectURL'];
      SendEmail::notification($dataMail);

      activity()
			->performedOn($user_event)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'code_certificate' =>  $request->codigo_usuario_evento,
          'event_name' => $data['nombre_evento']
				]
			])
      ->log('activitylog.certificate.notification');

      activity()
      ->performedOn($user_event)
      ->causedBy($user)
			->withProperties([
				'attributes' => [
          'code_certificate' =>  $request->codigo_usuario_evento,
          'event_name' => $data['nombre_evento']
				]
			])
			->log('activitylog.certificate.notification_user');

      // Commit Transaction
      DB::commit();

      return response()->json(['success' => true, 'message' => "Se ha enviado el certificado correctamente", 'codigo_usuario_evento' => $request->codigo_usuario_evento]);
    } catch(\Exception $e) {
        DB::rollback();
				Log::error(sprintf('%s:%s', 'EventoController:certificate', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
    }
  }

  public function duplicar(Request $request) {
    DB::beginTransaction();
    try {
      $event = Evento::select('*', 'evento.imagen_evento as image_event')
        ->join('core.evento_institucion', 'evento.codigo_evento', '=', 'evento_institucion.codigo_evento')
        ->where('evento.codigo_evento', $request->evento)->first();

      if (!$event instanceof Evento) {
        abort(404);
      }

      $newEvent = new Evento();
      $newEvent->fill($event->attributesToArray());
      $newEvent->codigo_usuario = $event->codigo_usuario;
      $newEvent->codigo_estado = $event->codigo_estado;
      $newEvent->url_evento = slugtp($event->nombre_evento, $event->getTable(), 'url_evento');
      $newEvent->cantidad_horas = $event->cantidad_horas;
      $newEvent->imagen_evento = $event->image_event;
      $newEvent->save();

      $eventInstitution = new EventoInstitucion();
      $eventInstitution->codigo_evento = $newEvent->codigo_evento;
      $eventInstitution->codigo_tipo_evento_calendario = $event->codigo_tipo_evento_calendario;
      $eventInstitution->codigo_institucion = $event->codigo_institucion;
      $eventInstitution->codigo_estado = $event->codigo_estado;
      $eventInstitution->save();

      // Commit Transaction
      DB::commit();
      return response()->json(['success' => true, 'message' => "Se ha duplicado el evento correctamente", 'codigo_evento' => $newEvent->codigo_evento ]);
    } catch (\Exception $e) {
      DB::rollback();
      Log::error(sprintf('%s:%s', 'EventoController:duplicar', $e->getMessage()));
      return response()->json(['success' => false, 'errors' => trans('app.exception')]);
    }
  }

  public function getEventType(Request $request) {
	if (is_numeric($request->calendar)) {
		$typeEventCalendar = Evento::getTypeEvent($request->calendar);
		if (!count($typeEventCalendar) > 0) {
		abort(404);
		}
		return response()->json($typeEventCalendar);
	}
  }

}
