<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoEvaluacionItem extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.tipo_evaluacion_item';

   	protected $primaryKey = 'codigo_tipo_evaluacion_item';

   	public $timestamps = false;

     public static function getForm($id)
     {
       $query = EvaluacionItemEmprendimiento::query();
       $query->select('evaluacion_item_emprendimiento.*');
       $query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', $id);
       return $query->get();
     }
}
