<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Core\Asesoria, App\Models\Seguridad\Usuario, App\Models\Param\Dashboard;

use DB;

class PercentageAccompaniments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:percentageaccompaniments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates the percentage of accompaniments in the institutions';

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
        $query = Asesoria::query();
        $query->select(
            'asesoria_institucion.codigo_institucion', 
            DB::raw("sum((CASE WHEN ((CURRENT_DATE - fecha_asesoria) >= 3 AND (CURRENT_DATE - fecha_asesoria) < 6) THEN 1 ELSE 0 END)) as tdias"),
            DB::raw("sum((CASE WHEN (CURRENT_DATE - fecha_asesoria) >= 6 THEN 1 ELSE 0 END)) as sdias"),
            DB::raw("sum((CASE WHEN (CURRENT_DATE - fecha_asesoria) < 3 THEN 1 ELSE 0 END)) as cdias")
        );
        $query->join('aprende.asesoria_institucion', 'asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria');
        $query->where('asesoria_institucion.codigo_estado', '=', 8);
        $query->groupBy('asesoria_institucion.codigo_institucion');
        $asesorias = $query -> get();

        foreach($asesorias as $asesoria){
            $dashboard = Dashboard::where('codigo_institucion',$asesoria->codigo_institucion)->first();
            if($dashboard instanceof Dashboard){
                $total = $asesoria->tdias+$asesoria->sdias+$asesoria->cdias;
                $dashboard->numero_total_acompanamientos = $total;
                $dashboard->numero_acompanamientos_tres = $asesoria->tdias;
                $dashboard->numero_acompanamientos_seis = $asesoria->sdias;
                $dashboard->numero_acompanamientos_menos_tres = $asesoria->cdias;
                $dashboard->save();
            }
            
            // \Log::info("dias ".$asesoria->codigo_institucion." numero de comentarios ".$asesoria->tdias." ".$asesoria->sdias." ".$asesoria->cdias);
        }
    }
}
