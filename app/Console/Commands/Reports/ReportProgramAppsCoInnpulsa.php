<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
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
    App\Models\Seguridad\Usuario,
    App\Models\Param\Institucion;
use Log, Storage, Excel, DB, SendEmail;

class ReportProgramAppsCoInnpulsa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:reportappsco {user} {institution} {program} {filters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de convocatoria Appsco de innpulsa';

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
                            $query->select(
                                'registro_fase_usuario.codigo_usuario',
                                'fecha_registro_fase_usuario',
                                'codigo_usuario_encuesta',
                                'hora_registro_fase_usuario',
                                'registro_fase_usuario.codigo_fase',
                                'fecha_registro_convocatoria',
                                'hora_registro_convocatoria',
                                'convocatoria.codigo_convocatoria',
                                'codigo_orientacion_convocatoria',
                                'numero_registro_convocatoria',
                                DB::raw("trim(initcap(nombre_ciudad)) as nombre_ciudad"),
                                DB::raw("trim(initcap(distrito_ciudad)) as distrito_ciudad"),
                                // Person data
                                DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')."
                                    THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '')
                                        || ' ' || COALESCE(persona.segundo_apellido_persona, '')
                                    ELSE usuario.nombre_usuario END) as nombre_usuario"),
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

                            if( isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
                                $query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
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
                            
                            //scoring responses
                            $header[] = 'Nivel de educación';
                            $header[] = 'Experiencia laboral';
                            $header[] = 'Dedicación';
                            $header[] = 'Empleos';
                            $header[] = 'Usuarios';
                            $header[] = 'Ventas';
                            $header[] = 'Financiación';
                            $header[] = 'Tipo mercado';
                            $header[] = 'Scoring total';

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

                                //scoring responses
                                $record[] = RespuestaEncuesta::getResponseNivelEducacion($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseExperienciaLaboral($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseDedicacion($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseEmpleos($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseUsuariosActivos($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseVentas($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseFinanciacion($register->codigo_usuario_encuesta);
                                $record[] = RespuestaEncuesta::getResponseTipoMercado($register->codigo_usuario_encuesta);
                                $record[] = $this->scoringProgramAppsco($register->codigo_usuario_encuesta);

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

            $file = Excel::create($filename, function($excel) use($header, $data) {
                $excel->sheet('Aplicaciones', function($sheet) use($header, $data) {
                    // Titles
                    $sheet->appendRow($header);
                    // Data
                    $sheet->rows($data);
                 });
            })->string('xlsx');
            \Log::info("Creo excel \n");

            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte registros convocatoria File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
            \Log::info("Cargo excel \n");
            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte único programa Apps.co";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

            \Log::info("Envio correo \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'RegisteredProgram:command', $e->getMessage()));
            abort(500);
        }
    }

    public static function scoringProgramAppsco($codigo_usuario_encuesta): int
	{
        $scoring_nivel_educacion = RespuestaEncuesta::getResponseNivelEducacion($codigo_usuario_encuesta);

		$scoring_experiencia_laboral = RespuestaEncuesta::getResponseExperienciaLaboral($codigo_usuario_encuesta);

		$scoring_dedicacion = RespuestaEncuesta::getResponseDedicacion($codigo_usuario_encuesta);

		$scoring_empleos = RespuestaEncuesta::getResponseEmpleos($codigo_usuario_encuesta);

		$scoring_usuarios_activos = RespuestaEncuesta::getResponseUsuariosActivos($codigo_usuario_encuesta);

		$scoring_ventas = RespuestaEncuesta::getResponseVentas($codigo_usuario_encuesta);

		$scoring_financiacion = RespuestaEncuesta::getResponseFinanciacion($codigo_usuario_encuesta);

		$scoring_tipo_mercado = RespuestaEncuesta::getResponseTipoMercado($codigo_usuario_encuesta);

		return $scoring_nivel_educacion + $scoring_experiencia_laboral + $scoring_dedicacion
		    + $scoring_empleos + $scoring_usuarios_activos + $scoring_ventas + $scoring_financiacion
            + $scoring_tipo_mercado;
	}
}
