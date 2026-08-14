<?php


namespace App\Custom;


use App\Models\Core\Ciudad;
use App\Models\Core\Estado;
use App\Models\Core\PreguntaEncuesta;
use App\Models\Core\RegistroFaseUsuario;
use App\Models\Core\RespuestaEncuesta;
use App\Models\Core\TipoCampoPregunta;
use App\Models\Seguridad\Usuario;
use DB;

class Compensar
{
  public static function getResultExportApplicationProccessEntrepreneurship($institution, $arQuestions, $matriz, $program, $filters = [], $cities, $sectors, $levels)
  {
    $object = new \stdClass();

    // Recuperar Usuarios
    $query = RegistroFaseUsuario::query();
    $query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', 'registro_fase_usuario.codigo_estado', 'codigo_usuario_encuesta', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario", "ciuent.nombre_ciudad as nombre_ciudad_entidad", "resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi as puntaje_evaluacion", DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'genero_persona', 'nombre_categoria_proyecto', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',
      DB::raw("(CASE person.tipo_usuario WHEN " . Usuario::typeUser('person') . " THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'numero_registro_convocatoria',
      DB::raw("trim(initcap(ciudad.nombre_ciudad) || ' - ' || COALESCE(initcap(ciudad.distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad")
    );

    $query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
    $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
    $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

    $query->leftJoin('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
    $query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');

    // User / Program
    $query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
    $query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

    // User institution
    $query->join('core.usuario_institucion', function ($join) use ($institution) {
      $join->on('usuario_institucion.codigo_usuario', '=', 'entidad.codigo_usuario')
        ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
    });

    // Data entity
    $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
    $query->leftJoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');

    $query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
    $query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
    $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
    $query->join('core.convocatoria_institucion', function ($join) use ($institution) {
      $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
        ->where('convocatoria_institucion.codigo_institucion', '=', $institution->codigo_institucion)
        ->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
    });
    $query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

    $query->leftJoin('core.ciudad as ciuent', 'ciuent.codigo_ciudad', '=', 'entity.codigo_ciudad');

    // City / Country
    $query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
    $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

    $query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
    $query->where('convocatoria.codigo_estado', Estado::getState('active'));

    // States filter
    $query->whereIn('registro_fase_usuario.codigo_estado', [
      Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected')
    ]);

    if (isset($filters['email']) && !empty($filters['email'])) {
      $query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%" . $filters['email'] . "%'))");
    }

    if (isset($filters['emprendimiento']) && !empty($filters['emprendimiento'])) {
      $query->whereRaw("entity.nombre_usuario ilike '%" . $filters['emprendimiento'] . "%'");
    }

    if (isset($filters['number_register']) && !empty($filters['number_register'])) {
      $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
    }

    if (isset($filters['city']) && !empty($filters['city'])) {
      $query->where('person.codigo_ciudad', $filters['city']);
    }

    $query->where('registro_fase_usuario.codigo_estado', '!=', Estado::getState('inactive'));
    $query->where('convocatoria.codigo_estado', Estado::getState('active'));
    $query->orderBy('numero_registro_convocatoria', 'asc');
    $usuarios = $query->get();

    // Preparar Respuestas
    $query = RespuestaEncuesta::query();
    $query->select('texto_opcion_encuesta','puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
    $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
    $query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
    $query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $usuarios->pluck('codigo_usuario_encuesta')->all() );
    $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
    $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
    $allAnswers = $query->get();

    $answers = [];
    foreach ($allAnswers as $answer) {
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
          break;
      }
    }

    // Prepare headers
    $header = [];
    $header[] = 'Aplicación';
    $header[] = 'Email';
    $header[] = 'Usuario';
    $header[] = 'Genero';
    $header[] = 'Fecha postulación';
    $header[] = 'Proyecto';
    $header[] = transtp('entities.project.industry', [], $institution->slug_institucion);
    $header[] = 'Ciudad';
    $header[] = 'Puntaje';
    $header[] = 'Resultado';

    $result = RespuestaEncuesta::buildReportHeaders($arQuestions, $header, $matriz);
    $header = $result->header;
    $matriz = $result->matriz;

    //Prepare data
    $data = collect([]);
    foreach ($usuarios as $usuario)
    {
      unset($record);
      $record[] = $usuario->numero_registro_convocatoria;
      $record[] = $usuario->correo_electronico;
      $record[] = $usuario->nombre_usuario;
      $record[] =  isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
      $record[] = $usuario->fecha_registro_convocatoria.' '.$usuario->hora_registro_convocatoria;
      $record[] = $usuario->nombre_entidad;
      $record[] = $usuario->nombre_categoria_proyecto;
      $record[] = $usuario->nombre_ciudad_entidad;
      $record[] = isset($usuario->puntaje_evaluacion) ? $usuario->puntaje_evaluacion : 'Sin evaluar';
      if (isset($usuario->puntaje_evaluacion)) {
        if ($usuario->codigo_estado === Estado::getState('rejected')) {
          $record[] = 'No paso';
        } else if ($usuario->codigo_estado === Estado::getState('active')) {
          if ($usuario->puntaje_evaluacion < 6.5) {
            $record[] = 'No paso';
          } else {
            $record[] = 'Paso';
          }
        }
      } else if (!isset($usuario->puntaje_evaluacion)) {
        $record[] = 'Sin evaluar';
      }

      // Questions Survey
      foreach ($arQuestions as $question)
      {
        if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
        {
          switch ($question->tipo_pregunta_encuesta)
          {
            case PreguntaEncuesta::getType('open'):

              $respuesta = '';
              $key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
              $answer = isset($answers[$key]) ? $answers[$key] : '';
              if($answer instanceof RespuestaEncuesta){
                $respuesta = $answer->texto_respuesta_encuesta;
              }
              // Tipo ciudad
              if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')) {
                if($answer instanceof RespuestaEncuesta) {
                  $key = "{$answer->texto_respuesta_encuesta}";
                  $city = isset($cities[$key]) ? $cities[$key] : '';
                  if($city instanceof Ciudad){
                    $respuesta = $city->distrito_ciudad;
                    $record[] = $city->nombre_ciudad;
                  }
                }else{
                  $record[] = '';
                }
              }
              // Tipo Sector interes
              if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
                $respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';
              }
              // Tipo nivel educación
              if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')) {
                $respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
              }
              $record[] = $respuesta;
              break;

            case PreguntaEncuesta::getType('single'):

              $respuesta = '';
              $key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
              $answer = isset($answers[$key]) ? $answers[$key] : '';
              if($answer instanceof RespuestaEncuesta){
                $respuesta = $answer->texto_opcion_encuesta;
              }
              $record[] = $respuesta;
              if($question->puntaje_pregunta_encuesta) {
                $puntajeOpcion = "";
                if($answer instanceof RespuestaEncuesta) {
                  $puntajeOpcion = $answer->puntaje_opcion_encuesta;
                }
                $record[] = $puntajeOpcion;
              }

              break;

            case PreguntaEncuesta::getType('multiple'):
              $respuesta = '';
              $key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
              $answerop = isset($answers[$key]) ? $answers[$key] : [];
              foreach ($answerop as $option) {
                if($option instanceof RespuestaEncuesta){
                  $respuesta.= "{$option->texto_opcion_encuesta} ";
                }
              }
              $record[] = $respuesta;
              break;

            case PreguntaEncuesta::getType('upload'):
              $respuesta = '';
              $key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
              $answer = isset($answers[$key]) ? $answers[$key] : '';
              if($answer instanceof RespuestaEncuesta){
                $respuesta = $answer->texto_respuesta_encuesta;
              }
              $record[] = $respuesta;
              break;

            case PreguntaEncuesta::getType('matriz'):

              if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
                foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
                  foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
                    $respuesta = '';
                    $key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
                    $answer = isset($answers[$key]) ? $answers[$key] : '';
                    if($answer instanceof RespuestaEncuesta){
                      $respuesta = $answer->texto_respuesta_encuesta;
                    }
                    $record[] = $respuesta;
                  }
                }
              }
              break;
          }
        }
      }

      $data[] = $record;
    }

    $object->headers = $header;
    $object->data = $data;

    return $object;
  }
}
