<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Datatables, Log, DB, SendEmail;

use App\Models\Param\Institucion;

use App\Models\Core\UsuarioEvento, App\Models\Core\Evento, App\Models\Core\UsuarioInstitucion, App\Models\Core\Estado, App\Models\Seguridad\Usuario, App\Models\Core\AsistenciaSesion;

class UsuarioEventoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evento/participante', ['except' => ['search']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evento/participante', ['only' => ['update']]);
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return Datatables::of( UsuarioEvento::usuariosEvento( $request->all() ) )->make(true);
		}
    }

    /**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		if($request->ajax()){
      $data = $request->all();

      DB::beginTransaction();
      try {
        $message = "Se ha invitado a los usuarios correctamente.";

        $event = Evento::find($request->codigo_evento);
        if(!$event instanceof Evento){
          abort(404);
        }

        if ($request->has('codigo_usuario') && !$request->has('turn')) {
          $user = Usuario::find($data['codigo_usuario']);
          if (!$user instanceof Usuario) {
            abort(404);
          }
          //dd("OK 1");
          $userList = Usuario::usersList($request->all())->get();

          foreach ($userList as $value) {
            $userEvent = UsuarioEvento::find($value->codigo_usuario_evento);
            if (!$userEvent instanceof UsuarioEvento) {
              $userInstitution = UsuarioInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
                ->where('codigo_usuario', $value->codigo_usuario)->first();

              $userEvent = new UsuarioEvento;
              $userEvent->codigo_evento = $event->codigo_evento;
              $userEvent->codigo_estado = Estado::getState('active');
              $userEvent->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
              $userEvent->fecha_usuario_evento = date('Y-m-d H:m:s');
              $userEvent->save();

              switch (session('institution')->codigo_institucion) {
                case Institucion::institution('compensar');
                   Log::info(sprintf('%s:%s', 'UsuarioEventoController:store', "Event Invitation email is disabled"));
                break;

                default:
                  $dataMail = [];
                  $dataMail['_template'] = 'emails.event.invitation';
                  $dataMail['_inst'] = session('institution')->codigo_institucion;
                  $dataMail['_subject'] = 'Invitación a Evento';
                  $dataMail['_to'] = $user->correo_electronico;
                  $dataMail['_name_user'] = $user->nombre_usuario;
                  $dataMail['_name_event'] = $event->nombre_evento;
                  $dataMail['_url_action'] = '/calendario/evento/';
                  $dataMail['_url_link'] = trim(strtolower($event->url_evento));
                  $dataMail['label_action'] = 'Ver detalle del evento';
                  SendEmail::notification($dataMail);
                break;
              }

              activity()
              ->performedOn($userEvent)
              ->withProperties([
                'attributes' => [
                  'user' => $user->nombre_usuario,
                  'user_id' => $user->codigo_usuario,
                  'title' => $event->nombre_evento
                ]
              ])
              ->log('activitylog.event.invitation');

              activity()
              ->performedOn($userEvent)
              ->causedBy($user)
              ->withProperties([
                'attributes' => [
                  'title' => $event->nombre_evento
                ]
              ])
              ->log('activitylog.event.invitation_user');
            }
          }
        } else if ($request->has('codigos_usuarios') && !$request->has('turn') ) {
//          dd("OK 2");
          $filter = [];
          $filter['codigo_evento'] = $event->codigo_evento;
          foreach ($data['codigos_usuarios'] as $value) {
            $user = Usuario::find($value);
            if (!$user instanceof Usuario) {
              abort(404);
            }

            $filter['codigo_usuario'] = $value;
            $userList = Usuario::usersList($filter)->first();

            $userEvent = UsuarioEvento::find($userList->codigo_usuario_evento);

            if (!$userEvent instanceof UsuarioEvento) {
              $userInstitution = UsuarioInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
                ->where('codigo_usuario', $value)->first();

              $userEvent =  new UsuarioEvento;
              $userEvent->codigo_evento = $event->codigo_evento;
              $userEvent->codigo_estado = Estado::getState('active');
              $userEvent->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
              $userEvent->fecha_usuario_evento = date('Y-m-d H:m:s');
              $userEvent->save();
            }

            switch (session('institution')->codigo_institucion) {
              case Institucion::institution('compensar');
                 Log::info(sprintf('%s:%s', 'UsuarioEventoController:store', "Event Invitation email is disabled"));
              break;

              default:
                $dataMail = [];
                $dataMail['_template'] = 'emails.event.invitation';
                $dataMail['_inst'] = session('institution')->codigo_institucion;
                $dataMail['_subject'] = 'Invitación a Evento';
                $dataMail['_to'] = $user->correo_electronico;
                $dataMail['_name_user'] = $user->nombre_usuario;
                $dataMail['_name_event'] = $event->nombre_evento;
                $dataMail['_url_action'] = '/calendario/evento/';
                $dataMail['_url_link'] = trim(strtolower($event->url_evento));
                $dataMail['label_action'] = 'Ver detalle del evento';
                SendEmail::notification($dataMail);
              break;

            }

            activity()
              ->performedOn($userEvent)
              ->withProperties([
                'attributes' => [
                  'user' => $user->nombre_usuario,
                  'user_id' => $user->codigo_usuario,
                  'title' => $event->nombre_evento
                ]
              ])
              ->log('activitylog.event.invitation');

              activity()
              ->performedOn($userEvent)
              ->causedBy($user)
              ->withProperties([
                'attributes' => [
                  'title' => $event->nombre_evento
                ]
              ])
              ->log('activitylog.event.invitation_user');
          }
        } else if($request->has('codigo_usuario') && $request->has('turn')) {
          $data = $request->all();
          $dataSend = [];

          if ($request->turn) {
            $user = Usuario::find($data['codigo_usuario']);
            if (!$user instanceof Usuario) {
              abort(404);
            }

            $dataSend['codigo_usuario'] = $data['codigo_usuario'];
            $dataSend['codigo_evento'] = $data['codigo_evento'];
            $userList = Usuario::usersList($dataSend)->first();

            $assistSession = AsistenciaSesion::where('codigo_usuario_evento', $userList->codigo_usuario_evento)->first();

            if ($assistSession instanceof AsistenciaSesion) {
              DB::rollback();
              return response()->json(['success' => false, 'errors' => 'No se puede quitar invitación a usuarios que ya asistieron a sesiones']);
            } else {
              $userEvent = UsuarioEvento::find($userList->codigo_usuario_evento);
              $userEvent->delete();
              $message = "Se ha devinculado a los usuarios correctamente";
            }

            activity()
              ->performedOn($userEvent)
              ->withProperties([
                'attributes' => [
                  'user' => $user->nombre_usuario,
                  'user_id' => $user->codigo_usuario,
                  'title' => $event->nombre_evento
                ]
              ])
              ->log('activitylog.event.uninvitation');

              activity()
              ->performedOn($userEvent)
              ->causedBy($user)
              ->withProperties([
                'attributes' => [
                  'title' => $event->nombre_evento
                ]
              ])
              ->log('activitylog.event.uninvitation_user');

          } else {
            $user = Usuario::find($data['codigo_usuario']);
            if (!$user instanceof Usuario) {
              abort(404);
            }

            $dataSend['codigo_usuario'] = $data['codigo_usuario'];
            $dataSend['codigo_evento'] = $data['codigo_evento'];
            $userList = Usuario::usersList($dataSend)->first();

            $userEvent = UsuarioEvento::find($userList->codigo_usuario_evento);

            if (!$userEvent instanceof UsuarioEvento) {
              $userInstitution = UsuarioInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
                ->where('codigo_usuario', $data['codigo_usuario'])->first();

              $userEvent = new UsuarioEvento;
              $userEvent->codigo_evento = $event->codigo_evento;
              $userEvent->codigo_estado = Estado::getState('active');
              $userEvent->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
              $userEvent->fecha_usuario_evento = date('Y-m-d H:m:s');
              $userEvent->save();
            }
            switch (session('institution')->codigo_institucion) {
              case Institucion::institution('compensar');
                 Log::info(sprintf('%s:%s', 'UsuarioEventoController:store', "Event Invitation email is disabled"));
              break;

              default:
                $dataMail = [];
                $dataMail['_template'] = 'emails.event.invitation';
                $dataMail['_inst'] = session('institution')->codigo_institucion;
                $dataMail['_subject'] = 'Invitación a Evento';
                $dataMail['_to'] = $user->correo_electronico;
                $dataMail['_name_user'] = $user->nombre_usuario;
                $dataMail['_name_event'] = $event->nombre_evento;
                $dataMail['_url_action'] = '/calendario/evento/';
                $dataMail['_url_link'] = trim(strtolower($event->url_evento));
                $dataMail['label_action'] = 'Ver detalle del evento';
                SendEmail::notification($dataMail);
              break;
            }
            activity()
            ->performedOn($userEvent)
            ->withProperties([
              'attributes' => [
                'user' => $user->nombre_usuario,
                'user_id' => $user->codigo_usuario,
                'title' => $event->nombre_evento
              ]
            ])
            ->log('activitylog.event.invitation');

            activity()
            ->performedOn($userEvent)
            ->causedBy($user)
            ->withProperties([
              'attributes' => [
                'title' => $event->nombre_evento
              ]
            ])
            ->log('activitylog.event.invitation_user');
          }
        }
        DB::commit();
        return response()->json(['success' => true, 'message' => $message]);
      } catch (\Exception $e) {
        DB::rollback();
        Log::error(sprintf('%s:%s', 'UsuarioEventoController:store', $e->getMessage()));
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
    public function destroy(Request $request, $id)
    {
        if($request->ajax()){
          if(!is_numeric($id)){
            return response()->json(['success' => false, 'error' => 'El dato seleccionado no corresponde con la base de datos, verifique.']);
          }

          $sessionAssits = AsistenciaSesion::where('codigo_usuario_evento', $id)->first();
          if($sessionAssits instanceof AsistenciaSesion){
            return response()->json(['success' => false, 'error' => 'No se puede remover la invitación porque la persona tiene una asistencia registrada en este evento, verifique.']);
          }

          $userEvent = UsuarioEvento::find($id);
          if(!$userEvent instanceof UsuarioEvento){
            abort(404);
          }

          DB::beginTransaction();
          try {
            $userEvent->delete();

            // Commit Transaction
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Se ha removido la invitación correctamente.']);

          }catch(\Exception $e){
            DB::rollback();
            Log::error(sprintf('%s:%s', 'UsuarioEventoController:destroy', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
          }
		  }
		  abort(404);
    }
}
