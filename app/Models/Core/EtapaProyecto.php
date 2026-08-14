<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Input;

use App\Models\Core\Estado;

class EtapaProyecto extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.etapa_proyecto';

   	protected $primaryKey = 'codigo_etapa_proyecto';

   	public $timestamps = false;

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_etapa_proyecto'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getStage($stage)
	{
		$query = EtapaProyecto::query();
		$query->select('codigo_etapa_proyecto', 'nombre_etapa_proyecto', 'descripcionetapa_proyecto', 'codigo_estado');
		$query->where('codigo_etapa_proyecto', '=', $stage);
        $query->where('codigo_institucion', '=', session('institution')->codigo_institucion);
		return $query->first();
	}

	public static function getAllStages($inst){
    $query = EtapaProyecto::query();
    $query->select('codigo_etapa_proyecto', 'nombre_etapa_proyecto');
    $query->where('codigo_institucion', '=', $inst);
    $query->where('codigo_estado', '=', Estado::getState('active'));
    return $query->get();
  }

  public static function getAllStagesForPrograms() {
    $query = EtapaProyecto::query();
    $query->where('codigo_estado', Estado::getState('active'));
    $query->where('codigo_institucion', session('institution')->codigo_institucion);
    $query->orderby('codigo_etapa_proyecto','asc');
    $return = $query->get();
    return $return;
  }

	public static function stages(array $filters = [])
	{
		$query = EtapaProyecto::query();
		$query->select('codigo_etapa_proyecto as id', 'nombre_etapa_proyecto as text');
        $query->where('codigo_institucion', '=', session('institution')->codigo_institucion);

		if(isset($filters['q']) && !empty($filters['q'])) {
			$query->whereRaw("nombre_etapa_proyecto ilike '%".$filters['q']."%'");
		}

		$query->where("codigo_estado", "=", Estado::getState('active'));

        $query->orderBy('text','asc');
    	return $query->get();
	}

	public static function getStages(array $filters = [])
    {
        $query = EtapaProyecto::query();
        $query->where('codigo_institucion', '=', session('institution')->codigo_institucion);

		if(isset($filters['nombre_etapa_proyecto']) && !empty($filters['nombre_etapa_proyecto'])) {
			$query->whereRaw("nombre_etapa_proyecto ilike '%".$filters['nombre_etapa_proyecto']."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])) {
			$query->where("codigo_estado", "=", $filters['codigo_estado']);
		}

		  $query->orderby('codigo_etapa_proyecto','asc');
    	return $query->get();
    }
}
