<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class ListaOcupacion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.lista_ocupacion';

	protected $primaryKey = 'codigo_lista_ocupacion';

    public $timestamps = false;
    
    public static function employmentList()
    {
        $cache = sprintf('_%s', '_get_employment_list');
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
            $query = ListaOcupacion::query();
            $query->select('codigo_lista_ocupacion', 'nombre_lista_ocupacion');
            $query->orderBy('nombre_lista_ocupacion', 'asc');

            return $query->get();
        });
    }
}
