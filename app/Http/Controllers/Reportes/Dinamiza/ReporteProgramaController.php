<?php

namespace App\Http\Controllers\Reportes\Dinamiza;

use App\Models\Core\CategoriaProceso, App\Models\Seguridad\Usuario;
use App\Models\Core\Entidad;
use App\Models\Core\EtapaProyecto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ReporteProgramaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if ($request->ajax()) {
        return response()->json(Entidad::getEntitiesForProgram());
      }
      $stages = EtapaProyecto::getAllStagesForPrograms();
      $categories = CategoriaProceso::getCategoriesProgram();
      return view('reports.dinamiza.programs.index', compact('stages', 'categories'));
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
      \Artisan::queue('dinamiza:programs', [
        'user' => \Auth::user()->codigo_usuario,
        'institution' => session('institution')->codigo_institucion,
        'filters' => $request->all()
      ]);

      $user = Usuario::find(\Auth::user()->codigo_usuario);
      if(!$user instanceof Usuario) {
        throw new \Exception("Error recuperando usuario de dinamiza ".\Auth::user()->codigo_usuario);
      }

      activity()
        ->performedOn($user)
        ->withProperties([
          'attributes' => [
            'user' => $user->nombre_usuario,
            'user_id' => $user->codigo_usuario,
            'report' => 'programas dinamiza'
          ]
        ])
        ->log('activitylog.reports');

      return redirect()->route('reportes.dinamiza.programas.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
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
