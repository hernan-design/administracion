<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Datatables, Log, DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Seguridad\Rol,
    App\Models\Seguridad\Permiso,
    App\Models\Seguridad\RolInstitucion,
    App\Models\Core\Estado,
    App\Models\Seguridad\Usuario;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return Datatables::of( Rol::getRoles( $request->all() ) )->make(true);
        }

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('roles.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create-edit');
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
			$role = new Rol();
			if($role->isValid($data)){
				DB::beginTransaction();
				try {
                    if(!$request->has('tipo_rol')) {
                        throw new \Exception("Tipo rol no identificado.");
                    }

					$role->title = $request->title;
					$role->description = $request->description;
					$role->codigo_estado = Estado::getState('active');
					$role->name = slugtp($request->title, $role->getTable(), 'name');
					$role->tipo_rol = Rol::getTypeRole($request->tipo_rol);
					$role->save();

                    switch ($role->tipo_rol) {
                        case Rol::getTypeRole('administrator'):
                            $role->attachPermission(Permiso::getPermission('login-admin'));
                        break;
                    }

					$institutionRole = new RolInstitucion();
					$institutionRole->role_id = $role->id;
					$institutionRole->codigo_institucion = session('institution')->codigo_institucion;
					$institutionRole->codigo_estado = Estado::getState('active');
					$institutionRole->save();

					// Commit Transaction
					DB::commit();
					return response()->json(['success' => true, 'message' => 'El rol ha sido creado correctamente!!', 'id' => $role->id, 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'RolController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $role->errors]);
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {   
        \Artisan::queue('reports:rolsmodule', [
            'user' => \Auth::user()->codigo_usuario,
            'institution' => session('institution')->codigo_institucion,
            'filters' => $request->all()
        ]);

        $user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando Usuario módulo roles ".\Auth::user()->codigo_usuario);
		}

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => 'módulo roles'
				]
			])
		->log('activitylog.reports');

        return redirect()->route('rol.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if(!is_numeric($id)){
			abort(404);
		}

		$role = Rol::getRole($id);
		if(!$role instanceof Rol){
			abort(404);
		}

		$admin = $role->id == Rol::getRol('admin') ? true : false;
		return view('roles.create-edit', compact('role', 'admin'));
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

			$role = Rol::find($id);
			if(!$role instanceof Rol){
				throw new \Exception("Rol no encontrado {$id}");
			}

			$institutionRole = RolInstitucion::where('role_id', $id)->where('codigo_institucion', session('institution')->codigo_institucion)->first();
            if(!$institutionRole instanceof RolInstitucion){
                throw new \Exception("Rol institución no encontrado {$id}");
            }

			DB::beginTransaction();
			try {
				if ( !$request->has('change_state') ) {
					if($role->isValid($data)){
						$role->title = $request->title;
						$role->description = $request->description;
						$role->save();
					}else {
						return response()->json(['success' => false, 'errors' => $role->errors]);
					}
				}

				$codigo_estado = $request->has('change_state') ? $institutionRole->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $institutionRole->codigo_estado;

				$institutionRole->codigo_estado = $codigo_estado;
				$institutionRole->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El rol ha sido editado correctamente!!', 'id' => $role->id, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'RolController:update', $e->getMessage()));
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
        //
    }
}
