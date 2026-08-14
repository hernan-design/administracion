<?php

namespace App\Console\Commands\Scoring;

use App\Models\Core\Estado;
use App\Models\Core\RegistroConvocatoria;
use App\Models\Core\RegistroFaseUsuario;
use App\Models\Core\ReporteUnico;
use App\Models\Core\ResultadoEvaluation;
use Illuminate\Console\Command;
use DB, Log;

class scoring2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:scoring2 {execute?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make scoring to challenge 2 iNNpulsa';

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

  // Enterprising Team

  public function getScoringEnterprisingTeam($challenge) {
    $totalScoringEnterprisingTeam = 0;
    if ($challenge) {
      $queryScoreEnterprisingTeam = ResultadoEvaluation::query();
      $queryScoreEnterprisingTeam->select(DB::raw("(SUM(valor_opc_eval_pregunta) / 3) as score"), 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $queryScoreEnterprisingTeam->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
      $queryScoreEnterprisingTeam->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
      $queryScoreEnterprisingTeam->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=','evaluacion_item_emprendimiento.valor_evaluacion_item');
      $queryScoreEnterprisingTeam->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
      $queryScoreEnterprisingTeam->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $challenge->codigo_registro_fase_usuario);
      $queryScoreEnterprisingTeam->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 10);
      $queryScoreEnterprisingTeam->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval','=',5);
      $queryScoreEnterprisingTeam->groupBy('item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $dataScoreEnterprising = $queryScoreEnterprisingTeam->first();

      if ($dataScoreEnterprising instanceof ResultadoEvaluation) {
        $totalScoringEnterprisingTeam = round(($dataScoreEnterprising->score / 40) * 100);
      }

      return $totalScoringEnterprisingTeam;
    }
  }

  // Market

  public function getScoringMarket($challenge) {
    $totalScoreMarket = 0;
    if (isset($challenge)) {
      $queryScoreMarket = ResultadoEvaluation::query();
      $queryScoreMarket->select(DB::raw("(SUM(valor_opc_eval_pregunta) / 3) as score"), 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $queryScoreMarket->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
      $queryScoreMarket->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
      $queryScoreMarket->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=','evaluacion_item_emprendimiento.valor_evaluacion_item');
      $queryScoreMarket->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
      $queryScoreMarket->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $challenge->codigo_registro_fase_usuario);
      $queryScoreMarket->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 10);
      $queryScoreMarket->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval','=',6);
      $queryScoreMarket->groupBy('item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $dataScoreMarket = $queryScoreMarket->first();

      if ($dataScoreMarket instanceof ResultadoEvaluation) {
        $totalScoreMarket = round(($dataScoreMarket->score / 25) * 100 );
      }

      return $totalScoreMarket;
    }
  }

  // Product

