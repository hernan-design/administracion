<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Auth, Log;

use App\Models\Core\Estado, App\Models\Core\MuroInstitucion;

class Muro extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.muro';

	protected $primaryKey = 'codigo_muro';

	public $timestamps = false;

	public static function getVisibility($visibility)
    {
    	switch ($visibility) :
		    case 'private': return 0;
		    case 'public': return 1;
			default: return 'Not visibility identified';
		endswitch;
    }

	public static function store($data)
	{
		try
		{
			
			// Wall
			$wall = new self;
			$wall->texto_muro = $data['texto_muro'];
			$wall->visibilidad_muro = self::getVisibility('public');
			$wall->codigo_modulo = $data['codigo_modulo'];
			$wall->codigo_estado = Estado::getState('active');
			$wall->codigo_usuario = Auth::user()->codigo_usuario;
			$wall->codigo_recurso_modulo = $data['codigo_recurso_modulo'];
			$wall->fecha_muro = date('Y-m-d');
			$wall->hora_muro = date('H:i:s');
			$wall->codigo_canal = is_numeric($data['channel']) ? $data['channel'] : null;
			$wall->save();

			$wallInstitution = new MuroInstitucion;
			$wallInstitution->codigo_muro = $wall->codigo_muro;
			$wallInstitution->codigo_institucion = session('institution')->codigo_institucion;
			$wallInstitution->codigo_estado = Estado::getState('active');
			$wallInstitution->save();

			// Return
			return $wall;
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}
}
