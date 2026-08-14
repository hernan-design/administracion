<?php

namespace App\Http\Controllers\Reportes;

use App\Models\Core\EvaluacionEmprendimiento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Log;
use App\Models\Seguridad\Usuario;

class EvaluacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $evaluations = EvaluacionEmprendimiento::getEvaluations();
      return view('reports.evaluations.evaluations', compact('evaluations'));
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
      \Artisan::queue('evaluations:monitoreo', [
        'user' => \Auth::user()->codigo_usuario,
        'institution' => session('institution')->codigo_institucion,
        'filters' => $request->has('slc_survey') ? $request->slc_survey : null
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
            'report' => 'evaluaciones'
          ]
        ])
        ->log('activitylog.reports');

      return redirect()->route('reportes.evaluaciones.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
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
