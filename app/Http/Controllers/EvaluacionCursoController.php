<?php

namespace App\Http\Controllers;

use App\Models\Param\Institucion;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log, Excel, Auth;

use App\Models\Core\Estado, App\Models\Core\Formulario, App\Models\Core\EvaluacionEmprendimiento, App\Models\Aprende\Curso,  App\Models\Seguridad\Usuario;
use TuProyecto;

class EvaluacionCursoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
       // $this->middleware('ability:admin|super-admin,view,evaluacion', ['only' => ['index']]);
        //$this->middleware('ability:admin|super-admin,create,evaluacion', ['only' => ['create', 'store']]);
        //$this->middleware('ability:admin|super-admin,edit,evaluacion', ['only' => ['edit', 'update']]);
       // $this->middleware('ability:admin|super-admin,remove,evaluacion', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      $data = $request->all();

      $id = $request->has('codigo_curso') ? $data['codigo_curso'] : null;
      if(!is_numeric($id)) {
        $errmsg =sprintf('el codigo del curso no es valido: %s', $id);
        Log::error(sprintf('%s:%s', 'EvaluacionCursoController:index', $errmsg));
        return response()->json(['success' => false, 'errors' => trans('app.exception')]);
      }

        $course = Curso::getCourse($id);

        if(!$course instanceof Curso){
          abort(404);
        }
        if($request->ajax()) {
            return Curso::getDataEvaluations($data);
        }
       abort('404');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

    $data = $request->all();
    if(!is_numeric($id)){
      $errmsg =sprintf('el codigo del formulario no es valido: %s', $id);
        Log::error(sprintf('%s:%s', 'EvaluacionCursoController::show', $errmsg));
      abort(404);
    }
    $codigo_curso = $request->has('curso') ? (int)  $data['curso'] : null;
    if(!is_numeric($codigo_curso)){
      $errmsg =sprintf('el codigo del curso no es valido: %s', $codigo_curso);
        Log::error(sprintf('%s:%s', 'EvaluacionCursoController::show', $errmsg));
      abort(404);
    }

    $course = Curso::getCourse($codigo_curso);

    if(!$course instanceof Curso){
      abort(404);
    }

    $form = Formulario::getForm($id);
    if(!$form instanceof Formulario) {
      abort(404);
    }

    $form = Formulario::buildForm($form);

    if ($request->ajax()) {
      return response()->json($form);
    }

    return view('course.evaluation.detail');
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
	 * Export pdf the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function exportar(Request $request)
	{
    $data = $request->all();
    if($request->ajax()){
      return response()->json(['success' => false, 'errors' => "404-Not Found"]);
    }
		abort(404);
	}

  /**
	 * Display the data evaluation program.
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function evaluations(Request $request)
    {
      if($request->ajax()){

        if($request->has('id') && !empty($request->id)){
          return response()->json(EvaluacionEmprendimiento::select('codigo_evaluacion_emprendimiento as id', 'nombre_evaluacion_emprendimiento as text')->where('codigo_evaluacion_emprendimiento', $request->id)->get() );
        }

        if($request->has('data')){
          return response()->json(EvaluacionEmprendimiento::getCommentsEvaluationList( $request->all() ) );
        }
      }
      abort(404);
    }
}
