<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;
use DB, Lang, App, Log, Auth, Queue, SendEmail;

use App\Models\Core\AsignacionJurado, App\Models\Core\Estado, App\Models\Core\RegistroFaseUsuario;
use App\Models\Seguridad\Usuario, Custom\Innpulsa;

class AsignacionController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evaluacion/asignacion');
        $this->middleware('ability:admin|super-admin|administrativo,create,evaluacion/asignacion', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evaluacion/asignacion', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,evaluacion/asignacion', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {

            if($request->has('register')) {

                // Decrypt src-params
                list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->register);
                if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
                    abort(403);
                }
                return response()->json(AsignacionJurado::getAssignmentJury($codigo_registro_fase_usuario));
            }
        }
        return view('program.detail');
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
        if($request->ajax()) {
            if ($request->has('email') && isset($request->email) && !empty($request->email)) {
              $user = Usuario::getUserMail(['email' => $request->email]);
              if(!$user instanceof Usuario) {
                  return response()->json(['success' => false, 'errors' => 'No existe evaluador asociado a esta dirección de correo electrónico.']);
              }
            } else if ($request->has('identificacion_evaluador') && isset($request->identificacion_evaluador) && !empty($request->identificacion_evaluador)) {
              $user = Usuario::getUsersEvaluators($request->identificacion_evaluador);
              if(!$user instanceof Usuario) {
                return response()->json(['success' => false, 'errors' => 'No existe evaluador asociado a ese numero de identificación.']);
              }
            }

            if(!$user->hasRole(['jurado'])) {
                return response()->json(['success' => false, 'errors' => 'El usuario no tiene asignado el rol de evaluador.']);
            }

            // Decrypt src-params
            list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->codigo_registro_fase_usuario);
            if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }

            // Valid fase
            $registroFase = RegistroFaseUsuario::join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')
                ->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase')->find($codigo_registro_fase_usuario);
            if(!$registroFase instanceof RegistroFaseUsuario) {
            	return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }

            $evaluators = AsignacionJurado::where('codigo_registro_fase_usuario', '=', $registroFase->codigo_registro_fase_usuario)->where('codigo_estado', '!=', Estado::getState('removed'))->count();
            if($evaluators >= $registroFase->numero_maximo_evaluadores) {
            	return response()->json(['success' => false, 'errors' => "No es posible asignar evaluador; ya se encuentran asignados el numero máximo ($registroFase->numero_maximo_evaluadores) de jurados permitidos para esta fase."]);
            }

            DB::beginTransaction();
            try {
                $assignmentJury = AsignacionJurado::where('codigo_usuario', '=', $user->codigo_usuario)
                    ->where('codigo_registro_fase_usuario', '=', $registroFase->codigo_registro_fase_usuario)
                    ->whereIn('codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')])
                    ->first();

                if( ($assignmentJury instanceof AsignacionJurado) && in_array($assignmentJury->codigo_estado, [Estado::getState('active'), Estado::getState('evaluated')]) ) {
                    DB::rollback();
                    return response()->json(['success' => false, 'errors' => 'Este jurado ya se encuentra asignado.']);
                }

                if(!$assignmentJury instanceof AsignacionJurado)
                {
                    $assignmentJury = new AsignacionJurado;
                    $assignmentJury->codigo_usuario =  $user->codigo_usuario;
                    $assignmentJury->codigo_registro_fase_usuario = $registroFase->codigo_registro_fase_usuario;
                    $assignmentJury->asignacion_jurado_virtual = $request->tipo_evaluacion === "0" ? false : true;
                }

                if($request->has('codigo_especialidad') && !empty($request->codigo_especialidad)) {
                    $assignmentJury->codigo_especialidad = $request->codigo_especialidad;
                }
                if($request->has('lugar_evaluacion_jurado') && !empty($request->lugar_evaluacion_jurado)) {
                    $assignmentJury->lugar_evaluacion_jurado = $request->lugar_evaluacion_jurado;
                }
                if($request->has('fecha_evaluacion_jurado') && !empty($request->fecha_evaluacion_jurado)) {
                    $assignmentJury->fecha_evaluacion_jurado = $request->fecha_evaluacion_jurado;
                }

                $assignmentJury->codigo_estado = Estado::getState('active');
                $assignmentJury->fecha_asignacion_jurado = date('Y-m-d');
                $assignmentJury->hora_asignacion_jurado = date('H:i:s');
                $assignmentJury->hora_desarrollo_asignacion = $request->hora_evaluacion;
                $assignmentJury->save();

                // Prepare response
                $response = AsignacionJurado::buildAssignment(AsignacionJurado::getDataJury($assignmentJury));
                $response->success = true;
                $response->message = 'Asignación realizada con éxito.';
                switch ($registroFase->codigo_convocatoria) {
                    case Innpulsa::$program_aldea20:
                        switch ($registroFase->codigo_fase) {
                            case Innpulsa::$stage_challenge4_aldea_2020:
                                    $nameEvaluators = AsignacionJurado::select('usuario.correo_electronico', 'usuario.nombre_usuario', 'lugar_evaluacion_jurado', 'codigo_registro_fase_usuario')
                                    ->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario')
                                    ->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $registroFase->codigo_registro_fase_usuario)
                                    ->where('asignacion_jurado.codigo_estado', '!=', Estado::getState('removed'))
                                    ->get();

                                    $finalCount = AsignacionJurado::where('codigo_registro_fase_usuario', '=', $registroFase->codigo_registro_fase_usuario)
                                    ->where('codigo_estado', '!=', Estado::getState('removed'))
                                    ->count();

                                    if($finalCount === $registroFase->numero_maximo_evaluadores) {
                                        $date = TuProyecto::dateFormat($assignmentJury->fecha_evaluacion_jurado, 'complete');
                                        $objectUrl = 'http://static.tuproyecto.com/documents/asignaciones/Guia+Evaluadores-ALDEA-Conversa+con+expertos.pdf';
                                        if ($request->tipo_evaluacion === "0") {
                                            $data = [];
                                            $data['_template'] = 'emails.program.innpulsa.moment__four__group_disscusion';
                                            $data['_subject'] = 'Conversatorio Reto 2';
                                            $data['_date'] = $date;
                                            $data['_hour'] = $request->hora_evaluacion;
                                            $data['_register'] = $registroFase->numero_registro_convocatoria;
                                            $data['_place'] = $assignmentJury->lugar_evaluacion_jurado;
                                            $data['_modality'] = 'Presencial';
                                            $data['_inst'] = session('institution')->codigo_institucion;
                                            $data['_meet'] = null;
                                            $data['_url_action'] = $objectUrl;

                                            foreach ($nameEvaluators as $evaluator) {
                                                $data['_to_name'] = ucwords(strtolower($evaluator->user_name));
                                                $data['_to'] = $evaluator->correo_electronico;

                                                SendEmail::notification($data);
                                            }
                                        } else {
                                            $hour = $request->hora_evaluacion;
                                            AsignacionJurado::buildConversatoryMeet($registroFase, $assignmentJury, $nameEvaluators, $hour, $date);
                                        }
                                    }
                            break;
                        }
                    break;
                }

                activity()
                  ->performedOn($assignmentJury)
                  ->withProperties([
                    'attributes' => [
                      'user' => $user->nombre_usuario,
                      'user_id' => $user->codigo_usuario,
                      'proyect' => $response->nombre_usuario_proyecto,
                    ]
                  ])
                  ->log('activitylog.assign.evaluator');

                  activity()
                  ->performedOn($assignmentJury)
                  ->causedBy($user)
                  ->withProperties([
                    'attributes' => [
                      'user' => $user->nombre_usuario,
                      'user_id' => $user->codigo_usuario,
                      'user_responsable' => Auth::user()->nombre_usuario,
                      'user_id_responsable' => Auth::user()->codigo_usuario,
                      'proyect' => $response->nombre_usuario_proyecto
                    ]
                  ])
                  ->log('activitylog.assign.received');


              // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        abort(404);
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
        if($request->ajax()) {
            $assignmentJury = AsignacionJurado::where('codigo_asignacion_jurado', '=', $request->codigo_asignacion_jurado)->where('codigo_usuario', '=', $request->codigo_usuario )->first();
            if(!$assignmentJury instanceof AsignacionJurado) {
                abort(404);
            }

            DB::beginTransaction();
            try {

                // Insert Assignment
                if($request->has('codigo_especialidad') && $request->get('codigo_especialidad') != ''){
                    $assignmentJury->codigo_especialidad = $request->codigo_especialidad;
                }

                if($request->has('lugar_evaluacion') && $request->get('lugar_evaluacion') != ''){
                    $assignmentJury->lugar_evaluacion_jurado = $request->lugar_evaluacion;
                }
                if($request->has('fecha_evaluacion') && $request->get('fecha_evaluacion') != '' ){
                    $assignmentJury->fecha_evaluacion_jurado = $request->fecha_evaluacion;
                }else{
                    $assignmentJury->fecha_evaluacion_jurado = null;
                }
                $assignmentJury->save();

                // Prepare response
                $response = AsignacionJurado::buildAssignment(AsignacionJurado::getDataJury($assignmentJury));
                $response->success = true;
                $response->message = 'Su asignación se ha actualizado con éxito.';

                // Commit Transaction
                DB::commit();
                return response()->json($response);

            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
            }
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->ajax()) {
            DB::beginTransaction();
            try {
                // Set removed state
                $assignmentJury = AsignacionJurado::find($request->codigo_asignacion_jurado);
                if(!$assignmentJury instanceof AsignacionJurado) {
	                DB::rollback();
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }

                $user = Usuario::find($assignmentJury->codigo_usuario);
                if (!$user instanceof Usuario) {
                  DB::rollback();
                  return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }

                $project = AsignacionJurado::getProjectJudge($assignmentJury->codigo_registro_fase_usuario);
                if (!$project instanceof Usuario) {
                  DB::rollback();
                  return response()->json(['success' => false, 'errors' => trans('app.exception')]);
                }

                // Valid remove
                if( $assignmentJury->asignacion_cerrada ) {
                	DB::rollback();
	                return response()->json(['success' => false, 'errors' => 'No es posible eliminar asignación, ya se encuentra cerrada esta fase para este emprendimiento.']);
                }

                if(in_array($assignmentJury->codigo_estado, [Estado::getState('evaluated')])){
					DB::rollback();
	                return response()->json(['success' => false, 'errors' => 'No es posible eliminar asignación porque ya fue realizada la evaluación.']);
                }

                $assignmentJury->codigo_estado = Estado::getState('removed');
                $assignmentJury->save();

                activity()
                  ->performedOn($assignmentJury)
                  ->withProperties([
                    'attributes' => [
                      'user' => $user->nombre_usuario,
                      'user_id' => $user->codigo_usuario,
                      'proyect' => $project->nombre_usuario
                    ]
                  ])
                  ->log('activitylog.assign.unsucribe_evaluator');

                  activity()
                  ->performedOn($assignmentJury)
                  ->causedBy($user)
                  ->withProperties([
                    'attributes' => [
                      'user' => $user->nombre_usuario,
                      'user_id' => $user->codigo_usuario,
                      'proyect' => $project->nombre_usuario,
                      'user_responsable' => Auth::user()->nombre_usuario,
                      'user_id_responsable' => Auth::user()->codigo_usuario,
                    ]
                  ])
                  ->log('activitylog.assign.unreceived');

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Se eliminó el integrante con éxito.']);

            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
            }
        }
        abort(404);
    }
}
