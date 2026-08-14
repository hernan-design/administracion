<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Seguridad\Usuario;

class DetalleNotificacionUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            if($request->item){
                $data['email'] = $request->correo_electronico;
                $response = Usuario::getUserMail($data);
                $response->success = true;
                $response->message = 'Se ha asignado caorrectamente el usuario.';

                return response()->json( $response );                    
            }
        }
        abort(404);
    }
}
