<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

use DB;

class Modulo extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'seguridad.modulo';

   	protected $primaryKey = 'codigo_modulo';

	public static function getModule($module) {
    	switch ($module) :
		    case 'forum': return 1;
		    case 'wall': return 2;
		    case 'header': return 3;
		    case 'banner': return 4;
		    case 'sitemap': return 7;
		    case 'projects': return 9;
		    case 'calendar': return 13;
		    case 'blog': return 14;
		    case 'mentoring': return 20;
		    case 'surveys': return 21;
		    case 'users': return 22;
		    case 'teams': return 23;
		    case 'learn': return 24;
		    case 'marketplace': return 25;
		    case 'calls': return 26;
		    case 'program': return 28;
		    case 'register': return 29;
			case 'negotiationplatform': return 52;
			case 'phases' : return 53;
			default: return 'Not module identified';
		endswitch;
    }

    public static function getTypeModule($module) {
    	switch ($module) :
		    case 'platform': return 1;
			case 'landing': return 2;
		    case 'administrator': return 3;
			default: return 'Not module identified';
		endswitch;
    }

	public static function getModules(Array $denied = [], Array $allowed = [], $filter)
	{
		$query = Modulo::query();
		$query->select('codigo_modulo', DB::raw('initcap(nombre_modulo) as nombre_modulo'));

		if(count($denied) > 0){
			$query->whereNotIn('codigo_modulo', $denied);
		}

		if(count($allowed) > 0){
			$query->whereIn('codigo_modulo', $allowed);
		}

		if($filter){
			$query->whereRaw("nombre_modulo ilike '%".$filter."%'");
		}

		$query->orderby('nombre_modulo', 'asc');
		return $query->get();
	}

	public static function getModulesParametrized() {
		$query = Modulo::query();
		$query->select('codigo_modulo', 'descripcion_modulo', DB::raw('initcap(nombre_modulo) as nombre_modulo'));
		$query->where('parametrizacion_modulo', '=', true);
		$query->orderBy('nombre_modulo', 'ASC');

		return $query->get();
	}
}
