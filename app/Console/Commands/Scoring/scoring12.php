<?php

namespace App\Console\Commands\Scoring;

use App\Models\Core\Estado;
use App\Models\Core\ItemEvaluacionEmprendimiento;
use App\Models\Core\RegistroConvocatoria;
use App\Models\Core\RegistroFaseUsuario;
use App\Models\Core\ReporteUnico;
use App\Models\Core\RespuestaEncuesta;
use App\Models\Core\ResultadoEvaluation;
use Illuminate\Console\Command;
use Log, DB;

class scoring12 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:scoring12 {execute?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make scoring to challenge 1.2 iNNpulsa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // Query all applications

    public function getApplications() {
      $query = RegistroConvocatoria::query();
      $query->select('registro_convocatoria.numero_registro_convocatoria', 'registro_convocatoria.fecha_registro_convocatoria', 'registro_convocatoria.hora_registro_convocatoria', 'registro_convocatoria.codigo_usuario_encuesta', 'registro_convocatoria.codigo_registro_convocatoria');
      $query->where('registro_convocatoria.codigo_convocatoria', '=', 113);
      $query->where('registro_convocatoria.codigo_estado', '!=', Estado::getState('removed'));
      $query->orderBy('registro_convocatoria.fecha_registro_convocatoria', 'ASC');
      $query->orderBy('registro_convocatoria.hora_registro_convocatoria', 'ASC');
      $data = $query->get();

      return $data;
    }

    // Average Differential

