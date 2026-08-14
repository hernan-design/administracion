<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use  Datatables, TuProyecto, Input, DB;

use App\Models\Seguridad\Usuario;

class UsuarioEncuesta extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.usuario_encuesta';

   	protected $primaryKey = 'codigo_usuario_encuesta';

	public $timestamps = false;

    public static function getUserSurvey($user_code, $survey_code)
    {
    	$query = UsuarioEncuesta::query();
		$query->select('codigo_encuesta', 'nombre_usuario', 'codigo_estado', 'fecha_usuario_encuesta', 'hora_usuario_encuesta', 'puntaje_total_encuesta', 'codigo_usuario_encuesta', 'codigo_modulo', 'id_recurso', 'codigo_version_encuesta');
        $query->where('codigo_usuario', '=', $user_code);
        $query->where('codigo_encuesta', '=', $survey_code);
        return $query->first();
    }

    public static function getUsersSurvey($survey)
    {
    $query = UsuarioEncuesta::query();
		$query->select('codigo_encuesta', 'nombre_estado', DB::raw("(fecha_usuario_encuesta || ' ' || hora_usuario_encuesta) as fecha_usuario_encuesta"), 'fecha_fin_usuario_encuesta', 'hora_usuario_encuesta', 'puntaje_total_encuesta', 'codigo_usuario_encuesta', 'nombre_modulo', 'id_recurso', 'codigo_version_encuesta',
			DB::raw("(CASE tipo_usuario WHEN ".Usuario::typeUser('person')." THEN nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE nombre_usuario END) as nombre_usuario")
		);

		// User
		$query->join('seguridad.usuario', 'usuario_encuesta.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.estado', 'usuario_encuesta.codigo_estado', '=', 'estado.codigo_estado');

		$query->leftJoin('seguridad.modulo', 'usuario_encuesta.codigo_modulo', '=', 'modulo.codigo_modulo');

        $query->where('codigo_encuesta', '=', $survey);
        $query->where('usuario_encuesta.codigo_estado', '=', Estado::getState('complete'));

		if(Input::has('nombre_usuario')){
			$query->whereRaw("nombre_usuario ilike '%".Input::get('nombre_usuario')."%'");
		}

		return Datatables::of($query)
			->addColumn('codigo_usuario_encuesta', function ($data) {
				if($data->codigo_usuario_encuesta != null){
					return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
				}
			})
			->make(true);

    }

}
