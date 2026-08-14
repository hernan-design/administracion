<?php

namespace App\Models\param;

use Illuminate\Database\Eloquent\Model;

class TipoMenu extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'param.tipo_menu';

   	protected $primaryKey = 'codigo_tipo_menu';

	public static function getType($type) {
    	switch ($type) :
		    case 'admin': return 4;
			default: return 'Not type identified';
		endswitch;
    }
}
