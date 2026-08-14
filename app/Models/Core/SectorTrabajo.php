<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class SectorTrabajo extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.sector_trabajo';

   	protected $primaryKey = 'codigo_sector_trabajo';

    public $timestamps = false;
    
    public static function workSector()
    {
        $cache = sprintf('_%s', '_get_work_sector');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = SectorTrabajo::query();
            $query->select('codigo_sector_trabajo', 'nombre_sector_trabajo');
            $query->orderBy('nombre_sector_trabajo', 'asc');

            return $query->get();
        });
    }
}
