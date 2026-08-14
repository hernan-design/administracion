<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoFormulario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_encuesta';

	protected $primaryKey = 'codigo_tipo_encuesta';

	public $timestamps = false;

	public static function getSurveyType()
	{
		$query = TipoFormulario::query();
		$query->select('codigo_tipo_encuesta', 'nombre_tipo_encuesta');
		$query->orderBy('nombre_tipo_encuesta', 'asc');
		return $query->get();
	}
}
