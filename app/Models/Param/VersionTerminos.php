<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class VersionTerminos extends Model
{
    protected $table = 'param.version_terminos';

	protected $primaryKey = 'codigo_version_terminos';

	public $timestamps = false;

	public static function getValidationTerms($institucion, $type) {
		$query = VersionTerminos::query();
		$query->join('param.terminos_condiciones', function($join) use($institucion) {
			$join->on('terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones')
				->where('terminos_condiciones.codigo_institucion', '=', $institucion);
		});
		$query->where('terminos_condiciones.tipo_terminos', '=', $type);

		return $query->first();
	}

	public static function getTerms($institucion, $codeTerms) {
		$query = VersionTerminos::query();
		$query->select('version_terminos.codigo_version_terminos');
		$query->join('param.terminos_condiciones', 'terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones');
		$query->where('version_terminos.codigo_terminos_condiciones', '=', $codeTerms);
		$query->where('terminos_condiciones.codigo_institucion', '=', $institucion);

		return $query->first();
	}
}
