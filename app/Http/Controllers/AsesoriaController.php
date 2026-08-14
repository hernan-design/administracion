<?php

namespace App\Http\Controllers;

use App\Models\Core\Persona;
use App\Models\Seguridad\Rol;
use App\Models\Seguridad\RolUsuario;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use SendEmail, TuProyecto, Log, DB, Auth, Datatables;

use App\Models\Core\Asesoria,
	App\Models\Core\TemaMentoria,
	App\Models\Core\Estado ,
	App\Models\Seguridad\Usuario,
	App\Models\Aprende\AsesoriaInstitucion,
	App\Models\Core\Entidad,
	App\Models\Aprende\EvaluacionAsesoria,
	App\Models\Aprende\Mentoria;

use App\Models\Bita\LogActivity;
use App\Models\Param\TerminosCondiciones;
use App\Models\Seguridad\AceptacionTerminos;


class AsesoriaController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,mentoria');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			      if($request->has('entity') && !empty($request->entity)){
                return response()->json( Asesoria::mentoring( $request->all() ) );
              }

              return Asesoria::getMentoring( $request->all() );
		      }

      $mentorThemes = TemaMentoria::getThemesList();
          $states = Estado::getStates([], [Estado::getState('active'), Estado::getState('rejected'), Estado::getState('pending'), Estado::getState('inactive'), Estado::getState('closed')], $filter = null);
      return view('mentor.index', compact('mentorThemes', 'states'));
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
		$user_mentoring = Usuario::getUserMail(['email' => $request->correo_electronico]);
		if(!$user_mentoring instanceof Usuario) {
			abort(404);
		}

    $codigo_usuario = $user_mentoring->codigo_usuario;

     //Se busca en la tabla seguridad.rol_usuario po rel user_id, codigo_institucion y role_id igual a mentor que el codigo estado sea distincto de inactivo
    $mentoringRole = RolUsuario::where('user_id', $codigo_usuario)
      ->where('codigo_institucion', session('institution')->codigo_institucion)
      ->where('role_id', Rol::getRol('mentor'))
      ->where('codigo_estado', '!=', Estado::getState('inactive'))
      ->first();

    if(!$mentoringRole instanceof RolUsuario) {
      return response()->json(['success' => false, 'errors' => 'El usuario no se encuentra activo como mentor en la institución.']);
    }


		$entity = Entidad::getEntityUrl($request->entity);
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

		if($request->ajax()){
			$asesoria = new Asesoria;
			DB::beginTransaction();
			try {
				// Asesoria
				$asesoria->descripcion_asesoria = transtp('mentoring.description');
				$asesoria->codigo_tipo_asesoria = $request->codigo_tipo_asesoria;
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
				$questionInstitution->codigo_estado = Estado::getState('pending');
				$questionInstitution->save();
				// Send notification
				$data = [];
				$data['_template'] = 'emails.mentoring.requestprocess';
				$data['_inst'] = session('institution')->codigo_institucion;
				$data['_subject'] = transtp('mentoring.subjectApplicationProcess');
				$data['_to'] = $user_mentoring->correo_electronico;
				$data['_to_name'] = ucwords(strtolower($user_mentoring->nombre_usuario));
				$data['_url_action'] = sprintf("/%s/%s", 'mentoria/preguntas/aceptar', TuProyecto::encryptSrcParams([$asesoria->codigo_asesoria]));
				$data['_requesting'] = ucwords(strtolower($user->nombre_usuario));
				$data['_entity'] = ucwords(strtolower($entity->nombre_usuario));
				$data['_desc'] = $asesoria->descripcion_asesoria;
				SendEmail::notification($data);

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

				$mentoring = new \stdClass();
				$mentoring->ciudad_mentor = '';
				$mentoring->codigo_asesoria = $asesoria->codigo_asesoria;
				$mentoring->codigo_mentor = $user_mentoring->codigo_usuario;
				$mentoring->imagen_mentor = $user_mentoring->imagen_usuario;
				$mentoring->nombre_mentor = $user_mentoring->nombre_usuario;
				$mentoring->correo_mentor = $user_mentoring->correo_electronico;
				$mentoring->nickname_mentor = $user_mentoring->nickname_usuario;
				$mentoring->codigo_estado = $questionInstitution->codigo_estado;
				$mentoring->success = true;
				$mentoring->message = 'Se ha creado la asesoría correctamente.';

				// Commit Transaction
				DB::commit();
				return response()->json($mentoring);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'AsesoriaController:store', $e->getMessage()));
				return response()->json(array('success' => false, 'errors' => trans('app.exception')));
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
		if(!is_numeric($id)){
			abort(404);
		}

		$mentor = Asesoria::getMentor($id);
		if(!$mentor instanceof Asesoria){
			abort(404,'menor no encontrado');
		}

    	$attached = $mentor->attachedAdvice()->get();
		$evaluationAdviser = EvaluacionAsesoria::evaluacionAsesoria(['codigo_asesoria' => $mentor->codigo_asesoria, 'type' => EvaluacionAsesoria::typeEvaluation('adviser')]);
		$evaluationEntrepreneur = EvaluacionAsesoria::evaluacionAsesoria(['codigo_asesoria' => $mentor->codigo_asesoria, 'type' => EvaluacionAsesoria::typeEvaluation('entrepreneur')]);

        return view('mentor.detail', compact('mentor', 'attached', 'evaluationAdviser', 'evaluationEntrepreneur'));
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

		DB::beginTransaction();
		try {

			AsesoriaInstitucion::where('codigo_asesoria', $id)
            					->update(['codigo_estado' => $request->codigo_estado]);

			// Commit Transaction
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Mentoría activada!']);

		}catch(\Exception $e){

			DB::rollback();
			Log::error(sprintf('%s:%s', 'AsesoriaController:update', $e->getMessage()));
			return response()->json(['success' => false, 'errors' => 'Error al intentar activar mentoría']);

		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		if(!is_numeric($id)){
			abort(404);
		}

		$mentoring = Asesoria::find($id);
		if(!$mentoring instanceof Asesoria){
			throw new Exception("La asesoría no existe, verifique {$id}");
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

	public function getSessionMentorings(Request $request)
	{//dd('sesion ' ,$request->ajax());

		if($request->ajax()){
			//if($request->has('entity') && !empty($request->entity)){
			$sesiones = Mentoria::getSessions($request->codigo_asesoria);

		    return Datatables::of($sesiones)->make(true);

			//}

		}
	}
}
