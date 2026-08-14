<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\RegistroFaseUsuario, App\Models\Core\Estado, App\Models\Core\RespuestaEncuesta, App\Models\Core\UsuarioEncuesta, App\Models\Core\AsignacionJurado, App\Models\Param\Institucion;

use DB, SendEmail, Log;

class AssignmentEmpodera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:assignmentempodera';

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
		$query->whereRaw('usuario_encuesta.puntaje_total_encuesta >= 0');
		// $query->whereRaw("(EXTRACT(DAY FROM age(timestamp 'now()',date(fecha_registro_convocatoria) ) )) >= 5");
		$items = $query->get();		
		DB::beginTransaction();
		try {
			// Notification
			foreach ($items as $registerPhase) {
                
                $assignment = AsignacionJurado::where('codigo_registro_fase_usuario',$registerPhase->codigo_registro_fase_usuario)->first();

                if(!$assignment instanceof AsignacionJurado){
                    $this->comment("Notificando: $registerPhase->codigo_registro_fase_usuario ($registerPhase->puntaje_total_encuesta > 0) $registerPhase->nickname_usuario");
                    $juries = collect([352236,360448]);

                    // Insert Assignment
                    $new_assignment = new AsignacionJurado;				
                    $new_assignment->codigo_usuario =  $juries->random();				
                    $new_assignment->codigo_estado = Estado::getState('active');
                    $new_assignment->fecha_asignacion_jurado = date('Y-m-d');
                    $new_assignment->hora_asignacion_jurado = date('H:i:s');
                    $new_assignment->codigo_registro_fase_usuario = $registerPhase->codigo_registro_fase_usuario;
                    $new_assignment->save();
                }
                // Commit Transaction
			}

			DB::commit();
			$this->info("Rutina finalizada con exito! ({$items->count()}) Notificados");

		}catch(\Exception $e){
			DB::rollback();
			Log::error(sprintf('%s:%s', 'ReportNotAbsolute::Command', $e->getMessage()));
			$this->error(sprintf('%s:%s', 'ReportNotAbsolute:Command', $e->getMessage()));
		}
    }
}
