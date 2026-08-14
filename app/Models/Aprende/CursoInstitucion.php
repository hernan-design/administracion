<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class CursoInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.curso_institucion';

	public $incrementing = false;

	public $timestamps = false;
}
