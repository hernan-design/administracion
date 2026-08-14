<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use Log, Storage, Excel, DB;

use App\Models\Core\DetalleVoucher,
    App\Models\Core\Compra,
    App\Models\Core\DevolucionVoucher;

class CloseAgreement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:closeagreement';

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
        $llaves = DetalleVoucher::where('codigo_estado',8)->where('fecha_detalle_voucher','>=','2019-11-01')->orderBy('fecha_detalle_voucher')->get();
        foreach($llaves as $llave){
            $codigo_llave = $llave->codigo_detalle_voucher;
            $fecha_llave = $llave->fecha_detalle_voucher;
            $codigo_comprador = $llave->codigo_usuario;
            $saldo_llave = $llave->saldo_detalle_voucher;
            $nuevafecha = strtotime ( '+15 day' , strtotime ( $fecha_llave ) ) ;
            $fecha_cierre_llave = date ( 'Y-m-d' , $nuevafecha );

            if($fecha_cierre_llave <= date('Y-m-d') && $saldo_llave > 0){
                
                $devolucion_voucher = new DevolucionVoucher;
                $devolucion_voucher->codigo_detalle_voucher = $codigo_llave;
                $devolucion_voucher->codigo_estado = 8;
                $devolucion_voucher->codigo_usuario = $codigo_comprador;
                $devolucion_voucher->codigo_voucher = 'V20180213-1';
                $devolucion_voucher->fecha_devolucion_voucher = date('Y-m-d');
                $devolucion_voucher->motivo_devolucion_voucher = 'Vencimiento de tiempo';
                $devolucion_voucher->valor_devolucion_voucher = $saldo_llave;
                $devolucion_voucher->save();

                $llave->saldo_detalle_voucher = 0;
                $llave->save();
                // Se quita el saldo de las llaves de acceso y se regresa a la bolsa de innpulsa
            }
            // \Log::info("Fecha llave: $fecha_llave Fecha cierre $fecha_cierre_llave \n");
            $compra = Compra::select('ac.codigo_compra','compra.codigo_producto','acu.fecha_final_actividad_acuerdo')
                        ->where('compra.codigo_usuario',$codigo_comprador)
                        ->join('core.acuerdo_compra as ac','compra.codigo_compra','=','ac.codigo_compra')
                        ->join('core.actividad_acuerdo as acu','ac.codigo_acuerdo_compra','=','acu.codigo_acuerdo_compra')
                        // ->where('acu.codigo_estado',8)
                        ->where('acu.codigo_estado','!=',9)
                        ->where('compra.codigo_estado','!=',13)
                        ->orderBy('acu.fecha_final_actividad_acuerdo', 'DESC')
                        ->orderBy('acu.codigo_actividad_acuerdo','DESC')
                        ->first();

            if($compra instanceof Compra){
                $codigo_compra = $compra->codigo_compra;
                $codigo_producto = $compra->codigo_producto;
                $fecha_fin_actividad_acuerdo = $compra->fecha_final_actividad_acuerdo;
                if($fecha_fin_actividad_acuerdo <= date('Y-m-d')){
                    // Se cambia el estado del voucher y se activa el middleware
                    $llave->codigo_estado = 29;
                    $llave->save();
                }                
            }
        }
        \Log::info("Fin!! \n");
        exit();
    }
}
