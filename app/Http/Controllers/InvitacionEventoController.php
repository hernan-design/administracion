<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Datatables;

use App\Models\Seguridad\Usuario;

class InvitacionEventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return Datatables::of( Usuario::usersList( $request->all() ) )->make(true);
		}
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
					$event->save();

					$eventInstitucion = new EventoInstitucion;
					$eventInstitucion->codigo_evento = $event->codigo_evento;
					$eventInstitucion->codigo_institucion = session('institution')->codigo_institucion;
					$eventInstitucion->codigo_tipo_evento_calendario = $request->codigo_tipo_evento_calendario;
					$eventInstitucion->codigo_estado = Estado::getState('active');
					$eventInstitucion->save();

					// Commit Transaction
					DB::commit();
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
}
