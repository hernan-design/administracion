<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\RespuestaEncuesta, App\Models\Core\UsuarioEncuesta, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado, App\Models\Param\Institucion;

use DB, SendEmail, Log, DateTime;

class ReportNotAbsolute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:notabsolute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reportar emprendimientos no absolutos reto 1 Aldea iNNpulsa';

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
    	// Notificacion no absolutos
        $this->info("Buscando no absolutos.");

		$query = RegistroFaseUsuario::query();
		$query->select('registro_convocatoria.codigo_usuario_encuesta','fecha_registro_convocatoria','entrepreneurship.nombre_usuario','registro_fase_usuario.codigo_registro_fase_usuario', 'numero_registro_convocatoria', 'puntaje_total_encuesta', 'rango_inferior_evaluacion', 'entrepreneurship.nickname_usuario', DB::raw("(entrepreneur.nombre_usuario || ' ' || persona.primer_apellido_persona) as entrepreneur_name"), 'entrepreneur.correo_electronico', DB::raw("(EXTRACT(DAY FROM age(timestamp 'now()',date(fecha_registro_convocatoria) ) )) as dias"));
		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
		$query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');
		$query->join('seguridad.usuario as entrepreneurship', 'registro_convocatoria.codigo_usuario', '=', 'entrepreneurship.codigo_usuario');
		$query->join('core.entidad','entrepreneurship.codigo_usuario','=','entidad.codigo_usuario');
		$query->join('seguridad.usuario as entrepreneur','entidad.seg_codigo_usuario','=','entrepreneur.codigo_usuario');
		$query->join('core.persona','entrepreneur.codigo_usuario','=','persona.codigo_usuario');
		$query->where('registro_convocatoria.codigo_convocatoria', Innpulsa::$challenge_aldea_2020);
		$query->where('registro_fase_usuario.codigo_fase', Innpulsa::$stage_challenge_aldea_2020);
		$query->where('registro_fase_usuario.visualizacion_usuario', false);
		$query->whereIn('registro_fase_usuario.codigo_estado', [ Estado::getState('active') ]);
		$query->whereRaw('usuario_encuesta.puntaje_total_encuesta < fase.rango_inferior_evaluacion');
		$query->whereRaw("(EXTRACT(DAY FROM age(timestamp 'now()',date(fecha_registro_convocatoria) ) )) >= 5");
		$items = $query->get();

		DB::beginTransaction();
		try {
			// Notification
			foreach ($items as $registerPhase) {
				// $this->comment("Notificando: $registerPhase->numero_registro_convocatoria ($registerPhase->puntaje_total_encuesta <= $registerPhase->rango_inferior_evaluacion) $registerPhase->nickname_usuario");
				// $adicion_puntos = 0;
				// $fecha_equivocada = "";
				// $codigo_usuario_encuesta = $registerPhase->codigo_usuario_encuesta;				
				// $usuario_encuesta = UsuarioEncuesta::find($codigo_usuario_encuesta);
				// if($usuario_encuesta instanceof UsuarioEncuesta){
				// 	$respuesta_fecha_correcta = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4284)->first();
				// 	$respuesta_fecha_incorrecta = RespuestaEncuesta::select('texto_respuesta_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4283)->first();
				// 	if($respuesta_fecha_correcta instanceof RespuestaEncuesta){						
				// 		$today = new DateTime($registerPhase->fecha_registro_convocatoria);
				// 		$date_response_correct = new DateTime($respuesta_fecha_correcta->texto_respuesta_encuesta);
				// 		$diff__years_correct = $today->diff($date_response_correct);
				// 		if ($diff__years_correct->y <= 7) {
				// 			// se deben dar los puntos
				// 			if($respuesta_fecha_incorrecta instanceof RespuestaEncuesta){	
				// 				$date_response_incorrect = new DateTime($respuesta_fecha_incorrecta->texto_respuesta_encuesta);
				// 				$fecha_equivocada = $respuesta_fecha_incorrecta->texto_respuesta_encuesta;
				// 				$diff__years_incorrect = $today->diff($date_response_incorrect);
				// 				if ($diff__years_incorrect->y > 1 || $diff__years_incorrect->y < 7) {
				// 					// Sele dieron y si se deberian haber dado (no se hace nada)
				// 				} else{
				// 					// no se le dieron, pero se le deberian dar (le debo sumar 10pts)
				// 					$adicion_puntos = 10;
				// 				}
				// 			} else{
				// 				// no se le dieron puntos, pero deberia darselos (le debo sumar 10pts)
				// 				$adicion_puntos = 10;
				// 			}
				// 		} else{
				// 			// no se le deberian dar puntos
				// 			if($respuesta_fecha_incorrecta instanceof RespuestaEncuesta){						
				// 				$date_response_incorrect = new DateTime($respuesta_fecha_incorrecta->texto_respuesta_encuesta);
				// 				$fecha_equivocada = $respuesta_fecha_incorrecta->texto_respuesta_encuesta;
				// 				$diff__years_incorrect = $today->diff($date_response_incorrect);
				// 				if ($diff__years_incorrect->y > 1 || $diff__years_incorrect->y < 7) {
				// 					// Sele dieron y no se deberian haber dado (resto 10 pts)
				// 					$adicion_puntos = -10;
				// 				} else{
				// 					// no se le dieron, pero no se le deberian dar (no hago nada)
				// 				}
				// 			} else{
				// 				// no se le dieron puntos (no hago nada)
				// 			}
				// 		}						
				// 	}
					
				// 	if($adicion_puntos != 0){
				// 		$nuevo_puntaje = $usuario_encuesta->puntaje_total_encuesta + $adicion_puntos;
				// 		$usuario_encuesta->puntaje_total_encuesta = $nuevo_puntaje;						
				// 		$usuario_encuesta->save();
				// 		$this->comment("$registerPhase->numero_registro_convocatoria -> $registerPhase->fecha_registro_convocatoria - $respuesta_fecha_correcta->texto_respuesta_encuesta = $diff__years_correct->y | camara $fecha_equivocada modifico $adicion_puntos actual $usuario_encuesta->puntaje_total_encuesta nuevo $nuevo_puntaje");
				// 		// $this->comment("Aplicación: ".$registerPhase->numero_registro_convocatoria." -> ".$registerPhase->puntaje_total_encuesta." -> ".$nuevo_puntaje." -> ".$codigo_usuario_encuesta);
				// 	}
				// }

				$fecha_nueva_postulacion = "";
				if($registerPhase->fecha_registro_convocatoria != "")
					$fecha_nueva_postulacion = date("Y-m-d",strtotime($registerPhase->fecha_registro_convocatoria."+ 180 days"));

	        	$data = [];
				$data['_template'] = 'emails.program.notification';
				$data['_subject'] = 'Conoce los resultados de tu aplicación - PROGRAMA ALDEA';
				// Data Template
				$data['comment'] = trans('program.mail.body', ['emprendimiento' => $registerPhase->nombre_usuario, 'fecha_proxima_aplicacion' => $fecha_nueva_postulacion]);
				$data['_to'] = $registerPhase->correo_electronico;
				$data['_to_name'] = ucwords(strtolower($registerPhase->entrepreneur_name));
				$data['_inst'] = Institucion::institution('innpulsa');
				//$data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto', $registerPhase->nickname_usuario, 'convocatorias');
				$data['_url_action'] = sprintf("/%s/%s", 'convocatoria', 'aldea-20');
				// SendEmail::notification($data);
								
				// $registerPhase->visualizacion_usuario = true;
				// $registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
				// $registerPhase->notificacion_registro_fase_usuario = true;
				// $registerPhase->seg_codigo_usuario = 45352; // Soporte
				// $registerPhase->rango_inferior_eval_fase = $registerPhase->rango_inferior_evaluacion;
				// $registerPhase->codigo_estado = 26;
				// $registerPhase->save();
			}

			// Commit Transaction
			DB::commit();
			$this->info("Rutina finalizada con exito! ({$items->count()}) Notificados.");

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'ReportNotAbsolute::Command', $e->getMessage()));
			$this->error(sprintf('%s:%s', 'ReportNotAbsolute:Command', $e->getMessage()));
		}
    }
}
