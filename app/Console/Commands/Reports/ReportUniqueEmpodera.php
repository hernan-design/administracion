<?php

namespace  App\Console\Commands\Reports;

use App\Exports\ReportsExport;
use App\Models\Core\Ciudad;
use App\Models\Core\Convocatoria;
use App\Models\Core\Encuesta;
use App\Models\Core\Entidad;
use App\Models\Core\Estado;
use App\Models\Core\NivelEducacion;
use App\Models\Core\RegistroFaseUsuario;
use App\Models\Core\RespuestaEncuesta;
use App\Models\Core\SectorInteres;
use App\Models\Core\UsuarioInstitucion;
use App\Models\Param\Institucion;
use App\Models\Param\TerminosCondiciones;
use App\Models\Seguridad\Usuario;
use Illuminate\Console\Command;
use Log, Storage, Excel, DB, SendEmail;

class ReportUniqueEmpodera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:empoderaunique {institution} {user} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte unico de Empodera';

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

          // Recuperar preguntas
          $arQuestions = $survey->getSurveyQuestions();


          // Recuperar Usuarios
          $query = UsuarioInstitucion::query();
          $query->select("entity.nombre_usuario as nombre_entidad", 'ue.codigo_usuario_encuesta', "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'genero_persona', 'categoria_proyecto.nombre_categoria_proyecto', 'rc.fecha_registro_convocatoria', 'rc.hora_registro_convocatoria',
            DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'rc.numero_registro_convocatoria',
            DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
            "person.identificacion_usuario as numero_documento", "person.fecha_nacimiento_usuario as fecha_nacimiento", "ciudad.distrito_ciudad as departamento", "ciudad.nombre_ciudad as municipio", "person.telefono_usuario as telefono", "person.movil_usuario as movil",
            "person.direccion_usuario as direccion", "aceptacion_terminos.fecha_aceptacion_terminos", "entity.identificacion_usuario as nit_empresa", "person.cargo as cargo_usuario"
          );

          $query->join('seguridad.usuario as u', function($join) use($institution) {
            $join->on('u.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
              ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
          });

          // User / Program
          $query->leftJoin('core.entidad', 'entidad.seg_codigo_usuario', '=', 'u.codigo_usuario');
          $query->leftJoin('core.entidad as empresa', 'empresa.codigo_usuario', '=', 'entidad.codigo_usuario');
          $query->leftJoin('seguridad.usuario as entity', 'entity.codigo_usuario', '=', 'empresa.codigo_usuario');

          $query->leftJoin('core.usuario_institucion as ui', 'ui.codigo_usuario', '=', 'empresa.codigo_usuario');

          // Data entity
          $query->leftJoin('core.dato_entidad', 'ui.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
          $query->leftJoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');


          $query->leftJoin('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
          $query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
          $query->leftJoin('core.usuario_institucion as usinsper', function ($join) {
            $join->on('usinsper.codigo_usuario', '=', 'person.codigo_usuario')
              ->where('usinsper.codigo_estado', '=', Estado::getState('active'));
          });

          $query->leftJoin('core.usuario_encuesta as ue', 'ue.codigo_usuario', '=', 'entidad.codigo_usuario');
          $query->leftJoin('core.registro_convocatoria as rc', function($join) {
            $join->on('rc.codigo_usuario_encuesta', '=', 'ue.codigo_usuario_encuesta')
              ->where('rc.codigo_convocatoria', '=', 349);
          });

          $query->leftJoin('core.convocatoria', 'rc.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
          $query->leftJoin('core.convocatoria_institucion', function($join) use($institution) {
            $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
              ->where('convocatoria_institucion.codigo_institucion', '=', $institution->codigo_institucion)
              ->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
          });

          $query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

          $query->join('seguridad.aceptacion_terminos', 'aceptacion_terminos.codigo_usuario_institucion', '=', 'usinsper.codigo_usuario_institucion');
          $query->join('param.version_terminos', 'version_terminos.codigo_version_terminos', '=', 'aceptacion_terminos.codigo_version_terminos');
          $query->join('param.terminos_condiciones', function($join) use($institution) {
            $join->on('terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones')
              ->where('terminos_condiciones.codigo_institucion', '=', $institution->codigo_institucion);
          });

          // City / Country
          $query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
          $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

          $query->where('usuario_institucion.fecha_registro_usuario', '>', '2019-08-19');
          $query->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
          $query->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('privacy'));
          $query->orderBy('numero_registro_convocatoria','asc');
          $usuarios = $query->get();

          // Prepare headers
          $header = [];
          $header[] = 'Aplicación';
          $header[] = 'Email';
          $header[] = 'Usuario';
          $header[] = 'Numero identificacíon';
          $header[] = 'Fecha de nacimiento';
          $header[] = 'Departamento';
          $header[] = 'Municipio';
          $header[] = 'Telefono';
          $header[] = 'Celular';
          $header[] = 'Direccion';
          $header[] = 'Genero';
          $header[] = 'Cargo';
          $header[] = 'Fecha aceptacion de terminos';
          $header[] = 'Fecha postulación';
          $header[] = 'Proyecto';
          $header[] = 'Nit Empresa';
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
            $record[] = $register->correo_electronico;
            $record[] = $register->nombre_usuario;
            $record[] = $register->numero_documento;
            $record[] = $register->fecha_nacimiento;
            $record[] = $register->departamento;
            $record[] = $register->municipio;
            $record[] = $register->telefono;
            $record[] = $register->movil;
            $record[] = $register->direccion;
            $record[] =  isset(Usuario::$gender[$register->genero_persona]) ? Usuario::$gender[$register->genero_persona]: '';
            $record[] = $register->cargo_usuario;
            $record[] = $register->fecha_aceptacion_terminos;
            $record[] = $register->fecha_registro_convocatoria.' '.$register->hora_registro_convocatoria;
            $record[] = $register->nombre_entidad;
            $record[] = $register->nit_empresa;
            $record[] = $register->nombre_categoria_proyecto;

            // Prepare survey answers
            $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, $sectors, $levels, $register->codigo_usuario_encuesta, $record);

            $data[] = $record;
          }

          // Prepare file
          $filename = sprintf('%s_%s.xlsx', 'reporte_emprende_pais', date('YmdHms'));
          $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

          $export = new ReportsExport([$header], [$data]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);

          // Send mail
          $data = [];
          $data['_template'] = 'emails.report';
          $data['_subject'] = "Reporte aplicaciones $program->nombre_convocatoria";
          $data['_to'] = $user->correo_electronico;
          $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
          $data['_inst'] = $institution->codigo_institucion;
          $data['_url'] = $fileUrl;
          SendEmail::notification($data);

        } catch (\Exception $e) {
          Log::error(sprintf('%s:%s', 'ReportsCursos:Command', $e->getMessage()));
          abort(500);
        }
    }
}
