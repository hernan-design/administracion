<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\RegistroFaseUsuario, 
App\Models\Core\Estado, 
App\Models\Core\RespuestaEncuesta, 
App\Models\Core\UsuarioEncuesta, 
App\Models\Param\Institucion,
App\Models\Core\Voucher;

use DB, SendEmail, Log;

class ValidateResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:validateresource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida los recursos disponibles en las llaves de acceso';

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
        $this->info("Buscando recursos a el programa");

		$query = RegistroFaseUsuario::query();
		$query->select('registro_convocatoria.codigo_registro_convocatoria','registro_fase_usuario.codigo_usuario','usuario_encuesta.puntaje_total_encuesta','registro_convocatoria.codigo_usuario_encuesta','registro_fase_usuario.codigo_registro_fase_usuario', 'numero_registro_convocatoria', 'puntaje_total_encuesta', 'rango_inferior_evaluacion', 'entrepreneurship.nickname_usuario', DB::raw("(entrepreneur.nombre_usuario || ' ' || persona.primer_apellido_persona) as entrepreneur_name"), 'entrepreneur.correo_electronico', DB::raw("(EXTRACT(DAY FROM age(timestamp 'now()',date(fecha_registro_convocatoria) ) )) as dias"));
		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
		$query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');
		$query->join('seguridad.usuario as entrepreneurship', 'registro_convocatoria.codigo_usuario', '=', 'entrepreneurship.codigo_usuario');
		$query->join('core.entidad','entrepreneurship.codigo_usuario','=','entidad.codigo_usuario');
		$query->join('seguridad.usuario as entrepreneur','entidad.seg_codigo_usuario','=','entrepreneur.codigo_usuario');
		$query->join('core.persona','entrepreneur.codigo_usuario','=','persona.codigo_usuario');
		$query->where('registro_convocatoria.codigo_convocatoria', Innpulsa::$challenge_aldea_2020);
		$query->where('registro_fase_usuario.codigo_fase', Innpulsa::$stage_challenge3_aldea_2020);
		$query->where('registro_fase_usuario.visualizacion_usuario', false);
		$query->whereIn('registro_fase_usuario.codigo_estado', [ Estado::getState('pendinginstitution') ]);
		$items = $query->get();		
		DB::beginTransaction();
		try {
			// Notification
			foreach ($items as $registerPhase) {
				
				$capitulo_aldea = RespuestaEncuesta::where('codigo_usuario_encuesta',$registerPhase->codigo_usuario_encuesta)->where('codigo_pregunta_encuesta',4501)->first();
				
				if($capitulo_aldea->codigo_opcion_encuesta == 7641){
					$codigo_voucher = 'V20200707-1';
				} else if($capitulo_aldea->codigo_opcion_encuesta == 7642){
					$codigo_voucher = 'V20200707-2';
				}

				if($codigo_voucher != ""){
					$llave_acceso = Voucher::where('codigo_voucher',$codigo_voucher)->first();
					$saldo_llave_acceso = $llave_acceso->saldo_voucher;
					if($saldo_llave_acceso > 60000000){

						// Insert Register Phase User
						$register = new RegistroFaseUsuario;
						$register->codigo_fase = Innpulsa::$stage_challenge4_aldea_2020;
						$register->codigo_usuario = $registerPhase->codigo_usuario;
						$register->fecha_registro_fase_usuario = date('Y-m-d');
						$register->hora_registro_fase_usuario = date('H:i:s');
						$register->codigo_estado = Estado::getState('active');
						$register->codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
						$register->visualizacion_usuario = false;
						$register->save();

						// Update current phase
						$registerPhase->visualizacion_usuario = true;
						$registerPhase->notificacion_cambio_registro_fase = date('Y-m-d H:m:s');
						$registerPhase->notificacion_registro_fase_usuario = true;
						$registerPhase->seg_codigo_usuario = 45352; // Soporte
						$registerPhase->rango_inferior_eval_fase = $registerPhase->rango_inferior_evaluacion;
						$registerPhase->codigo_estado = 21;
						$registerPhase->save();	
					}
					$this->comment("Capitulo: $capitulo_aldea->codigo_opcion_encuesta disponible: $saldo_llave_acceso");				
				}				
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
