<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;
use App\Models\Seguridad\Usuario;
use App\Http\Controllers\Controller;

class UnicoTerpelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.unique.uniqueterpel');
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
        \Artisan::queue('report:terpelunique', [
            'institution' => session('institution')->codigo_institucion,
            'user' => \Auth::user()->codigo_usuario,
            'program' => 436
        ]);

        $user = Usuario::find(\Auth::user()->codigo_usuario);
        if(!$user instanceof Usuario) {
        throw new \Exception("Error recuperando reporte Terpel ".\Auth::user()->codigo_usuario);
        }

        activity()
        ->performedOn($user)
        ->withProperties([
            'attributes' => [
            'user' => $user->nombre_usuario,
            'user_id' => $user->codigo_usuario,
            'report' => 'Unico Terpel'
            ]
        ])
        ->log('activitylog.reports');

        return redirect()->route('reportes.unicoterp.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
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
