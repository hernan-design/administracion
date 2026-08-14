<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log;

use App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado, App\Models\Core\AsignacionMarketplace, App\Models\Seguridad\Usuario;

class AsignacionMarketplaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
        	if($request->has('codigo_registro_fase')) {
				return response()->json(AsignacionMarketplace::getAssignmentsMember($request->codigo_registro_fase));
        	}

        	if($request->has('data')){
				return response()->json(AsignacionMarketplace::getAssignmentMembers($request->all()));
			}
        }
		return view('marketplace.assignment.index');
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
		if($request->ajax()) {
			$data = $request->all();
			$assignmentMarketplace = new AsignacionMarketplace;

			$user = Usuario::getUserMail(['email' => $request->email]);
            if(!$user instanceof Usuario) {
                return response()->json(['success' => false, 'errors' => 'No existe un usuario asociado a esta dirección de correo electrónico.']);
            }

			$usuario = Usuario::find($data['codigo_usuario']);
			if(!$usuario instanceof Usuario){
				return 	response()->json(['success' => false, 'errors' => 'El usuario no existe, verifique!!']);
			}

			// validate if user exists as assigment member
			if(AsignacionMarketplace::userExists($usuario->codigo_usuario, $request->codigo_registro_fase_usuario) instanceof AsignacionMarketplace){
				return response()->json(['success' => false, 'errors' => '¡El usuario con este correo electrónico, ya se encuentra asignado!']);
			}

			// validate retrived data
			if( $assignmentMarketplace->isValid($data) ){
				DB::beginTransaction();
				try {
					$assignmentMarketplace->codigo_estado = Estado::getState('active');
					$assignmentMarketplace->fecha_asignacion_mp = date('Y-m-d');
					$assignmentMarketplace->fill($data)->save();

					// Prepare response
					$response = AsignacionMarketplace::buildAssignment(AsignacionMarketplace::getDataMember($assignmentMarketplace));
					$response->success = true;
					$response->message = 'El usuario ha sido asignado correctamente.';

					// // Commit Transaction
					DB::commit();
					return response()->json($response);
				} catch (\Exception $e) {
					DB::rollback();
					Log::error($e->getMessage());
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $assignmentMarketplace->errors]);
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
		$register = RegistroFaseUsuario::getRegisterPhase($id);
        if(!$register instanceof RegistroFaseUsuario) {
            abort(404);
        }
        return view('marketplace.assignment.detail', compact('register'));
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
        if($request->ajax()) {
			DB::beginTransaction();
			try {
				// check out if register exists
				$assignmentMarketplace = AsignacionMarketplace::find($id);
				if(!$assignmentMarketplace instanceof AsignacionMarketplace) {
					DB::rollback();
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}

				$assignmentMarketplace->codigo_estado = Estado::getState('removed');
				$assignmentMarketplace->save();

				DB::commit();
                return response()->json(['success' => true, 'message' => 'El integrante se quito con éxito.']);

			} catch (\Exception $e) {
				DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
			}
		}
    }
}
