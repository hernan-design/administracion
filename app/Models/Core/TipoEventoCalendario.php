<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado;

class TipoEventoCalendario extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_evento_calendario';

   	protected $primaryKey = 'codigo_tipo_evento_calendario';

	public $timestamps = false;

	/**
	 * @param int $tipo_evento
	 * @return TipoEventoCalendario
	 */
	public static function getCalendarInstitution($tipo_evento)
	{
		$query = TipoEventoCalendario::query();
		$query->select('tipo_evento_calendario.codigo_tipo_evento_calendario','calendario.codigo_calendario');
		$query->join('core.calendario', 'tipo_evento_calendario.codigo_calendario', '=', 'calendario.codigo_calendario');
		$query->where('calendario.codigo_institucion', session('institution')->codigo_institucion);
		$query->where('tipo_evento_calendario.codigo_tipo_evento', '=', $tipo_evento);
		$query->where('tipo_evento_calendario.codigo_estado', Estado::getState('active'));
		return $query->first();
	}

}
