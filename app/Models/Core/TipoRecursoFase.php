<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoRecursoFase extends Model
{
  protected $table = 'core.tipo_recurso_fase';

  public $timestamps = false;

  public $incrementing = false;

  protected $fillable = ['codigo_tipo_recurso_fase', 'nombre_tipo_recurso_fase', 'icono_tipo_recurso_fase'];

  public static function getAllResourcesPhases() {
    $query = TipoRecursoFase::query();
    $query->select('*');
    return $query->get();
  }

  public static function getTypeResource($type) {
    switch($type) {
      case 'course': return 1;
      case 'event': return 2;
      case 'program': return 3;
      case 'article': return 4;
    }
  }
}
