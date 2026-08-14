<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SesionMasiva extends Model
{
	protected $table = 'core.sesiones_masivas';

	protected $primaryKey = 'codigo_sesion_masiva';


    //relaciones
    public function mentoriamasiva()
    {
        return $this->belongsTo('App\Models\Core\MentoriaMasiva', 'codigo_mentoria_masiva', 'codigo_mentoria_masiva');
    }

    public function sesiones()
    {
        return $this->hasMany('App\Models\Aprende\Mentoria', 'codigo_sesion_masiva', 'codigo_sesion_masiva');
    }
}
