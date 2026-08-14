<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class EventoInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.evento_institucion';

   	public $incrementing = false;

   	public $timestamps = false;
}
