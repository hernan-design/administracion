<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
   	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.parametro';

   	protected $primaryKey = 'codigo_parametro';

  	public static function getParameter($parameter) {
    	switch ($parameter) :
	    	case 'logo_header': return 1;
			case 'color_header': return 2;
			case 'background_header': return 3;
			case 'color_active_header': return 4;
			case 'color_body': return 5;
			case 'background_body': return 6;
			case 'color': return 7;
			case 'color_title': return 8;
			case 'background': return 9;
			case 'imagen': return 10;
			case 'color_hover': return 11;
			case 'primary_color': return 12;
			case 'secondary_color': return 13;
			case 'position': return 14;
			case 'title': return 15;
			case 'text': return 16;
			case 'background_hover': return 17;
			case 'background_title': return 18;
			case 'color_icon': return 19;
			case 'fontsize': return 20;
			case 'fontsize_title': return 21;
			default: return 'Not type identified';
		endswitch;
    }

  	/**
	 * Defining Relations
	*/

	public function tipo()
    {
        return $this->hasOne('TipoParametro', 'codigo_tipo_parametro', 'codigo_tipo_parametro');
   	}
}
