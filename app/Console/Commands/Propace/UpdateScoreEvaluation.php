<?php

namespace App\Console\Commands\Propace;

use Illuminate\Console\Command;

use App\Models\Core\UsuarioEncuesta, App\Models\Core\RespuestaEncuesta, App\Models\Core\Estado;

use DB, Log;

class UpdateScoreEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'propace:updatescoreevaluation
                            {id : Id of survey}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los puntaje totales obtenidos por los usuarios que completaron la encuesta';

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

      try{
        $id = $this->argument('id');
        // $codigo_encuesta = 127;
        $this->info(sprintf('Updating total score for evaluation %s...', $id));
        Log::info(sprintf('Updating total score for evaluation %s...', $id));

        $query = UsuarioEncuesta::query();
        $query->where('usuario_encuesta.codigo_encuesta', $id );
        $query->where('usuario_encuesta.codigo_estado', Estado::getState('complete'));
        $userSurveys = $query->get();

        if($userSurveys->count()){
          $progressBar = $this->output->createProgressBar($userSurveys->count());
          $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %memory:6s%');

          foreach ($userSurveys as $userSurvey)
          {
            $score = $this->generalScore($userSurvey);
            $userSurvey->puntaje_total_encuesta = $score;
            $userSurvey->save();
            $progressBar->advance();

          }
          $progressBar->finish();
        }


        $this->info('');
        $this->info(sprintf('Score updated for evaluation %s', $id));
        Log::info(sprintf('Score updated for evaluation %s', $id));

      } catch (\Exception $e){
        $this->error($e->getMessage());
        Log::error(sprintf('%s:%s', 'App\Console\Commands\Propace\UpdateScoreEvaluation:command', $e->getMessage()));
    }

    }


    /**
     * Compute total score of a evaluation anwswered by user
     */
    public function generalScore(UsuarioEncuesta $userSurvey){

      $query = RespuestaEncuesta::query();
      $query->select(DB::raw('SUM(COALESCE(puntaje_respuesta_encuesta, 0) ) AS total_evaluation'));
      $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
          $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
      $query->where('respuesta_encuesta.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
      $scoring = $query->first()->total_evaluation;
      return $scoring;
    }
}
