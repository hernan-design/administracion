<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Seguridad\Usuario;

class FormularioController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
	    $this->middleware('ability:admin|super-admin|administrativo,export');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
    //dd($request->all());
    $codigo_curso = $request->has('curso') ? (int)  $request->curso : null;
    if($request->has('evaluation') && !empty($request->evaluation))
		{
	          \Artisan::queue('reports:formularios', [
    	        'user' => \Auth::user()->codigo_usuario,
    	        'institution' => session('institution')->codigo_institucion,
    	        'survey' => $request->evaluation,
    	        'filters' => $request->all()
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
                        'report' => 'registro formulario'
                    ]
                ])
            ->log('activitylog.reports');

          if($codigo_curso){
            return redirect()->route('curso.evaluacion.show', ['evaluacion' => $request->evaluation, 'curso'=> $codigo_curso])->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
		      }

        	return redirect()->route('formulario.show', ['formulario' => $request->evaluation])->with('message', trans('app.reports.success', ['email' => \Auth::user()->correo_electronico]));
		}
		abort(500);
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
        //
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
