<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Cache;
use App\Models\Core\TipoContenido, App\Models\Core\Estado;
class TipoContenido extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_contenido';

   	protected $primaryKey = 'codigo_tipo_contenido';

	public $timestamps = false;

	protected $fillable = ['codigo_tipo_contenido'];

    public static function getTypes()
    {
        $cache = sprintf('_%s_%s', 'blog_content_type', session('institution')->codigo_institucion);
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }

        return Cache::rememberForever($cache, function() {
            $query = TipoContenido::query();
            $query->join('param.tipo_contenido_insti', function($join) {
                $join->on('tipo_contenido.codigo_tipo_contenido', '=', 'tipo_contenido_insti.codigo_tipo_contenido')
                    ->where('tipo_contenido_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                    ->where('tipo_contenido_insti.codigo_estado', '=', Estado::getState('active'));
            });

            $query->orderby('nombre_tipo_contenido', 'asc');
            return $query->get();
        });
    }

	public static function getType($codigo_tipo_cont_institucion)
	{
		$query = TipoContenido::query();
		$query->join('param.tipo_contenido_insti', function($join) use($codigo_tipo_cont_institucion){
			$join->on('tipo_contenido.codigo_tipo_contenido', '=', 'tipo_contenido_insti.codigo_tipo_contenido')
				->where('tipo_contenido_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('tipo_contenido_insti.codigo_tipo_cont_institucion', '=', $codigo_tipo_cont_institucion)
				->where('tipo_contenido_insti.codigo_estado', '=', Estado::getState('active'));
		});

		return $query->first();
	}
}
