<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class VisibilidadInstitucion extends Model
{
    protected $table = 'param.visibilidad_instituci';

	protected $primaryKey = 'codigo_institucion';

	public $timestamps = false;
}
