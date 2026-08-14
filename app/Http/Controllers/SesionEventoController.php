<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use App\Models\Core\Evento, App\Models\Core\SesionEvento, App\Models\Core\Estado;

class SesionEventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if ($request->ajax()) {
            if($request->has('event')) {
                $event = Evento::find($request->event);
                if(!$event instanceof Evento) {
                    abort(404);
                }
                return response()->json(SesionEvento::getSesionEvent( $request->all() ));
            }

            if($request->has('data')){
                $event = Evento::find($request->data);
                if(!$event instanceof Evento) {
                    abort(404);
                }
                return response()->json( SesionEvento::sessionsEvent( $request->all() ) );
            }
        }
        abort(404);
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
			$session_number = 1;

			$sessionNumber = SesionEvento::getMaxSesion( $request->all() );
			if(isset($sessionNumber)){
				$session_number = $sessionNumber + 1;
            }
            
            $session = new SesionEvento;
			if ($session->isValid($data)) {
				DB::beginTransaction();
	            try {

					$session->fill($data);
					$session->codigo_estado = Estado::getState('active');
					$session->numero_sesion = $session_number;
                    $session->save();
                    
                    $response = $session;
                    $response->success = true;
                    $response->message = 'La sesión ha sido creada correctamente.';

                    // Commit Transaction
                    DB::commit();
                    return response()->json($response);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'SesionEventoController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $session->errors]);            
        }
        abort(404);
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

			$session = SesionEvento::find($id);
			if(!$session instanceof SesionEvento ){
				throw new \Exception("Sesión no encontrada {$id}");
			}

			if ($session->isValid($data)) {
				DB::beginTransaction();
	            try {
					if($request->has('change_state')){
						$session->codigo_estado = $request->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');

						$event = $session->codigo_estado == Estado::getState('active') ? 'activada' : 'inactivada';
					}else{
						$session->fill($data);
						$event = 'editada';
					}

                    $session->save();
                    
                    $response = $session;
                    $response->success = true;
                    $response->message = trans('app.rest.success', ['resource' => 'La sesión', 'event' => $event]);

                    // Commit Transaction
                    DB::commit();
                    return response()->json($response);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'SesionEventoController:update', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $session->errors]);
		}
		abort(404);
    }

    /**
     * Order all resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function order(Request $request)
	{
		if($request->ajax()){
			DB::beginTransaction();
			try {

				foreach ($request->data as $value) {
					$session = SesionEvento::find($value['codigo_sesion_evento']);

					if(!$session instanceof SesionEvento){
						return response()->json(['success' => false, 'errors' => 'La sesión no se encuentra en el sistema, verifique.']);
					}

					$session->numero_sesion = $value['numero_sesion'];
					$session->save();
				}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha cambiado la posición de la sesión correctamente.']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'SesionEventoController:order', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
	}
}
