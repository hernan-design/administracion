<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class AdjuntoTema extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.adjunto_tema';
	protected $primaryKey = null;

   	public $incrementing = false;

   	public $timestamps = false;
}
