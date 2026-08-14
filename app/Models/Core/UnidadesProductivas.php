<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

class UnidadesProductivas extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.unidades_productivas';

   	protected $primaryKey = 'codigo_unidad_productiva';

	public $timestamps = false;

	public static function getDiagnosticsList(array $filters = [])
	{
		$query = UnidadesProductivas::query();
		$query->select('unidades_productivas.codigo_unidad_productiva as id', DB::raw('initcap(nombre_unidad_productiva) as text'));
		$query->join('core.diagnostico', function ($join){
			$join->on('unidades_productivas.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico');
		});

		// Order
		$query->orderBy('unidades_productivas.codigo_unidad_productiva','desc');
		return $query->get();
	}

  public static function getDiagnostics(array $filters = [])
	{


		$query = UnidadesProductivas::query();
		$query->select('unidades_productivas.codigo_unidad_productiva', 'nombre_unidad_productiva', 'descripcion_unidad_productiva', 'porcentaje', 'unidades_productivas.codigo_diagnostico', 'unidades_productivas.estado' );
		$query->join('core.diagnostico', function ($join){
			$join->on('unidades_productivas.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico');
		});

		$query->orderBy('unidades_productivas.codigo_unidad_productiva','desc');
		return $query->get();
	}

  public static function getDiagnostic($id)
	{


		$query = UnidadesProductivas::query();
		$query->select('unidades_productivas.codigo_unidad_productiva', 'nombre_unidad_productiva', 'descripcion_unidad_productiva', 'porcentaje', 'unidades_productivas.codigo_diagnostico', 'unidades_productivas.estado');
		$query->join('core.diagnostico', function ($join){
			$join->on('unidades_productivas.codigo_diagnostico', '=', 'diagnostico.codigo_diagnostico');
		});

    $query->where('unidades_productivas.codigo_diagnostico', $id);

		$query->orderBy('unidades_productivas.codigo_unidad_productiva','desc');
		return $query->get();
	}
}
