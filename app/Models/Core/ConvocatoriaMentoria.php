<?php

namespace App\Models\Core;


use Illuminate\Database\Eloquent\Model;

use Datatables, DB, Input, Validator, Lang;


class ConvocatoriaMentoria extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.convocatoria_mentoria';

   	protected $primaryKey = 'codigo_convocatoria_mentoria';


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		//
	];

	//relations
	public function asesoria()
	{
		return $this->hasOne('App\Models\Core\Asesoria', 'codigo_asesoria', 'codigo_asesoria');
	}

	public function convocatoria()
	{
		return $this->hasOne('App\Models\Core\Convocatoria', 'codigo_convocatoria', 'codigo_convocatoria');
	}

	//obtener mentor
	public function mentor()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario', 'codigo_usuario', 'seg_codigo_usuario');
    }
}
