<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use Validator;

class OpcionPreguntaEncuesta extends Model
{
	protected $table = 'core.opcion_encuesta';

    protected $primaryKey = 'codigo_opcion_encuesta';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_pregunta_encuesta', 'texto_opcion_encuesta', 'complemento', 'todas', 'ninguna', 'visibilidad_opcion', 'eje_opcion', 'calculado_opcion_encuesta', 'formula_opcion_encuesta', 'orden_opcion_encuesta', 'referencia_opcion_encuesta', 'tipo_campo_opcion', 'puntaje_opcion_encuesta'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_pregunta_encuesta'		=> 'required',
			'texto_opcion_encuesta'			=> 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

    public static function getAxis($axis)
    {
    	switch ($axis) :
		    case 'y': return 1;
		    case 'x': return 2;
			default: return 'Not axis identified';
		endswitch;
    }

	public static function getOptionsByQuestion(array $filters = [])
	{
		$query = OpcionPreguntaEncuesta::query();
		$query->where('codigo_estado', '=', Estado::getState('active'));
		$query->where('codigo_pregunta_encuesta', '=', $filters['codigo_pregunta_encuesta']);
		$query->orderBy('codigo_opcion_encuesta', 'asc');
		return $query->get();
	}

	/**
     * Defining Relations
    */
    public function choices()
    {
        return $this->hasMany('App\Models\Core\ItemOpcionEncuesta', 'codigo_opcion_encuesta', 'codigo_opcion_encuesta');
    }
}
