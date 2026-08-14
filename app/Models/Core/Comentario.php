<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.comentario';

	protected $primaryKey = 'codigo_comentario';

	public $timestamps = false;

	public static $evaluation = [ 1 => 'Positivo', 2 => 'Negativo', 3 => 'Neutro'];

   	/**
	 * Defining Relations
	*/
	public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
    }
}
