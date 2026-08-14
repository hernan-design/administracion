<?php

namespace App\Console\Commands;

use App\Models\Core\Asesoria;
use App\Models\Param\Institucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Console\Command;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\Validator;
use Log, DB, Excel, Storage, SendEmail;

class Mentorias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:mentorias {user} {institution} {--startAt=} {--endAt=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make report of general of mentoring';

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
      \Log::info("codigo de usuario: " . $this->argument('user'));
      \Log::info("codigo de institucion: " . $this->argument('institution'));

      // Valid user
      $user = Usuario::find($this->argument('user'));
      if(!$user instanceof Usuario) {
        throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
      }

      $institution = Institucion::find($this->argument('institution'));
      if(!$institution instanceof Institucion) {
        throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
      }

      $query = Asesoria::query();

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
        $query->whereBetween('asesoria.fecha_asesoria', [$startAt, $endAt]);
      }

      $query->select(
          'asesoria.codigo_asesoria',
          'asesoria.fecha_asesoria',
          'asesoria.hora_asesoria',
          DB::raw("CONCAT( mentor.nombre_usuario, ' ', p_mentor.primer_apellido_persona ) as nombre_mentor"),
          DB::raw("CONCAT( mentorizado.nombre_usuario, ' ', p_mentorizado.primer_apellido_persona ) as nombre_mentorizado"),
          'at_mentor.fecha_aceptacion_terminos',
          'usuario_entidad.nombre_usuario as nombre_entidad',
          'mentor.identificacion_usuario as identificacion_mentor',
          'mentor.correo_electronico as email_mentor',
          'mentorizado.identificacion_usuario as identificacion_mentorizado',
          'mentorizado.correo_electronico as correo_mentorizado',
          'estado.nombre_estado'
        );
      $query->join('aprende.asesoria_institucion as ai', 'asesoria.codigo_asesoria', '=', 'ai.codigo_asesoria');
      $query->join('core.estado', 'ai.codigo_estado', '=', 'estado.codigo_estado');
      $query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');
      $query->join('core.persona as p_mentor', 'mentor.codigo_usuario', '=', 'p_mentor.codigo_usuario');
      $query->join('core.usuario_institucion as ui_mentor', 'mentor.codigo_usuario', '=', 'ui_mentor.codigo_usuario');
      $query->join('seguridad.aceptacion_terminos as at_mentor', 'ui_mentor.codigo_usuario_institucion', '=', 'at_mentor.codigo_usuario_institucion');
      $query->join('param.version_terminos as vt', 'at_mentor.codigo_version_terminos', '=', 'vt.codigo_version_terminos');
      $query->join('param.terminos_condiciones as tc', 'vt.codigo_terminos_condiciones', '=', 'tc.codigo_terminos_condiciones');
      $query->join('seguridad.usuario as mentorizado', 'asesoria.codigo_usuario', '=', 'mentorizado.codigo_usuario');
      $query->join('core.persona as p_mentorizado', 'mentorizado.codigo_usuario', '=', 'p_mentorizado.codigo_usuario');
      $query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
      $query->join('seguridad.usuario as usuario_entidad', 'entidad.codigo_usuario', '=', 'usuario_entidad.codigo_usuario');
      $query->where('ai.codigo_institucion', '=', $institution->codigo_institucion);
      $query->where('tc.tipo_terminos', '=', 4);
      $query->orderBy('asesoria.fecha_asesoria', 'ASC');
      $query->orderBy('asesoria.hora_asesoria', 'ASC');
     // $query->limit(15);
      $dataQuery = $query->get();



      $filename = sprintf('%s_%s.xlsx', 'reporte_general_mentoria', date('YmdHms'));
      $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);


      // \Log::info('codigo usuario: ' . $user->codigo_usuario);

      foreach ($dataQuery as $key => $value) {
        // \Log::info('codigo asesoria: ' . $value->codigo_asesoria);
        // \Log::info('codigo institucion: ' . $institution->codigo_institucion);
           $mentor = $this->getMentor($value->codigo_asesoria, $institution->codigo_institucion);

            if ( $mentor instanceof Asesoria){
                $attached = $mentor->attachedAdvice()->get();

                if ( $attached->count() > 0) {

                    foreach ($attached as $value) {
                        $output[] = trim($value->direccion_adjunto);
                    }

                    $dataQuery[$key]->attached = implode(" , \n", $output);

                } else {
                    $dataQuery[$key]->attached = 'Sin adjuntos';
                }

            }else{
                $dataQuery[$key]->attached = 'Sin adjuntos';
            }

        // \Log::info('adjuntos?: ' . $key . ' ' . $dataQuery[$key]->attached);

      }

      $array = array(
        'Fecha asignación mentor',
        'Hora asignación mentor',
        'Fecha de aceptación terminos mentor',
        'Nombre proyecto',
        'Mentor',
        //'Fecha aceptación solicitud',
        'Cédula mentor',
        'Email mentor',
        'Cédula emprendedor',
        'Nombre emprendedor',
        'Correo emprendedor',
        'Estado proceso',
        'Archivos adjuntos'
      );
      $dataToexcel = [];
      foreach ($dataQuery as $value) {

        $dataToexcel [] = [
          $value->fecha_asesoria,
          $value->hora_asesoria,
          $value->fecha_aceptacion_terminos,
          $value->nombre_entidad,
          $value->nombre_mentor,
          //$value->codigo_asesoria,
          $value->identificacion_mentor,
          $value->email_mentor,
          $value->identificacion_mentorizado,
          $value->nombre_mentorizado,
          $value->correo_mentorizado,
          $value->nombre_estado,
          $value->attached
        ];
      }

      $export = new ReportsExport([
        $array], [$dataToexcel]
      );

      // Genera el archivo Excel y guárdalo en storage/app/reports
      $file = Excel::store($export,$filepath,'s3', null, [
        'visibility' => 'public',
    ]);

      \Log::info("Termino el excel \n");

      // File valid
      $fileUrl = Storage::disk('s3')->url($filepath);
      // Send mail
      $data = [];
      $data['_template'] = 'emails.report';
      $data['_subject'] = 'Reporte general mentorias';
      $data['_to'] = $user->correo_electronico;
      $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
      $data['_inst'] = $institution->codigo_institucion;
      $data['_url'] = $fileUrl;
      SendEmail::notification($data);
      \Log::info("Termino el reporte \n");
    }

    public function getMentor($codigo_asesoria, $codigo_institucion)
    {
      \Log::info('codigo asesoria en Asesoria: ' . $codigo_asesoria);
      \Log::info('Sesión: ' . $codigo_institucion);
      $query = Asesoria::query();
      $query->select('asesoria.codigo_asesoria', 'descripcion_asesoria', 'fecha_asesoria', DB::raw("initcap(nombre_estado) as nombre_estado"), 'mentor.nickname_usuario as nickname_mentor', 'emprendedor.nickname_usuario as nickname_emprendedor', 'mentor.imagen_usuario as imagen_mentor', 'emprendedor.imagen_usuario as imagen_emprendedor', 'mentor.correo_electronico as correo_electronico_mentor', 'emprendedor.correo_electronico as correo_electronico_emprendedor',
        DB::raw("initcap( (CASE mentor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN mentor.nombre_usuario || ' ' || COALESCE(pmentor.primer_apellido_persona, '') || ' ' || COALESCE(pmentor.segundo_apellido_persona, '') ELSE mentor.nombre_usuario END) ) as nombre_mentor"),
        DB::raw("initcap( (CASE emprendedor.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN emprendedor.nombre_usuario || ' ' || COALESCE(pemprendedor.primer_apellido_persona, '') || ' ' || COALESCE(pemprendedor.segundo_apellido_persona, '') ELSE emprendedor.nombre_usuario END) ) as nombre_emprendedor"), 'mentor_institution.promedio_evaluacion_mentor', 'entrepreneur_institution.promedio_evaluacion_emprendedor', 'emprendimiento.nombre_usuario as nombre_emprendimiento'
      );
      $query->join('aprende.asesoria_institucion', function($join) use($codigo_institucion){
        $join->on('asesoria.codigo_asesoria', '=', 'asesoria_institucion.codigo_asesoria')
          ->where('asesoria_institucion.codigo_institucion', '=', $codigo_institucion);
      });

      $query->join('core.estado', 'asesoria.codigo_estado', '=', 'estado.codigo_estado');

      $query->join('core.tipo_asesoria', 'asesoria.codigo_tipo_asesoria', '=', 'tipo_asesoria.codigo_tipo_asesoria');

      // Mentor
      $query->join('seguridad.usuario as mentor', 'asesoria.seg_codigo_usuario', '=', 'mentor.codigo_usuario');

      // Mentor institution
      $query->join('core.usuario_institucion AS mentor_institution', function($join) use($codigo_institucion){
        $join->on('mentor_institution.codigo_usuario', '=', 'mentor.codigo_usuario')
          ->where('mentor_institution.codigo_institucion', '=', $codigo_institucion);
      });
      $query->leftJoin('core.persona as pmentor', 'mentor.codigo_usuario', '=', 'pmentor.codigo_usuario');

      // Emprendedor
      $query->join('seguridad.usuario as emprendedor', 'asesoria.codigo_usuario', '=', 'emprendedor.codigo_usuario');

      // Emprendedor institution
      $query->join('core.usuario_institucion AS entrepreneur_institution', function($join) use($codigo_institucion){
        $join->on('entrepreneur_institution.codigo_usuario', '=', 'emprendedor.codigo_usuario')
          ->where('entrepreneur_institution.codigo_institucion', '=', $codigo_institucion);
      });
      $query->leftJoin('core.persona as pemprendedor', 'emprendedor.codigo_usuario', '=', 'pemprendedor.codigo_usuario');

      // Emprendimiento
      $query->join('core.entidad', 'asesoria.codigo_entidad', '=', 'entidad.codigo_entidad');
      $query->join('seguridad.usuario as emprendimiento', 'entidad.codigo_usuario', '=', 'emprendimiento.codigo_usuario');


      if($codigo_asesoria){
        $query->where('asesoria.codigo_asesoria', '=', $codigo_asesoria);
        return $query->first();
      }
    }
}
