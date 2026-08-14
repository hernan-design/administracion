<?php

namespace App\Console\Commands\Dinamiza;

use App\Models\Core\Convocatoria;
use App\Models\Param\Institucion;
use App\Models\Seguridad\Usuario;
use Illuminate\Console\Command;
use Log, Storage, Excel, DB, SendEmail;

class Program extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinamiza:programs {user} {institution} {filters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte programas de dinamiza en xlsx';

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
        $filters = $this->argument('filters');

        $user = Usuario::find($this->argument('user'));
        if(!$user instanceof Usuario) {
          throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
        }

        $institution = Institucion::find($this->argument('institution'));
        if(!$institution instanceof Institucion) {
          throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
        }

        $query = Convocatoria::query();
        $query->select('convocatoria.nombre_convocatoria', 'usuario.nombre_usuario', 'convocatoria.contador_inscritos', 'etapa_proyecto.nombre_etapa_proyecto', 'categoria_proceso.nombre_categoria_proceso', 'convocatoria.fecha_creacion_convocatoria', 'convocatoria.fecha_creacion_convocatoria', 'convocatoria.fecha_cierre_convocatoria');
        $query->join('core.convocatoria_institucion', 'convocatoria_institucion.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
        $query->leftJoin('core.entidad', 'entidad.codigo_usuario', '=', 'convocatoria.seg_codigo_usuario');
        $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->leftJoin('core.etapa_proyecto', 'etapa_proyecto.codigo_etapa_proyecto', '=', 'convocatoria.codigo_etapa_proyecto');
        $query->leftJoin('core.categoria_proceso_insti', 'categoria_proceso_insti.codigo_cat_proceso_insti', '=', 'convocatoria_institucion.codigo_cat_proceso_insti');
        $query->leftJoin('core.categoria_proceso', 'categoria_proceso.codigo_categoria_proceso', '=', 'categoria_proceso_insti.codigo_categoria_proceso');
        $query->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
        $query->orderBy('usuario.nombre_usuario', 'asc');

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
        }

        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
        }

        if (isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
        }

        if (isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }

        if (isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program']) && empty($filters['fecha_inicio_programa']) && empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa'])) {
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
        }

        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }

        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }

        if (isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
        }


        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && empty($filters['etapa_programa']) && empty($filters['codigo_entidad_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        if (isset($filters['codigo_entidad_programa']) && !empty($filters['codigo_entidad_programa']) && isset($filters['etapa_programa']) && !empty($filters['etapa_programa']) && isset($filters['categoria_programa']) && !empty($filters['categoria_programa']) && isset($filters['fecha_inicio_programa']) && !empty($filters['fecha_inicio_programa']) && isset($filters['fecha_final_program']) && !empty($filters['fecha_final_program'])) {
          $query->where('convocatoria.seg_codigo_usuario', '=', $filters['codigo_entidad_programa']);
          $query->where('etapa_proyecto.codigo_etapa_proyecto', '=', $filters['etapa_programa']);
          $query->where('categoria_proceso.codigo_categoria_proceso', '=', $filters['categoria_programa']);
          $query->where('convocatoria.fecha_creacion_convocatoria', '=', $filters['fecha_inicio_programa']);
          $query->where('convocatoria.fecha_cierre_convocatoria', '=', $filters['fecha_final_program']);
        }

        $programs = $query->get();

        $header= [];
        $header[] = 'Nombre del programa';
        $header[] = 'Entidad organizadora';
        $header[] = 'Número de usuarios registrados';
        $header[] = 'Etapa';
        $header[] = 'Categoría';
        $header[] = 'Fecha de inicio';
        $header[] = 'Fecha final';

        $data = collect([]);

        foreach ($programs as $program)
        {
          unset($record);
          $record[] = $program->nombre_convocatoria;
          $record[] = $program->nombre_usuario;
          $record[] = $program->contador_inscritos;
          $record[] = $program->nombre_etapa_proyecto;
          $record[] = $program->nombre_categoria_proceso;
          $record[] = $program->fecha_creacion_convocatoria;
          $record[] = $program->fecha_cierre_convocatoria;

          $data[] = $record;
        }

        $filename = sprintf('%s_%s.xlsx', 'programas', date('YmdHms'));
        $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

        $file = Excel::create($filename, function($excel) use($header, $data) {
          $excel->sheet('Programas', function($sheet) use($header, $data) {
            // Titles
            $sheet->appendRow($header);
            // Data
            $sheet->rows($data);
          });
        })->string('xlsx');

        Storage::put($filepath, $file, 'public');

        if(!Storage::has($filepath)) {
          throw new \Exception("Error recuperando xlsx reporte programas de dinamiza File($filepath).");
        }

        $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

        $data = [];
        $data['_template'] = 'emails.report';
        $data['_subject'] = "Reporte programas";
        $data['_to'] = $user->correo_electronico;
        $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
        $data['_inst'] = $institution->codigo_institucion;
        $data['_url'] = $fileurl;
        SendEmail::notification($data);

      } catch (\Exception $e){
          Log::error(sprintf('%s:%s', 'ProgramDinamiza:command', $e->getMessage()));
          abort(500);
      }

    }
}
