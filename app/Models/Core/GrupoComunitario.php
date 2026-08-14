<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class GrupoComunitario extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.grupo_comunitario';

   	protected $primaryKey = 'codigo_grupo_comunitario';

    public $timestamps = false;
    
    public static function communityGroup()
    {
        $cache = sprintf('_%s', '_get_community_group');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = GrupoComunitario::query();
            $query->select('codigo_grupo_comunitario', 'nombre_grupo_comunitario');
            $query->orderBy('nombre_grupo_comunitario', 'asc');

            return $query->get();
        });
    }
}
