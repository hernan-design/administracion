<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;
use Custom\Innpulsa;
use App\Models\Core\RegistroFaseUsuario, App\Models\Core\Fase, App\Models\Core\Estado, App\Models\Core\PuntajeRegistroFase;

class UpdateScoringAldea20 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scoring:aldea20';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update scoring Aldea 20 for challenge 1.3';

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
        try {
            $query = RegistroFaseUsuario::query();
            $query->select('registro_fase_usuario.codigo_registro_fase_usuario', 'registro_convocatoria.codigo_usuario_encuesta', 'registro_convocatoria.numero_registro_convocatoria');
            $query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
            $query->join('core.asignacion_jurado', function($join) {
                $join->on('asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario')
                    ->where('asignacion_jurado.codigo_estado', Estado::getState('evaluated'));
            });
            $query->where('registro_fase_usuario.codigo_fase', Fase::getIdPhase('aldea__20_challenge__1_3'));
            $query->where('registro_fase_usuario.codigo_estado', Estado::getState('active'));
            $data = $query->distinct()->get();

            foreach ($data as $item) {
                $score = Innpulsa::get__Total_Response__Aldea20($item->codigo_usuario_encuesta, $item->codigo_registro_fase_usuario);
                $scoreRegisterPhase = PuntajeRegistroFase::where('codigo_registro_fase_usuario', $item->codigo_registro_fase_usuario)->first();

                if(!$scoreRegisterPhase instanceof PuntajeRegistroFase) {
                    \Log::error('No se encontro esta aplicacion en la tabla de puntaje');
                } else {
                    $scoreRegisterPhase->valor_puntaje_registro_fase = $score;
                    $scoreRegisterPhase->save();

                    \Log::info("PUNTAJE MODIFICADO ( numero_aplicacion: ".$item->numero_registro_convocatoria." ) - ( codigo_registro_fase_usuario: ".$item->codigo_registro_fase_usuario.' )');
                }

                $this->info("Puntajes actualizados correctamente");
            }
        } catch (\Exception $e) {
            $this->info("Algo salio mal");
            \Log::error($e->getMessage());
        }
    }
}
