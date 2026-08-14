<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use DB;

class EtapaContenido  extends Model
{
  protected $table = 'core.etapa_contenido';

  protected $primaryKey = 'codigo_etapa_contenido';

  public $timestamps = false;

  protected $fillable = ['codigo_etapa_contenido', 'nombre_etapa_contenido'];

  public static function getStages($event)
  {
    $query = EtapaContenido::query();
    $query->select('etapa_contenido.codigo_etapa_contenido', 'etapa_contenido.nombre_etapa_contenido',  DB::raw("(CASE WHEN etapa_cont_event.codigo_etapa_contenido IS NOT NULL THEN true ELSE false END) as comparator"));
    $query->join('param.etapa_conten_insti', 'etapa_conten_insti.codigo_etapa_contenido', '=', 'etapa_contenido.codigo_etapa_contenido');
    $query->leftJoin('core.etapa_cont_event', function($join) use($event) {
      $join->on('etapa_contenido.codigo_etapa_contenido', '=', 'etapa_cont_event.codigo_etapa_contenido')
        ->where('etapa_cont_event.codigo_evento', '=', $event);
    });
    $query->where('etapa_conten_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('etapa_conten_insti.codigo_estado', '=', Estado::getState('active'));
    return $query->get();
  }
}
