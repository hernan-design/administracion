<?php
/*
 * Creacion de reporte de eventos
 * Creator @Brahian Sánchez
 */
namespace App\Console\Commands\Innpulsa;
use App\Models\Core\Ciudad;
use App\Models\Core\UsuarioEvento;
use Illuminate\Console\Command;
use App\Exports\ReportsExport;
use App\Models\Seguridad\Usuario,
	App\Models\Core\Evento,
	App\Models\Core\SesionEvento,
	App\Models\Param\Institucion,
	App\Models\Core\RegistroConvocatoria,
	App\Models\Core\Estado;
use DB, Excel, Storage, SendEmail;

class ReportEvents extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'innpulsa:events {user}{event}{institution}';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reporte seguimiento eventos';
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
		try {
			$user = Usuario::find($this->argument('user'));
			$institution = $this->argument('institution');
			if(!$user instanceof Usuario) {
				throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
			}
			$query = UsuarioEvento::query();
			$querySesionOfEvent = SesionEvento::query();
			$query->select('usuario.nombre_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'usuario.identificacion_usuario', 'usuario.correo_electronico', 'usuario.movil_usuario','tipo_identificacion.nombre_tipo_identificacion', 'ciudad.nombre_ciudad', 'usuario_evento.asistencia_evento', 'usuario_evento.codigo_evento', 'usuario_institucion.codigo_institucion', 'usuario_institucion.codigo_usuario_institucion',
				DB::raw("string_agg(asistencia_sesion.codigo_sesion_evento::text, ',') as sesiones_usuario"),
				DB::raw("string_agg(sesion_evento.numero_sesion::text, ',' ORDER BY sesion_evento.numero_sesion ASC) as num_sesiones"),
        		'usuario_evento.certificado_evento', 'usuario_evento.fecha_certificado', 'usuario_evento.codigo_usuario_evento'
			);
			$query->leftJoin('core.asistencia_sesion', 'usuario_evento.codigo_usuario_evento', 'asistencia_sesion.codigo_usuario_evento');
			$query->join('core.usuario_institucion', 'usuario_evento.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');
			$query->leftJoin('core.sesion_evento', 'sesion_evento.codigo_sesion_evento', '=', 'asistencia_sesion.codigo_sesion_evento');
			$query->join('seguridad.usuario', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
			$query->join('core.persona', 'persona.codigo_usuario', '=', 'usuario.codigo_usuario');
			$query->leftJoin('core.tipo_identificacion', 'usuario.tipo_identificacion_usuario', '=', 'tipo_identificacion.codigo_tipo_identificacion');
			$query->leftJoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
			$query->where('usuario_evento.codigo_evento', '=', $this->argument('event'));
			$query->groupBy('usuario_evento.codigo_usuario_evento','usuario.nombre_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'usuario.identificacion_usuario', 'usuario.correo_electronico', 'usuario.movil_usuario','tipo_identificacion.nombre_tipo_identificacion', 'ciudad.nombre_ciudad', 'usuario_evento.asistencia_evento', 'usuario_evento.codigo_evento', 'usuario_institucion.codigo_institucion', 'usuario_institucion.codigo_usuario_institucion', 'usuario_evento.certificado_evento', 'usuario_evento.fecha_certificado');
			$data = $query->get();

			$querySesionOfEvent->select('sesion_evento.codigo_evento',
				DB::raw("sesion_evento.numero_sesion::text"),
				DB::raw("sesion_evento.codigo_sesion_evento::text"),
				DB::raw("string_agg(beneficio_evento.codigo_beneficio_evento::text, ',') as codigo_beneficio")
			);
			$querySesionOfEvent->leftJoin('core.asistencia_sesion', 'asistencia_sesion.codigo_sesion_evento', '=', 'sesion_evento.codigo_sesion_evento');
			$querySesionOfEvent->leftJoin('core.entrega_beneficio', 'entrega_beneficio.codigo_asistencia_sesion', '=', 'asistencia_sesion.codigo_asistencia_sesion');
			$querySesionOfEvent->leftJoin('core.lista_beneficio_evento', 'lista_beneficio_evento.codigo_lista_ben_even', '=', 'entrega_beneficio.codigo_lista_ben_even');
			$querySesionOfEvent->leftJoin('core.beneficio_evento', 'beneficio_evento.codigo_beneficio_evento', '=', 'lista_beneficio_evento.codigo_beneficio_evento');
			$querySesionOfEvent->where('sesion_evento.codigo_evento', '=',  $this->argument('event'));
			$querySesionOfEvent->orderBy('sesion_evento.numero_sesion', 'asc');
			$querySesionOfEvent->groupBy('sesion_evento.numero_sesion', 'sesion_evento.codigo_sesion_evento');
			$dataSessionOfEvent = $querySesionOfEvent->get();

			//facilitator
			$query = Evento::query();
			$query->select('facilitator');
			$query->where('codigo_evento', $this->argument('event'));
			$facilitator = $query->get()->toArray();
			$facilitator = $facilitator[0]['facilitator'] ? $facilitator[0]['facilitator'] : '';

			$filename = sprintf('%s_%s.xlsx', 'reporte_evento', date('YmdHms'));
			$filepath = sprintf('reports/%s/%s/%s', $institution, $user->codigo_usuario, $filename);

      $header = array(
        'Nombres',
        'Apellidos',
        'Celular',
        'Correo Electronico',
        'Numero de Documento',
        'Tipo de identificacion',
        'Ciudad',
              'Fecha entrega certificado',
        'Certificado entregado',
              'Codigo certificado'
      );

      $arrayData = array();


      if(count($dataSessionOfEvent) >= 1) {
        $num_sesion_ev = array();

        // Add to Array that Sessions

        foreach ($dataSessionOfEvent as $valueSesionEvent) {
          array_push($header, "Sesion ". $valueSesionEvent->numero_sesion);
          array_push($num_sesion_ev, $valueSesionEvent->numero_sesion);
        }

        // Include rest data of Users

        foreach ($data as $value) {

          $rowData = [
            $value->nombre_usuario,
            $value->primer_apellido_persona.' '.$value->segundo_apellido_persona,
            $value->movil_usuario,
            $value->correo_electronico,
            $value->identificacion_usuario,
            $value->nombre_tipo_identificacion,
            $value->nombre_ciudad,
                    isset($value->fecha_certificado) ? $value->fecha_certificado : '',
                    isset($value->certificado_evento) && $value->certificado_evento ? 'Entregado' : 'Sin entregar',
                    isset($value->certificado_evento) && $value->certificado_evento ? $value->codigo_usuario_evento : ''
          ];

          $facilitator = isset($value->facilitator) ? $value->facilitator : '';

          // Obtain sessions of Users with Explode

          $sesion_user = explode(',', $value->sesiones_usuario);
          if(isset($value->sesiones_usuario)) {
            $num_sesions = explode(',', $value->num_sesiones);

            /*
              Init counter in 0 and validate value of Explode ( Session User ), If the index in array exist, and is equal to Session of Event show response if Assist of did not Assist, and increment the counter.
            */
            $countSession = 0;
            foreach ($num_sesion_ev as $itemSession) {
              if (isset($num_sesions[$countSession])) {
                if ($num_sesions[$countSession] == $itemSession) {
                  $rowData[] = 'Asistio';

                  $countSession++;
                } else {
                  $rowData[] = 'Sin asistencia';
                }
              } else {
                $rowData[] = 'Sin asistencia';
              }
            }

          }

          $arrayData[] = $rowData;
        }
      } else {

        // Validate if not exist Sessions in event

        array_push($header, 'Sesion');

        foreach ($data as $value) {
          $arrayData [] = [
            $value->nombre_usuario,
                    $value->primer_apellido_persona.' '.$value->segundo_apellido_persona,
            $value->movil_usuario,
            $value->correo_electronico,
            $value->identificacion_usuario,
            $value->nombre_tipo_identificacion,
            $value->nombre_ciudad,
            isset($value->fecha_certificado) ? $value->fecha_certificado : '',
            isset($value->certificado_evento) && $value->certificado_evento ? 'Entregado' : 'Sin entregar',
                    isset($value->certificado_evento) && $value->certificado_evento ? $value->codigo_usuario_evento : '',
            'No hay sesiones asignadas para el evento'
          ];
        }
      }

      $export = new ReportsExport([$header], [$arrayData]);

      // Genera el archivo Excel
      $file = Excel::store($export, $filepath, 's3', null, [
        'visibility' => 'public',
      ]);

      $fileUrl = Storage::disk('s3')->url($filepath);


			// Send mail
			$data = [];
			$data['_template'] = 'emails.report';
			$data['_subject'] = 'Reporte evento';
			$data['_to'] = $user->correo_electronico;
			$data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
			$data['_inst'] = $institution;
			$data['_url'] = $fileUrl;
			SendEmail::notification($data);
		} catch (\Exception $e) {
			\Log::error(sprintf('%s:%s', 'ReportEvents:Command', $e->getMessage()));
			abort(500);
		}
	}
}
