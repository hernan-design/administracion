<?php

namespace App\Console\Commands\Compensar;

use App\Models\Core\ClaseTipoEvento;
use App\Models\Core\Evento;
use App\Models\Core\RegistroConvocatoria;
use App\Models\Core\TipoEvento;
use App\Models\Param\Institucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Console\Command;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\Validator;
use DB, Excel, Storage, SendEmail;

class Events extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compensar:events {user} {--startAt=} {--endAt=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make report of general events';

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
      // Valid user
      $user = Usuario::find($this->argument('user'));
      if(!$user instanceof Usuario) {
        throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
      }

      $query = Evento::query();

      $startAt = $this->option('startAt');
      $endAt = $this->option('endAt');

      $validator = Validator::make([
        'startAt' => $startAt,
        'endAt' => $endAt
      ], [
        'startAt' => 'required|date_format:Y-m-d',
        'endAt' => 'required|required_with:startAt|date_format:Y-m-d|after_or_equal:startAt'
      ]);

      if (!$validator->fails()) {
        $query->whereBetween('evento.fecha_evento', [$startAt, $endAt]);
      }

      $query->select(
          'evento.codigo_evento', 'evento.nombre_evento', 'evento.fecha_evento', 'evento.fecha_fin_evento', 'evento.lugar_evento', 'usuario.nombre_usuario',
          DB::raw("CONCAT( persona.primer_apellido_persona, ' ', persona.segundo_apellido_persona ) as apellido_usuario"),
          'usuario.movil_usuario', 'usuario.correo_electronico','usuario.identificacion_usuario',
          'tipo_identificacion.nombre_tipo_identificacion',
          'ciudad.nombre_ciudad',
          'usuario_evento.fecha_certificado',
          DB::raw("CASE usuario_evento.certificado_evento WHEN true THEN 'Entregado' ELSE 'Sin entregar' END certificado_evento"),
          'usuario_evento.codigo_usuario_evento as codigo_certificado',
          DB::raw("COUNT(asistencia_sesion.codigo_sesion_evento::text) as asistencia_usuario"),
          'evento.facilitator'
          );
      $query->join('core.evento_institucion', 'evento_institucion.codigo_evento', '=', 'evento.codigo_evento');
      $query->join('core.tipo_evento_calendario', 'tipo_evento_calendario.codigo_tipo_evento_calendario', '=', 'evento_institucion.codigo_tipo_evento_calendario');
      $query->leftJoin('core.usuario_evento', 'usuario_evento.codigo_evento', '=', 'evento.codigo_evento');
      $query->leftJoin('core.usuario_institucion', 'usuario_institucion.codigo_usuario_institucion', '=', 'usuario_evento.codigo_usuario_institucion');
      $query->leftJoin('core.asistencia_sesion', 'usuario_evento.codigo_usuario_evento', 'asistencia_sesion.codigo_usuario_evento');
      $query->leftJoin('core.sesion_evento', 'sesion_evento.codigo_sesion_evento', '=', 'asistencia_sesion.codigo_sesion_evento');
      $query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario');
      $query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'usuario.codigo_usuario');
      $query->leftJoin('core.tipo_identificacion', 'tipo_identificacion.codigo_tipo_identificacion', '=', 'usuario.tipo_identificacion_usuario');
      $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');
      $query->where('evento_institucion.codigo_institucion', '=', Institucion::institution('compensar'));
      $query->where('tipo_evento_calendario.codigo_clase_tipo_evento', '=', ClaseTipoEvento::getClass('platform'));
      $query->groupBy(
          'evento.nombre_evento',  'evento.fecha_evento', 'usuario.nombre_usuario',
          'persona.primer_apellido_persona', 'persona.segundo_apellido_persona',
          'usuario.movil_usuario', 'usuario.correo_electronico',
          'usuario.identificacion_usuario', 'tipo_identificacion.nombre_tipo_identificacion',
          'ciudad.nombre_ciudad', 'usuario_evento.certificado_evento', 'usuario_evento.fecha_certificado',
          'usuario_evento.codigo_usuario_evento','evento.fecha_evento', 'evento.lugar_evento',
          'evento.codigo_evento'
          );
      $query->orderBy('evento.codigo_evento', 'DESC');
      $dataQuery = $query->get();
      $filename = sprintf('%s_%s.xlsx', 'reporte_general_evento', date('YmdHms'));
      $filepath = sprintf('reports/%s/%s/%s', Institucion::institution('compensar'), $user->codigo_usuario, $filename);

      $array = array(
        'Código de evento',
        'Nombre Evento',
        'Fecha evento',
        'Fecha fin',
        'Lugar Evento',
        'Nombres',
        'Apellidos',
        'Celular',
        'Correo Electronico',
        'Numero de Documento',
        'Tipo de identificacion',
        'Ciudad',
        'Fecha entrega certificado',
        'Certificado entregado',
        'Código certificado',
        'Asistencias',
        'Facilitador'
      );


      $export = new ReportsExport([$array], [$dataQuery]);

      // Genera el archivo Excel y guárdalo en storage/app/reports
      $file = Excel::store($export, $filepath, 's3', null, [
        'visibility' => 'public',
      ]);


    $fileUrl = Storage::disk('s3')->url($filepath);

      // Send mail
      $data = [];
      $data['_template'] = 'emails.report';
      $data['_subject'] = 'Reporte general eventos';
      $data['_to'] = $user->correo_electronico;
      $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
      $data['_inst'] = Institucion::institution('compensar');
      $data['_url'] = $fileUrl;
      SendEmail::notification($data);
    }
}
