<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ClaseTipoEvento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.clase_tipo_evento';

   	protected $primaryKey = 'codigo_clase_tipo_evento';

   	public $timestamps = false;

	public static function getClass($class) {
    	switch ($class) :
		    case 'user': return 1;
		    case 'platform': return 2;
			default: return 'Not class type event identified';
		endswitch;
    }
}
