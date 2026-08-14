<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado, App\Models\Core\RespuestaEncuesta, App\Models\Core\UsuarioEncuesta, App\Models\Param\Institucion;

use DB, SendEmail, Log;

class ReportNotAbsoluteEmpodera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:notabsoluteempodera';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reportar emprendimientos no absolutos reto 1 Aldea iNNpulsa Empodera';

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
        $this->info("Buscando no absolutos empodera.");

		$query = RegistroFaseUsuario::query();
		$query->select('usuario_encuesta.puntaje_total_encuesta','registro_convocatoria.codigo_usuario_encuesta','registro_fase_usuario.codigo_registro_fase_usuario', 'numero_registro_convocatoria', 'puntaje_total_encuesta', 'rango_inferior_evaluacion', 'entrepreneurship.nickname_usuario', DB::raw("(entrepreneur.nombre_usuario || ' ' || persona.primer_apellido_persona) as entrepreneur_name"), 'entrepreneur.correo_electronico', DB::raw("(EXTRACT(DAY FROM age(timestamp 'now()',date(fecha_registro_convocatoria) ) )) as dias"));
		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
		$query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');
		$query->join('seguridad.usuario as entrepreneurship', 'registro_convocatoria.codigo_usuario', '=', 'entrepreneurship.codigo_usuario');
		$query->join('core.entidad','entrepreneurship.codigo_usuario','=','entidad.codigo_usuario');
		$query->join('seguridad.usuario as entrepreneur','entidad.seg_codigo_usuario','=','entrepreneur.codigo_usuario');
		$query->join('core.persona','entrepreneur.codigo_usuario','=','persona.codigo_usuario');
		$query->where('registro_convocatoria.codigo_convocatoria', Innpulsa::$challenge_empodera_reto_1_2020);
		$query->where('registro_fase_usuario.codigo_fase', Innpulsa::$stage_challenge_empodera_1_2020);
		$query->where('registro_fase_usuario.visualizacion_usuario', false);
		$query->whereIn('registro_fase_usuario.codigo_estado', [ Estado::getState('active') ]);
		$query->whereRaw('usuario_encuesta.puntaje_total_encuesta < 0');
		$query->whereRaw("(EXTRACT(DAY FROM age(timestamp 'now()',date(fecha_registro_convocatoria) ) )) >= 5");
		$items = $query->get();		
		DB::beginTransaction();
		try {
			// Notification
			foreach ($items as $registerPhase) {
				$this->comment("Notificando: $registerPhase->numero_registro_convocatoria ($registerPhase->puntaje_total_encuesta < 0) $registerPhase->nickname_usuario");
				// $codigo_usuario_encuesta = $registerPhase->codigo_usuario_encuesta;				
				// $usuario_encuesta = UsuarioEncuesta::find($codigo_usuario_encuesta);
				// if($usuario_encuesta instanceof UsuarioEncuesta){
				// 	$respuesta_empleo = RespuestaEncuesta::select('codigo_opcion_encuesta')->where('codigo_usuario_encuesta',$codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4215)->first();			
				// 	if($respuesta_empleo->codigo_opcion_encuesta == 6896){
				// 		$nuevo_puntaje = $usuario_encuesta->puntaje_total_encuesta + 15;
				// 		// $usuario_encuesta->puntaje_total_encuesta = $nuevo_puntaje;						
				// 		// $usuario_encuesta->save();
				// 		// $this->comment("Aplicación: ".$registerPhase->numero_registro_convocatoria." -> ".$registerPhase->puntaje_total_encuesta." -> ".$nuevo_puntaje." -> ".$codigo_usuario_encuesta);
				// 	}
				// }

	        	$data = [];
				$data['_template'] = 'emails.program.notification_empodera';
				$data['_subject'] = 'Conoce los resultados de tu aplicación - iNNpulsa Empodera 2020';
				// Data Template
				$data['comment'] = trans('program.mail.body.empodera2020');
				$data['_to'] = $registerPhase->correo_electronico;
				$data['_to_name'] = ucwords(strtolower($registerPhase->entrepreneur_name));
				$data['_inst'] = Institucion::institution('innpulsa');				
				SendEmail::notification($data);
				
				$registerPhase->visualizacion_usuario = true;
				$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
				$registerPhase->notificacion_registro_fase_usuario = true;
				$registerPhase->seg_codigo_usuario = 45352; // Soporte
				$registerPhase->rango_inferior_eval_fase = $registerPhase->rango_inferior_evaluacion;
				$registerPhase->codigo_estado = 26;
				$registerPhase->save();
			}

			// Commit Transaction
			DB::commit();
			$this->info("Rutina finalizada con exito! ({$items->count()}) Notificados");

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'ReportNotAbsolute::Command', $e->getMessage()));
			$this->error(sprintf('%s:%s', 'ReportNotAbsolute:Command', $e->getMessage()));
		}
    }
}