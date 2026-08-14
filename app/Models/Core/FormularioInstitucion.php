<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class FormularioInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.encuesta_institucion';

	public $incrementing = false;

	public $timestamps = false;
}
