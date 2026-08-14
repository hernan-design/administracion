<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntaFrecuente extends Model
{
  use HasFactory;

  protected $table = 'core.preguntas_frecuentes';

  protected $primaryKey = 'codigo_preguntas_frecuentes';

  protected $fillable = [
    'codigo_estado', 'texto_preguntas_frecuentes', 'respuesta_preguntas_frecuentes'
  ];

  protected $hidden = ['pivot'];

  public $timestamps = false;
}
