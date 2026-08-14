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
    App\Models\Core\TipoAdjunto,
    App\Models\Seguridad\Usuario,
    App\Models\Core\Estado,
    App\RegistroSesion,
    App\Models\Param\Documento,
    App\Models\Param\Institucion,
    App\RegistroFaseUsuario,
    Custom\Innpulsa,
    stdClass;

class AdjuntoProyectoController extends Controller {

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
			return Response::json(AdjuntoUsuario::getAttachedUser(Request::get('codigo_tipo_adjunto')));
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
	public function store()
	{
		if (Request::ajax()) {
			$data = Input::all();
			// Decrypt src-params
			$codigo_usuario = (int) Input::get('codigo_usuario');
			if(!$codigo_usuario || !is_numeric($codigo_usuario)) {
				abort(403);
			}

			$user = Usuario::find($codigo_usuario);
			if(!$user instanceof Usuario) {
	            abort(404);
			}

			$attachedUser = new AdjuntoUsuario;
			if ($attachedUser->isValid($data)) {
				DB::beginTransaction();
	        	try {

				$codigo_documento = (int) $data['codigo_documento'];

				// Evaluate if unique file already exists
				$query = Documento::query();
				$query->select(
						'documento.codigo_documento', 
						DB::raw('(CASE WHEN adjunto_usuario.codigo_adjunto IS NULL THEN false ELSE true END) as file'
					));
				$query->join('param.institucion_documento', 'institucion_documento.codigo_documento', '=', 'documento.codigo_documento');
				$query->leftJoin('core.adjunto_usuario', function($join) use($user) {
					$join->on('adjunto_usuario.codigo_documento', '=', 'documento.codigo_documento')
						->on('adjunto_usuario.codigo_institucion', '=', 'institucion_documento.codigo_institucion')
						->where('adjunto_usuario.codigo_usuario', '=', $user->codigo_usuario)
						->where('adjunto_usuario.codigo_estado', '=', Estado::getState('active'));
				});
				$query->where('institucion_documento.codigo_institucion',  session('institution')->codigo_institucion );
				$query->where('institucion_documento.codigo_estado', Estado::getState('active'));
				$query->where('documento.codigo_documento', $codigo_documento );
				$query->where('institucion_documento.documento_unico', true);
				$document = $query->get();

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

						//Adjunto
			   			$attached = new Adjunto();
						$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('gallery');
			   			$attached->direccion_adjunto = Request::get('direccion_adjunto');
						$attached->codigo_privacidad_contenido =  Input::get('codigo_privacidad_contenido');
                        $attached->fecha_adjunto = date('Y-m-d H:i:s');
						$attached->save();

	        		}elseif (Request::hasFile('file')) {
						$file = Input::file('file');
						$result = Adjunto::upload($file->getRealPath(), Input::get('qqfilename'), 'document');
						if(is_object($result) && $result->success === true) {
				   			// Adjunto
				   			$attached = new Adjunto();
							$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('document');
				   			$attached->direccion_adjunto = $result->file_path;
							$attached->nombre_adjunto = Input::get('qqfilename');
                            $attached->fecha_adjunto = date('Y-m-d H:i:s');
				   			$attached->save();
						}
					}



					if($attached instanceof Adjunto) {

						// Adjunto Usuario
						$attachedUser->fill($data);
						$attachedUser->codigo_adjunto = $attached->codigo_adjunto;
						$attachedUser->codigo_usuario = $user->codigo_usuario;
						$attachedUser->codigo_institucion = session('institution')->codigo_institucion;
						$attachedUser->codigo_estado = Estado::getState('active');
						$attachedUser->codigo_privacidad_contenido = Input::get('codigo_privacidad_contenido');
						$attachedUser->fecha_adjunto_usuario = date('Y-m-d');
	   					$attachedUser->hora_adjunto_usuario = date('H:i:s');
			   			$attachedUser->save();

			   			// Prepare response
						$response = AdjuntoUsuario::buildAttached(AdjuntoUsuario::getDataAttached($attachedUser->codigo_adjunto, $attachedUser->codigo_usuario));
						$response->success = true;

						// Evaluate message required documents
						$query = Documento::query();
						$query->select('documento.codigo_documento', DB::raw('(CASE WHEN adjunto_usuario.codigo_adjunto IS NULL THEN false ELSE true END) as file'));
						$query->join('param.institucion_documento', 'institucion_documento.codigo_documento', '=', 'documento.codigo_documento');
						$query->leftJoin('core.adjunto_usuario', function($join) use($user) {
							$join->on('adjunto_usuario.codigo_documento', '=', 'documento.codigo_documento')
								->on('adjunto_usuario.codigo_institucion', '=', 'institucion_documento.codigo_institucion')
								->where('adjunto_usuario.codigo_usuario', '=', $user->codigo_usuario)
								->where('adjunto_usuario.codigo_estado', '=', Estado::getState('active'));
						});
						$query->where('institucion_documento.codigo_institucion', $attachedUser->codigo_institucion);
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

						$totalDocuments = AdjuntoUsuario::query();
						$totalDocuments->where('codigo_usuario', $user->codigo_usuario);
						$totalDocuments->whereIn('codigo_documento', [
																		Documento::getTypeDocument('rut'),
																		Documento::getTypeDocument('photocopy'),
																		Documento::getTypeDocument('chamberCommerce')
																	]);
						//if ($totalDocuments->count() === 3 ) {
							//Innpulsa::sendEmailsDocumentsToResponsables($codigo_usuario);
						//}

						//Log
						$user = Usuario::find(\Auth::user()->codigo_usuario);
						$log = 'activitylog.archives.add';
						activity()
							->performedOn($user)
							->withProperties([
									'attributes' => [
										'project' => Input::get('name_project')
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
					Log::error(sprintf('%s:%s', 'AdjuntoUsuarioController:store', $e->getMessage()));
					return Response::json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return Response::json(['success' => false, 'errors' => $attachedUser->errors]);
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
	{
		// if (Request::ajax()) {
			DB::beginTransaction();
			try {
				if(!Request::has('codigo_usuario')) {
					throw new \Exception("Error recuperando codigo de usuario");
				}

				// Decrypt src-params
				list($codigo_usuario) = TuProyecto::decryptSrcParams(Input::get('codigo_usuario'));
				if(!$codigo_usuario || !is_numeric($codigo_usuario)) {
					abort(403);
				}

				$user = Usuario::find($codigo_usuario);
				if(!$user instanceof Usuario) {
		            abort(404);
				}

				// Validate users update attach
				if(in_array(Auth::user()->codigo_usuario, $user->getAllowedUsers())) {

					// Decrypt src-params
					list($codigo_adjunto) = TuProyecto::decryptSrcParams($id);
					if(!$codigo_adjunto || !is_numeric($codigo_adjunto)) {
						abort(403);
					}

					$attached = Adjunto::find($codigo_adjunto);
					if(!$attached instanceof Adjunto) {
						abort(404);
					}

					$attached->direccion_adjunto = Request::get('direccion_adjunto');
					$attached->save();

					DB::commit();
					return Response::json(['success' => true]);
				}

				DB::rollback();
				abort(403);
			}catch(\Exception $e){
				DB::rollback();
				Log::error($e->getMessage());
				return Response::json(['success' => false, 'errors' => trans('app.exception')]);
			}
		// }
    	// abort(404);
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
			
				$codigo_usuario = (int) Input::get('codigo_usuario');
				if(!$codigo_usuario || !is_numeric($codigo_usuario)) {
					abort(403);
				}

				$user = Usuario::find($codigo_usuario);
				if(!$user instanceof Usuario) {
		            abort(404);
				}

				// Validate users update attach
				//if(in_array(Auth::user()->codigo_usuario, $user->getAllowedUsers())) {
					// Decrypt src-params
					list($codigo_adjunto) = TuProyecto::decryptSrcParams($id);
					if(!$codigo_adjunto || !is_numeric($codigo_adjunto)) {
						abort(403);
					}

					$attachedUser = AdjuntoUsuario::getDataAttached($codigo_adjunto, $user->codigo_usuario);
					if(!$attachedUser instanceof AdjuntoUsuario) {
						abort(404);
					}

					// Set removed stated
					AdjuntoUsuario::where('codigo_adjunto', '=', $attachedUser->codigo_adjunto)->where('codigo_usuario', '=', $attachedUser->codigo_usuario)
						->update(['codigo_estado' => Estado::getState('removed')]);

						//Log
					$user = Usuario::find(\Auth::user()->codigo_usuario);
					$log = 'activitylog.archives.delete';
					activity()
						->performedOn($user)
						->withProperties([
								'attributes' => [
									'project' => Input::get('name_project')
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
