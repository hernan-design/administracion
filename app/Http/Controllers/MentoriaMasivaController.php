<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SendEmail, TuProyecto, Log, DB, Auth, Datatables, Lang, Excel, Storage;
use App\Models\Core\TemaMentoria,
    App\Models\Core\Estado,
    App\Models\Core\Asesoria,
    App\Models\Seguridad\Usuario,
    App\Models\Param\TerminosCondiciones,
    App\Models\Aprende\AsesoriaInstitucion,
    App\Models\Core\Entidad,
    App\Models\Core\MentoriaMasiva,
    App\Models\Core\TipoEventoCalendario,
    App\Models\Core\Evento,
    App\Models\Core\EventoInstitucion,
    App\Models\Core\TemaPrincipalEvento,
    App\Models\Core\SesionMasiva,
    App\Models\Aprende\TareaMentoria,
    App\Models\Core\Convocatoria,
	App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\ConvocatoriaMentoria,
    App\Models\Core\Fase;
    use App\Exports\ReportsExport;


class MentoriaMasivaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            // return Entidad::entities( $request->all() );
            $program = Convocatoria::find(634);

            //solo trae registros en las fases indicadas
            if ($request['phase'] == Fase::getIdPhase('general')) {
                $request['phase'] = '350,351,352,353,354';
            }

            return RegistroFaseUsuario::getProgramEntity($program, $request->all());

        }

        $mentorThemes = TemaMentoria::getThemesList();
        $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('rejected'), Estado::getState('pending'), Estado::getState('inactive'), Estado::getState('closed')], $filter = null);

        return view('mentor.mentoria-masiva.index', compact('mentorThemes', 'states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * Guarda una mentoria masvisa y crea asesorias para cada entidad
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $entities = $request['data'];
        $mentor = $request['mentor'][0];
        $program = Convocatoria::find(634);//convocatoria rutan emprendimiento

        $history = array();

        //guarda codigo_asesoria de asesorias creadas
        $codigos_asesorias = [];

        foreach ($entities as $key => $entiti) {

            $user_mentoring = Usuario::getUserMail(['email' => $mentor['correo_electronico']]);
            if(!$user_mentoring instanceof Usuario) {
                abort(404);
            }

            $entity = Entidad::getEntityUrl($entiti['nickname_usuario']);
            if(!$entity instanceof Entidad) {
                abort(404);
            }

            $user = Usuario::find($entity->seg_codigo_usuario);
            if(!$user instanceof Usuario) {
                abort(404);
            }

            //inicializacion de variable
            $msg = '';
            // validation if mentor have mentoring active with the same project
            $validationMentoring = Asesoria::join('aprende.asesoria_institucion', 'asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
                ->where('asesoria_institucion.codigo_institucion', session('institution')->codigo_institucion)
                ->where('seg_codigo_usuario', $user_mentoring->codigo_usuario)
                ->where('codigo_entidad', $entity->codigo_entidad)
                ->where('asesoria_institucion.codigo_estado', Estado::getState('active'))
                ->first();

            if($validationMentoring instanceof Asesoria){
                // return response()->json(['success' => false, 'errors' => 'El asesor tiene una asesoría activa con este proyecto.']);
                $msg = 'El/Los proyecto(s) seleccionado(s) ya tienen asesoría(s) activa(s) con el mentor';
                array_push($history, "- El mentor $user_mentoring->nombre_usuario ya tiene una asesoría activa con el proyecto $entity->nombre_usuario." . "\n");
                continue;
            }

            // validate if I accept terms
            $acceptTerms = Usuario::query();
            $acceptTerms->select(
                    DB::raw("(SELECT CASE WHEN aceptacion_terminos.fecha_aceptacion_terminos IS NULL THEN FALSE ELSE TRUE END  FROM seguridad.aceptacion_terminos
                        JOIN param.version_terminos ON aceptacion_terminos.codigo_version_terminos = version_terminos.codigo_version_terminos
                        JOIN param.terminos_condiciones ON version_terminos.codigo_terminos_condiciones = terminos_condiciones.codigo_terminos_condiciones
                        AND terminos_condiciones.codigo_estado = ".Estado::getState('active')."
                        JOIN core.usuario_institucion ON usuario_institucion.codigo_usuario = " . $user_mentoring->codigo_usuario . "
                        AND usuario_institucion.codigo_institucion = ". session('institution')->codigo_institucion ."
                        WHERE terminos_condiciones.tipo_terminos ='".TerminosCondiciones::getTypeTerms('mentoring')."'
                        AND usuario_institucion.codigo_usuario_institucion = aceptacion_terminos.codigo_usuario_institucion) AS terminos_aceptados")
            );

            $acceptTerms = $acceptTerms->first();

            if($acceptTerms->terminos_aceptados != true){
                return response()->json(['success' => false, 'errors' => 'El mentor no ha aceptado los Términos y Políticas de mentoria en la plataforma']);
            }

            $convocatoria_mentoria = ConvocatoriaMentoria::where('convocatoria_mentoria.codigo_convocatoria', $program->codigo_convocatoria)
													->join('aprende.asesoria_institucion', 'asesoria_institucion.codigo_asesoria', '=', 'convocatoria_mentoria.codigo_asesoria')
													->where('convocatoria_mentoria.codigo_fase', $entiti['codigo_fase'])
													->where('convocatoria_mentoria.codigo_usuario', $entity->codigo_usuario)
													->where('convocatoria_mentoria.codigo_institucion', session('institution')->codigo_institucion)
													->where('asesoria_institucion.codigo_estado', Estado::getState('active'))
													->first();
            if($convocatoria_mentoria instanceof ConvocatoriaMentoria) {
                // return response()->json(['success' => false, 'errors' => 'No se puede asignar otro mentor a la fase actual']);
                $msg = "Al proyecto $entity->nombre_usuario no se le puede asignar otro mentor a la fase actual.";
                array_push($history, "- $msg" . "\n");
                continue;
            }

            if($request->ajax()){
                $asesoria = new Asesoria;
                DB::beginTransaction();
                try {
                    // Asesoria
                    $asesoria->descripcion_asesoria = transtp('mentoring.description');
                    $asesoria->codigo_tipo_asesoria = $mentor['codigo_tipo_asesoria'];
                    $asesoria->codigo_estado = Estado::getState('active');
                    $asesoria->codigo_usuario = $user->codigo_usuario;
                    $asesoria->fecha_asesoria = date('Y-m-d');
                    $asesoria->hora_asesoria = date('H:i:s');
                    $asesoria->seg_codigo_usuario = $user_mentoring->codigo_usuario;
                    $asesoria->codigo_entidad = $entity->codigo_entidad;
                    $asesoria->save();

                    $questionInstitution = new AsesoriaInstitucion;
                    $questionInstitution->codigo_asesoria = $asesoria->codigo_asesoria;
                    $questionInstitution->codigo_institucion = session('institution')->codigo_institucion;

                    //codigo_estado = activo para que el mentor no tenga que aceptar proceso de mentoria
                    $questionInstitution->codigo_estado = Estado::getState('active');
                    $questionInstitution->save();

                    $convocatoriaMentoria = new ConvocatoriaMentoria;
                    $convocatoriaMentoria->codigo_convocatoria	= $program->codigo_convocatoria;
                    $convocatoriaMentoria->codigo_asesoria		= $asesoria->codigo_asesoria;
                    $convocatoriaMentoria->codigo_fase			= $entiti['codigo_fase'];
                    $convocatoriaMentoria->codigo_usuario		= $entity->codigo_usuario;
                    $convocatoriaMentoria->seg_codigo_usuario	= $user_mentoring->codigo_usuario;
                    $convocatoriaMentoria->codigo_institucion	= session('institution')->codigo_institucion;
                    $convocatoriaMentoria->save();

                    //guarda codigo_asesoria de asesorias creadas
                    $codigos_asesorias[] = $asesoria->codigo_asesoria;

                    // Send notification to entrepreneur
                    $data = [];
                    $data['_template']  = 'emails.mentoring.acceptadvice';
                    $data['_inst']      = session('institution')->codigo_institucion;
                    $data['_subject']   = transtp('mentoring.subjectApplicationProcess');
                    $data['_to']        = $user->correo_electronico;
                    $data['_to_name']   = ucwords(strtolower($user->nombre_usuario));
                    $data['_url_action'] = sprintf("/%s", 'mentoria/mentores/procesos');
                    $data['_requesting'] = ucwords(strtolower($user_mentoring->nombre_usuario));
                    SendEmail::notification($data);


                    activity()
                        ->performedOn($entity)
                        ->withProperties([
                            'attributes' => [
                                'mentoring' => $asesoria->codigo_asesoria,
                                'entity' => $entity->nombre_usuario,
                                'user_asign' => $user_mentoring->nombre_usuario
                            ] ])
                        ->log('activitylog.mentoring.created');

                        activity()
                        ->performedOn($entity)
                        ->causedBy($user_mentoring)
                        ->withProperties([
                            'attributes' => [
                                'mentoring' => $asesoria->codigo_asesoria,
                                'user_mentor' => Auth::user()->nombre_usuario,
                                'mentor_code' => Auth::user()->codigo_usuario,
                                'entity' => $entity->nombre_usuario,
                                'user_asign' => $user_mentoring->nombre_usuario
                            ] ])
                        ->log('activitylog.mentoring.received');

                    // Commit Transaction
                    DB::commit();
                    //return response()->json($mentoring);
                    array_push( $history, "- Empresa " . ucwords(strtolower($entity->nombre_usuario)) );
                    continue;

                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error(sprintf('%s:%s', 'MentoriaMasivaController:store', $e->getMessage()));
                    return response()->json(array('success' => false, 'errors' => trans('app.exception')));
                }
            } else {
                abort(404);
            }
        }

        $response = [ 'success' => false, 'errors' => $msg];

        //si por lo menos un proyecto no tiene asesoría activa entonces
        //su codigo_asesoria se guardará en codigos_asesorias
        if (count($codigos_asesorias) > 0) {
            // $codigos_asesorias = implode(",", $codigos_asesorias);
            $mentoriaMasiva = new MentoriaMasiva;
            $mentoriaMasiva->seg_codigo_usuario = $user_mentoring->codigo_usuario;
            $mentoriaMasiva->codigo_institucion = session('institution')->codigo_institucion;
            $mentoriaMasiva->save();

            Asesoria::whereIn('codigo_asesoria', $codigos_asesorias)
                        ->update(['codigo_mentoria_masiva' => $mentoriaMasiva->codigo_mentoria_masiva]);

            $response = [ 'success' => true, 'message' => 'Se han creado las asesorías correctamente'];

            //send email to mentor
            $data = [];
            $data['_template'] = 'emails.mentoring.requestprocessmassive';
            $data['_inst'] = session('institution')->codigo_institucion;
            $data['_subject'] = transtp('mentoring.subjectApplicationProcess');
            $data['_to'] = $user_mentoring->correo_electronico;
            $data['to_copy'] = ['administracion@tuproyecto.com', 'juan.tabares@rutanmedellin.org'];
            $data['_to_name'] = ucwords(strtolower($user_mentoring->nombre_usuario));
            $data['_history'] = $history;
            SendEmail::notification($data);
        }


        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //si la solicitud es ajax o desde datatable
        $mentoriaMasiva = MentoriaMasiva::find($id);

        if($request->ajax()){

            $mentoriaMasiva = Asesoria::select(
                                            'codigo_asesoria',
                                            'fecha_asesoria',
                                            'usuario_entidad.nombre_usuario as nombre_entidad',
                                            DB::raw("CONCAT( usuario.nombre_usuario, ' ', p_usuario.primer_apellido_persona ) as nombre_usuario"),
                                            'usuario.correo_electronico as correo_usuario'
                                        )
                                ->join('core.entidad', 'entidad.codigo_entidad', '=', 'asesoria.codigo_entidad')
                                ->join('seguridad.usuario as usuario_entidad', 'usuario_entidad.codigo_usuario', '=', 'entidad.codigo_usuario')
                                ->join('seguridad.usuario as usuario', 'usuario.codigo_usuario', '=', 'asesoria.codigo_usuario')
                                ->join('core.persona as p_usuario', 'usuario.codigo_usuario', '=', 'p_usuario.codigo_usuario')

                                ->where('codigo_mentoria_masiva', $mentoriaMasiva->codigo_mentoria_masiva)
                                ->get();
            return Datatables::of( $mentoriaMasiva )->make(true);
        }

        //obtengo nombre usuario y apellido usuario mediante las relaciones
        $mentor = $mentoriaMasiva->usuario->nombre_usuario . " " . $mentoriaMasiva->usuario->persona->primer_apellido_persona;
        $codigo_mentoria_masiva = $mentoriaMasiva->codigo_mentoria_masiva;
        $count_asesorias = count($mentoriaMasiva->asesorias);

        return view('mentor.mentoria-masiva.show', compact('mentor', 'codigo_mentoria_masiva', 'count_asesorias'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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

    /**
     * get mentor de mentoria masiva
     * @param \Illuminate\Http\Request  $request
     * @return \App\Models\Seguridad\Usuario
     */
    public function getMentor(Request $request): Usuario
    {
        $user = Usuario::select(
                            'codigo_usuario',
                            'nombre_usuario',
                            'correo_electronico',
                            'nickname_usuario',
                            'imagen_usuario'
                        )
                        ->where('correo_electronico', $request->correo_electronico)
                        ->first();

        if (!$user instanceof Usuario) {
            Log::error(sprintf('%s:%s', 'MentoriaMasivaController:getMentor ', 'Error al recuperar Usuario'));
        }

        return $user;
    }

    /**
     * obtener datos de taba MentoriaMasiva
     * @param \Illuminate\Http\Request  $request
     * @return App\Models\Core\MentoriaMasiva
     */
    public function getMassiveMentoring(Request $request)
    {
        //si la solicitud es ajax o desde datatable
        if($request->ajax()){
            $mentoriaMasiva = MentoriaMasiva::select(
                                'codigo_mentoria_masiva',
                                DB::raw("CONCAT( mentor.nombre_usuario, ' ', p_mentor.primer_apellido_persona ) as nombre_mentor"),
                                'created_at as fecha_creacion'
                            )
                            ->join('seguridad.usuario as mentor', 'mentor.codigo_usuario', '=', 'mentorias_masivas.seg_codigo_usuario')
                            ->join('core.persona as p_mentor', 'mentor.codigo_usuario', '=', 'p_mentor.codigo_usuario')

                            ->where('mentorias_masivas.codigo_institucion', session('institution')->codigo_institucion)
                            ->orderBy('codigo_mentoria_masiva', 'asc')
                            ->get();

            return Datatables::of( $mentoriaMasiva )
                                ->addColumn('codigos_asesorias', function ($data) {
                                    return count($data->asesorias);
                                })
                                ->make(true);
        }

        return view('mentor.mentoria-masiva.list');
    }

    /**
	 * almacena sesiones o eventos de mentoria
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function storeSessions(Request $request)
	{
		if ($request->ajax()) {

            //obtener tipo calendario
			$typeEvenCalendar = TipoEventoCalendario::getCalendarInstitution($request->codigo_tipo_evento);

			if(!$typeEvenCalendar instanceof TipoEventoCalendario) {
				$message = "EL tipo de evento no es válido";
				Log::error(sprintf('%s:%s', 'EventoController store',$message));
				return response()->json(['success' => false, 'errors' => $message]);
			}

            $mentoriaMasiva = MentoriaMasiva::find($request->codigo_mentoria_masiva);

            //guardamos registro en sesión masiva para tener control de dichas sesiones
            $sesion_masiva = new SesionMasiva;
            $sesion_masiva->codigo_mentoria_masiva = $mentoriaMasiva->codigo_mentoria_masiva;
            $sesion_masiva->save();

            foreach ($mentoriaMasiva->asesorias as $key => $advisory) {
                $asesoria = Asesoria::find($advisory->codigo_asesoria);

                DB::beginTransaction();
                try {
			        $event = new Evento;
                    $event->fill($request->all());
                    $event->codigo_calendario = $typeEvenCalendar->codigo_calendario;
                    $event->codigo_usuario    = $mentoriaMasiva->usuario->codigo_usuario; //este codigo_usuario es del mentor
                    $event->codigo_estado     = Estado::getState('active');
                    $event->privacidad_evento = false;
                    $event->codigo_tema_principal_evento = $request->has('codigo_tema_principal_evento')
                                                            ? (int) $request->codigo_tema_principal_evento : null;

                    $event->url_evento = slugtp($event->nombre_evento, 'core.evento', 'url_evento');

                    // Date start event
                    if($request->has('fecha_inicio') && !empty($request->fecha_inicio)) {
                        list($date, $time)          = explode(' ', $request->fecha_inicio);
                        $event->fecha_evento        = $date;
                        $event->hora_inicio_evento  = $time;
                    }

                    // Date end event
                    if($request->has('fecha_fin') && !empty($request->fecha_fin)) {
                        list($date, $time) = explode(' ', $request->fecha_fin);
                        $event->fecha_fin_evento = $date;
                        $event->hora_fin_evento  = $time;
                    }

                    $event->asistencia_evento = false;
                    $event->save();


                    $eventInstitution = new EventoInstitucion;
                    $eventInstitution->codigo_evento = $event->codigo_evento;
                    $eventInstitution->codigo_tipo_evento_calendario = $typeEvenCalendar->codigo_tipo_evento_calendario;
                    $eventInstitution->codigo_institucion = session('institution')->codigo_institucion;
                    $eventInstitution->codigo_estado = Estado::getState('active');
                    $eventInstitution->save();

                    if ($advisory->codigo_asesoria != 0) {
                        Evento::createMentoring(
                                    $event,
                                    $asesoria->usuario->codigo_usuario,
                                    $advisory->codigo_asesoria,
                                    $request->all(),
                                    $sesion_masiva->codigo_sesion_masiva
                                );
                    }

                    // confirmacion de mentoría
                    Evento::confirmMentoring($event);

                    //contact email
                    // Send notification to entrepreneur
                    $data = [];
                    switch (session('institution')->codigo_institucion ) {
                        case '61':
                            Log::info('template de ruta n');
                            $data['_template'] = 'emails.mentoring.confirmrutan';
					        $contactEmail = 'rutan@rutan.co';
                            break;
                        default:
                            $data['_template'] = 'emails.mentoring.confirm';
					        $contactEmail = '';
                            break;
                    }
                    $data['_contactEmail'] = $contactEmail;
			        $data['_subject'] = transtp('mentoring.processMassive.mentorconfirm_subject', ['user' => $mentoriaMasiva->usuario->nombre_usuario]);
                    $data['_name_user_action'] = $event->usuario->nombre_usuario;
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['date_start'] = sprintf("%s %s", TuProyecto::dateFormat($event->fecha_evento, "complete"), TuProyecto::timeFormat($event->hora_inicio_evento));
                    $data['date_end'] = sprintf("%s %s", TuProyecto::dateFormat($event->fecha_fin_evento, "complete"), TuProyecto::timeFormat($event->hora_fin_evento));
                    $data['_to'] = $asesoria->usuario->correo_electronico;
                    $data['_to_name'] = $asesoria->usuario->nombre_usuario;
                    SendEmail::notification($data);

                    // Commit Transaction
                    DB::commit();

                }catch(\Exception $e){
                    DB::rollback();
                    Log::error(sprintf('%s:%s', 'MentoriaMasivaController storeSessions', $e->getMessage()));
                    return response()->json(['success' => false, 'errors' => Lang::get('tuproyecto.exception')]);
                }
            }

             //iniciamos a crear excel

            $dataQuery = Asesoria::select(
                                        'asesoria.codigo_asesoria',
                                        'mentoria.codigo_evento as codigo_sesion',
                                        'fecha_asesoria',
                                        'usuario_entidad.nombre_usuario as nombre_entidad',
                                        DB::raw("CONCAT( usuario.nombre_usuario, ' ', p_usuario.primer_apellido_persona ) as nombre_usuario"),
                                        'usuario.correo_electronico as correo_usuario',
                                        'mentoria.descripcion_mentoria'
                                    )
                                ->join('core.entidad', 'entidad.codigo_entidad', '=', 'asesoria.codigo_entidad')
                                ->join('seguridad.usuario as usuario_entidad', 'usuario_entidad.codigo_usuario', '=', 'entidad.codigo_usuario')
                                ->join('seguridad.usuario as usuario', 'usuario.codigo_usuario', '=', 'asesoria.codigo_usuario')
                                ->join('core.persona as p_usuario', 'usuario.codigo_usuario', '=', 'p_usuario.codigo_usuario')
                                ->join('aprende.mentoria', 'mentoria.codigo_asesoria', '=', 'asesoria.codigo_asesoria')
                                ->where('asesoria.codigo_mentoria_masiva', $mentoriaMasiva->codigo_mentoria_masiva)
                                ->where('mentoria.codigo_sesion_masiva', $sesion_masiva->codigo_sesion_masiva)
                                ->get();

            $filename = sprintf('%s.xlsx', 'cargue_desarrollo_sesiones');
            $filepath = sprintf('reports/%s/%s/%s', session('institution')->codigo_institucion, $mentoriaMasiva->usuario->codigo_usuario, $filename);

            $header = array(
              '# mentoria',
              '# sesion',
              'Entidad',
              'Usuario',
              'Correo usuario',
              'Fecha mentoria',
              'Descripción de la intervención',
              'Nombre del Beneficio otorgado',
              'Acuerdos/Plan de acción',
              'Informacion del emprendimiento',
              'Observaciones',
              'Duración de la sesión',
              'Fases del Proceso ',
              '¿Se recomienda para la siguiente etapa?',
              'Seguimiento',
              'Asignaciónde VOUCHER',
          );
          $sheet = [];
          foreach ($dataQuery as $value) {

            $sheet [] = [
                $value->codigo_asesoria,
                $value->codigo_sesion,
                $value->nombre_entidad,
                $value->nombre_usuario,
                $value->correo_usuario,
                $value->fecha_asesoria,
                $value->descripcion_mentoria,
            ];
        }

          $export = new ReportsExport([$header], [$sheet]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);

            //send email to mentor
            $data = [];
            $data['_template'] = 'emails.mentoring.requestsessionmassive';
            $data['_inst'] = session('institution')->codigo_institucion;
            $data['_subject'] = transtp('mentoring.subjectApplicationProcess');
            $data['_to'] = $mentoriaMasiva->usuario->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($mentoriaMasiva->usuario->nombre_usuario));
            $data['_to_name'] = ucwords(strtolower($mentoriaMasiva->usuario->nombre_usuario));
            $data['_url_action'] = $fileUrl;
            SendEmail::notification($data);

            return response()->json(['success' => true, 'message' => 'Se han creado las sesiones satisfactoriamente']);

		}
		abort(404);
	}

    /**
     * retorna la lista de temas para eventos o
     * sesiones de mentorías
     * @param Illuminate\Http\Request $request
     * @return App\Models\Core\TemaPrincipalEvento
     */
    public function getTemasSesionList(Request $request)
    {
        if($request->ajax()){
            return TemaPrincipalEvento::temasEventoList($request);
        }
        abort('404');
    }

    /**
     * @param int $id
     * @return MentoriaMasiva
     */
    public function getSessionsMass($id)
    {
        $mentoriaMasiva = MentoriaMasiva::select(
                        'sesiones_masivas.codigo_sesion_masiva',
                        'sesiones_masivas.created_at as creacion_sesion_masiva'
                        )
                        ->join('core.sesiones_masivas', 'sesiones_masivas.codigo_mentoria_masiva', 'mentorias_masivas.codigo_mentoria_masiva')

                        ->where('mentorias_masivas.codigo_mentoria_masiva', $id)
                        ->orderBy('sesiones_masivas.codigo_sesion_masiva', 'desc')
                        ->get();
        return Datatables::of( $mentoriaMasiva )
                        ->make(true);
    }

    /**
     * @param Illuminate\Http\Request $request
     * @param int $sesion
     * @return Datatables
     */
    public function getSingleSession(Request $request, int $mentoriamasiva)
    {
        $sesiones = MentoriaMasiva::select(
                'sesiones_masivas.codigo_sesion_masiva',
                'mentoria.codigo_mentoria',
                'mentoria.codigo_estado',
                'usuario_entidad.nombre_usuario as nombre_entidad',
                DB::raw("CONCAT( usuario.nombre_usuario, ' ', p_usuario.primer_apellido_persona ) as nombre_usuario"),
                'usuario.correo_electronico as correo_usuario',
                'evento.fecha_evento',
                'mentoria.descripcion_mentoria',
                'tema_principal_evento.nombre_tema_principal_evento'
                )
                ->join('core.sesiones_masivas', 'sesiones_masivas.codigo_mentoria_masiva', 'mentorias_masivas.codigo_mentoria_masiva')
                ->join('aprende.mentoria', 'mentoria.codigo_sesion_masiva', 'sesiones_masivas.codigo_sesion_masiva')
                ->join('core.asesoria', 'asesoria.codigo_asesoria', 'mentoria.codigo_asesoria')
                ->join('core.evento', 'evento.codigo_evento', 'mentoria.codigo_evento')
                ->join('core.tema_principal_evento', 'tema_principal_evento.codigo_tema_principal_evento', 'evento.codigo_tema_principal_evento')
                ->join('core.entidad', 'entidad.codigo_entidad', '=', 'asesoria.codigo_entidad')
                ->join('seguridad.usuario as usuario_entidad', 'usuario_entidad.codigo_usuario', '=', 'entidad.codigo_usuario')
                ->join('seguridad.usuario as usuario', 'usuario.codigo_usuario', '=', 'asesoria.codigo_usuario')
                ->join('core.persona as p_usuario', 'usuario.codigo_usuario', '=', 'p_usuario.codigo_usuario')

                ->where('mentorias_masivas.codigo_mentoria_masiva', $mentoriamasiva)
                ->where('sesiones_masivas.codigo_sesion_masiva', $request->idsesionmasiva)
                ->where('mentoria.codigo_estado', 8)
                ->orderBy('sesiones_masivas.codigo_sesion_masiva', 'desc')
                ->get();

        foreach ($sesiones as $key => $sesion) {
            $descripcion_tarea_mentoria = TareaMentoria::select('descripcion_tarea_mentoria')
                                            ->where('codigo_mentoria', $sesion->codigo_mentoria)
                                            ->get();
            if ($descripcion_tarea_mentoria->count() > 0) {
                $des = $descripcion_tarea_mentoria->implode('descripcion_tarea_mentoria', ',');
            }

                $sesion->descripcion_tarea_mentoria = isset($des) ? $des : '';
        }
        return Datatables::of( $sesiones )
                            ->make(true);
    }
}
