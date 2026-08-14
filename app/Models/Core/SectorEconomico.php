<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\Seguridad\Usuario;
use Validator, Input, Cache, DB;

class SectorEconomico extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.sector_economico';

   	protected $primaryKey = 'codigo_sector_economico';

   	public $timestamps = false;

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_sector_economico'	=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	public static function getIndustry($industry)
	{
		$query = SectorEconomico::query();
		$query->where('codigo_sector_economico', '=', $industry);
		return $query->first();
	}

  public static function getSectors()
  {
    if (Cache::has('_economic_sectors')) {
      return Cache::get('_economic_sectors');
    }

    return Cache::rememberForever('_economic_sectors', function() {
      $query = self::query();
      $query->orderby('nombre_sector_economico', 'asc');
      return $query->get();
    });
  }

  public static function getSectorsByPerson($codigo_persona)
  {
      $query = SectorEconomico::query();
      $query->select(DB::raw('sector_economico.codigo_sector_economico'),
        DB::raw('sector_economico.nombre_sector_economico'),
        DB::raw("(CASE WHEN sector_economico_persona.codigo_sector_economico IS NOT NULL THEN true ELSE false END) as comparator"));
      $query->leftJoin('core.sector_economico_persona', function($join) use($codigo_persona) {
      $join->on('sector_economico.codigo_sector_economico', '=', 'sector_economico_persona.codigo_sector_economico')
        ->where('sector_economico_persona.codigo_estado', '=', Estado::getState('active'))
        ->where('sector_economico_persona.codigo_persona', '=', $codigo_persona);
    });
      $query->orderby('nombre_sector_economico', 'asc');
      return $query->get();
  }


	public static function getIndustries()
	{
		$query = self::query();
		$query->select('codigo_sector_economico', 'nombre_sector_economico', 'codigo_estado');

		if(Input::has('nombre_sector_economico')) {
			$query->whereRaw("nombre_sector_economico ilike '%".Input::get('nombre_sector_economico')."%'");
		}

		if(Input::has('codigo_estado')){
			$query->where('codigo_estado', '=', Input::get('codigo_estado'));
		}

		$query->orderby('nombre_sector_economico', 'asc');

		return $query->get();
	}
}
