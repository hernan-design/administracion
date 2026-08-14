<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Auth, Artisan, DB, Input, Request, Response, stdClass, Log, TuProyecto, Storage;

use App\Models\Core\{Adjunto, AdjuntoUsuario,
	Estado, TipoAdjunto};

use App\Models\Seguridad\Usuario;

use App\Models\Param\Documento;
use App\Imports\Compensar\LoadUsersEntities;

class CargaExcelEntidadesController extends Controller {

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
		// if (Request::ajax()) {
		// 	return Response::json(AdjuntoUsuario::getAttachedUser(Request::get('codigo_tipo_adjunto'), Request::get('codigo_documento')));
		// }
		// abort(404);
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
	 * Guarda archivo del cargue masivo.
	 *
	 * @access public
	 * @param Request
	 * @return Response
	 */
	public function store()
	{
		if (Request::ajax()) {
			$data = Input::all();

			// Desencripta codigo_usuario.
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
					if (Request::hasFile('file')) {
						$file = Input::file('file');
						//Establece el nombre del archivo.
						switch (session('institution')->codigo_institucion) {
							case 75 or 51 or 30:
								$file_name = 'cargue_usuarios_entidades_' . session('institution')->slug_institucion. '.xlsx';
								break;
							default:
								$file_name = Input::get('qqfilename');
								break;
						}

						// Prepare response
						$response = new \stdClass();
						$response->success = true;

						// Guarda el archivo en la carpeta 'storage/nombre_institucion'
						Storage::disk('local')->putFileAs('compensar', $file, $file_name);

						//Selecciona rutina
						$this->selectRountine();

						// Elimina el archivo de la carpeta storage
						Storage::disk('local')->delete(session('institution')->slug_institucion . '/' . $file_name);

						$response->msg = 'Has cargado con éxito la información.';

						$user = Usuario::find($codigo_usuario);
						activity()
							->performedOn($user)
							->withProperties([
								'attributes' => []
						    ])
							->log('activitylog.cargaexcel.usuarios_entidades.add');

						// Commit Transaction
						DB::commit();
						return Response::json($response);
					}

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
	public function selectRountine(): void
	{
		//Seleccionamos rutina por institucion.
		switch (session('institution')->codigo_institucion) {
			case 30:
				// Artisan::queue 'compensar:loadusersentities'
				LoadUsersEntities::upload();
			break;
		}

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
	 * Descarga txt con errores del excel.
	 */
	public function viewFileErrorsEntities()
	{

		$path = storage_path().'/'.'app'.'/' . session('institution')->slug_institucion . '/errores_cargue_entidades.txt';

		if (file_exists($path)) {
			return Response::download($path);
		} else {
			return redirect()->route('entidad.index');
		}
	}

}
