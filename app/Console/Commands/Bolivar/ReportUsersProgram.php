<?php

namespace App\Console\Commands\Bolivar;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;
use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario;

class ReportUsersProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bolivar:reportprogram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporta los usuarios que pasaron a la siguiente fase y notifica';

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
        Excel::load('magdalena.xlsx', function($reader){
            $results = $reader->get()->toArray();
		    foreach($results as $resul){
                $correo = strtolower(trim($resul['correo']));
                $usuario = $this->getEmailUsuario($correo);
                if($usuario instanceof Usuario){
                    $enviar = 0;
                    $email = $usuario->correo_electronico;
                    
                    if($usuario->nombre_usuario != ""){
                        $nombre = $usuario->nombre_usuario;
                    }
                    else{
                        $nombre = "Apreciado usuario";
                    }

                    $data['_template'] = 'emails.mailing.mail';
                    $data['_inst_name'] = 'Emprende País - Fundación Bolívar Davivienda';
                    $data['_subject'] = 'Bienvenida al enrutamiento virtual de empresas preseleccionadas Emprende País Magdalena';

                    $data['_to'] =  $email;
                    $data['_to_name'] = $nombre;                
                    
                    if($data['_to'] == 'joaquin@dioestudio.com'){
                        $enviar = 1;
                    }

                    // if($data['_to'] != 'luisa.ibanez@aguacontumarca.com'){                
                        Mail::send($data['_template'], $data, function($message) use($data) {
                            $message->from('info@emprendepais.com', $data['_inst_name']);                            
                            $message->subject($data['_subject']);
                            $message->to($data['_to'], isset($data['_to_name']) ? $data['_to_name'] : null);
                        }); 
                        \Log::info("Se envio: -> ".$data['_to']);  
                    // } 
                } else{
                    // \Log::info("Correo no registrado: ".$correo."\n");
                }
                // dd("Finalizo");
                // exit();
            }
            \Log::info("Llego al final \n");
        });
        dd("Finalizo");
    }

    public static function getEmailUsuario($email) {
        return Usuario::where('correo_electronico', '=',  $email) -> first();
    }
}