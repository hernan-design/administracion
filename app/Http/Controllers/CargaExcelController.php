<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

use Storage, Request, Response, Auth, Input, App, DB, Log, Lang, TuProyecto, SendEmail, stdClass;
use App\Models\Core\Adjunto,
    App\Models\Core\AdjuntoUsuario,
    App\Models\Core\TipoAdjunto,
    App\Models\Seguridad\Usuario,
    App\Models\Core\Estado,
    App\RegistroSesion,
    App\Models\Param\Documento,
    App\Models\Param\Institucion,
    App\RegistroFaseUsuario,
	App\Models\Core\SesionMasiva;

use App\Imports\Innpulsa\LoadUsersEntities,
	App\Imports\Compensar\LoadUsers,
	App\Imports\Propace\LoadUsersPropace;


class CargaExcelController extends Controller {

	/**
     * Instantiate a new CargaExcelController instance.
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
			return Response::json(AdjuntoUsuario::getAttachedUser(Request::get('codigo_tipo_adjunto'), Request::get('codigo_documento')));
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
	 * Almacena archivo para cargue masivo
	 * @return Response
	 */
	public function store()
	{	\Log::info('in store');
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
								Log::error(sprintf('%s:%s', 'CargaExcelController:store 115', $errmsg ));
								return Response::json(['success' => false, 'msg' =>  $errmsg,'errors' => $errmsg]);
					}

					$attached = null;

					if(Request::has('codigo_tipo_adjunto') && Request::get('codigo_tipo_adjunto') == TipoAdjunto::getTypeAttached('gallery')) {
						//Adjunto
						$attached = new Adjunto();
						$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('gallery');
						$attached->direccion_adjunto = Request::get('direccion_adjunto');
						$attached->codigo_privacidad_contenido =  Input::get('codigo_privacidad_contenido');
						$attached->fecha_adjunto = date('Y-m-d H:i:s');
						$attached->save();

					} elseif (Request::hasFile('file')) {
						$file = Input::file('file');

						//establecemos el nombre del archivo
						switch (session('institution')->codigo_institucion) {
							case 75 or 51 or 30:
								$file_name = 'cargue_usuarios_' . session('institution')->slug_institucion. '.xlsx';
								break;
							// case 51:
							// 	$file_name = 'cargue_usuarios_' . session('institution')->slug_institucion. '.xlsx';
							// 	break;
							default:
								$file_name = Input::get('qqfilename');
								break;
						}

						$result = Adjunto::upload($file->getRealPath(), $file_name, 'document');
						if(is_object($result) && $result->success === true) {
							// Adjunto
							$attached = new Adjunto();
							$attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('excel');
							$attached->direccion_adjunto = $result->file_path;
							$attached->nombre_adjunto = $file_name;
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

						//seleccionamos rutina
						$this->selectRountine($codigo_documento);

						$response->msg = 'Has cargado con éxito la información';

						//Log
						// $user = Usuario::find($codigo_usuario);
						activity()
							->performedOn($user)
							->withProperties([
								'attributes' => []
						    ])
							->log('activitylog.cargaexcel.usuarios.add');

						// Commit Transaction
						DB::commit();
						return Response::json($response);
					}

					DB::rollback();
					Log::error('No se pudo cargar el archivo');
					return Response::json(['success' => false, 'errors' => 'No se pudo cargar el archivo']);
				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'CargaExcelController:store', $e->getMessage()));
					return Response::json(['success' => false, 'errors' => 'No se pudo cargar el archivo, revisa el txt']);
				}
			}
			return Response::json(['success' => false, 'errors' => $attachedUser->errors]);
		}
    	abort(404);
	}

	/**
	 * permite seleccionar rutina
	 */
	public function selectRountine($codigo_documento) {
		Log::info('seleccionando rutina');
		$this->downloadExcell($codigo_documento);

		//seleccionamos rutina por institucion, por ahora será asi
		switch (session('institution')->codigo_institucion) {
			case 75:
				Log::info('Rutina de Propace seleccionada.');
				LoadUsersPropace::upload();
				break;
			case 51:
				Log::info('Innpulsa seleccionada');
				LoadUsersEntities::upload();
				break;
			case 30:
				Log::info('compensar seleccionada');
				LoadUsers::upload();
				break;
		}
		Log::info('Despues del switch.');
		return;

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
	{dd('uldate');
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
					AdjuntoUsuario::where('codigo_adjunto', '=', $attachedUser->codigo_adjunto)
							->where('codigo_usuario', '=', $attachedUser->codigo_usuario)
							->update(['codigo_estado' => Estado::getState('removed')]);

						//Log
					$user = Usuario::find($codigo_usuario);
					activity()
						->performedOn($user)
						->withProperties([
								'attributes' => []
						])
                        ->log('activitylog.cargaexcel.usuarios.delete');

                        $response = new stdClass();
                        $response->success = true;
						$response->msg = 'Archivo borrado.';

					DB::commit();
					return Response::json(array('success' => true, 'msg' => 'Archivo borrado.'));
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

	/**
     * Descarga del excel
     *
     */
    public function downloadExcell(int $codigo_documento)
    {
        Log::info('inicia descarga archivo');

        //Valida que documento va descargar.
        if ($codigo_documento == 17) { //Aplica para cargar usuarios (17)
            $nombre_archivo = 'cargue_usuarios_' . session('institution')->slug_institucion . '.xlsx';
        } elseif ($codigo_documento == 18) { //Aplica para cargar usuarios y entidades (18)
            $nombre_archivo = 'cargue_usuarios_entidades_' . session('institution')->slug_institucion . '.xlsx';
        }
        $type ='documents';
        $key = sprintf('%s/%d/%d/%s', $type, date('Y'), date('m'), $nombre_archivo);
        $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');
        $url = sprintf('https://s3.us-west-2.amazonaws.com/%s/%s', $bucket, $key);

        $ruta_archivo = session('institution')->slug_institucion . '/' . $nombre_archivo;
        try {
            $contents = file_get_contents($url);
            if ($contents === false) {
                throw new \Exception('Error al descargar el archivo desde S3');
            }
            Storage::disk('local')->put($ruta_archivo, $contents);
        } catch (\Exception $e) {
            $message = sprintf('%s/', $e->getMessage());
            Log::error($message);
            Log::error('Error al recuperar excel');
        }

        $path = '/storage/app/' . $ruta_archivo;

        // lectura del excel descargado
        // Excel::load($path, function($reader) {
        //     $results = $reader->get()->toArray();
        //     foreach($results as $key => $resul){
        //         Log::info($resul['entidad']);
                //user data

        //     }
        // });

        \Log::info("Finalizo descarga archivo");
    }

	/**
	 * descarga txt con errores del excel
	 */
	public function viewFileErrors()
	{

		$path = storage_path().'/'.'app'.'/'. session('institution')->slug_institucion .'/errores_archivo.txt';

		if (file_exists($path)) {
			return Response::download($path);
		}
	}

}
