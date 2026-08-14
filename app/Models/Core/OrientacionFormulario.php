<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrientacionFormulario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.orientacion_encuesta';

	protected $primaryKey = 'codigo_orientacion_encuesta';

	public $timestamps = false;

	public static function getSurveyOrientation()
	{
		$query = OrientacionFormulario::query();
		$query->select('codigo_orientacion_encuesta', 'nombre_orientacion_encuesta');
		$query->orderBy('nombre_orientacion_encuesta', 'asc');
		return $query->get();
	}
}
