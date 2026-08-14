<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use DB, Excel;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Core\UsuarioEncuesta, App\Models\Seguridad\Usuario, App\Models\Core\Estado, App\Models\Core\Convocatoria, App\Models\Core\Encuesta, App\Models\Core\RespuestaEncuesta, App\Models\Core\Ciudad, App\Models\Core\SectorInteres, App\Models\Core\NivelEducacion, App\Models\Seguridad\Modulo;

class SeguimientoConvocatoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('reports.program.seguimiento.index');
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
    	if($request->has('convocatoria') && !empty($request->convocatoria))
		{
	        \Artisan::queue('reports:monitoringprogram', [
    	        'user' => \Auth::user()->codigo_usuario,
    	        'institution' => session('institution')->codigo_institucion,
    	        'program' => $request->convocatoria
        	]);

        	return redirect()->route('reportes.seguimiento.index')->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
		}
		abort(500);
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
