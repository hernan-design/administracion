<?php
namespace  App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB;

class ConvocatoriaCategoria extends Model {

  protected $table = 'core.convocatoria_categoria';

  public $timestamps = false;

  public $incrementing = false;

  protected $fillable = ['codigo_convo_categoria', 'codigo_estado', 'codigo_categoria_proceso', 'codigo_convocatoria'];

  public static function getCategoriesProgram($convocatoria) {
    $query = CategoriaProceso::query();
    $query->select('codigo_cat_proceso_insti', 'categoria_proceso.codigo_categoria_proceso', 'nombre_categoria_proceso', 'descripcion_categoria_proceso', 'categoria_proceso_insti.codigo_estado', DB::raw("(CASE WHEN convocatoria_categoria.codigo_categoria_proceso IS NOT NULL THEN true ELSE false END) as comparator"));
    $query->join('core.categoria_proceso_insti', function($join) {
      $join->on('categoria_proceso.codigo_categoria_proceso', '=', 'categoria_proceso_insti.codigo_categoria_proceso')
        ->where('categoria_proceso_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
    });
    $query->leftJoin('core.convocatoria_categoria', function ($join) use($convocatoria) {
      $join->on('convocatoria_categoria.codigo_categoria_proceso', '=', 'categoria_proceso.codigo_categoria_proceso')
      ->where('convocatoria_categoria.codigo_estado', '=', Estado::getState('active'))
      ->where('convocatoria_categoria.codigo_convocatoria', '=', $convocatoria);
    });
    $query->where('codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('categoria_proceso_insti.codigo_estado', '=', Estado::getState('active'));
    $query->orderby('nombre_categoria_proceso', 'asc');
    return $query->get();
  }
}