    public function getScoringAverageDifferential($userSurvey, $challenge) {
      $totalScoreAverageDifferential = '';
      if (isset($challenge)) {
        $queryTotalScoreAverage = RespuestaEncuesta::query();
        $queryTotalScoreAverage->select(DB::raw("SUM(puntaje_respuesta_encuesta) as scoring"));
        $queryTotalScoreAverage->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $queryTotalScoreAverage->where('pregunta_encuesta.codigo_encuesta', '=', 49);
        $queryTotalScoreAverage->where('respuesta_encuesta.codigo_usuario_encuesta', '=',$userSurvey);
        $queryTotalScoreAverage->whereIn('pregunta_encuesta.codigo_pregunta_encuesta', array(1245, 1249, 1246, 1247));
        $queryTotalScoreAverage->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $dataTotalScoreAverage = $queryTotalScoreAverage->first();

        $queryGroupItemEvalAverage = ItemEvaluacionEmprendimiento::query();
        $queryGroupItemEvalAverage->select('codigo_grupo_item_eval', DB::raw("SUM( COALESCE(puntaje_resul_eval_empren, 0) ) AS evaluacion"));
        $queryGroupItemEvalAverage->leftJoin('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
        $queryGroupItemEvalAverage->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion');
        $queryGroupItemEvalAverage->leftJoin('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
        $queryGroupItemEvalAverage->where('item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 9);
        $queryGroupItemEvalAverage->where('codigo_grupo_item_eval', '=', 1);
        $queryGroupItemEvalAverage->whereRaw('(asignacion_jurado.codigo_registro_fase_usuario = ' . $challenge->codigo_registro_fase_usuario . ' OR asignacion_jurado.codigo_registro_fase_usuario IS NULL)');
        $queryGroupItemEvalAverage->groupBy('codigo_grupo_item_eval');
        $queryGroupItemEvalAverage->orderBy('codigo_grupo_item_eval');
        $dataGroupItemEvalAverage = $queryGroupItemEvalAverage->first();

        if ($dataGroupItemEvalAverage instanceof ItemEvaluacionEmprendimiento) {
          $totalScoreAverageDifferential = round(($dataGroupItemEvalAverage->evaluacion + $dataTotalScoreAverage->scoring) / 2, 2);
        }

        return $totalScoreAverageDifferential;
      }
    }

    public function getScoringAverageCommercial($userSurvey, $challenge) {
      $totalScoreAverageCommercial = '';
      if (isset($challenge)) {
        $queryTotalScoreCommercial = RespuestaEncuesta::query();
        $queryTotalScoreCommercial->select('pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.puntaje_respuesta_encuesta');
        $queryTotalScoreCommercial->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $queryTotalScoreCommercial->where('pregunta_encuesta.codigo_encuesta', '=', 49);
        $queryTotalScoreCommercial->where('respuesta_encuesta.codigo_estado', '=', 8);
        $queryTotalScoreCommercial->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey);
        $queryTotalScoreCommercial->whereIn('pregunta_encuesta.codigo_pregunta_encuesta', array(1395, 1252, 1253, 1248));
        $dataQueryTotalScoreCommercial = $queryTotalScoreCommercial->get();

        $queryGroupItemEvalCommercial = ItemEvaluacionEmprendimiento::query();
        $queryGroupItemEvalCommercial->select('codigo_grupo_item_eval', DB::raw("SUM( COALESCE(puntaje_resul_eval_empren, 0)) as evaluacion"));
        $queryGroupItemEvalCommercial->leftJoin('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 'item_evaluacion_emprendimiento.codigo_item_evaluacion');
        $queryGroupItemEvalCommercial->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion');
        $queryGroupItemEvalCommercial->leftJoin('core.asignacion_jurado', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
        $queryGroupItemEvalCommercial->where('item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 9);
        $queryGroupItemEvalCommercial->where('codigo_grupo_item_eval', '=', 2);
        $queryGroupItemEvalCommercial->whereRaw('(asignacion_jurado.codigo_registro_fase_usuario = ' . $challenge->codigo_registro_fase_usuario . ' OR asignacion_jurado.codigo_registro_fase_usuario IS NULL)');
        $queryGroupItemEvalCommercial->groupBy('codigo_grupo_item_eval');
        $queryGroupItemEvalCommercial->orderBy('codigo_grupo_item_eval');
        $dataGroupItemEvalCommercial = $queryGroupItemEvalCommercial->first();

        $scoreCommercial = 0;
        foreach ($dataQueryTotalScoreCommercial as $commercial) {
          if ($commercial->codigo_pregunta_encuesta == 1395) {
            if ($commercial->codigo_pregunta_encuesta == 1395 && $commercial->codigo_opcion_encuesta == 1851) {
              $queryResponse1260 = RespuestaEncuesta::query();
              $queryResponse1260->select(DB::raw('COALESCE(respuesta_encuesta.texto_respuesta_encuesta::double precision, 0) as response'));
              $queryResponse1260->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey);
              $queryResponse1260->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 1260);
              $dataQueryResponse1260 = $queryResponse1260->first();
              if ($dataQueryResponse1260 instanceof RespuestaEncuesta) {
                if ($dataQueryResponse1260->response >= 300000) {
                  $scoreCommercial += 7.5;
                } else {
                  $scoreCommercial += -7.5;
                }
              }
            } else {
              $scoreCommercial += 0;
            }
          } else {
            $scoreCommercial += $commercial->puntaje_respuesta_encuesta;
          }
        }
        if ($dataGroupItemEvalCommercial instanceof ItemEvaluacionEmprendimiento) {
          $totalScoreAverageCommercial = round(($dataGroupItemEvalCommercial->evaluacion + $scoreCommercial) / 2, 2);
        }
      }

      return $totalScoreAverageCommercial;
    }

    public function getScoringValidationCommercial($userSurvey, $challenge) {
      $totalScoringValidationCommercial = '';
      if (isset($challenge)) {
        $queryTotalScoreValidationCommercial = RespuestaEncuesta::query();
        $queryTotalScoreValidationCommercial->select(DB::raw("SUM(puntaje_respuesta_encuesta)::text as score"));
        $queryTotalScoreValidationCommercial->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $queryTotalScoreValidationCommercial->where('pregunta_encuesta.codigo_encuesta', '=', 49);
        $queryTotalScoreValidationCommercial->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $queryTotalScoreValidationCommercial->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey);
        $queryTotalScoreValidationCommercial->whereIn('pregunta_encuesta.codigo_pregunta_encuesta', array(1254, 1395, 1256));
        $dataQueryTotalScoreValidationCommercial = $queryTotalScoreValidationCommercial->first();
        if ($dataQueryTotalScoreValidationCommercial instanceof RespuestaEncuesta) {
          $totalScoringValidationCommercial = $dataQueryTotalScoreValidationCommercial->score;
        }
      }

      return $totalScoringValidationCommercial;
    }

    public function getScoringAverageWorkTeam($userSurvey, $challenge) {
      $averageWorkTeam = '';
      if ($challenge) {
        $queryTotalScoreAverageWorkTeam = RespuestaEncuesta::query();
        $queryTotalScoreAverageWorkTeam->select(DB::raw("SUM(puntaje_respuesta_encuesta)::text as score"));
        $queryTotalScoreAverageWorkTeam->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $queryTotalScoreAverageWorkTeam->where('pregunta_encuesta.codigo_encuesta', '=', 49);
        $queryTotalScoreAverageWorkTeam->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $queryTotalScoreAverageWorkTeam->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey);
        $queryTotalScoreAverageWorkTeam->whereIn('pregunta_encuesta.codigo_pregunta_encuesta', array(1250, 1251));
        $dataQueryTotalScoreAverageWorkTeam = $queryTotalScoreAverageWorkTeam->first();
        if ($dataQueryTotalScoreAverageWorkTeam instanceof RespuestaEncuesta) {
          $averageWorkTeam = $dataQueryTotalScoreAverageWorkTeam->score;
        }

        return $averageWorkTeam;
      }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $argument = $this->argument('execute');
      if (isset($argument)) {
        foreach ($this->getApplications() as $application) {
          if ($application instanceof RegistroConvocatoria) {
            $challenge12 = RegistroFaseUsuario::query();
            $challenge12->select('codigo_registro_fase_usuario', 'rango_inferior_eval_fase', 'notificacion_cambio_registro_fase');
            $challenge12->where('registro_fase_usuario.codigo_fase', '=', 150);
            $challenge12->where('registro_fase_usuario.codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria);
            $dataChallenge12 = $challenge12->first();

            switch ($argument) {
              case 'AverageDifferential':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  if ($reportUnique->puntaje_c1_reto12 == 0) {
                    $scoringAverageDifferential = $this->getScoringAverageDifferential($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageDifferential) && $scoringAverageDifferential != '') {
                      $reportUnique->puntaje_c1_reto12 = $scoringAverageDifferential;
                      $reportUnique->save();
                      Log::info('Average Differential Successful - Update '.$application->codigo_registro_convocatoria);
                    } else {
                      Log::info('Average Differential not Update ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                    }
                  }
                } else {
                  $scoringAverageDifferential = $this->getScoringAverageDifferential($application->codigo_usuario_encuesta, $dataChallenge12);
                  if (isset($scoringAverageDifferential) && $scoringAverageDifferential != '') {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = $scoringAverageDifferential;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Average Differential Successful - Insert '.$application->codigo_registro_convocatoria);
                  } else {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Average Differential Insert ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                  }
                }

                break;
              case 'AverageCommercial':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  if ($reportUnique->puntaje_c2_reto12 == 0) {
                    $scoringAverageCommercial = $this->getScoringAverageCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageCommercial) && $scoringAverageCommercial != '') {
                      $reportUnique->puntaje_c2_reto12 = $scoringAverageCommercial;
                      $reportUnique->save();
                      Log::info('Average Commercial Successful - Update '.$application->codigo_registro_convocatoria);
                    } else {
                      Log::info('Average Commercial not Update ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                    }
                  }
                } else {
                  $scoringAverageCommercial = $this->getScoringAverageCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                  if (isset($scoringAverageCommercial) && $scoringAverageCommercial != '') {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = $scoringAverageCommercial;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Average Commercial Successful - Insert '.$application->codigo_registro_convocatoria);
                  } else {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Average Commercial Insert ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                  }
                }

                break;
              case 'ValidationCommercial':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  if ($reportUnique->puntaje_c3_reto12 == 0) {
                    $scoringValidationCommercial = $this->getScoringValidationCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringValidationCommercial) && $scoringValidationCommercial != '') {
                      $reportUnique->puntaje_c3_reto12 = $scoringValidationCommercial;
                      $reportUnique->save();
                      Log::info('Validation Commercial Successful - Update '.$application->codigo_registro_convocatoria);
                    } else {
                      Log::info('Validation Commercial not Update ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                    }
                  }
                } else {
                  $scoringValidationCommercial = $this->getScoringValidationCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                  if (isset($scoringValidationCommercial) && $scoringValidationCommercial != '') {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = $scoringValidationCommercial;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Validation Commercial Successful - Insert '.$application->codigo_registro_convocatoria);
                  } else {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Validation Commercial Insert ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                  }
                }

                break;
              case 'AverageWorkTeam':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  if ($reportUnique->puntaje_c4_reto12 == 0) {
                    $scoringAverageWorkteam = $this->getScoringAverageWorkTeam($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageWorkteam) && $scoringAverageWorkteam != '') {
                      $reportUnique->puntaje_c1_reto12 = 0;
                      $reportUnique->puntaje_c2_reto12 = 0;
                      $reportUnique->puntaje_c3_reto12 = 0;
                      $reportUnique->puntaje_c4_reto12 = $scoringAverageWorkteam;
                      $reportUnique->puntaje_c1_reto2 = 0;
                      $reportUnique->puntaje_c2_reto2 = 0;
                      $reportUnique->puntaje_c3_reto2 = 0;
                      $reportUnique->puntaje_c4_reto2 = 0;
                      $reportUnique->save();
                      Log::info('Average Work Team Successful - Update '.$application->codigo_registro_convocatoria);
                    } else {
                      Log::info('Average Work Team not Update ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                    }
                  }
                } else {
                  $scoringAverageWorkteam = $this->getScoringAverageWorkTeam($application->codigo_usuario_encuesta, $dataChallenge12);
                  if (isset($scoringAverageWorkteam) && $scoringAverageWorkteam != '') {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = $scoringAverageWorkteam;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Average Work Team Successful - Insert '.$application->codigo_registro_convocatoria);
                  } else {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();
                    Log::info('Average Work Team Successful - Insert ( Scoring Null )'.$application->codigo_registro_convocatoria);
                  }
                }
                break;
              case 'All':
                DB::beginTransaction();
                try {
                  $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                  if ($reportUnique instanceof ReporteUnico) {
                    $scoringAverageDifferential = $this->getScoringAverageDifferential($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageDifferential) && $scoringAverageDifferential != '') {
                      $reportUnique->puntaje_c1_reto12 = $scoringAverageDifferential;
                    } else {
                      $reportUnique->puntaje_c1_reto12 = 0;
                    }

                    $scoringAverageCommercial = $this->getScoringAverageCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageCommercial) && $scoringAverageCommercial != '') {
                      $reportUnique->puntaje_c2_reto12 = $scoringAverageCommercial;
                    } else {
                      $reportUnique->puntaje_c2_reto12 = 0;
                    }

                    $scoringValidationCommercial = $this->getScoringValidationCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringValidationCommercial) && $scoringValidationCommercial != '') {
                      $reportUnique->puntaje_c3_reto12 = $scoringValidationCommercial;
                    } else {
                      $reportUnique->puntaje_c3_reto12 = 0;
                    }

                    $scoringAverageWorkteam = $this->getScoringAverageWorkTeam($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageWorkteam) && $scoringAverageWorkteam != '') {
                      $reportUnique->puntaje_c4_reto12 = $scoringAverageWorkteam;
                    } else {
                      $reportUnique->puntaje_c4_reto12 = 0;
                    }

                    $reportUnique->save();

                    Log::info('Scoring Successful - Update '.$application->codigo_registro_convocatoria);
                  } else {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;

                    $scoringAverageDifferential = $this->getScoringAverageDifferential($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageDifferential) && $scoringAverageDifferential != '') {
                      $reportUnique->puntaje_c1_reto12 = $scoringAverageDifferential;
                    } else {
                      $reportUnique->puntaje_c1_reto12 = 0;
                    }

