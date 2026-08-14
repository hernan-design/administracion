<?php

namespace App\Http\Controllers;

use App\Models\Core\ConvocatoriaCategoria;
use App\Models\Core\ConvocatoriaEtapa;
use App\Models\Core\Eje;
use App\Models\Core\EtapaProyecto;
use App\Models\Core\TipoRecursoFase;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Lang, App, Log, Auth, TuProyecto, Datatables;
use App\Models\Core\Convocatoria,
	App\Models\Core\Estado,
	App\Models\Core\RegistroFaseUsuario,
	App\Models\Core\OrientacionConvocatoria,
	App\Models\Core\RespuestaEncuesta,
	App\Models\Core\SectorInteres,
	App\Models\Core\CategoriaProceso,
	App\Models\Core\PrivacidadContenido,
	App\Models\Core\ConvocatoriaInstitucion,
	App\Models\Core\Encuesta,
	App\Models\Core\EvaluacionEmprendimiento,
	App\Models\Core\LugarAplicacion,
	App\Models\Core\EncuestaInstitucion,
	App\Models\Seguridad\Usuario,
	App\Models\Param\Institucion,
	App\Models\Core\ConvocatoriaMentoria,
	App\Models\Core\Entidad,
	App\Models\Param\TerminosCondiciones,
	App\Models\Core\Asesoria,
	App\Models\Aprende\AsesoriaInstitucion;


