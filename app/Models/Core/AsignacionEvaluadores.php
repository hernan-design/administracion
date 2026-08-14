<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class AsignacionEvaluadores extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.asignacion_evaluadores';

	protected $primaryKey = 'codigo_evaluador';

	public function user()
    {
        return $this->belongsTo('App\Models\Seguridad\Usuario', 'codigo_usuario');
    }
}
