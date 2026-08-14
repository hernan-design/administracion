<?php

namespace App\Console\Commands\Redcoomeva;

use Illuminate\Console\Command;

use Artisaninweb\SoapWrapper\SoapWrapper;

use App\Models\Seguridad\Usuario, App\Models\Seguridad\RolUsuario, App\Models\Core\Estado;

use DB, Log;

class SyncMembership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redcoomeva:syncmembership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar el estado de asociado de los usuarios';

    /**
     * The soap wrapper instance.
     *
     * @var SoapWrapper
     */
    protected $soapWrapper;
      
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SoapWrapper $soapWrapper)
    {
        parent::__construct();

        $this->soapWrapper = $soapWrapper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Buscando usuarios
        $this->info("Buscando usuarios en datosAsociadoVida.");
        
        DB::beginTransaction();
        try {
            $query = Usuario::query();
            // User institution
            $query->join('core.usuario_institucion', function($join) {
                $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                    ->where('usuario_institucion.codigo_institucion', '=', 29)
                    ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active'));
            });
            $query->whereNotIn('usuario.codigo_usuario', [82730, 82737, 233896, 238918]);
            $query->whereNotNull('usuario.identificacion_usuario');
            // $query->where('usuario_institucion.usuario_verificado', true);
            $query->orderBy('usuario.codigo_usuario', 'asc');
            $users = $query->get();

            // Prepare soap
            $context = stream_context_create([
                // set some SSL/TLS specific options
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            $this->soapWrapper->add('RedCoomeva', function ($service) use($context) {
                $service->wsdl('https://secure.coomeva.com.co/WsAsociado/services/datosAsociadoVida?wsdl')
                ->options([ 'stream_context' => $context ]);
            });

            foreach ($users as $user) {
                // Without classmap
                $soapResponse = $this->soapWrapper->call('RedCoomeva.consultarAsociadoVida', [
                    'identificacion' => $user->identificacion_usuario,
                    'tipDocumento' => $user->tipo_identificacion_usuario
                ]);

                if($soapResponse->codRetornoWS) {
                    if(isset($soapResponse->estado) && in_array($soapResponse->estado, [10, 11])) {
                        // Status 10, 11 add role
                        $userRol = RolUsuario::where('codigo_institucion', 29)->where('role_id', 39)->where('user_id', $user->codigo_usuario)->first();
                        if(!$userRol instanceof RolUsuario) {
                            $userRol = new RolUsuario;
                            $userRol->role_id = 39;
                            $userRol->user_id = $user->codigo_usuario;
                            $userRol->codigo_institucion = 29;
                            $userRol->save();

                            $this->info("Activación de suscripcion $user->correo_electronico, estado actual $soapResponse->estado.");
                        }
                    }else{
                        // Diferent status remove rol
                        $userRol = RolUsuario::where('codigo_institucion', 29)->where('role_id', 39)->where('user_id', $user->codigo_usuario)->first();
                        if($userRol instanceof RolUsuario) {
                            $userRol = RolUsuario::where('codigo_institucion', 29)->where('role_id', 39)->where('user_id', $user->codigo_usuario)->delete();
                            $this->info("Inactivación de suscripcion $user->correo_electronico, estado actual $soapResponse->estado.");             
                        }  
                    }
                }
            }

            // Commit Transaction
            DB::commit();
            // DB::rollback();
        }catch(\Exception $e){
            DB::rollback();
            Log::error(sprintf('%s:%s', 'SyncMembership:Command', $e->getMessage()));
        }
    }
}
