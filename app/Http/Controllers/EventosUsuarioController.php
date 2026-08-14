<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request,
    App\Http\Controllers\Controller,
    App\Models\Core\UsuarioEvento,
    Datatables, Log, DB, Queue, Auth, App, AWS, Carbon,
    App\Models\Core\Estado;

class EventosUsuarioController extends Controller
{

    public function index(Request $request)
    {
        $codigo_usuario = $request->codigo_usuario;
        return view('event.eventsforuser', compact('codigo_usuario'));
    }

    public function show(Request $request, $id)
    {
        if($request->ajax()){
            //dd($request->nombre_evento);
            return Datatables::of( UsuarioEvento::getEventsForUser( $request->all() ) )->make(true);
        }

    }
}