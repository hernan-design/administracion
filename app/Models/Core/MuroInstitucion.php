<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MuroInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.muro_institucion';

	public $timestamps = false;

	public $incrementing = false;
}
