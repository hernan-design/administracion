<?php

namespace App\Models\Core;

use App\Models\Core\Estado;

use Illuminate\Database\Eloquent\Model;

class CategoriaCanal extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.categoria_canal';

   	protected $primaryKey = 'codigo_categoria_canal';

   	public $timestamps = false;

	public static function getChannelCategories(array $filters = [])
	{
		$query = CategoriaCanal::query();
		$query->select('codigo_categoria_canal', 'nombre_categoria_canal', 'codigo_estado');
		$query->where('codigo_institucion', session('institution')->codigo_institucion);

		if(isset($filters['nombre_categoria_canal']) && !empty($filters['nombre_categoria_canal'])){
			$query->whereRaw("nombre_categoria_canal ilike '%".$filters['nombre_categoria_canal']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where('codigo_estado', '=', $filters['codigo_estado']);
		}

		$query->orderby('nombre_categoria_canal', 'desc');
		return $query->get();
	}

	public static function getCategoriesAll(array $filters = [])
	{
		$query = CategoriaCanal::query();
		$query->select('codigo_categoria_canal as id', 'nombre_categoria_canal as text');
		$query->where('codigo_institucion', session('institution')->codigo_institucion);
		$query->where('codigo_estado', '=', Estado::getState('active'));

		if(isset($filters['q']) && !empty($filters['q'])){
			$query->whereRaw("nombre_categoria_canal ilike '%".$filters['q']."%'");
		}

		if(isset($filters['id']) && !empty($filters['id'])){
			$query->where('codigo_categoria_canal', '=', $filters['id']);
		}

		$query->orderby('nombre_categoria_canal', 'desc');
		if(empty($filters['q']) && empty($filters['id'])) {
			return $query->limit( 50 )->get();
		}
		return $query->get();
	}

	public static function getCategoryById($id)
	{
		$query = CategoriaCanal::query();
		$query->select('codigo_categoria_canal', 'nombre_categoria_canal', 'codigo_estado');
		$query->where('codigo_categoria_canal', '=', $id);
		return $query->first();
	}
}
