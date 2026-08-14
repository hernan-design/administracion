<?php

namespace App\Console\Commands\Rutan;

use App\Custom\Rutan;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use App\Exports\ReportsExport;
use App\Models\Aprende\Mentoria,
    App\Models\Aprende\TagSesionMentoria,
    App\Models\Aprende\TareaMentoria,
    App\Models\Core\Asesoria,
    App\Models\Core\Ciudad,
    App\Models\Core\Convocatoria,
    App\Models\Core\Estado,
    App\Models\Core\UsuarioEncuesta,
    App\Models\Param\Institucion,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Seguridad\Usuario;

use Log, DB, Excel, Storage, SendEmail;

class SessionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:sessions_rutan {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make report of general of sessions';

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

      Log::info('Log_3.2 Iniciando consulta reporte sesiones');
      try {
            $header = $data = [];

            // Valid user
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
              throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
              throw new \Exception("Error recuperando institución {$this->argument('institution')}.");
            }

            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria){
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            $query = Mentoria::query();
            $query->select(
                DB::raw("CONCAT( mentor.nombre_usuario, ' ', p_mentor.primer_apellido_persona ) as nombre_mentor"),
                'mentor.correo_electronico as correo_electronico_mentor',
                DB::raw("CONCAT( usuario.nombre_usuario, ' ', p_usuario.primer_apellido_persona ) as nombre_emprendedor"),
                'usuario.identificacion_usuario as numero_identificacion',
                'usuario.correo_electronico as correo_electronico',
                'usuario.movil_usuario as movil_usuario',
                'usuario.codigo_usuario as codigo_usuario',
                'usuario.codigo_usuario as codigo_usuario',
                'usuario.movil_usuario as movil_usuario',
                'p_usuario.genero_persona as sexo',
                'nivel_educacion.nombre_nivel_educacion',
                'usuario.fecha_nacimiento_usuario as fecha_nacimiento_usuario',
                'tipo_identificacion.nombre_tipo_identificacion as tipo_identificacion',
                'proyecto.nombre_usuario as nombre_proyecto',
                'proyecto.codigo_usuario as codigo_proyecto',
                'proyecto.biografia_usuario as descripcion_proyecto',
                'ep.nombre_etapa_proyecto',
                'evento.nombre_evento as titulo_sesion',
                'evento.lugar_evento as lugar_evento',
                'ciudad.nombre_ciudad as nombre_ciudad',
                'evento.fecha_evento as fecha_sesion',
                'evento.hora_inicio_evento as hora_inicio',
                'evento.descripcion_evento as descripcion_sesion',
                'evento.fecha_fin_evento as fecha_fin',
                'evento.hora_fin_evento as hora_fin',
                'tem.nombre_tema_principal_evento as beneficio_otorgado',
                'om.descripcion_objetivo_mentoria as tema_sesion',
                'mentoria.objetivo_mentor as observaciones',
                'mentoria.fecha_finalizo_mentoria as aceptacion_sesion',
                'mentoria.fecha_finalizo_mentoria',
                'mentoria.codigo_mentoria as codigo_sesion',
                'mentoria.codigo_estado as estado_sesion',
                'mentoria.descripcion_mentoria as descripcion_mentoria',
                'mentoria.duration_mentoring',
                'mentoria.fase as fase_proceso',
                'mentoria.recomendacion as recomendacion',
                'mentoria.objetivo_mentor as objetivo_mentor',
                'mentoria.seguimiento as seguimiento',
                'mentoria.duration_mentoring as duracion_sesion',
                'mentoria.fecha_finalizo_mentoria as aceptacion_sesion',

                'asesoria.codigo_estado as estado_mentoria',
                'tema_asesoria.nombre_tipo_asesoria'
            );
            $query->join('aprende.mentoria_institucion as mi', 'mentoria.codigo_mentoria', '=', 'mi.codigo_mentoria');
            $query->join('core.asesoria as asesoria', 'mentoria.codigo_asesoria', '=', 'asesoria.codigo_asesoria');

            //Mentoring Type
            $query->join('core.tipo_asesoria as tema_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tema_asesoria.codigo_tipo_asesoria');

            //Emprendedor
            $query->join('seguridad.usuario as usuario', 'mentoria.codigo_usuario', '=', 'usuario.codigo_usuario');
            $query->join('core.persona as p_usuario', 'usuario.codigo_usuario', '=', 'p_usuario.codigo_usuario');
            $query->join('core.nivel_educacion', 'nivel_educacion.codigo_nivel_educacion', '=', 'p_usuario.codigo_nivel_educacion');
            $query->join('core.tipo_identificacion', 'usuario.tipo_identificacion_usuario', '=', 'tipo_identificacion.codigo_tipo_identificacion');
            //$query->join('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');



            //Mentor
            $query->join('seguridad.usuario as mentor', 'mentoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
            $query->join('core.persona as p_mentor', 'mentor.codigo_usuario', '=', 'p_mentor.codigo_usuario');

            //Proyecto
            $query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
            $query->join('seguridad.usuario as proyecto', 'entidad.codigo_usuario', '=', 'proyecto.codigo_usuario');

            //Datos proyecto
            $query->join( 'core.usuario_institucion as ui', 'proyecto.codigo_usuario', '=', 'ui.codigo_usuario' );
            $query->join( 'core.dato_entidad as de', 'ui.codigo_usuario_institucion', '=', 'de.codigo_usuario_institucion' );
            $query->join( 'core.etapa_proyecto as ep', 'ep.codigo_etapa_proyecto', '=', 'de.codigo_etapa_proyecto' );

            $query->leftjoin( 'core.evento as evento', 'evento.codigo_evento', '=', 'mentoria.codigo_evento' );
            $query->join( 'core.ciudad as ciudad', 'evento.codigo_ciudad', '=', 'ciudad.codigo_ciudad' );
            $query->join( 'core.tema_principal_evento as tem', 'tem.codigo_tema_principal_evento', '=', 'evento.codigo_tema_principal_evento' );
            $query->leftjoin( 'aprende.objetivo_mentoria as om', 'om.codigo_mentoria', '=', 'mentoria.codigo_mentoria' );
            $query->leftjoin( 'aprende.tag_sesion_mentoria as ts', 'ts.codigo_mentoria', '=', 'mentoria.codigo_mentoria' );

            $query->groupBy('nombre_mentor', 'nombre_emprendedor', 'mentoria.codigo_mentoria', 'usuario.identificacion_usuario', 'usuario.correo_electronico',
              'usuario.movil_usuario', 'proyecto.nombre_usuario', 'ep.nombre_etapa_proyecto', 'proyecto.biografia_usuario', 'proyecto.codigo_usuario',
              'evento.hora_inicio_evento', 'evento.fecha_evento', 'evento.fecha_fin_evento','evento.hora_fin_evento','evento.nombre_evento',
              'evento.lugar_evento', 'evento.descripcion_evento','om.descripcion_objetivo_mentoria', 'ciudad.nombre_ciudad',
              'mentor.correo_electronico', 'tema_asesoria.nombre_tipo_asesoria', 'tem.nombre_tema_principal_evento', 'asesoria.codigo_estado',
              'usuario.codigo_usuario', 'p_usuario.genero_persona', 'nivel_educacion.nombre_nivel_educacion', 'tipo_identificacion.nombre_tipo_identificacion');

            $query->where('mi.codigo_institucion', '=', $institution->codigo_institucion);
            $query->orderBy('mentoria.codigo_mentoria', 'DESC');

            $data_query = $query->get();

            Log::info('Log_4.2 finaliza consulta sesiones,creando encabezados');

            // Prepare header
            $header = [];
            $header[] = 'Nº Postulación';
            $header[] = 'Estado Postulación';

            //headers user

            $header[] = 'Correo Emprendedor';
            $header[] = 'Nombre Emprendedor';
            $header[] = 'Tipo Identificación Emprendedor';
            $header[] = 'Número Documento';
            /* $header[] = 'Ciudad Nacimiento';
            $header[] = 'Fecha Nacimiento';
            $header[] = 'Sexo';
            $header[] = 'Nivel Educación';
            $header[] = 'Celular'; */

            //headers entity
            $header[] = 'Nombre Emprendimiento';
            //$header[] = 'Codigo Proyecto';



            //headers mentoring-session
            $header[] = 'Código Sesión';
            $header[] = 'Nombre Acompañante';
            $header[] = 'Correo Acompañante';
            $header[] = 'Tema De Acompañamiento';
            $header[] = 'Estado Acompañamiento';
            $header[] = 'Titulo Sesión';
            $header[] = 'Estado Sesión';
            $header[] = 'Descripción Sesión';
            $header[] = 'Ciudad Sesión';
            $header[] = 'Link O Lugar Reunión';
            $header[] = 'Fecha Inicio';
            $header[] = 'Hora Inicio';
            $header[] = 'Fecha Fin';
            $header[] = 'Hora Fin';
            $header[] = 'Beneficio Otorgado';
            $header[] = 'Descripción Intervención';
            $header[] = 'Acuerdos O Plan De Acción';
            $header[] = 'Información Emprendimiento';
            $header[] = 'Fase Del Proceso';
            $header[] = 'Recomendación Siguiente Etapa';
            $header[] = 'Observaciones';
            $header[] = 'Duración Sesión';
            $header[] = 'Aceptación Sesión';

            //Prepare data
            $data = collect([]);

            foreach ($data_query as $value)
            {

/*               $record_phase = RegistroFaseUsuario::query();
              $record_phase->select('numero_registro_convocatoria as num_postulacion');
              $query->where('registro_fase_usuario.codigo_fase', '=', Rutan::getIdPhase('registro_ruta_emprendimiento'));
              $query->where('registro_fase_usuario.codigo_usuario', '=', $value->codigo_usuario);
              $record = $record_phase->get(); */

              //dd($record);




              $agreements = TareaMentoria::select('descripcion_tarea_mentoria')
                                        ->where('codigo_mentoria', $value->codigo_sesion )
                                        ->get();

              if (count($agreements) > 0) {
                $deal_plan = [];
                foreach ($agreements as $agreement) {
                    $deal_plan[] = $agreement->descripcion_tarea_mentoria;
                }
                $deal_plan_portion = "- " . implode("\n- ", $deal_plan);
                $value->acuerdos_plan_accion = $deal_plan_portion;

              }
              else {
                  $value->acuerdos_plan_accion = '---';
              }

              $info_entrepreneurship = TagSesionMentoria::select(DB::raw("CONCAT( nombre_tag_sesion_mentoria, ': ', valor_tag_sesion_mentoria ) as informacion_emprendimiento"))
                                        ->where('codigo_mentoria', $value->codigo_sesion )
                                        ->get();

              if (count($info_entrepreneurship) > 0) {
                  $info = [];
                  foreach ($info_entrepreneurship as $info_en) {
                    $info[] = $info_en->informacion_emprendimiento;
                  }
                $info_portion = "- " . implode("\n- ", $info);
                $value->informacion_emprendimiento = $info_portion;

              }else {
                $value->informacion_emprendimiento = '---';
              }

              if ( $value->aceptacion_sesion ) {
                $value->aceptacion_sesion = $value->aceptacion_sesion;
              }else{
                $value->aceptacion_sesion ='No';
              }

              if( $value->estado_sesion == Estado::getState('active') ) {
                  $value->estado_sesion = 'Sesión en Desarrollo';
              } elseif( $value->estado_sesion == Estado::getState('closed') || $value->codigo_estado == Estado::getState('pending_accept_session') ) {
                  $value->estado_sesion = 'Sesión Finalizada';
              } elseif ( $value->estado_sesion == Estado::getState('removed') ) {
                  $value->estado_sesion = 'Sesión Cancelada';
              }else {
                $value->estado_sesion = 'Pendiente';
              }

              if ($value->duracion_sesion != '') {
                  $cadena = explode(',', trim($value->duracion_sesion));
                  if ($cadena[0] == '') {
                      $cadena[0] = 0;
                  }
                  if ($cadena[1] == '') {
                      $cadena[1] = 1;
                  }
                  $value->duracion_sesion = "$cadena[0] horas y $cadena[1] minutos";
              };

              unset($record);

              $record   = [];

              //data convocatory

              $data_convocatory = $this->getRecordConvocatory($value->codigo_proyecto);

              $record[] = (string) $data_convocatory['num_convocatory'];
              $record[] = (string) $data_convocatory['status_convocatory'];

              //data user
              $record[] = $value->correo_electronico;
              $record[] = mb_convert_case($value->nombre_emprendedor, MB_CASE_TITLE, "UTF-8");
              $record[] = $value->tipo_identificacion;
              $record[] = $value->numero_identificacion;
              //$record[] = $this->getBirthCity($value->codigo_usuario);
              //$record[] = $value->fecha_nacimiento_usuario;
              //$record[] = $this->getGender($value->sexo);
              //$record[] = $value->nombre_nivel_educacion;
              //$record[] = $value->movil_usuario;

              //data entity
              $record[] = $value->nombre_proyecto;
              //$record[] = $value->tipo_identificacion_proyecto;

              //data mentoring-session
              $record[] = $value->codigo_sesion;
              $record[] = mb_convert_case($value->nombre_mentor, MB_CASE_TITLE, "UTF-8");
              $record[] = $value->correo_electronico_mentor;
              $record[] = mb_convert_case($value->nombre_tipo_asesoria, MB_CASE_TITLE, "UTF-8");
              $record[] = Estado::getNameState($value->estado_mentoria);
              $record[] = mb_convert_case($value->titulo_sesion, MB_CASE_TITLE, "UTF-8");
              $record[] = $value->estado_sesion;
              $record[] = $this->mb_ucfirst($value->descripcion_sesion);
              $record[] = mb_convert_case($value->nombre_ciudad, MB_CASE_TITLE, "UTF-8");
              $record[] = mb_convert_case($value->lugar_evento, MB_CASE_TITLE, "UTF-8");
              $record[] = $value->fecha_sesion;
              $record[] = $value->hora_inicio;
              $record[] = $value->fecha_fin;
              $record[] = $value->hora_fin;
              $record[] = mb_convert_case($value->beneficio_otorgado, MB_CASE_TITLE, "UTF-8");
              $record[] = $this->mb_ucfirst($value->descripcion_mentoria);
              $record[] = mb_convert_case($value->acuerdos_plan_accion, MB_CASE_TITLE, "UTF-8");
              $record[] = mb_convert_case($value->informacion_emprendimiento, MB_CASE_TITLE, "UTF-8");
              $record[] = $value->fase_proceso;
              $record[] = mb_convert_case($value->recomendacion, MB_CASE_TITLE, "UTF-8");
              $record[] = $this->mb_ucfirst($value->objetivo_mentor);
              $record[] = $value->duracion_sesion;
              $record[] = $value->aceptacion_sesion;

              $data[] = $record;

            }

            \Log::info("Fin Consulta \n");

            // Prepare file
            Log::info('Log_5.2 creando ruta del excel acompaniamientos');
            $filename = sprintf('%s_%s_%s_%s.xlsx', 'sessions', '_general', $program->url_convocatoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $export = new ReportsExport([$header], [$data]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);
            \Log::info("Excel saved \n");

            // Send mail
            Log::info('Log_6.2 creacion excel, preparando correo');
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject']  = "REPORTE SESIONES";
            $data['_to']       = $user->correo_electronico;
            $data['_to_name']  = ucwords(strtolower($user->nombre_usuario));
            $data['_inst']     = $institution->codigo_institucion;
            $data['_url']      = $fileUrl;

            SendEmail::notification($data);

            \Log::info("Email send => session report \n");

      } catch(\Exception $e) {
        Log::error(sprintf('%s:%s', 'SessionReport:command', $e->getMessage()));
        abort(500);
      }

    }

    private function getGender(int $sex): string
    {
	    $gender = [
            1 => 'Masculino',
            2 => 'Femenino',
            3 => 'Prefiero no decirlo'
        ];

        return $gender[$sex];
    }

    private function getBirthCity(int $id_user) : string
    {
      $query = Usuario::query();
      $query->join('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
      $query->where('usuario.codigo_usuario', $id_user);
      $data = $query->first();

      $city = $data->nombre_ciudad . "-" . $data->distrito_ciudad . "-" . $data->codigo_pais;

      return $city;
    }

    private function getRecordConvocatory(int $id_entity)
    {
      $query = UsuarioEncuesta::query();
      $query->select('rec.numero_registro_convocatoria', 'rec.codigo_estado');
      $query->leftJoin('core.registro_convocatoria as rec', 'usuario_encuesta.codigo_usuario_encuesta', '=', 'rec.codigo_usuario_encuesta');
      $query->where('usuario_encuesta.codigo_usuario', $id_entity);
      $query = $query->get();
      $count = $query->count();

      if ($count > 0) {
        $data['num_convocatory'] = is_null($query[0]->numero_registro_convocatoria) ? 'Usuario no ha concluido registro' : $query[0]->numero_registro_convocatoria;
        $data['status_convocatory'] = is_null($query[0]->codigo_estado) ? 'Usuario no ha concluido registro' : Estado::getNameState($query[0]->codigo_estado);

      } else {
        $data['num_convocatory'] = 'Usuario no se ha apostulado';
        $data['status_convocatory'] = 'Usuario no se ha apostulado';
      }

      return $data;
    }

    private function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = true) {
      $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
      $str_end = "";
      if ($lower_str_end) {
        $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
      }
      else {
        $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
      }
      $str = $first_letter . $str_end;
      return $str;
    }



}
