<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Log, Storage, Excel, DB, DateTime;

use App\Models\Core\RegistroConvocatoria,
    App\Models\Core\RespuestaEncuesta,
    App\Models\Core\UsuarioEncuesta,
    App\Models\Core\Estado;

class CompareScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:comparescore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Excel::load('unico.xlsx', function($reader) {
            $results = $reader->get()->toArray();
            foreach($results as $resul){
                $aplicacion = trim($resul['aplicacion']);
                $empresaconstituida = trim($resul['empresaconstituida']);
                $score_personeriajuridica = trim($resul['score_personeriajuridica']);
                $score_menos7anos = trim($resul['score_menos7anos']);
                $score_proy_3anos_100m = trim($resul['score_proy_3anos_100m']);
                $score_mas_100 = trim($resul['score_mas_100']);
                $score_equipoemprendedor = trim($resul['score_equipoemprendedor']);
                $score_dedicacion100 = trim($resul['score_dedicacion100']);
                $score_nobuscandotrabajo = trim($resul['score_nobuscandotrabajo']);
                $score_final_1_1 = trim($resul['score_final_1_1']);

                $suma_total = $empresaconstituida+$score_personeriajuridica+$score_menos7anos+$score_proy_3anos_100m+$score_mas_100+$score_equipoemprendedor+$score_dedicacion100+$score_nobuscandotrabajo;
                $diferencia = $suma_total - $score_final_1_1; 
                                
                if($aplicacion != ""){
                    $RegistroConvocatoria = RegistroConvocatoria::where('numero_registro_convocatoria',$aplicacion)->first();
                    if($RegistroConvocatoria instanceof RegistroConvocatoria){
                        $codigo_registro_convocatoria = $RegistroConvocatoria->codigo_registro_convocatoria;
                        $numero_registro_convocatoria = $RegistroConvocatoria->numero_registro_convocatoria;
                        $codigo_usuario_encuesta = $RegistroConvocatoria->codigo_usuario_encuesta;
                        $usuario_encuesta = UsuarioEncuesta::find($codigo_usuario_encuesta);                        
                        $fecha_registro_convocatoria = $RegistroConvocatoria->fecha_registro_convocatoria;
                        $puntaje_usuario_encuesta = self::scoringAldea20($usuario_encuesta, $fecha_registro_convocatoria);

                        // if($score_final_1_1 >= 70 && $score_final_1_1 <= 80){
                            $score_empresa_constituida = 0;
                            $score_persona_juridica = 0;
                            $score_inicio_ventas = 0;
                            $score_ventas_2020 = 0;
                            $score_proyeccion_2023 = 0;
                            $score_equipo_emprendedor = 0;
                            $score_dedicacion_lider = 0;
                            $score_busqueda_trabajo = 10;
                            $empresa_constituida = RespuestaEncuesta::select('codigo_opcion_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4263)->first();
                            $persona_juridica = RespuestaEncuesta::select('codigo_opcion_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4282)->first();
                            $inicio_ventas = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4284)->first();
                            $ventas_2019 = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4336)->where('cor_codigo_opcion_encuesta',7146)->first();
                            $ventas_2020 = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4336)->where('cor_codigo_opcion_encuesta',7147)->first();
                            $proyeccion_2023 = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4342)->where('cor_codigo_opcion_encuesta',7174)->first();
                            $equipo_emprendedor = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4372)->first();
                            $dedicacion_lider = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4373)->where('codigo_opcion_encuesta',7262)->where('cor_codigo_opcion_encuesta',7269)->first();
                            $busqueda_trabajo = RespuestaEncuesta::select('codigo_opcion_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4392)->get();

                            if($empresa_constituida instanceof RespuestaEncuesta){
                                if($empresa_constituida->codigo_opcion_encuesta == 7015){
                                    $score_empresa_constituida = 10;
                                }
                            }
                            
                            if($persona_juridica instanceof RespuestaEncuesta){
                                if($persona_juridica->codigo_opcion_encuesta == 7045){
                                    $score_persona_juridica = 10;
                                }
                            }

                            $fecha_registro = new DateTime($fecha_registro_convocatoria);
                            $fecha_inicio_ventas = new DateTime($inicio_ventas->texto_respuesta_encuesta);
                            $diff = $fecha_registro->diff($fecha_inicio_ventas);

                            if ($diff->y <= 7) {
                                $score_inicio_ventas = 10;
                            }                            
                            
                            if($ventas_2020->texto_respuesta_encuesta >= 100000000){
                                $score_ventas_2020 = 10;
                                $total_2023 = $ventas_2020->texto_respuesta_encuesta + 100000000;
                                if($proyeccion_2023->texto_respuesta_encuesta >= $total_2023){
                                    $score_proyeccion_2023 = 10;
                                }
                            } else if($ventas_2019->texto_respuesta_encuesta >= 100000000){
                                $score_ventas_2020 = 10;
                                $total_2023 = $ventas_2020->texto_respuesta_encuesta + 100000000;
                                if($proyeccion_2023->texto_respuesta_encuesta >= $total_2023){
                                    $score_proyeccion_2023 = 10;
                                }
                            }

                            if($equipo_emprendedor->texto_respuesta_encuesta >= 2){
                                $score_equipo_emprendedor = 10;
                            }

                            if(in_array($dedicacion_lider->texto_respuesta_encuesta,array(102,105))){
                                $score_dedicacion_lider = 10;
                            }

                            // if($busqueda_trabajo->codigo_opcion_encuesta != 7323){
                            //     $score_busqueda_trabajo = 10;
                            // }
                            $busqueda_de_trabajo = 0;
                            if(count($busqueda_trabajo) > 0){
                                foreach($busqueda_trabajo as $trabajo){
                                    if($trabajo->codigo_opcion_encuesta == 7323){
                                        $score_busqueda_trabajo = 0;
                                        $busqueda_de_trabajo = $trabajo->codigo_opcion_encuesta;
                                    }
                                }
                            }

                            // \Log::info("Respuestas ".$empresa_constituida->codigo_opcion_encuesta." -> ".$persona_juridica->codigo_opcion_encuesta." -> ".$diff->y." -> ".$inicio_ventas->texto_respuesta_encuesta." -> ".$ventas_2019->texto_respuesta_encuesta." -> ".$ventas_2020->texto_respuesta_encuesta." -> ".$proyeccion_2023->texto_respuesta_encuesta." -> ".$equipo_emprendedor->texto_respuesta_encuesta." -> ".$dedicacion_lider->texto_respuesta_encuesta." -> ".$busqueda_de_trabajo."\n");
                            $total_calculado = $score_empresa_constituida + $score_persona_juridica + $score_inicio_ventas + $score_ventas_2020 + $score_proyeccion_2023 + $score_equipo_emprendedor + $score_dedicacion_lider + $score_busqueda_trabajo;
                            \Log::info($score_empresa_constituida." + ".$score_persona_juridica." + ".$score_inicio_ventas." + ".$score_ventas_2020." + ".$score_proyeccion_2023." + ".$score_equipo_emprendedor." + ".$score_dedicacion_lider." + ".$score_busqueda_trabajo."\n");
                            \Log::info("Aplicación: ".$numero_registro_convocatoria." Reporte: ".$score_final_1_1." Suma: ".$suma_total." Calculo: ".$total_calculado." Diferencia: ".$diferencia." Funcion ".$puntaje_usuario_encuesta."\n");
                            $usuario_encuesta->puntaje_total_encuesta = $total_calculado;
                            $usuario_encuesta->save();
                        // }                        
                    }                    
                }
                
            }
        });
        \Log::info("Finalizo");
    }

    public static function getScoring__4336__Aldea20($usuario) {
		$query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4336);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7146, 7147]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $usuario);
		$query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta::BIGINT >= 100000000');

		return $query->first();
    }
    
    public static function getScoring__4342__Aldea20($usuario) {
		$query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4342);
		$query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', '=', 7174);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $usuario);

		$total__score = 0;
		$expected__response = 0;
		if (self::getScoring__4336__Aldea20($usuario) != null) {
			$expected__response = (int)self::getScoring__4336__Aldea20($usuario)->texto_respuesta_encuesta + 100000000;

			if((int)$query->first()->texto_respuesta_encuesta >= $expected__response) {
				$total__score = 10;
			}
		}

		return $total__score;
    }
    
    public static function getScoring__4373__Aldea20($usuario) {
		$query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7262);
		$query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', '=', 7269);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $usuario);
		$query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta IN (102::varchar(255), 105::varchar(255))');
		$data = $query->first();

		return $data;
    }
    
    public static function getScoring__4392__Aldea20($usuario) {
		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('COUNT(respuesta_encuesta.codigo_respuesta_encuesta) as value'));
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4392);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '!=', 7323);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $usuario);

		return $query->first();
    }
    
    public static function date__4284_aldea20($usuario) {
		$query = RespuestaEncuesta::query();
		$query->select('texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4284);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $usuario);
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));

		return $query->first();
	}

    public static function scoringAldea20(UsuarioEncuesta $userSurvey, $fecha_registro) {
		$scoring_date = 0;
		$scoring_total_4336 = 0;
		$scoring_total_4373 = 0;
		$scoring_total_4392 = 0;
		$scoring__4336 = self::getScoring__4336__Aldea20($userSurvey->codigo_usuario_encuesta);
		$scoring__4342 = self::getScoring__4342__Aldea20($userSurvey->codigo_usuario_encuesta);
		$scoring__4373 = self::getScoring__4373__Aldea20($userSurvey->codigo_usuario_encuesta);
		$scoring__4392 = self::getScoring__4392__Aldea20($userSurvey->codigo_usuario_encuesta);
		$date__4284 = self::date__4284_aldea20($userSurvey->codigo_usuario_encuesta);
		if ($date__4284 != null) {
			$date_response = new DateTime($date__4284->texto_respuesta_encuesta);
			$today = new DateTime($fecha_registro);
			$diff__years = $today->diff($date_response);
			if ($diff__years->y <= 7) {
				$scoring_date = 10;
			}
		}
		if ($scoring__4336 != null) {
			$scoring_total_4336 = 10;
		}
		if ($scoring__4373 != null) {
			$scoring_total_4373 = 10;
		}
		if ($scoring__4392->value > 0) {
			$scoring_total_4392 = 10;
		}
		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('
			SUM(
				COALESCE(puntaje_respuesta_encuesta, 0) +
				(CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4263 AND respuesta_encuesta.codigo_opcion_encuesta = 7015
				THEN
					10
				ELSE
					0
				END ) +
				( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4282 AND respuesta_encuesta.codigo_opcion_encuesta = 7045
				THEN 10
				ELSE 0
				END ) +
				( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4372 AND respuesta_encuesta.texto_respuesta_encuesta::INTEGER >= 2
				THEN 10
				ELSE 0
				END )
			) as total_scoring
		'));
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->whereIn('respuesta_encuesta.codigo_pregunta_encuesta', [4263, 4282, 4336, 4342, 4372, 4373, 4392]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $userSurvey->codigo_usuario_encuesta);
		$scoring = $query->first()->total_scoring;
		return $scoring += ($scoring_date + $scoring_total_4336 + $scoring__4342 + $scoring_total_4373 + $scoring_total_4392);
	 }
}
