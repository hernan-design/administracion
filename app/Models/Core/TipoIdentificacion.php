<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;

class TipoIdentificacion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_identificacion';

	protected $primaryKey = 'codigo_tipo_identificacion';

	public $timestamps = false;

	public static function identificationType()
	{
		$cache = sprintf('_%s_%s', '_get_identification_type', session('institution')->codigo_institucion);
		if (Cache::has($cache)) {
			return Cache::get($cache);
        }
        
        return Cache::rememberForever($cache, function() {
			$query = TipoIdentificacion::query();
			$query->select('tipo_id_institucion.codigo_tipo_identificacion', 'nombre_tipo_identificacion');
			$query->join('core.tipo_id_institucion', function($join){
				$join->on('tipo_identificacion.codigo_tipo_identificacion', '=', 'tipo_id_institucion.codigo_tipo_identificacion')
				->where('tipo_id_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
			});
			$query->orderby('nombre_tipo_identificacion', 'asc');
			return $query->get();
		});
	}
}
