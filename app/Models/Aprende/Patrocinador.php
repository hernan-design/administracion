<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

use Input, DB;

class Patrocinador extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.patrocinador';

   	protected $primaryKey = 'codigo_patrocinador';

	public $timestamps = false;

	public static function getOrganizers()
	{
		$query = Patrocinador::query();
		$query->select('codigo_patrocinador', DB::raw('initcap(nombre_patrocinador) as nombre_patrocinador'), 'imagen_patrocinador', DB::raw('initcap(descripcion_patrocinador) as descripcion_patrocinador') );
		$query->orderBy('nombre_patrocinador', 'asc');
		return $query->get();
	}

	public static function getOrganizersList()
	{
		$query = Patrocinador::query();
		$query->select('codigo_patrocinador as id', DB::raw('initcap(nombre_patrocinador) as text') );

		if(Input::has('q')){
			$query->whereRaw("nombre_patrocinador ilike '%".Input::get("q")."%'");
		}

		$query->orderBy('nombre_patrocinador', 'asc');
		return $query->get();
	}
}
