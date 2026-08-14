<?php

namespace App\Http\Controllers;

use App\Models\Core\AcuerdoCompra;
use App\Models\Core\AsignacionMarketplace;
use App\Models\Core\Compra;
use App\Models\Core\EntregableAcuerdo;
use App\Models\Core\EvaluacionCompra;
use App\Models\Core\EvaluacionItemEmprendimiento;
use App\Models\Core\ItemEvaluacionCompra;
use App\Models\Core\ItemEvaluacionEmprendimiento;
use App\Models\Core\Producto;
use App\Models\Core\RespuestaEvalCompra;
use App\Models\Param\Institucion;
use Illuminate\Http\Request;
use App\Exports\ReportsDownloadExport;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log, Excel, Auth;

use App\Models\Core\Estado, App\Models\Core\EvaluacionEmprendimiento, App\Models\Core\AsignacionJurado, App\Models\Seguridad\Usuario;
use TuProyecto;

class ItemEvaluacionEmprendimientoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evaluacion', ['only' => ['index']]);
        $this->middleware('ability:admin|super-admin|administrativo,create,evaluacion', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evaluacion', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,evaluacion', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->ajax()) {




            return EvaluacionEmprendimiento::getDataEvaluations();
        }

        return view('evaluation.main');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      if($request->ajax()) {
    }
      return view('evaluation.create');
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

			$evaluation = new EvaluacionEmprendimiento;
			DB::beginTransaction();
      try {
          $evaluation->nombre_evaluacion_emprendimiento = $data['nombre_evaluacion_emprendimiento'];
          $evaluation->descripcion_evaluacion_emprendimiento = $data['descripcion_evaluacion_emprendimiento'];
          $evaluation->aceptacion_termnos_evaluacion = $data['aceptacion_terminos_evaluacion_emprendimiento'];
          $evaluation->visibilidad_aplicacion = $data['visibilidad_aplicacion_evaluacion_emprendimiento'];
          $evaluation->comentario_obligatorio_evaluacion = $data['comentario_obligatorio_evaluacion_emprendimiento'];
          $evaluation->comentario_evaluacion_cerrado = $data['comentario_evaluacion_cerrado_evaluacion_emprendimiento'];
          $evaluation->codigo_estado = Estado::getState('active');
          $evaluation->codigo_usuario = Auth::user()->codigo_usuario;
          $evaluation->fecha_evaluacion_emprendimiento = $data['fecha_evaluacion_emprendimiento'];
          $evaluation->hora_evaluacion_emprendimiento = $data['hora_evaluacion_emprendimiento'];
          $evaluation->codigo_institucion = session('institution')->codigo_institucion;
          $evaluation->save();


					// Commit Transaction
          DB::commit();
          return response()->json(['success' => true, 'message' => 'El formulario ha sido creado correctamente.', 'codigo_evaluacion_emprendimiento' => $evaluation->codigo_evaluacion_emprendimiento, 'redirect' => true]);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'EvaluacionEmprendimientoController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			return response()->json(['success' => false, 'errors' => $form->errors]);
		}
		abort(404);
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
            $itemEmprendimiento = ItemEvaluacionEmprendimiento::getForm($id);
            return response()->json($itemEmprendimiento);
        }
        return view('evaluation.main');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if(!is_numeric($id)) {
        abort(404);
      }

      $evaluation = EvaluacionEmprendimiento::getForm($id);
      if(!$evaluation instanceof EvaluacionEmprendimiento){
        abort(404);
      }

      // dd($evaluation);

      $itemEvaluacion = ItemEvaluacionEmprendimiento::getForm($id);
      $tipoEvaluacionItem = TipoEvaluacionItem::get();
      $codigoGrupOpcEvalPregunta = GrupOpcEvalPregunta::get();
      $codigoGrupoItemEval = GrupoItemEval::get();


      return view('evaluation.create-edit', compact('evaluation', 'itemEvaluacion', 'tipoEvaluacionItem', 'codigoGrupOpcEvalPregunta', 'codigoGrupoItemEval'));

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
			return EvaluacionEmprendimiento::getEvaluations($request->all());
		}
		abort(404);
	}

	/**
	 * Export pdf the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function exportar(Request $request)
	{
		if($request->has('evaluation') && !empty($request->evaluation))
		{
			$user = Usuario::find(Auth::user()->codigo_usuario);
			if(!$user instanceof Usuario) {
				throw new \Exception("Error recuperando usuario en sesión.");
			}

			$query = AsignacionJurado::query();
			$query->select('asignacion_jurado.codigo_asignacion_jurado','asignacion_jurado.codigo_usuario as owner', 'fecha_asignacion_jurado', 'registro_fase_usuario.codigo_fase','fase.codigo_evaluacion_emprendimiento', 'asignacion_jurado.codigo_registro_fase_usuario', 'fase.url_fase', 'fase.nombre_fase', DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'numero_registro_convocatoria',
				// Project data
				DB::raw("usuario.nombre_usuario as nombre_usuario_proyecto"), DB::raw('(CASE  WHEN codigo_resultado_evaluacion IS NULL THEN false ELSE true END) as evaluation'), 'resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', 'resultado_evaluacion_emprendimiento.hora_resultado_evaluacion','person.correo_electronico', 'nombre_especialidad',
				DB::raw('COALESCE(puntaje_total_eval_emprendi, 0) AS total_evaluation')
			);

			$query->leftJoin('core.especialidad', 'asignacion_jurado.codigo_especialidad', '=', 'especialidad.codigo_especialidad');
			$query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');

			$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
			$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
			$query->leftJoin('core.persona', 'asignacion_jurado.codigo_usuario', '=', 'persona.codigo_usuario');
			$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

			$query->join('core.entidad', 'registro_fase_usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
			$query->join('seguridad.usuario as person', 'asignacion_jurado.codigo_usuario', '=', 'person.codigo_usuario');

			// User institution
			$query->join('core.usuario_institucion', function($join) {
				$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
					->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
					->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
			});

			// Get Results Evaluation
			$query->leftjoin('core.resultado_evaluacion_emprendimiento', function($join) use($request){
				$join->on('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento')
					->on('resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado')
					->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=',  $request->evaluation);
			});

			$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

			if($request->has('evaluated') && !empty($request->evaluated)){
				if($request->evaluated == AsignacionJurado::getEvaluated('evaluated')){
					$query->whereNotNull('codigo_resultado_evaluacion');
				}
				if($request->evaluated == AsignacionJurado::getEvaluated('notevaluated')){
					$query->whereNull('codigo_resultado_evaluacion');
				}
			}

			if($request->has('email') && !empty($request->email)){
				$email = trim($request->email);
				$query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%$email%'))");
			}

			if($request->has('emprendimiento') && !empty($request->emprendimiento)) {
				$query->whereRaw("usuario.nombre_usuario ilike '%".$request->emprendimiento."%'");
			}

			if($request->has('number_register') && !empty($request->number_register)){
				$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $request->number_register);
			}

			$query->where('fase.codigo_evaluacion_emprendimiento', $request->evaluation);
			$query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
			$query->orderBy('numero_registro_convocatoria','asc');
			$usuarios = $query->get();

			// Prepare header
			$header = [];
			$header[] = 'Aplicación';
			$header[] = 'Emprendimiento';
			$header[] = 'Evaluador';
			$header[] = 'Correo electronico';
			$header[] = 'Especialidad';
			$header[] = 'Fecha asignación';
			$header[] = 'Fecha evaluación';
			$header[] = 'Total evaluación';

			$item = 1;
			$data = collect([]);
			foreach ($usuarios as $usuario)
			{
				unset($record);
				$record[] = $usuario->numero_registro_convocatoria;
				$record[] = $usuario->nombre_usuario_proyecto;
				$record[] = $usuario->nombre_usuario;
				$record[] = $usuario->correo_electronico;
				$record[] = $usuario->nombre_especialidad;
				$record[] = $usuario->fecha_asignacion_jurado;
				$record[] = $usuario->fecha_resultado_evaluacion.' '.$usuario->hora_resultado_evaluacion;
				$record[] = $usuario->total_evaluation;
				$data[] = $record;

				$item++;
			}

			$filename = "Asignaciones";

			activity()
				->performedOn($user)
				->withProperties([
					'attributes' => [
						'user' => $user->nombre_usuario,
						'user_id' => $user->codigo_usuario,
						'report' => $filename
					]
				])
			->log('activitylog.reports');

      return (new ReportsDownloadExport([$header], [$data]))->download('Asignaciones.xlsx');
		}
		abort(500);
	}
}
