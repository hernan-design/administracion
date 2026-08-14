<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log, Lang, Storage, DB;

use App\Models\Core\SectorInteres,
App\Models\Core\SectorInteresInstitucion;

class LoadCiiu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:loadciiu';

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
        $codigo_institucion = 79;        
        $actividades = SectorInteres::get();        
        foreach($actividades as $actividad){
            $nombre_actividad = $actividad->nombre_sector_interes;
            $codigo_actividad = $actividad->codigo_sector_interes;
            $sector_institucion = SectorInteresInstitucion::where('codigo_institucion', $codigo_institucion)->where('codigo_sector_interes', $codigo_actividad)->first();
            if(!$sector_institucion instanceof SectorInteresInstitucion) {
                $new_sector_institucion = new SectorInteresInstitucion;
                $new_sector_institucion->codigo_estado = 8;
                $new_sector_institucion->codigo_sector_interes = $codigo_actividad;
                $new_sector_institucion->codigo_institucion = $codigo_institucion;
                $new_sector_institucion->save();                
                \Log::info($codigo_actividad."-".$nombre_actividad."\n");
            }
        }
        dd("Salida");
    }
}