                    $scoringAverageCommercial = $this->getScoringAverageCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageCommercial) && $scoringAverageCommercial != '') {
                      $reportUnique->puntaje_c2_reto12 = $scoringAverageCommercial;
                    } else {
                      $reportUnique->puntaje_c2_reto12 = 0;
                    }

                    $scoringValidationCommercial = $this->getScoringValidationCommercial($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringValidationCommercial) && $scoringValidationCommercial != '') {
                      $reportUnique->puntaje_c3_reto12 = $scoringValidationCommercial;
                    } else {
                      $reportUnique->puntaje_c3_reto12 = 0;
                    }

                    $scoringAverageWorkteam = $this->getScoringAverageWorkTeam($application->codigo_usuario_encuesta, $dataChallenge12);
                    if (isset($scoringAverageWorkteam) && $scoringAverageWorkteam != '') {
                      $reportUnique->puntaje_c4_reto12 = $scoringAverageWorkteam;
                    } else {
                      $reportUnique->puntaje_c4_reto12 = 0;
                    }

                    // Other Scoring

                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;

                    $reportUnique->save();
                    Log::info('Scoring Successful - Insert '.$application->codigo_registro_convocatoria);
                  }

                  // Commit Transaction
                  DB::commit();
                } catch (\Exception $e) {
                  DB::rollback();
                  Log::info('Algo salio mal !');
                  Log::info('----------------------------------------------------');
                  Log::info($e);
                }
                break;
              default:
                $this->error('Option Invalid !');
                $this->info('Options available:');
                $this->warn('{ All } execute all scoring of challenge 1.2');
                $this->warn('{ AverageDifferential } execute scoring of average differential challenge 1.2');
                $this->warn('{ AverageCommercial } execute scoring of average commercial challenge 1.2');
                $this->warn('{ ValidationCommercial } execute scoring of validation commercial challenge 1.2');
                $this->warn('{ AverageWorkTeam } execute scoring of average work team challenge 1.2');
                die();
                break;
            }
          }
        }
      } else {
        $this->info('Options available:');
        $this->warn('{ All } execute all scoring of challenge 1.2');
        $this->warn('{ AverageDifferential } execute scoring of average differential challenge 1.2');
        $this->warn('{ AverageCommercial } execute scoring of average commercial challenge 1.2');
        $this->warn('{ ValidationCommercial } execute scoring of validation commercial challenge 1.2');
        $this->warn('{ AverageWorkTeam } execute scoring of average work team challenge 1.2');
      }
    }
}
