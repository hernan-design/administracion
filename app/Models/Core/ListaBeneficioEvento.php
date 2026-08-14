<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

class ListaBeneficioEvento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.lista_beneficio_evento';

   	protected $primaryKey = 'codigo_lista_ben_even';

	public $timestamps = false;

	public static function benefitsEvent(array $filters = [])
	{
		$query = ListaBeneficioEvento::query();
		$query->join('core.beneficio_evento', 'lista_beneficio_evento.codigo_beneficio_evento', '=', 'beneficio_evento.codigo_beneficio_evento');
		$query->where('codigo_evento', $filters['event']);
		return $query->get();
	}
	
	public static function listBenefitsEvent(array $filters = [])
	{
		$query = UsuarioEvento::query();
		$query->select('beneficio_evento.codigo_beneficio_evento', 'nombre_beneficio_evento', 'lista_beneficio_evento.codigo_lista_ben_even',
			DB::raw('(CASE WHEN entrega_beneficio.codigo_entrega_beneficio IS NOT NULL THEN TRUE ELSE FALSE END) AS benefit') 
		);
		$query->leftJoin('core.asistencia_sesion', function($join) use($filters){
			$join->on('usuario_evento.codigo_usuario_evento', '=', 'asistencia_sesion.codigo_usuario_evento')
				->where('asistencia_sesion.codigo_sesion_evento', $filters['codigo_sesion_evento']);
		});

		$query->leftJoin('core.lista_beneficio_evento', 'lista_beneficio_evento.codigo_evento', '=', 'usuario_evento.codigo_evento');
		$query->join('core.beneficio_evento', 'beneficio_evento.codigo_beneficio_evento', '=', 'lista_beneficio_evento.codigo_beneficio_evento');

		$query->leftJoin('core.entrega_beneficio', function($join) use($filters){
			$join->on('entrega_beneficio.codigo_asistencia_sesion', '=', 'asistencia_sesion.codigo_asistencia_sesion')
				->on('lista_beneficio_evento.codigo_lista_ben_even', '=', 'entrega_beneficio.codigo_lista_ben_even');
		});

		$query->where('usuario_evento.codigo_usuario_evento', $filters['codigo_usuario_evento']);
		return $query->get();
	}
}
