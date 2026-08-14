<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class UsuarioInstitucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.usuario_institucion';

	protected $primaryKey = 'codigo_usuario_institucion';

	public $timestamps = false;
}
