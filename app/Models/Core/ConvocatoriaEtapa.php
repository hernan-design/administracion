<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

class ConvocatoriaEtapa extends Model
{
	protected $table = 'core.convocatoria_etapa';

	public $timestamps = false;

	public $incrementing = false;

	protected $fillable = ['codigo_convocatoria_etapa', 'codigo_estado', 'codigo_convocatoria', 'codigo_etapa_proyecto'];

	public static function getStagePrograms($convocatoria) {
		$query = EtapaProyecto::query();
		$query->select('etapa_proyecto.codigo_etapa_proyecto', 'etapa_proyecto.nombre_etapa_proyecto', DB::raw("(CASE WHEN convocatoria_etapa.codigo_etapa_proyecto IS NOT NULL THEN true ELSE false END) as comparator"));
		$query->leftJoin('core.convocatoria_etapa', function ($join) use($convocatoria) {
			$join->on('convocatoria_etapa.codigo_etapa_proyecto', '=', 'etapa_proyecto.codigo_etapa_proyecto')
				->where('convocatoria_etapa.codigo_estado', '=', Estado::getState('active'))
				->where('convocatoria_etapa.codigo_convocatoria', '=', $convocatoria);
		});
		$query->where('etapa_proyecto.codigo_institucion', '=', session('institution')->codigo_institucion);
		$query->where('etapa_proyecto.codigo_estado', '=', Estado::getState('active'));
		return $query->get();
	}
}
