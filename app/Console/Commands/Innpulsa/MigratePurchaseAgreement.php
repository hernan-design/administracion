<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Log, Storage, Excel, DB, DateTime;

use App\Models\Core\AcuerdoCompra,
    App\Models\Core\Compra;

class MigratePurchaseAgreement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:migratepurchaseagreement';

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
        $acuerdos_compra = AcuerdoCompra::get();
        foreach($acuerdos_compra as $acuerdo_compra){
            $codigo_acuerdo_compra = $acuerdo_compra->codigo_acuerdo_compra;
            $codigo_compra = $acuerdo_compra->codigo_compra;
            $compra = Compra::select('compra.codigo_compra','vendedor.correo_electronico as correo_electronico', 'vendedor.codigo_usuario as id_vendedor')
                            ->join('seguridad.usuario as vendedor','compra.seg_codigo_usuario','=','vendedor.codigo_usuario')
                            // ->join('core.entidad','vendedor.codigo_usuario','=','entidad.seg_codigo_usuario')
                            // ->join('seguridad.usuario as u_entidad','entidad.codigo_usuario','=','u_entidad.codigo_usuario')
                            ->where('codigo_compra',$codigo_compra)
                            ->first();
            if($acuerdo_compra instanceof AcuerdoCompra){
                if($compra->id_vendedor){
                    $acuerdo_compra->seg_codigo_usuario = $compra->id_vendedor;
                    $acuerdo_compra->save();
                }
            }
            \Log::info("Codigo: ".$compra->codigo_compra." vendedor: ".$compra->correo_electronico);
            // ,'u_entidad.nombre_usuario as empresa'
        }
        dd("Finalizar");
    }
}
