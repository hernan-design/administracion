<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class RedSocial extends Model
{
    protected $table = 'param.red_social';

	protected $primaryKey = 'codigo_red_social';

	public $timestamps = false;
}
