<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\Aprende\Patrocinador;

class OrganizadorEvento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.organizador_evento';

   	protected $primaryKey = 'codigo_organizador_evento';

	public $timestamps = false;

	public static function getEventOrganizerById($code_event_organizer)
	{
		$query = OrganizadorEvento::query();
		$query->where('codigo_organizador_evento', '=', $code_event_organizer);
		return $query->first();
	}

	public static function getEventOrganizers(array $filters = [])
	{
		$query = Patrocinador::query();
		$query->select('codigo_organizador_evento', 'nombre_patrocinador');
		$query->join('core.organizador_evento', function($join) use($filters){
			$join->on('patrocinador.codigo_patrocinador', '=', 'organizador_evento.codigo_patrocinador')
				->where('organizador_evento.codigo_evento', '=', $filters['evento']);
		});
		return $query->get();
	}

	public static function getEventOrganizer(array $filters = [])
	{
		$query = Patrocinador::query();
		$query->select('patrocinador.codigo_patrocinador', 'nombre_patrocinador');
		$query->join('core.organizador_evento', function($join) use($filters){
			$join->on('patrocinador.codigo_patrocinador', '=', 'organizador_evento.codigo_patrocinador')
				->where('organizador_evento.codigo_patrocinador', '=', $filters['codigo_patrocinador'])
				->where('organizador_evento.codigo_evento', '=', $filters['codigo_evento']);
		});
		return $query->first();
	}
}
