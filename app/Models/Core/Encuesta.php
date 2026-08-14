<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use TuProyecto, URL, Input, DB;
use App\Models\Core\Estado,
	App\Models\Core\PreguntaEncuesta,
	App\Models\Seguridad\Modulo,
	App\Models\Core\Fase,
	App\Models\Core\EncuestaInstitucion,
	App\Models\Core\LugarAplicacion,
	App\Models\Param\PreguntaRegistroInstitucion,
	App\Models\Seguridad\Usuario,
	App\Models\Core\UsuarioEncuesta,
	App\Models\Core\Convocatoria;

class Encuesta extends Model
{
    protected $table = 'core.encuesta';

    protected $primaryKey = 'codigo_encuesta';

    public $timestamps = false;


	/**
	 * Returns a formatted and ordered array with the questions of the survey
	 *
	 * @return array an ordered list of questions of the survey
	 */
	public function getSurveyQuestions()
	{
		$query = $this->questions();
		$query->select('pregunta_encuesta.*', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta');
		$query->leftJoin('core.grupo_pregunta_encuesta', 'pregunta_encuesta.codigo_grupo_pregunta_encuesta', '=', 'grupo_pregunta_encuesta.codigo_grupo_pregunta_encuesta');
		$query->leftJoin(DB::raw('(SELECT * FROM core.subpregunta_encuesta ORDER BY codigo_version_encuesta DESC LIMIT 1) AS pregunta'), function($join)
		{
			$join->on('pregunta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta.codigo_pregunta_encuesta');
		});
		$query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		// $query->where('pregunta_encuesta.codigo_pregunta_encuesta', 4343);
		$query->orderBy('grupo_pregunta_encuesta.pagina_grupo_pregunta', 'asc');
		$query->orderBy('pregunta_encuesta.numero_pregunta_encuesta', 'asc');
		$query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');

		return $query->get();
	}

	public static function getSurvey($survey) {
    	switch ($survey) :
		    case 'angels': return 2;
			default: return 'Not survey identified';
		endswitch;
    }

	public static function getFieldType($type) {
    	switch ($type) :
			case 'number_format': return 3;
		    case 'cities': return 5;
		    case 'file': return 13;
		    case 'data': return 14;
		    case 'ciiu': return 16;
		    case 'education_level': return 17;
		    case 'economic_sector': return 18;
		    case 'currency': return 19;
		    case 'before_date': return 26;
		    case 'negative_currency': return 27;
			default: return 'Not type identified';
		endswitch;
    }

    public static function getClass($class) {
    	switch ($class) :
		    case 'internal': return 1;
		    case 'surveygizmo': return 2;
			default: return 'Not class identified';
		endswitch;
    }

    public static function getSurveyUrl($url) {
		return Encuesta::whereRaw("trim(lower(url_encuesta)) = trim(lower('$url'))")
			->whereRaw("trim(lower(url_encuesta)) != ''")
			->whereNotNull('url_encuesta')
			->join('core.encuesta_institucion', function($join) {
				$join->on('encuesta_institucion.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
					->where('encuesta_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
					->where('encuesta_institucion.codigo_estado', '=', Estado::getState('active'));
	        })
			->first();
   	}

   	public static function buildSurvey(Encuesta $survey, $userSurvey = null, $arAnswers = null)
   	{
		$object = new \stdClass();
		$object->codigo_encuesta = TuProyecto::encryptSrcParams([$survey->codigo_encuesta]);
		$object->url_encuesta = $survey->url_encuesta;
		$object->nombre_encuesta = $survey->nombre_encuesta;
		$object->descripcion_encuesta = $survey->descripcion_encuesta;
		$object->clase_encuesta = $survey->clase_encuesta;

		switch ($survey->clase_encuesta) {

			case Encuesta::getClass('internal'):
				// Internal
				$object->label_finalizar = $survey->label_boton_finalizar_encuesta;
				$object->color_fondo_encuesta = $survey->color_fondo_encuesta;
				$object->color_encuesta = $survey->color_encuesta;
				$object->paginacion_encuesta = $survey->paginacion_encuesta;
				$object->redirect_to = $survey->redirect_encuesta ?: URL::route('dashboard');
				$object->preguardado_encuesta = $survey->preguardado_encuesta;
				$object->cancelar_encuesta = $survey->cancelar_encuesta;
				$object->tipo_paginacion_encuesta = $survey->tipo_paginacion_encuesta;

				$query = $survey->questions();
				$query->select('pregunta_encuesta.*', 'ver_preg_encuesta.numero_pregunta_version as numero_pregunta_encuesta', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta', 'sigla_moneda', 'ver_preg_encuesta.clase_pregunta_encuesta');

				$query->join('core.ver_preg_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'ver_preg_encuesta.codigo_pregunta_encuesta');
				$query->leftJoin('core.subpregunta_encuesta as pregunta', function($join) {
					$join->on('pregunta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
					$join->on('pregunta.codigo_version_encuesta', '=', 'ver_preg_encuesta.codigo_version_encuesta');
		        });
				$query->leftJoin('core.moneda', 'pregunta_encuesta.codigo_moneda', '=', 'moneda.codigo_moneda');

				$query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
				$query->where('ver_preg_encuesta.codigo_estado', Estado::getState('active'));
				// Version encuesta
				if($userSurvey instanceof UsuarioEncuesta && $userSurvey->codigo_estado == Estado::getState('complete')) {
					$query->where('ver_preg_encuesta.codigo_version_encuesta', $userSurvey->codigo_version_encuesta);
				}else {
					$query->where('ver_preg_encuesta.codigo_version_encuesta', $survey->codigo_version_encuesta);
				}

				$query->orderBy('ver_preg_encuesta.numero_pregunta_version', 'asc');
				$query->orderBy('pregunta.orden_subpregunta_encuesta', 'desc');
				$query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
				$arQuestions = $query->get();

				if($arQuestions->count() > 0) {

					$questions = [];
					foreach ($arQuestions as $question) {
						$item = new \stdClass();
						$item->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
						$item->texto_pregunta_encuesta = $question->texto_pregunta_encuesta;
						$item->texto_opcional_pregunta = $question->texto_opcional_pregunta;
						$item->tipo_pregunta_encuesta = $question->tipo_pregunta_encuesta;
						$item->tipo_campo_pregunta = $question->tipo_campo_pregunta;
						$item->opcional_pregunta_encuesta = $question->opcional_pregunta_encuesta;
						$item->clase_pregunta_encuesta = $question->clase_pregunta_encuesta;
						$item->codigo_grupo_pregunta_encuesta = $question->codigo_grupo_pregunta_encuesta;
						$item->numero_pregunta_encuesta = $question->numero_pregunta_encuesta;
						$item->longitud_pregunta_encuesta = $question->longitud_pregunta_encuesta;

						$item->subpregunta_pregunta = $question->subpregunta_pregunta;
						$item->pregunta_padre = $question->pregunta_padre;
						$item->codigo_opcion_encuesta = $question->codigo_opcion_encuesta;

						// Currency questions
						$item->codigo_moneda = $question->codigo_moneda;
						$item->sigla_moneda = $question->sigla_moneda;

						// Options questions closed, unique or mulplique answer (y-axis)
						$options = [];
						if(in_array($question->tipo_pregunta_encuesta, [PreguntaEncuesta::getType('single'), PreguntaEncuesta::getType('multiple'), PreguntaEncuesta::getType('matriz')])) {
							// Choices closed questions
							$arChoices = $question->choices()->where('codigo_estado', Estado::getState('active'))->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))->orderBy('orden_opcion_encuesta', 'asc')->orderBy('codigo_opcion_encuesta', 'asc')->get();
							if($arChoices->count() > 0) {
								foreach ($arChoices as $choice) {
									$choiceItem = new \stdClass();
									$choiceItem->codigo_opcion_encuesta = $choice->codigo_opcion_encuesta;
									$choiceItem->texto_opcion_encuesta = $choice->texto_opcion_encuesta;
									$choiceItem->calculado_opcion_encuesta = $choice->calculado_opcion_encuesta;
									$choiceItem->referencia_opcion_encuesta = $choice->referencia_opcion_encuesta;
									$choiceItem->tipo_campo_opcion = $choice->tipo_campo_opcion;
									if($choice->calculado_opcion_encuesta == true){
										$choiceItem->formula_opcion_encuesta = $choice->formula_opcion_encuesta;
									}
									$options[] = $choiceItem;
								}
							}
						}
						$item->opciones = $options;

						// Options questions matriz (x-axis)
						$campos_opciones = [];
						if(in_array($question->tipo_pregunta_encuesta, [PreguntaEncuesta::getType('matriz')])) {
							// Choices closed questions
							$arChoices = $question->choices()->where('codigo_estado', Estado::getState('active'))->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))->orderBy('orden_opcion_encuesta', 'asc')->orderBy('codigo_opcion_encuesta', 'asc')->get();
							if($arChoices->count() > 0) {
								foreach ($arChoices as $choice) {
									$choiceItem = new \stdClass();
									$choiceItem->codigo_opcion_encuesta = $choice->codigo_opcion_encuesta;
									$choiceItem->texto_opcion_encuesta = $choice->texto_opcion_encuesta;
									$choiceItem->calculado_opcion_encuesta = $choice->calculado_opcion_encuesta;
									$choiceItem->referencia_opcion_encuesta = $choice->referencia_opcion_encuesta;
									$choiceItem->tipo_campo_opcion = $choice->tipo_campo_opcion;
									if($choice->calculado_opcion_encuesta == true){
										$choiceItem->formula_opcion_encuesta = $choice->formula_opcion_encuesta;
									}
									$campos_opciones[] = $choiceItem;
								}
							}
						}
						$item->campos_opciones = $campos_opciones;

						if(in_array($question->tipo_pregunta_encuesta, [PreguntaEncuesta::getType('upload')])) {
							$config = new \stdClass();
							$config->minWidthImg = $question->ancho_pregunta_encuesta;
							$config->minHeightImg = $question->alto_pregunta_encuesta;

							$item->upload_config = $config;
						}

						// Build answer
						$item->respuesta = [];
						if($arAnswers instanceof Collection) {
							$item->respuesta = RespuestaEncuesta::buildAnswer($arAnswers, $question->tipo_pregunta_encuesta, $question->codigo_pregunta_encuesta);
						}

						// Add item
						$questions[] = $item;
					}
					// Add questions
					$object->preguntas = $questions;

					// Options questions closed unique or mulplique answer
					$object->agrupaciones = $survey->groups()->select('codigo_grupo_pregunta_encuesta', 'nombre_grupo_pregunta_encuesta', 'desc_grupo_pregunta_encuesta', 'pagina_grupo_pregunta')->get();
				}
			break;
		}

		return $object;
   	}

    public static function getSurveys()
    {
        $query = Encuesta::query();
		$query->select('encuesta.codigo_encuesta as id', 'nombre_encuesta as text');
        $query->join('core.encuesta_institucion', function($join){
            $join->on('encuesta.codigo_encuesta', '=', 'encuesta_institucion.codigo_encuesta')
                ->where('encuesta_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('encuesta_institucion.codigo_estado', '=', Estado::getState('active'));

        });

		$query->where(function($query)
		{
			$query->where('encuesta_institucion.codigo_lugar_aplicacion', '=', null);
			$query->where('encuesta_institucion.codigo_recurso', '=', null);
		});

		if(Input::has('data')) {
			$query->orWhere('encuesta_institucion.codigo_encuesta', '=', Input::get('data'));
		}

		// Filters
		if(Input::has('q')) {
 			$query->whereRaw("nombre_encuesta ilike '%".Input::get('q')."%'");
 		}

		$query->orderBy('text', 'asc');
		return $query->get();
	}

	/**
	 * Custom resource and module.
	 * metodo copiado de plataforma para
	 * carga de respuestas
	 */
	public function getResource($request): Object
	{
		$response = new \stdClass();
		$response->module = null;
		$response->resource = null;

		$surveyInstitution = null;

		// Programs
		if(!empty($request->codigo_convocatoria)) {
			//
			if(!$request->codigo_convocatoria || !is_numeric($request->codigo_convocatoria)) {
				throw new \Exception(sprintf('%s:%s', 'Encuesta::getResource', "codigo_convocatoria no es un numerico $request->codigo_convocatoria"));
			}

			$surveyInstitution = $this->encuesta_institucion()->join('core.lugar_aplicacion', 'lugar_aplicacion.codigo_lugar_aplicacion', '=', 'encuesta_institucion.codigo_lugar_aplicacion')
									->where('codigo_institucion', $request->codigo_institucion)
									->where('codigo_recurso', $request->codigo_convocatoria)
									->where('codigo_modulo', Modulo::getModule('calls'))
									->first();

		}else if(!empty($request->url_fase)) {
			// Find phase
			$fase = Fase::where('url_fase', $request->url_fase)->first();
			if(!$fase instanceof Fase) {
				throw new \Exception(sprintf('%s:%s', 'Encuesta::getResource', "Fase url_fase $request->url_fase"));
			}

			$surveyInstitution = $this->encuesta_institucion()
									->join('core.lugar_aplicacion', 'lugar_aplicacion.codigo_lugar_aplicacion', '=', 'encuesta_institucion.codigo_lugar_aplicacion')
									->where('codigo_institucion', $request->codigo_institucion)
									->where('codigo_recurso', $fase->codigo_fase)
									->where('codigo_modulo', Modulo::getModule('phases'))
									->first();

		}else if(!empty($request->module)){
			//
			$surveyInstitution = $this->encuesta_institucion()
									->join('core.lugar_aplicacion', 'lugar_aplicacion.codigo_lugar_aplicacion', '=', 'encuesta_institucion.codigo_lugar_aplicacion')
									->where('codigo_institucion', $request->codigo_institucion)
									->where('codigo_recurso', $request->codigo_institucion)
									->where('codigo_modulo', Modulo::getModule( $request->module ))
									->first();
		}else{
			//
			$surveyInstitution = $this->encuesta_institucion()
									->join('core.lugar_aplicacion', 'lugar_aplicacion.codigo_lugar_aplicacion', '=', 'encuesta_institucion.codigo_lugar_aplicacion')
									->where('codigo_institucion', $request->codigo_institucion)
									->first();
		}

		// Set module and resource
		if($surveyInstitution instanceof EncuestaInstitucion)
		{
	 		$response->module = $surveyInstitution->codigo_modulo;
	 		$response->resource = $surveyInstitution->codigo_recurso;
		}
		return $response;
	}

	/**
	 * Se ejecuta al completar registro de respuestas convocatoria
	 * metodo utilizado en CargueEncuesta.
	 * Método copiado de plataforma
	 */
	public function callback(Usuario $user, $data, UsuarioEncuesta $userSurvey)
	{
		// Calculate score survey
		// $total_score = $this->calculateScore($userSurvey);

		// Set complete user survey
		$userSurvey->codigo_version_encuesta = $this->codigo_version_encuesta;
		$userSurvey->codigo_estado = Estado::getState('complete');
		// $userSurvey->puntaje_total_encuesta = $total_score;
		$userSurvey->fecha_fin_usuario_encuesta = date('Y-m-d H:i:s');
		$userSurvey->save();

		// If survey is required for apply program
		if($data->codigo_convocatoria)
		{
			$codigo_convocatoria = $data->codigo_convocatoria;
			if(!$codigo_convocatoria || !is_numeric($codigo_convocatoria)) {
				throw new \Exception(sprintf('%s:%s', 'Encuesta callback', "codigo_convocatoria $codigo_convocatoria"));
			}

			$call = Convocatoria::find($codigo_convocatoria);
			if(!$call instanceof Convocatoria) {
				throw new \Exception(sprintf('%s:%s', 'Encuesta callback', "find codigo_convocatoria $codigo_convocatoria"));
			}

			//método que permite registrar en convocatoria 
			//y registrar en la fase
			$call->apply($user, $userSurvey);

		}

		// After actions
		// if(in_array($this->codigo_encuesta, [Terpel::$challenge])) {
		// 	Terpel::challenge($user, $userSurvey);
		// }

		return true;
	}

   	/**
	 * Defining Relations
	*/
    public function questions()
    {
        return $this->hasMany('App\Models\Core\PreguntaEncuesta','codigo_encuesta','codigo_encuesta');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Core\GrupoPreguntaEncuesta','codigo_encuesta','codigo_encuesta');
    }

	public function encuesta_institucion()
    {
        return $this->hasOne(EncuestaInstitucion::class,'codigo_encuesta','codigo_encuesta');
    }
}
