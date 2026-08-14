<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use Validator, Input, Auth, DB, Lang, App;

use Datatables;
use App\Models\Core\Estado;
use App\Models\Seguridad\Usuario;

class AsignacionJurado extends Model
{
    protected $table = 'core.asignacion_jurado';

    protected $primaryKey = 'codigo_asignacion_jurado';

    public $timestamps = false;

    public static function getEvaluated($evaluated) {
    	switch ($evaluated) :
		    case 'evaluated': return 1;
		    case 'notevaluated': return 2;
			default: return 'Not evaluated identified';
		endswitch;
    }

    public static function getAssignments(EvaluacionEmprendimiento $evaluation, array $filters = [])
    {
		$query = AsignacionJurado::query();
		$query->select('asignacion_jurado.codigo_asignacion_jurado','asignacion_jurado.codigo_usuario as owner', 'fecha_asignacion_jurado', 'registro_fase_usuario.codigo_fase','fase.codigo_evaluacion_emprendimiento', 'asignacion_jurado.codigo_registro_fase_usuario', 'fase.url_fase', 'fase.nombre_fase', DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'nombre_fase', DB::raw('COALESCE(puntaje_total_eval_emprendi, 0) AS puntaje_total_eval_emprendi'),
			// Project data
			DB::raw("usuario.nombre_usuario as nombre_usuario_proyecto"), DB::raw('(CASE  WHEN codigo_resultado_evaluacion IS NULL THEN false ELSE true END) as evaluation'), 'resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', 'resultado_evaluacion_emprendimiento.hora_resultado_evaluacion','person.correo_electronico', 'numero_registro_convocatoria'
		);

		// User project
		$query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
        $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.persona', 'asignacion_jurado.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

		$query->join('core.entidad', 'registro_convocatoria.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'asignacion_jurado.codigo_usuario', '=', 'person.codigo_usuario');

		// User institution
		$query->join('core.usuario_institucion', function($join) {
			$join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
				->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
        });

        // Get Results Evaluation
		$query->leftjoin('core.resultado_evaluacion_emprendimiento', function($join) use($evaluation){
			$join->on('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento')
			    ->on('resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado')
				->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=',  $evaluation->codigo_evaluacion_emprendimiento);
        });


        if( isset($filters['evaluated']) && !empty($filters['evaluated']) ) {
        	if($filters['evaluated'] == AsignacionJurado::getEvaluated('evaluated')){
           		$query->whereNotNull('codigo_resultado_evaluacion');
            }
            if($filters['evaluated'] == AsignacionJurado::getEvaluated('notevaluated')){
           		$query->whereNull('codigo_resultado_evaluacion');
            }
        }

        if( isset($filters['email']) && !empty($filters['email']) ) {
            $email = trim($filters['email']);
            $query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%$email%'))");
        }

        if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("usuario.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
 		}

        if( isset($filters['fecha_asignacion']) && !empty($filters['fecha_asignacion']) ) {
            $query->where('asignacion_jurado.fecha_asignacion_jurado', $filters['fecha_asignacion']);
        }

        if( isset($filters['fecha_evaluacion']) && !empty($filters['fecha_evaluacion']) ) {
            $query->where('resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', $filters['fecha_evaluacion']);
        }

        if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
            $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
        }

        if( (isset($filters['sortby']) && !empty($filters['sortby'])) && (isset($filters['scoring']) && !empty($filters['scoring'])) ) {
            $query->whereRaw( sprintf('%s %s %s', "puntaje_total_eval_emprendi", $filters['sortby'], $filters['scoring']) );
        }

        if( (isset($filters['sortby_2']) && !empty($filters['sortby_2'])) && (isset($filters['scoring_2']) && !empty($filters['scoring_2'])) ) {
            $query->whereRaw( sprintf('%s %s %s', "puntaje_total_eval_emprendi", $filters['sortby_2'], $filters['scoring_2']) );
        }

		$query->where('fase.codigo_evaluacion_emprendimiento', $evaluation->codigo_evaluacion_emprendimiento);
		$query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
		$query->orderBy('numero_registro_convocatoria', 'asc');

		return Datatables::of($query)
            ->make(true);
    }

