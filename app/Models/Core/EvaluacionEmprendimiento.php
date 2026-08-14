<?php

namespace App\Models\Core;

use App\Models\Param\Institucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

use Datatables;

use DB, Lang, App, Log, Input;
use TuProyecto;

class EvaluacionEmprendimiento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.evaluacion_emprendimiento';

   	protected $primaryKey = 'codigo_evaluacion_emprendimiento';

   	public $timestamps = false;

     protected $fillable = ['codigo_institucion', 'codigo_estado', 'codigo_usuario', 'nombre_evaluacion_emprendimiento', 'fecha_evaluacion_emprendimiento', 'hora_evaluacion_emprendimiento'];




   	public static function getEvaluation($evaluation) {
    	switch ($evaluation) :
		    case 'evaluationchallenge1': return 8;
		    case 'evaluationchallenge1_2': return 9;
			default: return 'Not evaluation identified';
		endswitch;
    }

	public static function getDataEvaluations() {

		$evaluations = [];
    $query = EvaluacionEmprendimiento::query();
    $query->select('evaluacion_emprendimiento.*', 'nombre_convocatoria', DB::raw('(contador_asignados - contador_evaluados) as pendientes'));
    $query->join('core.fase', 'evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento');
    $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->where('evaluacion_emprendimiento.codigo_estado', '=', Estado::getState('active'));
		$query->where('evaluacion_emprendimiento.codigo_institucion', session('institution')->codigo_institucion);
    // Obtener el SQL generado
    $sql = $query->toSql();

    // Obtener los parámetros utilizados en la consulta
    $bindings = $query->getBindings();

    // Imprimir el SQL y los parámetros
    // dd([
    //     'sql' => $sql,
    //     'bindings' => $bindings,
    // ]);
		return Datatables::of($query)->make(true);
  }

  public static function getDataCommentsEvaluations() {

		$evaluations = [];
    $query = EvaluacionEmprendimiento::query();
    $query->select('evaluacion_emprendimiento.*', 'nombre_convocatoria');
    $query->join('core.fase', 'evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento');
    $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->where('evaluacion_emprendimiento.codigo_estado', '=', Estado::getState('active'));
		$query->where('evaluacion_emprendimiento.codigo_institucion', session('institution')->codigo_institucion);

    return Datatables::of($query)->make(true);
  }

  public static function getCommentsEvaluationList(array $filters = [])
  {
          $query = EvaluacionEmprendimiento::query();
          $query->select('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento as id', 'nombre_evaluacion_emprendimiento as text');
          $query->join('core.fase', 'evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento');
          $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
          $query->where('evaluacion_emprendimiento.codigo_estado', '=', Estado::getState('active'));
          $query->where('evaluacion_emprendimiento.codigo_institucion', session('institution')->codigo_institucion);
          $query->where('evaluacion_emprendimiento.comentario_evaluacion_cerrado',true);

          if(isset($filters['data']) && !empty($filters['data'])) {
            $query->orWhere('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', $filters['data']);
          }

          // Filters
          if(isset($filters['q']) && !empty($filters['q'])) {
            $query->whereRaw("nombre_evaluacion_emprendimiento ilike '%".$filters['q']."%'");
          }

          $query->orderBy('text', 'asc');
          return $query->get();
  }

	/**
	 * Build Evaluation.
	*/
   	public static function buildEvaluation($evaluation)
   	{
		$object = new \stdClass();
		$object->codigo_evaluacion_emprendimiento = $evaluation->codigo_evaluacion_emprendimiento;
		$object->nombre_evaluacion_emprendimiento = $evaluation->nombre_evaluacion_emprendimiento;
		$object->descripcion_evaluacion_emprendimiento = $evaluation->descripcion_evaluacion_emprendimiento;
		$object->contador_evaluados = $evaluation->contador_evaluados;
		$object->contador_asignados = $evaluation->contador_asignados;
		$object->nombre_convocatoria = $evaluation->nombre_convocatoria;
		$object->pendientes = $evaluation->contador_asignados - $evaluation->contador_evaluados;
    $object->fecha_evaluacion_emprendimiento = $evaluation->fecha_evaluacion_emprendimiento;
    $object->hora_evaluacion_emprendimiento = $evaluation->hora_evaluacion_emprendimiento;
    $object->codigo_estado = $evaluation->codigo_estado;
    $object->codigo_usuario = $evaluation->codigo_usuario;
    $object->aceptacion_termnos_evaluacion = $evaluation->aceptacion_termnos_evaluacion;
    $object->visibilidad_aplicacion = $evaluation->visibilidad_aplicacion;
    $object->comentario_obligatorio_evaluacion = $evaluation->comentario_obligatorio_evaluacion;
    $object->comentario_evaluacion_cerrado = $evaluation->comentario_evaluacion_cerrado;


		return $object;
   	}

	public static function getEvaluations(array $filters = [])
	{
		$query = EvaluacionEmprendimiento::query();
		$query->select('codigo_evaluacion_emprendimiento as id', 'nombre_evaluacion_emprendimiento as text');
		$query->where('codigo_institucion', session('institution')->codigo_institucion);
		$query->where('codigo_estado', Estado::getState('active'));

		if(isset($filters['q']) && !empty($filters['q'])){
			$query->whereRaw("nombre_evaluacion_emprendimiento ilike '%".$filters['q']."%'");
		}

		$query->orderby('text', 'asc');
		return $query->get();
	}

	public static function getFileAgreement($purchase) {
    $queryPurchaseAgreement = AcuerdoCompra::query();
    $queryPurchaseAgreement->select('adjunto.codigo_adjunto', 'adjunto.direccion_adjunto');
    $queryPurchaseAgreement->join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'acuerdo_compra.cor_codigo_adjunto');
    $queryPurchaseAgreement->where('acuerdo_compra.codigo_compra', '=', $purchase);
    $dataPurchaseAgreement = $queryPurchaseAgreement->first();

    return $dataPurchaseAgreement;
  }

  public static function exportarEvaluacion($id) {
    list($codigo_compra) = TuProyecto::decryptSrcParams($id);

    if(!$codigo_compra || !is_numeric($codigo_compra)) {
      abort(403);
    }

    $purchase = Compra::find($codigo_compra);
    if (!$purchase instanceof Compra) {
      abort(404);
    }

    $query = Institucion::query();
    $query->select('institucion.logo_institucion', 'institucion.nombre_institucion');
    $query->where('institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
    $inst = $query->first();

    $queryProduct = Producto::query();
    $queryProduct->where('producto.codigo_producto', '=', $purchase->codigo_producto);
    $queryProduct->whereIn('producto.codigo_estado', [Estado::getState('active'), Estado::getState('pending'), Estado::getState('inactive')]);
    $product = $queryProduct->first();

    $queryBuyer = AcuerdoCompra::query();
    $queryBuyer->select('carta_compormiso_compra.nombre_empresa', 'carta_compormiso_compra.nombre_representante_legal', 'carta_compormiso_compra.apellidos_representante_legal', 'ciudad.nombre_ciudad');
    $queryBuyer->join('core.compra', 'acuerdo_compra.codigo_compra', '=', 'compra.codigo_compra');
    $queryBuyer->join('core.consumo_voucher', 'compra.codigo_compra', '=', 'consumo_voucher.codigo_compra');
    $queryBuyer->join('core.detalle_voucher', 'consumo_voucher.codigo_detalle_voucher', '=', 'detalle_voucher.codigo_detalle_voucher');
    $queryBuyer->join('core.carta_compormiso_compra', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'carta_compormiso_compra.codigo_registro_fase_usuario');
    $queryBuyer->join('core.ciudad', 'ciudad.codigo_ciudad', '=', 'carta_compormiso_compra.cor_codigo_ciudad');
    $queryBuyer->where('acuerdo_compra.codigo_compra', '=', $codigo_compra);
    $buyer = $queryBuyer->first();

    $querySeller = Usuario::query();
    $querySeller->select('usuario.nombre_usuario');
    $querySeller->where('codigo_usuario', '=', $product->seg_codigo_usuario);
    $querySeller->where('codigo_estado', '=', Estado::getState('active'));
    $seller = $querySeller->first();

    $queryRepSeller = Usuario::query();
    $queryRepSeller->select('usuario.nombre_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona');
    $queryRepSeller->join('core.persona', 'persona.codigo_usuario', '=', 'usuario.codigo_usuario');
    $queryRepSeller->where('usuario.codigo_usuario', '=', $product->codigo_usuario);
    // $queryRepSeller->where('usuario.codigo_estado', '=', Estado::getState('active'));
    $repSeller = $queryRepSeller->first();

    $queryPuchaser = AcuerdoCompra::query();
    $queryPuchaser->select('acuerdo_compra.codigo_acuerdo_compra','acuerdo_compra.fecha_aprobacion_asesor_acuerdo_compra', 'acuerdo_compra.fecha_acuerdo_aprobacion_compra', 'acuerdo_compra.fecha_cargue_entregables', 'acuerdo_compra.fecha_aceptacion_entregables', 'acuerdo_compra.fecha_aprobacion_entregables');
    $queryPuchaser->where('acuerdo_compra.codigo_compra', '=', $codigo_compra);
    $purchaser = $queryPuchaser->first();

    $queryDeliverable = EntregableAcuerdo::query();
    $queryDeliverable->select('entregable_acuerdo.descripcion_entregable', 'adjunto.codigo_adjunto', 'adjunto.nombre_adjunto', 'adjunto.direccion_adjunto');
    $queryDeliverable->join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'entregable_acuerdo.codigo_adjunto');
    $queryDeliverable->where('entregable_acuerdo.codigo_acuerdo_compra', '=', $purchaser->codigo_acuerdo_compra);
    $queryDeliverable->orderBy('adjunto.codigo_adjunto', 'ASC');
    $deliverables = $queryDeliverable->get();

    $queryDeliverableFinal = AcuerdoCompra::query();
    $queryDeliverableFinal->select('adjunto.codigo_adjunto','adjunto.nombre_adjunto', 'adjunto.direccion_adjunto');
    $queryDeliverableFinal->join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'acuerdo_compra.codigo_adjunto');
    $queryDeliverableFinal->where('acuerdo_compra.codigo_compra', '=', $codigo_compra);
    $deliverableFinal = $queryDeliverableFinal->first();

    $queryUserAssigned = AcuerdoCompra::query();
    $queryUserAssigned->select('usuario.nombre_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona');
    $queryUserAssigned->join('core.consumo_voucher', 'consumo_voucher.codigo_compra', '=', 'acuerdo_compra.codigo_compra');
    $queryUserAssigned->join('core.detalle_voucher', 'detalle_voucher.codigo_detalle_voucher', '=', 'consumo_voucher.codigo_detalle_voucher');
    $queryUserAssigned->join('core.carta_compormiso_compra', 'carta_compormiso_compra.codigo_registro_fase_usuario', '=', 'detalle_voucher.codigo_registro_fase_usuario');
    $queryUserAssigned->join('core.asignacion_marketplace', 'asignacion_marketplace.codigo_registro_fase_usuario', '=','carta_compormiso_compra.codigo_registro_fase_usuario');
    $queryUserAssigned->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_marketplace.codigo_usuario');
    $queryUserAssigned->join('core.persona', 'persona.codigo_usuario', '=', 'usuario.codigo_usuario');
    $queryUserAssigned->where('acuerdo_compra.codigo_compra', '=', $codigo_compra);
    $queryUserAssigned->where('asignacion_marketplace.codigo_estado', '=', Estado::getState('active'));
    $userAssigned = $queryUserAssigned->first();

    $queryEvaluation = EvaluacionCompra::query();
    $queryEvaluation->select('evaluacion_compra.codigo_evaluacion_compra','tipo_evaluacion_compra', 'evaluacion_compra.promedio_evaluacion');
    $queryEvaluation->where('evaluacion_compra.codigo_compra', '=', $codigo_compra);
    $averageItem = $queryEvaluation->get();

    foreach($averageItem as $result) {
      if ($result->tipo_evaluacion_compra == Compra::getTypeUser('purchaser')) {
        $resultEvalSeller = $result->promedio_evaluacion;
      } else {
        $resultEvalBuyer = $result->promedio_evaluacion;
      }
    }

    $data = [];
    $data['title'] = 'ACTA DE APROBACIÓN DE ENTREGABLES';
    $data['logo_inst'] = $inst->logo_institucion;
    $data['name_inst'] = $inst->nombre_institucion;

    $data['date_approval'] = $purchaser->fecha_aprobacion_asesor_acuerdo_compra;
    $data['name_seller'] = $seller->nombre_usuario;

    $data['date_upload_deliverables_seller'] = $purchaser->fecha_cargue_entregables;
    $data['date_approve_deliverables_buyer'] = $purchaser->fecha_aceptacion_entregables;
    $data['date_approve_deliverables_asesor'] = $purchaser->fecha_aprobacion_entregables;

   // $data['user_assign_name'] = $userAssigned->nombre_usuario.' '.$userAssigned->primer_apellido_persona.' '.$userAssigned->primer_apellido_persona;

    $data['name_company'] = $buyer->nombre_empresa;
    $data['name_buyer'] = $buyer->nombre_representante_legal.' '.$buyer->apellidos_representante_legal;
    $data['city_company'] = $buyer->nombre_ciudad;

    $data['rep_seller'] = $repSeller->nombre_usuario.' '.$repSeller->primer_apellido_persona.' '.$repSeller->segundo_apellido_persona;

    $data['name_product'] = $product->nombre_producto;
    $data['price_product'] = $product->precio_producto;
    $data['date_init_product'] = $purchaser->fecha_acuerdo_aprobacion_compra;

    $data['deliverables'] = $deliverables;
    $data['deliverable_final'] = $deliverableFinal;

    $data['result_evaluation_buyer'] = $resultEvalBuyer;
    $data['result_evaluation_seller'] = $resultEvalSeller;


    return $data;
  }

  public static function getQuestionsEvaluations() {
   	  $query = EvaluacionEmprendimiento::query();
   	  $query->select('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento','evaluacion_emprendimiento.nombre_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', 'item_evaluacion_emprendimiento.nombre_item_evaluacion', 'grupo_item_eval.codigo_grupo_item_eval', DB::raw("UPPER(grupo_item_eval.nombre_grupo_item_eval) as nombre_grupo_item_eval"));
   	  $query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=','evaluacion_emprendimiento.codigo_evaluacion_emprendimiento');
   	  $query->leftJoin('core.grupo_item_eval', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
   	  $query->where('evaluacion_emprendimiento.codigo_institucion', '=', session('institution')->codigo_institucion);
   	  $query->whereNotIn('item_evaluacion_emprendimiento.codigo_item_evaluacion', array(145, 146, 147));
   	  $query->orderBy('item_evaluacion_emprendimiento.codigo_item_evaluacion', 'ASC');
   	  $data = $query->get();

   	  return $data;
  }

  public static function getGroupsEvaluations() {
   	  $query = EvaluacionEmprendimiento::query();
   	  $query->select('grupo_item_eval.codigo_grupo_item_eval', 'grupo_item_eval.nombre_grupo_item_eval');
      $query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=','evaluacion_emprendimiento.codigo_evaluacion_emprendimiento');
      $query->join('core.grupo_item_eval', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $query->where('evaluacion_emprendimiento.codigo_institucion', '=', session('institution')->codigo_institucion);
      $query->orderBy('grupo_item_eval.codigo_grupo_item_eval', 'ASC');
      $data = $query->distinct()->get();

      return $data;
  }

  public static function getAnswersEvaluation($evaluation) {

    $query = AsignacionJurado::query();
    $query->select('asignacion_jurado.codigo_asignacion_jurado', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', 'opc_eval_pregunta.nombre_opc_eval_pregunta as respuesta', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval', 'evaluacion_item_emprendimiento.puntaje_resul_eval_empren');

    // User project
    $query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
    $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
    $query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
    $query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
    $query->leftJoin('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
    $query->leftJoin('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
    $query->leftJoin('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

    $query->where('fase.codigo_evaluacion_emprendimiento', $evaluation);
    $query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
    $query->orderBy('numero_registro_convocatoria', 'asc');
    $query->orderBy('evaluacion_item_emprendimiento.codigo_item_evaluacion', 'asc');
    $data = $query->get();

    return $data;
  }

  public static function getCommentaryEvaluation($evaluation) {
    $query = AsignacionJurado::query();
    $query->select(DB::raw("UPPER(resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion) as commentary"), "resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion");
    $query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
    $query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
    $query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
    $query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
    $query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
    $query->where('fase.codigo_evaluacion_emprendimiento', '=', $evaluation);
    $query->orderBy('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', 'ASC');
    $data = $query->get();

    return $data;
  }

  public static function getOtherQuestions($evaluation) {
    $query = AsignacionJurado::query();
    $query->select( 'item_evaluacion_emprendimiento.nombre_item_evaluacion');
    $query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
    $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
    $query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
    $query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
    $query->leftJoin('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
    $query->leftJoin('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
    $query->leftJoin('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
    $query->where('fase.codigo_evaluacion_emprendimiento', $evaluation);
    $query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
    $query->whereIn('evaluacion_item_emprendimiento.codigo_item_evaluacion', array(145, 146, 147));
    $data = $query->distinct()->get();

    return $data;

  }

  public static function getForm($id)
	{
		$query = EvaluacionEmprendimiento::query();
		$query->select('evaluacion_emprendimiento.*');
    $query->where('evaluacion_emprendimiento.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', $id);
		return $query->first();
	}

}
