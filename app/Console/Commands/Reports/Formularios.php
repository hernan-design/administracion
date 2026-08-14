<?php

namespace App\Console\Commands\Reports;

use App\Custom\Compensar;
use App\Custom\Terpel;
use Illuminate\Console\Command;

use Custom\Innpulsa;

use App\Models\Core\UsuarioEncuesta,  App\Models\Core\RespuestaEncuesta,  App\Models\Core\Estado,  App\Models\Core\Encuesta, App\Models\Core\Ciudad, App\Models\Core\NivelEducacion, App\Models\Core\PreguntaEncuesta;

use App\Models\Seguridad\Usuario;

use App\Models\Param\Institucion;

use Log, Storage, Excel, DB, SendEmail;

class Formularios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:formularios
                            { user : id de usuario }
                            { institution : codigo de la institucion }
                            { survey : codigoo del formulario},
                            { filters : aditional parameters to filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte en xlsx de un formulario y las respuestas de los usuarios que lo han completado';

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
            $filters = [];
            $filters = $this->argument('filters');

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            $survey = Encuesta::find($this->argument('survey'));
            if(!$survey instanceof Encuesta){
                throw new \Exception("Error recuperando encuesta {$this->argument('survey')}.");
            }

            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario){
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            // Preparar preguntas
            $cities = [];
            $citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
            foreach ($citys as $city) {
              $cities["{$city->codigo_ciudad}"] = $city;
            }

            $this->info("Inicia Consulta");
            Log::info("Inicia Consulta");
            // Recuperar preguntas
            $arQuestions = $survey->getSurveyQuestions();


            // Recuperar Usuarios
            $query = UsuarioEncuesta::query();
            $query->select('encuesta.codigo_encuesta', 'encuesta.url_encuesta', 'encuesta.nombre_encuesta','nombre_estado', DB::raw("(fecha_usuario_encuesta || ' ' || hora_usuario_encuesta) as fecha_usuario_encuesta"), 'fecha_fin_usuario_encuesta', 'hora_usuario_encuesta',
              'puntaje_total_encuesta', 'codigo_usuario_encuesta', 'nombre_modulo', 'id_recurso', 'usuario_encuesta.codigo_version_encuesta', 'usuario.correo_electronico',
              DB::raw("trim(initcap(nombre_ciudad)) as nombre_ciudad"),
              DB::raw("trim(initcap(distrito_ciudad)) as distrito_ciudad"),
              DB::raw("(CASE tipo_usuario WHEN ".Usuario::typeUser('person')." THEN nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE nombre_usuario END) as nombre_usuario")
            );
            // Survey
            $query->join('core.encuesta', 'usuario_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
            // User
            $query->join('seguridad.usuario', 'usuario_encuesta.codigo_usuario', '=', 'usuario.codigo_usuario');
            $query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

            $query->join('core.estado', 'usuario_encuesta.codigo_estado', '=', 'estado.codigo_estado');

            $query->leftJoin('seguridad.modulo', 'usuario_encuesta.codigo_modulo', '=', 'modulo.codigo_modulo');

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
            $query->where('encuesta.codigo_encuesta', '=', $survey->codigo_encuesta);
            $query->where('usuario_encuesta.codigo_estado', '=', Estado::getState('complete'));


            if( isset($filters['nombre_usuario']) && !empty($filters['nombre_usuario']) ) {
                $query->whereRaw("usuario.nombre_usuario ilike '%".$filters['nombre_usuario']."%'");
            }
            $query->orderBy('usuario_encuesta.fecha_fin_usuario_encuesta','desc');
            $usuarios = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Email';
            $header[] = 'Usuario';
            $header[] = 'Ciudad';
            $header[] = 'Departamento';
            $header[] = 'Nombre encuesta';
            $header[] = 'Estado encuesta';
            $header[] = 'Fecha Fin';
            $header[] = 'Puntaje total';
            $header[] = 'Modulo';

            $result = RespuestaEncuesta::buildReportHeaders($arQuestions, $header, $matriz);
            $header = $result->header;
            $matriz = $result->matriz;

            // Prepare answers
            $answers = RespuestaEncuesta::buildReportAnswers($usuarios->pluck('codigo_usuario_encuesta')->all());

            //Prepare data
            $data = collect([]);


            if($usuarios->count()){
              $progressBar = $this->output->createProgressBar($usuarios->count());
              $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %memory:6s%');

              foreach ($usuarios as $register)
              {

                  unset($record);
                  $record[] = $register->correo_electronico;
                  $record[] = $register->nombre_usuario;
                  $record[] = $register->nombre_ciudad;
                  $record[] = $register->distrito_ciudad;
                  $record[] = $register->nombre_encuesta;
                  $record[] = $register->nombre_estado;
                  $record[] = $register->fecha_fin_usuario_encuesta;
                  $record[] = $register->puntaje_total_encuesta;
                  $record[] = $register->nombre_modulo;

                  // Prepare survey answers
                  $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, null, null, $register->codigo_usuario_encuesta, $record);

                  $data[] = $record;
                  $progressBar->advance();

              }
              $progressBar->finish();
          }

            $this->info("\nFin Consulta \n");
            Log::info("Fin Consulta \n");
            // Prepare file
            $filename = sprintf('%s_%s_%s.xlsx', 'formularios', $survey->url_encuesta, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data) {
                $excel->sheet('Aplicaciones', function($sheet) use($header, $data) {
                    // Titles
                    $sheet->appendRow($header);
                    // Data
                    $sheet->rows($data);
                 });
            })->string('xlsx');
            $this->info("Creando archivo excel \n");
            Log::info("Creando archivo excel \n");

            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte registros convocatoria File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
            $this->info("Subiendo excel a AWS\n");
            Log::info("Subiendo excel a AWS \n");
            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte encuestas $survey->nombre_encuesta";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

            $this->info("Enviando correo \n");
            Log::info("Enviando correo \n");


          $this->info("\n");
          $this->info(sprintf('Reporte del formulario %s', $survey->codigo_encuesta));
          Log::info(sprintf('Reporte del formulario %s',$survey->codigo_encuesta));

        }catch(\Exception $e) {
            $this->error($e->getMessage());
            $this->error(sprintf('File:%s Line:%s', $e->getFile(), $e->getLine()));
            Log::error(sprintf('%s:%s', 'App\Console\Commands\Reports\Formularios:command', $e->getMessage()));
            Log::error(sprintf('File:%s Line:%s', $e->getFile(), $e->getLine()));
            abort(500);
        }
    }
}
