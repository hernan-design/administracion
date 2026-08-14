<?php

namespace App\Console\Commands\Reports;

use App\Custom\Compensar;
use App\Custom\Terpel;
use Illuminate\Console\Command;
use Custom\Innpulsa;
use App\Models\Core\Convocatoria,
    App\Models\Core\OrientacionConvocatoria,
    App\Models\Core\RespuestaEncuesta,
    App\Models\Core\SectorInteres,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\Estado,
    App\Models\Core\Fase,
    App\Models\Core\Encuesta,
    App\Models\Core\Ciudad,
    App\Models\Core\NivelEducacion,
    App\Models\Core\PreguntaEncuesta;
use App\Models\Seguridad\Usuario;
use App\Models\Param\Institucion;
use App\Exports\ReportsExport;
use Log, Storage, Excel, DB, SendEmail;

class RegisteredProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:registeredprogram {user} {institution} {program} {filters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte emprendimientos inscritos a un programa, incluyendo el formulario de aplicación en xlsx';

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
            $filters = $this->argument('filters');

            // Valid arguments
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria){
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            $survey = Encuesta::find($program->codigo_encuesta);
            if(!$survey instanceof Encuesta){
                throw new \Exception("Error recuperando encuesta {$program->codigo_encuesta}.");
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

            \Log::info("Inicia Consulta \n");
            // Recuperar preguntas
            $arQuestions = $survey->getSurveyQuestions();
            switch ($program->codigo_orientacion_convocatoria) {
                case OrientacionConvocatoria::getOrientation('user'):
                    switch ($program->codigo_convocatoria) {
                        default :
                            // Recuperar Usuarios
                            $query = RegistroFaseUsuario::query();
                            $query->select('registro_fase_usuario.codigo_usuario', 'fecha_registro_fase_usuario', 'codigo_usuario_encuesta', 'hora_registro_fase_usuario', 'registro_fase_usuario.codigo_fase', 'fecha_registro_convocatoria', 'hora_registro_convocatoria', 'convocatoria.codigo_convocatoria', 'codigo_orientacion_convocatoria', 'numero_registro_convocatoria',  DB::raw("trim(initcap(nombre_ciudad)) as nombre_ciudad"), DB::raw("trim(initcap(distrito_ciudad)) as distrito_ciudad"),
                            // Person data
                            DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END) as nombre_usuario"),
                            DB::raw("usuario.correo_electronico")
                            );

                            $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

                            // User Program
                            $query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
                            $query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

                            $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

                            $query->join('core.convocatoria_institucion', function($join) use($institution) {
                             $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
                                 ->where('convocatoria_institucion.codigo_institucion', '=', $institution->codigo_institucion);
                            });
                            $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

                            // City / Country
                            $query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
                            $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');


                            $query->leftjoin('core.pais_institucion', function($join) use($institution) {
                             $join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
                                 ->where('pais_institucion.codigo_institucion', '=', $institution->codigo_institucion)
                                 ->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
                            });
                            $query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
                            $query->whereNotNull('distrito_ciudad');
                            $query->whereRaw("distrito_ciudad != ''");
                            $query-> where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

                            if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
                                $query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
                            }

                            if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
                                $query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
                            }

                            if( isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
                                $query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
                            }

                            if( isset($filters['email']) && !empty($filters['email']) ) {
                                $email = $filters['email'];
                                $query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
                            }

                            if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
                                $query->whereRaw("usuario.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
                            }

                            if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
                                $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
                            }

                            if( isset($filters['city']) && !empty($filters['city']) ) {
                                $query->where('usuario.codigo_ciudad', $filters['city']);
                            }

                            $query->orderBy('numero_registro_convocatoria','asc');
                            $usuarios = $query->get();

                            // Prepare header
                            $header = [];
                            $header[] = 'Número';
                            $header[] = 'Email';
                            $header[] = 'Usuario';
                            $header[] = 'Ciudad';
                            $header[] = 'Departamento';
                            $header[] = 'Fecha postulación';
                            $header[] = 'Fecha registro';

                            $result = RespuestaEncuesta::buildReportHeaders($arQuestions, $header, $matriz);
                            $header = $result->header;
                            $matriz = $result->matriz;

                            // Prepare answers
                            $answers = RespuestaEncuesta::buildReportAnswers($usuarios->pluck('codigo_usuario_encuesta')->all());

                            //Prepare data
                            $data = collect([]);
                            foreach ($usuarios as $register)
                            {
                                unset($record);
                                $record[] = $register->numero_registro_convocatoria;
                                $record[] = $register->correo_electronico;
                                $record[] = $register->nombre_usuario;
                                $record[] = $register->nombre_ciudad;
                                $record[] = $register->distrito_ciudad;
                                $record[] = $register->fecha_registro_convocatoria.' '.$register->hora_registro_convocatoria;
                                $record[] = $register->fecha_registro_fase_usuario.' '.$register->hora_registro_fase_usuario;

                                // Prepare survey answers
                                $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, $sectors, $levels, $register->codigo_usuario_encuesta, $record);

                                $data[] = $record;
                            }
                        break;
                    }
                break;

                case OrientacionConvocatoria::getOrientation('entity'):
                    switch ($program->codigo_convocatoria) {
                        case Convocatoria::getProgram('invitation_entrepreneurs'):
                            $result = Innpulsa::getResultExportAldea($arQuestions, $program, $this->argument('filters'));
                            $header = $result->headers;
                            $data = $result->data;
                        break;
                        case Convocatoria::getProgram('aldea__20'):
                            $result = Innpulsa::getResultExportAldea20($arQuestions, $program, $this->argument('filters'));
                            $header = $result->headers;
                            $data = $result->data;
                        break;
                        case Convocatoria::getProgram('innpulsa_empodera'):
                            $result = Innpulsa::getResultExportAldeaEmpodera($arQuestions, $program, $this->argument('filters'));
                            $header = $result->headers;
                            $data = $result->data;
                        break;
                        case Convocatoria::getProgram('empodera__2020'):
                            $result = Innpulsa::getResultExportAldeaEmpodera20($arQuestions, $program, $this->argument('filters'));
                            $header = $result->headers;
                            $data = $result->data;
                        break;
                        case Convocatoria::getProgram('application_proccess_entrepreneurship'):
                          $result = Compensar::getResultExportApplicationProccessEntrepreneurship($institution, $arQuestions, $matriz, $program, $this->argument('filters'), $cities, $sectors, $levels);
                          $header = $result->headers;
                          $data = $result->data;
                        break;
                        case Convocatoria::getProgram('disena_cambio'):
                            $result = Terpel::getExportTerpel($institution, $program, $this->argument('filters'), $arQuestions, $matriz, $cities, $sectors, $levels);
                            $header = $result->headers;
                            $data = $result->data;
                        break;
                        default:
                            // Recuperar Usuarios
                            $query = RegistroFaseUsuario::query();
                            $query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', 'codigo_usuario_encuesta', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "entity.identificacion_usuario as nit", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'genero_persona', 'nombre_categoria_proyecto', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',
                             DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'numero_registro_convocatoria',
                             DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad")
                            );

                            $query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
                            $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
                            $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

                            // User / Program
                            $query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
                            $query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

                            // User institution
                            $query->join('core.usuario_institucion', function($join) use($institution) {
                             $join->on('usuario_institucion.codigo_usuario', '=', 'entidad.codigo_usuario')
                                 ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
                            });

                            // Data entity
                            $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
                            $query->leftJoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');

                            $query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
                            $query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
                            $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
                            $query->join('core.convocatoria_institucion', function($join) use($institution) {
                                $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
                                    ->where('convocatoria_institucion.codigo_institucion', '=', $institution->codigo_institucion)
                                    ->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
                            });
                            $query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

                            // City / Country
                            $query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
                            $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

                            $query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
                            $query->where('convocatoria.codigo_estado', Estado::getState('active'));

                            // States filter
                            $query->whereIn('registro_fase_usuario.codigo_estado', [
                                Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('linked'), Estado::getState('returned'), Estado::getState('rejected')
                            ]);

                            if( isset($filters['email']) && !empty($filters['email']) ) {
                                $query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%".$filters['email']."%'))");
                            }

                            if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
                                $query->whereRaw("entity.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
                            }

                            if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
                                $query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
                            }

                            if( isset($filters['city']) && !empty($filters['city']) ) {
                                $query->where('person.codigo_ciudad', $filters['city']);
                            }

                            if( isset($filters['state']) && !empty($filters['state']) ) {
                                $query->where('registro_fase_usuario.codigo_estado', $filters['state']);
                            }

                            $query->where('registro_fase_usuario.codigo_estado', Estado::getState('active'));
                            $query->where('convocatoria.codigo_estado', Estado::getState('active'));
                            $query->orderBy('numero_registro_convocatoria','asc');
                            $usuarios = $query->distinct()->get();

                            // Prepare headers
                            $header = [];
                            $header[] = 'Aplicación';
                            $header[] = 'Email';
                            $header[] = 'Usuario';
                            $header[] = 'Genero';
                            $header[] = 'Fecha postulación';
                            $header[] = 'Fecha registro';
                            $header[] = 'Proyecto';
                            $header[] = transtp('entities.project.industry', [], $institution->slug_institucion);
                            $header[] = 'NIT';

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
                                $record[] = $register->correo_electronico;
                                $record[] = $register->nombre_usuario;
                                $record[] =  isset(Usuario::$gender[$register->genero_persona]) ? Usuario::$gender[$register->genero_persona]: '';
                                $record[] = $register->fecha_registro_convocatoria.' '.$register->hora_registro_convocatoria;
                                $record[] = $register->fecha_registro_fase_usuario.' '.$register->hora_registro_fase_usuario;
                                $record[] = $register->nombre_entidad;
                                $record[] = $register->nombre_categoria_proyecto;
                                $record[] = $register->nit;

                                // Prepare survey answers
                                $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, $sectors, $levels, $register->codigo_usuario_encuesta, $record);

                                $data[] = $record;
                            }
                        break;
                    }
                break;
            }
            \Log::info("Fin Consulta \n");
            // Prepare file
            $filename = sprintf('%s_%s_%s.xlsx', 'aplicaciones', $program->url_convocatoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $export = new ReportsExport([
              $header], [$data]
            );

            // Genera el archivo Excel y guárdalo en storage/app/reports
            $file = Excel::store($export,$filepath,'s3', null, [
              'visibility' => 'public',
          ]);

            // Obtén la URL del archivo en S3
            // Obtener la URL del archivo almacenado
            $fileUrl = Storage::disk('s3')->url($filepath);

            \Log::info("Cargo excel \n");
            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte aplicaciones $program->nombre_convocatoria";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

            \Log::info("Envio correo \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'RegisteredProgram:command', $e->getMessage()));
            abort(500);
        }
    }
}
