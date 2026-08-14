<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;


use App\Estado;

class OpcionEvaluacionPregunta extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.opc_eval_pregunta';

    protected $primaryKey = 'codigo_opc_eval_pregunta';

    public $timestamps = false;

}