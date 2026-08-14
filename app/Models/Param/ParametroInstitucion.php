<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class ParametroInstitucion extends Model
{
    protected $table = 'param.parametro_institucion';

	protected $primaryKey = 'codigo_institucion';

	public $timestamps = false;
}
