<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class NivelEducacion extends Model
{
    protected $table = 'core.nivel_educacion';

    protected $primaryKey = 'codigo_nivel_educacion';

    public $timestamps = false;

    public static function levelsEducation()
    {
        $cache = sprintf('_%s_%s', '_get_level_education', session('institution')->codigo_institucion);
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = self::query();
            $query->select('codigo_nivel_educacion', 'nombre_nivel_educacion');
            $query->orderby('orden_nivel_educacion', 'asc');
            return $query->get();
        });
    }
}
