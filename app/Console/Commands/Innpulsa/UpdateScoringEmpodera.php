<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Log, Storage, DB;

use App\Models\Core\RespuestaEncuesta,
App\Models\Core\UsuarioEncuesta,
App\Models\Core\Estado;

class UpdateScoringEmpodera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:updatescoringempodera';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Scorging empodera reto 1';

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
        $usersSurvey = UsuarioEncuesta::where('codigo_encuesta',92)->where('codigo_estado',11)->get();
        $contador = 1;
        foreach($usersSurvey as $userSurvey){            
            $score = $this->scoreChallengeEmpodera_1($userSurvey);
            $userSurvey->puntaje_total_encuesta = $score;
            $userSurvey->save();
            \Log::info($contador." Usuario  ".$userSurvey->codigo_usuario." Score ".$score."\n");
            $contador++;
        }
        dd("Llego al final");        
    }

    public static function scoreChallengeEmpodera_1(UsuarioEncuesta $userSurvey)
	{
		$query = RespuestaEncuesta::query();
        $query->select(
            DB::raw('
                SUM(
                    COALESCE(puntaje_respuesta_encuesta, 0) +
                    (CASE WHEN (pregunta_encuesta.codigo_pregunta_encuesta = 2878 and respuesta_encuesta.codigo_opcion_encuesta = 4084)
                        THEN ( CASE
                            WHEN (
                                (SELECT COALESCE(r2877.texto_respuesta_encuesta::double precision, 0)
                                FROM core.respuesta_encuesta as r2877
                                WHERE r2877.codigo_usuario = respuesta_encuesta.codigo_usuario
                                AND r2877.codigo_usuario_encuesta = respuesta_encuesta.codigo_usuario_encuesta
                                AND r2877.codigo_pregunta_encuesta = 2877) >= 300000
                            )
                            THEN 7.5 ELSE -7.5
                        END)
                    ELSE 0 END) +
                    (CASE WHEN (pregunta_encuesta.codigo_pregunta_encuesta = 2817 and respuesta_encuesta.codigo_opcion_encuesta = 3906)
                        THEN ( CASE
                            WHEN (
                                (
                                    SELECT COALESCE( SUM(COALESCE(resp.texto_respuesta_encuesta::double precision, 0)) ,0)
                                    FROM core.respuesta_encuesta as resp
                                    WHERE resp.codigo_usuario = respuesta_encuesta.codigo_usuario
                                    AND resp.codigo_usuario_encuesta = respuesta_encuesta.codigo_usuario_encuesta
                                    AND resp.codigo_pregunta_encuesta in (2875, 2876, 2877)
                                ) < 300000
                            )
                            THEN -100 ELSE 0
                        END)
                        ELSE 0 END) +
                    (CASE WHEN (pregunta_encuesta.codigo_pregunta_encuesta = 2878 and respuesta_encuesta.codigo_opcion_encuesta = 4084)
            		    THEN (
							CASE WHEN (
								SELECT COALESCE( SUM(COALESCE(resp.texto_respuesta_encuesta::double precision, 0)) ,0)
								FROM core.respuesta_encuesta as resp
								WHERE resp.codigo_usuario = respuesta_encuesta.codigo_usuario
								AND resp.codigo_usuario_encuesta = respuesta_encuesta.codigo_usuario_encuesta
								AND resp.codigo_pregunta_encuesta in (2875, 2876, 2877)
							) < 300000 THEN -100 ELSE 0 END)
					    ELSE 0 END
                    )
                ) AS total_evaluation
            ')
        );
        $query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
        $query->whereIn('pregunta_encuesta.codigo_pregunta_encuesta', [2809, 2814, 2831, 2823, 2821, 2832, 2834, 2829, 2825, 2815, 2817, 2878]);
        $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
        return $query->first()->total_evaluation;
    }
}
