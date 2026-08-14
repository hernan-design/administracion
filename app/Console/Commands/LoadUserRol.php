<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log, Lang, Storage, DB;

use App\Models\Seguridad\RolUsuario,
App\Models\Core\UsuarioInstitucion,
App\Models\Core\Estado;

class LoadUserRol extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:loadroluser';

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
        $codigo_institucion = 30;
        $contador = 0;
        $id_role = 53;
        $id_role_asociado = 39;
        $usuarios = UsuarioInstitucion::where('codigo_institucion',$codigo_institucion)->get();        
        foreach($usuarios as $usuario){
            $codigo_usuario = $usuario->codigo_usuario;
            $userRol = RolUsuario::where('codigo_institucion', $codigo_institucion)->where('role_id', $id_role)->where('user_id', $codigo_usuario)->first();
            if(!$userRol instanceof RolUsuario) {
                $userRol = new RolUsuario;
                $userRol->role_id = $id_role;
                $userRol->user_id = $codigo_usuario;
                $userRol->codigo_institucion = $codigo_institucion;
                $userRol->save();                
                \Log::info("Se inserto el usuario: ".$codigo_usuario."\n");
            }

            $userRolAsociado = RolUsuario::where('codigo_institucion', $codigo_institucion)->where('role_id', $id_role_asociado)->where('user_id', $codigo_usuario)->first();
            if(!$userRolAsociado instanceof RolUsuario) {
                $userRolAsociado = new RolUsuario;
                $userRolAsociado->role_id = $id_role_asociado;
                $userRolAsociado->user_id = $codigo_usuario;
                $userRolAsociado->codigo_institucion = $codigo_institucion;
                $userRolAsociado->save();
                \Log::info("Se inserto asociado: ".$codigo_usuario."\n");
                $contador++;
            }
        }
        \Log::info("Total: ".$contador."\n");
        dd("Salida");
    }
}
