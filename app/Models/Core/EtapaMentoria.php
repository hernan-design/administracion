<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class EtapaMentoria extends Model
{
	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table = 'core.etapa_mentoria';

	public $incrementing = false;

	public $timestamps = false;
}
