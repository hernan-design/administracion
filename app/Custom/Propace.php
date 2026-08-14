<?php

namespace Custom;

use DB, Lang, App, Log, SendEmail, Response, Auth, TuProyecto, AWS, Datatables;

use App\Models\Core\Fase, 
    App\Models\Seguridad\Usuario, 
    App\Models\Seguridad\Rol, 
    App\Models\Core\RegistroFaseUsuario, 
    App\Models\Core\Estado,
    App\Models\Core\Convocatoria,
    App\Models\Core\RespuestaEncuesta,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Pago,
    App\Models\Core\TipoRecursoFase,
    App\Models\Aprende\InscripcionCurso;

class Propace
{
    public static function registerToPhase(Fase $phase, Usuario $dataUser, RegistroFaseUsuario $registerPhase, $request)
    {
        
        switch ($phase->codigo_fase) {
            case Fase::getIdPhase('propace_course_register'):
                if($request->turn || $request->data['turn']){                    
                    $courses = RespuestaEncuesta::select('puntaje_respuesta_encuesta')
                        ->where('codigo_usuario_encuesta', $registerPhase->codigo_usuario_encuesta)
                        ->where('codigo_estado', Estado::getState('active'))->get();

                    $userInstitution = UsuarioInstitucion::select('codigo_usuario_institucion')
                        ->where('codigo_usuario', $dataUser->codigo_usuario)->first();

                    foreach($courses as $item) {

                        $existPayment = Pago::select('codigo_pago')
                            ->where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                            ->where('codigo_recurso', (int)$item->puntaje_respuesta_encuesta)
                            ->where('codigo_estado', Estado::getState('active'))->first();

                        if (!$existPayment instanceof Pago) {
                            $payment = new Pago();
                            $payment->codigo_institucion = session('institution')->codigo_institucion;
                            $payment->codigo_estado = Estado::getState('active');
                            $payment->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                            $payment->fecha_pago = date('Y-m-d');
                            $payment->tipo_recurso = TipoRecursoFase::getTypeResource('course');
                            $payment->codigo_recurso = $item->puntaje_respuesta_encuesta;
                            $payment->save();
                        }
                    }

                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

                    //Insert Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = Fase::getIdPhase('propace_course_second');
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

                $registerPhase->save();
            break;

            case Fase::getIdPhase('propace_course_two_register'):

                if($request->turn || $request->data['turn']){
                    $courses = RespuestaEncuesta::select('puntaje_respuesta_encuesta', 'codigo_opcion_encuesta')
                        ->where('codigo_usuario_encuesta', $registerPhase->codigo_usuario_encuesta)
                        ->where('codigo_estado', Estado::getState('active'))->get();

                    $defaultCourses = [306, 266, 264, 319, 307, 263, 265, 403];

                    $userInstitution = UsuarioInstitucion::select('codigo_usuario_institucion')
                        ->where('codigo_usuario', $dataUser->codigo_usuario)->where('codigo_institucion', session('institution')->codigo_institucion)->first();

                    foreach($courses as $item) {

                        if ($item->codigo_opcion_encuesta === 8724) {
                            foreach($defaultCourses as $course) {
                                $existPayment = Pago::select('codigo_pago')
                                ->where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                ->where('codigo_recurso', (int)$course)
                                ->where('codigo_estado', Estado::getState('active'))->first();

                                if (!$existPayment instanceof Pago) {
                                    $payment = new Pago();
                                    $payment->codigo_institucion = session('institution')->codigo_institucion;
                                    $payment->codigo_estado = Estado::getState('active');
                                    $payment->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                                    $payment->fecha_pago = date('Y-m-d');
                                    $payment->tipo_recurso = TipoRecursoFase::getTypeResource('course');
                                    $payment->codigo_recurso = $course;
                                    $payment->save();
                                }
                            }
                        } else {
                            if($item->puntaje_respuesta_encuesta !=""){
                                $existPayment = Pago::select('codigo_pago')
                                    ->where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                    ->where('codigo_recurso', (int)$item->puntaje_respuesta_encuesta)
                                    ->where('codigo_estado', Estado::getState('active'))->first();

                                if (!$existPayment instanceof Pago) {

                                        $payment = new Pago();
                                        $payment->codigo_institucion = session('institution')->codigo_institucion;
                                        $payment->codigo_estado = Estado::getState('active');
                                        $payment->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                                        $payment->fecha_pago = date('Y-m-d');
                                        $payment->tipo_recurso = TipoRecursoFase::getTypeResource('course');
                                        $payment->codigo_recurso = $item->puntaje_respuesta_encuesta;
                                        $payment->save();
                                }
                            }
                        }
                    }

                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;

                    //Insert Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = Fase::getIdPhase('propace_course_two_payment');
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

                $registerPhase->save();
            break;

            case Fase::getIdPhase('propace_test_zarit'):
                if($request->turn || $request->data['turn']){
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = Fase::getIdPhase('propace_formacion');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();
                    // Sending Email
                    $data = [];
                    $data['_template'] = 'emails.program.propace.notification_propace';
                    $data['_subject'] = 'Programa de formación a cuidadores';
                    // Data Template
                    $data['comment'] = Lang::get('program.mail.body.propaceCambioFase');
                    $data['_to'] = $dataUser->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($dataUser->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
                    // $data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
                    SendEmail::notification($data);
                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case Fase::getIdPhase('propace_formacion'):
                if($request->turn || $request->data['turn']){

                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }
                $registerPhase->save();
            break;	
            
            case Fase::getIdPhase('registro_pfc_abierto'):
                if($request->turn || $request->data['turn']){
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = Fase::getIdPhase('registro_pfc_formacion');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();
 
                    //inscripcion a cursos
                    $courses = [263,264,265,266];

                    $userInstitution = UsuarioInstitucion::select('codigo_usuario_institucion')
                                                        ->where('codigo_usuario', $dataUser->codigo_usuario)
                                                        ->where('codigo_institucion', session('institution')->codigo_institucion)
                                                        ->first();


                    foreach ($courses as $course) {

                        InscripcionCurso::create([
                            'codigo_usuario'          => $registerPhase->codigo_usuario,
                            'codigo_curso'            => $course,
                            'codigo_estado'           => Estado::getState('active'),
                            'fecha_inscripcion_curso' => date('Y-m-d'),
                            'hora_inscripcion_curso'  => date('H:i:s'),
                            'porcentaje_progreso'     => 0,
                            'codigo_institucion'      => session('institution')->codigo_institucion,
                            'fecha_envio_certificado' => null 
                        ]);

                        $existPayment = Pago::select('codigo_pago')
                                        ->where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                        ->where('codigo_recurso', (int)$course)
                                        ->where('codigo_estado', Estado::getState('active'))
                                        ->first();

                        if (!$existPayment instanceof Pago) {
                            $payment = new Pago();
                            $payment->codigo_institucion = session('institution')->codigo_institucion;
                            $payment->codigo_estado = Estado::getState('active');
                            $payment->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                            $payment->fecha_pago = date('Y-m-d');
                            $payment->tipo_recurso = TipoRecursoFase::getTypeResource('course');
                            $payment->codigo_recurso = $course;
                            $payment->save();
                        }
                    }


                    // Sending Email
                    $data = [];
                    $data['_template'] = 'emails.program.propace.notification_propace';
                    $data['_subject'] = 'Programa de formación a cuidadores';
                    // Data Template
                    $data['comment'] = Lang::get('program.mail.body.propaceCambioFase');
                    $data['_to'] = $dataUser->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($dataUser->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
                    // $data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
                    SendEmail::notification($data);
                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case Fase::getIdPhase('registro_pfc_abierta_particulares'):
                if($request->turn || $request->data['turn']){
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = Fase::getIdPhase('formacion_pfc_abierta_particulares');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();
 
                    //inscripcion a cursos
                    $courses = [263,264,265,266];

                    $userInstitution = UsuarioInstitucion::select('codigo_usuario_institucion')
                                                        ->where('codigo_usuario', $dataUser->codigo_usuario)
                                                        ->where('codigo_institucion', session('institution')->codigo_institucion)
                                                        ->first();


                    foreach ($courses as $course) {

                        InscripcionCurso::create([
                            'codigo_usuario'          => $registerPhase->codigo_usuario,
                            'codigo_curso'            => $course,
                            'codigo_estado'           => Estado::getState('active'),
                            'fecha_inscripcion_curso' => date('Y-m-d'),
                            'hora_inscripcion_curso'  => date('H:i:s'),
                            'porcentaje_progreso'     => 0,
                            'codigo_institucion'      => session('institution')->codigo_institucion,
                            'fecha_envio_certificado' => null 
                        ]);

                        $existPayment = Pago::select('codigo_pago')
                                        ->where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                        ->where('codigo_recurso', (int)$course)
                                        ->where('codigo_estado', Estado::getState('active'))
                                        ->first();

                        if (!$existPayment instanceof Pago) {
                            $payment = new Pago();
                            $payment->codigo_institucion = session('institution')->codigo_institucion;
                            $payment->codigo_estado = Estado::getState('active');
                            $payment->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                            $payment->fecha_pago = date('Y-m-d');
                            $payment->tipo_recurso = TipoRecursoFase::getTypeResource('course');
                            $payment->codigo_recurso = $course;
                            $payment->save();
                        }
                    }


                    // Sending Email
                    $data = [];
                    $data['_template'] = 'emails.program.propace.notification_propace';
                    $data['_subject'] = 'Programa de formación a cuidadores';
                    // Data Template
                    $data['comment'] = Lang::get('program.mail.body.propaceCambioFase');
                    $data['_to'] = $dataUser->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($dataUser->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
                    // $data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
                    SendEmail::notification($data);
                }else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            case Fase::getIdPhase('registro_pfc_3_0'):
                if($request->turn || $request->data['turn']){
                    $registerPhase->codigo_estado = Estado::getState('finalized');
                    $registerPhase->visualizacion_usuario = true;
                    $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
                    $registerPhase->notificacion_registro_fase_usuario = true;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                    // Insert Register Phase User
                    $register = new RegistroFaseUsuario;
                    $register->codigo_fase = Fase::getIdPhase('formacion_pfc_3_0');
                    $register->codigo_usuario = $registerPhase->codigo_usuario;
                    $register->fecha_registro_fase_usuario = date('Y-m-d');
                    $register->hora_registro_fase_usuario = date('H:i:s');
                    $register->codigo_estado = Estado::getState('active');
                    $register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                    $register->visualizacion_usuario = false;
                    $register->save();
 
                    //inscripcion a cursos
                    $courses = [263,264,265,266];

                    $userInstitution = UsuarioInstitucion::select('codigo_usuario_institucion')
                                                        ->where('codigo_usuario', $dataUser->codigo_usuario)
                                                        ->where('codigo_institucion', session('institution')->codigo_institucion)
                                                        ->first();


                    foreach ($courses as $course) {

                        InscripcionCurso::create([
                            'codigo_usuario'          => $registerPhase->codigo_usuario,
                            'codigo_curso'            => $course,
                            'codigo_estado'           => Estado::getState('active'),
                            'fecha_inscripcion_curso' => date('Y-m-d'),
                            'hora_inscripcion_curso'  => date('H:i:s'),
                            'porcentaje_progreso'     => 0,
                            'codigo_institucion'      => session('institution')->codigo_institucion,
                            'fecha_envio_certificado' => null 
                        ]);

                        $existPayment = Pago::select('codigo_pago')
                                        ->where('codigo_usuario_institucion', $userInstitution->codigo_usuario_institucion)
                                        ->where('codigo_recurso', (int)$course)
                                        ->where('codigo_estado', Estado::getState('active'))
                                        ->first();

                        if (!$existPayment instanceof Pago) {
                            $payment = new Pago();
                            $payment->codigo_institucion = session('institution')->codigo_institucion;
                            $payment->codigo_estado = Estado::getState('active');
                            $payment->codigo_usuario_institucion = $userInstitution->codigo_usuario_institucion;
                            $payment->fecha_pago = date('Y-m-d');
                            $payment->tipo_recurso = TipoRecursoFase::getTypeResource('course');
                            $payment->codigo_recurso = $course;
                            $payment->save();
                        }
                    }


                    // Sending Email
                    $data = [];
                    $data['_template'] = 'emails.program.propace.notification_propace';
                    $data['_subject'] = 'Programa de formación a cuidadores';
                    // Data Template
                    $data['comment'] = Lang::get('program.mail.body.propaceCambioFase');
                    $data['_to'] = $dataUser->correo_electronico;
                    $data['_to_name'] = ucwords(strtolower($dataUser->nombre_usuario));
                    $data['_inst'] = session('institution')->codigo_institucion;
                    $data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto',$dataUser->nickname_usuario,'convocatorias');
                    // $data['_attach'] = [$result['ObjectURL'] => 'Empodera.pdf'];
                    SendEmail::notification($data);
                } else {
                    $registerPhase->visualizacion_usuario = false;
                    $registerPhase->notificacion_registro_fase_usuario = false;
                    $registerPhase->seg_codigo_usuario = Auth::user()->codigo_usuario;
                }

                $registerPhase->save();

            break;
            default:
                if($request->turn || $request->data['turn']){
                    return 'No hay mas fases';
                }
        
        }
    }
    
    public static function getProgramPFC(Convocatoria $program, array $filters = [])
	{
		$query = RegistroFaseUsuario::query();
		$query->select(
			"fase.codigo_evaluacion_emprendimiento",
			"observaciones_registro_fase_usuario",
			"registro_fase_usuario.codigo_registro_fase_usuario",
			"registro_fase_usuario.codigo_usuario",
			"fecha_registro_fase_usuario",
			"hora_registro_fase_usuario",
			"registro_fase_usuario.codigo_fase",
			"registro_fase_usuario.visualizacion_usuario",
			"convocatoria.codigo_convocatoria",
			"codigo_orientacion_convocatoria",
			"usuario.correo_electronico as correo_electronico",
			'url_encuesta',
			'encuesta.codigo_encuesta',
			'nombre_fase',
			'numero_registro_convocatoria',
			'registro_convocatoria.codigo_usuario_encuesta',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("initcap((CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END)) as nombre_usuario")
		);

		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');

		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->leftjoin('core.pais_institucion', function($join) {
			$join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
				->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
		$query->whereNotNull('distrito_ciudad');
		$query->whereRaw("distrito_ciudad != ''");

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if(isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if(isset($filters['city']) && !empty($filters['city'])){
			$query->where('usuario.codigo_ciudad', $filters['city']);
		}

		// States filter
		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active')
		]);

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('codigo_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}
}
