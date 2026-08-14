<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Models\Core\Canal;

class ContenidoCanal extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.contenido_canal';

	protected $primaryKey = 'codigo_contenido_canal';

	public $timestamps = false;

	public static function getChannelsContent($tipo_contenido, $id_contenido)
	{
		$query = Canal::query();
		$query->select('contenido_canal.codigo_contenido_canal', 'canal.codigo_canal', 'nombre_canal', DB::raw("(CASE WHEN contenido_canal.codigo_canal IS NOT NULL THEN true ELSE false END) as comparator"));
		$query->leftJoin('core.contenido_canal', function ($join) use($tipo_contenido, $id_contenido) {
			$join->on('canal.codigo_canal', '=', 'contenido_canal.codigo_canal')
				->where('codigo_recurso_modulo', '=', $id_contenido)
				->where('codigo_modulo', '=', $tipo_contenido);
		});

		$query->where('canal.codigo_institucion', '=', session('institution')->codigo_institucion);
		return $query->get();
	}

	public static function getChannelsContentList($tipo_contenido, $id_contenido)
	{
		$query = Canal::query();
		$query->select('contenido_canal.codigo_contenido_canal', 'canal.codigo_canal', 'nombre_canal');
		$query->join('core.contenido_canal', function ($join) use($tipo_contenido, $id_contenido) {
			$join->on('canal.codigo_canal', '=', 'contenido_canal.codigo_canal')
				->where('codigo_recurso_modulo', '=', $id_contenido)
				->where('codigo_modulo', '=', $tipo_contenido);
		});

		$query->where('canal.codigo_institucion', '=', session('institution')->codigo_institucion);
		return $query->get();
	}

	public static function getChannelContent($channel, $module_resource, $module)
	{
		$query = ContenidoCanal::query();
		$query->where('codigo_canal', '=', $channel);
		$query->where('codigo_recurso_modulo', '=', $module_resource);
		$query->where('codigo_modulo', '=', $module);
		return $query->first();
	}
}
