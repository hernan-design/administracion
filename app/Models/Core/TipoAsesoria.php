<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoAsesoria extends Model
{
    protected $table = 'core.tipo_asesoria';

    protected $primaryKey = 'codigo_tipo_asesoria';

    public $timestamps = false;

}
