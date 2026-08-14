<?php

namespace App\Models\Aprende;

use Illuminate\Database\Eloquent\Model;

use App\Models\Core\Estado, App\Models\Core\LugarAplicacion;

use Log, DB;

class ContenidoCurso extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'aprende.contenido_curso';

   	public $primaryKey = 'codigo_contenido_curso';

   	public $timestamps = false;

	public static function getTypeCourseContent($typeCourseContent) {
    	switch ($typeCourseContent) :
		    case 'attachment': return 1;
		    case 'article': return 2;
		    case 'evaluation': return 3;
		    case 'homework': return 4;
			default: return 'Not type course content identified';
		endswitch;
    }

	public static function getOrderMax(array $filters = []) {
		$query = ContenidoCurso::query();

		if(isset($filters['codigo_tema_curso']) && !empty($filters['codigo_tema_curso'])){
			$query->where('codigo_tema_curso', '=', $filters['codigo_tema_curso']);
		}

		$query->where('codigo_estado', Estado::getState('active'));
		return $query->max('orden_contenido_curso');
	}

	public static function getCourseContents(array $filters = []) {
		$query = ContenidoCurso::query();
		$query->select('contenido_curso.*', 'adjunto.codigo_adjunto', 'adjunto.direccion_adjunto', 'adjunto.codigo_privacidad_contenido', 'adjunto.codigo_tipo_adjunto', 'encuesta.codigo_encuesta');
		$query->leftjoin('aprende.adjunto_tema', 'contenido_curso.codigo_contenido_curso', '=', 'adjunto_tema.codigo_contenido_curso');
		$query->leftjoin('core.adjunto', 'adjunto_tema.codigo_adjunto', '=', 'adjunto.codigo_adjunto');
		
	   $query->leftJoin('core.encuesta_institucion', function($join) {
			$join->on('encuesta_institucion.codigo_recurso', '=', 'adjunto.codigo_adjunto')
			->where('encuesta_institucion.codigo_lugar_aplicacion', '=', LugarAplicacion::getPlace('aprende'))
			->where('encuesta_institucion.codigo_estado', '=', Estado::getState('active'));
		   });
		$query->leftJoin('core.encuesta', function($join) {
			$join->on('encuesta.codigo_encuesta', '=', 'encuesta_institucion.codigo_encuesta')
			->where('encuesta.codigo_estado', '=', Estado::getState('active'));
	   });   

		if(isset($filters['themeCourse']) && !empty($filters['themeCourse'])){
			$query->where('contenido_curso.codigo_tema_curso', $filters['themeCourse']);
		}

		$query->orderby('contenido_curso.orden_contenido_curso', 'asc');
		$data = $query->get();
		return $data;
	}

}
