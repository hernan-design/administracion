<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Log;
use App\Models\Seguridad\Usuario,
	App\Models\Seguridad\RolUsuario,
	App\Models\Seguridad\Rol,
	App\Models\Core\UsuarioInstitucion,
	App\Models\Param\TerminosCondiciones,
	App\Models\Seguridad\AceptacionTerminos,
	App\Models\Core\Estado;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Bita\LogActivity;

class RolUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			return RolUsuario::getRolesByUser($request->user);
		}
		abort(404);
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if($request->ajax()){

			DB::beginTransaction();
			try {
				$role = Rol::getRole($request->data['option_id']);
				if(!$role instanceof Rol){
					throw new \Exception("Rol no encontrado");
				}

				$user = Usuario::find($request->data['data_id']);
				if(!$user instanceof Usuario){
					throw new \Exception("Usuario no encontrado");
				}

				$user->attachRole($role->id);

				activity()
					->performedOn($user)
					->withProperties([
						'attributes' => [
							'title' => $role->title,
							'user' => $user->nombre_usuario,
							'user_id' => $user->codigo_usuario
						] ])
					->log('activitylog.user.role.created');

				if($role->id == Rol::getRol('mentor'))
				{
					// Ensure usuario_institucion exists
					$userInstitution = UsuarioInstitucion::where('codigo_usuario', $user->codigo_usuario)
						->where('codigo_institucion', session('institution')->codigo_institucion)
						->first();

					if(!$userInstitution instanceof UsuarioInstitucion) {
						$userInstitution = new UsuarioInstitucion();
						$userInstitution->codigo_usuario = $user->codigo_usuario;
						$userInstitution->codigo_institucion = session('institution')->codigo_institucion;
						$userInstitution->codigo_estado = Estado::getState('active');
						$userInstitution->save();
					}

					// Ensure mentoring terms acceptance exists
					$terms = TerminosCondiciones::termsAndConditions(TerminosCondiciones::getTypeTerms('mentoring'));
					if($terms instanceof TerminosCondiciones && $userInstitution instanceof UsuarioInstitucion) {
						$acceptanceTerms = AceptacionTerminos::where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
							->where('codigo_version_terminos', $terms->codigo_version_terminos)
							->first();
						if(!$acceptanceTerms instanceof AceptacionTerminos) {
							$acceptanceTerms = new AceptacionTerminos();
							$acceptanceTerms->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
							$acceptanceTerms->codigo_version_terminos = $terms->codigo_version_terminos;
							$acceptanceTerms->codigo_estado = Estado::getState('active');
							$acceptanceTerms->fecha_aceptacion_terminos = date('Y-m-d H:i:s');
							$acceptanceTerms->save();
						}
					}

					$user->sync_algolia = 1;
					$user->save();
					$user->syncIndex();
				}
				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha asignado el rol  correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'RolUsuarioController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
		if($request->ajax()){
			$role = Rol::getRole($id);
			if(!$role instanceof Rol){
				throw new \Exception("Rol no encontrado");
			}

			$user = Usuario::find($request->data['data_id']);
			if(!$user instanceof Usuario){
				throw new \Exception("Usuario no encontrado");
			}

			DB::beginTransaction();
			try {
				$user->detachRole($role->id);

				activity()
					->performedOn($user)
					->withProperties([
						'attributes' => [
							'title' => $role->title,
							'user' => $user->nombre_usuario,
							'user_id' => $user->codigo_usuario
						] ])
					->log('activitylog.user.role.deleted');

					if($role->id == Rol::getRol('mentor'))
					{
						$user->sync_algolia = 3;
						$user->save();
						$user->desyncIndex();

						$user->sync_algolia = 1;
						$user->save();
						$user->syncIndex();

					}

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha removido el rol correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'RolUsuarioController:destroy', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
}
