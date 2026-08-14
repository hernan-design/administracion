<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class PaginaEncuesta extends Model
{
	protected $table = 'core.pagina_encuesta';

   	protected $primaryKey = 'codigo_pagina_encuesta';

   	public $timestamps = false;
	
}
