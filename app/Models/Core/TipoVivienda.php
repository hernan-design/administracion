<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class TipoVivienda extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.tipo_vivienda';

   	protected $primaryKey = 'codigo_tipo_vivienda';

    public $timestamps = false;
    
    public static function housingType()
    {
        $cache = sprintf('_%s', '_get_housing_type');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = TipoVivienda::query();
            $query->select('codigo_tipo_vivienda', 'nombre_tipo_vivienda');
            $query->orderBy('nombre_tipo_vivienda', 'asc');

            return $query->get();
        });
    }
}
