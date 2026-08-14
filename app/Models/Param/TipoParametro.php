<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class TipoParametro extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.tipo_parametro';

   	protected $primaryKey = 'codigo_tipo_parametro';

   	   	public static function getType($type) {
    	switch ($type) :
		    case 'color': return 1;
			case 'image_logo_header': return 2;
			case 'image': return 3;
			case 'value': return 4;
			case 'video': return 5;
			default: return 'Not type identified';
		endswitch;
    }
}
