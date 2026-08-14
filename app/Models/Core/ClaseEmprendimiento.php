<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ClaseEmprendimiento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.clase_emprendimiento';

	protected $primaryKey = 'codigo_clase_emprendimiento';

	public $timestamps = false;

	public static function getClass($class)
	{
		switch ($class) {
			case 'project': return 2;
			case 'enterprise': return 4;
			default: return 'Not class identified';
		}
	}

	public static function getClasses()
	{
		$query = ClaseEmprendimiento::query();
		$query->join('core.clase_emprendimiento_insti', function($join) {
			$join->on('clase_emprendimiento.codigo_clase_emprendimiento', '=', 'clase_emprendimiento_insti.codigo_clase_emprendimiento')
				->where('clase_emprendimiento_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('clase_emprendimiento_insti.codigo_estado', '=', Estado::getState('active'));
		});

		$query->orderby('nombre_clase_emprendimiento', 'asc');
		return $query->get();
	}
}
