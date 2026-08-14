<?php

namespace App\Models\Core;

use App\Models\Core\Estado;

use Illuminate\Database\Eloquent\Model;

class VersionFormulario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.version_encuesta';

	protected $primaryKey = 'codigo_version_encuesta';

	public $timestamps = false;

	public static function getFormVersionsByForm(array $filters = [])
	{
		$query = VersionFormulario::query();
		$query->select('codigo_version_encuesta as id', 'comentario_version_encuesta as text');

		if(isset($filters['data']) && !empty($filters['data'])){
			$query->where('codigo_encuesta', $filters['data']);
		}

		if(isset($filters['q']) && !empty($filters['q'])){
			$query->whereRaw("comentario_version_encuesta ilike '%".trim($filters['q'])."%'");
		}

		if(isset($filters['id']) && !empty($filters['id'])){
			$query->where('codigo_version_encuesta', $filters['id']);
		}

		$query->where('codigo_estado', Estado::getState('active'));
		$query->orderBy('text', 'asc');
		return $query->get();
	}
}
