<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ReporteUnico extends Model
{
  protected $table = 'core.reporte_unico';

  protected $primaryKey = 'codigo_reporte_unico';

  public $timestamps = false;

  protected $fillable = ['codigo_registro_convocatoria', 'puntaje_c1_reto12', 'puntaje_c2_reto12', 'puntaje_c3_reto12', 'puntaje_c4_reto12'];

}
