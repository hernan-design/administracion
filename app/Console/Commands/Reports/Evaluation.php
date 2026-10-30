<?php

namespace App\Console\Commands\Reports;
// Se añade tiempo maximo de ejecucion en consultas SQL
ini_set('max_execution_time', 3600);
set_time_limit(3600);
ini_set('memory_limit', '1024M');

use App\Models\Core\AsignacionJurado;
use App\Models\Core\Estado;
use App\Models\Core\EvaluacionEmprendimiento;
use App\Models\Param\Institucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Console\Command;
use App\Exports\ReportsExport;
use DB, Log, Excel, Storage, SendEmail;

class Evaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluations:monitoreo {user}{institution}{filters}{--startAt=} {--endAt=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de evaluaciones dependiendo de encuesta';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $evaluation = $this->argument('filters');
      $startAt = $this->option('startAt');
      $endAt = $this->option('endAt');

      try {
        //Institution
        $institution = $this->argument('institution');

        // Valid user
        $user = Usuario::find($this->argument('user'));
        if (!$user instanceof Usuario) {
          throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
        }

        $query = AsignacionJurado::query();
        $query->select('asignacion_jurado.codigo_asignacion_jurado', 'asignacion_jurado.codigo_usuario as owner', 'fecha_asignacion_jurado', 'registro_fase_usuario.codigo_fase', 'fase.codigo_evaluacion_emprendimiento', 'asignacion_jurado.codigo_registro_fase_usuario', 'fase.url_fase', 'fase.nombre_fase', DB::raw("(CASE person.tipo_usuario WHEN " . Usuario::typeUser('person') . " THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'nombre_fase', DB::raw('COALESCE(puntaje_total_eval_emprendi, 0) AS puntaje_total_eval_emprendi'),
          // Project data
          DB::raw("usuario.nombre_usuario as nombre_usuario_proyecto"), DB::raw('(CASE  WHEN codigo_resultado_evaluacion IS NULL THEN false ELSE true END) as evaluation'), 'resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', 'resultado_evaluacion_emprendimiento.hora_resultado_evaluacion', 'person.correo_electronico', 'numero_registro_convocatoria', 'empresa.nombre_usuario as usuario_empresa', 'ciudad.nombre_ciudad  as ciudad_empre', 'ciudad.codigo_ciudad as cod_ciudad_empre', 'codigo_resultado_evaluacion as codigo_resultado'
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
        $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');

        $query->join('seguridad.usuario as empresa', 'empresa.codigo_usuario', '=', 'entidad.seg_codigo_usuario');

        // User institution
        $query->join('core.usuario_institucion', function ($join) use($institution) {
          $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
            ->where('usuario_institucion.codigo_institucion', '=', $institution)
            ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
        });

        // Get Results Evaluation
        $query->leftjoin('core.resultado_evaluacion_emprendimiento', function ($join) use ($evaluation) {
          $join->on('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento')
            ->on('resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado')
            ->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', $evaluation);
        });

        $query->where('fase.codigo_evaluacion_emprendimiento', $evaluation);
        $query->whereIn('asignacion_jurado.codigo_estado', [Estado::getState('active'), Estado::getState('evaluated')]);
        if($startAt != "" && $endAt != ""){
          $query->whereBetween('resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', [$startAt, $endAt]);
        }
        $query->orderBy('numero_registro_convocatoria', 'asc');

        $data = $query->get();

        $questions = $this->getQuestionsEvaluations($institution);
        $groups = $this->getGroupsEvaluations($institution);
        $answers = EvaluacionEmprendimiento::getAnswersEvaluation($evaluation);
        $evaluation_commentary = EvaluacionEmprendimiento::getCommentaryEvaluation($evaluation);
        $otherQuestions = EvaluacionEmprendimiento::getOtherQuestions($evaluation);

//        dd($answers);

        $header = [];
        $header[] = 'Número de aplicación';
        $header[] = 'Emprendimiento';
        $header[] = 'Usuario del emprendimiento';
        $header[] = 'Ciudad del emprendimiento';
        $header[] = 'Evaluador';
        $header[] = 'Correo del evaluador';
        $header[] = 'Fecha de la evaluación';

        // Build headers
        $result_text = '';
        foreach ($questions as $item_question) {
          if (isset($item_question->codigo_grupo_item_eval) && !empty($item_question->codigo_grupo_item_eval)) {
            foreach ($groups as $item_groups) {
              if ($item_groups->codigo_grupo_item_eval == $item_question->codigo_grupo_item_eval) {
                $header[] = strip_tags($item_question->nombre_item_evaluacion);
              }
            }
          } else {
            $header[] = strip_tags($item_question->nombre_item_evaluacion);
          }
        }

        foreach ($otherQuestions as $other) {
          $header[] = $other->nombre_item_evaluacion;
        }

        $header[] = 'Comentario Evaluación';
        $header[] = 'Puntaje final de la evaluación';
        $header[] = 'Resultado';

        // Indexar respuestas y comentarios por resultado para optimizar procesamiento
        $answersByResult = [];
        foreach ($answers as $ans) {
          $answersByResult[$ans->codigo_resultado_evaluacion][] = $ans;
        }

        $commentaryByResult = [];
        foreach ($evaluation_commentary as $comm) {
          $commentaryByResult[$comm->codigo_resultado_evaluacion] = $comm->commentary;
        }

        $dataReport = collect([]);
        foreach ($data as $value) {
          unset($record);
          $record[] = $value->numero_registro_convocatoria;
          $record[] = $value->nombre_usuario_proyecto;
          $record[] = $value->usuario_empresa;
          $record[] = $value->ciudad_empre;
          $record[] = $value->nombre_usuario;
          $record[] = $value->correo_electronico;
          $record[] = $value->fecha_resultado_evaluacion;

          $result = 0;
          if (isset($answersByResult[$value->codigo_resultado])) {
            foreach ($answersByResult[$value->codigo_resultado] as $item_answers) {
              foreach ($questions as $item_questions) {
                if ($item_questions->codigo_item_evaluacion == $item_answers->codigo_item_evaluacion) {
                  if ($item_questions->codigo_grupo_item_eval == $item_answers->codigo_grupo_item_eval) {
                    $record[] = $item_answers->puntaje_resul_eval_empren;
                    $result += $item_answers->puntaje_resul_eval_empren;
                  }
                }
              }

              if ($item_answers->codigo_item_evaluacion == 145 || $item_answers->codigo_item_evaluacion == 146 || $item_answers->codigo_item_evaluacion == 147) {
                $record[] = $item_answers->respuesta;
              }
            }
          }

          if (isset($commentaryByResult[$value->codigo_resultado])) {
            $record[] = $commentaryByResult[$value->codigo_resultado];
          }

          if ($value->cod_ciudad_empre == 2257) {
           if ($result >= 65) {
             $result_text = 'Paso';
           } else {
             $result_text = 'No paso';
           }
          } else {
            $result_text = 'No paso';
          }

          $record[] = $result;
          $record[] = $result_text;
          $dataReport[] = $record;
        }
        // Prepare file
        $filename = sprintf('%s_%s.xlsx', 'reporte_evaluaciones', date('YmdHms'));
        $filepath = sprintf('reports/%s/%s/%s', $institution, $user->codigo_usuario, $filename);

        $export = new ReportsExport([
          $header], [$dataReport]
        );

        // Genera el archivo Excel y guárdalo en storage/app/reports
        try {
          $file = Excel::store($export, $filepath, 's3', null, [
            'visibility' => 'public',
          ]);
          $fileUrl = Storage::disk('s3')->url($filepath);
        } catch (\Throwable $e) {
          \Log::warning('Evaluation.php S3 upload failed, saving to local: ' . $e->getMessage());
          $file = Excel::store($export, $filepath, 'local');
          $fileUrl = url('reportes/gestion');
        }

        if (isset($institution) && isset($user->codigo_usuario)) {
          // Send mail
          try {
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = 'Reporte evaluaciones';
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);
          } catch (\Throwable $e) {
            \Log::warning('Evaluation.php SendEmail: ' . $e->getMessage());
          }
        }
      } catch (\Exception $e) {
        \Log::error(sprintf('%s:%s', 'ReportMonitoreo:Command', $e->getMessage()));
        abort(500);
      }
    }

