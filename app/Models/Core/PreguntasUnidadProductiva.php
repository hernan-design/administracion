<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

class PreguntasUnidadProductiva extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.preguntas_unidad_productiva';

   	protected $primaryKey = 'codigo_pregunta_u_p';

	public $timestamps = false;

  public static function getDiagnostics(array $filters = [])
	{


		$query = PreguntasUnidadProductiva::query();
		$query->select('codigo_pregunta_u_p', 'codigo_unidad_productiva', 'codigo_pregunta_encuesta');
		$query->orderBy('codigo_pregunta_u_p','desc');
		return $query->get();
	}

  public static function getDiagnostic($id)
	{


		$query = PreguntasUnidadProductiva::query();
		$query->select('codigo_pregunta_u_p', 'codigo_unidad_productiva', 'codigo_pregunta_encuesta');
    $query->where('codigo_pregunta_u_p', $id);
		$query->orderBy('codigo_pregunta_u_p','desc');
		return $query->get();
	}
}
