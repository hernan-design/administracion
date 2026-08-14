<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Log;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Aprende\Curso, App\Models\Aprende\TemaCurso, App\Models\Core\Estado, App\Models\Core\TipoAdjunto;

class TemaCursoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,curso');
        $this->middleware('ability:admin|super-admin|administrativo,create,curso', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,curso', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,curso', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->has('course')){
			$course = Curso::find($request->course);
			if(!$course instanceof Curso) {
	            abort(404);
			}
			if ($request->ajax()) {
				return response()->json(TemaCurso::getThemesCourse($course->codigo_curso));
			}
		}
		abort(404);
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
		if($request->ajax()){
			$data = $request->all();

			$course = Curso::find($request->codigo_curso);
			if(!$course instanceof Curso){
				return response()->json(['success' => false, 'errors' => 'El código del curso no se encuentra en el sistema ']);
			}

			$courseTheme = new TemaCurso;
			if ($courseTheme->isValid($data)) {
				DB::beginTransaction();
	            try {
					$courseTheme->fill($data);
                    $courseTheme->url_tema_curso = slugtp($request->nombre_tema_curso, $courseTheme->getTable(), 'url_tema_curso');
					$courseTheme->codigo_estado = Estado::getState('active');
					$courseTheme->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'El tema ha sido creado correctamente!!', 'codigo_tema_curso' => $courseTheme->codigo_tema_curso, 'codigo_curso' => $course->codigo_curso, 'codigo_estado' => $course->codigo_estado]);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'TemaCursoController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $courseTheme->errors]);
		}
		abort(404);
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
		if($request->ajax()){
            $data = $request->all();
			$courseTheme = TemaCurso::find($id);
			if(!$courseTheme instanceof TemaCurso){
				return response()->json(['success' => false, 'errors' => 'El codigo del tema no existe en el sistema, verifique!!']);
			}

			DB::beginTransaction();
			try {
                $courseTheme->nombre_tema_curso = trim( preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->nombre_tema_curso) );
                $courseTheme->objetivo_tema_curso = trim( preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $request->objetivo_tema_curso) );
                $courseTheme->codigo_estado = $request->has('change_state') ? $request->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active') : $courseTheme->codigo_estado;
                $courseTheme->orden_tema = $request->slc_order_course != 0 ? $request->slc_order_course  : null;
				$courseTheme->save();

				$changedState = $courseTheme->codigo_estado == Estado::getState('active') ? 'activado' : 'inactivado';
                $message = $request->has('change_state') ? $changedState : 'actualizado';
				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El tema ha sido '.$message.' correctamente!!', 'codigo_tema_curso' => $courseTheme->codigo_tema_curso, 'codigo_curso' => $courseTheme->codigo_curso, 'codigo_estado' => $courseTheme->codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'TemaCursoController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}

		}
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
