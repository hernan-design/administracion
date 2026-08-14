<?php

namespace App\Models\Core;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Input, Validator, DB;

class GrupoPreguntaEncuesta extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.grupo_pregunta_encuesta';

	protected $primaryKey = 'codigo_grupo_pregunta_encuesta';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_encuesta', 'nombre_grupo_pregunta_encuesta', 'desc_grupo_pregunta_encuesta'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_encuesta' => 'required',
			'nombre_grupo_pregunta_encuesta'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getGroups(Request $request)
	{
		$query = GrupoPreguntaEncuesta::query();
		$query->select('grupo_pregunta_encuesta.codigo_grupo_pregunta_encuesta', 'grupo_pregunta_encuesta.codigo_pagina_encuesta', 'grupo_pregunta_encuesta.nombre_grupo_pregunta_encuesta', 'grupo_pregunta_encuesta.desc_grupo_pregunta_encuesta', 'pagina_encuesta.pagina_grupo_pregunta', 'pagina_encuesta.codigo_pagina_encuesta');
		$query->join('core.pagina_encuesta', 'pagina_encuesta.codigo_pagina_encuesta', '=', 'grupo_pregunta_encuesta.codigo_pagina_encuesta');
		$query->where('pagina_encuesta.codigo_encuesta', '=', $request->formulario);
		$query->where('grupo_pregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->orderBy('grupo_pregunta_encuesta.codigo_grupo_pregunta_encuesta', 'ASC');
		$query->orderBy('pagina_encuesta.codigo_pagina_encuesta', 'ASC');
		return $query->get();
	}

	public static function getGroup($id) {
    $query = GrupoPreguntaEncuesta::query();
    $query->select('grupo_pregunta_encuesta.codigo_grupo_pregunta_encuesta',  'grupo_pregunta_encuesta.codigo_encuesta', 'grupo_pregunta_encuesta.codigo_pagina_encuesta', 'grupo_pregunta_encuesta.nombre_grupo_pregunta_encuesta', 'grupo_pregunta_encuesta.desc_grupo_pregunta_encuesta');
    $query->where('codigo_grupo_pregunta_encuesta', '=', $id);
    $data = $query->first();

    return $data;
  }
}
