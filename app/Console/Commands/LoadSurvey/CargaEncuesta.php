<?php

namespace App\Console\Commands\LoadSurvey;

// use Illuminate\Support\Collection;
use TuProyecto, DB, Log;
use App\Models\Core\Adjunto,
    App\Models\Core\AdjuntoRespuestaEncuesta,
    App\Models\Core\AsignacionEvaluadores,
    App\Models\Core\AsignacionJurado,
    App\Models\Core\Convocatoria,
    App\Models\Core\Encuesta,
    App\Models\Core\Estado,
    App\Models\Core\OpcionPreguntaEncuesta,
    App\Models\Core\Persona,
    App\Models\Core\PreguntaEncuesta,
    App\Models\Core\RespuestaEncuesta,
    App\Models\Core\RegistroConvocatoria,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\TipoAdjunto,
    App\Models\Core\UsuarioEncuesta,
    App\Models\Seguridad\Usuario,
    App\Models\Seguridad\Rol;


class CargaEncuesta
{
    /**
	 * Guarda respuestas del formulario de convocatoria.
	 *
	 * @param  object  $data = new \stdClass();
     *                 $data->codigo_usuario = $codigo_usuario;
     *                 $data->codigo_encuesta = CODIGO_ENCUESTA;
     *                 $data->codigo_convocatoria = CODIGO_CONVOCATORIA;
     *                 $data->codigo_institucion = ID_INSTITUTION;
     *                 $data->answers_form_convocatory = array();
	 * @return void
	 */
	public static function store($data)
	{

        $survey = Encuesta::find($data->codigo_encuesta);
        if(!$survey instanceof Encuesta) {
            $message = 'no se encuentra la encuesta de codigo: ' . $data->codigo_encuesta;
            Log::error(sprintf('%s:%s', 'EncuestaController update', $message));
            throw new \Exception(sprintf('%s:%s', 'CargaEncuesta::store', "No se encuentra Encuesta"));
        }

        //preguntas configuradas para la encuesta
        //el cual es un collection
        $questions = $survey->questions()
                            ->where('pregunta_encuesta.codigo_estado', Estado::getState('active'))
                            ->get();

        if($questions->count()) {

            // Custom actions before insert survey
            try {
                // Resources
                $userSurvey = $user = null;

                //buscar usuario por codigo usuario
                $user = Usuario::find($data->codigo_usuario);
                if(!$user instanceof Usuario) {
                    throw new \Exception(sprintf('%s:%s', 'CargaEncuesta::store', "no se encuesta usuario $data->codigo_usuario"));
                }

                $resource = $survey->getResource($data);

                if(is_null($resource->resource) || is_null($resource->module)) {
                    throw new \Exception(sprintf('%s:%s', 'CargaEncuesta::store', "No es posible definir modulo ($resource->resource) y recurso ($resource->module)"));
                }

                $userSurvey = UsuarioEncuesta::where('codigo_encuesta', $survey->codigo_encuesta)
                                                ->where('codigo_usuario', $user->codigo_usuario)
                                                ->where('codigo_modulo', $resource->module)
                                                ->where('id_recurso', $resource->resource)
                                                ->orderByDesc('codigo_usuario_encuesta')
                                                ->first();

                if(!$userSurvey instanceof UsuarioEncuesta) {
                    $userSurvey = new UsuarioEncuesta;
                }

                $userSurvey->codigo_encuesta = $survey->codigo_encuesta;
                $userSurvey->codigo_usuario = $user->codigo_usuario;
                $userSurvey->codigo_modulo = $resource->module;
                $userSurvey->id_recurso = $resource->resource;
                $userSurvey->codigo_estado = Estado::getState('active');
                $userSurvey->fecha_usuario_encuesta = date('Y-m-d');
                $userSurvey->hora_usuario_encuesta = date('H:i:s');
                $userSurvey->save();
                if(!$userSurvey instanceof UsuarioEncuesta) {
                    throw new \Exception(sprintf('%s:%s', 'CargaEncuesta::store', "No es posible definir usuario encuesta"));
                }

            } catch (\Exception $e){
                Log::error(sprintf('%s:%s', 'EncuestaController update', $e->getMessage()));
                throw new \Exception("Error Processing Request CargaEncuesta::store try 1");
            }

            // Init save survey
            try {
                foreach ($questions as $question)
                {
                    switch ($question->tipo_pregunta_encuesta) {
                        // Case open questions
                        case PreguntaEncuesta::getType('open'):
                            if(!empty($data->answers_form_convocatory[$question->codigo_pregunta_encuesta])) {

                                //recupera respuesta
                                $answer = RespuestaEncuesta::where('codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                                                            ->where('codigo_usuario', $user->codigo_usuario)
                                                            ->where('codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta)
                                                            ->first();
                                //si no hay respuesta la creamos
                                if(!$answer instanceof RespuestaEncuesta) {
                                    $answer = new RespuestaEncuesta;
                                    $answer->codigo_usuario_encuesta = $userSurvey->codigo_usuario_encuesta;
                                    $answer->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
                                    $answer->codigo_usuario = $user->codigo_usuario;
                                }

                                //actualiza o guarda estos campos para respuesta
                                $answer->codigo_estado = Estado::getState('active');
                                $answer->texto_respuesta_encuesta = trim($data->answers_form_convocatory[$question->codigo_pregunta_encuesta]);
                                $answer->periodo_respuesta_encuesta = $question->periodo_pregunta_encuesta;
                                $answer->fecha_respuesta_encuesta = date('Y-m-d');
                                $answer->hora_respuesta_encuesta = date('H:i:s');
                                $answer->save();

                            }
                        break;

                        // Case single questions
                        case PreguntaEncuesta::getType('single'):
                            // if(isset($request->has(sprintf("pregunta_%s_opcion", $question->codigo_pregunta_encuesta)))) {
                            if(isset($data->answers_form_convocatory[$question->codigo_pregunta_encuesta])) {

                                // $choice = OpcionPreguntaEncuesta::find($request->input(sprintf("pregunta_%s_opcion", $question->codigo_pregunta_encuesta)));
                                $choice = OpcionPreguntaEncuesta::find($data->answers_form_convocatory[$question->codigo_pregunta_encuesta]);

                                if(!$choice instanceof OpcionPreguntaEncuesta) {
                                    throw new \Exception(sprintf('%s:%s', "CargaEncuesta  case: PreguntaEncuesta::getType('single')",
                                                                "Error recuperando opción para $question->codigo_pregunta_encuesta single"));
                                }

                                //Recuperar Respuesta
                                $answer = RespuestaEncuesta::where('codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                                                                ->where('codigo_usuario', $user->codigo_usuario)
                                                                ->where('codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta)
                                                                ->first();
                                //si no existe Respuesta la creamos
                                if(!$answer instanceof RespuestaEncuesta) {
                                    $answer = new RespuestaEncuesta;
                                    $answer->codigo_usuario_encuesta = $userSurvey->codigo_usuario_encuesta;
                                    $answer->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
                                    $answer->codigo_usuario = $user->codigo_usuario;
                                }

                                //Actualizamos o guadamos Respuesta
                                $answer->codigo_estado = Estado::getState('active');
                                $answer->codigo_opcion_encuesta = $choice->codigo_opcion_encuesta;
                                $answer->puntaje_respuesta_encuesta = $choice->puntaje_opcion_encuesta;
                                $answer->periodo_respuesta_encuesta = $question->periodo_pregunta_encuesta;
                                $answer->fecha_respuesta_encuesta = date('Y-m-d');
                                $answer->hora_respuesta_encuesta = date('H:i:s');
                                $answer->save();

                            }
                        break;

                        // Case multiple questions
                        case PreguntaEncuesta::getType('multiple'):
                            $choices = $question->choices()->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                                                            ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
                                                            ->get();
                            $flag = true;

                            foreach ($choices as $choice) {
                                // if($request->has(sprintf("pregunta_%s_opcion_%s", $question->codigo_pregunta_encuesta, $choice->codigo_opcion_encuesta))) {
                                if(isset($data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta])) {

                                    if($flag){
                                        $answer = RespuestaEncuesta::where('codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                                                                        ->where('codigo_usuario', $user->codigo_usuario)
                                                                        ->where('codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta)
                                                                        ->delete();
                                        $flag = false;
                                    }

                                    $answer = new RespuestaEncuesta;
                                    $answer->codigo_usuario_encuesta = $userSurvey->codigo_usuario_encuesta;
                                    $answer->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
                                    $answer->codigo_usuario = $user->codigo_usuario;
                                    $answer->codigo_estado = Estado::getState('active');
                                    $answer->codigo_opcion_encuesta = $data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta];
                                    $answer->puntaje_respuesta_encuesta = $choice->puntaje_opcion_encuesta;
                                    $answer->periodo_respuesta_encuesta = $question->periodo_pregunta_encuesta;
                                    $answer->fecha_respuesta_encuesta = date('Y-m-d');
                                    $answer->hora_respuesta_encuesta = date('H:i:s');
                                    $answer->save();

                                }
                            }

                        break;

                        case PreguntaEncuesta::getType('upload'):
                            // if($request->has("pregunta_$question->codigo_pregunta_encuesta")) {
                            //para esta respuesta se carga un string, validamos si no está vacio
                            if(!empty($data->answers_form_convocatory[$question->codigo_pregunta_encuesta])) {

                                //recuparamos Respuesta, sí existe
                                $answer = RespuestaEncuesta::where('codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                                                                ->where('codigo_usuario', $user->codigo_usuario)
                                                                ->where('codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta)
                                                                ->first();
                                //si no existe Respuesta la creamos
                                if(!$answer instanceof RespuestaEncuesta) {
                                    $answer = new RespuestaEncuesta;
                                    $answer->codigo_usuario_encuesta = $userSurvey->codigo_usuario_encuesta;
                                    $answer->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
                                    $answer->codigo_usuario = $user->codigo_usuario;
                                }
                                //actualizamos o guargamos Respuesta
                                $answer->codigo_estado = Estado::getState('active');
                                $answer->texto_respuesta_encuesta = $data->answers_form_convocatory[$question->codigo_pregunta_encuesta];
                                $answer->periodo_respuesta_encuesta = $question->periodo_pregunta_encuesta;
                                $answer->fecha_respuesta_encuesta = date('Y-m-d');
                                $answer->hora_respuesta_encuesta = date('H:i:s');
                                $answer->save();

                                if(in_array($question->tipo_campo_pregunta, [Encuesta::getFieldType('file')])) {

                                    // Prepare filename
                                    $filename = explode('/', $data->answers_form_convocatory[$question->codigo_pregunta_encuesta]);
                                    $attachedAnswer = AdjuntoRespuestaEncuesta::where('codigo_respuesta_encuesta', $answer->codigo_respuesta_encuesta)
                                                                                ->first();
                                    if(!$attachedAnswer instanceof AdjuntoRespuestaEncuesta) {
                                        // Adjunto
                                        $attached = new Adjunto();
                                        $attached->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('document');
                                        $attached->nombre_adjunto = urldecode(substr(end($filename), 0, 250));
                                        $attached->direccion_adjunto = $data->answers_form_convocatory[$question->codigo_pregunta_encuesta];
                                        $attached->fecha_adjunto = date('Y-m-d H:i:s');
                                        $attached->save();

                                        // Adjunto Usuario
                                        $attachedAnswer = new AdjuntoRespuestaEncuesta;
                                        $attachedAnswer->codigo_adjunto = $attached->codigo_adjunto;
                                        $attachedAnswer->codigo_respuesta_encuesta = $answer->codigo_respuesta_encuesta;
                                        $attachedAnswer->codigo_estado = Estado::getState('active');
                                        $attachedAnswer->save();
                                    }else{

                                        $attached = Adjunto::find($attachedAnswer->codigo_adjunto);
                                        if($attached instanceof Adjunto){
                                            $attached->nombre_adjunto = urldecode(substr(end($filename), 0, 250));
                                            $attached->direccion_adjunto = $data->answers_form_convocatory[$question->codigo_pregunta_encuesta];
                                            $attached->save();
                                        }
                                    }
                                }
                            }
                        break;

                        //por ahora no se utiliza éste método
                        case PreguntaEncuesta::getType('matriz'):

                            // Y-Axis
                            $fields = $question->choices()
                                                ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                                                ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
                                                ->orderBy('codigo_opcion_encuesta','asc')
                                                ->get();

                            // X-Axis
                            $choices = $question->choices()
                                                ->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
                                                ->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
                                                ->orderBy('codigo_opcion_encuesta','asc')
                                                ->get();

                            $answersMatriz = [];
                            foreach ($choices as $choice) {
                                foreach ($fields as $field) {
                                    //if( $data->has(sprintf("pregunta_%s_opcion_%s_campo_%s", $question->codigo_pregunta_encuesta, $choice->codigo_opcion_encuesta, $field->codigo_opcion_encuesta)) ) {
                                    //Log::info($data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta][$field->codigo_opcion_encuesta]);

                                        if($data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta][$field->codigo_opcion_encuesta] != null && $data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta][$field->codigo_opcion_encuesta] != '') {
                                        $answer = RespuestaEncuesta::where('codigo_usuario', $user->codigo_usuario)
                                                                        ->where('codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
                                                                        ->where('codigo_opcion_encuesta', $choice->codigo_opcion_encuesta)
                                                                        ->where('cor_codigo_opcion_encuesta', $field->codigo_opcion_encuesta)
                                                                        ->where('codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta)
                                                                        ->first();

                                        if(!$answer instanceof RespuestaEncuesta) {
                                            $answer = new RespuestaEncuesta;
                                            $answer->codigo_usuario_encuesta = $userSurvey->codigo_usuario_encuesta;
                                            $answer->codigo_pregunta_encuesta = $question->codigo_pregunta_encuesta;
                                            $answer->codigo_usuario = $user->codigo_usuario;
                                            $answer->codigo_opcion_encuesta = $field->codigo_opcion_encuesta;
                                            //Log::info("fila: $answer->codigo_opcion_encuesta");
                                            $answer->cor_codigo_opcion_encuesta = $choice->codigo_opcion_encuesta;
                                            //Log::info("columna: $answer->cor_codigo_opcion_encuesta");
                                        }
                                        $answer->codigo_estado = Estado::getState('active');
                                        $answer->texto_respuesta_encuesta = $data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta][$field->codigo_opcion_encuesta];
                                        //Log::info($data->answers_form_convocatory[$question->codigo_pregunta_encuesta][$choice->codigo_opcion_encuesta][$field->codigo_opcion_encuesta]);
                                        //Log::info("answer->texto_respuesta_encuesta: $answer->texto_respuesta_encuesta");
                                        $answer->periodo_respuesta_encuesta = $question->periodo_pregunta_encuesta;
                                        $answer->fecha_respuesta_encuesta = date('Y-m-d');
                                        $answer->hora_respuesta_encuesta = date('H:i:s');
                                        $answer->save();

                                        $answersMatriz["{$choice->codigo_opcion_encuesta}_{$field->codigo_opcion_encuesta}"] = $answer->texto_respuesta_encuesta;
                                    }
                                }
                            }

                        break;
                    }
                }

                // custom actions after insert survey
                $survey->callback($user, $data, $userSurvey);

                //assign evaluator

                if ($data->has_evaluation) {
				    $data_for_evaluation_phase = self::evaluatorAssignment($data, $userSurvey);
                    return $data_for_evaluation_phase;
                }

            } catch (\Exception $e){
                Log::error(sprintf('%s:%s', 'CargaEncuesta store', $e->getMessage()));
                throw new \Exception("Error Processing Request CargaEncuesta::store try 2");
            }
        }

	}

    public static function evaluatorAssignment($data, $user_survey)
    {
        if ($data->email_evaluator) {
            $user = self::getUserMail(['email' => $data->email_evaluator, 'id_institution' => $data->codigo_institucion]);

            if(!$user instanceof Usuario) {
                Log::critical("ERROR => No existe evaluador asociado a esta dirección de correo electrónico.");
                die;
            }
            elseif ($user->identificacion_usuario)
            {
                $user = self::getUsersEvaluators($user->identificacion_usuario, $data->codigo_institucion);
                if(!$user instanceof Usuario) {
                    Log::critical("ERROR => No existe evaluador asociado a ese numero de identificación.");
                    die;
                    }
                }

            // validate phase
            $id_user_record_phase = RegistroFaseUsuario::where('codigo_usuario', '=', $user_survey->codigo_usuario)->orderBy('codigo_registro_fase_usuario', 'desc')->first();

            $record_phase = RegistroFaseUsuario::join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')
            ->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase')->find($id_user_record_phase->codigo_registro_fase_usuario);

            if(!$record_phase instanceof RegistroFaseUsuario) {
                Log::critical("FATAL ERROR...");
                die;
            }

            DB::beginTransaction();
            try {
                $assignment_jury = AsignacionJurado::where('codigo_usuario', '=', $user->codigo_usuario)
                    ->where('codigo_registro_fase_usuario', '=', $record_phase->codigo_registro_fase_usuario)
                    ->whereIn('codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')])
                    ->first();

                if( ($assignment_jury instanceof AsignacionJurado) && in_array($assignment_jury->codigo_estado, [Estado::getState('active'), Estado::getState('evaluated')]))
                {
                    Log::info("Este jurado ($data->email_evaluator) ya se encuentra asignado, se procede a actualizar evaluacion");
                }

                if(!$assignment_jury instanceof AsignacionJurado)
                {
                    $assignment_jury = new AsignacionJurado;
                    $assignment_jury->codigo_usuario =  $user->codigo_usuario;
                    $assignment_jury->codigo_registro_fase_usuario = $record_phase->codigo_registro_fase_usuario;
                    $assignment_jury->asignacion_jurado_virtual = true;
                }

                //$assignment_jury->codigo_especialidad = $request->codigo_especialidad;

                $assignment_jury->lugar_evaluacion_jurado = trim('Virtual');
                //$assignment_jury->fecha_evaluacion_jurado = date('Y-m-d');
                $assignment_jury->codigo_estado = Estado::getState('evaluated');
                $assignment_jury->fecha_asignacion_jurado = date('Y-m-d');
                $assignment_jury->hora_asignacion_jurado = date('H:i:s');
                $assignment_jury->hora_desarrollo_asignacion = date('H:i:s');
                $assignment_jury->save();

                // Commit Transaction
                DB::commit();
                Log::info("Se ha asigando con éxito al evaluador registrado con correo $data->email_evaluator.'");

                $data_for_evaluation_phase = new \stdClass();
                $data_for_evaluation_phase->id_record_convocatory = $record_phase->codigo_registro_convocatoria;
                $data_for_evaluation_phase->id_assignment_jury = $assignment_jury->codigo_asignacion_jurado;
                $data_for_evaluation_phase->id_user_survey = $user_survey->codigo_usuario_encuesta;


                return $data_for_evaluation_phase;
            }
            catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
            }
            abort(404);
        }
    }

    public static function getUserMail(array $filters = [])
	{
		$query = Usuario::query();
		$query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=','usuario.codigo_usuario');
		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->where('correo_electronico', '=', $filters['email']);
        $query->where('usuario_institucion.codigo_institucion', '=', (int) $filters['id_institution']);
        $query->first();
		return $query->first();
	}

    public static function getUsersEvaluators($user_identification, $id_institution)
    {
        $query = Usuario::query();
        $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->join('seguridad.rol_usuario', 'rol_usuario.user_id', '=', 'usuario.codigo_usuario');
        $query->where('rol_usuario.role_id', '=', Rol::getRoleId('evaluator'));
        $query->where('usuario_institucion.codigo_institucion', '=', $id_institution);
        $query->where('usuario.identificacion_usuario', '=', $user_identification);
        $query->whereNotNull('identificacion_usuario');
        return $query->first();
    }
}
//este es el correcto
