<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class TareaMentoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.tarea_mentoria';

	protected $primaryKey = 'codigo_tarea_mentoria';

	public $timestamps = false;
}
