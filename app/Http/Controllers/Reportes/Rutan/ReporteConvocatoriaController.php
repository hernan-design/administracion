<?php

namespace App\Http\Controllers\Reportes\Rutan;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Artisan,Log;
use App\Models\Seguridad\Usuario, App\Models\Core\Convocatoria;

class ReporteConvocatoriaController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('reports.program.rutan.convocatoria.index');
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

		Log::info('Log _1 reportes (btn)');
		switch ($request->id_report) {
			case '209':
				\Artisan::queue('reports:convocatoriarutan', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => session('institution')->codigo_institucion,
					'program' => 634
				  ]);
				break;
			case '200':
				\Artisan::queue('reports:convocatoriarutan200', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => session('institution')->codigo_institucion,
					'program' => 634
				  ]);
				break;
			//reporte general de convocatoria ruta del emprendimiento 634	
			case 'General Ruta Emprendimiento':
				\Artisan::queue('reports:convocatoriarutanrutaemprendimiento', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => session('institution')->codigo_institucion,
					'program' => 634
					]);
			break;
			//mentoring report
			case 'Mentorias':
				Log::info('Log_2.1 rutina reporte acompaniamientos');
				\Artisan::queue('reports:acompaniamientos', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => session('institution')->codigo_institucion,
					'program' => 634
					]);
			break;
			//session report
			case 'Sessions_rutan':
				Log::info('Log_2.2 rutina reporte sesiones');
				Log::info('codigo_usuario: '. \Auth::user()->codigo_usuario);
				Log::info('codigo_institucion: '. session('institution')->codigo_institucion);
				/* \Artisan::queue('report:sessions_rutan', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => session('institution')->codigo_institucion,
					'program' => 634
					]); */

				Artisan::queue('report:sessions_rutan', [
					'user' => \Auth::user()->codigo_usuario,
					'institution' => 61,
					'program' => 634
					]);
			break;
			default:
				$message = 'No existe el reporte';
				return response()->json(['success' => false, 'errors' => $message]);
				break;
		}
		
  
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
                      'report' => 'Convocatorias de RutaN ' . $request->id_report
                  ]
              ])
              ->log('activitylog.reports');
  
          $message = trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]);
          return response()->json(['success' => true, 'message' => $message]);
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
