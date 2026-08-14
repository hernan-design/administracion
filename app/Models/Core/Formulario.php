<?php

namespace App\Models\Core;

use App\Models\BaseModel;

use Input, Cache, Validator, DB;

use App\Models\Core\UsuarioEncuesta,  App\Models\Core\OpcionPreguntaEncuesta, App\Models\Core\PreguntaEncuesta, App\Models\Core\Estado;

class Formulario extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.encuesta';

	protected $primaryKey = 'codigo_encuesta';

	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['nombre_encuesta', 'label_boton_finalizar_encuesta', 'codigo_tipo_encuesta', 'codigo_orientacion_encuesta', 'codigo_privacidad_contenido', 'codigo_estado', 'color_encuesta', 'color_fondo_encuesta', 'descripcion_encuesta', 'imagen_encuesta', 'respuesta_encuesta', 'tipo_paginacion_encuesta', 'paginacion_encuesta'];

	protected $nullable = ['paginacion_encuesta'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'nombre_encuesta'				=> 'required',
			'codigo_tipo_encuesta'			=> 'required',
			'codigo_orientacion_encuesta'	=> 'required',
			'codigo_privacidad_contenido' 	=> 'required',
			'descripcion_encuesta'			=> 'required',
			'respuesta_encuesta'			=> 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	* Pagination type
	*
	* @var array
	*/
	public static $pagination = [1 => 'Preguntas', 2  => 'Páginas'];

	public static function getPagination($pagination){
		switch ($pagination) {
			case 'questions': return 1;
			case 'groups': return 2;
			default: return 'Not pagination identified';
		}
	}

	public static function getAdvanceType($type) {
    	switch ($type) :
		    case 'page': return 1;
		    case 'questions': return 2;
			default: return 'Not type advance identified';
		endswitch;
    }

	public static function getFormsAlls(array $filters = [])
	{
		$query = Formulario::query();
		$query->select('encuesta.codigo_encuesta', 'nombre_encuesta', 'descripcion_encuesta', 'encuesta_institucion.codigo_estado', 'codigo_version_encuesta');
		$query->join('core.encuesta_institucion', function ($join){
			$join->on('encuesta.codigo_encuesta', '=', 'encuesta_institucion.codigo_encuesta')
				->where('encuesta_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		if(isset($filters['nombre_encuesta']) && !empty($filters['nombre_encuesta'])){
			$query->whereRaw("nombre_encuesta ilike '%".trim($filters['nombre_encuesta'])."%'");
		}

		if(isset($filters['codigo_estado']) && !empty($filters['codigo_estado'])){
			$query->where("encuesta_institucion.codigo_estado", $filters['codigo_estado']);
		}

		$query->orderby('fecha_encuesta', 'desc');

		return $query->get();
	}

	public static function getForm($id)
	{
		$query = Formulario::query();
		$query->select('encuesta.codigo_encuesta', 'nombre_encuesta', 'descripcion_encuesta', 'encuesta_institucion.codigo_estado', 'codigo_tipo_encuesta', 'codigo_orientacion_encuesta', 'clase_encuesta', 'label_boton_finalizar_encuesta', 'color_fondo_encuesta', 'color_encuesta', 'imagen_encuesta', 'codigo_privacidad_contenido', 'cancelar_encuesta', 'tipo_paginacion_encuesta', 'codigo_version_encuesta', 'respuesta_encuesta', 'paginacion_encuesta');
		$query->join('core.encuesta_institucion', function ($join){
			$join->on('encuesta.codigo_encuesta', '=', 'encuesta_institucion.codigo_encuesta')
				->where('encuesta_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->where('encuesta.codigo_encuesta', '=', $id);
		return $query->first();
	}

	public static function getForms(array $filters = [])
    {
    $query = Encuesta::query();
		$query->select('encuesta.codigo_encuesta as id', 'nombre_encuesta as text');
        $query->join('core.encuesta_institucion', function($join){
            $join->on('encuesta.codigo_encuesta', '=', 'encuesta_institucion.codigo_encuesta')
                ->where('encuesta_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('encuesta_institucion.codigo_estado', '=', Estado::getState('active'))
				->where('encuesta_institucion.codigo_lugar_aplicacion', '=', null)
				->where('encuesta_institucion.codigo_recurso', '=', null);
        });

		// $query->where(function($query)
		// {
		// 	$query->where('encuesta_institucion.codigo_lugar_aplicacion', '=', null);
		// 	$query->where('encuesta_institucion.codigo_recurso', '=', null);
		// });

		// if(isset($filters['data']) && !empty($filters['data'])) {
		// 	$query->orWhere('encuesta_institucion.codigo_encuesta', '=', $filters['data']);
		// }

		// Filters
		if(isset($filters['q']) && !empty($filters['q'])) {
 			$query->whereRaw("nombre_encuesta ilike '%".$filters['q']."%'");
 		}

		$query->orderBy('text', 'asc');
		return $query->get();
    }

	public static function buildForm(Formulario $survey, $userSurvey = null, $arAnswers = null)
   	{
   		$object = new \stdClass();
		$object->codigo_encuesta = $survey->codigo_encuesta;
		$object->url_encuesta = $survey->url_encuesta;
		$object->nombre_encuesta = $survey->nombre_encuesta;
		$object->descripcion_encuesta = $survey->descripcion_encuesta;
		$object->clase_encuesta = $survey->clase_encuesta;

		$object->label_finalizar = $survey->label_boton_finalizar_encuesta;
		$object->color_fondo_encuesta = $survey->color_fondo_encuesta;
		$object->color_encuesta = $survey->color_encuesta;
		$object->paginacion_encuesta = $survey->paginacion_encuesta;
		$object->redirect_to = $survey->redirect_encuesta;
		$object->preguardado_encuesta = $survey->preguardado_encuesta;
		$object->cancelar_encuesta = $survey->cancelar_encuesta;
		$object->tipo_paginacion_encuesta = $survey->tipo_paginacion_encuesta;
		$object->tipo_porcentaje_avance = $survey->tipo_porcentaje_avance;
		$object->versions = $survey->versions()->select('codigo_version_encuesta', 'codigo_encuesta', 'codigo_estado', 'fecha_version_encuesta', 'comentario_version_encuesta')->where('codigo_estado', Estado::getState('active'))->get();
		$object->codigo_version_encuesta = $survey->codigo_version_encuesta;
		$object->version_encuesta_editable = $survey->current_version()->where('codigo_version_encuesta', '=', $survey->codigo_version_encuesta)->first()->version_encuesta_editable;

		// Survey version
		$version = $survey->codigo_version_encuesta;

		// Prepare questions
		$query = $survey->questions();
		$query->select('pregunta_encuesta.*', 'ver_preg_encuesta.numero_pregunta_version as numero_pregunta_encuesta', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta', 'sigla_moneda', 'ver_preg_encuesta.clase_pregunta_encuesta', 'pregunta_encuesta.codigo_pagina_pregunta_encuesta');

		$query->join('core.ver_preg_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'ver_preg_encuesta.codigo_pregunta_encuesta');
		$query->leftJoin('core.subpregunta_encuesta as pregunta', function($join) {
			$join->on('pregunta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
			$join->on('pregunta.codigo_version_encuesta', '=', 'ver_preg_encuesta.codigo_version_encuesta');
			$join->where('pregunta.codigo_estado', '=', Estado::getState('active'));
        });
		$query->leftJoin('core.moneda', 'pregunta_encuesta.codigo_moneda', '=', 'moneda.codigo_moneda');

		$query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('ver_preg_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('pregunta_encuesta.clase_pregunta_encuesta_v1', '=', 1);
		$query->where('ver_preg_encuesta.codigo_version_encuesta', $version);

		$query->orderBy('ver_preg_encuesta.numero_pregunta_version', 'asc');
		$query->orderBy('pregunta.orden_subpregunta_encuesta', 'desc');
		$query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
		$arQuestions = $query->get();

		if($arQuestions->count() > 0) {
			// Percentage advance
			$object->total_preguntas_padre = $arQuestions->where('clase_pregunta_encuesta', PreguntaEncuesta::getClass('question'))->count();
			$object->total_respuestas_padre = 0;

			if($survey->tipo_porcentaje_avance == self::getAdvanceType('questions')) {
				if(Auth::check() && $userSurvey instanceof UsuarioEncuesta) {
					$object->total_respuestas_padre = $userSurvey->answers($version);
				}
			}

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
				$item->codigo_pagina_pregunta_encuesta = $question->codigo_pagina_pregunta_encuesta;

				// Only matriz titile of left options
				$item->titulo_opciones_pregunta = $question->titulo_opciones_pregunta;

				// Currency questions
				$item->codigo_moneda = $question->codigo_moneda;
				$item->sigla_moneda = $question->sigla_moneda;

				// Options questions closed, unique or mulplique answer (y-axis)
				$options = [];
				if(in_array($question->tipo_pregunta_encuesta, [PreguntaEncuesta::getType('open'), PreguntaEncuesta::getType('single'), PreguntaEncuesta::getType('multiple'), PreguntaEncuesta::getType('matriz')])) {
					// Choices closed questions

					$arChoices = OpcionPreguntaEncuesta::where('codigo_estado', Estado::getState('active'))->where('codigo_pregunta_encuesta', '=', $question->codigo_pregunta_encuesta)->orderBy('orden_opcion_encuesta', 'asc')->orderBy('codigo_opcion_encuesta', 'asc')->get();

					if($arChoices->count() > 0) {
						foreach ($arChoices as $choice) {
							$choiceItem = new \stdClass();
							$choiceItem->codigo_opcion_encuesta = $choice->codigo_opcion_encuesta;
							$choiceItem->texto_opcion_encuesta = $choice->texto_opcion_encuesta;
							$choiceItem->calculado_opcion_encuesta = $choice->calculado_opcion_encuesta;
							$choiceItem->referencia_opcion_encuesta = $choice->referencia_opcion_encuesta;
							$choiceItem->tipo_campo_opcion = $choice->tipo_campo_opcion;
							$choiceItem->codigo_pregunta_encuesta = $choice->codigo_pregunta_encuesta;
							$choiceItem->orden_opcion_encuesta = $choice->orden_opcion_encuesta;
							$choiceItem->puntaje_opcion_encuesta = $choice->puntaje_opcion_encuesta;
							$choiceItem->codigo_estado = $choice->codigo_estado;
							if($choice->calculado_opcion_encuesta == true){
								$choiceItem->formula_opcion_encuesta = $choice->formula_opcion_encuesta;
							}

							// Options field select in matriz
							$choiceItem->opciones = [];
							if( in_array($choice->tipo_campo_opcion , [PreguntaEncuesta::getTypeField('select')]) ) {
								$choiceItem->opciones = $choice->choices()->select('codigo_item_opcion_encuesta', 'nombre_item_opcion_encuesta')->where('codigo_estado', Estado::getState('active'))->get();
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

							// Options field select in matriz
							$choiceItem->opciones = [];
							if( in_array($choice->tipo_campo_opcion , [PreguntaEncuesta::getTypeField('select')]) ) {
								$choiceItem->opciones = $choice->choices()->select('codigo_item_opcion_encuesta', 'nombre_item_opcion_encuesta')->where('codigo_estado', Estado::getState('active'))->get();
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

				// Add item
				$questions[] = $item;
			}
			// Add questions
			$object->preguntas = $questions;

		}else{
			$object->preguntas = [];
		}

		// Options questions closed unique or mulplique answer

    $queryGroups = GrupoPreguntaEncuesta::query();
  	$queryGroups->join('core.pagina_encuesta', 'pagina_encuesta.codigo_pagina_encuesta', '=', 'grupo_pregunta_encuesta.codigo_pagina_encuesta');
  	$queryGroups->where('grupo_pregunta_encuesta.codigo_encuesta', '=', $survey->codigo_encuesta);
  	$dataGroups = $queryGroups->get();
		$object->agrupaciones = $dataGroups;

		// Prepare Pages

    $queryPages = PaginaEncuesta::query();
    $queryPages->where('codigo_encuesta','=', $survey->codigo_encuesta);
    $dataPages = $queryPages->get();

    $object->paginas = $dataPages;

		return $object;
   	}

	/**
	 * Defining Relations
	*/
	public function usuario_encuesta()
    {
        return $this->hasOne('App\Models\Core\UsuarioEncuesta','codigo_usuario','codigo_usuario');
    }

    public function encuesta_institucion()
    {
        return $this->hasOne('App\Models\Core\EncuestaInstitucion','codigo_encuesta','codigo_encuesta');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Core\PreguntaEncuesta','codigo_encuesta','codigo_encuesta');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Core\GrupoPreguntaEncuesta','codigo_encuesta','codigo_encuesta');
    }

	public function versions()
	{
		return $this->hasMany('App\Models\Core\VersionFormulario', 'codigo_encuesta', 'codigo_encuesta');
	}

	public function current_version()
	{
		return $this->belongsTo('App\Models\Core\VersionFormulario', 'codigo_encuesta', 'codigo_encuesta');
	}
}
