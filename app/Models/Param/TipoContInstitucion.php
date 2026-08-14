<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class TipoContInstitucion extends Model
{
    protected $table = 'param.tipo_contenido_insti';

	protected $primaryKey = 'codigo_tipo_cont_institucion';

	public $timestamps = false;
}
