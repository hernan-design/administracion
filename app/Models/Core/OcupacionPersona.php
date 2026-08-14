<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Input;

class OcupacionPersona extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.ocupacion_persona';

   	protected $primaryKey = 'codigo_ocupacion_persona';

   	public $timestamps = false;

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_ocupacion_persona'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getOccupations(array $filters = [])
	{
		$query = OcupacionPersona::query();
		$query->select('codigo_ocupacion_persona', 'nombre_ocupacion_persona', 'codigo_estado');

		if(isset($filters['nombre_ocupacion_persona']) && !empty($filters['nombre_ocupacion_persona'])) {
			$query->whereRaw("nombre_ocupacion_persona ilike '%".$filters['nombre_ocupacion_persona']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])) {
			$query->where('codigo_estado', '=', $filters['codigo_estado']);
		}

		$query->orderby('nombre_ocupacion_persona','asc');

		return $query->get();
	}

	public static function occupations(array $filters = [])
	{
		$query = OcupacionPersona::query();
		$query->select('codigo_ocupacion_persona as id', 'nombre_ocupacion_persona as text');

		if(isset($filters['q']) && !empty($filters['q'])) {
			$query->whereRaw("nombre_ocupacion_persona ilike '%".$filters['q']."%'");
		}

		$query->where('codigo_estado', Estado::getState('active'));
		$query->orderby('nombre_ocupacion_persona','asc');

		return $query->limit(50)->get();
	}
}
