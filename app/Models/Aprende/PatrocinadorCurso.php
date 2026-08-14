<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

use Input, DB;

class PatrocinadorCurso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.patrocinador_curso';

   	protected $primaryKey = 'codigo_patrocinador';

	public $timestamps = false;

	protected $fillable = ['codigo_patrocinador', 'codigo_curso'];


}
