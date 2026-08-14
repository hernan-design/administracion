<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class AdjuntoRegistroFaseUsuario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.adjunto_rfu';

   	protected $primaryKey = 'codigo_adjunto_rfu';

   	public $timestamps = false;
}
