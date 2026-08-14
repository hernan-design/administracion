<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB, Log, Response, Auth, SendEmail;
use App\Models\Aprende\Mentoria,
    App\Models\Aprende\TareaMentoria,
    App\Models\Aprende\TagSesionMentoria,
    App\Models\Core\Estado,
    App\Models\Core\SesionMasiva,
    App\Models\Core\Evento,
    App\Models\Core\MentoriaMasiva,
    App\Models\Aprende\MentoriaInstitucion,
    App\Models\Aprende\ObjetivoMentoria,
    App\Models\Core\EventoInstitucion;

class MentoriaSesionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('subscription', ['only' => ['store', 'create']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
     * guarda descripcion de mentoria/intervencion
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function descriptionMentoring(Request $request) {
        $data = $request->all();

        //validaciones
        $messages = [
            'mentoring_description.string' => 'La descripción debe ser un texto.',
            'mentoring_description.max' => 'La descripción no debe ser mayor a 300 caracteres.',
            'mentoring_description.required' => 'El campo no puede estar vacío.',
        ];

        $rules = [
            'mentoring_description'     => 'required|string|max:300',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        $error = '';

        foreach ($errors->all() as $message) {
            $error .= $message . '<br>';
        }

        if ($validator->fails()) {
            return Response::json(['success' => false, 'errors' => $error ]);
        }


        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@descriptionMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                $mentoria->descripcion_mentoria = $data['mentoring_description'];
                $mentoria->save();

            }

            DB::commit();
            return Response::json([
                'success' => true,
                'message' => 'Descripcion agregada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@descriptionMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    public function getSesionMasiva (
            int $codigo_mentoria_masiva,
            int $codigo_sesion_masiva,
            $array_sesiones = null
        ) {
        $sesion_masiva = SesionMasiva::query();
        $sesion_masiva->join('aprende.mentoria', 'sesiones_masivas.codigo_sesion_masiva', '=', 'mentoria.codigo_sesion_masiva');
        $sesion_masiva->where('sesiones_masivas.codigo_mentoria_masiva', $codigo_mentoria_masiva);
        $sesion_masiva->where('sesiones_masivas.codigo_sesion_masiva', $codigo_sesion_masiva);
        $sesion_masiva->where('mentoria.codigo_estado', 8);

        if (isset($array_sesiones)) {
            $sesion_masiva->whereIn('mentoria.codigo_mentoria', $array_sesiones);
        }
        $sesion_masiva = $sesion_masiva->get();
        return $sesion_masiva;
    }

    /**
     * guarda Acuerdos/Plan de acción
     * @param  \Illuminate\Http\Request  $request
     */
    public function TareaMentoria(Request $request) {
        $data = $request->all();

        //validaciones
        $messages = [
            'mentoring-task-session.string' => 'El Acuerdos/Plan de acción debe ser un texto.',
            'mentoring-task-session.max' => 'La descripción no debe ser mayor a 250 caracteres.',
            'mentoring-task-session.required' => 'El campo no puede estar vacío.',
        ];

        $rules = [
            'mentoring-task-session'     => 'required|string|max:250',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        $error = '';

        foreach ($errors->all() as $message) {
            $error .= $message . '<br>';
        }

        if ($validator->fails()) {
            return Response::json(['success' => false, 'errors' => $error ]);
        }

        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@TareaMentoria', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                //add TareaMentoria
                if (!empty($data['mentoring-task-session'])) {
                    $taskMentoring = new TareaMentoria;
                    $taskMentoring->codigo_mentoria = $mentoria->codigo_mentoria;
                    $taskMentoring->descripcion_tarea_mentoria = $data['mentoring-task-session'];
                    $taskMentoring->codigo_estado = Estado::getState('active');
                    $taskMentoring->save();
                }

            }

            DB::commit();
            return Response::json([
                'success' => true,
                'message' => 'Acuerdos/Plan de acción agregado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@TareaMentoria', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    /**
     * guarda Informacon emprendimiento
     * @param  \Illuminate\Http\Request  $request
     */
    public function TagSesionMentoria(Request $request){
        $data = $request->all();

        //validaciones
        $messages = [
            'mentoring-tags-title.string' => 'El titulo debe ser un texto.',
            'mentoring-tags-title.max' => 'El titulo no debe ser mayor a 250 caracteres.',
            'mentoring-tags-title.required' => 'El titulo no puede estar vacío.',
            'mentoring-tags-value.string' => 'El valor debe ser un texto.',
            'mentoring-tags-value.max' => 'El valor no debe ser mayor a 250 caracteres.',
            'mentoring-tags-value.required' => 'El valor no puede estar vacío.',
        ];

        $rules = [
            'mentoring-tags-title'     => 'required|string|max:250',
            'mentoring-tags-value'     => 'required|string|max:250',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        $error = '';

        foreach ($errors->all() as $message) {
            $error .= $message . '<br>';
        }

        if ($validator->fails()) {
            return Response::json(['success' => false, 'errors' => $error ]);
        }

        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@TagSesionMentoria', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                //Tags Mentoring
                if (!empty($data['mentoring-tags-title']) && !empty($data['mentoring-tags-value'])) {
                    $tagsMentoring = new TagSesionMentoria;
                    $tagsMentoring->codigo_mentoria = $mentoria->codigo_mentoria;
                    $tagsMentoring->nombre_tag_sesion_mentoria = $data['mentoring-tags-title'];
                    $tagsMentoring->valor_tag_sesion_mentoria = $data['mentoring-tags-value'];
                    $tagsMentoring->codigo_estado = Estado::getState('active');
                    $tagsMentoring->save();
                }
            }

            DB::commit();
            return Response::json([
                'success' => true,
                'message' => 'Información del emprendimiento agregado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@TagSesionMentoria', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    public function generalObjetive(Request $request) {
        $data = $request->all();

        //validaciones
        $messages = [
            'mentoring-objetive-general.string' => 'La Observación debe ser un texto.',
            'mentoring-objetive-general.max' => 'La Observación no debe ser mayor a 500 caracteres.',
            'mentoring-objetive-general.required' => 'Debe escribir algo en la observación',
        ];

        $rules = [
            'mentoring-objetive-general'     => 'required|string|max:500',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        $error = '';

        foreach ($errors->all() as $message) {
            $error .= $message . '<br>';
        }

        if ($validator->fails()) {
            return Response::json(['success' => false, 'errors' => $error ]);
        }
        
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@generalObjetive', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                $mentoria->objetivo_mentor = $data['mentoring-objetive-general'];
                $mentoria->save();
            }

            DB::commit();
            return Response::json([
                    'success' => true,
                    'message' => 'Obsevaciones agregada correctamente']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@generalObjetive', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    /**
     * guarda la fase de la mentoria
     * función solicitada por Rutan para el campo fase del proceso
     * @param $request
     * @return Response
     */
    public function phaseMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@phaseMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }
                $mentoria->fase = $data['mentoring-phase'];

                //si la fase no es "diagnostico", seteamos campo en DB a ''
                if ($data['mentoring-phase'] !== 'diagnóstico') {
                    $mentoria->diagnostico = '';
                }
                $mentoria->save();
            }

            DB::commit();
            return Response::json([
                    'success' => true,
                    'message' => 'Fase agregada correctamente'
                ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@phaseMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    /**
     * guarda pregunta : ¿Se recomienda para la siguiente etapa?, campo de rutan
     * @param Request $request
     * @return Response
     */
    public function recommendsMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@recommendsMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }
                $mentoria->recomendacion = $data['mentoring-recommends'];
                $mentoria->save();
            }

            DB::commit();
            return Response::json([
                    'success' => true,
                    'message' => 'Recomendación agregada correctamente'
                ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@recommendsMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    /**
     * guarda pregunta : Seguimento, campo de rutan
     * @param Request $request
     * @return Response
     */
    public function tracingMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@tracingMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }
                $mentoria->seguimiento = $data['mentoring-tracing'];
                $mentoria->save();

            }

            DB::commit();
            return Response::json([
                    'success' => true,
                    'message' => 'Seguimiento agregado correctamente'
                ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@tracingMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' => 'No se pudo agregar Seguimiento'));
        }
    }

    /**
     * guarda pregunta : Diagóstico, campo de rutan
     * @param Request $request
     * @return Response
     */
    public function diagnosisMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@diagnosisMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }
                $mentoria->diagnostico = $data['mentoring-diagnosis'];
                $mentoria->save();
            }

            DB::commit();
            return Response::json([
                    'success' => true,
                    // 'codigo_mentoria' => TuProyecto::encryptSrcParams([$codigo_mentoria]),
                    'message' => 'Diagnóstico agregado correctamente'
                ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@diagnosisMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' => 'No se pudo agregar Diagnóstico'));
        }
    }

    /**
     * guarda duracion de la sesion
     * @param  \Illuminate\Http\Request  $request
     */
    public function durationMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        $messages = [
            'required'    => 'El campo :attribute es requerido.',
            'mentoring-duration-hour-value.min'    => 'El valor mínimo de la Hora es 0',
            'mentoring-duration-hour-value.max'    => 'El valor máximo de la Hora es 99',
            'mentoring-duration-minutes-value.min' => 'El valor mínimo de los Minutos es 0',
            'mentoring-duration-minutes-value.max' => 'El valor máximo de los Minutos es 59',
        ];

        $rules = [
            // 'mentoria' => 'required',
            'mentoring-duration-hour-value'     => 'required|integer|min:0|max:99',
            'mentoring-duration-minutes-value'  => 'required|integer|min:0|max:59',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        $error = '';

        foreach ($errors->all() as $message) {
            $error .= $message . '<br>';
        }

        if ($validator->fails()) {
            return Response::json([
                    'success' => false,
                    'errors' => $error
                ]);
        }

        $duration = implode(",", [$data['mentoring-duration-hour-value'], $data['mentoring-duration-minutes-value']]);

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@TareaMentoria', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }
                $mentoria->duration_mentoring = $duration;
                $mentoria->save();
            }

            DB::commit();
            return Response::json([
                'success' => true,
                'message' => 'Duración de la sesión agregada correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@durationMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    /**
     * finalización de sesion de mentoria masivo por el mentor
     */
    public function finishMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        $mentoriaMasiva = MentoriaMasiva::find($data['codigo_mentoria_masiva']);
        //obtengo nombre usuario y apellido usuario mediante las relaciones
        $nombre_mentor = $mentoriaMasiva->usuario->nombre_usuario . " " . $mentoriaMasiva->usuario->persona->primer_apellido_persona;

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {
                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@finishMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                $mentoria->codigo_estado = Estado::getState('pending_accept_session');
                $mentoria->save();

                $evento = Evento::select('nombre_evento', 'fecha_evento', 'hora_inicio_evento')
                                ->find($mentoria->codigo_evento);

                if (isset($mentoria->duration_mentoring)) {
                    $duracionMentoria = trim($mentoria->duration_mentoring);
                    $duracionMentoria = explode(',', $duracionMentoria);
                    $duracionMentoria = "$duracionMentoria[0]h y $duracionMentoria[1]m";
                } else {
                    $duracionMentoria = '--';
                }

                // Send notification
                $data = [];
                $data['_template'] = 'emails.mentoring.finishementoring';
                $data['_inst'] = session('institution')->codigo_institucion;
                $data['_subject'] = transtp('mentoring.subjectFinishedMentoring', ['nameMentor' => ucwords(strtolower($mentoriaMasiva->usuario->nombre_usuario))]);
                $data['_to'] = $mentoria->usuario->correo_electronico; //correo emprendedor
                $data['_to_name'] = ucwords(strtolower($mentoria->usuario->nombre_usuario));//nombre emprendedor
                $data['_url_action'] = sprintf("/%s", 'mentoria/mentores/procesos');
                $data['_requesting'] = ucwords(strtolower($mentoriaMasiva->usuario->nombre_usuario));
                $data['_title_mentoring'] = ucwords(strtolower($evento->nombre_evento));
                $data['_sessionID'] = $mentoria->codigo_mentoria;
                $data['_description_mentoring'] = $mentoria->descripcion_mentoria;
                $data['_duration_mentoring'] = $duracionMentoria;
                $data['_dateAndHour'] = "$evento->fecha_evento $evento->hora_inicio_evento";
                SendEmail::notification($data);
            }

            DB::commit();
            return Response::json([
                'success' => true,
                'message' => 'Se han finalizado las sesiones de mentoria'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@finishMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }

    /**
     * cancelarcion de sesion de mentoria masivo por el mentor
     */
    public function cancelMentoring(Request $request) {
        $data = $request->all();
        $array_sesiones = null;
        if ($request->has('array_sesiones')) {
            $array_sesiones = $request->array_sesiones;
        }
        $sesion_masiva = $this->getSesionMasiva($data['codigo_mentoria_masiva'], $data['codigo_sesion_masiva'], $array_sesiones);

        $mentoriaMasiva = MentoriaMasiva::find($data['codigo_mentoria_masiva']);
        //obtengo nombre usuario y apellido usuario mediante las relaciones
        $nombre_mentor = $mentoriaMasiva->usuario->nombre_usuario . " " . $mentoriaMasiva->usuario->persona->primer_apellido_persona;

        try {
            DB::beginTransaction();
            foreach ($sesion_masiva as $key => $sesion) {

                $mentoringInstitution = MentoriaInstitucion::where('codigo_mentoria', $sesion->codigo_mentoria)
                                                    ->where('codigo_institucion', session('institution')->codigo_institucion)->first();
                if (!$mentoringInstitution instanceof MentoriaInstitucion) {
                    $errormsg = "No se encuentra para esta insitucion mentoria de codigo " . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@cancelMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                $mentoria = Mentoria::find($sesion->codigo_mentoria);
                if (!$mentoria instanceof Mentoria) {
                    $errormsg = "No se encuentra la Mentoria" . $sesion->codigo_mentoria;
                    Log::error(sprintf('%s:%s', 'MentoriaSessionesController@cancelMentoring', $errormsg));
                    return Response::json(array('success' => false, 'errors' => $errormsg));
                }

                $mentoria->codigo_estado = Estado::getState('removed');
                $mentoria->save();

                MentoriaInstitucion::where('codigo_mentoria', $mentoria->codigo_mentoria)
                                    ->where('codigo_institucion', session('institution')->codigo_institucion)
                                    ->update(['codigo_estado' => Estado::getState('removed')]);

                ObjetivoMentoria::where('codigo_mentoria', $mentoria->codigo_mentoria )
                                    ->update(['codigo_estado' => Estado::getState('removed')]);

                Evento::where('codigo_evento', '=', $mentoria->codigo_evento)
                                    ->update(['codigo_estado' => Estado::getState('removed')]);

                // Update event institution
                EventoInstitucion::where('codigo_evento', $mentoria->codigo_evento)
                                    ->where('codigo_institucion', session('institution')->codigo_institucion)
                                    ->update(['codigo_estado' => Estado::getState('active')]);

            }

            DB::commit();
            return Response::json([
                'success' => true,
                'message' => 'Sesión(es) de mentoria eliminada(s) correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'MentoriaSessionesController@cancelMentoring', $e->getMessage()));
            return Response::json(array('success' => false, 'errors' =>  $e->getMessage()));
        }
    }
}
