<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\RegistroConvocatoria;

use DB, Log;

class ChangeNumberApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:changenumberapplication';

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
        $this->info("Buscando registros en la convocatoria.");

		$query = RegistroConvocatoria::query();
		$query->select('registro_convocatoria.numero_registro_convocatoria','registro_convocatoria.codigo_registro_convocatoria');
        $query->where('registro_convocatoria.codigo_convocatoria', Innpulsa::$challenge_aldea_2020);
        $query->orderBy('numero_registro_convocatoria','asc');
		$items = $query->get();

		DB::beginTransaction();
		try {
            // Notification
            $ultimo_numero = 6865;
			foreach ($items as $registerPhase) {
                $codigo_registro_convocatoria = $registerPhase->codigo_registro_convocatoria;
                if($registerPhase instanceof RegistroConvocatoria){
                    $ultimo_numero = $ultimo_numero + 1;

                    RegistroConvocatoria::where('codigo_registro_convocatoria', '=', $codigo_registro_convocatoria)
                    ->update(['numero_registro_convocatoria' => $ultimo_numero]);

                    $this->info("Registro numero: $codigo_registro_convocatoria -> $ultimo_numero");
                } else {
                    dd("Aqui encontre un error");
                }				
                
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
