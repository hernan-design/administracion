<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model, App\Models\Seguridad\Modulo;

class ParametroModulo extends Model
{
    protected $table = 'param.parametro_modulo';

	protected $primaryKey = 'codigo_institucion';

	public $timestamps = false;

	public static function getValueParameters($institucion) {
		$query = ParametroModulo::query();
		$query->leftJoin('seguridad.modulo', 'modulo.codigo_modulo', '=', 'parametro_modulo.codigo_modulo');
		$query->leftJoin('param.parametro', 'parametro.codigo_parametro', '=', 'parametro_modulo.codigo_parametro');
		$query->where('parametro_modulo.codigo_institucion', '=', $institucion);
		$query->orderBy('codigo_tipo_parametro', 'ASC');

		return $query->get();
	}
}
