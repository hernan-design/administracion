<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoComentEvalCompra extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_comen_eval_insti';

   	protected $primaryKey = 'tipo_comentario_evaluacion';

	public $timestamps = false;

}
