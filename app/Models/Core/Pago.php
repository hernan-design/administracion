<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'core.pago';

	protected $primaryKey = 'codigo_pago';

	public $timestamps = false;
}
