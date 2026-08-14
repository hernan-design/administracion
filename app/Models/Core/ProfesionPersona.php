<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Input;

class ProfesionPersona extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.profesion_persona';

   	protected $primaryKey = 'codigo_profesion_persona';

   	public $timestamps = false;

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombnre_profesion_persona'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getProfessions(array $filters = [])
	{
		$query = ProfesionPersona::query();
		$query->select('codigo_profesion_persona', 'nombnre_profesion_persona', 'codigo_estado');

		if(isset($filters['nombnre_profesion_persona']) && !empty($filters['nombnre_profesion_persona'])) {
			$query->whereRaw("nombnre_profesion_persona ilike '%".$filters['nombnre_profesion_persona']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])) {
			$query->where("codigo_estado", "=", $filters['codigo_estado']);
		}

		$query->orderby('nombnre_profesion_persona', 'asc');

		return $query->get();
	}

	public static function getProfessionsList(array $filters = [])
	{
		$query = ProfesionPersona::query();
		$query->select('codigo_profesion_persona as id', 'nombnre_profesion_persona as text');

		if(isset($filters['q']) && !empty($filters['q'])) {
			$query->whereRaw("nombnre_profesion_persona ilike '%".$filters['q']."%'");
		}

		$query->orderby('nombnre_profesion_persona', 'asc');

		return $query->limit(50)->get();
	}
}
