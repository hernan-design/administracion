<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado;

class TipoEvento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.tipo_evento';

   	protected $primaryKey = 'codigo_tipo_evento';

   	public $timestamps = false;

	public static function getEventType($classEventType)
	{
		$query = TipoEvento::query();
		$query->select('tipo_evento.codigo_tipo_evento', 'nombre_tipo_evento', 'codigo_tipo_evento_calendario');
		$query->join('core.tipo_evento_calendario', 'tipo_evento.codigo_tipo_evento', '=', 'tipo_evento_calendario.codigo_tipo_evento');
		$query->join('core.calendario', function($join){
			$join->on('tipo_evento_calendario.codigo_calendario', '=', 'calendario.codigo_calendario')
				->where('calendario.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('calendario.codigo_estado', '=', Estado::getState('active'));
		});
		$query->where('tipo_evento_calendario.codigo_clase_tipo_evento', '=', $classEventType);
		$query->orderby('nombre_tipo_evento', 'asc');
		return $query->get();
	}
}
