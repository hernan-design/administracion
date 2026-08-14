<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache, DB;
class Ciudad extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.ciudad';

   	protected $primaryKey = 'codigo_ciudad';

	public $timestamps = false;


	public static function getCities()
    {
        $cache = sprintf('_%s_%s', '_cities_institucion_', session('institution')->codigo_institucion);
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }

        return Cache::rememberForever($cache, function() {
            $query = Ciudad::query();
            $query->select('codigo_ciudad as key', DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as value"));
            $query->join('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

            $query->join('core.pais_institucion', function($join) {
                $join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
                    ->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                    ->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
            });
            $query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
            $query->whereNotNull('distrito_ciudad');
            $query->whereRaw("distrito_ciudad != ''");

            $query->orderby('distrito_ciudad','asc');
            $query->orderby('nombre_ciudad','asc');
            return $query->get();

        });
    }
}
