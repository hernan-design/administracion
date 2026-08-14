<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class BeneficioEvento extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.beneficio_evento';

   	protected $primaryKey = 'codigo_beneficio_evento';

    public $timestamps = false;
	
	
	public static function benefitsEventList()
	{
		$query = BeneficioEvento::query();
		$query->select('codigo_beneficio_evento as id', 'nombre_beneficio_evento as text');
		$query->where('codigo_institucion', session('institution')->codigo_institucion);
		return $query->get();
	}
}
