<?php

namespace App\Http\Controllers;

use App\Models\Core\EvaluacionEmprendimiento;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Auth, AWS, Datatables, TuProyecto, Log;

class ReporteController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $reports = [];
    $s3 = AWS::createClient('s3');

    $results = $s3->ListObjects([
      'Bucket' => env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com'),
      'Prefix' => 'reports/' . session('institution')->codigo_institucion . '/' . Auth::user()->codigo_usuario
    ]);

    if (!is_null($results['Contents'])) {
      foreach ($results['Contents'] as $object) {
        list($folder, $institution, $user, $name_report) = explode("/", $object['Key']);

        $data['key'] = $object['Key'];
        $data['name'] = $name_report;
        $data['date'] = date('Y-m-d H:i:s', strtotime($object['LastModified']));
        $data['size'] = TuProyecto::formatBytes($object['Size']);
        $data['download'] = config('tuproyecto.url_download_aws') . "/{$object['Key']}";

        $reports[] = $data;
      }
    }
    $evaluations = EvaluacionEmprendimiento::getEvaluations();
    return view('reports.general.index', compact('reports', 'evaluations'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $reportOption = $request->get('tipo_reporte');
    $reportOptionEvaluation = $request->get('tipo_reporte_evaluacion');
    $startAt = $request->get('fecha_inicial');
    $endAt = $request->get('fecha_final');

    $user = Usuario::find(\Auth::user()->codigo_usuario);
    if (!$user instanceof Usuario) {
      throw new \Exception("Error recuperando reporte cursos " . \Auth::user()->codigo_usuario);
    }

    switch ($reportOption) {
      case '1':
        \Artisan::queue('evaluations:monitoreo', [
          'user' => \Auth::user()->codigo_usuario,
          'institution' => session('institution')->codigo_institucion,
          'filters' => $reportOptionEvaluation != "" ? $reportOptionEvaluation : null,
          '--startAt' => $startAt,
          '--endAt' => $endAt
        ]);

        activity()
        ->performedOn($user)
        ->withProperties([
          'attributes' => [
            'user' => $user->nombre_usuario,
            'user_id' => $user->codigo_usuario,
            'report' => 'evaluaciones'
          ]
        ])
        ->log('activitylog.reports');

        return redirect()->route('reportes.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
        break;
      case '2':
        \Artisan::queue("compensar:events", [
          'user' => \Auth::user()->codigo_usuario,
          '--startAt' => $startAt,
          '--endAt' => $endAt,
        ]);

        activity()
          ->performedOn($user)
          ->withProperties([
            'attributes' => [
              'user' => $user->nombre_usuario,
              'user_id' => $user->codigo_usuario,
              'report' => 'general de eventos'
            ]
          ])
          ->log('activitylog.reports');

        return redirect()->route('reportes.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
        break;
      case '3':
        \Artisan::queue('reports:cursos', [
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
              'report' => 'cursos'
            ]
          ])
          ->log('activitylog.reports');

        return redirect()->route('reportes.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
        break;

      case '4':
        \Artisan::queue('admin:mentorias', [
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
              'report' => 'general de mentorias'
            ]
          ])
          ->log('activitylog.reports');

        return redirect()->route('reportes.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
        break;

      case '5':
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
        return redirect()->route('reportes.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
        break;


      default:
        # code...
        break;
    }
  }

}
