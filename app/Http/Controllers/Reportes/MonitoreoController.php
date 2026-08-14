<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
use App\Models\Seguridad\Usuario, App\Models\Core\Convocatoria;

class MonitoreoController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('reports.program.innpulsa.monitoreo.index');
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
		$user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando reporte reto monitoreo ".\Auth::user()->codigo_usuario);
		}

		switch ($request->codigo_convocatoria) {
			case Convocatoria::getProgram('invitation_entrepreneurs'):
				\Artisan::queue('innpulsa:monitoreo', [
					'user' => \Auth::user()->codigo_usuario
					]);

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

				return redirect()->route('reportes.monitoreo.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
			break;
			case Convocatoria::getProgram('aldea__20'):
				\Artisan::queue('innpulsa:monitoreo20', [
					'user' => \Auth::user()->codigo_usuario
				]);

				activity()
					->performedOn($user)
					->withProperties([
						'attributes' => [
							'user' => $user->nombre_usuario,
							'user_id' => $user->codigo_usuario,
							'report' => 'único Aldea 20'
						]
					])
				->log('activitylog.reports');

				return redirect()->route('reportes.monitoreo.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
			break;
			case Convocatoria::getProgram('fase_modelo_negocios_programa_apps_co'):
				\Artisan::queue('reports:reportappsco', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => session('institution')->codigo_institucion,
					'program' => Convocatoria::getProgram('fase_modelo_negocios_programa_apps_co'),
					'filters' => ['phase' => 1]
				]);
	
				$user = Usuario::find(\Auth::user()->codigo_usuario);
				if(!$user instanceof Usuario) {
					throw new \Exception("Error recuperando reporte registros convocatoria ".\Auth::user()->codigo_usuario);
				}
	
				activity()
					->performedOn($user)
					->withProperties([
						'attributes' => [
							'user' => $user->nombre_usuario,
							'user_id' => $user->codigo_usuario,
							'report' => 'Programa Apps.co'
						]
					])
					->log('activitylog.reports');

				return redirect()->route('reportes.monitoreo.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
			break;
		}
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
