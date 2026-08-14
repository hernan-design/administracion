<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class LocalidadCiudad extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.localidad_ciudad';

	protected $primaryKey = 'codigo_localidad_ciudad';

    public $timestamps = false;
    
    public static function locations(array $filters = [])
    {
        $query = LocalidadCiudad::query();
        $query->select('codigo_localidad_ciudad as id', 'nombrelocalidad_ciudad as text');
        $query->where('codigo_estado', Estado::getState('active'));

        if(isset($filters['city']) && !empty($filters['city'])){
            $query->where('codigo_ciudad', $filters['city']);
        }

        if(isset($filters['q']) && !empty($filters['q'])){
            $query->whereRaw("nombrelocalidad_ciudad ilike '%".trim($filters['q'])."%'");
        }

        $query->orderBy('nombrelocalidad_ciudad', 'asc');

        return $query->limit(50)->get();
    }
}
