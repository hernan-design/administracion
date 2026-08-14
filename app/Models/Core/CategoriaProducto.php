<?php

namespace App\Models\Core;

use App\RegistroSesion;
use Illuminate\Database\Eloquent\Model;

use Validator, Input, Auth, DB, Lang, App, URL, Request, Cache;

use App\Models\Core\Estado;

class CategoriaProducto extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.categoria_producto';

	protected $primaryKey = 'codigo_categoria_producto';

	public $timestamps = false;

	public static function getSubcategorylist()
    {
        $cache = sprintf('_%s_%s', 'categories_product_list', session('institution')->codigo_institucion);
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }
        return Cache::rememberForever($cache, function() {
            $query = CategoriaProducto::query();
            $query->select('categoria_producto_insti.codigo_categoria_prod_insti', 'nombre_categoria_producto');
            $query->join('core.categoria_producto_insti', function($join) {
                $join->on('categoria_producto.codigo_categoria_producto', '=', 'categoria_producto_insti.codigo_categoria_producto')
                    ->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                    ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
            });
            $query->orderby('nombre_categoria_producto','asc');
            return $query->get();
        });
    }

	public static function getCategories($register = null)
    {
        $categories = [];
        $query = CategoriaPadreProducto::query();
        $query->select('cat_padre_producto.codigo_cat_padre_producto', 'cat_padre_producto.nombre_cat_padre_producto');
        $query->join('core.categoria_producto_insti', function($join) {
            $join->on('cat_padre_producto.codigo_cat_padre_producto', '=', 'categoria_producto_insti.codigo_cat_padre_producto')
                ->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
        });

        // Get total posts
        $query->orderby('codigo_cat_padre_producto','asc');

        // Get posts
		    $data = $query->distinct()->get();

        if(count($data) > 0) {
          $cat_select = self::getCategoriesService($register)->pluck('cat_padre')->toArray();
          foreach ($data as $recom) {
            $categories[] = CategoriaProducto::buildCategory($recom, $cat_select);
          }
        }

		    return $categories;
    }

    public static function getCategoriesService($register = null) {
	    $query = CategoriaPadreProducto::query();
	    $query->select('categoria_producto_insti.codigo_cat_padre_producto as cat_padre', 'categoria_producto_insti.codigo_categoria_producto', 'cat_padre_producto.nombre_cat_padre_producto');

      $query->join('core.recom_producto_eval', function($join) use($register){
        $join->where('recom_producto_eval.codigo_registro_fase_usuario', '=', $register);
      });

      $query->join('core.categoria_producto_insti', function ($join) {
        $join->on('categoria_producto_insti.codigo_categoria_producto', '=', 'recom_producto_eval.codigo_categoria_producto')
          ->where('categoria_producto_insti.codigo_institucion', '=',  session('institution')->codigo_institucion)
          ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
      });

      $data = $query->distinct()->get();

      return $data;
    }

    public static function getCategorieForFather($id) {
      $query = CategoriaPadreProducto::query();
      $query->select('cat_padre_producto.codigo_cat_padre_producto', 'nombre_cat_padre_producto', 'categoria_producto_insti.codigo_categoria_producto as categoria_producto');
      $query->join('core.categoria_producto_insti', 'categoria_producto_insti.codigo_cat_padre_producto', '=', 'cat_padre_producto.codigo_cat_padre_producto');

      $query->where('cat_padre_producto.codigo_cat_padre_producto', '=', $id);

      $data = $query->get();

      return $data;
    }

    public static function getCategoriesSelected($register){
      $categories = [];
      $query = CategoriaProducto::query();
      $query->select('cat_padre_producto.nombre_cat_padre_producto');
      $query->join('core.categoria_producto_insti', function($join) {
        $join->on('categoria_producto.codigo_categoria_producto', '=', 'categoria_producto_insti.codigo_categoria_producto')
          ->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
          ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
      });

      $query->join('core.recom_producto_eval', function($join) use($register){
        $join->on('recom_producto_eval.codigo_categoria_producto', '=', 'categoria_producto_insti.codigo_categoria_producto')
          ->where('recom_producto_eval.codigo_registro_fase_usuario', '=', $register);
      });

      $query->join('core.cat_padre_producto', 'cat_padre_producto.codigo_cat_padre_producto', '=', 'categoria_producto_insti.codigo_cat_padre_producto');

      // Get total posts
      $query->groupBy('cat_padre_producto.codigo_cat_padre_producto');
      // Get posts
      return $query->get();
    }

    /**
	 * Build Category.
	*/
   	public static function buildCategory($category, $cat_select = [])
   	{
   	$selected = false;
		$object = new \stdClass();
		$object->codigo_categoria_producto = $category->codigo_cat_padre_producto;
		$object->nombre_categoria_producto = $category->nombre_cat_padre_producto;
		foreach ($cat_select as $cat) {
		  if ($cat == $category->codigo_cat_padre_producto) {
        $selected = true;
      }
    }
		$object->selected = $selected;

		return $object;
   	}


}
