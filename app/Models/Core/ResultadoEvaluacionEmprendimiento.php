<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

//use App\ResultadoEvaluacionEmprendimiento;

class ResultadoEvaluacionEmprendimiento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.resultado_evaluacion_emprendimiento';

	protected $primaryKey = 'codigo_resultado_evaluacion';

	public $timestamps = false;
    /**
     * Realiza la consulta de los datos asociados al resultado evaluación.
     *
     * @access public
     * @static function
     * @param int $id_entity
     * @return null|object
     */
    public static function getUserPhaseRecord($id_entity)
    {

        $query = ResultadoEvaluacionEmprendimiento::query();
        $query->join('core.asignacion_jurado as aj', 'aj.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
        $query->join('core.registro_fase_usuario as rf', 'rf.codigo_registro_fase_usuario', '=', 'aj.codigo_registro_fase_usuario');
        $query->where('rf.codigo_usuario', $id_entity);
        $query->orderBy('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion','desc');
        $data = $query->first();

        return $data->codigo_resultado_evaluacion;
    }
	/**
     * Obtiene puntaje para el diagnóstico por grupo de preguntas.
	 *
     * @access public
	 * @static function
     * @param int $id_answer_evaluation, $id_group
     * @return object|boolean $answers|false
     */
    public static function getScoring($id_answer_evaluation, $id_group)
    {
        if ( !is_null($id_answer_evaluation) ) {

            $query = ResultadoEvaluacionEmprendimiento::query();
            $query->select('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion',
                'opc_eval_pregunta.nombre_opc_eval_pregunta as respuesta', 'grup_opc_eval_pregunta.nombre_grup_opc_eval_pregunta',
                'opc_eval_pregunta.valor_opc_eval_pregunta as puntaje', 'item_evaluacion_emprendimiento.codigo_item_evaluacion',
                'item_evaluacion_emprendimiento.codigo_grupo_item_eval',
                'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion as observacion',
				'evaluacion_item_emprendimiento.comentario_adicional'
            );
            $query->leftJoin('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
            $query->leftJoin('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
            $query->leftJoin('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
            $query->leftJoin('core.grup_opc_eval_pregunta', 'grup_opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 'item_evaluacion_emprendimiento.codigo_grup_opc_eval_pregunta');
            $query->where('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', $id_answer_evaluation);
            $query->whereIn('item_evaluacion_emprendimiento.codigo_grupo_item_eval', $id_group);
            $query->orderBy('item_evaluacion_emprendimiento.codigo_item_evaluacion','ASC');
            $answers = $query->get();

            return $answers;
        } else {
            return false;
        }
	}

}
