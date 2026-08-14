<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, App, TuProyecto;

use App\Models\Core\Encuesta, App\Models\Core\UsuarioEncuesta, App\Models\Core\AsignacionJurado, App\Models\Core\PuntajeRegistroFase, App\Models\Core\CategoriaProducto, App\Models\Core\RegistroFaseUsuario, App\Models\Core\ItemOpcionEncuesta, App\Models\Core\TipoCampoPregunta, App\Models\Core\RespuestaEncuesta, App\Models\Seguridad\Usuario, App\Models\Core\Estado, Custom\Innpulsa, App\Models\Core\Ciudad, App\Models\Core\SectorInteres, App\Models\Core\NivelEducacion, App\Models\Core\PreguntaEncuesta, App\Models\Core\VerPregEncuesta, App\Models\Core\OpcionPreguntaEncuesta;

class RespuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	/**
     * Export the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
	 * @param  \GuzzleHttp\Client  $client
     */
	public function exportar($id)
	{
		// Decrypt src-params
		list($usuario_encuesta) = TuProyecto::decryptSrcParams($id);
		if(!$usuario_encuesta || !is_numeric($usuario_encuesta)) {
			abort(403);
    }

		$userSurvey = UsuarioEncuesta::find($usuario_encuesta);
		if(!$userSurvey instanceof UsuarioEncuesta){
            abort(404);
		}

    // Preparar preguntas
    $survey = Encuesta::find($userSurvey->codigo_encuesta);
    if(!$survey instanceof Encuesta){
        abort(404);
    }

    // Answers
    $query = RespuestaEncuesta::query();
    $query->select('respuesta_encuesta.*', 'opcion_encuesta.texto_opcion_encuesta','opcion_encuesta.puntaje_opcion_encuesta', 'opc_x.tipo_campo_opcion', 'opcion_encuesta.tipo_campo_opcion as tipo_campo_opcion_y', 'pregunta_encuesta.codigo_pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
    $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
    $query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
    $query->leftjoin('core.opcion_encuesta as opc_x','respuesta_encuesta.cor_codigo_opcion_encuesta','=','opc_x.codigo_opcion_encuesta');
    $query->where('respuesta_encuesta.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
    $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
    $arAnswers = $query->get();

    // Preparar respuestas
    $answers = [];
    foreach ($arAnswers as $answer) {
        switch ($answer->tipo_pregunta_encuesta) {
            case PreguntaEncuesta::getType('open'):
                $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
            break;
            case PreguntaEncuesta::getType('single'):
                $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
            break;
            case PreguntaEncuesta::getType('multiple'):
                $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
            break;
            case PreguntaEncuesta::getType('upload'):
                $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
            break;
            case PreguntaEncuesta::getType('matriz'):
                $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;

                if($answer->tipo_campo_opcion == TipoCampoPregunta::getTypeQuestion('select') || $answer->tipo_campo_opcion_y == TipoCampoPregunta::getTypeQuestion('select')) {
                  $item = ItemOpcionEncuesta::select('nombre_item_opcion_encuesta')->where('codigo_item_opcion_encuesta', (int)$answer->texto_respuesta_encuesta)->first();
                  $answer->texto_respuesta_encuesta = $item instanceof ItemOpcionEncuesta ? $item->nombre_item_opcion_encuesta : null;

                  $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
                }

            break;
        }
    }

    // Questions
    $query = $survey->questions();
    $query->select('pregunta_encuesta.*', 'ver_preg_encuesta.numero_pregunta_version as numero_pregunta_encuesta', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta', 'ver_preg_encuesta.clase_pregunta_encuesta', 'pregunta.codigo_subpregunta_encuesta', 'pregunta.orden_subpregunta_encuesta');
    $query->join('core.ver_preg_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'ver_preg_encuesta.codigo_pregunta_encuesta');
    $query->leftJoin('core.subpregunta_encuesta as pregunta', function($join) {
        $join->on('pregunta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $join->on('pregunta.codigo_version_encuesta', '=', 'ver_preg_encuesta.codigo_version_encuesta');
        $join->where('pregunta.codigo_estado', '=', Estado::getState('active'));
    });

    $query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
    $query->where('ver_preg_encuesta.codigo_estado', Estado::getState('active'));
    $query->where('ver_preg_encuesta.codigo_version_encuesta', $userSurvey->codigo_version_encuesta);
    $query->orderBy('ver_preg_encuesta.numero_pregunta_version', 'asc');
    $query->orderBy('pregunta.orden_subpregunta_encuesta', 'asc');
    $query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
    $arQuestions = $query->get();

    $matriz = $questions = $subquestions = [];
    foreach ($arQuestions as $question) {
        if(!in_array($question->tipo_campo_pregunta, [6, 7])) {
            switch ($question->clase_pregunta_encuesta) {
                case VerPregEncuesta::surveyQuestionClass('question'):
                    if(isset($question->codigo_grupo_pregunta_encuesta) && !empty($question->codigo_grupo_pregunta_encuesta)) {
                        $questions[$question->codigo_grupo_pregunta_encuesta][$question->codigo_pregunta_encuesta] = $question;
                    }else{
                        $questions['detached'][$question->codigo_pregunta_encuesta] = $question;
                    }
                break;
                case VerPregEncuesta::surveyQuestionClass('subquestion'):
                    $subquestions["{$question->pregunta_padre}_{$question->codigo_opcion_encuesta}"][$question->codigo_pregunta_encuesta] = $question;
                break;
            }
            switch ($question->tipo_pregunta_encuesta) {
                case PreguntaEncuesta::getType('matriz'):
                    // Choices closed questions
                    $matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
                        ->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
                        ->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
                        ->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
                        ->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                        ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
                        ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                        ->orderBy('orden_opcion_encuesta', 'asc')
                        ->orderBy('codigo_opcion_encuesta', 'asc')
                        ->get();
                    $matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
                        ->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
                        ->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
                        ->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
                        ->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                        ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
                        ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                        ->orderBy('orden_opcion_encuesta', 'asc')
                        ->orderBy('codigo_opcion_encuesta', 'asc')
                        ->get();
                break;
            }
        }
    }

    // Groups
    $groupings = $survey->groups()->select('codigo_grupo_pregunta_encuesta', 'nombre_grupo_pregunta_encuesta', 'desc_grupo_pregunta_encuesta', 'pagina_grupo_pregunta')->orderBy('pagina_grupo_pregunta', 'asc')->orderBy('codigo_grupo_pregunta_encuesta', 'asc')->get();

    // Preparar ciudades
    $cities = [];
    $citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
    foreach ($citys as $city) {
        $cities["{$city->codigo_ciudad}"] = $city;
    }

    // Preparar sectores
    $sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

    // Preparar Nivel de educación
    $levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

    // Prepare data
    $data = [];
    $data['title'] = "$survey->nombre_encuesta";
    $data['survey'] = $survey;
    $data['levels'] = $levels;
    $data['matriz'] = $matriz;
    $data['cities'] = $cities;
    $data['sectors'] = $sectors;

    $data['userSurvey'] = $userSurvey;
    $data['answers'] = $answers;
    $data['questions'] = $questions;
    $data['subquestions'] = $subquestions;
    $data['groupings'] = $groupings;

    // Prepare PDF
		$pdf = App::make('dompdf.wrapper');
		switch ($userSurvey->id_recurso) {
			case Innpulsa::$challenge_reto1:
			  Innpulsa::report($userSurvey, $data);
        $pdf->loadHTML(view('reports.program.innpulsa.resultadopostulacion', $data)->render());
      break;
      default:
        $pdf->loadHTML(view('reports.forms.default', $data)->render());
      break;
		}
		return $pdf->stream(sprintf('%s_%s.pdf', 'resultado_postulacion', date('YmdHms')));
	}

	public function exportSpecializedService($id) {
    list($usuario_encuesta) = TuProyecto::decryptSrcParams($id);
    if(!$usuario_encuesta || !is_numeric($usuario_encuesta)) {
      abort(403);
    }

    $userSurvey = UsuarioEncuesta::find($usuario_encuesta);
    if(!$userSurvey instanceof UsuarioEncuesta){
      abort(404);
    }

    // Preparar preguntas
    $survey = Encuesta::find($userSurvey->codigo_encuesta);
    if(!$survey instanceof Encuesta){
      abort(404);
    }

    // Answers
    $query = RespuestaEncuesta::query();
    $query->select('respuesta_encuesta.*', 'texto_opcion_encuesta','puntaje_opcion_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
    $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
    $query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
    $query->where('respuesta_encuesta.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
    $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
    $arAnswers = $query->get();

    // Preparar respuestas
    $answers = [];
    foreach ($arAnswers as $answer) {
      switch ($answer->tipo_pregunta_encuesta) {
        case PreguntaEncuesta::getType('open'):
          $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
          break;
        case PreguntaEncuesta::getType('single'):
          $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
          break;
        case PreguntaEncuesta::getType('multiple'):
          $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
          break;
        case PreguntaEncuesta::getType('upload'):
          $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
          break;
        case PreguntaEncuesta::getType('matriz'):
          $answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
        break;
      }
    }

    // Questions
    $query = $survey->questions();
    $query->select('pregunta_encuesta.*', 'ver_preg_encuesta.numero_pregunta_version as numero_pregunta_encuesta', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta', 'ver_preg_encuesta.clase_pregunta_encuesta', 'pregunta.codigo_subpregunta_encuesta', 'pregunta.orden_subpregunta_encuesta');
    $query->join('core.ver_preg_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'ver_preg_encuesta.codigo_pregunta_encuesta');
    $query->leftJoin('core.subpregunta_encuesta as pregunta', function($join) {
      $join->on('pregunta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
      $join->on('pregunta.codigo_version_encuesta', '=', 'ver_preg_encuesta.codigo_version_encuesta');
      $join->where('pregunta.codigo_estado', '=', Estado::getState('active'));
    });

    $query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
    $query->where('ver_preg_encuesta.codigo_estado', Estado::getState('active'));
    $query->where('ver_preg_encuesta.codigo_version_encuesta', $userSurvey->codigo_version_encuesta);
    $query->orderBy('ver_preg_encuesta.numero_pregunta_version', 'asc');
    $query->orderBy('pregunta.orden_subpregunta_encuesta', 'asc');
    $query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
    $arQuestions = $query->get();

    $matriz = $questions = $subquestions = [];
    foreach ($arQuestions as $question) {
      if(!in_array($question->tipo_campo_pregunta, [6, 7])) {
        switch ($question->clase_pregunta_encuesta) {
          case VerPregEncuesta::surveyQuestionClass('question'):
            if(isset($question->codigo_grupo_pregunta_encuesta) && !empty($question->codigo_grupo_pregunta_encuesta)) {
              $questions[$question->codigo_grupo_pregunta_encuesta][$question->codigo_pregunta_encuesta] = $question;
            }else{
              $questions['detached'][$question->codigo_pregunta_encuesta] = $question;
            }
            break;
          case VerPregEncuesta::surveyQuestionClass('subquestion'):
            $subquestions["{$question->pregunta_padre}_{$question->codigo_opcion_encuesta}"][$question->codigo_pregunta_encuesta] = $question;
            break;
        }
        switch ($question->tipo_pregunta_encuesta) {
          case PreguntaEncuesta::getType('matriz'):
            // Choices closed questions
            $matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
              ->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
              ->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
              ->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
              ->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
              ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
              ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
              ->orderBy('orden_opcion_encuesta', 'asc')
              ->orderBy('codigo_opcion_encuesta', 'asc')
              ->get();
            $matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
              ->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
              ->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
              ->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
              ->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
              ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
              ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
              ->orderBy('orden_opcion_encuesta', 'asc')
              ->orderBy('codigo_opcion_encuesta', 'asc')
              ->get();
            break;
        }
      }
    }

    // Groups
    $groupings = $survey->groups()->select('codigo_grupo_pregunta_encuesta', 'nombre_grupo_pregunta_encuesta', 'desc_grupo_pregunta_encuesta', 'pagina_grupo_pregunta')->orderBy('pagina_grupo_pregunta', 'asc')->orderBy('codigo_grupo_pregunta_encuesta', 'asc')->get();

    // Prepare data
    $data = [];
    $data['title'] = "$survey->nombre_encuesta";
    $data['survey'] = $survey;
    $data['matriz'] = $matriz;

    $data['userSurvey'] = $userSurvey;
    $data['answers'] = $answers;
    $data['questions'] = $questions;
    $data['subquestions'] = $subquestions;
    $data['groupings'] = $groupings;

    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('reports.forms.default', $data)->render());

    return $pdf->stream(sprintf('%s_%s.pdf', 'resultado_postulacion', date('YmdHms')));
  }

  public function exportarPDF($register) {
    // Decrypt src-params
    list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($register);
    if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
      return response()->json(['success' => false, 'errors' => 'El código de registro fase no es un número ']);
    }

    $register = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
    if(!$register instanceof RegistroFaseUsuario) {
      abort(404);
      return response()->json(['success' => false, 'errors' => 'No se encontro el registro fase usuario']);
    }

    $score = PuntajeRegistroFase::where('codigo_registro_fase_usuario', $register->codigo_registro_fase_usuario)->first();
    if(!$score instanceof PuntajeRegistroFase) {
      DB::rollback();
      return response()->json(['success' => false, 'errors' => 'Aun no se ha evaluado en este momento']);
    }

    // Get Data User
    $dataUser = Usuario::getUserByEntity($register->codigo_usuario);
    if(!$dataUser instanceof Usuario) {
      return response()->json(['success' => false, 'errors' => 'La entidad no pertenece a un usuario registrado.']);
    }

    $recomendations = AsignacionJurado::select('resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion')
      ->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=','asignacion_jurado.codigo_asignacion_jurado')
      ->where('asignacion_jurado.codigo_registro_fase_usuario', $register->codigo_registro_fase_usuario)->get();

    if($register->valor_asignacion_rfu > 0) {
      $categories = CategoriaProducto::getCategories($register->codigo_registro_fase_usuario);

      $data = [];
      $data['title'] = 'Notificación Reto 2 ¡Conversa con Expertos! de ALDEA';
      $data['entity_name'] = $dataUser->entity_name;
      $data['recomendations'] = $recomendations;
      $data['categories'] = $categories;
      $data['score'] = $score->valor_puntaje_registro_fase;
      $data['total_keys'] = $register->valor_asignacion_rfu;
      $data['fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));

      $pdf = App::make('dompdf.wrapper');
      $pdf->loadHTML(view('reports.program.innpulsa.response', $data)->render());
      return $pdf->stream(sprintf('%s_%s.pdf', 'resultado_reto_2', date('YmdHms')));
    } else {

      $data = [];
      $data['title'] = 'Notificación Reto 2 ¡Conversa con Expertos! de ALDEA';
      $data['entity_name'] = $dataUser->entity_name;
      $data['recomendations'] = $recomendations;
      $data['score'] = $score->valor_puntaje_registro_fase;
      $data['total_keys'] = $register->valor_asignacion_rfu;
      $data['fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));

      $pdf = App::make('dompdf.wrapper');
      $pdf->loadHTML(view('reports.program.innpulsa.response_refuse', $data)->render());
      return $pdf->stream(sprintf('%s_%s.pdf', 'resultado_reto_2', date('YmdHms')));
    }
  }
}
