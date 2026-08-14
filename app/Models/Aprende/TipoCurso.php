<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class TipoCurso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.tipo_curso';

   	protected $primaryKey = 'codigo_tipo_curso';

	public $timestamps = false;

	public static function getCourseType()
	{
		$query = TipoCurso::query();
		$query->orderby('nombre_tipo_curso', 'asc');
		return $query->get();
	}

	public static function getCourseTypeById($code_type_course)
	{
		$query = TipoCurso::query();
		$query->where('codigo_tipo_curso', $code_type_course);
		return $query->first();
	}
}
