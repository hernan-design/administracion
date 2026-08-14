<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use DB;

class CategoriaContenido extends Model
{
  protected $table = 'core.categoria_contenido';

  protected $primaryKey = 'codigo_categoria_contenido';

  public $timestamps = false;

  protected $fillable = ['codigo_categoria_contenido', 'nombre_categoria_contenido'];


  public static function getCategories($event){
    $query = CategoriaContenido::query();
    $query->select('categoria_contenido.codigo_categoria_contenido', 'categoria_contenido.nombre_categoria_contenido', DB::raw("(CASE WHEN cat_conten_evento.codigo_categoria_contenido IS NOT NULL THEN true ELSE false END) as comparator"));
    $query->join('param.cat_conten_insti', 'cat_conten_insti.codigo_categoria_contenido', '=', 'categoria_contenido.codigo_categoria_contenido');
    $query->leftJoin('core.cat_conten_evento', function($join) use($event) {
      $join->on('categoria_contenido.codigo_categoria_contenido', '=', 'cat_conten_evento.codigo_categoria_contenido')
        ->where('cat_conten_evento.codigo_evento', '=', $event);
    });
    $query->where('cat_conten_insti.codigo_institucion', '=', session('institution')->codigo_institucion);
    $query->where('cat_conten_insti.codigo_estado', '=', Estado::getState('active'));
    return $query->get();
  }

}
