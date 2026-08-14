<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

class TagSesionMentoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.tag_sesion_mentoria';

	protected $primaryKey = 'codigo_tag_sesion_mentoria';

	public $timestamps = false;
}
