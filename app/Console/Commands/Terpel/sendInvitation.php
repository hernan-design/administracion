<?php

namespace App\Console\Commands\Terpel;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use App\Models\Seguridad\Usuario, App\Models\Core\RegistroConvocatoria, App\Models\Core\UsuarioEncuesta;

class sendInvitation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'terpel:sendinvitation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia invitación a programa';

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
        $usuarios = $this->getUsers();        
        try{
            \Log::info("Usuarios encontrados ".$usuarios->count());    
            
            $data['_template'] = 'emails.mailing.mail';
            $data['_inst_name'] = 'Fundación Terpel - Diseña el cambio';
            $data['_subject'] = 'Agradecimiento y ganador 2020';
                    
            $contador_enviados = 0;
            $enviar = 0;
            $contador = 0;
            foreach($usuarios as $registrado){
                $email = $registrado->correo_electronico;
                    
                if($registrado->nombre_usuario != ""){
                    $nombre = $registrado->nombre_usuario;
                }
                else{
                    $nombre = "Apreciado usuario";
                }

                $data['_to'] =  $email;
                $data['_to_name'] = $nombre;                
                
                if($data['_to'] == 'joaquin@dioestudio.com'){
                    $enviar = 1;
                }

                // if($enviar == 1){                
                    Mail::send('emails.mailing.mail', $data, function($message) use($data) {
                        $message->from(config('tuproyecto.noreply'), $data['_inst_name']);
                        $message->subject($data['_subject']);
                        $message->to($data['_to'], isset($data['_to_name']) ? $data['_to_name'] : null);
                    }); 
                    $contador_enviados++;
                    \Log::info("Se envio: final ".$contador_enviados." -> ".$data['_to']);  
                // }                
            }
            \Log::info("Se termino el proceso de envio");
        }catch(\Exception $e) {
            \Log::error(sprintf('%s:%s', 'SendEmails:Command', $e->getMessage()));
        }
        dd("finalizo");
    }

    public static function getUsers() {

        $codigo_convocatoria = 436; // 203, 436
        $codigo_fase = 237; // 216, 237
        $codigo_institucion = 37;
        $terminos = 5; //39 Aldea, 25, coomeva, 76 Dinamiza, 30 Emprendepais, 5 Terpel
        $ciudades = ["Bogotá D.C.","CUNDINAMARCA","Cundinamarca","Norte de Santander","SANTANDER","Santander","Antioquia","Valle del Cauca", "Valle del cauca","Tolima"];
        $usuario = ['administracion@tuproyecto.com','marybell.gutierrez@terpel.com','silvia.madrinan@terpel.com','diego.gomez@tuproyecto.com'];
        $codigo_encuesta = 75; // 99, 75

        // $query = Usuario::query();
        // $query -> select("usuario.*");                
        // $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario'); 
        // $query->join('seguridad.aceptacion_terminos', 'usuario_institucion.codigo_usuario_institucion', '=', 'aceptacion_terminos.codigo_usuario_institucion');
        // // $query->join('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');        
        // $query -> where('usuario_institucion.codigo_institucion', '=', $codigo_institucion);
        // $query -> where('usuario_institucion.codigo_estado', '=', 8);
        // $query -> where('aceptacion_terminos.codigo_estado', '=', 8);        
        // // $query->whereNotIn('ciudad.distrito_ciudad',$ciudades);
        // $query -> where('aceptacion_terminos.codigo_version_terminos', '=', $terminos);            
        // $query -> whereNotNull('usuario.correo_electronico');
        // $query -> whereIn('usuario.correo_electronico', $usuario);
        // $query -> orderby('usuario.codigo_usuario', 'asc');

        $query = RegistroConvocatoria::query();
        $query->select('propietario.correo_electronico', 'propietario.nombre_usuario');                
        $query->join('seguridad.usuario as u', 'registro_convocatoria.codigo_usuario', '=', 'u.codigo_usuario'); 
        $query->join('core.entidad', 'u.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->join('seguridad.usuario as propietario', 'entidad.seg_codigo_usuario', '=', 'propietario.codigo_usuario');
        $query->join('core.registro_fase_usuario as rfu', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu.codigo_registro_convocatoria');
        $query->where('registro_convocatoria.codigo_convocatoria', '=', $codigo_convocatoria);
        $query->where('registro_convocatoria.codigo_estado', '=', 8);
        $query->where('rfu.codigo_fase', '=', $codigo_fase);
        // $query->where('rfu.codigo_estado', '=', 24);
        $query->whereNotNull('rfu.valor_asignacion_rfu');            
        $query->whereNotNull('propietario.correo_electronico');
        $query->groupBy('u.codigo_usuario', 'propietario.correo_electronico', 'propietario.nombre_usuario');
        $users = $query->get();
        return $users;
    }
}
