<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class PuntajeRegistroFase extends Model
{
    protected $table = 'core.puntaje_registro_fase';

	protected $primaryKey = 'codigo_puntaje_registro_fase';

	public $timestamps = false;
}
