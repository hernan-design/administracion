<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, DB;

use App\Models\Core\UsuarioEvento, App\Models\Core\Evento, App\Models\Seguridad\Usuario, App\Models\Core\AsistenciaSesion, App\Models\Core\Estado, App\Models\Core\EntregaBeneficio;

class ControlAsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
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

			DB::beginTransaction();
			try {
					if (isset($request->dataAssist) && !empty($request->dataAssist)) {
						foreach ($request->dataAssist as $value) {
							$request->codigo_usuario_evento = $value;

							$userEvent = UsuarioEvento::find($request->codigo_usuario_evento);
							if(!$userEvent instanceof UsuarioEvento){
								abort(404);
							}
							$event = Evento::find($userEvent->codigo_evento);
							if(!$event instanceof Evento){
								abort(404);
							}
							$user = Usuario::select('usuario.codigo_usuario', 'nombre_usuario')
										->join('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
										->where('usuario_institucion.codigo_usuario_institucion', $userEvent->codigo_usuario_institucion)
										->first();
							if(!$user instanceof Usuario){
								abort(404);
							}

							$asistenciaSesion = AsistenciaSesion::where('codigo_usuario_evento', $userEvent->codigo_usuario_evento)
																->get();

							if(count($asistenciaSesion) == 0){

								$sessionAssits = new AsistenciaSesion;
								$sessionAssits->codigo_sesion_evento = $request->codigo_sesion_evento;
								$sessionAssits->codigo_usuario_evento = $userEvent->codigo_usuario_evento;
								$sessionAssits->codigo_estado = Estado::getState('active');
								$sessionAssits->save();

								if($request->turn){
									$message = 'Se ha registrado la asistencia correctamente!!';
									$log = 'activitylog.event.attended';
								}

								activity()
								->performedOn($userEvent)
								->withProperties([
									'attributes' => [
										'title' => $event->nombre_evento,
										'user' => $user->nombre_usuario,
										'user_id' => $user->codigo_usuario
									] ])
								->log($log);

								activity()
								->performedOn($userEvent)
								->causedBy($user)
								->withProperties([
									'attributes' => [
										'title' => $event->nombre_evento,
									] ])
								->log('activitylog.event.attended_user');

							}
						}

						// Commit Transaction
						DB::commit();
					} else {

						$userEvent = UsuarioEvento::find($request->codigo_usuario_evento);
						if(!$userEvent instanceof UsuarioEvento){
							abort(404);
						}
			
						$event = Evento::find($userEvent->codigo_evento);
						if(!$event instanceof Evento){
							abort(404);
						}
			
						$user = Usuario::select('usuario.codigo_usuario', 'nombre_usuario')
							->join('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
							->where('usuario_institucion.codigo_usuario_institucion', $userEvent->codigo_usuario_institucion)->first();
						if(!$user instanceof Usuario){
							abort(404);
						}

						$sessionAssits = new AsistenciaSesion;
						$sessionAssits->codigo_sesion_evento = $request->codigo_sesion_evento;
						$sessionAssits->codigo_usuario_evento = $userEvent->codigo_usuario_evento;
						$sessionAssits->codigo_estado = Estado::getState('active');
						$sessionAssits->save();

						if($request->turn){
							$message = 'Se ha registrado la asistencia correctamente!!';
							$log = 'activitylog.event.attended';
						}

						activity()
							->performedOn($userEvent)
							->withProperties([
								'attributes' => [
									'title' => $event->nombre_evento,
									'user' => $user->nombre_usuario,
									'user_id' => $user->codigo_usuario
								] ])
							->log($log);

							activity()
							->performedOn($userEvent)
							->causedBy($user)
							->withProperties([
								'attributes' => [
									'title' => $event->nombre_evento,
								] ])
							->log('activitylog.event.attended_user');

						// Commit Transaction
						DB::commit();
					}
					
					return response()->json(['success' => true, 'message' => $message]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ControlAsistenciaController:store', $e->getMessage()));
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
			DB::beginTransaction();
			try {
				$id = explode(',', $id);
				if (count($id) > 1) {
					foreach ($id as $value) {
						if(!is_numeric($value)){
							return response()->json(['success' => false, 'errors' => 'El dato seleccionado no corresponde con la base de datos, verifique.']);
						}
	
						$sessionAssits = AsistenciaSesion::find($value);
						if(!$sessionAssits instanceof AsistenciaSesion){
							abort(404);
						}
	
						$userEvent = UsuarioEvento::find($sessionAssits->codigo_usuario_evento);
						if(!$userEvent instanceof UsuarioEvento){
							abort(404);
						}
	
						$event = Evento::find($userEvent->codigo_evento);
						if(!$event instanceof Evento){
							abort(404);
						}
	
						$user = Usuario::select('usuario.codigo_usuario', 'nombre_usuario')
							->join('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
							->where('usuario_institucion.codigo_usuario_institucion', $userEvent->codigo_usuario_institucion)->first();
						if(!$user instanceof Usuario){
							abort(404);
						}
	
						$sessionAssits->benefitsAssist()->delete();
						$sessionAssits->delete();
	
						activity()
							->performedOn($userEvent)
							->withProperties([
								'attributes' => [
									'title' => $event->nombre_evento,
									'user' => $user->nombre_usuario,
									'user_id' => $user->codigo_usuario
								] ])
							->log('activitylog.event.removed');
					}
					// Commit Transaction
					DB::commit();

				} else {
					if(!is_numeric($id[0])){
						return response()->json(['success' => false, 'errors' => 'El dato seleccionado no corresponde con la base de datos, verifique.']);
					}

					$sessionAssits = AsistenciaSesion::find($id[0]);
					if(!$sessionAssits instanceof AsistenciaSesion){
						abort(404);
					}

					$userEvent = UsuarioEvento::find($sessionAssits->codigo_usuario_evento);
					if(!$userEvent instanceof UsuarioEvento){
						abort(404);
					}

					$event = Evento::find($userEvent->codigo_evento);
					if(!$event instanceof Evento){
						abort(404);
					}

					$user = Usuario::select('usuario.codigo_usuario', 'nombre_usuario')
						->join('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
						->where('usuario_institucion.codigo_usuario_institucion', $userEvent->codigo_usuario_institucion)->first();
					if(!$user instanceof Usuario){
						abort(404);
					}

					$sessionAssits->benefitsAssist()->delete();
					$sessionAssits->delete();

					activity()
						->performedOn($userEvent)
						->withProperties([
							'attributes' => [
								'title' => $event->nombre_evento,
								'user' => $user->nombre_usuario,
								'user_id' => $user->codigo_usuario
							] ])
						->log('activitylog.event.removed');

					// Commit Transaction
					DB::commit();
				}
				return response()->json(['success' => true, 'message' => 'Se ha removido la asistencia correctamente.']);
			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ControlAsistenciaController:destroy', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
}
