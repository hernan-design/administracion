<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class EvaluacionItemEmprendimiento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.evaluacion_item_emprendimiento';

   	protected $primaryKey = 'codigo_evaluacion_item';

   	public $timestamps = false;

     public static function getForm($id)
     {
       $query = EvaluacionItemEmprendimiento::query();
       $query->select('evaluacion_item_emprendimiento.*');
       $query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', $id);
       return $query->get();
     }
}
