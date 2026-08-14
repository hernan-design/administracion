<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, Auth, Queue, Datatables;

use App\Models\Seguridad\Modulo, App\Models\Core\Muro, App\Models\Core\MuroInstitucion, App\Models\Core\Estado, App\Models\Aprende\Curso, App\Models\Aprende\TipoCurso, App\Models\Aprende\OrientacionCurso, App\Models\Core\Contenido, App\Models\Core\Convocatoria, App\Models\Core\Evento, App\Models\Core\TipoContenido, App\Models\Core\Notificacion, App\Models\Core\NivelEducacion, App\Models\Core\ListaOcupacion, App\Models\Core\LocalidadCiudad, App\Models\Core\BarrioLocalidad, App\Models\Core\Estrato, App\Models\Core\TipoVivienda, App\Models\Core\SectorTrabajo, App\Models\Core\Autodenominacion, App\Models\Core\GrupoPoblacional, App\Models\Core\GrupoComunitario, App\Models\Core\GrupoSocial, App\Models\Seguridad\Usuario;

use App\Models\Mongodb\Notificacion as MDBNotificacion;

class NotificacionOutputController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,notificacion/output');
        $this->middleware('ability:admin|super-admin|administrativo,create,notificacion/output', ['only' => ['create', 'store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
			return Datatables::of( Notificacion::notifications( $request->all() ))->make(true);
        }

        $types = Notificacion::$type;
		return view('notification.output.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = Notificacion::$type;
        $stratum = Estrato::stratum([]);
        $addressees = Notificacion::addressee();
		$workSector = SectorTrabajo::workSector();
		$socialGroup = GrupoSocial::socialGroup();
		$housingType = TipoVivienda::housingType();
		$locations = LocalidadCiudad::locations([]);
		$levels = NivelEducacion::levelsEducation();
		$employment = ListaOcupacion::employmentList();
		$communityGroup = GrupoComunitario::communityGroup();
		$selfDomination = Autodenominacion::selfDomination();
		$populationGroup = GrupoPoblacional::populationGroup();

        $modules = Modulo::getModules([], [Modulo::getModule('calendar'), Modulo::getModule('blog'), Modulo::getModule('learn'), Modulo::getModule('program')], $filter = null);
        return view('notification.output.create-edit', compact('modules', 'types', 'addressees',  'levels', 'employment', 'stratum', 'locations', 'housingType', 'workSector', 'selfDomination', 'populationGroup', 'communityGroup', 'socialGroup'));
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
            if(!isset($request->envio_plataforma) && !isset($request->envio_email)){
                return response()->json(['success' => false, 'errors' => 'Debe seleccionar almenos un método de notificación, verifique.']);
            }

            $data = $request->all();
            $module = '';
            $resource = '';
            $title_resource = $request->titulo_notificacion;
            $url_resource = $request->has('url_notificacion') ? $request->url_notificacion : '';

            if($request->has('codigo_modulo')){
                $module = $request->codigo_mudulo;
                switch ($request->codigo_modulo) {
                    case Modulo::getModule('calendar'):
                        $event = Evento::getEvent($request->codigo_recurso_modulo);

                        $resource = $request->codigo_recurso_modulo;
                        $title_resource = $event->nombre_evento;
                        $url_resource = session('institution')->url_institucion.'/calendario/evento/'.$event->url_evento;
                    break;

                    case Modulo::getModule('learn'):
                        $course = Curso::getCourse($request->codigo_recurso_modulo);
                        $type_course = TipoCurso::getCourseTypeById($course->tipo_curso);
                        $type = $type_course->url_tipo_curso;

                        if($course->codigo_orientacion_curso == OrientacionCurso::courseOrientation('internal')){
                            $link = session('institution')->url_institucion.'/aprende/'.$type.'/'.$course->url_categoria_curso.'/'.$course->url_curso;
                        }

                        if($course->codigo_orientacion_curso == OrientacionCurso::courseOrientation('external')){
                            $link = $course->url_externa_curso;
                        }

                        $resource = $request->codigo_recurso_modulo;
                        $title_resource = $course->nombre_curso;
                        $url_resource = $link;
                    break;

                    case Modulo::getModule('blog'):
                        $content = Contenido::getArticle($request->codigo_recurso_modulo);
                        $contenType = TipoContenido::getType($content->codigo_tipo_cont_institucion);

                        $resource = $request->codigo_recurso_modulo;
                        $title_resource = $content->nombre_contenido;
                        $url_resource = session('institution')->url_institucion.'/blog/'.$contenType->url_tipo_contenido.'/'.$content->url_contenido;
                    break;

                    case Modulo::getModule('program'):
                        $program = Convocatoria::getProgramById($request->codigo_recurso_modulo);
                        if($program->tipo_convocatoria == Convocatoria::getCallType('internal')){
                            $link = session('institution')->url_institucion.'/convocatoria/'.$program->url_convocatoria;
                        }

                        if($program->tipo_convocatoria == Convocatoria::getCallType('external')){
                            $link = $program->url_externa_convocatoria;
                        }

                        $resource = $request->codigo_recurso_modulo;
                        $title_resource = $program->nombre_convocatoria;
                        $url_resource = $link;
                    break;
                }
            }

            $notification = new Notificacion;
            if ($notification->isValid($data)) {
                DB::beginTransaction();
                try {
                    $notification->fill($data);
                    $notification->codigo_estado = Estado::getState('active');
                    $notification->codigo_institucion = session('institution')->codigo_institucion;
                    $notification->fecha_notificacion = date('Y-m-d H:i:s');
                    $notification->url_notificacion = $url_resource;
                    $notification->titulo_notificacion = $title_resource;
                    $notification->envio_plataforma = $request->envio_plataforma ? TRUE : FALSE;
                    $notification->envio_email = $request->envio_email ? TRUE : FALSE;
                    $notification->save();

                    $dataNotification = [
                        'title' => $title_resource,
                        'content' => $request->texto_notificacion,
                        'url' => $url_resource,
                        'type' => MDBNotificacion::getType('general'),
                        'module' => $notification->codigo_modulo,
                        'resource' => $resource,
                        'codigo_usuario' => Auth::user()->codigo_usuario,
                        'codigo_notificacion' => $notification->codigo_notificacion,
                        'logo_institucion' => session('institution')->logo_institucion,
                        'nombre_institucion' => session('institution')->nombre_institucion,
                        'codigo_institucion' => session('institution')->codigo_institucion
                    ];

                    // Mail notification
                    Queue::push('App\Models\Core\Notificacion@notifyUsers', array_merge($dataNotification, $data));

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'Se ha notificado correctamente.']);

                }catch(\Exception $e){
                    DB::rollback();
                    Log::error(sprintf('%s:%s', 'NotificacionOutputController:store', $e->getMessage()));
                    return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }
            }
			return response()->json(['success' => false, 'errors' => $notification->errors]);
        }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if($request->ajax()){
            $notificationOld = Notificacion::find($id);
            if(!$notificationOld instanceof Notificacion){
                abort(404);
            }

            $module = '';
            $resource = '';
            $title_resource = $notificationOld->titulo_notificacion;
            $url_resource = $notificationOld->url_notificacion;

            if($notificationOld->codigo_modulo){
                $module = $notificationOld->codigo_mudulo;
                switch ($notificationOld->codigo_modulo) {
                    case Modulo::getModule('calendar'):
                        $event = Evento::getEvent($notificationOld->codigo_recurso_modulo);

                        $resource = $notificationOld->codigo_recurso_modulo;
                        $title_resource = $event->nombre_evento;
                        $url_resource = session('institution')->url_institucion.'/calendario/evento/'.$event->url_evento;
                    break;

                    case Modulo::getModule('learn'):
                        $course = Curso::getCourse($notificationOld->codigo_recurso_modulo);
                        $type_course = TipoCurso::getCourseTypeById($course->tipo_curso);
                        $type = $type_course->url_tipo_curso;

                        if($course->codigo_orientacion_curso == OrientacionCurso::courseOrientation('internal')){
                            $link = session('institution')->url_institucion.'/aprende/'.$type.'/'.$course->url_categoria_curso.'/'.$course->url_curso;
                        }

                        if($course->codigo_orientacion_curso == OrientacionCurso::courseOrientation('external')){
                            $link = $course->url_externa_curso;
                        }

                        $resource = $notificationOld->codigo_recurso_modulo;
                        $title_resource = $course->nombre_curso;
                        $url_resource = $link;
                    break;

                    case Modulo::getModule('blog'):
                        $content = Contenido::getArticle($notificationOld->codigo_recurso_modulo);
                        $contenType = TipoContenido::getType($content->codigo_tipo_cont_institucion);

                        $resource = $notificationOld->codigo_recurso_modulo;
                        $title_resource = $content->nombre_contenido;
                        $url_resource = session('institution')->url_institucion.'/blog/'.$contenType->url_tipo_contenido.'/'.$content->url_contenido;
                    break;

                    case Modulo::getModule('program'):
                        $program = Convocatoria::getProgramById($notificationOld->codigo_recurso_modulo);
                        if($program->tipo_convocatoria == Convocatoria::getCallType('internal')){
                            $link = session('institution')->url_institucion.'/convocatoria/'.$program->url_convocatoria;
                        }

                        if($program->tipo_convocatoria == Convocatoria::getCallType('external')){
                            $link = $program->url_externa_convocatoria;
                        }

                        $resource = $notificationOld->codigo_recurso_modulo;
                        $title_resource = $program->nombre_convocatoria;
                        $url_resource = $link;
                    break;
                }
            }

            DB::beginTransaction();
            try {
                $notification = new Notificacion;
                $notification->fill($notificationOld->toArray());
                $notification->codigo_estado = Estado::getState('active');
                $notification->codigo_institucion = session('institution')->codigo_institucion;
                $notification->fecha_notificacion = date('Y-m-d H:i:s');
                $notification->url_notificacion = $url_resource;
                $notification->titulo_notificacion = $title_resource;
                $notification->envio_plataforma = $notificationOld->envio_plataforma;
                $notification->envio_email = $notificationOld->envio_email;
                $notification->save();

                $data['destinatarios_notificacion'] = $notificationOld->destinatarios_notificacion;

                if( ($notificationOld->destinatarios_notificacion == Notificacion::getAddresseeNotification('addressee_to')) || ($notificationOld->destinatarios_notificacion == Notificacion::getAddresseeNotification('filters')) ){
                    $usersNoti = $notificationOld->usersNotification()->pluck('codigo_usuario')->toArray();
                    $channelsNoti = $notificationOld->channelsNotification()->pluck('codigo_canal')->toArray();

                    if(!empty($channelsNoti)){
                        $data['channels'] = $channelsNoti;
                    }

                    if(!empty($usersNoti)){
                        $data['users'] = $usersNoti;
                    }
                }

                $dataNotification = [
                    'update' => true,
                    'title' => $title_resource,
                    'content' => $notificationOld->texto_notificacion,
                    'url' => $url_resource,
                    'type' => MDBNotificacion::getType('general'),
                    'module' => $notification->codigo_mudulo,
                    'resource' => $resource,
                    'codigo_usuario' => Auth::user()->codigo_usuario,
                    'codigo_notificacion' => $notification->codigo_notificacion,
                    'logo_institucion' => session('institution')->logo_institucion,
                    'codigo_institucion' => session('institution')->codigo_institucion
                ];

                // Mail notification
                Queue::push('App\Models\Core\Notificacion@notifyUsers', array_merge($dataNotification, $data));

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Se ha notificado correctamente.']);

            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'NotificacionOutputController:update', $e->getMessage()));
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
        if(!is_numeric($id)){
			abort(404);
        }

        $notification = Notificacion::find($id);
        if(!$notification instanceof Notificacion){
            abort(404);
        }

        DB::beginTransaction();
		try {
			$notification->codigo_estado = Estado::getState('inactive');
			$notification->save();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'La notificación se ha eliminado correctamente.']);

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'NotificacionController:destroy', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
    }
}
