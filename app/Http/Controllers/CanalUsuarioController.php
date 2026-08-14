<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log;

use App\Models\Core\CanalUsuario, App\Models\Seguridad\Usuario, App\Models\Core\Estado;

class CanalUsuarioController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,comunidad/canal');
        $this->middleware('ability:admin|super-admin|administrativo,create,comunidad/canal', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,comunidad/canal', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,comunidad/canal', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return response()->json( CanalUsuario::getUsersChannel( $request->all() ) );
		}
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
			if(empty($request->correo_electronico)){
				return response()->json(['success' => false, 'errors' => 'Debe ingresar un correo electrónico.']);
			}

			$user = Usuario::getUserMail(['email' => $request->correo_electronico]);
			if(!$user instanceof Usuario){
				return response()->json(['success' => false, 'errors' => 'El correo que ingresaste no existe']);
			}

			// get user institution
			$userInstitution = $user->institutions()->where('codigo_institucion', '=', session('institution')->codigo_institucion)->first();

			$channerUserVal = CanalUsuario::getUserchannel($request->channel, $user->codigo_usuario, $userInstitution->codigo_usuario_institucion);
			if($channerUserVal instanceof CanalUsuario){
				return response()->json(['success' => false, 'errors' => 'El usuario ya se encuentra asignado, verifique!!']);
			}

			DB::beginTransaction();
			try {
				$channelUser = new CanalUsuario;
				$channelUser->codigo_estado = Estado::getState('active');
				$channelUser->codigo_canal	= $request->channel;
				$channelUser->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
				$channelUser->save();

				$data = [
					'success' => true,
					'message' => 'El usuario ha sido asignado al canal correctamente!!',
					'codigo_canal_usuario' => $channelUser->codigo_canal_usuario,
					'codigo_usuario' => $user->codigo_usuario,
					'imagen_usuario' => $user->imagen_usuario,
					'user_name' => $user->user_name,
					'nombre_ciudad' => $user->nombre_ciudad,
					'correo_electronico' => $user->correo_electronico,
					'nickname_usuario' => $user->nickname_usuario
				];

				// Commit Transaction
				DB::commit();
				return response()->json($data);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CanalUsuarioController:store', $e->getMessage()));
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
        if ($request->ajax()) {
            DB::beginTransaction();
            $channelUser = CanalUsuario::find($request->codigo_canal_usuario);

            if (!$channelUser instanceof CanalUsuario) {
                abort(404);
            }

            try {

                if ($request->action === 'activate') {
                    $channelUser->codigo_estado = Estado::getState('active');
                } else {
                    $channelUser->codigo_estado = Estado::getState('inactive');
                }

                $channelUser->save();

                $response = $request->action === 'activate' ? 'activado ' : 'inactivado ';

                // Commit Transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Usuario '.$response.'en el canal']);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error(sprintf('%s:%s', 'CanalUsuarioController:update', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		if(!is_numeric($id)){
			abort(404);
		}

		$channelUser = CanalUsuario::find($id);
		if(!$channelUser instanceof CanalUsuario){
			abort(404);
		}

		DB::beginTransaction();
		try {
			$channelUser->delete();

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'El usuario ha sido eliminado del canal correctamente!!']);

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'CanalUsuarioController:destroy', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => trans('app.exception')]);
		}
    }
}
