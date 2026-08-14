<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;
use App\Models;

use Validator, Input, Auth, DB, Lang, App, Cache;
use App\Models\Core\Estado;

class Menu extends Model
{
   	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.menu';

   	protected $primaryKey = 'codigo_menu';

	public $timestamps = false;

    public static function getType($type) {
        switch ($type) :
            case 'parent': return 1;
            case 'son': return 2;
            default: return 'Not type identified';
        endswitch;
    }

	public static function getDataMenu($type) {

		$menus = [];
        $query = MenuInstitucion::query();
        $query->select('menu.codigo_menu', 'menu_institucion.codigo_menu_institucion', 'tipo_menu_institucion', 'tipo_enlace_menu_institucion', 'par_codigo_menu_institucion as menu_padre', 'icono_menu_item', DB::raw('COALESCE(nombre_menu_institucion, nombre_menu_item) as nombre_item_menu'), DB::raw('COALESCE(ruta_menu_institucion, url_menu_item) as ruta_menu_institucion'), DB::raw('COALESCE(descripcion_menu_institucion, descripcion_menu_item) as descripcion_menu_item'));

        $query->join('param.item_menu', 'menu_institucion.codigo_item_menu', '=', 'item_menu.codigo_item_menu');
        $query->join('param.menu', 'menu_institucion.codigo_menu', '=', 'menu.codigo_menu');

        $query->leftJoin('param.submenu_institucion', 'menu_institucion.codigo_menu_institucion', '=', 'submenu_institucion.codigo_menu_institucion');

		$query->where('menu_institucion.codigo_estado', Estado::getState('active'));
		$query->where('menu_institucion.codigo_institucion', session('institution')->codigo_institucion);
		$query->where('menu.codigo_tipo_menu', $type);

        $query->orderby('menu_padre','asc')->orderby('orden_menu_institucion','asc');

        // Get posts
		$data = $query->get();
		if(count($data) > 0) {
			foreach ($data as $menu) {
				$menus[] = Menu::buildMenu($menu);
			}
		}
   		return $menus;
	}

	/**
	 * Build Menu.
	*/
   	public static function buildMenu($menu)
   	{
		$object = new \stdClass();
		$object->codigo_menu = $menu->codigo_menu;
		$object->codigo_menu_institucion = $menu->codigo_menu_institucion;
		$object->nombre_item_menu = $menu->nombre_item_menu;
		$object->tipo_enlace_menu_institucion = $menu->tipo_enlace_menu_institucion;
		$object->menu_padre = $menu->menu_padre;
		$object->icono_menu_item = $menu->icono_menu_item;
		$object->ruta_menu_institucion = $menu->ruta_menu_institucion;
		$object->descripcion_menu_item = $menu->descripcion_menu_item;
		$object->tipo_menu_institucion = $menu->tipo_menu_institucion;
		return $object;
   	}

   	public static function getOptionsMenu($type) {
        $cache = sprintf('_%s_%s_%s', 'options_menu', session('institution')->codigo_institucion, $type);
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }

        return Cache::rememberForever($cache, function() use($type) {
            $query = MenuInstitucion::query();
            $query->select('menu_institucion.codigo_menu_institucion', 'tipo_menu_institucion', 'tipo_enlace_menu_institucion', 'par_codigo_menu_institucion as menu_padre', 'icono_menu_item', 'clase_item_menu', DB::raw('COALESCE(nombre_menu_institucion, nombre_menu_item) as nombre_item_menu'), DB::raw('COALESCE(ruta_menu_institucion, url_menu_item) as ruta_menu_institucion'), DB::raw('COALESCE(descripcion_menu_institucion, descripcion_menu_item) as descripcion_menu_item'));

            $query->join('param.item_menu', 'menu_institucion.codigo_item_menu', '=', 'item_menu.codigo_item_menu');
            $query->join('param.menu', 'menu_institucion.codigo_menu', '=', 'menu.codigo_menu');
            $query->leftJoin('param.submenu_institucion', 'menu_institucion.codigo_menu_institucion', '=', 'submenu_institucion.codigo_menu_institucion');

    		$query->where('menu_institucion.codigo_estado', Estado::getState('active'));
    		$query->where('menu_institucion.codigo_institucion', session('institution')->codigo_institucion);
    		$query->where('menu.codigo_tipo_menu', $type);

            $query->orderby('menu_padre','asc')->orderby('orden_menu_institucion','asc');
            return $query->get();
        });
	}

    public static function getParents($type) {
        $items = self::getOptionsMenu($type);
        return $items->where('tipo_menu_institucion', self::getType('parent'));
    }

    public static function getChildren($type, $parent) {
        $items = Menu::getOptionsMenu($type);
        return $items->where('tipo_menu_institucion', self::getType('son'))->where('menu_padre', $parent);
    }
}
