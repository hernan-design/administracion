<?php

namespace App\Models\Core;

use Validator;
use Illuminate\Database\Eloquent\Model;

class UsuarioAcuerdo extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.usuario_acuerdo';

	protected $primaryKey = 'codigo_usuario_acuerdo';

	public $timestamps = false;
}
