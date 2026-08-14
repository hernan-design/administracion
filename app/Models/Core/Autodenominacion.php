<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class Autodenominacion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.autodenominacion';

   	protected $primaryKey = 'codigo_autodenominacion';

    public $timestamps = false;
    
    public static function selfDomination()
    {
        $cache = sprintf('_%s', '_get_self_domination');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = Autodenominacion::query();
            $query->select('codigo_autodenominacion', 'nombre_autodenominacion');
            $query->orderBy('nombre_autodenominacion', 'asc');

            return $query->get();
        });
    }
}
