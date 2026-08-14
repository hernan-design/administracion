<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 'param.meta';

	protected $primaryKey = 'codigo_meta';

	public $timestamps = false;
}

