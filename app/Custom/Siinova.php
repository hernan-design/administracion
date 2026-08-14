<?php

namespace App\Custom;

use App\Models\Core\AsignacionJurado,
    App\Models\Core\Ciudad,
    App\Models\Core\Encuesta,
    App\Models\Core\Estado,
    App\Models\Core\ItemOpcionEncuesta,
    App\Models\Core\RegistroConvocatoria,
    App\Models\Core\OpcionPreguntaEncuesta,
    App\Models\Core\PreguntaEncuesta,
	App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\RespuestaEncuesta,
    App\Models\Core\SectorInteres,
    App\Models\Core\NivelEducacion,
	App\Models\Core\ResultadoEvaluacionEmprendimiento,
    App\Models\Core\TipoCampoPregunta,
	App\Models\Seguridad\Usuario,
    App\Models\Core\UsuarioEncuesta,
    App\Models\Core\VerPregEncuesta;

use Exception, Log;

class Siinova
{

    /**
	 * Se definen atributos.
	 * @Annotation atributo $id_institution equivale al codigo_institucion de Siinova Bolivar.
	 * @Annotation el codigo_encuesta_datos (287) hace referencia a la encuesta de datos básicos.
	 * @access public
	 */
	public static $id_institution = 62;
	public static $id_group = [58, 59, 60, 61, 62, 69];
	public static $codigo_evaluacion_datos_basicos = 29; //Código evaluación datos básicos.
    public static $codigo_evaluacion_diagnostico = 30; //Código evaluacion diagnóstico.
    public static $codigo_encuesta_287 = 287; //Encuesta datos básicos 287.
    public static $codigo_encuesta_292 = 292; //Encuesta diagnóstico 292.
    public static $codigo_convocatoria_648 = 648; //Convocatoria datos básicos 648 bolivar.
    public static $codigo_convocatoria_652 = 652; //Convocatoria diagnóstico 652 bolivar.
	public static $id_phase = 367; //Fase registro convocatoria diagnóstico

