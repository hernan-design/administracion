<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Input;

class CategoriaProyecto extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.categoria_proyecto';

	protected $primaryKey = 'codigo_categoria_proyecto';

	public $timestamps = false;

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_categoria_proyecto'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getIndustry($category)
	{
		$query = CategoriaProyecto::query();
		$query->select('codigo_categoria_proyecto', 'nombre_categoria_proyecto', 'descripcion_categoria_proyecto', 'codigo_estado', 'codigo_institucion');
		$query->where('codigo_categoria_proyecto', '=', $category);
		return $query->first();
	}

	public static function industries(array $filters = [])
	{
		$query = CategoriaProyecto::query();
		$query->select('codigo_categoria_proyecto as id', 'nombre_categoria_proyecto as text');
		$query->where('codigo_institucion', '=', session('institution')->codigo_institucion);

		if(isset($filters['q']) && !empty($filters['q'])){
			$query->whereRaw("nombre_categoria_proyecto ilike '%".$filters['q']."%'");
		}

		$query->where('codigo_estado', '=', Estado::getState('active'));

		$query->orderBy('text', 'asc');
		return $query->get();
	}

	public static function getIndustries(array $filters = [])
	{
		$query = CategoriaProyecto::query();
		$query->select('codigo_categoria_proyecto', 'nombre_categoria_proyecto', 'descripcion_categoria_proyecto', 'codigo_estado', 'codigo_institucion');
		$query->where('codigo_institucion', '=', session('institution')->codigo_institucion);

		if(isset($filters['nombre_categoria_proyecto']) && !empty($filters['nombre_categoria_proyecto'])){
			$query->whereRaw("nombre_categoria_proyecto ilike '%".$filters['nombre_categoria_proyecto']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where('codigo_estado', '=', $filters['codigo_estado']);
		}

		$query->orderBy('nombre_categoria_proyecto', 'asc');
		return $query->get();
	}
}
