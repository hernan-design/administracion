<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado;

class TemaMentoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_asesoria';

	protected $primaryKey = 'codigo_tipo_asesoria';

	public $timestamps = false;

	protected $fillable = ['codigo_tipo_asesoria', 'nombre_tipo_asesoria', 'descripcion_tipo_asesoria', 'color_tipo_asesoria', 'imagen_tipo_asesoria', 'codigo_estado', 'url_tipo_asesoria'];

	/**
	* Themes Mentoring list.
	*/
	public static function getThemesList()
	{
	    $query = TemaMentoria::query();
        $query->where('codigo_estado', Estado::getState('active'));
		$query->where('codigo_institucion', session('institution')->codigo_institucion);
        $query->orderby('nombre_tipo_asesoria', 'asc');
        return $query->get();
   	}
}