    /**
	 * Obtiene las respuestas que se incluiran en el pdf.
	 *
	 * @access public
	 * @static function
	 * @param mixed $register, $data, $user_project
	 * @return array|boolean $data_pdf
	 */
	public static function buildAnswersPdf(UsuarioEncuesta $user_survey)
    {
        try {
                if ( !is_null($user_survey) ) {
                    //Consulta datos del registro a la convocatoria 648 datos básicos mediante codigos encuesta y usuario.
                $register_annoucement_648 = RegistroConvocatoria::getRegisterAnnouncement($user_survey->codigo_usuario, self::$codigo_encuesta_287);
                //Consulta respuestas de la encuesta 287 datos básicos, convocatoria 648.
                $answers_survey_287 = self::getAnswerSurvey_287($register_annoucement_648->codigo_usuario_encuesta);
                sleep(0.1);
                //Consulta respuestas de la evaluación 29 diagnóstico bolivar.
                $answers_evaluation_29 = AsignacionJurado::getAnswerEvaluation($register_annoucement_648->codigo_registro_fase_usuario, self::$codigo_evaluacion_datos_basicos);
                sleep(0.1);
                //Consulta datos del registro a la convocatoria 652 datos básicos mediante codigos encuesta y usuario.
                $register_annoucement_652 = RegistroConvocatoria::getRegisterAnnouncement($user_survey->codigo_usuario, self::$codigo_encuesta_292);
                //Consulta respuestas de la encuesta 292 diagnóstico, convocatoria 648.
                $type_pdf= self::validateTypePdf($register_annoucement_652->codigo_usuario_encuesta);
                sleep(0.1);
                //Consulta los datos de la encuesta 292 diagnóstico, convocatoria 648 para implementar en reporte pdf.
                $data_answers_announcement = self::getAnswerSurvey_292($register_annoucement_652->codigo_usuario_encuesta);
                sleep(0.1);
                //Consulta los puntajes de la evaluación 30 diagnóstico bolivar.
                $id_eval_result = ResultadoEvaluacionEmprendimiento::getUserPhaseRecord($user_survey->codigo_usuario);
                $answers_evaluation_30 = ResultadoEvaluacionEmprendimiento::getScoring($id_eval_result, self::$id_group);
                sleep(0.1);
                $data_entity = Usuario::getUserEntity($user_survey->codigo_usuario);
                $data_user = Usuario::getDataUserById($user_survey->codigo_usuario);

                //Valida que tipo de scoring genera, sirve para adecuar espacio en pdf (margin-top).
                if ($type_pdf[6337] == 13762) {
                    $type_scoring = true; //Aplica cuando es producto.
                } else {
                    $type_scoring = false; //Aplica cuando es servicio.
                }

                $key_6480= $data_answers_announcement['userSurvey']['codigo_usuario_encuesta'] . "_6480";
                $key_6481= $data_answers_announcement['userSurvey']['codigo_usuario_encuesta'] . "_6481";
                $key_6482= $data_answers_announcement['userSurvey']['codigo_usuario_encuesta'] . "_6482";

                $legal_representative = $data_answers_announcement['answers'][$key_6480]['texto_respuesta_encuesta'];
                $legal_repre_phone_number = $data_answers_announcement['answers'][$key_6481]['texto_respuesta_encuesta'];
                $legal_repre_email = isset($data_answers_announcement['answers'][$key_6482]['texto_respuesta_encuesta']) ? $data_answers_announcement['answers'][$key_6482]['texto_respuesta_encuesta'] : "";

                //Preparando los datos que contendrá el pdf.
                $data_pdf = [];
                $data_pdf['legal_representative']       = mb_convert_case($legal_representative, MB_CASE_TITLE, "UTF-8");
                $data_pdf['legal_repre_phone_number']   = $legal_repre_phone_number;
                $data_pdf['legal_repre_email']          = $legal_repre_email;
                $data_pdf['title']                      = "Diagnóstico De Radar";
                $data_pdf['_type_scoring']              = $type_scoring;
                $data_pdf['_answers_eval']              = $answers_evaluation_30;
                $data_pdf['name_entity']                = $data_entity->nombre_usuario;
                $data_pdf['nit']                        = $data_entity->identificacion_usuario;
                $data_pdf['name_contact']               = $answers_survey_287[6319];
                $data_pdf['address']                    = $answers_survey_287[6320];
                $data_pdf['phone']                      = $answers_survey_287[6321];
                $data_pdf['municipality']               = $answers_survey_287[6324]['municipio'];
                $data_pdf['province']                   = $answers_survey_287[6324]['departamento'];
                $data_pdf['email_contact']              = $data_user->correo_electronico;
                $data_pdf['economic_sector']            = $answers_evaluation_29[266];
                $data_pdf['web_page']                   = $data_entity->pagina_web_usuario ? $data_entity->pagina_web_usuario : 'No';
                $data_pdf['title']                      = $data_answers_announcement['title'];
                $data_pdf['survey']                     = $data_answers_announcement['survey'];
                $data_pdf['levels']                     = $data_answers_announcement['levels'];
                $data_pdf['matriz']                     = $data_answers_announcement['matriz'];
                $data_pdf['cities']                     = $data_answers_announcement['cities'];
                $data_pdf['sectors']                    = $data_answers_announcement['sectors'];
                $data_pdf['userSurvey']                 = $data_answers_announcement['userSurvey'];
                $data_pdf['answers']                    = $data_answers_announcement['answers'];
                $data_pdf['questions']                  = $data_answers_announcement['questions'];
                $data_pdf['subquestions']               = $data_answers_announcement['subquestions'];
                $data_pdf['groupings']                  = $data_answers_announcement['groupings'];

                if ( count($data_pdf) >0 ) {
                    return $data_pdf;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        } catch (Exception $e) {
            $message = $e->getMessage();
			Log::error(sprintf('%s:%s', 'App\Custom\Commands\Siinova', $message));
            return false;
        }
	}
    /**
     * Obtiene respuestas de la encuesta datos básicos 287.
	 *
     * @access private
	 * @static function
     * @param int $id_user_survey
     * @return array $basic_data
     */
    private static function getAnswerSurvey_287(int $id_user_survey)
	{

		$answers = RespuestaEncuesta::getAnswers($id_user_survey);

        $basic_data = [];

        foreach ($answers as $key => $value) {
            if ($value['codigo_pregunta_encuesta'] == 6319) {
                $basic_data [6319] = mb_convert_case(trim($value['texto_respuesta_encuesta']), MB_CASE_TITLE, "UTF-8"); //Nombre contacto
            }
            if ($value['codigo_pregunta_encuesta'] == 6320) {
                $basic_data [6320] = mb_convert_case(trim($value['texto_respuesta_encuesta']), MB_CASE_TITLE, "UTF-8"); //Dirección
            }
            if ($value['codigo_pregunta_encuesta'] == 6321) {
                $basic_data [6321] = trim($value['texto_respuesta_encuesta']); //Teléfono
            }
            if ($value['codigo_pregunta_encuesta'] == 6324) {
                $text = self::getText((int) $value['codigo_opcion_encuesta']); //Zodes-Municipio
                $texts = explode("-", $text);
                $basic_data [6324]['municipio'] = mb_convert_case(trim($texts[0]), MB_CASE_TITLE, "UTF-8"); //Municipio
                $basic_data [6324]['departamento'] = mb_convert_case(trim($texts[1]), MB_CASE_TITLE, "UTF-8"); //Departamento
            }
        }
        return $basic_data;
    }
    /**
     * Obtiene respuestas de la encuesta datos básicos 292.
	 *
     * @access private
	 * @static function
     * @param int $id_user_survey
     * @return array $data_validate
     */
	private static function validateTypePdf(int $id_user_survey)
	{
		$answers = RespuestaEncuesta::getAnswers($id_user_survey);
		$answers = $answers->toArray();

		$data_validate = [];

        foreach ($answers as $key => $value) {

			if ($value['codigo_pregunta_encuesta'] == 6337) {
				$data_validate[6337] = $value['codigo_opcion_encuesta'];
			}
        }

        return $data_validate;
    }
    /**
     * Obtiene textos de respuesta encuesta datos básicos 287.
	 *
     * @access private
	 * @static function
     * @param int $id_code
     * @return array $text
     */
    private static function getText( int $id_code)
    {

        $query = OpcionPreguntaEncuesta::query();
        $query->select('texto_opcion_encuesta');
        $query->where('codigo_opcion_encuesta', $id_code);
        $text = $query->first();

        return $text['texto_opcion_encuesta'];
    }
    /**
     * Obtiene respuestas de la postulación convocatoria 652 diagnóstico bolivar.
	 *
     * @access private
     * @param int $id_user_survey
     * @return array $data
     */
    private static function getAnswerSurvey_292($id_user_survey)
	{
        //Consulta si el usuario contestó la encuesta.
        $user_survey = UsuarioEncuesta::find($id_user_survey);
        if(!$user_survey instanceof UsuarioEncuesta){
            Log::critical('Error => No se encuentra codigo_usuario_encuesta');
            die;
        }

        //Consulta si existe la encuesta.
        $survey = Encuesta::find($user_survey->codigo_encuesta);
        if(!$survey instanceof Encuesta){
            abort(404);
        }

        //Respuestas de la encuesta.
        $query = RespuestaEncuesta::query();
        $query->select('respuesta_encuesta.*', 'opcion_encuesta.texto_opcion_encuesta','opcion_encuesta.puntaje_opcion_encuesta', 'opc_x.tipo_campo_opcion', 'opcion_encuesta.tipo_campo_opcion as tipo_campo_opcion_y', 'pregunta_encuesta.codigo_pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
        $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
        $query->leftjoin('core.opcion_encuesta as opc_x','respuesta_encuesta.cor_codigo_opcion_encuesta','=','opc_x.codigo_opcion_encuesta');
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', $user_survey->codigo_usuario_encuesta);
        $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $ar_answers = $query->get();

        //Preparar respuestas de la encuesta.
        $answers = [];
        foreach ($ar_answers as $answer) {
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

        //Preguntas de la encuesta.
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
        $query->where('ver_preg_encuesta.codigo_version_encuesta', $user_survey->codigo_version_encuesta);
        $query->orderBy('ver_preg_encuesta.numero_pregunta_version', 'asc');
        $query->orderBy('pregunta.orden_subpregunta_encuesta', 'asc');
        $query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
        $ar_questions = $query->get();

        $matriz = $questions = $subquestions = [];
        foreach ($ar_questions as $question) {
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

        // Grupos.
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

        //Prepara datos
        $data = [];
        $data['title'] = "$survey->nombre_encuesta";
        $data['survey'] = $survey;
        $data['levels'] = $levels;
        $data['matriz'] = $matriz;
        $data['cities'] = $cities;
        $data['sectors'] = $sectors;

        $data['userSurvey'] = $user_survey;
        $data['answers'] = $answers;
        $data['questions'] = $questions;
        $data['subquestions'] = $subquestions;
        $data['groupings'] = $groupings;

        return $data;
    }
}
