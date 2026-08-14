<?php

namespace App\Models\Core;

use Input;
use App\Models\Core\Estado;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;

use DB;

class PreguntaEncuesta extends Model
{
    protected $table = 'core.pregunta_encuesta';

    protected $primaryKey = 'codigo_pregunta_encuesta';

    public $timestamps = false;

    public static function getType($type) {
        switch ($type) :
            case 'open': return 1;
            case 'single': return 2;
            case 'multiple': return 3;
            case 'upload': return 4;
            case 'matriz': return 5;
            default: return 'Not type identified';
        endswitch;
    }

    public static function getTypeField($type) {
        switch ($type) :
            case 'number': return 4;
            case 'select': return 15;
            case 'currency': return 19;
            case 'integer': return 24;
            default: return 'Not field type identified';
        endswitch;
    }

	public static function getClass($class) {
    	switch ($class) :
		    case 'question': return 1;
		    case 'subquestion': return 2;
			default: return 'Not class question identified';
		endswitch;
    }

	public static function getQuestions(array $filters = [])
	{
		$query = PreguntaEncuesta::query();

		$query->join('core.subpregunta_encuesta', function($join) use($filters){
			$join->on('pregunta_encuesta.codigo_pregunta_encuesta', '=', 'subpregunta_encuesta.codigo_pregunta_encuesta')
				->where("subpregunta_encuesta.codigo_pregunta_encuesta", '=', $filters['codigo_pregunta_encuesta'])
				->where('subpregunta_encuesta.codigo_version_encuesta', '=', $filters['codigo_version_encuesta'])
				->where("subpregunta_encuesta.codigo_estado", '=', Estado::getState('active'));
		});

		return $query->get();
	}

