<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Request,
    Response,
    Auth,
    Input,
    App,
    DB,
    Log,
    Lang,
    TuProyecto,
    SendEmail,
    App\Models\Core\Adjunto,
    App\Models\Core\AdjuntoUsuario,
	App\Models\Core\AdjuntoEvento,
    App\Models\Core\TipoAdjunto,
    App\Models\Seguridad\Usuario,
    App\Models\Core\Estado,
    App\Models\Core\Evento,
    App\RegistroSesion,
    App\Models\Param\Documento,
    App\Models\Param\Institucion,
    App\RegistroFaseUsuario,
    Custom\Innpulsa,
    stdClass,
	App\Http\Requests\StoreArchivesAttendance;

class AdjuntoEventoController extends Controller {

	/**
     * Instantiate a new AdjuntoUsuarioController instance.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (Request::ajax()) {
			return Response::json(AdjuntoEvento::getAttachedUser(Request::get('codigo_tipo_adjunto')));
			//dd(AdjuntoEvento::getAttachedUser(Request::get('codigo_tipo_adjunto')));
		}
		abort(404);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(StoreArchivesAttendance $request)
	{
		$validated = $request->validated();

		if (Request::ajax()) {
			$data = Input::all();
			// Decrypt src-params
			$codigo_evento = (int) Input::get('codigo_evento');
			if(!$codigo_evento || !is_numeric($codigo_evento)) {
				abort(403);
			}

			$evento = Evento::find($codigo_evento);
			if(!$evento instanceof Evento) {
	            abort(404);
			}

			DB::beginTransaction();
			try {

				$codigo_documento = 16; // Asistencia a evento
				$codigo_privacidad_contenido = 2; // publico

				// Evaluate if unique file already exists
				$query = Documento::query();
				$query->select(
						'documento.codigo_documento', 
						DB::raw('(CASE WHEN adjunto_evento.codigo_adjunto IS NULL THEN false ELSE true END) as file'
					));
				$query->join('param.institucion_documento', 'institucion_documento.codigo_documento', '=', 'documento.codigo_documento');
				$query->leftJoin('core.adjunto_evento', function($join) use($evento) {
					$join->on('adjunto_evento.codigo_documento', '=', 'documento.codigo_documento')
						->on('adjunto_evento.codigo_institucion', '=', 'institucion_documento.codigo_institucion')
						->where('adjunto_evento.codigo_evento', '=', $evento->codigo_evento)
						->where('adjunto_evento.codigo_estado', '=', Estado::getState('active'));
				});
				$query->where('institucion_documento.codigo_institucion',  session('institution')->codigo_institucion );
				$query->where('institucion_documento.codigo_estado', Estado::getState('active'));
				$query->where('documento.codigo_documento', $codigo_documento );
				$query->where('institucion_documento.documento_unico', true);
				$document = $query->get();
				//dd($document->where('file', true)->count(), $codigo_documento);
				//dd(Input::get('codigo_privacidad_contenido'), gettype(Input::get('codigo_privacidad_contenido')));
				if( $document->count() > 0 &&  $document->where('file', true)->count() > 0 ) {
							$response = new stdClass();
							DB::rollback();
							$errmsg = "No se puede volver a cargar este documento";
							Log::error(sprintf('%s:%s', 'AdjuntoUsuarioController:store', $errmsg ));
							return Response::json(['success' => false, 'msg' =>  $errmsg,'errors' => $errmsg]);
				}

				$attached = null;
				//	dd(Input::file('file')->getClientOriginalName());

				if(Request::has('codigo_tipo_adjunto') && Request::get('codigo_tipo_adjunto') == TipoAdjunto::getTypeAttached('gallery')) {
				
					//Cargar el Adjunto
					$attached = new Adjunto();
					$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('gallery');
					$attached->direccion_adjunto = Request::get('direccion_adjunto');
					$attached->codigo_privacidad_contenido =  $codigo_privacidad_contenido;
					$attached->fecha_adjunto = date('Y-m-d H:i:s');
					$attached->save();

				}elseif (Request::hasFile('file')) {
					$file = Input::file('file');
					$result = Adjunto::upload($file->getRealPath(), Input::get('qqfilename'), 'document');
					if(is_object($result) && $result->success === true) {
						// Cargar el Adjunto
						$attached = new Adjunto();
						$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('document');
						$attached->direccion_adjunto = $result->file_path;
						$attached->nombre_adjunto = Input::get('qqfilename');
						$attached->fecha_adjunto = date('Y-m-d H:i:s');
						$attached->save();
					}
				}



				if($attached instanceof Adjunto) {
					$data['codigo_documento'] = $codigo_documento;
					// Adjunto Evento
					$attachedEvent = new AdjuntoEvento;
					$attachedEvent->fill($data);
					$attachedEvent->codigo_adjunto = $attached->codigo_adjunto;
					$attachedEvent->codigo_evento = $evento->codigo_evento;
					$attachedEvent->codigo_institucion = session('institution')->codigo_institucion;
					$attachedEvent->codigo_estado = Estado::getState('active');
					$attachedEvent->codigo_privacidad_contenido = $codigo_privacidad_contenido;
					$attachedEvent->fecha_adjunto_evento = date('Y-m-d');
					$attachedEvent->hora_adjunto_evento = date('H:i:s');
					$attachedEvent->save();

					// Prepare response
					$response = AdjuntoEvento::buildAttached(AdjuntoEvento::getDataAttached($attachedEvent->codigo_adjunto, $attachedEvent->codigo_evento));
					$response->success = true;

					// Evaluate message required documents
					$query = Documento::query();
					$query->select(
						'documento.codigo_documento',
						DB::raw('(CASE WHEN adjunto_evento.codigo_adjunto IS NULL THEN false ELSE true END) as file')
					);
					$query->join('param.institucion_documento', 'institucion_documento.codigo_documento', '=', 'documento.codigo_documento');
					$query->leftJoin('core.adjunto_evento', function($join) use($evento) {
						$join->on('adjunto_evento.codigo_documento', '=', 'documento.codigo_documento')
							->on('adjunto_evento.codigo_institucion', '=', 'institucion_documento.codigo_institucion')
							->where('adjunto_evento.codigo_evento', '=', $evento->codigo_evento)
							->where('adjunto_evento.codigo_estado', '=', Estado::getState('active'));
					});
					$query->where('institucion_documento.codigo_institucion', $attachedEvent->codigo_institucion);
					$query->where('institucion_documento.clasificacion_documento_institucion', Documento::getClass('entity'));
					$query->where('institucion_documento.codigo_estado', Estado::getState('active'));
					$query->where('institucion_documento.documento_requerido', true);
					$required = $query->get();

					if( $required->count() > 0 && ($required->count() == $required->where('file', true)->count()) ) {
						// Other actions institution
						switch (session('institution')->codigo_institucion) {
							case Institucion::institution('innpulsa'):
								$response->msg = 'Has cargado con éxito los documentos requeridos.';
								//Innpulsa::sendEmailLinkingDocuments($user);
							break;
						}
					}

					//Log
					$user = Usuario::find(\Auth::user()->codigo_usuario);
					$log = 'activitylog.attendance.add';
					activity()
						->performedOn($user)
						->withProperties([
								'attributes' => [
									'event' => $evento->nombre_evento
								]
						])
						->log($log);

					// Commit Transaction
					DB::commit();
					return Response::json($response);
				}

				DB::rollback();
				return Response::json(['success' => false, 'errors' => trans('app.exception')]);
			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'AdjuntoEventoController:store', $e->getMessage()));
				return Response::json(['success' => false, 'errors' => trans('app.exception')]);
			}
			return Response::json(['success' => false, 'errors' => $attachedEvent->errors]);
		}
    	abort(404);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{dd('en update');

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (Request::ajax()) {
			DB::beginTransaction();
			try {
			
				$codigo_evento = (int) Input::get('codigo_evento');
				if(!$codigo_evento || !is_numeric($codigo_evento)) {
					abort(403);
				}

				$evento = Evento::find($codigo_evento);
				if(!$evento instanceof Evento) {
		            abort(404);
				}

				// Validate users update attach
				//if(in_array(Auth::user()->codigo_usuario, $user->getAllowedUsers())) {
					// Decrypt src-params
					list($codigo_adjunto) = TuProyecto::decryptSrcParams($id);
					if(!$codigo_adjunto || !is_numeric($codigo_adjunto)) {
						abort(403);
					}

					$attachedEvent = AdjuntoEvento::getDataAttached($codigo_adjunto, $evento->codigo_evento);
					if(!$attachedEvent instanceof AdjuntoEvento) {
						abort(404);
					}

					// Set removed stated
					AdjuntoEvento::where('codigo_adjunto', '=', $attachedEvent->codigo_adjunto)
									->where('codigo_evento', '=', $attachedEvent->codigo_evento)
						->update(['codigo_estado' => Estado::getState('removed')]);

						//Log
					$user = Usuario::find(\Auth::user()->codigo_usuario);
					$log = 'activitylog.attendance.delete';
					activity()
						->performedOn($user)
						->withProperties([
								'attributes' => [
									'event' => $evento->nombre_evento
								]
						])
						->log($log);
						
					DB::commit();
					return Response::json(array('success' => true, 'msg' => 'Archivo borrado'));
				//}
				DB::rollback();
				abort(403);
			}catch(\Exception $e){
				DB::rollback();
				Log::error($e->getMessage());
				return Response::json(array('success' => false, 'errors' => trans('app.exception')));
			}
		}
    	abort(404);
	}


}
