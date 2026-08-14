<?php

namespace App\Console\Commands\Promotions;

use Illuminate\Console\Command;

use App\Models\Core\Promocion, App\Models\Core\DetallePromocion, App\Models\Core\Estado;

class ChangeStatePromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotions:state';

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
        try {
            $query = Promocion::select('codigo_promocion', 'fecha_fin_promocion', 'codigo_estado')->where('tipo_promocion', Promocion::getTypePromo('promotion'))->get();
            foreach ($query as $item) {
                if($item->fecha_fin_promocion < date('Y-m-d')) {
                    Promocion::where('codigo_promocion', $item->codigo_promocion)
                        ->update(['codigo_estado' => Estado::getState('inactive')]);

                    DetallePromocion::where('codigo_promocion', $item->codigo_promocion)
                        ->update(['codigo_estado' => Estado::getState('inactive')]);

                    \Log::info("PROMOCION CADUCADA SU ESTADO CAMBIA (Codigo Promocion: ".$item->codigo_promocion.' - Fecha fin de promocion: '.$item->fecha_fin_promocion.' )');
                } else {
                    \Log::info("PROMOCION NO CADUCADA SU ESTADO SE MANTIENE (Codigo Promocion: ".$item->codigo_promocion.' - Fecha fin de promocion: '.$item->fecha_fin_promocion.' )');
                }
            }

            $this->info("Promociones actualizadas correctamente");
        } catch (\Exception $e) {
            $this->info("Algo salio mal");
            Log::error($e->getMessage());
        }
    }
}