  public function getScoringProduct($challenge) {
    $totalScoreProduct = 0;
    if ($challenge) {
      $queryScoreProduct = ResultadoEvaluation::query();
      $queryScoreProduct->select(DB::raw("(SUM(valor_opc_eval_pregunta) / 3) as score"), 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $queryScoreProduct->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
      $queryScoreProduct->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
      $queryScoreProduct->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=','evaluacion_item_emprendimiento.valor_evaluacion_item');
      $queryScoreProduct->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
      $queryScoreProduct->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $challenge->codigo_registro_fase_usuario);
      $queryScoreProduct->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 10);
      $queryScoreProduct->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval','=',7);
      $queryScoreProduct->groupBy('item_evaluacion_emprendimiento.codigo_grupo_item_eval');
      $dataScoreProduct = $queryScoreProduct->first();

      if ($dataScoreProduct instanceof ResultadoEvaluation) {
        $totalScoreProduct = round(($dataScoreProduct->score / 25) * 100 );
      }

      return $totalScoreProduct;
    }
  }

  // Productive Chains

  public function getScoringProductiveChains($challenge) {
      $totalScoreProductiveChains = 0;
      if ($challenge) {
        $queryScoreProductiveChains = ResultadoEvaluation::query();
        $queryScoreProductiveChains->select(DB::raw("(SUM(valor_opc_eval_pregunta) / 3) as score"), 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
        $queryScoreProductiveChains->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
        $queryScoreProductiveChains->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
        $queryScoreProductiveChains->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=','evaluacion_item_emprendimiento.valor_evaluacion_item');
        $queryScoreProductiveChains->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
        $queryScoreProductiveChains->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $challenge->codigo_registro_fase_usuario);
        $queryScoreProductiveChains->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 10);
        $queryScoreProductiveChains->where('item_evaluacion_emprendimiento.codigo_grupo_item_eval','=',8);
        $queryScoreProductiveChains->groupBy('item_evaluacion_emprendimiento.codigo_grupo_item_eval');
        $dataScoreProductiveChains = $queryScoreProductiveChains->first();

        if ($dataScoreProductiveChains instanceof ResultadoEvaluation) {
          $totalScoreProductiveChains = round(($dataScoreProductiveChains->score / 10) * 100 );
        }

        return $totalScoreProductiveChains;
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
            $challenge2 = RegistroFaseUsuario::query();
            $challenge2->select('codigo_registro_fase_usuario', 'rango_inferior_eval_fase', 'notificacion_cambio_registro_fase');
            $challenge2->where('registro_fase_usuario.codigo_fase', '=', 137);
            $challenge2->where('registro_fase_usuario.codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria);
            $dataChallenge2 = $challenge2->first();

            switch ($argument) {
              case 'EnterprisingTeam':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  if ($reportUnique->puntaje_c1_reto2 == 0) {
                    $scoringEnterprisingTeam = $this->getScoringEnterprisingTeam($dataChallenge2);
                    if (isset($scoringEnterprisingTeam) && $scoringEnterprisingTeam != 0) {
                      $reportUnique->puntaje_c1_reto2 = $scoringEnterprisingTeam;
                      $reportUnique->save();
                      Log::info('Enterprising Team Successful - Update '.$application->codigo_registro_convocatoria);
                    } else {
                      Log::info('Enterprising Team not Update ( Scoring Null ) '.$application->codigo_registro_convocatoria);
                    }
                  }
                } else {
                  $scoringEnterprisingTeam = $this->getScoringEnterprisingTeam($dataChallenge2);
                  if (isset($scoringEnterprisingTeam) && $scoringEnterprisingTeam != 0) {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = $scoringEnterprisingTeam;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();

                    Log::info('Enterprising Team Successful - Insert '.$application->codigo_registro_convocatoria);
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

                    Log::info('Enterprising Team Successful - Insert ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                  }
                }
              break;
              case 'Market':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  $scoringMarket = $this->getScoringMarket($dataChallenge2);
                  if (isset($scoringMarket) && $scoringMarket != 0) {
                    $reportUnique->puntaje_c2_reto2 = $scoringMarket;
                    $reportUnique->save();

                    Log::info('Market Successful - Update '.$application->codigo_registro_convocatoria);
                  } else {
                    Log::info('Market Successful - not Update ( Scoring Null ) '.$application->codigo_registro_convocatoria);
                  }
                } else {
                  $scoringMarket = $this->getScoringMarket($dataChallenge2);
                  if (isset($scoringMarket) && $scoringMarket != 0) {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = $scoringMarket;
                    $reportUnique->puntaje_c3_reto2 = 0;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();

                    Log::info('Market Successful - Insert '.$application->codigo_registro_convocatoria);
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

                    Log::info('Market Successful - Insert ( Scoring Null ) - '.$application->codigo_registro_convocatoria);
                  }
                }
              break;
              case 'Product':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  $scoringProduct = $this->getScoringProduct($dataChallenge2);
                  if (isset($scoringProduct) && $scoringProduct != 0){
                    $reportUnique->puntaje_c3_reto2 = $scoringProduct;
                    $reportUnique->save();

                    Log::info('Product Successful - Update '.$application->codigo_registro_convocatoria);
                  } else {
                    Log::info('Product Successful - not Update ( Scoring Null ) '.$application->codigo_registro_convocatoria);
                  }
                } else {
                  $scoringProduct = $this->getScoringProduct($dataChallenge2);
                  if (isset($scoringProduct) && $scoringProduct != 0) {
                    $reportUnique = new ReporteUnico();
                    $reportUnique->codigo_estado = Estado::getState('active');
                    $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                    $reportUnique->puntaje_c1_reto12 = 0;
                    $reportUnique->puntaje_c2_reto12 = 0;
                    $reportUnique->puntaje_c3_reto12 = 0;
                    $reportUnique->puntaje_c4_reto12 = 0;
                    $reportUnique->puntaje_c1_reto2 = 0;
                    $reportUnique->puntaje_c2_reto2 = 0;
                    $reportUnique->puntaje_c3_reto2 = $scoringProduct;
                    $reportUnique->puntaje_c4_reto2 = 0;
                    $reportUnique->save();

                    Log::info('Market Successful - Insert '.$application->codigo_registro_convocatoria);
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

                    Log::info('Market Successful - Insert ( Scoring Null ) '.$application->codigo_registro_convocatoria);
                  }
                }
              break;
              case 'ProductiveChains':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  $scoringProductiveChains = $this->getScoringProductiveChains($dataChallenge2);
                  if (isset($scoringProductiveChains) && $scoringProductiveChains != 0) {
                    $reportUnique->puntaje_c4_reto2 = $scoringProductiveChains;
                    $reportUnique->save();

                    Log::info('Productive Chains Successful - Update '.$application->codigo_registro_convocatoria);
                  } else {
                    Log::info('Productive Chains not Update ( Scoring Null ) '.$application->codigo_registro_convocatoria);
                  }
                } else {
                  $scoringProductiveChains = $this->getScoringProductiveChains($dataChallenge2);
                  if (isset($scoringProductiveChains) && $scoringProductiveChains != 0) {
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
                    $reportUnique->puntaje_c4_reto2 = $scoringProductiveChains;
                    $reportUnique->save();

                    Log::info('Productive Chains Successful - Insert '.$application->codigo_registro_convocatoria);
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

                    Log::info('Productive Chains Successful - Insert ( Scoring Null )'.$application->codigo_registro_convocatoria);
                  }
                }
              break;
              case 'All':
                $reportUnique = ReporteUnico::where('codigo_registro_convocatoria', '=', $application->codigo_registro_convocatoria)->first();
                if ($reportUnique instanceof ReporteUnico) {
                  $scoringEnterprisingTeam = $this->getScoringEnterprisingTeam($dataChallenge2);
                  if(isset($scoringEnterprisingTeam) && $scoringEnterprisingTeam != 0) {
                    $reportUnique->puntaje_c1_reto2 = $scoringEnterprisingTeam;
                  } else {
                    $reportUnique->puntaje_c1_reto2 = 0;
                  }

                  $scoringMarket = $this->getScoringMarket($dataChallenge2);
                  if (isset($scoringMarket) && $scoringMarket != 0) {
                    $reportUnique->puntaje_c2_reto2 = $scoringMarket;
                  } else {
                    $reportUnique->puntaje_c2_reto2 = 0;
                  }

                  $scoringProduct = $this->getScoringProduct($dataChallenge2);
                  if (isset($scoringProduct) && $scoringProduct != 0) {
                    $reportUnique->puntaje_c3_reto2 = $scoringProduct;
                  } else {
                    $reportUnique->puntaje_c3_reto2 = 0;
                  }

                  $scoringProductiveChains = $this->getScoringProductiveChains($dataChallenge2);
                  if (isset($scoringProductiveChains) && $scoringProductiveChains != 0) {
                    $reportUnique->puntaje_c4_reto2 = $scoringProductiveChains;
                  } else {
                    $reportUnique->puntaje_c4_reto2 = 0;
                  }

                  $reportUnique->save();
                  Log::info('Scoring Successful - Update '.$application->codigo_registro_convocatoria);
                } else {
                  $reportUnique = new ReporteUnico();
                  $reportUnique->codigo_estado = Estado::getState('active');
                  $reportUnique->codigo_registro_convocatoria = $application->codigo_registro_convocatoria;
                  $reportUnique->puntaje_c1_reto12 = 0;
                  $reportUnique->puntaje_c2_reto12 = 0;
                  $reportUnique->puntaje_c3_reto12 = 0;
                  $reportUnique->puntaje_c4_reto12 = 0;

                  $scoringEnterprisingTeam = $this->getScoringEnterprisingTeam($dataChallenge2);
                  if(isset($scoringEnterprisingTeam) && $scoringEnterprisingTeam != 0) {
                    $reportUnique->puntaje_c1_reto2 = $scoringEnterprisingTeam;
                  } else {
                    $reportUnique->puntaje_c1_reto2 = 0;
                  }

                  $scoringMarket = $this->getScoringMarket($dataChallenge2);
                  if (isset($scoringMarket) && $scoringMarket != 0) {
                    $reportUnique->puntaje_c2_reto2 = $scoringMarket;
                  } else {
                    $reportUnique->puntaje_c2_reto2 = 0;
                  }

                  $scoringProduct = $this->getScoringProduct($dataChallenge2);
                  if (isset($scoringProduct) && $scoringProduct != 0) {
                    $reportUnique->puntaje_c3_reto2 = $scoringProduct;
                  } else {
                    $reportUnique->puntaje_c3_reto2 = 0;
                  }

                  $scoringProductiveChains = $this->getScoringProductiveChains($dataChallenge2);
                  if (isset($scoringProductiveChains) && $scoringProductiveChains != 0) {
                    $reportUnique->puntaje_c4_reto2 = $scoringProductiveChains;
                  } else {
                    $reportUnique->puntaje_c4_reto2 = 0;
                  }

                  $reportUnique->save();
                  Log::info('Scoring Successful - Insert '.$application->codigo_registro_convocatoria);
                }

                break;
              default:
                $this->error('Option Invalid !');
                $this->info('Options available:');
                $this->warn('{ All } execute all scoring of challenge 2');
                $this->warn('{ EnterprisingTeam } execute scoring of enterprising team challenge 2');
                $this->warn('{ Market } execute scoring of market challenge 2');
                $this->warn('{ Product } execute scoring of product challenge 2');
                $this->warn('{ ProductiveChains } execute scoring of productive chains challenge 2');
                die();

                break;
            }
          }
        }
      } else {
        $this->info('Options available:');
        $this->warn('{ All } execute all scoring of challenge 2');
        $this->warn('{ EnterprisingTeam } execute scoring of enterprising team challenge 2');
        $this->warn('{ Market } execute scoring of market challenge 2');
        $this->warn('{ Product } execute scoring of product challenge 2');
        $this->warn('{ ProductiveChains } execute scoring of productive chains challenge 2');
      }
    }
}
