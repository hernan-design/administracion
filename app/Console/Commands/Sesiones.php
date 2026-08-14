<?php

namespace App\Console\Commands;

use App\Models\Core\Asesoria,
    App\Models\Aprende\Mentoria,
    App\Models\Param\Institucion,
    App\Models\Seguridad\Usuario,
    App\Models\Core\Estado,
    App\Models\Aprende\TareaMentoria,
    App\Models\Aprende\TagSesionMentoria,
    Illuminate\Console\Command;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\Validator;
use Log, DB, Excel, Storage, SendEmail;

class Sesiones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:sesiones {user} {institution} {--startAt=} {--endAt=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make report of general of Sessions';

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

      $institution = Institucion::find($this->argument('institution'));
      if(!$institution instanceof Institucion) {
        throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
      }

      $query = Mentoria::query();

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
          DB::raw("CONCAT( mentor.nombre_usuario, ' ', p_mentor.primer_apellido_persona ) as nombre_mentor"),
          DB::raw("CONCAT( usuario.nombre_usuario, ' ', p_usuario.primer_apellido_persona ) as nombre_emprendedor"),
          'usuario.identificacion_usuario as cedula',
          'usuario.correo_electronico as correo_electronico',
          'usuario.movil_usuario as movil_usuario',
          'proyecto.nombre_usuario as nombre_proyecto',
          'ep.nombre_etapa_proyecto',
          'proyecto.biografia_usuario as descripcion_proyecto',
          'evento.fecha_evento as fecha_sesion',
          'evento.hora_inicio_evento as hora_inicio',
          'evento.hora_fin_evento as hora_fin',
          'om.descripcion_objetivo_mentoria as tema_sesion',
          'mentoria.objetivo_mentor as observaciones',
          'mentoria.fecha_finalizo_mentoria as aceptacion_sesion',
          'mentoria.fecha_finalizo_mentoria',
          'mentoria.codigo_mentoria as codigo_sesion',
          'mentoria.codigo_estado',
          'mentoria.descripcion_mentoria',
          'mentoria.duration_mentoring'
      );
      $query->join('aprende.mentoria_institucion as mi', 'mentoria.codigo_mentoria', '=', 'mi.codigo_mentoria');
      $query->join('core.asesoria as asesoria', 'mentoria.codigo_asesoria', '=', 'asesoria.codigo_asesoria');
      //Emprendedor
      $query->join('seguridad.usuario as usuario', 'mentoria.codigo_usuario', '=', 'usuario.codigo_usuario');
      $query->join('core.persona as p_usuario', 'usuario.codigo_usuario', '=', 'p_usuario.codigo_usuario');
      //Mentor
      $query->join('seguridad.usuario as mentor', 'mentoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
      $query->join('core.persona as p_mentor', 'mentor.codigo_usuario', '=', 'p_mentor.codigo_usuario');
      //Proyecto
      $query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
      $query->join('seguridad.usuario as proyecto', 'entidad.codigo_usuario', '=', 'proyecto.codigo_usuario');

      //datos nombre etapa proyecto
      $query->join( 'core.usuario_institucion as ui', 'proyecto.codigo_usuario', '=', 'ui.codigo_usuario' );
      $query->join( 'core.dato_entidad as de', 'ui.codigo_usuario_institucion', '=', 'de.codigo_usuario_institucion' );
      $query->join( 'core.etapa_proyecto as ep', 'ep.codigo_etapa_proyecto', '=', 'de.codigo_etapa_proyecto' );

      $query->leftjoin( 'core.evento as evento', 'evento.codigo_evento', '=', 'mentoria.codigo_evento' );
      $query->leftjoin( 'aprende.objetivo_mentoria as om', 'om.codigo_mentoria', '=', 'mentoria.codigo_mentoria' );
      //$query->leftjoin( 'aprende.tag_sesion_mentoria as ts', 'ts.codigo_mentoria', '=', 'mentoria.codigo_mentoria' );

      $query->groupBy('nombre_mentor', 'nombre_emprendedor', 'mentoria.codigo_mentoria', 'usuario.identificacion_usuario', 'usuario.correo_electronico',
        'usuario.movil_usuario', 'proyecto.nombre_usuario', 'ep.nombre_etapa_proyecto', 'proyecto.biografia_usuario',
        'evento.hora_inicio_evento', 'evento.fecha_evento', 'evento.hora_fin_evento',
        'om.descripcion_objetivo_mentoria');

      $query->where('mi.codigo_institucion', '=', $institution->codigo_institucion);
      $query->orderBy('mentoria.codigo_mentoria', 'DESC');

      $dataQuery = $query->get();

       //dd($dataQuery[0]);

      foreach ($dataQuery as $key => $value) {
        $acuerdos = TareaMentoria::select('descripcion_tarea_mentoria')
                                  ->where('codigo_mentoria', $value->codigo_sesion )
                                  ->get();
        $infoEmpren = TagSesionMentoria::select(DB::raw("CONCAT( nombre_tag_sesion_mentoria, ': ', valor_tag_sesion_mentoria ) as informacion_emprendimiento"))
                                  ->where('codigo_mentoria', $value->codigo_sesion )
                                  ->get();

        if (  count($acuerdos) > 0 ) {
          $acuerdos_plan_accion = [];
          foreach ($acuerdos as $value) {
            $acuerdos_plan_accion[] = $value->descripcion_tarea_mentoria;
          }
            $acuerdos_plan_accion1 = "- " . implode("\n- ", $acuerdos_plan_accion);
            $dataQuery[$key]->acuerdos_plan_accion = $acuerdos_plan_accion1;

        }else $dataQuery[$key]->acuerdos_plan_accion = '';

        if (  count($infoEmpren) > 0 ) {
          $informacion_emprendimiento = [];
          foreach ($infoEmpren as $value) {
            $informacion_emprendimiento[] = $value->informacion_emprendimiento;
          }
            $informacion_emprendimiento1 = "- " . implode("\n- ", $informacion_emprendimiento);
            $dataQuery[$key]->informacion_emprendimiento = $informacion_emprendimiento1;

        }else $dataQuery[$key]->informacion_emprendimiento = '';

      }

      $filename = sprintf('%s_%s.xlsx', 'reporte_general_sesiones', date('YmdHms'));
      $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

      $array = array(
        'Codigo Sesión',
        'Nombre del mentor',
        'Nombre del emprendedor',
        'Cédula',
        'Correo',
        'Teléfono',
        'Nombre del proyecto',
        'Etapa del proyecto',
        'Descripción del proyecto',
        'Fecha de la sesión',
        'Hora de inicio de la sesión',
        'Hora fin de la sesión',
        'Descripción de sesión',
        'Tema de la sesión',
        'Información del emprendimiento',
        'Observaciones',
        'Estado de la sesión',
        'Aceptación de la sesión',
        'Acuerdos/Plan de acción',
        'Horas de la sesión',
      );

      $dataToExcel = [];
      foreach ($dataQuery as $value) {

        if ( $value->aceptacion_sesion ) {
          $value->aceptacion_sesion = $value->fecha_finalizo_mentoria;
        }else{
          $value->aceptacion_sesion ='No';
        }

        if( $value->codigo_estado == Estado::getState('active') ) {
            $value->codigo_estado = 'Sesión en Desarrollo';
        } elseif( $value->codigo_estado == Estado::getState('closed') || $value->codigo_estado == Estado::getState('pending_accept_session') ) {
            $value->codigo_estado = 'Sesión Finalizada';
        } elseif ( $value->codigo_estado == Estado::getState('removed') ) {
            $value->codigo_estado = 'Sesión Cancelada';
        }else {
          $value->codigo_estado = 'Pendiente';
        }

        if ($value->duration_mentoring != '') {
            $cadena = explode(',', trim($value->duration_mentoring));
            if ($cadena[0] == '') {
                $cadena[0] = 0;
            }
            if ($cadena[1] == '') {
                $cadena[1] = 1;
            }
            $value->duration_mentoring = "$cadena[0] horas y $cadena[1] minutos";
        };

        $dataToExcel[] = [
          $value->codigo_sesion,
          $value->nombre_mentor,
          $value->nombre_emprendedor,
          $value->cedula,
          $value->correo_electronico,
          $value->movil_usuario,
          $value->nombre_proyecto,
          $value->nombre_etapa_proyecto,
          $value->descripcion_proyecto,
          $value->fecha_sesion,
          $value->hora_inicio,
          $value->hora_fin,
          $value->descripcion_mentoria,
          $value->tema_sesion,
          $value->informacion_emprendimiento,
          $value->observaciones,
          $value->codigo_estado,
          $value->aceptacion_sesion,
          $value->acuerdos_plan_accion,
          $value->duration_mentoring,
        ];
      }

      $export = new ReportsExport([
        $array], [$dataToExcel]
      );

      // Genera el archivo Excel y guárdalo en storage/app/reports
      $file = Excel::store($export,$filepath,'s3', null, [
        'visibility' => 'public',
    ]);

      \Log::info("Termino el excel \n");
      $fileUrl = Storage::disk('s3')->url($filepath);
      // Send mail
      $data = [];
      $data['_template'] = 'emails.report';
      $data['_subject'] = 'Reporte general sesiones';
      $data['_to'] = $user->correo_electronico;
      $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
      $data['_inst'] = $institution->codigo_institucion;
      $data['_url'] = $fileUrl;
      SendEmail::notification($data);
      \Log::info("Termino el reporte \n");
    }
}
