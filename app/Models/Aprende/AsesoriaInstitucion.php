<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class AsesoriaInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.asesoria_institucion';

	public $incrementing = false;

	public $timestamps = false;
}
