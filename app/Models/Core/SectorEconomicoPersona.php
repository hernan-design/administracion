<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SectorEconomicoPersona extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.sector_economico_persona';

	public $incrementing = false;

	public $timestamps = false;
}
