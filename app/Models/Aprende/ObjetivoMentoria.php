<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;


class ObjetivoMentoria extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.objetivo_mentoria';

	protected $primaryKey = 'codigo_objetivo_mentoria';

	public $timestamps = false;
}