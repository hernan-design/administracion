<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Estado;

class TerminosCondiciones extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.terminos_condiciones';

	protected $primaryKey = 'codigo_terminos_condiciones';
	   
	public $timestamps = false;

   	public static function getTypeTerms($type) {
    	switch ($type) :
		    case 'terms': return 1;
		    case 'privacy': return 2;
		    case 'sales': return 3;
			case 'mentoring': return 4;
			case 'evaluator': return 5;
			default: return 'Not event identified';
		endswitch;
	}

	public static function getNumberOfTerms($type) {
		switch ($type) {
			case 'all':
				return 5;
			break;			
		}
	}
	
	public static function termsAndConditions($type)
	{
		$query = TerminosCondiciones::query();
		$query->select('titulo_terminos_condiciones','texto_version_terminos','numero_version_terminos','fecha_inicio_terminos', 'version_terminos.codigo_version_terminos');
		$query->join('param.version_terminos', 'terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones');
		$query->whereRaw('version_actual_terminos = numero_version_terminos');
		$query->where('codigo_institucion', session('institution')->codigo_institucion);
		$query->where('codigo_estado', Estado::getState('active'));
		$query->where('tipo_terminos', $type);
		return $query->first();
	}

	public static function termsAndConditionsInstitution($type, $codigo_institucion)
	{
		$query = TerminosCondiciones::query();
		$query->select('titulo_terminos_condiciones','texto_version_terminos','numero_version_terminos','fecha_inicio_terminos', 'version_terminos.codigo_version_terminos');
		$query->join('param.version_terminos', 'terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones');
		$query->whereRaw('version_actual_terminos = numero_version_terminos');
		$query->where('codigo_institucion', $codigo_institucion);
		$query->where('codigo_estado', Estado::getState('active'));
		$query->where('tipo_terminos', $type);
		return $query->first();
	}
}
