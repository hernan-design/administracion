<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado;

class Calendario extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.calendario';

   	protected $primaryKey = 'codigo_calendario';

   	public $timestamps = false;

	public static function getCalendars()
	{
		$query = Calendario::query();
		$query->where('codigo_institucion', '=', session('institution')->codigo_institucion);
		$query->where('codigo_estado', '=', Estado::getState('active'));
		return $query->get();
	}
}
