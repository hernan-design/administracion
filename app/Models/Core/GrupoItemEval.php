<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class GrupoItemEval extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.grupo_item_eval';

	protected $primaryKey = 'codigo_grupo_item_eval';

	public $timestamps = false;

	public static function getGroup($average) {
		switch ($average) :
			case 'average_differential': return 1;
			case 'average_commercial': return 2;
			case 'validation_commercial': return 3;
			case 'average_work_team': return 4;
			case 'enterprising_team': return 5;
			case 'market': return 6;
			case 'product': return 7;
			case 'productive_chains': return 8;
			case 'innovation_component': return 34;
			case 'market_2020': return 35;
			case 'enterprising_team_2020': return 36;
			case 'objetives': return 37;
			case 'recomendations': return 38;
			default: return 'Not average identified';
		endswitch;
	}

	public static function averageGroup($groupItem, $registro_fase_usuario, $evaluator) {

		switch ($groupItem->codigo_grupo_item_eval) :
		    // Evaluacion reto 2
		    case GrupoItemEval::getGroup('enterprising_team'):
				$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('enterprising_team'));
				return round(($query->sum('valor_opc_eval_pregunta') / 40) * 100);
		    break;

		    case GrupoItemEval::getGroup('market'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
				$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('market'));
				return round(($query->sum('valor_opc_eval_pregunta') / 25) * 100);
		    break;

		    case GrupoItemEval::getGroup('product'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('product'));
				return round(($query->sum('valor_opc_eval_pregunta') / 25) * 100);
		    break;

		    case GrupoItemEval::getGroup('productive_chains'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('productive_chains'));
				return round(($query->sum('valor_opc_eval_pregunta') / 10) * 100);
			break;

			case GrupoItemEval::getGroup('innovation_component'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('innovation_component'));
				return round((($query->sum('valor_opc_eval_pregunta') * 0.32) * 10), 2);
			break;
			
			case GrupoItemEval::getGroup('market_2020'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('market_2020'));
				return round((($query->sum('valor_opc_eval_pregunta') * 0.11) * 10), 2);
			break;
			
			case GrupoItemEval::getGroup('enterprising_team_2020'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('enterprising_team_2020'));
				return round(($query->sum('valor_opc_eval_pregunta') / 15) * 100);
				// switch ($variable) {
				// 	case 'value':
				// 		return round(($query->sum('valor_opc_eval_pregunta') / 15) * 100);
				// 	break;
				// 	case 'value':
				// 		return round(($query->sum('valor_opc_eval_pregunta') / 15) * 100);
				// 	break;
				// }
			break;
			
			case GrupoItemEval::getGroup('objetives'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('objetives'));
				return round(((($query->sum('valor_opc_eval_pregunta') * 0.12) * 100) / 12), 2);
			break;
			
			case GrupoItemEval::getGroup('recomendations'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('resultado_evaluacion_emprendimiento.codigo_usuario', $evaluator);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('recomendations'));
				return round(((($query->sum('valor_opc_eval_pregunta') * 0.08) * 100)/ 8), 2);
		    break;

			default:
				return null;
			break;
		endswitch;
	}

	public static function generalAverageGroup($groupItem, $registro_fase_usuario) {

		switch ($groupItem->codigo_grupo_item_eval) :
		    // Evaluacion reto 2
		    case GrupoItemEval::getGroup('enterprising_team'):
				$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('enterprising_team'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 40) * 100);
		    break;

		    case GrupoItemEval::getGroup('market'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
				$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('market'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 25) * 100);
		    break;

		    case GrupoItemEval::getGroup('product'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('product'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 25) * 100);
		    break;

		    case GrupoItemEval::getGroup('productive_chains'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 10);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('productive_chains'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 10) * 100);
			break;
			
			case GrupoItemEval::getGroup('innovation_component'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('innovation_component'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 32) * 100);
			break;
			
			case GrupoItemEval::getGroup('market_2020'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('market_2020'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 33) * 100);
			break;
			
			case GrupoItemEval::getGroup('enterprising_team_2020'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('enterprising_team_2020'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 15) * 100);
			break;
			
			case GrupoItemEval::getGroup('objetives'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('objetives'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 12) * 100);
			break;
			
			case GrupoItemEval::getGroup('recomendations'):
		    	$query = ResultadoEvaluation::query();
		        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		        $query->join('core.item_evaluacion_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
		        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
				$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
				$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro_fase_usuario);
				$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 24);
				$query->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval', GrupoItemEval::getGroup('recomendations'));
				return round( ( ($query->sum('valor_opc_eval_pregunta') / 3) / 8) * 100);
		    break;

			default:
				return null;
			break;
		endswitch;
	}
}
