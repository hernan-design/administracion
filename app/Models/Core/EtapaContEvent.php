<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class EtapaContEvent extends Model
{
  protected $table = 'core.etapa_cont_event';

  protected $primaryKey = 'codigo_etapa_cont_event';

  public $timestamps = false;

  public $incrementing = false;

  protected $fillable = ['codigo_etapa_contenido', 'codigo_evento', 'codigo_estado'];
}
