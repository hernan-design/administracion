<?php

namespace App\Models\Core;

use App\Models\Param\Institucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

use Datatables;

use DB, Lang, App, Log, Input, Validator;
use TuProyecto;

class ComentarioEvaluacionEmprendimiento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.coment_eval_emprendi';

   	protected $primaryKey = 'codigo_coment_eval_emprendi';

    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [ 'name_coment_eval_emprendi', 'texto_coment_eval_emprendi',
    'codigo_evaluacion_emprendimiento',  'codigo_estado'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'name_coment_eval_emprendi'					=> 'required|max:100',
      'texto_coment_eval_emprendi'				=> 'required',
      'codigo_evaluacion_emprendimiento'  => 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}



	public static function getDataCommentsEvaluation() {

    $query = ComentarioEvaluacionEmprendimiento::query();
    $query->select('coment_eval_emprendi.*','evaluacion_emprendimiento.nombre_evaluacion_emprendimiento');
    $query->join('core.evaluacion_emprendimiento', 'evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', 'coment_eval_emprendi.codigo_evaluacion_emprendimiento');
    $query->where('evaluacion_emprendimiento.codigo_institucion', session('institution')->codigo_institucion);
    $query->where('evaluacion_emprendimiento.comentario_evaluacion_cerrado',true);
		$query->whereIn('coment_eval_emprendi.codigo_estado', [Estado::getState('active'), Estado::getState('inactive')]);

		return Datatables::of($query)->make(true);
  }


	/**
	 * Build Evaluation.
	*/
   	public static function buildCommentEvaluation($comment)
   	{

        $object = new \stdClass();
        $object->codigo_coment_eval_emprendi  = $comment->codigo_coment_eval_emprendi ;
        $object->codigo_estado = $comment->codigo_estado;
        $object->name_coment_eval_emprendi = $comment->name_coment_eval_emprendi;
        $object->texto_coment_eval_emprendi = $comment->texto_coment_eval_emprendi;
        $object->codigo_evaluacion_emprendimiento =$comment->codigo_evaluacion_emprendimiento;
        $object->nombre_evaluacion_emprendimiento =$comment->nombre_evaluacion_emprendimiento;

		    return $object;
   	}
}
