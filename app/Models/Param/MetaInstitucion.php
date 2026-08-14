<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class MetaInstitucion extends Model
{
    protected $table = 'param.meta_institucion';

	protected $primaryKey = 'codigo_institucion';

	public $timestamps = false;
}
