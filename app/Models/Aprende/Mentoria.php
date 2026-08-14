<?php
namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Estado;

use DB, Input, Auth, Log, Artisan, Config, Validator, Lang, URL, SendEmail, Datatables;

class Mentoria extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aprende.mentoria';

	protected $primaryKey = 'codigo_mentoria';

	public $timestamps = false;

	public static function getMentoringEvent($event) {
		return Mentoria::select('mentoria.*','mi.codigo_institucion')
							->where('mentoria.codigo_evento', '=', $event)
							->join('aprende.mentoria_institucion as mi', 'mentoria.codigo_mentoria', '=', 'mi.codigo_mentoria')
							->whereNotIn('mentoria.codigo_estado', [ Estado::getState('removed')] )
							->orderBy('mentoria.codigo_evento','desc')
							->first();
    }

    /**
	 * Defining Relations
	*/
	public function usuario()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','codigo_usuario');
    }

	public function mentor()
    {
        return $this->hasOne('App\Models\Seguridad\Usuario','codigo_usuario','seg_codigo_usuario');
	}

    public function estado()
    {
        return $this->hasOne('App\Estado','codigo_estado','codigo_estado');
    }

   	public function objetivos()
    {
        return $this->hasMany('App\ObjetivoMentoria', 'codigo_mentoria', 'codigo_mentoria');
    }

    public function usuarioInstitucion(){
      return $this->hasOne('App\UsuarioInstitucion', 'usuario_institucion', 'usuario_institucion');
    }

    public static function getSessions(int $codigo_asesoria) {
		$mentorings = [];
		$query = Mentoria::query();
		$query->select(
			'mentoria.codigo_mentoria AS codigo_sesion',
			'mentoria.duration_mentoring',
			'mentoria.codigo_asesoria',
			'mentoria.codigo_estado',
			'mentoria.fecha_finalizo_mentoria',
			'mentoria.fecha_mentoria',
            'evento.nombre_evento',
			'evento.fecha_evento',
			'evento.hora_inicio_evento',
			'evento.fecha_fin_evento'
		);

		//hora evento o sesion
		$query->join('core.evento as evento', 'evento.codigo_evento', '=', 'mentoria.codigo_evento');
		$query->where('mentoria.codigo_asesoria', $codigo_asesoria);
		$data = $query->get();

   		return $data;
	}

}
