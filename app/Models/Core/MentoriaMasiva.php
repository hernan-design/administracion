<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class MentoriaMasiva extends Model
{
	protected $table = 'core.mentorias_masivas';

	protected $primaryKey = 'codigo_mentoria_masiva';


    //relaciones

    public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario', 'codigo_usuario', 'seg_codigo_usuario');
    }

    public function asesorias()
    {
        return $this->hasMany('App\Models\Core\Asesoria', 'codigo_mentoria_masiva', 'codigo_mentoria_masiva');
    }

    public function sesionesmasivas()
    {
        return $this->hasMany('App\Models\Core\SesionMasiva', 'codigo_mentoria_masiva', 'codigo_mentoria_masiva');
    }
}
