<?php

namespace App\Console\Commands\Rutan;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Core\Entidad,
    App\Models\Core\TipoSociedad,
    App\Models\Core\Convocatoria,
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
use App\Custom\Rutan;
use Log, Storage, Excel, DB, SendEmail, DateTime;

class ReportConvocatorias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:convocatoriarutan {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de convocatorias 209 de rutaN';

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
            $header = $data = [];

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

            \Log::info("Inicia Consulta \n");
            // Recuperar preguntas
            switch ($program->codigo_orientacion_convocatoria) {
                case OrientacionConvocatoria::getOrientation('entity'):

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
                                DB::raw("usuario.correo_electronico"),
                                'founder.codigo_usuario as codigo_founder',
                                'founder.nickname_usuario as nickname_founder',
                                'founder.telefono_usuario as telefono_founder',
                                'founder.movil_usuario as movil_founder',
                                'founder.correo_electronico as correo_founder',

                                'tipo_identificacion.nombre_tipo_identificacion as tipo_identificacion_founder',
                                'founder.identificacion_usuario as identificacion_founder',

                                DB::raw(
                                    "initcap(
                                        CASE
                                            WHEN founder.tipo_usuario = ".Usuario::typeUser('person')." THEN (trim(founder.nombre_usuario)) 
                                            ELSE founder.nombre_usuario
                                        END
                                        ) as nombre_founder"
                                    ),
                                DB::raw("initcap(trim(personafounder.primer_apellido_persona)) as primer_apellido_persona"),
                                DB::raw("initcap(trim(personafounder.segundo_apellido_persona)) as segundo_apellido_persona"),
                                'founder.fecha_nacimiento_usuario as fecha_nacimiento_founder',
                                'personafounder.genero_persona as sexo',
                                'nivel_educacion.nombre_nivel_educacion',
                                'usuario.identificacion_usuario as identificacion_empresa',
                                'usuario.nombre_usuario as nombre_empresa',
                                'cp.nombre_categoria_proyecto',
                                'etapa_proyecto.nombre_etapa_proyecto as tipo_empresa',
                                //'clase_emprendimiento_insti.nombre_clase_empre_insti',
                                'usuario.telefono_usuario as telefono_empresa',
                                'usuario.movil_usuario as movil_empresa',
                                'usuario.correo_electronico as correo_empresa',
                                'usuario.biografia_usuario as idea_negocio',
                                'entidad.codigo_clase_empre_insti'
                            );

                            $query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

                            // User Program
                            $query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
                            $query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

                            //
                            $query->join('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
                            $query->join('core.dato_entidad', 'dato_entidad.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');

                            //macrosector-microsector
                            $query->join('core.categoria_proyecto as cp', 'cp.codigo_categoria_proyecto', '=', 'dato_entidad.codigo_categoria_proyecto');

                            //tipo empresa
                            $query->join('core.etapa_proyecto', 'dato_entidad.codigo_etapa_proyecto', '=', 'etapa_proyecto.codigo_etapa_proyecto');

                            $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

                            $query->join('core.convocatoria_institucion', function($join) use($institution) {
                             $join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
                                 ->where('convocatoria_institucion.codigo_institucion', '=', $institution->codigo_institucion);
                            });
                            $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

                            $query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');

                            //tipo proyecto: empresa/proyecto
                            //$query->join('core.clase_emprendimiento_insti', 'clase_emprendimiento_insti.codigo_clase_empre_insti', '=', 'entidad.codigo_clase_empre_insti');

                            //founder
                            $query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');
                            $query->leftjoin('core.persona as personafounder', 'personafounder.codigo_usuario', '=', 'founder.codigo_usuario');

                            //type identification
                            $query->join('core.tipo_identificacion', 'founder.tipo_identificacion_usuario', '=', 'tipo_identificacion.codigo_tipo_identificacion');

                            $query->join('core.nivel_educacion', 'nivel_educacion.codigo_nivel_educacion', '=', 'personafounder.codigo_nivel_educacion');
                            $query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

                            $query->where('registro_fase_usuario.codigo_fase', '=', Rutan::getIdPhase('registro_ruta_emprendimiento'));
                            $query->where('registro_convocatoria.codigo_estado', Estado::getState('active'));
                            $query->orderBy('numero_registro_convocatoria','desc');
                            $usuarios = $query->get();
                            //dd($usuarios[0]);
                            // Prepare header
                            $header = [];
                            $header[] = 'Nº';
                            $header[] = 'Tipo identificación';
                            $header[] = 'Número documento';
                            $header[] = 'Primer nombre';
                            $header[] = 'Segundo nombre';
                            $header[] = 'Primer apellido';
                            $header[] = 'Segundo apellido';
                            $header[] = 'Fecha nacimiento DÍA';
                            $header[] = 'Fecha nacimiento MES';
                            $header[] = 'Fecha nacimiento AÑO';
                            $header[] = 'Edad';
                            $header[] = 'Genero';
                            $header[] = 'Etnia';
                            $header[] = 'Discapacidad';
                            $header[] = 'Victima';
                            $header[] = 'Población campesina';
                            $header[] = 'Estado civíl';
                            $header[] = 'Nivel escolaridad';
                            $header[] = 'Cabeza de hogar';
                            $header[] = 'LGBT';
                            $header[] = 'Caracterización ciudadano';
                            $header[] = '¿Se encuentra empleado actualmente?';
                            $header[] = 'Nombre de la empresa donde labora';
                            $header[] = 'Macrosector';
                            $header[] = 'Subsector';
                            $header[] = 'Tipo de emprendimiento';
                            $header[] = 'Descripción de idea de negocio';

                            $header[] = 'Dirección ciudadano';
                            $header[] = 'Nomenclatura ciudadano';
                            $header[] = 'Estrato';
                            $header[] = 'Barrio/vereda ciudadano';
                            $header[] = 'Comuna ciudadano';
                            $header[] = 'Zona ciudadano';
                            $header[] = 'Teléfono ciudadano';
                            $header[] = 'Móvil ciudadano';
                            $header[] = 'Correo ciudadano';

                            $header[] = 'Nit';
                            $header[] = 'Nombre de la empresa';

                            $header[] = 'Naturaleza jurídica';
                            $header[] = 'Tamaño';

                            $header[] = 'Macrosector';
                            $header[] = 'Subsector';
                            $header[] = 'Tipo empresa';

                            $header[] = 'Productos/Servicios que ofrece';
                            $header[] = 'Número de empleados';
                            $header[] = 'Esta formalizada';
                            $header[] = '¿Importa actualmente?';
                            $header[] = '¿Exporta actualmente?';
                            $header[] = '¿Asociada a RED?';
                            $header[] = '¿Está asociada a UPA?';
                            $header[] = '¿Pertenece a un CLÚSTER?';
                            $header[] = 'Ventas anuales del producto/servicio';
                            $header[] = 'Dirección empresa';
                            $header[] = 'Nomenclatura empresa';
                            $header[] = 'Barrio/Vereda empresa';
                            $header[] = 'Comuna empresa';
                            $header[] = 'Zona de empresa';
                            $header[] = 'Fijo empresa';
                            $header[] = 'Movil empresa';
                            $header[] = 'Correo empresa';
                            $header[] = 'RUT';
                            $header[] = 'IYC';
                            $header[] = 'RPM';
                            $header[] = 'RNT';

                            //empty columns
                            $header[] = 'NOMBRE DEL BENEFICIO OTORGADO';
                            $header[] = 'FECHA DE BENEFICIO DIA';
                            $header[] = 'FECHA DE BENEFICIO MES';
                            $header[] = 'FECHA DE BENEFICIO AÑO';
                            $header[] = 'SEDE QUE OTORGA EL BENEFICIO';
                            $header[] = 'FASE DEL PROCESO';
                            $header[] = 'FASE DEL EMPRENDIMIENTO';
                            $header[] = 'DIÁGNOSTICO';
                            $header[] = 'SEGUIMIENTO';
                            $header[] = 'COMO SE ENTERÓ DEL SERVICIO?';
                            $header[] = 'SUBSECRETARÍA/DIRECCIÓN';
                            $header[] = 'NOMBRE DE LA UNIDAD / PROGRAMA';
                            $header[] = 'NOMBRE DEL ALIADO';
                            $header[] = 'FUENTE DE FINANCIACION';
                            $header[] = 'NOMBRE DEL PROYECTO';
                            $header[] = 'CODIGO DEL CONTRATO';
                            $header[] = 'NOMBRE GESTOR';
                            $header[] = 'NUMERO SOLICITUD';
                            $header[] = 'ESTADO SOLICITUD';
                            $header[] = 'CAUSALES NEGACION';
                            $header[] = 'ESTADO TRAMITE';
                            $header[] = 'OBSERVACIONES';
                            $header[] = '¿Proviene de etapa previa de CVS 2021?';
                            $header[] = '¿Cual etapa?';
                            $header[] = 'Observaciones';

                            //Prepare data
                            $data = collect([]);
                            foreach ($usuarios as $register)
                            {
                                $nombre_founder = explode(" ", $register->nombre_founder);
                                $fecha_nacimiento = explode("-", $register->fecha_nacimiento_founder);
                                $nombre_categoria_proyecto = explode("-", $register->nombre_categoria_proyecto);
 
                                unset($record);
                                $record = [];
                                $record[] = $register->numero_registro_convocatoria;
                                $record[] = $register->tipo_identificacion_founder;
                                $record[] = $register->identificacion_founder;
                                $record[] = empty($nombre_founder[0]) ? "" : mb_strtoupper($nombre_founder[0]);
                                $record[] = empty($nombre_founder[1]) ? "" : mb_strtoupper($nombre_founder[1]);
                                $record[] = mb_strtoupper($register->primer_apellido_persona);
                                $record[] = mb_strtoupper($register->segundo_apellido_persona);
                                $record[] = empty($fecha_nacimiento[2]) ? '' : (int) $fecha_nacimiento[2];
                                $record[] = empty($fecha_nacimiento[1]) ? '' : (int) $fecha_nacimiento[1];
                                $record[] = empty($fecha_nacimiento[0]) ? '' : (int) $fecha_nacimiento[0];

                                $nacimiento = new DateTime($register->fecha_nacimiento_founder);
                                $ahora = new DateTime(date("Y-m-d"));
                                $diferencia = $ahora->diff($nacimiento);
                                $record[] = $diferencia->format("%y"); //EDAD

                                $record[] = $this->getGender($register->sexo);
                                $record[] = $this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12255, 12254])?: 'OTRO'; //para etnia:afrocolombiano, indigena
                                $record[] = $this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12249]) ? 'SI' : 'NO'; //discapacidad
                                $record[] = $this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12252]) ? 'SI' : 'NO'; //victima
                                $record[] = $this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12253]) ? 'SI' : 'NO'; //pobracion campesina
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6040); //estado civil
                                $record[] = $register->nombre_nivel_educacion; //nivel escolaridad
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6041); //cabeza hogar
                                $record[] = $this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12250]) ? 'SI' : 'NO'; //lgtbi oriendation sexual
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6037); //caracterizacion ciudadano
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6038); //empleado ?
                                $record[] = $this->answer_written($register->codigo_usuario_encuesta, 6039); //nombre empresa donde labora
                                $record[] = $register->codigo_clase_empre_insti == 61 ? '' : $nombre_categoria_proyecto[0]; // si no es empresa trae macrosector de proyectos
                                $record[] = $register->codigo_clase_empre_insti == 61 ? '' : $nombre_categoria_proyecto[1]; // si no es empresa trae microsector de proyectos
                                $record[] = $register->codigo_clase_empre_insti == 61 ? '' : $register->tipo_empresa;
                                $record[] = $register->codigo_clase_empre_insti == 61 ? '' : trim($register->idea_negocio); //idea_negocio

                                $record[] = $this->answer_written($register->codigo_usuario_encuesta, 6033); //6033 direccion ciudadano
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6032); //6032
                                $record[] = str_replace('ESTRATO', "", $this->answer_select_sigle($register->codigo_usuario_encuesta, 6035)); //6035 ESTRATO
                                $barrio = $this->answer_select_sigle_divided($register->codigo_usuario_encuesta, 6027, 3); //barrio-comuna-zona ciudadano
                                foreach ($barrio as $key => $value) {
                                    if ($key == 1) {
                                        $count =  1;
                                        $record[] = substr_replace($value, '-', 2, $count);
                                    } else {
                                        $record[] = $value;
                                    }
                                }
                                //unset($barrio);
                                $record[] = $register->telefono_founder;
                                $record[] = $register->movil_founder;
                                $record[] = $register->correo_founder;

                                $record[] = $register->identificacion_empresa; //nit
                                $record[] = $register->nombre_empresa; //nombre empresa

                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6046); //naturaleza juridica
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6047); //tamaño

                                $record[] = $register->codigo_clase_empre_insti == 60 ? '' : $nombre_categoria_proyecto[0]; //si es empresa trae macrosector empresa
                                $record[] = $register->codigo_clase_empre_insti == 60 ? '' : $nombre_categoria_proyecto[1]; //si es empresa trae subsector de empresa
                                $record[] = $register->codigo_clase_empre_insti == 60 ? '' : $register->tipo_empresa;; //Tipo empresa

                                $record[] = $this->answer_written($register->codigo_usuario_encuesta, 6048); // Productos/Servicios
                                $record[] = $this->answer_written($register->codigo_usuario_encuesta, 6049); // numero empleados
                                //$record[] = $register->clase_emprendimiento_insti; // esta formalizada
                                $record[] = empty($register->identificacion_empresa) ? 'NO' : 'SI';//está formamlizada ? - si tiene nit entonces está formalizada

                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6051); //importa actualmente
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6052); //exporta actualmente
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6053); //asociada a red
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6054); // asociada a upa
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6055); //cluster
                                $record[] = $this->answer_written($register->codigo_usuario_encuesta, 6056); //Promedio de ventas anual
                                $record[] = $this->answer_written($register->codigo_usuario_encuesta, 6058); //direccion empresa
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6057); //nomenclatura empresa
                                $barrio = $this->answer_select_sigle_divided($register->codigo_usuario_encuesta, 6059, 3); //barrio-comuna-zona empresa
                                foreach ($barrio as $value) {
                                    if ($key == 1) {
                                        $count =  1;
                                        $record[] = substr_replace($value, '-', 2, $count);
                                    } else {
                                        $record[] = $value;
                                    }
                                }

                                $record[] = $register->telefono_empresa;
                                $record[] = $register->movil_empresa;
                                $record[] = $register->correo_empresa;
                                $record[] = empty($register->identificacion_empresa) ? 'NO' : 'SI';
                                $record[] = empty($register->identificacion_empresa) ? 'NO' : 'SI';
                                $record[] = empty($register->identificacion_empresa) ? 'NO' : 'SI';
                                $record[] = empty($register->identificacion_empresa) ? 'NO' : 'SI';

                                //empty columns
                                $record[] = '2. Orientación (Ingreso al programa)'; //NOMBRE DEL BENEFICIO OTORGADO
                                $record[] = '';
                                $record[] = '';
                                $record[] = '';
                                $record[] = 'Otro';
                                $record[] = '';
                                $record[] = '';
                                $record[] = ''; //DIÁGNOSTICO
                                $record[] = ''; //SEGUIMIENTO
                                $record[] = $this->answer_select_sigle($register->codigo_usuario_encuesta, 6172); //COMO SE ENTERÓ DEL SERVICIO?'
                                $record[] = 'SUBSECRETARIA DE DESARROLLO ECONÓMICO';
                                $record[] = 'PROGRAMA CVS 2022';
                                $record[] = 'CORPORACIÓN RUTA N';
                                $record[] = 'TRANSFERENCIA';
                                $record[] = 'CVS 2022';
                                $record[] = 'NA';
                                $record[] = '';
                                $record[] = 'NA'; //NUMERO SOLICITUD
                                $record[] = 'NA';
                                $record[] = 'NA';
                                $record[] = 'NA';
                                $record[] = '';//OBSERVACIONES
                                $record[] = '';
                                $record[] = '';
                                $record[] = '';

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
            $data['_subject'] = "Reporte único de convocatoria RutaN";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

            \Log::info("Envio correo \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportConvocatorias:command', $e->getMessage()));
            abort(500);
        }
    }

    public function caracteristicas_diferenciales(int $codigo_usuario_encuesta, array $codigo_opcion_encuesta): string
    {
        $query = RespuestaEncuesta::query();

		$query->select('texto_opcion_encuesta');
        $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', 6043);
        $query->where('codigo_usuario_encuesta', $codigo_usuario_encuesta);

        $query->whereIn('opcion_encuesta.codigo_opcion_encuesta', $codigo_opcion_encuesta);
        $query = $query->get();
        $count = $query->count();

        if ($count == 1) {
            $texto = $query[0]->texto_opcion_encuesta;
        } elseif ($count == 2) {
            $texto = '';
            foreach ($query as $key => $value) {
                $texto = $value->texto_opcion_encuesta;
                // if ($key == 0) {
                //     $texto .= "\n";
                // }
            }

        } else {
                $texto = false;
        }

        return mb_strtoupper($texto);

    }

    public function answer_select_sigle(int $codigo_usuario_encuesta, $codigo_pregunta_encuesta): string
    {
        $query = RespuestaEncuesta::query();
        
		$query->select('texto_opcion_encuesta');
        $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', $codigo_pregunta_encuesta);
        $query->where('codigo_usuario_encuesta', $codigo_usuario_encuesta);
        $query = $query->get();
        $count = $query->count();
        
        return $count > 0 ? mb_strtoupper($query[0]->texto_opcion_encuesta) : '';
    }

    public function answer_written(int $codigo_usuario_encuesta, $codigo_pregunta_encuesta): string
    {
        $query = RespuestaEncuesta::query();
        
		$query->select('texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', $codigo_pregunta_encuesta);
        $query->where('codigo_usuario_encuesta', $codigo_usuario_encuesta);
        $query = $query->get();
        $count = $query->count();
        return $count > 0 ? $query[0]->texto_respuesta_encuesta : '';
    }

    public function answer_select_sigle_divided(int $codigo_usuario_encuesta, int $codigo_pregunta_encuesta, $total_elements): array
    {
        $query = RespuestaEncuesta::query();
        
		$query->select('texto_opcion_encuesta');
        $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', $codigo_pregunta_encuesta);
        $query->where('codigo_usuario_encuesta', $codigo_usuario_encuesta);
        $query = $query->get();
        $count = $query->count();
        $explode = [];
        if ($count > 0) {
            $explode = explode("-", $query[0]->texto_opcion_encuesta);
        } else {
            for ($total_elements; $total_elements > 0 ; $total_elements--) {
                $explode[] = '';
            }
        }
        return $explode;
    }

    private function getGender(int $sexo): string
    {
	    $gender = [
            1 => 'Masculino',
            2  => 'Femenino',
            3 => 'Prefiero no decirlo'
        ];

        return $gender[$sexo];
    }
}