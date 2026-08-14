<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

class NivelMadurez extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.niveles_madurez';

   	protected $primaryKey = 'codigo_nivel_madurez';

	public $timestamps = false;

	public static function getDiagnosticsList(array $filters = [])
	{
		$query = NivelMadurez::query();
		$query->select('niveles_madurez.codigo_nivel_madurez as id', DB::raw('initcap(nombre_nivel_madurez) as text'));
		$query->join('core.diagnostico', function ($join){
			$join->on('niveles_madurez.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico');
		});

		// Order
		$query->orderBy('niveles_madurez.codigo_nivel_madurez','desc');
		return $query->get();
	}

  public static function getDiagnostics(array $filters = [])
	{


		$query = NivelMadurez::query();
		$query->select('niveles_madurez.codigo_nivel_madurez', 'nombre_nivel_madurez', 'descripcion_nivel_madurez', 'rango_minimo', 'rango_maximo', 'niveles_madurez.codigo_diagnostico', 'niveles_madurez.estado');
		$query->join('core.diagnostico', function ($join){
			$join->on('niveles_madurez.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico');
		});

		$query->orderBy('niveles_madurez.codigo_nivel_madurez','asc');
		return $query->get();
	}

  public static function getDiagnostic($id)
	{


		$query = NivelMadurez::query();
		$query->select('niveles_madurez.codigo_nivel_madurez', 'nombre_nivel_madurez', 'descripcion_nivel_madurez', 'rango_minimo', 'rango_maximo', 'niveles_madurez.codigo_diagnostico', 'niveles_madurez.estado');
		$query->join('core.diagnostico', function ($join){
			$join->on('niveles_madurez.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico');
		});

    $query->where('niveles_madurez.codigo_diagnostico', $id);

		$query->orderBy('niveles_madurez.codigo_nivel_madurez','desc');
		return $query->get();
	}
}