	public static function getOptionsQuestion(array $filters = []) {
      if (isset($filters['codigo_hijo_pregunta']) && !empty($filters['codigo_hijo_pregunta'])) {

        $options = SubpreguntaEncuesta::query();
        $options->select('opcion_encuesta.codigo_opcion_encuesta', 'opcion_encuesta.texto_opcion_encuesta', 'opcion_encuesta.puntaje_opcion_encuesta', 'opcion_encuesta.orden_opcion_encuesta', DB::raw('true as selected'));
        $options->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'subpregunta_encuesta.codigo_opcion_encuesta');
        $options->where('subpregunta_encuesta.cor_codigo_pregunta_encuesta', '=',$filters['codigo_pregunta_encuesta']);
        $options->where('subpregunta_encuesta.codigo_pregunta_encuesta', '=', $filters['codigo_hijo_pregunta']);
        $options->where('opcion_encuesta.codigo_estado', '=', Estado::getState('active'));
        $options->where('subpregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $options->orderBy('opcion_encuesta.orden_opcion_encuesta', 'ASC');
        $options->orderBy('opcion_encuesta.codigo_opcion_encuesta', 'ASC');
        $data = $options->get();

        if (count($data) >= 2) {
            return $data;
        } else {
            $query = PreguntaEncuesta::query();
            $query->select('opcion_encuesta.codigo_opcion_encuesta', 'subpregunta_encuesta.codigo_estado', 'opcion_encuesta.texto_opcion_encuesta', 'opcion_encuesta.puntaje_opcion_encuesta', 'opcion_encuesta.orden_opcion_encuesta',
            DB::raw('( CASE WHEN opcion_encuesta.codigo_opcion_encuesta = subpregunta_encuesta.codigo_opcion_encuesta THEN true ELSE false END ) AS selected'));
            $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
            $query->leftJoin('core.subpregunta_encuesta', 'subpregunta_encuesta.cor_codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
            $query->join('core.ver_preg_encuesta', 'ver_preg_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
            $query->where('opcion_encuesta.codigo_estado', '=', Estado::getState('active'));
            $query->where('subpregunta_encuesta.codigo_pregunta_encuesta', '=', $filters['codigo_hijo_pregunta']);
            $query->where('pregunta_encuesta.codigo_pregunta_encuesta', '=', $filters['codigo_pregunta_encuesta']);
            $query->where('ver_preg_encuesta.codigo_version_encuesta', '=', $filters['codigo_version_encuesta']);
            $query->where('subpregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
            $query->orderBy('opcion_encuesta.orden_opcion_encuesta', 'ASC');
            $query->orderBy('opcion_encuesta.codigo_opcion_encuesta', 'ASC');
        }
      } else {
        $query = PreguntaEncuesta::query();
        $query->select('opcion_encuesta.codigo_opcion_encuesta', 'opcion_encuesta.texto_opcion_encuesta', 'opcion_encuesta.puntaje_opcion_encuesta', 'opcion_encuesta.orden_opcion_encuesta');
        $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $query->join('core.ver_preg_encuesta', 'ver_preg_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $query->where('opcion_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('pregunta_encuesta.codigo_pregunta_encuesta', '=', $filters['codigo_pregunta_encuesta']);
        $query->where('ver_preg_encuesta.codigo_version_encuesta', '=', $filters['codigo_version_encuesta']);
        $query->orderBy('opcion_encuesta.orden_opcion_encuesta', 'ASC');
        $query->orderBy('opcion_encuesta.codigo_opcion_encuesta', 'ASC');
        }

      return $query->get();
  }

  public static function getQuestionAll(array $filters= []) {
      $query = PreguntaEncuesta::query();
      $query->join('core.ver_preg_encuesta', 'ver_preg_encuesta.codigo_pregunta_encuesta','=', 'pregunta_encuesta.codigo_pregunta_encuesta');
      $query->where('pregunta_encuesta.codigo_pregunta_encuesta', '=', $filters['codigo_pregunta_encuesta']);
      $query->where('ver_preg_encuesta.codigo_version_encuesta', '=', $filters['codigo_version_encuesta']);

      return self::buildQuestions($query->get(), $filters);
  }

  public static function buildQuestions($questions, $parameters) {
    $object = new \stdClass();
    
    foreach ($questions as $item) {
      $object->codigo_pregunta_encuesta = $item->codigo_pregunta_encuesta;
      $object->tipo_pregunta_encuesta = $item->tipo_pregunta_encuesta;
      $object->texto_pregunta_encuesta = $item->texto_pregunta_encuesta;
      $object->numero_pregunta_encuesta = $item->numero_pregunta_encuesta;
      $object->opcional_pregunta_encuesta = $item->opcional_pregunta_encuesta;
      $object->texto_opcional_pregunta = $item->texto_opcional_pregunta;
      $object->tipo_campo_pregunta = $item->tipo_campo_pregunta;
      $object->clase_pregunta_encuesta = $item->clase_pregunta_encuesta_v1; 
      $object->codigo_version_encuesta = $item->codigo_version_encuesta;
    }
    
    $object->options = self::getOptionsQuestion($parameters);
    $object->padre = isset(self::getFatherQuestions($parameters)->id) ? self::getFatherQuestions($parameters)->id : null;


    return $object;
  }

   public static function getFatherQuestions(array $filters = []) {
        $query = PreguntaEncuesta::query();
        $query->select('pregunta_encuesta.codigo_pregunta_encuesta as id', 'texto_pregunta_encuesta as text');
        $query->join('core.subpregunta_encuesta', 'subpregunta_encuesta.cor_codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $query->where('subpregunta_encuesta.codigo_pregunta_encuesta', '=', $filters['codigo_pregunta_encuesta']);

        return $query->first();
   }


	public static function getQuestionsByForm(array $filters = [])
	{ 
        // dd($filters);
		$query = PreguntaEncuesta::query();
		$query->select('pregunta_encuesta.codigo_pregunta_encuesta as id', 'texto_pregunta_encuesta as text');
		$query->join('core.ver_preg_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'ver_preg_encuesta.codigo_pregunta_encuesta');
		$query->join('core.version_encuesta', 'ver_preg_encuesta.codigo_version_encuesta', '=', 'version_encuesta.codigo_version_encuesta');

		if(isset($filters['data']) && !empty($filters['data'])) {
			$query->join('core.encuesta', function($join) use($filters){
				$join->on('version_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
					->where('version_encuesta.codigo_version_encuesta', '=', $filters['data']);
			});
		}

		if(isset($filters['codigo_pregunta_encuesta']) && !empty($filters['codigo_pregunta_encuesta'])) {
			$query->join('core.subpregunta_encuesta', function($join) use($filters){
				$join->on('pregunta_encuesta.codigo_pregunta_encuesta', '=', 'subpregunta_encuesta.codigo_pregunta_encuesta')
					->where("subpregunta_encuesta.codigo_pregunta_encuesta", '=', $filters['codigo_pregunta_encuesta']);
			});
		}

		// Filters
		if(isset($filters['q']) && !empty($filters['q'])) {
 			$query->whereRaw("texto_pregunta_encuesta ilike '%".$filters['q']."%'");
 		}

		if(isset($filters['id']) && !empty($filters['id'])) {
 			$query->where("pregunta_encuesta.codigo_pregunta_encuesta", '=', $filters['id']);
 		}

		$query->where('pregunta_encuesta.subpregunta_pregunta', '=', true);
		$query->where('pregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('ver_preg_encuesta.codigo_estado', '=', Estado::getState('active'));

		// Order by
		$query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
		$query->distinct();

		return $query->get();
	}

   	/**
	 * Defining Relations
	*/
    public function choices()
    {
        return $this->hasMany('App\Models\Core\OpcionPreguntaEncuesta', 'codigo_pregunta_encuesta', 'codigo_pregunta_encuesta');
    }
}
