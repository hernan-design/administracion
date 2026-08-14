<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth, DB, Log;

use App\Models\Seguridad\PermisoModulo, App\Models\Seguridad\Rol, App\Models\Seguridad\Permiso, App\Models\Seguridad\PermisoRol;

use App\Models\Bita\LogActivity;

class PermisoModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			if($request->has('assignment') && !empty($request->assignment)){
				return PermisoModulo::getPermissionsAssignment($request->all());
			}
			return PermisoModulo::getPermissionsByRole($request->all());
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
			$data = $request->all();
			$role = Rol::getRole($request->data['data_id']);
			if(!$role instanceof Rol){
				throw new \Exception("Rol no encontrado");
			}

			DB::beginTransaction();
			try {
				foreach ($data as $value) {
					if(isset($value['option_id'])) {
						foreach($value['option_id'] as $permission){
							list($permission_id, $module_id) = explode("_", $permission);
							$role->attachPermission($permission_id, ['codigo_modulo' => $module_id]);

							$permisoRol = PermisoRol::where('role_id', $role->id)->where('permission_id', $permission_id)->where('codigo_modulo', $module_id)->first();

                            activity()
							    ->performedOn($role)
							    ->withProperties([
                                    'attributes' => [
								   		'title' => $role->title,
										'display_name' => $permisoRol->permiso->display_name,
										'nombre_modulo' => $permisoRol->modulo->nombre_modulo
							   		] ])
							    ->log('activitylog.role.permission.created');
                        }
					}
		        }

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha asignado el permiso  correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PermisoModuloController:store', $e->getMessage()));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
		if($request->ajax()){
			$data = $request->all();
			$role = Rol::getRole($request->data['data_id']);
			if(!$role instanceof Rol){
				throw new \Exception("Rol no encontrado");
			}

			DB::beginTransaction();
			try {
				foreach ($data as $value) {
					if(isset($value['option_id'])) {
						foreach($value['option_id'] as $permission){
							list($permission_id, $module_id) = explode("_", $permission);

							$permisoRol = PermisoRol::where('codigo_modulo', $module_id)->where('role_id', $role->id)->where('permission_id', $permission_id)->first();
							if($permisoRol instanceof PermisoRol) {
								$permisoRol->delete();

                                activity()
                                    ->performedOn($role)
                                    ->withProperties([
                                        'attributes' => [
                                            'title' => $role->title,
                                            'display_name' => $permisoRol->permiso->display_name,
                                            'nombre_modulo' => $permisoRol->modulo->nombre_modulo
                                        ] ])
                                    ->log('activitylog.role.permission.deleted');
							}
						}
					}
		        }

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Se ha removido el permiso  correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PermisoModuloController:destroy', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }
}