    public function getQuestionsEvaluations($institution) {
      $query = EvaluacionEmprendimiento::query();
      $query->select('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento','evaluacion_emprendimiento.nombre_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', 'item_evaluacion_emprendimiento.nombre_item_evaluacion', 'grupo_item_eval.codigo_grupo_item_eval', DB::raw("UPPER(grupo_item_eval.nombre_grupo_item_eval) as nombre_grupo_item_eval"));
      $query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=','evaluacion_emprendimiento.codigo_evaluacion_emprendimiento');
      $query->leftJoin('core.grupo_item_eval', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $query->where('evaluacion_emprendimiento.codigo_institucion', '=', $institution);
      $query->whereNotIn('item_evaluacion_emprendimiento.codigo_item_evaluacion', array(145, 146, 147));
      $query->orderBy('item_evaluacion_emprendimiento.codigo_item_evaluacion', 'ASC');
      $data = $query->get();

      return $data;
    }

    public function getGroupsEvaluations($institution) {
      $query = EvaluacionEmprendimiento::query();
      $query->select('grupo_item_eval.codigo_grupo_item_eval', 'grupo_item_eval.nombre_grupo_item_eval');
      $query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=','evaluacion_emprendimiento.codigo_evaluacion_emprendimiento');
      $query->join('core.grupo_item_eval', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $query->where('evaluacion_emprendimiento.codigo_institucion', '=', $institution);
      $query->orderBy('grupo_item_eval.codigo_grupo_item_eval', 'ASC');
      $data = $query->distinct()->get();

      return $data;
    }
}
