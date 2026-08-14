<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class GrupoSocial extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.grupo_social';

   	protected $primaryKey = 'codigo_grupo_social';

    public $timestamps = false;
    
    public static function socialGroup()
    {
        $cache = sprintf('_%s', '_get_social_group');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = GrupoSocial::query();
            $query->select('codigo_grupo_social', 'nombre_grupo_social');
            $query->orderBy('nombre_grupo_social', 'asc');

            return $query->get();
        });
    }
}
