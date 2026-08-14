<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class OrientacionCurso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.orientacion_curso';

	protected $primaryKey = 'codigo_orientacion_curso';

	public $timestamps = false;

	public static function courseOrientation($orientation)
	{
		switch ($orientation) {
			case 'internal': return 1;
			case 'external': return 2;
			default: return 'Not type course identified';
		}
	}

	public static function getCoursOrientation()
	{
		$query = OrientacionCurso::query();
		$query->orderBy('nombre_orientacion_curso', 'asc');
		return $query->get();
	}
}
