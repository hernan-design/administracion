<?php

namespace App\Http\Controllers;

use App\Models\Param\Institucion;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log, Excel, Auth;

use App\Models\Core\Estado, App\Models\Core\EvaluacionEmprendimiento, App\Models\Core\ComentarioEvaluacionEmprendimiento, App\Models\Core\AsignacionJurado, App\Models\Seguridad\Usuario;
use TuProyecto;

class EvaluacionComentarioController extends Controller
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
        if($request->ajax()) {
            return ComentarioEvaluacionEmprendimiento::getDataCommentsEvaluation();
        }
        $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('evaluation.comments.main', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('evaluation.comments.create-edit');
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
        $comment  = new ComentarioEvaluacionEmprendimiento();
        if ($comment->isValid($data)) {
          DB::beginTransaction();
          try {

            $comment->fill($data);
            $comment->codigo_estado = Estado::getState('active');
            $comment->codigo_evaluacion_emprendimiento = $data['codigo_evaluacion_emprendimiento'];
            $comment->save();

            // Commit Transaction
            DB::commit();
            $message = 'El comentario de evaluacion ha sido creado correctamente!';
            return response()->json(['success' => true, 'redirect'=> true ,'message' => $message]);
          }catch(\Exception $e){
              DB::rollback();
              Log::error(sprintf('%s:%s', 'EvaluacionComentarioController:store', $e->getMessage()));
              return response()->json(['success' => false, 'errors' => trans('app.exception')]);
          }
          $errmsg =  $comment->errors;
          Log::error(sprintf('%s:%s', 'EvaluacionComentarioController:store', $errmsg ));
          return response()->json(['success' => false, 'errors' => $errmsg]);
        }
        $errmsg = 'This is not an ajax request';
        Log::error(sprintf('%s:%s', 'EvaluacionComentarioController:store', $errmsg ));
        abort(404);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $evaluation = EvaluacionEmprendimiento::find($id);
        if(!$evaluation instanceof EvaluacionEmprendimiento) {
            abort(404);
        }

        if ($request->ajax()) {
            $response = EvaluacionEmprendimiento::buildEvaluation($evaluation);
            return response()->json($response);
        }
        return view('evaluation.main');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if(!is_numeric($id)) {
          Log::error(sprintf('%s:%s', 'ConvocatoriaController:edit', 'Error: not numeric ID'));
          abort(404,'Error: not numeric ID') ;
        }

        $program = Convocatoria::getDataProgram($id);
        if(!$program instanceof Convocatoria){
          abort(404);
        }

        return view('evaluation.comments.create-edit');
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
