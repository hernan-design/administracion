<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Events\Notification;

use App\Models\Seguridad\Usuario, App\Models\Core\Convocatoria;

class Reto3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.program.innpulsa.reto3.index');
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
        $user = Usuario::find(\Auth::user()->codigo_usuario);
        if(!$user instanceof Usuario) {
            throw new \Exception("Error recuperando reporte reto 3 ".\Auth::user()->codigo_usuario);
        }
        
        switch ($request->codigo_convocatoria) {
			case Convocatoria::getProgram('invitation_entrepreneurs'):
                \Artisan::queue('innpulsa:reto3', [
                    'user' => \Auth::user()->codigo_usuario
                ]);

                activity()
                    ->performedOn($user)
                    ->withProperties([
                        'attributes' => [
                            'user' => $user->nombre_usuario,
                            'user_id' => $user->codigo_usuario,
                            'report' => 'reto 3'
                        ]
                    ])
                ->log('activitylog.reports');

                return redirect()->route('reportes.monitoreo.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
            break;
            case Convocatoria::getProgram('aldea__20'):
                \Artisan::queue('innpulsaAldea20:reto3', [
                    'user' => \Auth::user()->codigo_usuario
                ]);

                activity()
                    ->performedOn($user)
                    ->withProperties([
                        'attributes' => [
                            'user' => $user->nombre_usuario,
                            'user_id' => $user->codigo_usuario,
                            'report' => 'reto 3 Aldea 20'
                        ]
                    ])
                ->log('activitylog.reports');

                return redirect()->route('reportes.monitoreo.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
            break;
        }

        \Artisan::queue('innpulsa:reto3', [
            'user' => \Auth::user()->codigo_usuario
        ]);

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => 'reto 3'
				] 
			])
		->log('activitylog.reports');

        return redirect()->route('reportes.reto3.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
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
    public function destroy($id)
    {
        //
    }
}
