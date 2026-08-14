<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class CategoriaCurso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.categoria_curso';

   	protected $primaryKey = 'codigo_categoria_curso';

	public $timestamps = false;

	public static function getCourseCategory()
	{
		$query = CategoriaCurso::query();
		$query->orderBy('nombre_categoria_curso', 'asc');
		return $query->get();
	}
}
