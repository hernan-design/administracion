<?php

namespace App\Models\Core;

use Validator,Input;

use App\Models\BaseModel, App\Models\Core\Estado;

class Fase extends BaseModel
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.fase';

   	protected $primaryKey = 'codigo_fase';

   	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_convocatoria', 'nombre_fase', 'descripcion_fase', 'fecha_inicio_fase', 'fecha_fin_fase', 'resumen_fase', 'codigo_evaluacion_emprendimiento', 'codigo_privacidad_contenido', 'rango_superior_evaluacion', 'rango_inferior_evaluacion', 'numero_maximo_evaluadores'];

	protected $nullable = ['codigo_evaluacion_emprendimiento'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_convocatoria' => 'required',
			'nombre_fase' => 'required',
			'descripcion_fase' => 'required',
			'fecha_inicio_fase' => 'required',
			'fecha_fin_fase' => 'required',
			'resumen_fase' => 'required',
			'codigo_privacidad_contenido' => 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

   	public static function getIdPhase($phase) {
    	switch ($phase) :
		    case 'general': return 1;
			case 'one_challenge': return 134;
			case 'one_challenge_empodera': return 204;
			case 'two_challenge': return 137;
			case 'two_challenge_empodera': return 213;
			case 'three_challenge': return 138;
			case 'three_challenge_empodera': return 214;
			case 'four_challenge': return 139;
			case 'challenge_1.2': return 150;
			case 'challenge_empodera_1.2': return 212;
			case 'register': return 202;
			case 'reg_emprende_bog_2019' : return 210;
			case 'diag_emprende_bog_2019' : return 219;
			case 'reg_mentores_provedores' : return 230;
			case 'reg_entidades_provedores': return 228;
			case 'empodera___2020': return 249;
			case 'empodera___2020_second__phase': return 250;
			case 'empodera__2020_intermediate__phase': return 251;
			case 'aldea__20_first__challenge': return 253;
			case 'aldea__20_challenge__1_2': return 254;
			case 'aldea__20_challenge__1_3': return 255;
			case 'aldea__20_second__challenge': return 256;
			case 'aldea__20_three__challenge': return 271;
			case 'terpel__20_inscription': return 237;
			case 'terpel__20_second': return 272;
			case 'terpel__20_three': return 273;
			case 'propace_course_register': return 299;
			case 'propace_course_second': return 300;
			case 'propace_course_two_register': return 307;
			case 'propace_course_two_payment': return 308;
			case 'propace_test_zarit': return 328;
			case 'propace_formacion': return 329;
			case 'propace_test_zarit_cierre': return 330;
			case 'propace_certificacion': return 331;
			case 'registro_pfc_abierto': return 335;
			case 'registro_pfc_formacion': return 336;
			case 'registro_pfc_abierta_particulares': return 338;
			case 'formacion_pfc_abierta_particulares': return 339;
			case 'registro_pfc_3_0': return 347;
			case 'formacion_pfc_3_0': return 369;
			default: return 'Not phase identified';
		endswitch;
    }

   	public static function getPhaseProgram($program)
    {
	    $query = Fase::query();
	    $query->where('codigo_convocatoria', $program);
	    $query->orderby('orden_fase','asc');
		return $query->get();
	}

	public static function getPhase($phase) {
    $query = Fase::query();
    $query->select('codigo_convocatoria', 'codigo_fase', 'nombre_fase', 'descripcion_fase', 'fecha_inicio_fase', 'fecha_fin_fase', 'fase_registro', 'resumen_fase', 'codigo_evaluacion_emprendimiento', 'codigo_privacidad_contenido', 'rango_superior_evaluacion', 'rango_inferior_evaluacion', 'numero_maximo_evaluadores', 'asignacion_especialidad', 'fase_entidad_editable', 'edicion_formulario_registro', 'fecha_edicion_formulario_registro');
    $query->where('codigo_fase', '=', $phase);

    return $query->first();
  }

	public static function getResourceSpecificOfPhase($program, $phase, $attach) {
    $query = Fase::query();
    $query->join('core.adjunto_fase', 'adjunto_fase.codigo_fase', '=', 'fase.codigo_fase');
    $query->join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'adjunto_fase.codigo_adjunto');
    $query->join('core.tipo_recurso_fase', 'tipo_recurso_fase.codigo_tipo_recurso_fase', '=', 'adjunto_fase.codigo_tipo_recurso_fase');
    $query->where('fase.codigo_convocatoria', '=', $program);
    $query->where('fase.codigo_fase', '=', $phase);
    $query->where('adjunto.codigo_adjunto', '=', $attach);
    return $query->get();
  }

	public static function getResourceOfPhase($convocatoria, $fase) {
	    $query = Fase::query();
	    $query->join('core.adjunto_fase', 'adjunto_fase.codigo_fase', '=', 'fase.codigo_fase');
	    $query->join('core.adjunto', 'adjunto.codigo_adjunto', '=', 'adjunto_fase.codigo_adjunto');
	    $query->join('core.tipo_recurso_fase', 'tipo_recurso_fase.codigo_tipo_recurso_fase', '=', 'adjunto_fase.codigo_tipo_recurso_fase');
	    $query->where('fase.codigo_convocatoria', '=', $convocatoria);
	    $query->where('fase.codigo_fase', '=', $fase);
	    return $query->get();
  }

	public static function getMaxPhase()
	{
		$query = Fase::query();
		$query->where('codigo_convocatoria', '=', Input::get('codigo_convocatoria'));
		return $query->max('orden_fase');
	}
}
