<?php

namespace App\Models\Core;

use Validator;
use Illuminate\Database\Eloquent\Model;

class ActividadAcuerdo extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.actividad_acuerdo';

	protected $primaryKey = 'codigo_actividad_acuerdo';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['desc_actividad_acuerdo', 'resp_proveedor_actividad', 'resp_comprador_actividad', 'sesiones_presencial', 'sesiones_virtual', 'fecha_inicial_actividad_acuerdo', 'fecha_final_actividad_acuerdo', 'sesiones_presencial_horas', 'sesiones_virtual_horas'];

	/**
	* Valid create agreement.
	*/
	public function isValid($data)
	{
		$rules = [
			'desc_actividad_acuerdo' 			=> 'required',
			'resp_proveedor_actividad' 			=> 'required|boolean',
			'resp_comprador_actividad' 			=> 'required|boolean',
			'sesiones_presencial'		 		=> 'required|numeric|integer',
			'sesiones_virtual' 					=> 'required|numeric|integer',
			'fecha_inicial_actividad_acuerdo' 	=> 'required|date_format:Y-m-d',
			'fecha_final_actividad_acuerdo' 	=> 'required|date_format:Y-m-d',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}
}
