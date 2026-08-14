<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Excel;

use App\Models\Seguridad\Usuario;

class ReporteUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('reports.users.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Artisan::queue('reports:users', [
            'user' => \Auth::user()->codigo_usuario,
            'institution' => session('institution')->codigo_institucion
        ]);

        $user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando reporte usuarios ".\Auth::user()->codigo_usuario);
		}

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => 'usuarios'
				] 
			])
		->log('activitylog.reports');

        return redirect()->route('reportes.usuarios.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
    }
}
