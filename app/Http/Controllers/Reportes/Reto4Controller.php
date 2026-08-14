<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Events\Notification;

use App\Models\Seguridad\Usuario;

class Reto4Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.program.innpulsa.reto4.index');
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
        // Broadcasting event
        // event(new Notification([
        //     'title' => 'Hola admin 2015',
        //     'content' => 'Contenidooooooo',
        //     // 'url' => $url,
        //     'receivedBy' => [179361, 82737, 169970],
        //     // 'type' => MDBNotificacion::getType('like'),
        //     // 'module' => $model->id_recurso,
        //     // 'resource' => $model->codigo_recurso_modulo
        // ]));

		\Artisan::queue('innpulsa:reto4', [
            'user' => \Auth::user()->codigo_usuario
        ]);

		$user = Usuario::find(\Auth::user()->codigo_usuario);
        if(!$user instanceof Usuario) {
            throw new \Exception("Error recuperando reporte reto 4 ".\Auth::user()->codigo_usuario);
        }

        activity()
            ->performedOn($user)
            ->withProperties([
                'attributes' => [
                    'user' => $user->nombre_usuario,
                    'user_id' => $user->codigo_usuario,
                    'report' => 'reto 4'
                ]
            ])
        ->log('activitylog.reports');

        return redirect()->route('reportes.reto4.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
    }

    
}
