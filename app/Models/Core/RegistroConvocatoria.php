<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class RegistroConvocatoria extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.registro_convocatoria';

	protected $primaryKey = 'codigo_registro_convocatoria';

	public $timestamps = false;

	/**
	 * Obtiene datos descendentemente del modelo RegistroConvocatoria mediante codigo_usuario_encuesta.
	 *
	 * @Annotation $id_user hace referencia al codigo_usuario (entidad), $id_survey al codigo_encuesta
	 * @access public
	 * @static function
	 * @param int $id_user, $id_survey
	 * @return mixed
	 */
	public static function getRegisterAnnouncement($id_user, $id_survey)
	{
		$query = RegistroConvocatoria::query();
		$query->join('core.registro_fase_usuario as rfu', 'rfu.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->whereRaw("registro_convocatoria.codigo_usuario_encuesta =
			(
				select ue.codigo_usuario_encuesta
				from core.usuario_encuesta as ue
				where ue.codigo_usuario = ($id_user) and ue.codigo_encuesta = ($id_survey)
				order by ue.codigo_usuario_encuesta desc
				limit 1
			)"
		);
		$data = $query->first();

		return $data;
	}

}
