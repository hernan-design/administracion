<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;

use App\Models\Core\Convocatoria, App\Models\Core\Estado, App\Models\Core\SectorInteres, App\Models\Core\Ciudad, App\Models\Core\NivelEducacion, App\Models\Core\Encuesta, App\Models\Core\UsuarioEncuesta, App\Models\Core\RespuestaEncuesta;

use App\Models\Seguridad\Usuario, App\Models\Seguridad\Modulo;

use App\Models\Param\Institucion;

use App\Models\Param\TerminosCondiciones;

use Log, Storage, Excel, DB, SendEmail;

class MonitoringProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:monitoringprogram {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte emprendimientos con inscripcion pendiende o finalizada a un programa, incluyendo el formulario de aplicación en xls.';

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
            $header = $data = $matriz = [];

            // Valid arguments
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria) {
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            $survey = Encuesta::find($program->codigo_encuesta);
            if(!$survey instanceof Encuesta) {
                throw new \Exception("Error recuperando encuesta {$program->codigo_encuesta}.");
            }

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            // Preparar preguntas
            $cities = [];
            $citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
            foreach ($citys as $city) {
              $cities["{$city->codigo_ciudad}"] = $city;
            }

            // Preparar sectores
            $sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

            // Preparar Nivel de educación
            $levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

            // Recuperar preguntas
            $query = $survey->questions();
            $query->select('pregunta_encuesta.*', 'pregunta.cor_codigo_pregunta_encuesta as pregunta_padre', 'pregunta.codigo_opcion_encuesta');
            $query->leftJoin('core.grupo_pregunta_encuesta', 'pregunta_encuesta.codigo_grupo_pregunta_encuesta', '=', 'grupo_pregunta_encuesta.codigo_grupo_pregunta_encuesta');
            $query->leftJoin(DB::raw('(SELECT * FROM core.subpregunta_encuesta ORDER BY codigo_version_encuesta DESC LIMIT 1) AS pregunta'), function($join)
            {
                $join->on('pregunta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta.codigo_pregunta_encuesta');
            });

            $query->where('pregunta_encuesta.codigo_estado', Estado::getState('active'));
            $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
            $query->orderBy('pregunta_encuesta.codigo_pregunta_encuesta', 'asc');
            $arQuestions = $query->get();

            // Get users
            $query = UsuarioEncuesta::query();
            $query->select('usuario_encuesta.codigo_usuario_encuesta', 'numero_registro_convocatoria', 'fecha_registro_convocatoria', 'hora_registro_convocatoria', DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), DB::raw("trim(initcap(nombre_ciudad)) as nombre_ciudad"), DB::raw("person.correo_electronico"), 'entity.nombre_usuario as nombre_entidad', 'person.correo_electronico as correo_electronico', 'genero_persona', 'nombre_categoria_proyecto', 'nombre_etapa_proyecto', DB::raw("initcap(nombre_ciudad) nombre_ciudad"), DB::raw("initcap(distrito_ciudad) as distrito_ciudad"),
            'aceptacion_terminos.fecha_aceptacion_terminos', 'aceptacion_terminos.direccion_ip'
            );

            $query->join('seguridad.usuario as entity', 'usuario_encuesta.codigo_usuario', '=', 'entity.codigo_usuario');
            $query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
            $query->leftJoin('core.ciudad', 'entity.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
            $query->leftJoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

            // User Person User
            $query->leftJoin('seguridad.usuario as user_person', 'user_person.codigo_usuario', '=', 'entidad.seg_codigo_usuario');

            //User institution Person
            $query->leftJoin('core.usuario_institucion as user_institution_person', function($join) use($institution) {
                $join->on('user_institution_person.codigo_usuario', '=', 'user_person.codigo_usuario')
                ->where('user_institution_person.codigo_institucion', '=', $institution->codigo_institucion);
            });

            // User institution
            $query->join('core.usuario_institucion', function($join) use($institution) {
                $join->on('usuario_institucion.codigo_usuario', '=', 'entidad.codigo_usuario');
                $join->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
            });

            $query->leftJoin('param.terminos_condiciones', function($join) {
                $join->on('terminos_condiciones.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
                    ->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('privacy'));
             });
    
            $query->leftJoin('param.version_terminos', 'version_terminos.codigo_terminos_condiciones', '=', 'terminos_condiciones.codigo_terminos_condiciones');

            $query->leftJoin('seguridad.aceptacion_terminos', function($join) {
            $join->on('aceptacion_terminos.codigo_version_terminos', '=', 'version_terminos.codigo_version_terminos')
                ->on('aceptacion_terminos.codigo_usuario_institucion', '=', 'user_institution_person.codigo_usuario_institucion');
            });

            $query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');

            $query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
            $query->leftJoin('core.registro_convocatoria', function($join) {
                $join->on('registro_convocatoria.codigo_convocatoria', '=', 'usuario_encuesta.id_recurso');
                $join->on('registro_convocatoria.codigo_usuario', '=', 'usuario_encuesta.codigo_usuario');
                $join->on('registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');
            });

            // Data entity
            $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
            $query->leftJoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
            $query->leftJoin('core.etapa_proyecto', 'dato_entidad.codigo_etapa_proyecto', '=', 'etapa_proyecto.codigo_etapa_proyecto');

            $query->where('codigo_encuesta', '=', $survey->codigo_encuesta);
            $query->where('id_recurso', '=', $program->codigo_convocatoria);
            $query->where('codigo_modulo', '=', Modulo::getModule('calls'));
            $query->where(function($query)
            {
                $query->whereNull('registro_convocatoria.codigo_estado');
                $query->orWhere('registro_convocatoria.codigo_estado', Estado::getState('active'));
            });
            $query->orderBy('numero_registro_convocatoria', 'asc');
            $usuarios = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Aplicación';
            $header[] = 'Fecha registro';
            $header[] = 'Email';
            $header[] = 'Usuario';
            $header[] = 'Genero';
            $header[] = 'Proyecto';
            $header[] = 'Municipio';
            $header[] = 'Departamento';
            $header[] = 'Fecha aceptación terminos';
            $header[] = 'Dirección ip';
            $header[] = 'Fecha aceptación convocatoria';
            $header[] = transtp('entities.project.stage', [], $institution->slug_institucion);
            $header[] = transtp('entities.project.industry', [], $institution->slug_institucion);
            $result = RespuestaEncuesta::buildReportHeaders($arQuestions, $header, $matriz);
            $header = $result->header;
            $matriz = $result->matriz;

            // Preparar answers
            $answers = RespuestaEncuesta::buildReportAnswers($usuarios->pluck('codigo_usuario_encuesta')->all());

            //Prepare data
            $data = collect([]);
            foreach ($usuarios as $register)
            {
                unset($record);
                $record[] = $register->numero_registro_convocatoria;
                $record[] = "$register->fecha_registro_convocatoria $register->hora_registro_convocatoria";
                $record[] = $register->correo_electronico;
                $record[] = $register->nombre_usuario;
                $record[] = isset(Usuario::$gender[$register->genero_persona]) ? Usuario::$gender[$register->genero_persona]: '';
                $record[] = $register->nombre_entidad;
                $record[] = $register->nombre_ciudad;
                $record[] = $register->distrito_ciudad;
                $record[] = $register->fecha_aceptacion_terminos;
                $record[] = $register->direccion_ip;
                $record[] = $register->fecha_registro_convocatoria;
                $record[] = $register->nombre_etapa_proyecto;
                $record[] = $register->nombre_categoria_proyecto;

                // Prepare survey answers
                $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, $sectors, $levels, $register->codigo_usuario_encuesta, $record);

                $data[] = $record;
            }
            
            // Prepare file
            $filename = sprintf('%s_%s_%s.xlsx', 'seguimiento_aplicaciones', $program->url_convocatoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data)
            {
                $excel->sheet('Aplicaciones', function($sheet) use($header, $data)
                {
                    // Titles
                    $sheet->appendRow($header);
                    // Data
                    $sheet->rows($data);
                });
            })->string('xlsx');
            
            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte seguimiento convocatoria File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte seguimiento convocatoria $program->nombre_convocatoria";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'MonitoringProgram:command', $e->getMessage()));
            abort(500);
        }
    }
}
