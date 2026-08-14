<?php

namespace App\Http\Controllers\Reportes\Innpulsa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\Usuario;


class ReportEmpoderaController extends Controller
{
	public function index() {
		return view('reports.program.innpulsa.monitoreo.innpulsa_empodera');
	}


	public function store(Request $request) {
		\Artisan::queue('empodera:report', [
			'user' => \Auth::user()->codigo_usuario
		]);

		$user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando reporte reto monitoreo ".\Auth::user()->codigo_usuario);
		}

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => 'único'
				]
			])
			->log('activitylog.reports');

		return redirect()->route('reportes.empodera.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
	}
}