class ConvocatoriaController extends Controller
{
	/**
	 * Instantiate a new Controller instance.
	 */
	public function __construct()
	{
		$this->middleware('ability:admin|super-admin|administrativo,view,convocatoria', ['except' => ['search']]);
		$this->middleware('ability:admin|super-admin|administrativo,create,convocatoria', ['only' => ['create', 'store']]);
		$this->middleware('ability:admin|super-admin|administrativo,edit,convocatoria', ['only' => ['edit', 'update']]);
		$this->middleware('ability:admin|super-admin|administrativo,remove,convocatoria', ['only' => ['destroy']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{

		if($request->ajax()) {
			switch ($request->data) {
				case 'list':
					return response()->json(Convocatoria::getDataCallsList($request->type));
					break;
				default:
					return response()->json(Convocatoria::getDataCalls($request->type));
			}
		}

		$types = Convocatoria::$type;
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);

		return view('program.index', compact('types', 'privacyContent', 'states'));
	}

	public function notify(Request $request){
		if($request->ajax()){
			echo $request;
		}
		abort(403);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
  	{

		$types = Convocatoria::$type;
		$processCategory = CategoriaProceso::getProcessCategory();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$programOrentation = OrientacionConvocatoria::getOrientationAll();
		$stages = EtapaProyecto::getAllStages(session('institution')->codigo_institucion);
		$allEjes = Eje::getAllEje();

		return view('program.create-edit', compact('privacyContent', 'types', 'processCategory', 'programOrentation', 'stages', 'allEjes'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if($request->ajax()){
			$data = $request->all();
			$program = new Convocatoria();
			if($program->isValid($data)){
				DB::beginTransaction();
				try {
					$program->fill($data);
					$program->codigo_usuario = Auth::user()->codigo_usuario;
					$program->codigo_estado = Estado::getState('active');
					$program->fecha_creacion_convocatoria = date('Y-m-d');
					$program->hora_creacion_convocatoria = date('H:m:s');
					$program->url_convocatoria = slugtp($request->nombre_convocatoria, $program->getTable(), 'url_convocatoria');
					$program->banner_convocatoria = $request->imagen_convocatoria;
					$program->texto_boton = isset($request->texto_boton) && !empty($request->texto_boton) ? $request->texto_boton : 'Participar';
					$program->codigo_institucion = session('institution')->codigo_institucion;
					$program->permanente_convocatoria = $request->permanente_convocatoria ? TRUE : FALSE ;

					if($program->permanente_convocatoria){
						if(isset($request->fecha_cierre_convocatoria) ){
						$program->fecha_cierre_convocatoria = trim($request->fecha_cierre_convocatoria) === '' ? NULL : trim($request->fecha_cierre_convocatoria);
						}else{
						$program->fecha_cierre_convocatoria = NULL;
						}

            if(isset($request->hora_cierre_convocatoria) ){
              $program->hora_cierre_convocatoria = trim($request->hora_cierre_convocatoria) === '' ? NULL : trim($request->hora_cierre_convocatoria);
              }else{
              $program->hora_cierre_convocatoria = NULL;
            }

					}else{
						if(isset($request->fecha_cierre_convocatoria) ){
						$program->fecha_cierre_convocatoria = trim($request->fecha_cierre_convocatoria) === '' ? date('Y-m-d') : trim($request->fecha_cierre_convocatoria);
						}else{
						$program->fecha_cierre_convocatoria = date('Y-m-d');
						}

            if(isset($request->hora_cierre_convocatoria) ){
              $program->hora_cierre_convocatoria = trim($request->hora_cierre_convocatoria) === '' ? date('H:i:s') : trim($request->hora_cierre_convocatoria);
              }else{
              $program->hora_cierre_convocatoria = date('H:i:s');
            }
					}

					$program->visualizacion_usuario = $request->visualizacion_usuario ? TRUE : FALSE;
					$program->evaluacion_convocatoria = $request->evaluacion_convocatoria ? TRUE : FALSE;
          			$program->codigo_etapa_proyecto =  $request->has('etapa_convocatoria') ? $request->etapa_convocatoria === "" ? null : $request->etapa_convocatoria : null;
					$program->codigo_encuesta = !is_null($program->codigo_encuesta) ? $request->has('codigo_encuesta') ? $request->codigo_encuesta : $request->codigo_encuesta : $request->codigo_encuesta;
					$program->codigo_eje = $request->eje_convocatoria ? $request->eje_convocatoria : null;
          			$program->sync_algolia = 1;
					$program->save();

					$institutionProgram = new ConvocatoriaInstitucion;
					$institutionProgram->codigo_convocatoria = $program->codigo_convocatoria;
					$institutionProgram->codigo_institucion = session('institution')->codigo_institucion;
					$institutionProgram->codigo_estado = Estado::getState('active');
					$institutionProgram->codigo_cat_proceso_insti = $request->has('codigo_cat_proceso_insti') && !empty($request->codigo_cat_proceso_insti) ? $request->codigo_cat_proceso_insti : null;
					$institutionProgram->save();
					EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
						->where('codigo_encuesta', $program->codigo_encuesta)
						->update([
							'codigo_lugar_aplicacion' => LugarAplicacion::getPlace('programs'),
							'codigo_recurso' => $program->codigo_convocatoria
						]);

					if($program->codigo_privacidad_contenido != PrivacidadContenido::getPrivacy('public')){
						$program->sync_algolia = 3;
						$program->save();
						$program->desyncIndex();
					}

					// Commit Transaction
					DB::commit();
          			$program->syncIndex();
					return response()->json(['success' => true, 'message' => 'La convocatoria ha sido creada correctamente!!', 'codigo_convocatoria' => $program->codigo_convocatoria, 'redirect' => true]);

				}catch(\Exception $e){
					DB::rollback();
					Log::error(sprintf('%s:%s', 'ConvocatoriaController:store', $e->getMessage()));
					return response()->json(['success' => false, 'errors' => trans('app.exception')]);
				}
			}
			return response()->json(['success' => false, 'errors' => $program->errors]);
		}
		abort(404);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id)
	{
		$program = Convocatoria::getDataProgram($id);
		if(!$program instanceof Convocatoria) {
			abort(404);
		}

    if (session('institution')->contenido_usuario) {
      if ($program->codigo_usuario != Auth::user()->codigo_usuario) {
        abort(404);
      }
    }

		if ($request->ajax()) {
			$response = Convocatoria::buildCall($program);
			return response()->json($response);
		}
		return view('program.detail');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(!is_numeric($id)) {
      		Log::error(sprintf('%s:%s', 'ConvocatoriaController:edit', 'Error: not numeric ID'));
      		abort(404,'Error: not numeric ID') ;
    	}

		$program = Convocatoria::getDataProgram($id);
		if(!$program instanceof Convocatoria){
			abort(404);
		}

    if (session('institution')->contenido_usuario) {
      if ($program->codigo_usuario != Auth::user()->codigo_usuario) {
        abort(404);
      }
    }

		$types = Convocatoria::$type;
		$public = PrivacidadContenido::getPrivacy('public');
		$processCategory = CategoriaProceso::getProcessCategory();
		$privacyContent = PrivacidadContenido::getContentPrivacy();
		$programOrentation = OrientacionConvocatoria::getOrientationAll();
    	$stages = EtapaProyecto::getAllStages(session('institution')->codigo_institucion);
    	$categoriesProgram = ConvocatoriaCategoria::getCategoriesProgram($program->codigo_convocatoria);
    	$stagesProgram = ConvocatoriaEtapa::getStagePrograms($program->codigo_convocatoria);
    	$resourcesPhases = TipoRecursoFase::getAllResourcesPhases();
    	$allEjes = Eje::getAllEje();
		// $evaluador =  Convocatoria::find($id, ['asignacion_auto_evaluador']);

		return view('program.create-edit', compact(
					'program',
					'privacyContent',
					'types',
					'processCategory',
					'programOrentation',
					'public',
					'stages',
					'categoriesProgram',
					'stagesProgram',
					'resourcesPhases',
					'allEjes'
					// 'evaluador'
				));
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
		if($request->ajax()){
			$data = $request->all();
			$program = Convocatoria::find($id);
			if(!$program instanceof Convocatoria){
				throw new \Exception("Convocatoria no encontrada {$id}");
			}

			$institutionProgram = $program->institutions->where('codigo_institucion', session('institution')->codigo_institucion)->first();
			if(!$institutionProgram instanceof ConvocatoriaInstitucion){
				throw new \Exception("Convocatoria Institucion no encontrada {$id}");
			}

			DB::beginTransaction();
			try {
				if ( !$request->has('change_state') ) {
					if ($request->has('codigo_entidad') && !$request->has('unlink_entity')) {
						Convocatoria::where('codigo_institucion', session('institution')->codigo_institucion)
						->where('codigo_convocatoria', $request->codigo_convocatoria)
						->update([
							'seg_codigo_usuario' => ( int ) $request->codigo_entidad,
							'sync_algolia'=> 1
						]);
					} else if ($request->has('codigo_entidad') && $request->has('unlink_entity')) {
						Convocatoria::where('codigo_institucion', session('institution')->codigo_institucion)
						->where('codigo_convocatoria', $request->codigo_convocatoria)
						->update([
							'seg_codigo_usuario' => null,
							'sync_algolia'=> 1
						]);
					} else if ($request->has('numero_resultados') && $request->has('codigo_convocatoria')) {
						$user = Usuario::find(\Auth::user()->codigo_usuario);
						if(!$user instanceof Usuario) {
						throw new \Exception("Error recuperando usuario ".\Auth::user()->codigo_usuario);
						}

						Convocatoria::where('codigo_institucion',  session('institution')->codigo_institucion)
						->where('codigo_convocatoria', $request->codigo_convocatoria)
						->update([
							'contador_inscritos' => $request->numero_resultados,
							'sync_algolia'=> 1
						]);

						activity()
							->performedOn($user)
							->withProperties([
							'attributes' => [
								'user' => $user->nombre_usuario,
								'user_id' => $user->codigo_usuario,
								'convocatoria' => $program->nombre_convocatoria
							]
							])
							->log('activitylog.program');
					} else {
						if ($program->isValid($data)) {
						// Remove before register
						EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
							->where('codigo_encuesta', $program->codigo_encuesta)
							->where('codigo_recurso', $program->codigo_convocatoria)
							->update([
							'codigo_lugar_aplicacion' => null,
							'codigo_recurso' => null
							]);
						$program->fill($data);
						$program->banner_convocatoria = $request->imagen_convocatoria;
						$program->permanente_convocatoria = $request->permanente_convocatoria ? TRUE : FALSE;
						$program->visualizacion_usuario = $request->visualizacion_usuario ? TRUE : FALSE;
						$program->evaluacion_convocatoria = $request->evaluacion_convocatoria ? TRUE : FALSE;
            // $program->codigo_estado = $request->estado_evaluacion ? Estado::getState('inactive')  : Estado::getState('active');
						$program->texto_boton = isset($request->texto_boton) && !empty($request->texto_boton) ? $request->texto_boton : 'Participar';

						if($program->permanente_convocatoria){
							if(isset($request->fecha_cierre_convocatoria) ){
							$program->fecha_cierre_convocatoria = trim($request->fecha_cierre_convocatoria) === '' ? NULL : trim($request->fecha_cierre_convocatoria);
							}else{
							$program->fecha_cierre_convocatoria = NULL;
							}

              if(isset($request->hora_cierre_convocatoria) ){
                $program->hora_cierre_convocatoria = trim($request->hora_cierre_convocatoria) === '' ? NULL : trim($request->hora_cierre_convocatoria);
                }else{
                $program->hora_cierre_convocatoria = NULL;
                }
						}else{
							if(isset($request->fecha_cierre_convocatoria) ){
							$program->fecha_cierre_convocatoria = trim($request->fecha_cierre_convocatoria) === '' ? date('Y-m-d') : trim($request->fecha_cierre_convocatoria);
							}else{
							$program->fecha_cierre_convocatoria = date('Y-m-d');
							}

              if(isset($request->hora_cierre_convocatoria) ){
                $program->hora_cierre_convocatoria = trim($request->hora_cierre_convocatoria) === '' ? date('H:i:s') : trim($request->hora_cierre_convocatoria);
                }else{
                $program->hora_cierre_convocatoria = date('H:i:s');
                }
						}
							if (!is_null($program->codigo_etapa_proyecto)) {
							if ($request->has('etapa_convocatoria')) {
								if ($request->etapa_convocatoria === "") {
									$program->codigo_etapa_proyecto = null;
								} else {
									$program->codigo_etapa_proyecto = $request->etapa_convocatoria;
								}
							} else {
								$program->codigo_etapa_proyecto = null;
							}
							} if (!is_null($program->convocatoria_eje)) {
							if ($request->has('eje_convocatoria')) {
								if ($request->eje_convocatoria === "") {
								$program->convocatoria_eje = null;
								} else {
								$program->convocatoria_eje = $request->eje_convocatoria;
								}
							} else {
							$program->convocatoria_eje = null;
							}
							} if (is_null($program->convocatoria_eje)){
							if ($request->has('eje_convocatoria')) {
							if ($request->eje_convocatoria === "") {
								$program->codigo_eje = null;
							} else {
								$program->codigo_eje = $request->eje_convocatoria;
							}
							} else {
							$program->codigo_eje = null;
							}
						} else {
							if ($request->has('etapa_convocatoria')) {
								if ($request->etapa_convocatoria === "") {
								$program->codigo_etapa_proyecto = null;
								} else {
								$program->codigo_etapa_proyecto = $request->etapa_convocatoria;
								}
							}
							}
						$program->codigo_encuesta = !is_null($program->codigo_encuesta) ? $request->has('codigo_encuesta') ? $request->codigo_encuesta : $request->codigo_encuesta : $request->codigo_encuesta;

						if($program->codigo_privacidad_contenido != PrivacidadContenido::getPrivacy('public')){
							$program->sync_algolia = 3;
							$program->save();
							$program->desyncIndex();
						}
						if(isset($program->sync_algolia) && $program->sync_algolia === 0  ){
							$program->sync_algolia = 1;
						}

            $program->ocultar_al_finalizar = $request->ocultar_al_finalizar ? true : false;


						$program->save();

						// Add new register
						EncuestaInstitucion::where('codigo_institucion', session('institution')->codigo_institucion)
							->where('codigo_encuesta', $program->codigo_encuesta)
							->update([
							'codigo_lugar_aplicacion' => LugarAplicacion::getPlace('programs'),
							'codigo_recurso' => $program->codigo_convocatoria
							]);
						} else {
						return response()->json(['success' => false, 'errors' => $program->errors]);
						}
					}
				}

        //Se valida si la convocatoria tiene fecha y hora de cierre para quitarsela
        if($program->fecha_cierre_convocatoria != null && $program->fecha_cierre_convocatoria != "" && $request->has('change_state') && $institutionProgram->codigo_estado == 9){
          $program->fecha_cierre_convocatoria = null;
          $program->ocultar_al_finalizar = false;
        }

        if($program->hora_cierre_convocatoria != null && $program->hora_cierre_convocatoria != "" && $request->has('change_state') && $institutionProgram->codigo_estado == 9){
          $program->hora_cierre_convocatoria = null;
          $program->ocultar_al_finalizar = false;
        }

        $program->save();

        $codigo_estado = $request->has('change_state') ? $institutionProgram->codigo_estado == 9 ? Estado::getState('active') : Estado::getState('inactive') : $institutionProgram->codigo_estado;


				$codigo_cat_proceso_insti = $request->has('codigo_cat_proceso_insti') && !is_null($request->codigo_cat_proceso_insti) ? $request->codigo_cat_proceso_insti : $institutionProgram->codigo_cat_proceso_insti;
				ConvocatoriaInstitucion::where('codigo_convocatoria', $id)
					->where('codigo_institucion', $institutionProgram->codigo_institucion)
					->update([
						'codigo_estado' => $codigo_estado,
						'codigo_cat_proceso_insti' => $codigo_cat_proceso_insti ? $codigo_cat_proceso_insti : null
					]);

					if($codigo_estado == Estado::getState('inactive')){
						$program->sync_algolia = 3;
						$program->save();
						$program->desyncIndex();
						$log = 'activitylog.call.deactivated';
					}elseif($codigo_estado == Estado::getState('active')){
						$program->sync_algolia = 1;
						$program->save();
						$log = 'activitylog.call.activated';
					}

				$user = Usuario::find(\Auth::user()->codigo_usuario);

				activity()
				->performedOn($user)
				->withProperties([
					'attributes' => [
				'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'convocatoria' => $program->nombre_convocatoria
					]
				])
				->log($log);

				// Commit Transaction
				DB::commit();
        		$program->syncIndex();
				return response()->json(['success' => true, 'message' => 'La convocatoria ha sido editada correctamente!!', 'codigo_convocatoria' => $id, 'codigo_estado' => $codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'ConvocatoriaController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
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

	public function search(Request $request)
	{
		if($request->ajax()){
			return response()->json(Convocatoria::getDataCallsList($request->type));
		}
		abort(403);
	}

	public function cancelRegister(Request $request) {
		DB::beginTransaction();
		try {
			list($codigo_registro_fase_usuario) = TuProyecto::decryptSrcParams($request->register);
			if (!$codigo_registro_fase_usuario || !is_numeric($codigo_registro_fase_usuario)) {
				DB::rollback();
				return response()->json(['success' => false, 'errors' => 'El código de registro fase no es un número ']);
			}

			$register = RegistroFaseUsuario::find($codigo_registro_fase_usuario);
			if (!$register instanceof RegistroFaseUsuario) {
				DB::rollback();
				return response()->json(['success' => false, 'errors' => 'No se encontro el emprendimiento']);
			}

			$register->codigo_estado = Estado::getState('removed');
			$register->save();

			//Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Anulado correctamente', 'register' => $register->codigo_registro_fase_usuario, 'state' => $register->codigo_estado]);
		} catch (\Throwable $th) {
			DB::rollback();
			Log::error(sprintf('%s:%s', 'Error en anulacion', $th->getMessage()));
			return response()->json(['success' => false, 'errors' => 'Error intentando anular']);
		}
	}

	/**
	 * obtener mentores asignados a la convocatoria
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function getAssignments(Request $request) {
		if ($request->ajax()) {

			$entity = Entidad::getEntityUrl($request['nickname_usuario']);
			if(!$entity instanceof Entidad) {
				abort(404);
			}

			$user = Usuario::find($entity->seg_codigo_usuario);
			if(!$user instanceof Usuario) {
				abort(404);
			}

			$query = ConvocatoriaMentoria::select(
													'convocatoria_mentoria.codigo_usuario',
													'convocatoria_mentoria.seg_codigo_usuario',
													'convocatoria_mentoria.codigo_asesoria',
													'fase.nombre_fase',
													'asesoria_institucion.codigo_estado'
												)
											->where('convocatoria_mentoria.codigo_convocatoria', $request['codigo_convocatoria'])
											->join('core.fase', 'fase.codigo_fase', '=', 'convocatoria_mentoria.codigo_fase')
											->join('aprende.asesoria_institucion', 'asesoria_institucion.codigo_asesoria', '=', 'convocatoria_mentoria.codigo_asesoria')
											->where('codigo_usuario', $entity->codigo_usuario)
											->orderBy('codigo_convocatoria_mentoria', 'desc')
											->get();

			return Datatables::of($query)
				->addColumn('nombre_mentor', function ($data) {
					return $data->mentor->nombre_usuario . ' ' . $data->mentor->persona->primer_apellido_persona;
				})
				->addColumn('correo_mentor', function ($data) {
					return $data->mentor->correo_electronico;
				})
				->make(true);
		}
	}

	/**
	 * permite asignar mentor a entidad
	 * pero asociada a la convocatoria
	 * @param  int  $id codigo de convocatoria
	 * @return \Illuminate\Http\Response
	 */
	public function assignmentMentorView($id, $nickname_usuario) {

		return view('program.assignmentmentor');
	}

	/**
     * get mentor de mentoria masiva
     * @param \Illuminate\Http\Request  $request
     */
    public function storeMentoria(Request $request)
    {
		$program = Convocatoria::find($request['codigo_convocatoria']);
		$nickname_usuario = [ 'nickname_usuario' => $request['nickname_usuario']];
		$respuesta = $this->getProgramEntity($program, $nickname_usuario);

		$user_mentoring = Usuario::getUserMail(['email' => $request['correo_electronico']]);
		if(!$user_mentoring instanceof Usuario) {
			abort(404);
		}

		$entity = Entidad::getEntityUrl($request['nickname_usuario']);
		if(!$entity instanceof Entidad) {
			abort(404);
		}

		$user = Usuario::find($entity->seg_codigo_usuario);
		if(!$user instanceof Usuario) {
			abort(404);
		}

		// validation if mentor have mentoring active with the same project
		$validationMentoring = Asesoria::join('aprende.asesoria_institucion', 'asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
			->where('asesoria_institucion.codigo_institucion', session('institution')->codigo_institucion)
			->where('seg_codigo_usuario', $user_mentoring->codigo_usuario)
			->where('codigo_entidad', $entity->codigo_entidad)
			->where('asesoria_institucion.codigo_estado', Estado::getState('active'))
			->first();

		if($validationMentoring instanceof Asesoria){
			return response()->json(['success' => false, 'errors' => 'El asesor tiene una asesoría activa con este proyecto.']);
		}

		// validate if I accept terms
		$acceptTerms = Usuario::query();
		$acceptTerms->select(
				DB::raw("(SELECT CASE WHEN aceptacion_terminos.fecha_aceptacion_terminos IS NULL THEN FALSE ELSE TRUE END  FROM seguridad.aceptacion_terminos
					JOIN param.version_terminos ON aceptacion_terminos.codigo_version_terminos = version_terminos.codigo_version_terminos
					JOIN param.terminos_condiciones ON version_terminos.codigo_terminos_condiciones = terminos_condiciones.codigo_terminos_condiciones
					AND terminos_condiciones.codigo_estado = ".Estado::getState('active')."
					JOIN core.usuario_institucion ON usuario_institucion.codigo_usuario = " . $user_mentoring->codigo_usuario . "
					AND usuario_institucion.codigo_institucion = ". session('institution')->codigo_institucion ."
					WHERE terminos_condiciones.tipo_terminos ='".TerminosCondiciones::getTypeTerms('mentoring')."'
					AND usuario_institucion.codigo_usuario_institucion = aceptacion_terminos.codigo_usuario_institucion) AS terminos_aceptados")
		);

		$acceptTerms = $acceptTerms->first();

		if($acceptTerms->terminos_aceptados != true){
			return response()->json(['success' => false, 'errors' => 'El mentor no ha aceptado los Términos y Políticas de mentoria en la plataforma']);
		}


		$convocatoria_mentoria = ConvocatoriaMentoria::where('convocatoria_mentoria.codigo_convocatoria', $program->codigo_convocatoria)
													->join('aprende.asesoria_institucion', 'asesoria_institucion.codigo_asesoria', '=', 'convocatoria_mentoria.codigo_asesoria')
													->where('convocatoria_mentoria.codigo_fase', $respuesta->codigo_fase)
													->where('convocatoria_mentoria.codigo_usuario', $entity->codigo_usuario)
													->where('convocatoria_mentoria.codigo_institucion', session('institution')->codigo_institucion)
													->where('asesoria_institucion.codigo_estado', Estado::getState('active'))
													->first();
		if($convocatoria_mentoria instanceof ConvocatoriaMentoria) {
			return response()->json(['success' => false, 'errors' => 'No se puede asignar otro mentor a la fase actual']);
		}

		DB::beginTransaction();
		try {
			// Asesoria
			$asesoria = new Asesoria;
			$asesoria->descripcion_asesoria = transtp('mentoring.description');
			$asesoria->codigo_tipo_asesoria = $request['codigo_tipo_asesoria'];
			$asesoria->codigo_estado = Estado::getState('active');
			$asesoria->codigo_usuario = $user->codigo_usuario;
			$asesoria->fecha_asesoria = date('Y-m-d');
			$asesoria->hora_asesoria = date('H:i:s');
			$asesoria->seg_codigo_usuario = $user_mentoring->codigo_usuario;
			$asesoria->codigo_entidad = $entity->codigo_entidad;
			$asesoria->save();

			$questionInstitution = new AsesoriaInstitucion;
			$questionInstitution->codigo_asesoria = $asesoria->codigo_asesoria;
			$questionInstitution->codigo_institucion = session('institution')->codigo_institucion;

			//codigo_estado = activo para que el mentor no tenga que aceptar proceso de mentoria
			$questionInstitution->codigo_estado = Estado::getState('active');
			$questionInstitution->save();

			$convocatoriaMentoria = new ConvocatoriaMentoria;
			$convocatoriaMentoria->codigo_convocatoria	= $program->codigo_convocatoria;
			$convocatoriaMentoria->codigo_asesoria		= $asesoria->codigo_asesoria;
			$convocatoriaMentoria->codigo_fase			= $respuesta->codigo_fase;
			$convocatoriaMentoria->codigo_usuario		= $entity->codigo_usuario;
			$convocatoriaMentoria->seg_codigo_usuario	= $user_mentoring->codigo_usuario;
			$convocatoriaMentoria->codigo_institucion	= session('institution')->codigo_institucion;
			$convocatoriaMentoria->save();

			// Send notification
			// $data = [];
			// $data['_template'] = 'emails.mentoring.requestprocess';
			// $data['_inst'] = session('institution')->codigo_institucion;
			// $data['_subject'] = transtp('mentoring.subjectApplicationProcess');
			// $data['_to'] = $user_mentoring->correo_electronico;
			// $data['_to_name'] = ucwords(strtolower($user_mentoring->nombre_usuario));
			// $data['_url_action'] = sprintf("/%s/%s", 'mentoria/preguntas/aceptar', TuProyecto::encryptSrcParams([$asesoria->codigo_asesoria]));
			// $data['_requesting'] = ucwords(strtolower($user->nombre_usuario));
			// $data['_entity'] = ucwords(strtolower($entity->nombre_usuario));
			// $data['_desc'] = $asesoria->descripcion_asesoria;
			// SendEmail::notification($data);

			activity()
				->performedOn($entity)
				->withProperties([
					'attributes' => [
						'mentoring' => $asesoria->codigo_asesoria,
						'entity' => $entity->nombre_usuario,
						'user_asign' => $user_mentoring->nombre_usuario
					] ])
				->log('activitylog.mentoring.created');

			activity()
				->performedOn($entity)
				->causedBy($user_mentoring)
				->withProperties([
					'attributes' => [
						'mentoring' => $asesoria->codigo_asesoria,
						'user_mentor' => Auth::user()->nombre_usuario,
						'mentor_code' => Auth::user()->codigo_usuario,
						'entity' => $entity->nombre_usuario,
						'user_asign' => $user_mentoring->nombre_usuario
					] ])
				->log('activitylog.mentoring.received');

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Se asignó mentor y se creó proceso de mentoria']);
		} catch (\Exception $e) {
			DB::rollback();
			Log::error(sprintf('%s:%s', 'MentoriaMasivaController:store', $e->getMessage()));
			return response()->json(array('success' => false, 'errors' => trans('app.exception')));
		}

    }

	private function getProgramEntity(Convocatoria $program, array $filters = [])
	{
		$participants = [];
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico",
			DB::raw("initcap((CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END)) as nombre_usuario"), 'numero_registro_convocatoria','fase.codigo_fase', 'fase.nombre_fase', 'estado.nombre_estado as estado',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw("COALESCE((puntaje_registro_fase.valor_puntaje_registro_fase), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta)) as puntaje")
		);

		$query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->leftJoin('core.puntaje_registro_fase', 'puntaje_registro_fase.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');

		// User / Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

		// City / Country
		$query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query-> where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query-> where('convocatoria.codigo_estado', Estado::getState('active'));
		// States filter
		$query-> whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected')
		]);

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['nickname_usuario']) && !empty($filters['nickname_usuario'])){
			$email = trim($filters['nickname_usuario']);
			$query->whereRaw("entity.nickname_usuario ilike '%".trim($filters['nickname_usuario'])."%'");
		}
		return $query = $query->first();
	}

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMentor(Request $request)
    {

		if(!is_numeric($request->id)){
			abort(404);
		}

		$mentoring = Asesoria::find($request->id);
		if(!$mentoring instanceof Asesoria){
			throw new Exception("La asesoría no existe, verifique {$request->id}");
		}

		$user_mentoring = Usuario::find($mentoring->seg_codigo_usuario);
		if (!$user_mentoring instanceof Usuario) {
			throw new Exception("El mentor no existe, verifique {$mentoring->seg_codigo_usuario}");
		}

		$entity = Entidad::where('codigo_entidad', $mentoring->codigo_entidad)->first();
		if(!$entity instanceof Entidad){
			throw new Exception("Entidad no encontrada {$mentoring->codigo_entidad}");
		}

		$user_entity = Usuario::find($entity->codigo_usuario);
		if (!$user_entity instanceof Usuario) {
			throw new Exception("La entidad no existe, verifique {$entity->codigo_usuario}");
		}

		AsesoriaInstitucion::where('codigo_asesoria', $mentoring->codigo_asesoria)
						->where('codigo_institucion', session('institution')->codigo_institucion)
						->update([
							'codigo_estado' => Estado::getState('inactive')
						]);

		activity()
			->performedOn($entity)
			->withProperties([
				'attributes' => [
					'mentoring' => $mentoring->codigo_asesoria,
					'user_asign' => $user_mentoring->nombre_usuario,
					'entity' => $user_entity->nombre_usuario
				] ])
			->log('activitylog.mentoring.deleted');

		return response()->json(['success' => true, 'message' => 'Se ha removido correctamente el mentor !!']);
    }

}
