<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class LugarAplicacion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.lugar_aplicacion';

	protected $primaryKey = 'codigo_lugar_aplicacion';

	public $timestamps = false;

	public static function getPlace($place) {
    	switch ($place) :
		    case 'register': return 1;
			case 'programs': return 2;
			case 'projects': return 3;
			case 'surveys': return 4;
			case 'phases': return 5;
			case 'aprende': return 6;
			default: return 'Not place identified';
		endswitch;
	}
}
