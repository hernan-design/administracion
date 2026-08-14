<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class DatoEntidad extends Model
{
	protected $table = 'core.dato_entidad';

   	protected $primaryKey = 'codigo_dato_entidad';

	public $timestamps = false;
	   
	public static function getDataEntity($user) {
		$query = DatoEntidad::query();
		$query->select('dato_entidad.codigo_dato_entidad', 'dato_entidad.codigo_estado', 'dato_entidad.codigo_usuario_institucion', 'dato_entidad.codigo_etapa_proyecto');
		$query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
		$query->where('usuario_institucion.codigo_usuario', '=', $user);

		return $query->first();
	}
	
}
