<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB, Validator;

class SesionEvento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.sesion_evento';

   	protected $primaryKey = 'codigo_sesion_evento';

	public $timestamps = false;
	   
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_evento', 'fecha_inicio_sesion', 'fecha_fin_sesion', 'hora_inicio_sesion', 'hora_fin_sesion'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_evento' => 'required',
			'fecha_inicio_sesion' => 'required|date',
			'fecha_fin_sesion' => 'required|date',
			'hora_inicio_sesion' => 'required',
			'hora_fin_sesion' => 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	 * Get sessions by event
	 */
	public static function getSesionEvent(array $filters = [])
	{
		$query = SesionEvento::query();
		$query->where('codigo_evento', '=', $filters['event']);
		$query->orderBy('numero_sesion', 'asc');
		return $query->get();
	}

	/**
	 * Get all sessions by event
	 */
	public static function sessionsEvent(array $filters = [])
	{
		$query = SesionEvento::query();
		$query->select('codigo_sesion_evento as id', 'numero_sesion as text');
		$query->where('codigo_evento', '=', $filters['data']);

		if(isset($filters['q']) && !empty($filters['q'])){
			$query->where("numero_sesion", $filters['q']);
		}

		$query->orderBy('numero_sesion', 'asc');
		return $query->get();
	}

	/**
	 * Get max session by event
	 */
	public static function getMaxSesion(array $filters = [])
	{
		$query = SesionEvento::query();
		$query->where('codigo_evento', '=', $filters['codigo_evento']);
		return $query->max('numero_sesion');
	}
}
