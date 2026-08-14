<?php

namespace App\Models\Core;

use Cache, Validator, DB;

use App\Models\BaseModel;
// use Spatie\Activitylog\Traits\LogsActivity;

class Canal extends BaseModel
{
	// use LogsActivity;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.canal';

   	protected $primaryKey = 'codigo_canal';

   	public $timestamps = false;

	protected static $logAttributes = ['nombre_canal', 'descripcion_canal', 'canal_abierto', 'codigo_categoria_canal'];

	/**
	 * Type
	 *
	 * @var array
	 */
	public static $type = [true => 'Abierto', false  => 'Cerrado'];

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['nombre_canal', 'descripcion_canal', 'canal_abierto', 'codigo_categoria_canal'];

	protected $nullable = ['codigo_categoria_canal'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_canal'		=> 'required',
			'descripcion_canal'	=> 'required|max:200',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getChannels(array $filters = [])
	{
		$query = Canal::query();
		$query->select('codigo_canal', 'codigo_estado', 'codigo_institucion', 'nombre_canal', 'descripcion_canal', 'canal_abierto', 'canal_defecto', 'codigo_categoria_canal', 'contador_inscritos');

		if(isset($filters['nombre_canal']) && !empty($filters['nombre_canal'])){
			$query->whereRaw("nombre_canal  ilike '%".$filters['nombre_canal']."%'");
		}

		if(isset($filters['tipo_canal']) && !empty($filters['tipo_canal'])){
			$query->where('tipo_canal', $filters['tipo_canal']);
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where('codigo_estado', $filters['codigo_estado']);
		}

		$query->where('codigo_institucion', session('institution')->codigo_institucion);

		$query->orderby('nombre_canal','desc');
		return $query->get();
	}

	public static function getChannelById($id)
	{
		$query = Canal::query();
		$query->select('codigo_canal', 'codigo_estado', 'codigo_institucion', 'nombre_canal', 'descripcion_canal', 'canal_abierto', 'canal_defecto', 'codigo_categoria_canal', 'contador_inscritos');
		$query->where('codigo_canal', '=', $id);
		return $query->first();
	}
}