    public static function getRegistersList(array $filters = [])
    {
        $query = RegistroFaseUsuario::query();
        $query->select('registro_fase_usuario.codigo_fase','fase.codigo_evaluacion_emprendimiento', 'convocatoria.nombre_convocatoria', 'fase.nombre_fase', 'usuario.nombre_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro', 'numero_registro_convocatoria'
        );

        $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
        $query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

        $query->leftJoin('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
        $query->leftJoin('seguridad.usuario as usev', 'usev.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

        $query->leftJoin('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->leftJoin('seguridad.usuario as propietario', 'entidad.seg_codigo_usuario', '=', 'propietario.codigo_usuario');

        // User institution
        $query->join('core.usuario_institucion', function($join) {
            $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
        });

        $query->join('core.convocatoria_institucion', function($join) {
            $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
                ->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
        });

        $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
        $query->whereNotNull('fase.codigo_evaluacion_emprendimiento');

        if( isset($filters['program']) && !empty($filters['program']) ) {
            $query->where('convocatoria.codigo_convocatoria', '=', $filters['program']);
        }

        if( isset($filters['phase']) && !empty($filters['phase']) ) {
            $query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
        }

        if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
            $query->whereRaw("usuario.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
        }

        if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
            $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
        }

        if (isset($filters['mail']) && !empty($filters['mail'])) {
          $query->where('usev.correo_electronico', '=', $filters['mail']);
        }

        if (isset($filters['identification_user']) && !empty($filters['identification_user'])) {
          $query->where('propietario.identificacion_usuario', '=', $filters['identification_user']);
        }

      if (isset($filters['assignment']) && !empty($filters['assignment']) ) {

        if ($filters['assignment'] == 1) {
          $query->whereNull('asignacion_jurado.codigo_asignacion_jurado');

        } elseif ($filters['assignment'] == 2) {
          $query->whereNotNull('asignacion_jurado.codigo_asignacion_jurado');
          $query->where('asignacion_jurado.codigo_estado', '!=', Estado::getState('evaluated'));

        } elseif ($filters['assignment'] == 3) {
          $query->whereNotNull('asignacion_jurado.codigo_asignacion_jurado');
          $query->where('asignacion_jurado.codigo_estado', '=', Estado::getState('evaluated'));

        }
      }

        $query->whereNotIn('registro_fase_usuario.codigo_estado', [Estado::getState('inactive'), Estado::getState('returned')]);

        return Datatables::of($query)
            ->make(true);
    }

    public static function getAssignmentJury($register)
    {
        $assignments = [];
        $query = AsignacionJurado::query();
        $query->select('asignacion_jurado.codigo_asignacion_jurado','asignacion_jurado.codigo_usuario', 'asignacion_jurado.codigo_especialidad', 'fecha_asignacion_jurado', 'hora_asignacion_jurado', 'estado.codigo_estado', 'estado.nombre_estado', 'registro_fase_usuario.codigo_fase','fase.codigo_evaluacion_emprendimiento', 'asignacion_jurado.codigo_registro_fase_usuario', 'fase.url_fase', 'fase.nombre_fase', DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"),
            // Project data
            DB::raw("usuario.nombre_usuario as nombre_usuario_proyecto"),'person.correo_electronico', 'registro_fase_usuario.visualizacion_usuario','fecha_evaluacion_jurado','lugar_evaluacion_jurado'
        );

        // User project
        $query->join('core.estado', 'asignacion_jurado.codigo_estado', '=', 'estado.codigo_estado');
        $query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
        $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
        $query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->leftJoin('core.persona', 'asignacion_jurado.codigo_usuario', '=', 'persona.codigo_usuario');
        $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

        $query->join('core.entidad', 'registro_fase_usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->join('seguridad.usuario as person', 'asignacion_jurado.codigo_usuario', '=', 'person.codigo_usuario');

        // User institution
        $query->join('core.usuario_institucion', function($join) {
            $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
        });

        $query->where('asignacion_jurado.codigo_registro_fase_usuario', $register);
        $query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);

        // Get posts
        $data = $query->skip(Input::get('offset'))->take(Input::get('limit'))->get();

        if(count($data) > 0) {
            foreach ($data as $evaluation) {
                $assignments[] = AsignacionJurado::buildAssignment($evaluation);
            }
        }

        return $assignments;

    }

    public static function getDataJury($register)
    {
        $query = AsignacionJurado::query();
        $query->select('asignacion_jurado.codigo_asignacion_jurado','asignacion_jurado.codigo_usuario', 'asignacion_jurado.codigo_especialidad', 'fecha_asignacion_jurado', 'hora_asignacion_jurado', 'estado.codigo_estado', 'estado.nombre_estado', 'registro_fase_usuario.codigo_fase','fase.codigo_evaluacion_emprendimiento', 'asignacion_jurado.codigo_registro_fase_usuario', 'fase.url_fase', 'fase.nombre_fase', DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"),
            // Project data
            DB::raw("usuario.nombre_usuario as nombre_usuario_proyecto"),'person.correo_electronico', 'registro_fase_usuario.visualizacion_usuario','fecha_evaluacion_jurado','lugar_evaluacion_jurado','registro_fase_usuario.codigo_registro_fase_usuario'
        );

        // User project
        $query->join('core.estado', 'asignacion_jurado.codigo_estado', '=', 'estado.codigo_estado');
        $query->join('core.registro_fase_usuario', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');

        $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
        $query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->leftJoin('core.persona', 'asignacion_jurado.codigo_usuario', '=', 'persona.codigo_usuario');
        $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

        $query->join('core.entidad', 'registro_fase_usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->join('seguridad.usuario as person', 'asignacion_jurado.codigo_usuario', '=', 'person.codigo_usuario');

        // User institution
        $query->join('core.usuario_institucion', function($join) {
            $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                ->where('usuario_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
        });

        $query->where('asignacion_jurado.codigo_registro_fase_usuario', $register->codigo_registro_fase_usuario);
        $query->where('asignacion_jurado.codigo_usuario', $register->codigo_usuario);
        $query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
        return  $query->first();
    }

    public static function getProjectJudge($id) {
      $query = Usuario::query();
      $query->select('usuario.nombre_usuario');
      $query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
      $query->where('registro_fase_usuario.codigo_registro_fase_usuario', '=', $id);

      return $query->first();
    }

    public static function buildConversatoryMeet($register, $assignmentJury, $nameEvaluators, $hour, $date) {
        $dataUser = Usuario::getUserByEntity($register->codigo_usuario);
        if (!$dataUser instanceof Usuario) {
            abort(404);
        }

        \Artisan::call('googleapi:makeasignationmeetconference', [
			'user' => $dataUser,
            'judge' => $assignmentJury,
            'register' => $register,
            'evaluators' => $nameEvaluators,
            'hour' => $hour,
            'date' => $date,
			'institution' => session('institution')->codigo_institucion,
        ]);
    }

    /**
    * Build Assignment.
    */
    public static function buildAssignment($assignment)
    {
        $object = new \stdClass();
        $object->codigo_registro_fase_usuario = $assignment->codigo_registro_fase_usuario;
        $object->codigo_usuario = $assignment->codigo_usuario;
        $object->codigo_fase = $assignment->codigo_fase;
		$object->codigo_especialidad = $assignment->codigo_especialidad;
		$object->codigo_asignacion_jurado = $assignment->codigo_asignacion_jurado;
		$object->codigo_evaluacion_emprendimiento = $assignment->codigo_evaluacion_emprendimiento;
		$object->nombre_usuario_proyecto = $assignment->nombre_usuario_proyecto;
		$object->nombre_usuario = $assignment->nombre_usuario;
        $object->codigo_estado = $assignment->codigo_estado;
        $object->nombre_estado = $assignment->nombre_estado;
        $object->fecha_asignacion_jurado = $assignment->fecha_asignacion_jurado;
        $object->hora_asignacion_jurado = $assignment->hora_asignacion_jurado;
        $object->fecha_evaluacion_jurado = $assignment->fecha_evaluacion_jurado;
		$object->lugar_evaluacion_jurado = $assignment->lugar_evaluacion_jurado;
		$object->evaluation = $assignment->evaluation;
		$object->fecha_resultado_evaluacion = $assignment->fecha_resultado_evaluacion;
		$object->hora_resultado_evaluacion = $assignment->hora_resultado_evaluacion;
		$object->correo_electronico = $assignment->correo_electronico;
        $object->total_evaluation = $assignment->total_evaluation;
        $object->nombre_convocatoria = $assignment->nombre_convocatoria;
        $object->nombre_fase = $assignment->nombre_fase;
		$object->visualizacion_usuario = $assignment->visualizacion_usuario;

        return $object;
    }
    /**
     * Obtiene respuestas evaluación.
     *
     * @access private
     * @static function
     * @param int $id_register_phase_usuariocode
     * @return array|object $answers|$data
     */
    public static function getAnswerEvaluation(int $id_register_phase_usuario, $id_evaluation)
	{
        $query = AsignacionJurado::query();
        $query->join('core.resultado_evaluacion_emprendimiento as re', 'asignacion_jurado.codigo_asignacion_jurado', '=', 're.codigo_asignacion_jurado');
        $query->join('core.evaluacion_item_emprendimiento as evi', 're.codigo_resultado_evaluacion','=', 'evi.codigo_resultado_evaluacion');
        $query->join('core.opc_eval_pregunta as opc', 'opc.codigo_opc_eval_pregunta', '=', 'evi.valor_evaluacion_item');
        $query->where('asignacion_jurado.codigo_registro_fase_usuario', $id_register_phase_usuario);
        $query->orderBy('evi.codigo_item_evaluacion', 'asc');
        $data = $query->get();

		switch (session('institution')->slug_institucion) {
			case 'siinova':
				if ($id_evaluation == 29) //Evaluación datos básicos.
				{
					$answers = [];
					foreach ($data as $key => $value) {
						if ($value['codigo_item_evaluacion'] == 266) {
							$answers[266] = mb_convert_case($value['nombre_opc_eval_pregunta'], MB_CASE_TITLE, "UTF-8");
						}
					}
					return $answers;
				}
			break;
		}

		return $data;

    }
}
