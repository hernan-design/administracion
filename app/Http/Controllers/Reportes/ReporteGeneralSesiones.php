<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\Usuario;


class ReporteGeneralSesiones extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return view('reports.sessions.sessions');
  }



  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $request->validate([
      'fecha_inicial' => 'nullable|date_format:Y-m-d',
      'fecha_final' => 'nullable|required_with:fecha_inicial|date_format:Y-m-d|after_or_equal:fecha_inicial'
    ], [
      'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date'
    ]);

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if(!$user instanceof Usuario) {
      throw new \Exception("Error recuperando reporte general de mentorias ".\Auth::user()->codigo_usuario);
    }

    $startAt = $request->get('fecha_inicial');
    $endAt = $request->get('fecha_final');

    \Artisan::queue('admin:sesiones', [
      'user' => \Auth::user()->codigo_usuario,
      'institution' => session('institution')->codigo_institucion,
      '--startAt' => $startAt,
      '--endAt' => $endAt
    ]);

    activity()
      ->performedOn($user)
      ->withProperties([
        'attributes' => [
          'user' => $user->nombre_usuario,
          'user_id' => $user->codigo_usuario,
          'report' => 'general de sesiones'
        ]
      ])
      ->log('activitylog.reports');

    return redirect()->route('reportes.sesiones.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
  }


}
