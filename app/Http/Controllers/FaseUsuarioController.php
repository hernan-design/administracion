<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Collection as Collection;

use DB, Lang, App, Log, SendEmail, Response, Auth, TuProyecto, AWS;

use Custom\Innpulsa, Custom\Dinamiza, App\Custom\Terpel, Custom\Propace;

use App\Models\Core\Convocatoria,
	App\Models\Core\TipoRecursoFase,
	App\Models\Core\RespuestaEncuesta,
	App\Models\Core\UsuarioInstitucion,
	App\Models\Core\Pago,
	App\Models\Core\PuntajeRegistroFase,
	App\Models\Core\CategoriaProducto,
	App\Models\Core\ComentDefDocument,
	App\Models\Core\RegistroFaseUsuario,
	App\Models\Core\EvaluacionItemEmprendimiento,
	App\Models\Core\Estado,
	App\Models\Seguridad\Usuario,
	App\Models\Core\Fase,
	App\Models\Core\AsignacionJurado,
	App\Models\Core\ResultadoEvaluation,
	App\Models\Core\UsuarioEncuesta,
	App\Models\Core\OrientacionConvocatoria,
	App\Models\Core\GrupoItemEval,
	App\Models\Core\DetalleVoucher,
	App\Models\Core\Voucher,
	App\Models\Core\AdjuntoRegistroFaseUsuario,
	App\Models\Param\Institucion,
	App\Models\Core\RegistroConvocatoria,
	App\Custom\Rutan;
use App\Models\Aprende\Curso;

