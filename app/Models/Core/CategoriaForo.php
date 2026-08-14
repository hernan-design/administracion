<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CategoriaForo extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.categoria_foro';

   	protected $primaryKey = 'codigo_categoria_foro';

   	public $timestamps = false;

	public static function getCategoriesList(array $filter = [])
	{
		$query = CategoriaForo::query();
		$query->select('categoria_foro.codigo_categoria_foro', 'nombre_categoria_foro', 'categoria_foro_institucion.codigo_estado');
		$query->join('core.categoria_foro_institucion', function($join){
			$join->on('categoria_foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
				->where('categoria_foro_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		// Filters
		if(isset($filters['nombre_categoria_foro']) && !empty($filters['nombre_categoria_foro'])) {
 			$query->whereRaw("nombre_categoria_foro ilike '%".$filters['nombre_categoria_foro']."%'");
 		}

		$query->orderBy('nombre_categoria_foro', 'asc');
		return $query->get();
	}

	public static function getForumCategories(array $filters = [])
	{
		$query = CategoriaForo::query();
		$query->select('categoria_foro.codigo_categoria_foro', 'nombre_categoria_foro', 'categoria_foro_institucion.codigo_estado', 'categoria_foro.codigo_institucion');
		$query->join('core.categoria_foro_institucion', function($join){
			$join->on('categoria_foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
				->where('categoria_foro_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('categoria_foro_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		// Filters
		if(isset($filters['forum_category_name']) && !empty($filters['forum_category_name'])) {
 			$query->whereRaw("nombre_categoria_foro ilike '%".$filters['forum_category_name']."%'");
 		}

		$query->orderBy('nombre_categoria_foro', 'asc');

		$total_entries = $query->get()->count();
		$items = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $total_entries;
		$response->items = $items;

		return $response;
	}

	public static function getForumCategoriesList(array $filters = [])
	{
		$query = CategoriaForo::query();
		$query->select('categoria_foro.codigo_categoria_foro as id', 'nombre_categoria_foro as text');
		$query->leftJoin('core.categoria_foro_institucion', function($join){
			$join->on('categoria_foro.codigo_categoria_foro', '=', 'categoria_foro_institucion.codigo_categoria_foro')
				->whereNull("categoria_foro.codigo_institucion")
				->orWhere('categoria_foro.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('categoria_foro_institucion.codigo_estado', '=', Estado::getState('active'));
		});
    $query->where('categoria_foro.codigo_institucion', session('institution')->codigo_institucion);

		// Filters
		if(isset($filters['q']) && !empty($filters['q'])) {
 			$query->whereRaw("nombre_categoria_foro ilike '%".$filters['q']."%'");
 		}

		$query->orderBy('text', 'asc');
		$query->distinct();

		return $query->get();
	}
}
