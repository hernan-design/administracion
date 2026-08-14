<?php

namespace App\Console\Commands;

use App\Models\Core\ClaseTipoEvento,
    App\Models\Core\Evento,
    App\Models\Core\RecordatorioEvento as Recordatorios,
    App\Models\Core\RegistroConvocatoria,
    App\Models\Core\TipoEvento,
    App\Models\Param\Institucion,
    App\Models\Seguridad\Usuario,
    Illuminate\Console\Command;
use DB, Excel, Storage, SendEmail, Log, Lang;

class RecordatorioEvento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorios:recordatorioevento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de eventos';

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
      
        $query = Recordatorios::query();
        $query->select(
            'recordatorios_eventos.id',
            'recordatorios_eventos.codigo_evento',
            'recordatorios_eventos.enviado',
            'recordatorios_eventos.enviar',
            'evento.nombre_evento',
            'evento.fecha_evento',
            'evento.hora_inicio_evento',
            'evento.url_evento',
            'evento.url_meet_evento',
            'evento.others_urls_meeting',
            'usuario.nombre_usuario',
            'usuario.correo_electronico'
        );
        $query->join('core.evento as evento', 'recordatorios_eventos.codigo_evento', '=', 'evento.codigo_evento');
        
        $query->join('core.evento_institucion as ei', function ($join) {
            $join->on('recordatorios_eventos.codigo_evento', '=', 'ei.codigo_evento')
                 ->where('ei.codigo_estado', 8)
                 ->where('ei.codigo_institucion', 30);
        });

        $query->join('core.usuario_evento as ue', 'ue.codigo_evento', '=', 'recordatorios_eventos.codigo_evento');
        $query->join('core.usuario_institucion as ui', 'ui.codigo_usuario_institucion', '=', 'ue.codigo_usuario_institucion');
        $query->join('seguridad.usuario as usuario', 'usuario.codigo_usuario', '=', 'ui.codigo_usuario');

        $query->where('enviar', true);
        $query->whereNull('enviado');
        $dataQuery = $query->get();
       
        foreach ($dataQuery as $value) {

            if (isset($value->url_meet_evento)) {
                $url_meet = $value->url_meet_evento;
            } elseif (isset($$value->others_urls_meeting)) {
                $url_meet = $value->others_urls_meeting;
            } else {
                $url_meet = null;
            }

            $meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

            $newDate = $value->fecha_evento;
            $day = strftime("%d de ", strtotime($newDate));
            $date = strftime(" de %Y", strtotime($newDate));
            $date = $day . $meses[ date('n', strtotime( $newDate ) )-1] . $date ;

            $horaInicio = date_create($value->hora_inicio_evento);
            $horaInicio = date_format($horaInicio, "H:i A");

            if ($this->is_date($value->fecha_evento)) {

                $data = [];
                $data['_template']      = 'emails.reminders.reminderEvent'; 
                $data['_subject']       = ' Recordatorio de asistencia al evento';
                $data['_to']            = $value->correo_electronico;
                $data['_to_name']       = ucwords(strtolower($value->nombre_usuario));
                $data['_name_event']          = $value->nombre_evento;
                $data['_date']          = $date;
                $data['_hour']          = $horaInicio;   
                $data['_inst']          = Institucion::institution('compensar');
                $data['_url_event']     = 'https://emprendimiento.compensar.com/calendario/evento/' . $value->url_evento;
                $data['_url_reunion']   = $value->$url_meet;
                SendEmail::notification($data);

                Recordatorios::set_recordatorio_enviado($value->id);

            }else {
				Log::error('No se envió recordatorio ' . $value->codigo_evento . ' - ' . $this->is_date($value->fecha_evento) );
            }
        }
          
    }

    
    /**
     * Retorna un true o false si si la fecha recibida es igua al dia actual
     * @param string $date 
     */
    private function is_date($date){
        
        if (date('Y-m-d') == $date) {
            return true;
        } else {
            return false;
        }
    }

}
