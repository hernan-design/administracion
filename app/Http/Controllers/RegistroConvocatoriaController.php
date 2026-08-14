<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use Custom\Innpulsa;

use App\Models\Core\Convocatoria, App\Models\Core\OrientacionConvocatoria, App\Models\Core\RegistroConvocatoria, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Fase, App\Models\Core\Estado, App\Models\Seguridad\Usuario;

class RegistroConvocatoriaController extends Controller
{
    /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		if($request->ajax()){
            if($request->has('program')) {
				$program = Convocatoria::find($request->program);
				if(!$program instanceof Convocatoria) {
					abort(404);
				}

				return response()->json( RegistroFaseUsuario::programUser($program, $request->all()) );
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
            $data['email'] = $request->correo_electronico;

            $user = Usuario::getUserMail( $data );
            if(!$user instanceof Usuario){
                throw new \Exception("No existe el usuario $request->correo_electronico");
            }

            $program = Convocatoria::find( $request->program );
            if(!$program instanceof Convocatoria){
                throw new \Exception("No existe la convocatoria $request->program");
            }

            $userValidateRegister = RegistroConvocatoria::where('codigo_convocatoria', $program->codigo_convocatoria)
                ->where('codigo_usuario', $user->codigo_usuario)
                ->where('codigo_estado', Estado::getState('active'))
                ->first();
            if($userValidateRegister instanceof RegistroConvocatoria){
                return response()->json(['success' => false, 'errors' => 'El usuario ya está registrado en esta convocatoria.']);
            }

            $fase = Fase::where('codigo_convocatoria', $program->codigo_convocatoria)->where('fase_registro', true)->first();
            if(!$fase instanceof Fase){
                return response()->json(['success' => false, 'errors' => 'Para asignar el usuario debe tener una fase en esta convocatoria.']);
            }

            DB::beginTransaction();
            try {

                // Register in program
                $usuarioRegister = new RegistroConvocatoria;
                $usuarioRegister->codigo_convocatoria = $program->codigo_convocatoria;
                $usuarioRegister->codigo_usuario = $user->codigo_usuario;
                $usuarioRegister->codigo_estado = Estado::getState('active');
                $usuarioRegister->fecha_registro_convocatoria = date('Y-m-d');
                $usuarioRegister->hora_registro_convocatoria = date('H:i:s');
                $usuarioRegister->save();

                // Register in fase program
                $userFaseRegister = new RegistroFaseUsuario;
                $userFaseRegister->codigo_fase = $fase->codigo_fase;
                $userFaseRegister->codigo_usuario = $user->codigo_usuario;
                $userFaseRegister->fecha_registro_fase_usuario = date('Y-m-d');
                $userFaseRegister->hora_registro_fase_usuario = date('H:i:s');
                $userFaseRegister->codigo_estado = Estado::getState('active');
                $userFaseRegister->codigo_registro_convocatoria = $usuarioRegister->codigo_registro_convocatoria;
                $userFaseRegister->save();

                $response = $usuarioRegister;
                $response->success = true;
                $response->message = 'Se ha asignado el usuario correctamente!!';
                $response->user_name = $user->user_name;
                $response->imagen_usuario = $user->imagen_usuario;
                $response->nombre_ciudad = $user->nombre_ciudad;
                $response->correo_electronico = $user->correo_electronico;
                $response->nickname_usuario = $user->nickname_usuario;

                // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'RegistroConvocatoriaController:store', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
		}
		abort(404);
	}
}
