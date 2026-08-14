<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrientacionConvocatoria extends Model
{
    protected $table = 'core.orientacion_convocatoria';

    protected $primaryKey = 'codigo_orientacion_convocatoria';

    public static function getOrientation($orientation) {
    	switch ($orientation) :
		    case 'entity': return 1;
		    case 'user': return 2;
			default: return 'Not orientation identified';
		endswitch;
    }

	public static function getOrientationAll() {
		$query = OrientacionConvocatoria::query();
		return $query->get();
	}
}
