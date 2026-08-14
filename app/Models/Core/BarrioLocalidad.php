<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class BarrioLocalidad extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.barrio_localidad';

	protected $primaryKey = 'codigo_barrio_localidad';

    public $timestamps = false;
    
    public static function zone(array $filters = [])
    {
        $query = BarrioLocalidad::query();
        $query->select('codigo_barrio_localidad as id', 'nombre_barrio_localidad as text');
        $query->where('codigo_estado', Estado::getState('active'));

        if(isset($filters['location']) && !empty($filters['location'])){
            $query->where('codigo_localidad_ciudad', $filters['location']);
        }

        if(isset($filters['q']) && !empty($filters['q'])){
            $query->whereRaw("nombre_barrio_localidad ilike '%".trim($filters['q'])."%'");
        }

        $query->orderBy('nombre_barrio_localidad', 'asc');

        return $query->limit(50)->get();
    }
}
