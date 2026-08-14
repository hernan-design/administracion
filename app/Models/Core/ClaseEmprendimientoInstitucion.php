<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ClaseEmprendimientoInstitucion extends Model
{
  protected $table = 'core.clase_emprendimiento_insti';

  protected $primaryKey = 'codigo_clase_empre_insti';

  public $timestamps = false;

  /**
   * Build.
   */
  public static function build($class)
  {
    $object = new \stdClass();
    $object->codigo_clase_empre_insti = $class->codigo_clase_empre_insti;
    $object->codigo_clase_emprendimiento = $class->codigo_clase_emprendimiento;
    $object->nombre_clase_emprendimiento = !is_null($class->nombre_clase_empren_insti) ? $class->nombre_clase_empren_insti : $class->nombre_clase_emprendimiento;
    return $object;
  }
}
