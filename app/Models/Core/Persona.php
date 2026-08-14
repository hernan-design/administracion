<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;

use Log, Validator, DB;

class Persona extends BaseModel
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.persona';

	protected $primaryKey = 'codigo_persona';

	public $timestamps = false;

	protected $fillable = ['primer_apellido_persona', 'segundo_apellido_persona', 'genero_persona', 'estado_civil_persona', 'codigo_nivel_educacion', 'codigo_profesion_persona', 'codigo_ocupacion_persona'];

	/**
	 * The attributes nulleables from the model.
	 *
	 * @var array
	 */
	protected $nullable = ['codigo_nivel_educacion', 'codigo_profesion_persona', 'codigo_ocupacion_persona'];

	public function economicsectors()
    {
        return $this->belongsToMany('App\Models\Core\SectorEconomico', 'core.sector_economico_persona', 'codigo_persona', 'codigo_sector_economico');
    }


  /**
   * Alias to eloquent many-to-many relation's attach() method.
   *
   * @param mixed $role
   */
  public function attachEconomicSector($sector)
  {
    if(is_object($sector)) {
      $sector = $sector->getKey();
    }

    if(is_array($sector)) {
      $sector = $sector['id'];
    }

    $this->economicsectors()->attach($sector,['codigo_estado'=>8]);
  }

  /**
   * Alias to eloquent many-to-many relation's detach() method.
   *
   * @param mixed $role
   */
  public function detachEconomicSector($sector)
  {
    if (is_object($sector)) {
      $sector = $sector->getKey();
    }

    if (is_array($sector)) {
      $sector = $sector['id'];
    }

    $this->economicsectors()->detach($sector);
  }

  public function isValid($data)
	{
		$rules = [
			'primer_apellido_persona' => 'max:250'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

  public static function getLastName(int $codigo_usuario): String
  {
    $query = Persona::query();
    $query->select(
      DB::raw("COALESCE(primer_apellido_persona, '')
        || ' ' || COALESCE(segundo_apellido_persona, '') as apellido")
    );
    $query->where('codigo_usuario', $codigo_usuario);
    $query->orderBy('codigo_persona', 'desc');
    $data = $query->first();

    if (isset($data->apellido)) {
      return $data->apellido;
    }
    
    return $data = "";
  }

}
