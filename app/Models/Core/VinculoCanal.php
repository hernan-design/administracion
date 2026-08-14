<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Input;

class VinculoCanal extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.vinculo_canal';

	protected $primaryKey = 'codigo_vinculo_canal';

	public $timestamps = false;

	public static function getTableLinkChannel($link)
	{
		switch ($link) {
			case 'professions': return 1;
			default: return 'Not link identified';
		}
	}

	public static function getChannelLink(array $filters = [])
	{
		$query = VinculoCanal::query();
		$query->join('core.tabla_vinculo_canal', 'vinculo_canal.codigo_tabla_vinculo_canal', '=', 'tabla_vinculo_canal.codigo_tabla_vinculo_canal');

		switch ($filters['resource']) {
			case VinculoCanal::getTableLinkChannel('professions'):
				$query->join('core.profesion_persona', function($join){
					$join->on('vinculo_canal.codigo_recurso', '=', 'profesion_persona.codigo_profesion_persona')
						->where('profesion_persona.codigo_estado', '=', Estado::getState('active'));
				});

				if(Input::has('nombnre_profesion_persona')){
					$query->whereRaw("nombnre_profesion_persona ilike '%".$filters['nombnre_profesion_persona']."%'");
				}
		}

		$query->where('vinculo_canal.codigo_canal', '=', $filters['channel']);
		$query->where('vinculo_canal.codigo_institucion', '=', session('institution')->codigo_institucion);
		$query->where('vinculo_canal.codigo_estado', '=', Estado::getState('active'));
		$query->orderBy('vinculo_canal.codigo_recurso', 'asc');

		$total_entries = $query->get()->count();
		$items = $query->skip($filters['offset'])->take($filters['limit'])->get();

		$response = new \stdClass();
		$response->total_entries = $total_entries;
		$response->items = $items;

		return $response;
	}
}
