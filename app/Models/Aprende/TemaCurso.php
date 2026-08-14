<?php

namespace App\Models\Aprende;

use Validator, Input;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Estado;
use DB;

class TemaCurso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.tema_curso';

   	protected $primaryKey = 'codigo_tema_curso';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_curso', 'nombre_tema_curso', 'objetivo_tema_curso'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_curso'					=> 'required',
			'nombre_tema_curso'				=> 'required',
			'objetivo_tema_curso' 			=> 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getThemesCourse($course)
	{
		$query = TemaCurso::query();
		$query->where('codigo_curso', $course);
		$query->orderby('nombre_tema_curso', 'asc');
		$data = $query->get();

		return $data;
	}
}