class FaseUsuarioController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,convocatoria');
        $this->middleware('ability:admin|super-admin|administrativo,notification,convocatoria', ['only' => ['edit', 'update', 'notification']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$respuesta = null;
		if ($request->ajax()) {
			if($request->has('program')) {
				$program = Convocatoria::find($request->program);
				if(!$program instanceof Convocatoria) {
					abort(404);
				}

				$respuesta = null;

				switch ($program->codigo_orientacion_convocatoria) {
					case OrientacionConvocatoria::getOrientation('user'):
						switch ($program->codigo_convocatoria){
							case Convocatoria::getProgram('reg_mentores_provedores'):
								$respuesta = RegistroFaseUsuario::getProgramUserAndRol($program, $request->all());
							break;
							case Convocatoria::getProgram('propace__course'):
								$respuesta = RegistroFaseUsuario::getProgramPropaceCourses($program, $request->all());
							break;
							case Convocatoria::getProgram('propace_pfc'):
								$respuesta =  Propace::getProgramPFC($program, $request->all());
							break;
							case Convocatoria::getProgram('propace_pfc_abierto'):
								$respuesta =  Propace::getProgramPFC($program, $request->all());
							break;
							case Convocatoria::getProgram('propace_pfc_abierta_particulares'):
								$respuesta =  Propace::getProgramPFC($program, $request->all());
							break;
							case Convocatoria::getProgram('pfc_3_0'):
								$respuesta =  Propace::getProgramPFC($program, $request->all());
							break;
							case Convocatoria::getProgram('fase_modelo_negocios_programa_apps_co'):
								$respuesta =  Innpulsa::getParticipantsProgramAppsCo($program, $request->all());
							break;
							default :
								$respuesta = RegistroFaseUsuario::getProgramUser($program, $request->all());
							break;
						}
						break;
					case OrientacionConvocatoria::getOrientation('entity'):
						switch ($program->codigo_convocatoria){
							case Convocatoria::getProgram('reg_entidades_provedores'):
								$respuesta = RegistroFaseUsuario::getProgramEntityRol($program, $request->all());
							break;
							case Convocatoria::getProgram('invitation_entrepreneurs'):
								$respuesta =  Innpulsa::getParticipants($program, $request->all());
							break;
							case Convocatoria::getProgram('empodera'):
								$respuesta =  Innpulsa::getParticipantsEmpodera($program, $request->all());
							break;
							case Convocatoria::getProgram('empodera__2020'):
								$respuesta =  Innpulsa::getParticipantsEmpodera2020($program, $request->all());
							break;
							case Convocatoria::getProgram('aldea__20'):
								$respuesta =  Innpulsa::getParticipantsAldea20($program, $request->all());
							break;
							case Convocatoria::getProgram('terpel__20'):
								$respuesta = Terpel::getParticipantsTerpel20($program, $request->all());
							break;
							default :
								$respuesta = RegistroFaseUsuario::getProgramEntity($program, $request->all());
							break;
						}
						break;
				}

				return $respuesta;
			}
		}
		abort(404);
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
	 * @BrahianSánchez
	 * Funcion encargada de la notificacion multiple de usuarios que pasen a la Fase 2
	 */
	public function store(Request $request)
	{
		if($request->ajax()) {
			$request->all();
			$dataReq = $request->data;
			$array = $request->data;
			$array = $array['codigo_registro_fase_usuario'];
			$count = 0;
			try {
				DB::beginTransaction();
				foreach ($array as $resultArray) {
					// Decrypt src-params
					list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($resultArray[0]);
					if (!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
						DB::rollback();
						return response()->json(['success' => false, 'errors' => 'El código de registro fase no es un número ']);
					}

					$registerPhase = RegistroFaseUsuario::join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')->find($codigo_registro_fase_usuario);
					if(!$registerPhase instanceof RegistroFaseUsuario) {
						DB::rollback();
						return response()->json(['success' => false, 'errors' => "El código de registro fase ($codigo_registro_fase_usuario) no se encuentra en el sistema."]);
					}
					$phase = Fase::find($registerPhase->codigo_fase);
					if (!$phase instanceof Fase) {
						DB::rollback();
						return response()->json(['success' => false, 'errors' => 'No se encontró la fase']);
					}
					$program = Convocatoria::find($phase->codigo_convocatoria);
					if (!$program instanceof Convocatoria) {
						DB::rollback();
						return response()->json(['success' => false, 'errors' => 'No se encontró la convocatoria']);
					}

					switch ($program->codigo_orientacion_convocatoria) {
						case OrientacionConvocatoria::getOrientation('user'):
							$dataUser = Usuario::find($registerPhase->codigo_usuario);
							if (!$dataUser instanceof Usuario) {
								return response()->json(['success' => false, 'errors' => 'El usuario no se encuentra registrado.']);
							}
						break;

						case OrientacionConvocatoria::getOrientation('entity'):
							$dataUser = Usuario::getUserByEntity($registerPhase->codigo_usuario);
							if (!$dataUser instanceof Usuario) {
								return response()->json(['success' => false, 'errors' => 'La entidad no pertenece a un usuario registrado.']);
							}
						break;
					}

					// Validate apply
					$query = RegistroConvocatoria::query();
					if (!is_null($dataUser->identificacion_usuario)) {
						$query->join('seguridad.usuario', 'registro_convocatoria.codigo_usuario', '=', 'usuario.codigo_usuario');
						$query->where('identificacion_usuario', $dataUser->identificacion_usuario);
					} else {
						$query->where('codigo_usuario', $dataUser->codigo_usuario);
					}
					$query->where('codigo_convocatoria', $program->codigo_convocatoria);
					$query->orderBy('codigo_registro_convocatoria', 'desc');
          			$registerProgram = $query->first();

					// Set turn on-off
					$registerPhase->visualizacion_usuario = $request->turn;
          		switch ($program->codigo_institucion) {
            		case Institucion::institution('innpulsa'):
              			switch ($phase->codigo_fase) {
                			case Fase::getIdPhase('one_challenge'):
								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);
								if($scoringSurvey instanceof UsuarioEncuesta){
									if($scoringSurvey->puntaje_total_encuesta <= $phase->rango_inferior_evaluacion) {
										if($request->turn || $request->data['turn']){
											$data = [];
											$data['_template'] = 'emails.program.notification';
											$data['_subject'] = 'Conoce los resultados de tu aplicación Reto1 - ALDEA';
											// Data Template
											$data['comment'] = Lang::get('program.mail.body');
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_inst'] = session('institution')->codigo_institucion;
											$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
											SendEmail::notification($data);

											$registerPhase->visualizacion_usuario = true;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}else{
										// phase 1 complete
										// Previous Insert Register
										$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase', Fase::getIdPhase('challenge_1.2'))->where('codigo_registro_convocatoria', $registerPhase->codigo_registro_convocatoria)->first();

										if(!$register instanceof RegistroFaseUsuario ){
											$registerPhase->codigo_estado = Estado::getState('forward');

											// Insert Register Phase User
											$register = new RegistroFaseUsuario;
											$register->codigo_fase = Fase::getIdPhase('challenge_1.2');
											$register->codigo_usuario = $registerPhase->codigo_usuario;
											$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
											$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
											$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
											$register->codigo_estado = Estado::getState('active');
											$register->visualizacion_usuario = false;
											$register->save();

											$juries = collect([70994, 234396, 286451, 290465, 359269, 359630, 359631]);

											// Insert Assignment
											$assignment = new AsignacionJurado;
											if($dataUser->distrito_ciudad == "CUNDINAMARCA"){
												$assignment->codigo_usuario =  286451;
											}else{
												$assignment->codigo_usuario =  $juries->random();
											}
											$assignment->codigo_estado = Estado::getState('active');
											$assignment->fecha_asignacion_jurado = date('Y-m-d');
											$assignment->hora_asignacion_jurado = date('H:i:s');
											$assignment->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
											$assignment->save();

											$registerPhase->visualizacion_usuario = false;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}
								}

                			break;

							case Fase::getIdPhase('one_challenge_empodera'):

								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);
								if($scoringSurvey instanceof UsuarioEncuesta){
									if($scoringSurvey->puntaje_total_encuesta <= $phase->rango_inferior_evaluacion) {
										if($request->turn){
											$data = [];
											$data['_template'] = 'emails.program.notification';
											$data['_subject'] = 'Conoce los resultados de tu aplicación - iNNpulsa Empodera';
											// Data Template
											$data['comment'] = Lang::get('program.mail.body');
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_inst'] = session('institution')->codigo_institucion;
											$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
											// SendEmail::notification($data);

											$registerPhase->visualizacion_usuario = true;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}else{
										// phase 1 complete
										// Previous Insert Register
										$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase', Fase::getIdPhase('challenge_1.2'))->where('codigo_registro_convocatoria', $registerPhase->codigo_registro_convocatoria)->first();

										if(!$register instanceof RegistroFaseUsuario ){
											$registerPhase->codigo_estado = Estado::getState('forward');

											// Insert Register Phase User
											$register = new RegistroFaseUsuario;
											$register->codigo_fase = Fase::getIdPhase('challenge_empodera_1.2');
											$register->codigo_usuario = $registerPhase->codigo_usuario;
											$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
											$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
											$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
											$register->codigo_estado = Estado::getState('active');
											$register->visualizacion_usuario = false;
											$register->save();

											$juries = collect([72034, 352236, 179348, 190705]);

											// Insert Assignment
											$assignment = new AsignacionJurado;
											$assignment->codigo_usuario =  $juries->random();
											$assignment->codigo_estado = Estado::getState('active');
											$assignment->fecha_asignacion_jurado = date('Y-m-d');
											$assignment->hora_asignacion_jurado = date('H:i:s');
											$assignment->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
											$assignment->save();

											$registerPhase->visualizacion_usuario = false;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}
								}

							break;

							case Fase::getIdPhase('challenge_1.2'):

								// Scoring of the evaluation
								$scoringEvaluation = ResultadoEvaluation::join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado')
									->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
									->where('resultado_evaluacion_emprendimiento.codigo_estado', Estado::getState('active'))->first();
								if(!$scoringEvaluation instanceof ResultadoEvaluation) {
									DB::rollback();
									return response()->json(['success' => true, 'message' => 'El emprendimiento no ha sido evaluado en reto 1.2']);
								}

								// Previous Insert Register
								$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase',Fase::getIdPhase('two_challenge'))->where('codigo_registro_convocatoria',$registerPhase->codigo_registro_convocatoria)->first();

								if(!$register instanceof RegistroFaseUsuario ){
									if($scoringEvaluation->puntaje_total_eval_emprendi >= $phase->rango_inferior_evaluacion && $scoringEvaluation->puntaje_total_eval_emprendi <= $phase->rango_superior_evaluacion) {
										// phase 1.2 complete
										$registerPhase->codigo_estado = Estado::getState('forward');

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('two_challenge');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									}

									$data = [];
									$data['_template'] = 'emails.program.notification';
									$data['_subject'] = 'Conoce los resultados de tu aplicación Reto1 - ALDEA';
									// Data Template
									$data['comment'] = Lang::get('program.mail.body');
									$data['_to'] = $dataUser->correo_electronico;
									$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
									$data['_inst'] = session('institution')->codigo_institucion;
									$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
									// SendEmail::notification($data);

									$onePhase = RegistroFaseUsuario::where('codigo_fase', Fase::getIdPhase('one_challenge'))->where('codigo_usuario',$registerPhase->codigo_usuario)->first();
									$onePhase->visualizacion_usuario = true;
									$onePhase->save();

									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
									$registerPhase->rango_superior_eval_fase = $phase->rango_superior_evaluacion;
								}

							break;

							case Fase::getIdPhase('challenge_empodera_1.2'):

								// Scoring of the evaluation
								$scoringEvaluation = ResultadoEvaluation::join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado')
									->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
									->where('resultado_evaluacion_emprendimiento.codigo_estado', Estado::getState('active'))->first();
								if(!$scoringEvaluation instanceof ResultadoEvaluation) {
									DB::rollback();
									return response()->json(['success' => true, 'message' => 'El emprendimiento no ha sido evaluado en reto 1.2']);
								}

								// Previous Insert Register
								$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase',Fase::getIdPhase('two_challenge_empodera'))->where('codigo_registro_convocatoria',$registerPhase->codigo_registro_convocatoria)->first();

								if(!$register instanceof RegistroFaseUsuario ){
									if($scoringEvaluation->puntaje_total_eval_emprendi >= $phase->rango_inferior_evaluacion && $scoringEvaluation->puntaje_total_eval_emprendi <= $phase->rango_superior_evaluacion) {
										// phase 1.2 complete
										$registerPhase->codigo_estado = Estado::getState('forward');

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('two_challenge_empodera');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									}

									$data = [];
									$data['_template'] = 'emails.program.notification';
									$data['_subject'] = 'Conoce los resultados de tu aplicación - iNNpulsa Empodera';
									// Data Template
									$data['comment'] = Lang::get('program.mail.body');
									$data['_to'] = $dataUser->correo_electronico;
									$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
									$data['_inst'] = session('institution')->codigo_institucion;
									$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
									// SendEmail::notification($data);

									$onePhase = RegistroFaseUsuario::where('codigo_fase', Fase::getIdPhase('one_challenge_empodera'))->where('codigo_usuario',$registerPhase->codigo_usuario)->first();
									$onePhase->visualizacion_usuario = true;
									$onePhase->save();

									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
									$registerPhase->rango_superior_eval_fase = $phase->rango_superior_evaluacion;
								}

							break;

							case Fase::getIdPhase('two_challenge'):
								// Validar adjunto
								$attachedrfu = AdjuntoRegistroFaseUsuario::join('core.adjunto', 'adjunto_rfu.codigo_adjunto', '=', 'adjunto.codigo_adjunto')->where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->first();
								if(!$attachedrfu instanceof AdjuntoRegistroFaseUsuario) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => "Debe adjuntar carta de notificatión. {$registerPhase->numero_registro_convocatoria}"]);
								}

								// Prepare email notification
								$data = [];
								$data['promoted'] = false;

								// Valid promoted
								$registerPhase->codigo_estado = Estado::getState('rejected');
								if(doubleval($registerPhase->valor_asignacion_rfu) > 0)
								{
									// phase 2 complete
									$registerPhase->codigo_estado = Estado::getState('forward');

									// Insert Register Phase User
									$register = new RegistroFaseUsuario;
									$register->codigo_fase = Fase::getIdPhase('three_challenge');
									$register->codigo_usuario = $registerPhase->codigo_usuario;
									$register->fecha_registro_fase_usuario = date('Y-m-d');
									$register->hora_registro_fase_usuario = date('H:i:s');
									$register->codigo_estado = Estado::getState('no_linked');
									$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
									$register->visualizacion_usuario = false;
									$register->valor_asignacion_rfu = $registerPhase->valor_asignacion_rfu;
									$register->save();

									$usuario_entidad = Usuario::getUserByEntity($registerPhase->codigo_usuario);

									$detalleVoucher = new DetalleVoucher;
									$detalleVoucher->codigo_estado = Estado::getState('pending');
									$detalleVoucher->codigo_usuario = $usuario_entidad->owner_id;
									$detalleVoucher->codigo_voucher = Voucher::getVoucher('innpulsa');
									$detalleVoucher->valor_detalle_voucher = $registerPhase->valor_asignacion_rfu;
									$detalleVoucher->saldo_detalle_voucher = $registerPhase->valor_asignacion_rfu;
									$detalleVoucher->fecha_detalle_voucher = date('Y-m-d');
									$detalleVoucher->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
									$detalleVoucher->save();

									// Prepare email notification
									$data['promoted'] = true;
								}

								$registerPhase->visualizacion_usuario = true;
								$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
								$registerPhase->notificacion_registro_fase_usuario = true;
								$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

								// register program validate phase 3
								$registerProgram->bloqueo_nueva_aplicacion = true;
								$registerProgram->save();

								// Send email notification
								$data['_template'] = 'emails.program.innpulsa.reto3';
								$data['_subject'] = 'Conoce los resultados reto2 "Conversa con Expertos" - ALDEA';
								$data['_to'] = $dataUser->correo_electronico;
								$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
								$data['_inst'] = session('institution')->codigo_institucion;
								$data['_attach'] = [
									$attachedrfu->direccion_adjunto => $attachedrfu->nombre_adjunto
								];
								// Data Template
								$data['url_action'] = sprintf("/%s/%s/%s", 'proyecto', $dataUser->nickname_usuario, 'documentos');

								// Copy
								$data['to_copy'] = 'desarrollo@tuproyecto.com';
								$data['to_copy_name'] = 'Desarrollo - TuProyecto';
								SendEmail::notification($data);

							break;

							case Fase::getIdPhase('empodera___2020'):
								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);

								// Scoring of the evaluation
								$scoringEvaluation = ResultadoEvaluation::join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado')->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $registerPhase->codigo_registro_fase_usuario)->first();

								if (!$scoringSurvey instanceof UsuarioEncuesta && !$scoringEvaluation instanceof ResultadoEvaluation) {
									abort(404);
								}

								$totalScoring = $scoringEvaluation->puntaje_total_eval_emprendi;

								if ($totalScoring >= $phase->rango_inferior_evaluacion) {
									if($request->turn || $request->data['turn']){

										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// New phase
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('empodera__2020_intermediate__phase');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
										$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->codigo_estado = Estado::getState('active');
										$register->visualizacion_usuario = false;
										$register->save();

										$assignment = new AsignacionJurado;
										$assignment->codigo_usuario = 361963;
										$assignment->codigo_estado = Estado::getState('active');
										$assignment->fecha_asignacion_jurado = date('Y-m-d');
										$assignment->hora_asignacion_jurado = date('H:m:s');
										$assignment->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
										$assignment->lugar_evaluacion_jurado = 'Virtual';
										$assignment->asignacion_cerrada = false;
										$assignment->save();

										$data = [
											'title' => 'iNNpulsa Empodera 2020',
											'header' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/innpulsa/innpulsa-mincomercio-gobiernocolombia-logos.png',
											'date' => Tuproyecto::dateFormat(date('Y-m-d'), 'daymonthnameyear'),
											'name' => ucwords(strtolower($dataUser->user_name)),
											'entity' => ucwords(strtolower($dataUser->entity_description)),
											'mail' => $dataUser->correo_electronico,
											'address' => $dataUser->direccion,
											'city' => $dataUser->nombre_ciudad,
											'short_name' => $dataUser->short_name,
											'code_application' => $registerPhase->numero_radicado,
											'url_application' => App::environment('local') ? 'http://localhost/plataforma/public/proyecto/'.$dataUser->nickname_usuario.'/documentos' : session('institution')->url_institucion.'/proyecto'.$dataUser->nickname_usuario.'/documentos'
										];

										// Build PDF
										$pdf = App::make('dompdf.wrapper');
										$pdf->loadHTML(view('reports.program.innpulsa.empodera2020.notification', $data));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'iNNpulsaEmpodera__2020', $registerPhase->codigo_registro_convocatoria);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
										'Bucket' =>  $bucket,
										'Key'    =>  $key,
										'Body'   =>  $pdf->output(),
										'ACL'    => 'public-read'
										]);

										// Sending Email
										$data = [];
										$data['_template'] = 'emails.program.notification_empodera';
										$data['_subject'] = 'Conoce los resultados de tu aplicación Empodera 2020';
										// Data Template
										$data['comment'] = Lang::get('program.mail.body.empodera2020reg');
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
										$data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
										SendEmail::notification($data);
									}
								} else {
									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->codigo_estado = Estado::getState('rejected');
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

									$data = [
										'title' => 'iNNpulsa Empodera 2020',
										'date' => Tuproyecto::dateFormat(date('Y-m-d'), 'daymonthnameyear'),
										'header' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/innpulsa/innpulsa-mincomercio-gobiernocolombia-logos.png',
										'name' => ucwords(strtolower($dataUser->user_name)),
										'entity' => ucwords(strtolower($dataUser->entity_description)),
										'mail' => $dataUser->correo_electronico,
										'address' => $dataUser->direccion,
										'city' => $dataUser->nombre_ciudad,
										'short_name' => $dataUser->short_name,
										'code_application' => $registerPhase->numero_radicado,
										'questions' => Innpulsa::getItemEvaluation($registerPhase->codigo_registro_fase_usuario)
									];

									// Build PDF
									$pdf = App::make('dompdf.wrapper');
									$pdf->loadHTML(view('reports.program.innpulsa.empodera2020.notificacion_refuse', $data));

									$s3 = AWS::createClient('s3');

									$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

									$filename = sprintf('%s_%s.pdf', 'iNNpulsaEmpodera__2020', $registerPhase->codigo_registro_convocatoria);
									$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

									$result = $s3->putObject([
									'Bucket' =>  $bucket,
									'Key'    =>  $key,
									'Body'   =>  $pdf->output(),
									'ACL'    => 'public-read'
									]);

									// Sending Email
									$data = [];
									$data['_template'] = 'emails.program.notification_empodera';
									$data['_subject'] = 'Conoce los resultados de tu aplicación Empodera 2020';
									// Data Template
									$data['comment'] = Lang::get('program.mail.body.empodera2020reg');
									$data['_to'] = $dataUser->correo_electronico;
									$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
									$data['_inst'] = session('institution')->codigo_institucion;
									$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
									$data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
									SendEmail::notification($data);
								}
							break;

							case Fase::getIdPhase('empodera__2020_intermediate__phase'):
								$evalItemEmpren = EvaluacionItemEmprendimiento::where('codigo_item_evaluacion', '=', 171)->first();
								if(!$evalItemEmpren instanceof EvaluacionItemEmprendimiento) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro el adjunto']);
								}

								// New phase
								$register = new RegistroFaseUsuario;
								$register->codigo_fase = Fase::getIdPhase('empodera___2020_second__phase');
								$register->codigo_usuario = $registerPhase->codigo_usuario;
								$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
								$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
								$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
								$register->codigo_estado = Estado::getState('active');
								$register->visualizacion_usuario = false;
								$register->save();

							break;

							case Fase::getIdPhase('aldea__20_first__challenge'):
								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);

								if(!$scoringSurvey instanceof UsuarioEncuesta){
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro el usuario encuesta']);
								}

								$second__moment = Fase::find( Fase::getIdPhase('aldea__20_challenge__1_2') );
								if(!$second__moment instanceof Fase) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro el momento 1']);
								}

								if($request->turn || $request->data['turn']){
									if($scoringSurvey->puntaje_total_encuesta >= $phase->rango_inferior_evaluacion) {
										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = $second__moment->codigo_fase;
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									} else {
										$registerPhase->codigo_estado = Estado::getState('returned');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										$data = [];
										$data['_template'] = 'emails.program.innpulsa.moment__one__refused';
										$data['_subject'] = 'Notificacion Momento 1';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										SendEmail::notification($data);
									}
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							case Fase::getIdPhase('aldea__20_challenge__1_3'):
								if($request->turn || $request->data['turn']){
									$scoreRegisterPhase = PuntajeRegistroFase::where('codigo_registro_fase_usuario', '=', $registerPhase->codigo_registro_fase_usuario)->first();
									if(!$scoreRegisterPhase instanceof PuntajeRegistroFase) {
										DB::rollback();
										return response()->json(['success' => false, 'errors' => 'Aun no se ha evaluado en este momento']);
									}

									if($scoreRegisterPhase->valor_puntaje_registro_fase >= $phase->rango_inferior_evaluacion) {

										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('aldea__20_second__challenge');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;

										$balance = Innpulsa::get__Balance__Aldea20($registerPhase->codigo_usuario_encuesta);
										if($balance->saldo_voucher >= 60000000) {
											$register->codigo_estado = Estado::getState('active');

											/*$data = [];
											$data['_template'] = 'emails.program.innpulsa.moment__three';
											$data['_subject'] = 'Notificacion Momento 3';
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_entity_name'] = $dataUser->entity_name;
											$data['_inst'] = session('institution')->codigo_institucion;
											SendEmail::notification($data);*/
										} else {
											$register->codigo_estado = Estado::getState('pendinginstitution');

											/*$data = [];
											$data['_template'] = 'emails.program.innpulsa.moment__three_refused';
											$data['_subject'] = 'Notificacion Momento 3';
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_entity_name'] = $dataUser->entity_name;
											$data['_inst'] = session('institution')->codigo_institucion;
											SendEmail::notification($data);*/
											$messageFase = 'No se cuenta con los recursos suficientes para ' . $dataUser->entity_name;

										}

										$register->save();

									} else {
										$registerPhase->codigo_estado = Estado::getState('rejected');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										$data = [];
										$data['_template'] = 'emails.program.innpulsa.moment__three_minimum';
										$data['_subject'] = 'Notificacion Momento 3';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										SendEmail::notification($data);
									}
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							case Fase::getIdPhase('aldea__20_second__challenge'):
								if($request->turn || $request->data['turn']){
									$score = PuntajeRegistroFase::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->first();
									if(!$score instanceof PuntajeRegistroFase) {
										DB::rollback();
										return response()->json(['success' => false, 'errors' => 'Aun no se ha evaluado en este momento']);
									}

									$dataDocument = Innpulsa::getDataKeysAccessAprove($registerPhase->codigo_registro_fase_usuario, $registerPhase->codigo_registro_convocatoria);
									$pdf = App::make('dompdf.wrapper');

									$recomendations = AsignacionJurado::select('resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion')
										->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=','asignacion_jurado.codigo_asignacion_jurado')
										->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->get();
									$comments = ComentDefDocument::select('texto_coment_eval_emprendi')
										->join('core.coment_eval_emprendi', 'coment_eval_emprendi.codigo_coment_eval_emprendi', '=', 'coment_def_document.codigo_coment_eval_emprendi')
										->where('codigo_registro_fase_usuario', $codigo_registro_fase_usuario)
										->get();

									if($registerPhase->valor_asignacion_rfu > 0) {
										$categories = CategoriaProducto::getCategories($registerPhase->codigo_registro_fase_usuario);

										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('aldea__20_three__challenge');
										$register->codigo_estado = Estado::getState('no_linked');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->valor_asignacion_rfu = $registerPhase->valor_asignacion_rfu;
										$register->save();

										$usuario_entidad = Usuario::getUserByEntity($registerPhase->codigo_usuario);

										$detalleVoucher = new DetalleVoucher;
										$detalleVoucher->codigo_estado = Estado::getState('pending');
										$detalleVoucher->codigo_usuario = $usuario_entidad->owner_id;
										$detalleVoucher->codigo_voucher = Voucher::getVoucher('aldea');
										$detalleVoucher->valor_detalle_voucher = $registerPhase->valor_asignacion_rfu;
										$detalleVoucher->saldo_detalle_voucher = $registerPhase->valor_asignacion_rfu;
										$detalleVoucher->fecha_detalle_voucher = date('Y-m-d');
										$detalleVoucher->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
										$detalleVoucher->save();

										// Insert Score
										$registerScore = new PuntajeRegistroFase();
										$registerScore->codigo_estado = Estado::getState('active');
										$registerScore->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
										$registerScore->valor_puntaje_registro_fase = $score->valor_puntaje_registro_fase;
										$registerScore->save();

										$dataPDF = [];
										$dataPDF['recomendations'] = $recomendations;
										$dataPDF['categories'] = $categories;
										$dataPDF['data'] = $dataDocument;
										$dataPDF['score__challenge_Two'] = $score->valor_puntaje_registro_fase;
										$dataPDF['_comments'] = $comments;
										$dataPDF['keys'] = money_format("%i", $registerPhase->valor_asignacion_rfu);

										$pdf->loadHTML(view('reports.program.innpulsa.letter_keys', $dataPDF));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'carta_llaves', $registerPhase->codigo_registro_fase_usuario);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
											'Bucket' =>  $bucket,
											'Key'    =>  $key,
											'Body'   =>  $pdf->output(),
											'ACL'    => 'public-read'
										]);

										$data = [];
										$data['_template'] = 'emails.program.innpulsa.challenge__two';
										$data['_subject'] = 'Notificación Reto 2 ¡Conversa con Expertos! de ALDEA';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_recomendations'] = $recomendations;
										$data['_categories'] = $categories;
										$data['_score'] = $score->valor_puntaje_registro_fase;
										$data['_total_keys'] = $registerPhase->valor_asignacion_rfu;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_attach'] = [$result['ObjectURL'] => 'Carta de Llaves de Acceso.pdf'];

										$data['url_action'] = sprintf("/%s/%s/%s", 'proyecto', $dataUser->nickname_usuario, 'documentos');
										SendEmail::notification($data);
									} else {
										$registerPhase->codigo_estado = Estado::getState('rejected');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										$dataPDF = [];
										$dataPDF['recomendations'] = $recomendations;
										$dataPDF['data'] = $dataDocument;
										$dataPDF['score__challenge_Two'] = $score->valor_puntaje_registro_fase;
										$dataPDF['_comments'] = $comments;
										$dataPDF['keys'] = money_format("%i", $registerPhase->valor_asignacion_rfu);

										$pdf->loadHTML(view('reports.program.innpulsa.letter_keys_refuse', $dataPDF));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'carta_llaves', $registerPhase->codigo_registro_fase_usuario);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
										'Bucket' =>  $bucket,
										'Key'    =>  $key,
										'Body'   =>  $pdf->output(),
										'ACL'    => 'public-read'
										]);

										$data = [];
										$data['_template'] = 'emails.program.innpulsa.challenge__two_refused';
										$data['_subject'] = 'Notificación Reto 2 ¡Conversa con Expertos! de ALDEA';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_recomendations'] = $recomendations;
										$data['_score'] = $score->valor_puntaje_registro_fase;
										$data['_total_keys'] = $registerPhase->valor_asignacion_rfu;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_attach'] = [$result['ObjectURL'] => 'Carta de Llaves de Acceso.pdf'];
										SendEmail::notification($data);
									}
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							default:
								if(isset($phase->cor_codigo_fase)) {
									if (!Fase::find($phase->cor_codigo_fase) instanceof Fase) {
										DB::rollback();
										return response()->json(['success' => false, 'errors' => 'No se encontro la fase de Registro']);
									}

									if($request->turn || $request->data['turn']){
										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = $phase->cor_codigo_fase;
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									} else {
										$registerPhase->visualizacion_usuario = false;
										$registerPhase->notificacion_registro_fase_usuario = false;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									}
								} else {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No existe la siguiente fase']);
								}
							break;
						}

					$registerPhase->save();
					break;
          	}
		}
			//Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Cambiando visualización', 'visualizacion_usuario' => $request->turn, 'codigo_estado' => $registerPhase->codigo_estado, 'count' => $count]);
			} catch (\Exception $e) {
				DB::rollback();
				Log::error(sprintf('%s:%s', 'Email reporte notification', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => 'Ocurrio un error al momento de enviar el mail', 'count' => $count]);
			}
		}
	}

	public function EnvioDeCorreos($dataUser) {
		$data = [];
		$data['_template'] = 'emails.program.notification';
		$data['_subject'] = 'Conoce los resultados de tu aplicación Reto1 - ALDEA';
		// Data Template
		$data['comment'] = Lang::get('program.mail.body');
		$data['_to'] = $dataUser->correo_electronico;
		$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
		$data['_inst'] = session('institution')->codigo_institucion;
		$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
		SendEmail::notification($data);
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
		if ($request->ajax()) {
			DB::beginTransaction();
			try {
				// Decrypt src-params
				list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($id);
				if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
					DB::rollback();
					return response()->json(['success' => false, 'errors' => 'El código de registro fase no es un número ']);
				}

				$registerPhase = RegistroFaseUsuario::join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')->find($codigo_registro_fase_usuario);
				if(!$registerPhase instanceof RegistroFaseUsuario) {
					DB::rollback();
					return response()->json(['success' => false, 'errors' => "El código de registro fase ($codigo_registro_fase_usuario) no se encuentra en el sistema."]);
				}

				$phase = Fase::find($registerPhase->codigo_fase);
				if(!$phase instanceof Fase) {
					DB::rollback();
					return response()->json(['success' => false, 'errors' => 'No se encontró la fase']);
				}

				$program = Convocatoria::find($phase->codigo_convocatoria);
				if(!$program instanceof Convocatoria) {
					DB::rollback();
					return response()->json(['success' => false, 'errors' => 'No se encontró la convocatoria']);
				}

				switch ($program->codigo_orientacion_convocatoria) {
					case OrientacionConvocatoria::getOrientation('user'):
						$dataUser = Usuario::find($registerPhase->codigo_usuario);
						if(!$dataUser instanceof Usuario) {
							return response()->json(['success' => false, 'errors' => 'El usuario no se encuentra registrado.']);
						}
					break;

					case OrientacionConvocatoria::getOrientation('entity'):
						$dataUser = Usuario::getUserByEntity($registerPhase->codigo_usuario);
						if(!$dataUser instanceof Usuario) {
							return response()->json(['success' => false, 'errors' => 'La entidad no pertenece a un usuario registrado.']);
						}
					break;
				}

				// Validate apply
				$query = RegistroConvocatoria::query();
				if(!is_null($dataUser->identificacion_usuario)){
					$query->join('seguridad.usuario', 'registro_convocatoria.codigo_usuario', '=', 'usuario.codigo_usuario');
					$query->where('identificacion_usuario', $dataUser->identificacion_usuario);
				}else{
					$query->where('codigo_usuario', $dataUser->codigo_usuario);
				}
				$query->where('codigo_convocatoria', $program->codigo_convocatoria);
				$query->orderBy('codigo_registro_convocatoria', 'desc');
				$registerProgram = $query->first();

				// Set turn on-off
				$registerPhase->visualizacion_usuario = $request->turn;

				switch ($program->codigo_institucion) {
					case Institucion::institution('innpulsa'):
						switch ($phase->codigo_fase) {
							case Fase::getIdPhase('one_challenge'):
								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);
								if($scoringSurvey instanceof UsuarioEncuesta){
									if($scoringSurvey->puntaje_total_encuesta <= $phase->rango_inferior_evaluacion) {
										if($request->turn){
											$data = [];
											$data['_template'] = 'emails.program.notification';
											$data['_subject'] = 'Conoce los resultados de tu aplicación Reto1 - ALDEA';
											// Data Template
											$data['comment'] = Lang::get('program.mail.body');
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_inst'] = session('institution')->codigo_institucion;
											$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
											SendEmail::notification($data);

											$registerPhase->visualizacion_usuario = true;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}else{
										// phase 1 complete
										// Previous Insert Register
										$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase', Fase::getIdPhase('challenge_1.2'))->where('codigo_registro_convocatoria', $registerPhase->codigo_registro_convocatoria)->first();

										if(!$register instanceof RegistroFaseUsuario ){
											$registerPhase->codigo_estado = Estado::getState('forward');

											// Insert Register Phase User
											$register = new RegistroFaseUsuario;
											$register->codigo_fase = Fase::getIdPhase('challenge_1.2');
											$register->codigo_usuario = $registerPhase->codigo_usuario;
											$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
											$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
											$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
											$register->codigo_estado = Estado::getState('active');
											$register->visualizacion_usuario = false;
											$register->save();

											$juries = collect([70994, 234396, 286451, 290465, 359269, 359630, 359631]);

											// Insert Assignment
											$assignment = new AsignacionJurado;
											if($dataUser->distrito_ciudad == "CUNDINAMARCA"){
												$assignment->codigo_usuario =  286451;
											}else{
												$assignment->codigo_usuario =  $juries->random();
											}
											$assignment->codigo_estado = Estado::getState('active');
											$assignment->fecha_asignacion_jurado = date('Y-m-d');
											$assignment->hora_asignacion_jurado = date('H:i:s');
											$assignment->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
											$assignment->save();

											$registerPhase->visualizacion_usuario = false;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}
								}

							break;

							case Fase::getIdPhase('one_challenge_empodera'):

								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);
								if($scoringSurvey instanceof UsuarioEncuesta){
									if($scoringSurvey->puntaje_total_encuesta <= $phase->rango_inferior_evaluacion) {
										if($request->turn){
											$data = [];
											$data['_template'] = 'emails.program.notification';
											$data['_subject'] = 'Conoce los resultados de tu aplicación - iNNpulsa Empodera';
											// Data Template
											$data['comment'] = Lang::get('program.mail.body');
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_inst'] = session('institution')->codigo_institucion;
											$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
											// SendEmail::notification($data);

											$registerPhase->visualizacion_usuario = true;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}else{
										// phase 1 complete
										// Previous Insert Register
										$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase', Fase::getIdPhase('challenge_1.2'))->where('codigo_registro_convocatoria', $registerPhase->codigo_registro_convocatoria)->first();

										if(!$register instanceof RegistroFaseUsuario ){
											$registerPhase->codigo_estado = Estado::getState('forward');

											// Insert Register Phase User
											$register = new RegistroFaseUsuario;
											$register->codigo_fase = Fase::getIdPhase('challenge_empodera_1.2');
											$register->codigo_usuario = $registerPhase->codigo_usuario;
											$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
											$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
											$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
											$register->codigo_estado = Estado::getState('active');
											$register->visualizacion_usuario = false;
											$register->save();

											$juries = collect([72034, 352236, 179348, 190705]);

											// Insert Assignment
											$assignment = new AsignacionJurado;
											$assignment->codigo_usuario =  $juries->random();
											$assignment->codigo_estado = Estado::getState('active');
											$assignment->fecha_asignacion_jurado = date('Y-m-d');
											$assignment->hora_asignacion_jurado = date('H:i:s');
											$assignment->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
											$assignment->save();

											$registerPhase->visualizacion_usuario = false;
											$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
											$registerPhase->notificacion_registro_fase_usuario = true;
											$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
											$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
										}
									}
								}

							break;

							case Fase::getIdPhase('challenge_1.2'):

								// Scoring of the evaluation
								$scoringEvaluation = ResultadoEvaluation::join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado')
									->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
									->where('resultado_evaluacion_emprendimiento.codigo_estado', Estado::getState('active'))->first();
								if(!$scoringEvaluation instanceof ResultadoEvaluation) {
									DB::rollback();
									return response()->json(['success' => true, 'message' => 'El emprendimiento no ha sido evaluado en reto 1.2']);
								}

								// Previous Insert Register
								$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase',Fase::getIdPhase('two_challenge'))->where('codigo_registro_convocatoria',$registerPhase->codigo_registro_convocatoria)->first();

								if(!$register instanceof RegistroFaseUsuario ){
									if($scoringEvaluation->puntaje_total_eval_emprendi >= $phase->rango_inferior_evaluacion && $scoringEvaluation->puntaje_total_eval_emprendi <= $phase->rango_superior_evaluacion) {
										// phase 1.2 complete
										$registerPhase->codigo_estado = Estado::getState('forward');

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('two_challenge');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									}

									$data = [];
									$data['_template'] = 'emails.program.notification';
									$data['_subject'] = 'Conoce los resultados de tu aplicación Reto1 - ALDEA';
									// Data Template
									$data['comment'] = Lang::get('program.mail.body');
									$data['_to'] = $dataUser->correo_electronico;
									$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
									$data['_inst'] = session('institution')->codigo_institucion;
									$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
									SendEmail::notification($data);

									$onePhase = RegistroFaseUsuario::where('codigo_fase', Fase::getIdPhase('one_challenge'))->where('codigo_usuario',$registerPhase->codigo_usuario)->first();
									$onePhase->visualizacion_usuario = true;
									$onePhase->save();

									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
									$registerPhase->rango_superior_eval_fase = $phase->rango_superior_evaluacion;
								}

							break;

							case Fase::getIdPhase('challenge_empodera_1.2'):

								// Scoring of the evaluation
								$scoringEvaluation = ResultadoEvaluation::join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado')
									->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)
									->where('resultado_evaluacion_emprendimiento.codigo_estado', Estado::getState('active'))->first();
								if(!$scoringEvaluation instanceof ResultadoEvaluation) {
									DB::rollback();
									return response()->json(['success' => true, 'message' => 'El emprendimiento no ha sido evaluado en reto 1.2']);
								}

								// Previous Insert Register
								$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase',Fase::getIdPhase('two_challenge_empodera'))->where('codigo_registro_convocatoria',$registerPhase->codigo_registro_convocatoria)->first();

								if(!$register instanceof RegistroFaseUsuario ){
									if($scoringEvaluation->puntaje_total_eval_emprendi >= $phase->rango_inferior_evaluacion && $scoringEvaluation->puntaje_total_eval_emprendi <= $phase->rango_superior_evaluacion) {
										// phase 1.2 complete
										$registerPhase->codigo_estado = Estado::getState('forward');

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('two_challenge_empodera');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									}

									$data = [];
									$data['_template'] = 'emails.program.notification';
									$data['_subject'] = 'Conoce los resultados de tu aplicación - iNNpulsa Empodera';
									// Data Template
									$data['comment'] = Lang::get('program.mail.body');
									$data['_to'] = $dataUser->correo_electronico;
									$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
									$data['_inst'] = session('institution')->codigo_institucion;
									$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
									// SendEmail::notification($data);

									$onePhase = RegistroFaseUsuario::where('codigo_fase', Fase::getIdPhase('one_challenge_empodera'))->where('codigo_usuario',$registerPhase->codigo_usuario)->first();
									$onePhase->visualizacion_usuario = true;
									$onePhase->save();

									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									$registerPhase->rango_inferior_eval_fase = $phase->rango_inferior_evaluacion;
									$registerPhase->rango_superior_eval_fase = $phase->rango_superior_evaluacion;
								}

							break;

							case Fase::getIdPhase('two_challenge'):
								// Validar adjunto
								$attachedrfu = AdjuntoRegistroFaseUsuario::join('core.adjunto', 'adjunto_rfu.codigo_adjunto', '=', 'adjunto.codigo_adjunto')->where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->first();
								if(!$attachedrfu instanceof AdjuntoRegistroFaseUsuario) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => "Debe adjuntar carta de notificatión. {$registerPhase->codigo_registro_fase_usuario}"]);
								}

								// Prepare email notification
								$data = [];
								$data['promoted'] = false;

								// Valid promoted
								$registerPhase->codigo_estado = Estado::getState('rejected');
								if(doubleval($registerPhase->valor_asignacion_rfu) > 0)
								{
									// phase 2 complete
									$registerPhase->codigo_estado = Estado::getState('forward');
									$dataUser = Usuario::getUserByEntity($registerPhase->codigo_usuario);
									if(!$dataUser instanceof Usuario) {
										return response()->json(['success' => false, 'errors' => 'La entidad no pertenece a un usuario registrado.']);
									}
									// Insert Register Phase User
									$register = new RegistroFaseUsuario;
									$register->codigo_fase = Fase::getIdPhase('three_challenge');
									$register->codigo_usuario = $registerPhase->codigo_usuario;
									$register->fecha_registro_fase_usuario = date('Y-m-d');
									$register->hora_registro_fase_usuario = date('H:i:s');
									$register->codigo_estado = Estado::getState('no_linked');
									$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
									$register->visualizacion_usuario = false;
									$register->valor_asignacion_rfu = $registerPhase->valor_asignacion_rfu;
									$register->save();

									$usuario_entidad = Usuario::getUserByEntity($registerPhase->codigo_usuario);

									$detalleVoucher = new DetalleVoucher;
									$detalleVoucher->codigo_estado = Estado::getState('pending');
									$detalleVoucher->codigo_usuario = $usuario_entidad->owner_id;
									$detalleVoucher->codigo_voucher = Voucher::getVoucher('innpulsa');
									$detalleVoucher->valor_detalle_voucher = $registerPhase->valor_asignacion_rfu;
									$detalleVoucher->saldo_detalle_voucher = $registerPhase->valor_asignacion_rfu;
									$detalleVoucher->fecha_detalle_voucher = date('Y-m-d');
									$detalleVoucher->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
									$detalleVoucher->save();

									// Prepare email notification
									$data['promoted'] = true;
								}

								$registerPhase->visualizacion_usuario = true;
								$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
								$registerPhase->notificacion_registro_fase_usuario = true;
								$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

								// register program validate phase 3
								$registerProgram->bloqueo_nueva_aplicacion = true;
								$registerProgram->save();

								// Send email notification
								$data['_template'] = 'emails.program.innpulsa.reto3';
								$data['_subject'] = 'Conoce los resultados reto2 "Conversa con Expertos" - ALDEA';
								$data['_to'] = $dataUser->correo_electronico;
								$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
								$data['_inst'] = session('institution')->codigo_institucion;
								$data['_attach'] = [
									$attachedrfu->direccion_adjunto => $attachedrfu->nombre_adjunto
								];
								// Data Template
								$data['url_action'] = sprintf("/%s/%s/%s", 'proyecto', $dataUser->nickname_usuario, 'documentos');

								// Copy
								$data['to_copy'] = 'desarrollo@tuproyecto.com';
								$data['to_copy_name'] = 'Desarrollo - TuProyecto';
								SendEmail::notification($data);

							break;

							case Fase::getIdPhase('three_challenge'):

								$phase_4 = Fase::find( Fase::getIdPhase('four_challenge') );
								if(!$phase_4 instanceof Fase) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro la fase 4']);
								}

								// Insert Register Phase User
								$register = new RegistroFaseUsuario;
								$register->codigo_fase = $phase_4->codigo_fase;
								$register->codigo_usuario = $registerPhase->codigo_usuario;
								$register->fecha_registro_fase_usuario = date('Y-m-d');
								$register->hora_registro_fase_usuario = date('H:i:s');
								$register->codigo_estado = Estado::getState('active');
								$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
								$register->visualizacion_usuario = false;
								$register->save();

								$registerPhase->codigo_estado = Estado::getState('finalized');
								$registerPhase->visualizacion_usuario = true;
								$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
								$registerPhase->notificacion_registro_fase_usuario = true;
								$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

								$data = [];
								$data['_template'] = 'emails.program.innpulsa.reto4';
								$data['_subject'] = '¡Levantemos Capital! - ALDEA';
								$data['_to'] = $dataUser->correo_electronico;
								$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
								$data['_inst'] = session('institution')->codigo_institucion;
								$data['url_action'] = sprintf('/encuesta/aplicar/aldea-reto4/%s?url_fase=%s', TuProyecto::encryptSrcParams([$registerPhase->codigo_usuario]), $phase_4->url_fase);
								SendEmail::notification($data);
							break;

							case Fase::getIdPhase('empodera___2020'):
								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);

								// Scoring of the evaluation
								$scoringEvaluation = ResultadoEvaluation::join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado')->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $registerPhase->codigo_registro_fase_usuario)->first();

								if (!$scoringSurvey instanceof UsuarioEncuesta && !$scoringEvaluation instanceof ResultadoEvaluation) {
									abort(404);
								}

								$totalScoring = $scoringEvaluation->puntaje_total_eval_emprendi;
								if ($totalScoring >= $phase->rango_inferior_evaluacion) {
									if($request->turn || $request->data['turn']){
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// New phase
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('empodera__2020_intermediate__phase');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
										$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->codigo_estado = Estado::getState('active');
										$register->visualizacion_usuario = false;
										$register->save();

										$assignment = new AsignacionJurado;
										$assignment->codigo_usuario = 361963;
										$assignment->codigo_estado = Estado::getState('active');
										$assignment->fecha_asignacion_jurado = date('Y-m-d');
										$assignment->hora_asignacion_jurado = date('H:m:s');
										$assignment->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
										$assignment->lugar_evaluacion_jurado = 'Virtual';
										$assignment->asignacion_cerrada = false;
										$assignment->save();

										$data = [
											'title' => 'iNNpulsa Empodera 2020',
											'header' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/innpulsa/innpulsa-mincomercio-gobiernocolombia-logos.png',
											'date' => Tuproyecto::dateFormat(date('Y-m-d'), 'daymonthnameyear'),
											'name' => ucwords(strtolower($dataUser->user_name)),
											'entity' => ucwords(strtolower($dataUser->entity_description)),
											'mail' => $dataUser->correo_electronico,
											'address' => $dataUser->direccion,
											'city' => $dataUser->nombre_ciudad,
											'short_name' => $dataUser->short_name,
											'code_application' => $registerPhase->numero_radicado,
											'url_application' => App::environment('local') ? 'http://localhost/plataforma/public/proyecto/'.$dataUser->nickname_usuario.'/documentos' : session('institution')->url_institucion.'/proyecto'.$dataUser->nickname_usuario.'/documentos'
										];

										// Build PDF
										$pdf = App::make('dompdf.wrapper');
										$pdf->loadHTML(view('reports.program.innpulsa.empodera2020.notification', $data));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'iNNpulsaEmpodera__2020', $registerPhase->codigo_registro_convocatoria);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
										'Bucket' =>  $bucket,
										'Key'    =>  $key,
										'Body'   =>  $pdf->output(),
										'ACL'    => 'public-read'
										]);

										// Sending Email
										$data = [];
										$data['_template'] = 'emails.program.notification_empodera';
										$data['_subject'] = 'Conoce los resultados de tu aplicación Empodera 2020';
										// Data Template
										$data['comment'] = Lang::get('program.mail.body.empodera2020reg');
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
										$data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
										SendEmail::notification($data);
									} else {
										$registerPhase->visualizacion_usuario = false;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									}
								} else {
									if($request->turn || $request->data['turn']){
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = false;
										$registerPhase->codigo_estado = Estado::getState('rejected');
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										$data = [
											'title' => 'iNNpulsa Empodera 2020',
											'header' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/innpulsa/innpulsa-mincomercio-gobiernocolombia-logos.png',
											'date' => Tuproyecto::dateFormat(date('Y-m-d'), 'daymonthnameyear'),
											'name' => ucwords(strtolower($dataUser->user_name)),
											'entity' => ucwords(strtolower($dataUser->entity_description)),
											'mail' => $dataUser->correo_electronico,
											'address' => $dataUser->direccion,
											'city' => $dataUser->nombre_ciudad,
											'short_name' => $dataUser->short_name,
											'code_application' => $registerPhase->numero_radicado,
											'questions' => Innpulsa::getItemEvaluation($registerPhase->codigo_registro_fase_usuario)
										];

										// Build PDF
										$pdf = App::make('dompdf.wrapper');
										$pdf->loadHTML(view('reports.program.innpulsa.empodera2020.notificacion_refuse', $data));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'iNNpulsaEmpodera__2020', $registerPhase->codigo_registro_convocatoria);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
										'Bucket' =>  $bucket,
										'Key'    =>  $key,
										'Body'   =>  $pdf->output(),
										'ACL'    => 'public-read'
										]);

										// Sending Email
										$data = [];
										$data['_template'] = 'emails.program.notification_empodera';
										$data['_subject'] = 'Conoce los resultados de tu aplicación Empodera 2020';
										// Data Template
										$data['comment'] = Lang::get('program.mail.body.empodera2020reg');
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
										$data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
										SendEmail::notification($data);
									} else {
										$registerPhase->visualizacion_usuario = false;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = false;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									}
								}
							break;
							case Fase::getIdPhase('empodera__2020_intermediate__phase'):
								$evalItemEmpren = EvaluacionItemEmprendimiento::where('codigo_item_evaluacion', '=', 171)->first();
								if(!$evalItemEmpren instanceof EvaluacionItemEmprendimiento) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro el adjunto']);
								}

								// New phase
								$register = new RegistroFaseUsuario;
								$register->codigo_fase = Fase::getIdPhase('empodera___2020_second__phase');
								$register->codigo_usuario = $registerPhase->codigo_usuario;
								$register->fecha_registro_fase_usuario = $registerPhase->fecha_registro_fase_usuario;
								$register->hora_registro_fase_usuario = $registerPhase->hora_registro_fase_usuario;
								$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
								$register->codigo_estado = Estado::getState('active');
								$register->visualizacion_usuario = false;
								$register->save();

							break;
							case Fase::getIdPhase('aldea__20_first__challenge'):
								// Scoring of the survey
								$scoringSurvey = UsuarioEncuesta::find($registerPhase->codigo_usuario_encuesta);

								if(!$scoringSurvey instanceof UsuarioEncuesta){
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro el usuario encuesta']);
								}

								$second__moment = Fase::find( Fase::getIdPhase('aldea__20_challenge__1_2') );
								if(!$second__moment instanceof Fase) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro el momento 1']);
								}

								if($request->turn || $request->data['turn']){
									if($scoringSurvey->puntaje_total_encuesta >= $phase->rango_inferior_evaluacion) {
										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = $second__moment->codigo_fase;
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									} else {
										$registerPhase->codigo_estado = Estado::getState('rejected');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										//Se apaga correo de rechazo reto 1 aldea 2.0
										Log::info('Se apaga correo emails.program.innpulsa.moment__one__refused');
										/* $data = [];
										$data['_template'] = 'emails.program.innpulsa.moment__one__refused';
										$data['_subject'] = 'Notificacion Momento 1';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										SendEmail::notification($data); */
									}
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							case Fase::getIdPhase('aldea__20_challenge__1_3'):
								if($request->turn || $request->data['turn']){
									$scoreRegisterPhase = PuntajeRegistroFase::where('codigo_registro_fase_usuario', '=', $registerPhase->codigo_registro_fase_usuario)->first();
									if(!$scoreRegisterPhase instanceof PuntajeRegistroFase) {
										DB::rollback();
										return response()->json(['success' => false, 'errors' => 'Aun no se ha evaluado en este momento']);
									}

									if($scoreRegisterPhase->valor_puntaje_registro_fase >= $phase->rango_inferior_evaluacion) {
										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('aldea__20_second__challenge');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;

										$balance = Innpulsa::get__Balance__Aldea20($registerPhase->codigo_usuario_encuesta);
										if($balance->saldo_voucher >= 60000000) {
											$register->codigo_estado = Estado::getState('active');

											/*$data = [];
											$data['_template'] = 'emails.program.innpulsa.moment__three';
											$data['_subject'] = 'Notificacion Momento 3';
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_entity_name'] = $dataUser->entity_name;
											$data['_inst'] = session('institution')->codigo_institucion;
											SendEmail::notification($data);*/
										} else {
											$register->codigo_estado = Estado::getState('pendinginstitution');

											/*$data = [];
											$data['_template'] = 'emails.program.innpulsa.moment__three_refused';
											$data['_subject'] = 'Notificacion Momento 3';
											$data['_to'] = $dataUser->correo_electronico;
											$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
											$data['_entity_name'] = $dataUser->entity_name;
											$data['_inst'] = session('institution')->codigo_institucion;
											SendEmail::notification($data);*/

											$messageFase = 'No se cuenta con los recursos suficientes';

										}

										$register->save();

									} else {
										$registerPhase->codigo_estado = Estado::getState('rejected');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										//Se apaga correo de rechazo reto 1.3 aldea 2.0
										Log::info('Se apaga correo emails.program.innpulsa.moment__three_minimum');
									/* 	$data = [];
										$data['_template'] = 'emails.program.innpulsa.moment__three_minimum';
										$data['_subject'] = 'Notificacion Momento 3';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										SendEmail::notification($data); */
									}
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							case Fase::getIdPhase('aldea__20_second__challenge'):
								if($request->turn || $request->data['turn']){
									$score = PuntajeRegistroFase::where('codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->first();
									if(!$score instanceof PuntajeRegistroFase) {
										DB::rollback();
										return response()->json(['success' => false, 'errors' => 'Aun no se ha evaluado en este momento']);
									}

									$dataDocument = Innpulsa::getDataKeysAccessAprove($registerPhase->codigo_registro_fase_usuario, $registerPhase->codigo_registro_convocatoria);
									$comments = ComentDefDocument::select('texto_coment_eval_emprendi')
										->join('core.coment_eval_emprendi', 'coment_eval_emprendi.codigo_coment_eval_emprendi', '=', 'coment_def_document.codigo_coment_eval_emprendi')
										->where('codigo_registro_fase_usuario', $codigo_registro_fase_usuario)
										->get();
									$pdf = App::make('dompdf.wrapper');

									$recomendations = AsignacionJurado::select('resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion')
										->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=','asignacion_jurado.codigo_asignacion_jurado')
										->where('asignacion_jurado.codigo_registro_fase_usuario', $registerPhase->codigo_registro_fase_usuario)->get();

									if($registerPhase->valor_asignacion_rfu > 0) {
										$categories = CategoriaProducto::getCategories($registerPhase->codigo_registro_fase_usuario);

										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = Fase::getIdPhase('aldea__20_three__challenge');
										$register->codigo_estado = Estado::getState('no_linked');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->valor_asignacion_rfu = $registerPhase->valor_asignacion_rfu;
										$register->save();

										$usuario_entidad = Usuario::getUserByEntity($registerPhase->codigo_usuario);

										$detalleVoucher = new DetalleVoucher;
										$detalleVoucher->codigo_estado = Estado::getState('pending');
										$detalleVoucher->codigo_usuario = $usuario_entidad->owner_id;
										$detalleVoucher->codigo_voucher = Voucher::getVoucher('aldea');
										$detalleVoucher->valor_detalle_voucher = $registerPhase->valor_asignacion_rfu;
										$detalleVoucher->saldo_detalle_voucher = $registerPhase->valor_asignacion_rfu;
										$detalleVoucher->fecha_detalle_voucher = date('Y-m-d');
										$detalleVoucher->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
										$detalleVoucher->save();

										// Insert Score
										$registerScore = new PuntajeRegistroFase();
										$registerScore->codigo_estado = Estado::getState('active');
										$registerScore->codigo_registro_fase_usuario = $register->codigo_registro_fase_usuario;
										$registerScore->valor_puntaje_registro_fase = $score->valor_puntaje_registro_fase;
										$registerScore->save();

										$dataPDF = [];
										$dataPDF['recomendations'] = $recomendations;
										$dataPDF['categories'] = $categories;
										$dataPDF['data'] = $dataDocument;
										$dataPDF['score__challenge_Two'] = $score->valor_puntaje_registro_fase;
										$dataPDF['_comments'] = $comments;
										$dataPDF['keys'] = money_format("%i", $registerPhase->valor_asignacion_rfu);

										$pdf->loadHTML(view('reports.program.innpulsa.letter_keys', $dataPDF));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'carta_llaves', $registerPhase->codigo_registro_fase_usuario);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
										'Bucket' =>  $bucket,
										'Key'    =>  $key,
										'Body'   =>  $pdf->output(),
										'ACL'    => 'public-read'
										]);

										//Se apaga correo de aceptación reto 2.0 aldea 2.0
										Log::info('Se apaga correo emails.program.innpulsa.challenge__two');
									/* 	$data = [];
										$data['_template'] = 'emails.program.innpulsa.challenge__two';
										$data['_subject'] = 'Notificación Reto 2 ¡Conversa con Expertos! de ALDEA';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_recomendations'] = $recomendations;
										$data['_categories'] = $categories;
										$data['_score'] = $score->valor_puntaje_registro_fase;
										$data['_total_keys'] = $registerPhase->valor_asignacion_rfu;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_attach'] = [$result['ObjectURL'] => 'Carta de Llaves de Acceso.pdf'];
										$data['url_action'] = sprintf("/%s/%s/%s", 'proyecto', $dataUser->nickname_usuario, 'documentos');
										SendEmail::notification($data); */
									} else {
										$registerPhase->codigo_estado = Estado::getState('rejected');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										$dataPDF = [];
										$dataPDF['recomendations'] = $recomendations;
										$dataPDF['data'] = $dataDocument;
										$dataPDF['score__challenge_Two'] = $score->valor_puntaje_registro_fase;
										$dataPDF['_comments'] = $comments;
										$dataPDF['keys'] = money_format("%i", $registerPhase->valor_asignacion_rfu);

										$pdf->loadHTML(view('reports.program.innpulsa.letter_keys_refuse', $dataPDF));

										$s3 = AWS::createClient('s3');

										$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

										$filename = sprintf('%s_%s.pdf', 'carta_llaves', $registerPhase->codigo_registro_fase_usuario);
										$key = sprintf('%s/%s/%s', 'documents', 'innpulsa', $filename);

										$result = $s3->putObject([
										'Bucket' =>  $bucket,
										'Key'    =>  $key,
										'Body'   =>  $pdf->output(),
										'ACL'    => 'public-read'
										]);

										//Se apaga correo de rechazo reto 2.0 aldea 2.0
										Log::info('Se apaga correo emails.program.innpulsa.challenge__two_refused');
										/* $data = [];
										$data['_template'] = 'emails.program.innpulsa.challenge__two_refused';
										$data['_subject'] = 'Notificación Reto 2 ¡Conversa con Expertos! de ALDEA';
										$data['_to'] = $dataUser->correo_electronico;
										$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
										$data['_entity_name'] = $dataUser->entity_name;
										$data['_recomendations'] = $recomendations;
										$data['_score'] = $score->valor_puntaje_registro_fase;
										$data['_total_keys'] = $registerPhase->valor_asignacion_rfu;
										$data['_fecha_aplicacion'] = date("Y-m-d",strtotime($dataUser->fecha_registro_convocatoria."+ 180 days"));
										$data['_inst'] = session('institution')->codigo_institucion;
										$data['_attach'] = [$result['ObjectURL'] => 'Carta de Llaves de Acceso.pdf'];
										SendEmail::notification($data); */
									}
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							default:
								if(isset($phase->cor_codigo_fase)) {
									if (!Fase::find($phase->cor_codigo_fase) instanceof Fase) {
										DB::rollback();
										return response()->json(['success' => false, 'errors' => 'No se encontro la fase de Registro']);
									}

									if($request->turn || $request->data['turn']){
										$registerPhase->codigo_estado = Estado::getState('forward');
										$registerPhase->visualizacion_usuario = true;
										$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
										$registerPhase->notificacion_registro_fase_usuario = true;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

										// Insert Register Phase User
										$register = new RegistroFaseUsuario;
										$register->codigo_fase = $phase->cor_codigo_fase;
										$register->codigo_estado = Estado::getState('active');
										$register->codigo_usuario = $registerPhase->codigo_usuario;
										$register->fecha_registro_fase_usuario = date('Y-m-d');
										$register->hora_registro_fase_usuario = date('H:i:s');
										$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
										$register->visualizacion_usuario = false;
										$register->save();
									} else {
										$registerPhase->visualizacion_usuario = false;
										$registerPhase->notificacion_registro_fase_usuario = false;
										$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
									}
								} else {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No existe la siguiente fase']);
								}
							break;
						}
						$registerPhase->save();
					break;

					case Institucion::institution('dinamiza'):
						Dinamiza::registerToPhase($phase, $dataUser, $registerPhase);
					break;

					case Institucion::institution('propace'):
						$messageFase = Propace::registerToPhase($phase, $dataUser, $registerPhase, $request);
					break;

					case Institucion::institution('bolivar') :
						switch ($phase->codigo_fase) {
							case Fase::getIdPhase('reg_emprende_bog_2019'):
								$phaseReg = Fase::find( Fase::getIdPhase('reg_emprende_bog_2019') );
								if(!$phaseReg instanceof Fase) {
									DB::rollback();
									return response()->json(['success' => false, 'errors' => 'No se encontro la fase de Registro']);
								}

								$register = RegistroFaseUsuario::where('codigo_usuario', $registerPhase->codigo_usuario)->where('codigo_fase', Fase::getIdPhase('reg_emprende_bog_2019'))->where('codigo_registro_convocatoria', $registerPhase->codigo_registro_convocatoria)->first();
								if(!$register instanceof  RegistroFaseUsuario) {
									 //Insert Register Phase User
									$register = new RegistroFaseUsuario;
									$register->codigo_fase = $phaseReg->codigo_fase;
									$register->codigo_usuario = $registerPhase->codigo_usuario;
									$register->fecha_registro_fase_usuario = date('Y-m-d');
									$register->hora_registro_fase_usuario = date('H:i:s');
									$register->codigo_estado = Estado::getState('active');
									$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
									$register->visualizacion_usuario = false;
									$register->save();
								}

								$registerPhase->codigo_estado = Estado::getState('finalized');
								$registerPhase->visualizacion_usuario = true;
								$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
								$registerPhase->notificacion_registro_fase_usuario = true;
								$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

								$data = [];
								$data['_template'] = 'emails.program.bolivar.diagnostico';
								$data['_subject'] = 'Diagnostico para el enrutamiento de empresas';
								$data['_to'] = $dataUser->correo_electronico;
								$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
								$data['_inst'] = session('institution')->codigo_institucion;
								$data['url_action'] = sprintf('/encuesta/aplicar/diagnostico-para-el-enrutamiento-de-empresas/%s?url_fase=%s', TuProyecto::encryptSrcParams([$registerPhase->codigo_usuario]), $phaseReg->url_fase);
								SendEmail::notification($data);
							break;
						}
						$registerPhase->save();
					break;

					case Institucion::institution('terpel'):
						switch ($phase->codigo_fase) {
							case Fase::getIdPhase('terpel__20_inscription'):
								if($request->turn || $request->data['turn']){

									$registerPhase->codigo_estado = Estado::getState('finalized');
									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

									//Insert Register Phase User
									$register = new RegistroFaseUsuario;
									$register->codigo_fase = Fase::getIdPhase('terpel__20_second');
									$register->codigo_usuario = $registerPhase->codigo_usuario;
									$register->fecha_registro_fase_usuario = date('Y-m-d');
									$register->hora_registro_fase_usuario = date('H:i:s');
									$register->codigo_estado = Estado::getState('active');
									$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
									$register->visualizacion_usuario = false;
									$register->save();

								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
							case Fase::getIdPhase('terpel__20_second'):
								if($request->turn || $request->data['turn']){
									$registerPhase->codigo_estado = Estado::getState('finalized');
									$registerPhase->visualizacion_usuario = true;
									$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
									$registerPhase->notificacion_registro_fase_usuario = true;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

									//Insert Register Phase User
									$register = new RegistroFaseUsuario;
									$register->codigo_fase = Fase::getIdPhase('terpel__20_three');
									$register->codigo_usuario = $registerPhase->codigo_usuario;
									$register->fecha_registro_fase_usuario = date('Y-m-d');
									$register->hora_registro_fase_usuario = date('H:i:s');
									$register->codigo_estado = Estado::getState('active');
									$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
									$register->visualizacion_usuario = false;
									$register->save();
								} else {
									$registerPhase->visualizacion_usuario = false;
									$registerPhase->notificacion_registro_fase_usuario = false;
									$registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
								}
							break;
						}

						$registerPhase->save();
					break;
					case Institucion::institution('rutan'):
						$messageFase = Rutan::registerToPhase($phase, $dataUser, $registerPhase, $request);
					break;
				}
				// Commit Transaction
				DB::commit();

				if (isset($messageFase)) {
					return response()->json(['success' => false, 'errors' => $messageFase ]);
				}

				return response()->json(['success' => true, 'message' => 'Cambiando visualización', 'visualizacion_usuario' => $request->turn, 'codigo_estado' => $registerPhase->codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'Email reporte notification', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => 'Ocurrio un error al momento de enviar el mail']);
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
	public function destroy($id)
	{
		//
	}

	/**
	 * Forward email notificacion the specified phase.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function notification(Request $request, $id)
	{
		if ($request->ajax()) {

			// Decrypt src-params
			list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($id);
			if(!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
				return response()->json(['success' => false, 'errors' => 'El código de registro fase no es un número valido']);
			}

			$registerPhase = RegistroFaseUsuario::join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria')->find($codigo_registro_fase_usuario);
			if(!$registerPhase instanceof RegistroFaseUsuario) {
				return response()->json(['success' => false, 'errors' => "El código de registro fase ($codigo_registro_fase_usuario) no se encuentra en el sistema."]);
			}

			$dataUser = Usuario::getUserByEntity($registerPhase->codigo_usuario);
			if(!$dataUser instanceof Usuario) {
				return response()->json(['success' => false, 'errors' => 'La entidad no pertenece a un usuario registrado.']);
			}

			$phase = Fase::find($registerPhase->codigo_fase);
			if(!$phase instanceof Fase) {
				return response()->json(['success' => false, 'errors' => 'No se encontro la fase']);
			}

			switch ($phase->codigo_fase) {
				case Fase::getIdPhase('four_challenge'):
					$data = [];
					$data['_template'] = 'emails.program.innpulsa.reto4';
					$data['_subject'] = '¡Levantemos Capital! - ALDEA';
					$data['_to'] = $dataUser->correo_electronico;
					$data['_to_name'] = ucwords(strtolower($dataUser->user_name));
					$data['_inst'] = session('institution')->codigo_institucion;
					$data['url_action'] = sprintf('/encuesta/aplicar/aldea-reto4/%s?url_fase=%s', TuProyecto::encryptSrcParams([$registerPhase->codigo_usuario]), $phase->url_fase);
					SendEmail::notification($data);
				break;
			}
			return response()->json(['success' => true, 'message' => "Email notificación $phase->nombre_fase enviado a $dataUser->correo_electronico"]);
		}
		abort(404);
	}
}
