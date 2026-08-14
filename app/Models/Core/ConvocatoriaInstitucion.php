<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ConvocatoriaInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.convocatoria_institucion';

   	public $incrementing = false;

	public $timestamps = false;
}
