<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class GrupoPoblacional extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.grupo_poblacional';

   	protected $primaryKey = 'codigo_grupo_poblacional';

    public $timestamps = false;
    
    public static function populationGroup()
    {
        $cache = sprintf('_%s', '_get_population_group');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = GrupoPoblacional::query();
            $query->select('codigo_grupo_poblacional', 'nombre_grupo_poblacional');
            $query->orderBy('nombre_grupo_poblacional', 'asc');

            return $query->get();
        });
    }
}
