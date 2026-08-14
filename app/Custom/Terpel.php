<?php 

namespace App\Custom;

use DB, Datatables, TuProyecto;
use App\Models\Core\Ciudad;
use App\Models\Core\Convocatoria;
use App\Models\Core\Estado;
use App\Models\Seguridad\Usuario;
use App\Models\Core\PreguntaEncuesta;
use App\Models\Core\RespuestaEncuesta;
use App\Models\Core\TipoCampoPregunta;
use App\Models\Core\RegistroFaseUsuario;
use App\Models\Param\TerminosCondiciones;
use App\Models\Core\ResultadoEvaluation;
use App\Models\Core\Fase;

class Terpel {
    public static function getExportTerpel($institution, $program, $filters, $arQuestions, $matriz, $cities, $sectors, $levels) {
        $object = new \stdClass();

         // Recuperar Usuarios
         $query = RegistroFaseUsuario::query();
         $query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', 'codigo_usuario_encuesta', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'genero_persona', 'nombre_categoria_proyecto', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',
          DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'numero_registro_convocatoria',
          DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
          'aceptacion_terminos.fecha_aceptacion_terminos', 'aceptacion_terminos.direccion_ip', 'registro_convocatoria.fecha_registro_convocatoria'
         );

         $query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
         $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
         $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

         // User / Program
         $query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
         $query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
        
         // User Person User
         $query->leftJoin('seguridad.usuario as user_person', 'user_person.codigo_usuario', '=', 'entidad.seg_codigo_usuario');

         //User institution Person
         $query->leftJoin('core.usuario_institucion as user_institution_person', function($join) use($institution) {
            $join->on('user_institution_person.codigo_usuario', '=', 'user_person.codigo_usuario')
            ->where('user_institution_person.codigo_institucion', '=', $institution->codigo_institucion);
         });

         // User institution
         $query->join('core.usuario_institucion', function($join) use($institution) {
          $join->on('usuario_institucion.codigo_usuario', '=', 'entidad.codigo_usuario')
              ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
         });

         $query->leftJoin('param.terminos_condiciones', function($join) {
            $join->on('terminos_condiciones.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
                ->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('privacy'));
         });

         $query->leftJoin('param.version_terminos', 'version_terminos.codigo_terminos_condiciones', '=', 'terminos_condiciones.codigo_terminos_condiciones');

         $query->leftJoin('seguridad.aceptacion_terminos', function($join) {
            $join->on('aceptacion_terminos.codigo_version_terminos', '=', 'version_terminos.codigo_version_terminos')
                ->on('aceptacion_terminos.codigo_usuario_institucion', '=', 'user_institution_person.codigo_usuario_institucion');
         });

         // Data entity
         $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
         $query->leftJoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');

         $query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
         $query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
         $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
         $query->join('core.convocatoria_institucion', function($join) use($institution) {
             $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
                 ->where('convocatoria_institucion.codigo_institucion', '=', $institution->codigo_institucion)
                 ->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
         });
         $query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

         // City / Country
         $query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
         $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

         $query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
         $query->where('convocatoria.codigo_estado', Estado::getState('active'));

         // States filter
         $query->whereIn('registro_fase_usuario.codigo_estado', [
             Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected')
         ]);

         if( isset($filters['email']) && !empty($filters['email']) ) {
             $query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%".$filters['email']."%'))");
         }

         if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
             $query->whereRaw("entity.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
         }

         if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
             $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
         }

         if( isset($filters['city']) && !empty($filters['city']) ) {
             $query->where('person.codigo_ciudad', $filters['city']);
         }

         $query->where('registro_fase_usuario.codigo_estado', Estado::getState('active'));
         $query->where('convocatoria.codigo_estado', Estado::getState('active'));
         $query->orderBy('numero_registro_convocatoria','asc');
         $usuarios = $query->get();

         // Prepare headers
         $header = [];
         $header[] = 'Aplicación';
         $header[] = 'Email';
         $header[] = 'Usuario';
         $header[] = 'Genero';
         $header[] = 'Fecha postulación';
         $header[] = 'Fecha registro';
         $header[] = 'Proyecto';
         $header[] = 'Fecha aceptación terminos';
         $header[] = 'Direccion ip';
         $header[] = 'Fecha aceptación convocatoria';
         $header[] = transtp('entities.project.industry', [], $institution->slug_institucion);

         $result = RespuestaEncuesta::buildReportHeaders($arQuestions, $header, $matriz);
         $header = $result->header;
         $matriz = $result->matriz;

         // Preparar answers
         $answers = RespuestaEncuesta::buildReportAnswers($usuarios->pluck('codigo_usuario_encuesta')->all());

         //Prepare data
         $data = collect([]);
         foreach ($usuarios as $register)
         {
             unset($record);
             $record[] = $register->numero_registro_convocatoria;
             $record[] = $register->correo_electronico;
             $record[] = $register->nombre_usuario;
             $record[] =  isset(Usuario::$gender[$register->genero_persona]) ? Usuario::$gender[$register->genero_persona]: '';
             $record[] = $register->fecha_registro_convocatoria.' '.$register->hora_registro_convocatoria;
             $record[] = $register->fecha_registro_fase_usuario.' '.$register->hora_registro_fase_usuario;
             $record[] = $register->nombre_entidad;
             $record[] = $register->fecha_aceptacion_terminos;
             $record[] = $register->direccion_ip;
             $record[] = $register->fecha_registro_convocatoria;
             $record[] = $register->nombre_categoria_proyecto;

             // Prepare survey answers
             $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, $sectors, $levels, $register->codigo_usuario_encuesta, $record);

             $data[] = $record;
         }

         $object->headers = $header;
         $object->data = $data;
     
         return $object;
    }

    public static function getParticipantsTerpel20(Convocatoria $program, array $filters = [])
	{
		$participants = [];
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico",
			DB::raw("initcap((CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END)) as nombre_usuario"), 'numero_registro_convocatoria','fase.codigo_fase', 'fase.nombre_fase as fase',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw("COALESCE(puntaje_registro_fase.valor_puntaje_registro_fase, 0) as puntaje")
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

		if(isset($filters['emprendimiento']) && !empty($filters['emprendimiento'])) {
			$query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if(isset($filters['city']) && !empty($filters['city'])){
			$query->where('person.codigo_ciudad', $filters['city']);
        }
        
        if( isset($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->whereIn('registro_fase_usuario.codigo_fase', explode(',',$filters['phase']));
		}

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
		->make(true);
	}

    public static function calculateScore__Terpel_2020($register) {
        $query = ResultadoEvaluation::query();
        $query->select(DB::raw('SUM(puntaje_resul_eval_empren) as total'));
        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
        $query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);

        return $query->first();
    }
}