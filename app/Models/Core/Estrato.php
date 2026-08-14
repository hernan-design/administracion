<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class Estrato extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.estrato';

	protected $primaryKey = 'codigo_estrato';

    public $timestamps = false;

    public static function stratum()
    {
        $cache = sprintf('_%s', '_get_stratum_list');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = Estrato::query();
            $query->select('codigo_estrato', 'nombre_estrato');
            $query->orderBy('nombre_estrato', 'asc');

            return $query->get();
        });
    }
}
