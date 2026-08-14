<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Events\Notification;

use App\Models\Seguridad\Usuario,
    App\Models\Param\Institucion,
    App\Models\Aprende\Curso;

class CursoIndividualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {       
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        $data = $request->all();

        $user = Usuario::find(\Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando reporte cursos ".\Auth::user()->codigo_usuario);
		}

        $institucion = Institucion::find(session('institution')->codigo_institucion);
        if(!$institucion instanceof Institucion) {
            $message = sprintf(' %s: %s %s', 'App\Console\Commands\Reports\Curso',
                'Error recuperando institucion id', session('institution')->codigo_institucion);
            throw new \Exception($message);
        }

        $curseUsers = Curso::getCourseUsers($institucion, $data['codigo_curso']);
       
        if ($curseUsers->count() === 0) {
            return response()->json(['success' => false, 'errors' => 'Este curso no tiene inscritos']);
        }

        \Artisan::queue('reports:curso_individual', [
            'user' => \Auth::user()->codigo_usuario,
            'institution' => session('institution')->codigo_institucion,
            'codigo_curso' => $data['codigo_curso']
        ]);

		activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => 'Reporte unico de curso'
				] 
			])
        ->log('activitylog.reports');
        
        return response()->json(['success' => true, 'message' => 'Se ha enviado el reporte correctamente!!']);

    }
    
}