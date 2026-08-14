<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SectorInteresInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.sector_interes_institucion';

	public $incrementing = false;

	public $timestamps = false;
}
