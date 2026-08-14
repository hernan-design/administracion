<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class AdjuntoCurso extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.adjunto_curso';
	protected $primaryKey = 'codigo_curso';

   	public $incrementing = false;

   	public $timestamps = false;
}
