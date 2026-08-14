<?php

namespace App\Console\Commands\Rutan;

use App\Console\Commands\Reports\Evaluation;

use App\Custom\Rutan;

use App\Models\Core\{Convocatoria, Estado,
    EvaluacionEmprendimiento, OrientacionConvocatoria,
    RegistroFaseUsuario, RespuestaEncuesta,
    ResultadoEvaluacionEmprendimiento};

use App\Models\Param\Institucion;

use App\Models\Seguridad\Usuario;

use Illuminate\Console\Command;

use DateTime, DB, Excel, Log, SendEmail, Storage;

class ReportConvocatoriaRutaEmprendimiento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:convocatoriarutanrutaemprendimiento {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de perfilamiento';

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

                            $evaluation = 28; //Evaluation of Ruta N

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
                                'registro_convocatoria.codigo_estado as estado_postulacion',
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
                                'usuario.telefono_usuario as telefono_empresa',
                                'usuario.movil_usuario as movil_empresa',
                                'usuario.correo_electronico as correo_empresa',
                                'usuario.biografia_usuario as idea_negocio',
                                'entidad.codigo_clase_empre_insti',
                                'cp.nombre_categoria_proyecto',
                                'etapa_proyecto.nombre_etapa_proyecto as tipo_empresa',
                                'clase_emprendimiento.nombre_clase_emprendimiento',
                                'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion',
                                'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
                                'asignacion_jurado.codigo_estado as estado_jurado',
                                DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"),
                                DB::raw("initcap((CASE jurado.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN jurado.nombre_usuario ||
                                        ' ' || COALESCE(persona_jurado.primer_apellido_persona, '') || ' ' || COALESCE(persona_jurado.segundo_apellido_persona, '')
                                    ELSE jurado.nombre_usuario END)) as nombre_jurado")
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

                            $query->leftJoin('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
                            $query->leftJoin('seguridad.usuario as jurado', 'jurado.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
                            $query->leftJoin('core.persona as persona_jurado', 'jurado.codigo_usuario', '=', 'persona_jurado.codigo_usuario');

                            // Get Results Evaluation
                            $query->join('core.resultado_evaluacion_emprendimiento', function ($join) use ($evaluation) {
                                $join->on('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento')
                                ->on('resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado')
                                ->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', $evaluation);
                            });
                            //Type entity: company/project
                            $query->join('core.clase_emprendimiento_insti', 'clase_emprendimiento_insti.codigo_clase_empre_insti', '=', 'entidad.codigo_clase_empre_insti');
                            $query->join('core.clase_emprendimiento', 'clase_emprendimiento.codigo_clase_emprendimiento', '=', 'clase_emprendimiento_insti.codigo_clase_emprendimiento');

                            //founder
                            $query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');
                            $query->leftjoin('core.persona as personafounder', 'personafounder.codigo_usuario', '=', 'founder.codigo_usuario');

                            //type identification
                            $query->join('core.tipo_identificacion', 'founder.tipo_identificacion_usuario', '=', 'tipo_identificacion.codigo_tipo_identificacion');

                            $query->join('core.nivel_educacion', 'nivel_educacion.codigo_nivel_educacion', '=', 'personafounder.codigo_nivel_educacion');

                            $query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
                            $query->where('registro_fase_usuario.codigo_fase', '=', Rutan::getIdPhase('registro_ruta_emprendimiento'));
                            $query->where('registro_convocatoria.codigo_estado', Estado::getState('active'));
                            $query->where('fase.codigo_evaluacion_emprendimiento', $evaluation);

                            $query->orderBy('numero_registro_convocatoria','desc');
                            $registers = $query->get();

                            // Prepare header
                            $header = [];
                            $header[] = 'Nº Postulación';
                            $header[] = 'Estado Postulación';
                            $header[] = 'Fecha-Hora Postulación';

                            //Headers user
                            $header[] = 'Correo';
                            $header[] = 'Tipo Identificación';
                            $header[] = 'Número Documento';
                            $header[] = 'Primer Nombre';
                            $header[] = 'Segundo Nombre';
                            $header[] = 'Primer Apellido';
                            $header[] = 'Segundo Apellido';
                            $header[] = 'Fecha Nacimiento';
                            $header[] = 'Edad';
                            $header[] = 'Genero';
                            $header[] = 'Nivel De Educación';
                            $header[] = 'Celular';
                            $header[] = 'Telefono';
                            $header[] = 'Reside En Medellín';
                            $header[] = 'Barrio/Vereda';
                            $header[] = 'Comuna';
                            $header[] = 'Zona';
                            $header[] = 'Nomenclatura';
                            $header[] = 'Dirección';
                            $header[] = 'Estrato Socioeconómico';
                            $header[] = 'Caracterización Ciudadana';
                            $header[] = 'Empleado';
                            $header[] = 'Nombre Empresa Donde Labora';
                            $header[] = 'Estado Civil';
                            $header[] = 'Cabeza Hogar';
                            $header[] = 'Etnia';
                            $header[] = 'Discapacidad';
                            $header[] = 'Victima';
                            $header[] = 'Población Campesina';
                            $header[] = 'LGBT';

                            //Headers entity
                            $header[] = 'Nombre Emprendimiento';
                            $header[] = 'Tipo Emprendimiento';
                            $header[] = 'Descripción Idea De Negocio';
                            $header[] = 'Macrosector';
                            $header[] = 'Subsector';
                            $header[] = 'Tipo De Emprendimiento';
                            $header[] = 'Empresa Registrada Y Constituida';
                            $header[] = 'Nit';
                            $header[] = 'Naturaleza Jurìdica (Empresa)';
                            $header[] = 'Tamaño (Empresa)';
                            $header[] = 'Barrio/Vereda (Empresa)';
                            $header[] = 'Comuna (Empresa)';
                            $header[] = 'Zona (Empresa)';
                            $header[] = 'Nomenclatura (Empresa)';
                            $header[] = 'Dirección (Empresa)';
                            $header[] = 'Teléfono (Empresa)';
                            $header[] = 'Celular (Empresa)';
                            $header[] = 'Correo (Empresa)';
                            $header[] = 'Productos O Servicios (Empresa)';
                            $header[] = 'Número Empleados (Empresa)';
                            $header[] = 'Importa (Empresa)';
                            $header[] = 'Exporta (Empresa)';
                            $header[] = 'Asociado A Una Red (Empresa)';
                            $header[] = 'Asociado A UPA (Empresa)';
                            $header[] = 'Pertenece A Un Cluster (Empresa)';
                            $header[] = 'Pertenece A Sector Creativo';
                            $header[] = 'Identificación Iniciativa Negocio';
                            $header[] = 'Fortalecimiento Pertinente';
                            $header[] = 'Descripción Componente Tecnológico';
                            $header[] = 'Problema Que Resuelven'; //contar desde para el for NA
                            $header[] = 'Solución Al Problema Que Resuelven';
                            $header[] = 'Perfil URL';
                            $header[] = 'Descripción Del Mercado';
                            $header[] = 'Sectores De Aplicación';
                            $header[] = 'Subsectores De Aplicación';
                            $header[] = 'Tamaño Mercado';
                            $header[] = 'Estrategia Monetización';
                            $header[] = 'Competencia';
                            $header[] = 'Ventaja Competitiva';
                            $header[] = 'Tipo Propiedad Intelectual';
                            $header[] = 'Acceso A Mercados';
                            $header[] = 'Necesidades Para Comercialización';
                            $header[] = 'Tiene Integrante 1';
                            $header[] = 'Nombre Integrante 1';
                            $header[] = 'Rol Integrante 1';
                            $header[] = 'Descripción Perfil Integrante 1';
                            $header[] = 'Tiene Integrante 2';
                            $header[] = 'Nombre Integrante 2';
                            $header[] = 'Rol Integrante 2';
                            $header[] = 'Descripción Perfil Integrante 2';
                            $header[] = 'Tiene Integrante 3';
                            $header[] = 'Nombre Integrante 3';
                            $header[] = 'Rol Integrante 3';
                            $header[] = 'Descripción Perfil Integrante 3';
                            $header[] = 'Tiene Integrante 4';
                            $header[] = 'Nombre Integrante 4';
                            $header[] = 'Rol Integrante 4';
                            $header[] = 'Descripción Perfil Integrante 4';
                            $header[] = 'Cuenta Con Ventas Anuales (Producto/Servicio)';
                            $header[] = 'Ventas Anuales (Producto/Servicio)';
                            $header[] = 'Unidades Vendidas Anuales';
                            $header[] = 'Tasa De Crecimiento';
                            $header[] = 'Clientes Cautivos';
                            $header[] = 'Porcentaje Recurrencia De Compra';
                            $header[] = 'Tiempo Estimado En Salir Al Mercado';
                            $header[] = 'Capitalizaciones Previas';
                            $header[] = 'Valor Indicado';
                            $header[] = 'Número Inversionistas';
                            $header[] = 'Capital Requerido';
                            $header[] = 'Forma Inscripción Oferta';
                            $header[] = 'Sede Inscripción Oferta';
                            $header[] = 'Cómo Se Enteró Del Servicio';
                            $header[] = 'Nombre Perfilador';
                            $header[] = 'Estado Perfilamiento';




                            $questions = $this->getQuestionsEvaluation($evaluation);
                            $eval = new Evaluation();
                            $groups = $eval->getGroupsEvaluations($institution->codigo_institucion);

                            // Build headers profiling
                            $result_text = '';
                            foreach ($questions as $item_question) {
                                if (isset($item_question->codigo_grupo_item_eval) && !empty($item_question->codigo_grupo_item_eval)) {
                                    foreach ($groups as $item_groups) {
                                    if ($item_groups->codigo_grupo_item_eval == $item_question->codigo_grupo_item_eval) {
                                        $header[] = strip_tags($item_question->nombre_item_evaluacion);
                                    }
                                    }
                                } else {
                                    $header[] = strip_tags($item_question->nombre_item_evaluacion);
                                }
                            }

                            $header[] = 'Observación';

                            //Prepare data
                            $data = collect([]);
                            foreach ($registers as $register)
                            {
                                $nombre_founder = explode(" ", $register->nombre_founder);
                                //$fecha_nacimiento = explode("-", $register->fecha_nacimiento_founder);
                                $nombre_categoria_proyecto = explode("-", $register->nombre_categoria_proyecto);

                                unset($record);
                                $record = [];
                                $record[] = $register->numero_registro_convocatoria;
                                $record[] = Estado::getNameState($register->estado_postulacion);
                                $record[] = $register->fecha_registro_fase_usuario;

                                //Data user
                                $record[] = $register->correo_founder;
                                $record[] = $register->tipo_identificacion_founder;
                                $record[] = $register->identificacion_founder;
                                $record[] = empty($nombre_founder[0]) ? "" : mb_convert_case($nombre_founder[0], MB_CASE_TITLE, "UTF-8");
                                $record[] = empty($nombre_founder[1]) ? "" : mb_convert_case($nombre_founder[1], MB_CASE_TITLE, "UTF-8");
                                $record[] = mb_convert_case($register->primer_apellido_persona, MB_CASE_TITLE, "UTF-8");
                                $record[] = mb_convert_case($register->segundo_apellido_persona, MB_CASE_TITLE, "UTF-8");

                                $timestamp = strtotime($register->fecha_nacimiento_founder);
                                $new_date = date("d-m-Y", $timestamp);
                                $record[] = $new_date;

                                $birth_date = new DateTime($register->fecha_nacimiento_founder);
                                $date_currente = new DateTime(date("Y-m-d"));
                                $age = $date_currente->diff($birth_date);
                                $record[] = $age->format("%y"); //age

                                $record[] = $this->getGender($register->sexo);
                                $record[] = $register->nombre_nivel_educacion;
                                $record[] = $register->movil_founder;
                                $record[] = $register->telefono_founder;
                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6026), MB_CASE_TITLE, "UTF-8"); //Vives en Medellín

                                $neighborhood = $this->answer_select_sigle_divided($register->codigo_usuario_encuesta, 6027, 3); //Barrio-comuna-zona ciudadano
                                $record[] = $neighborhood[0];
                                $record[] = $neighborhood[1];
                                $record[] = $neighborhood[2];

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6032), MB_CASE_TITLE, "UTF-8"); //Nomenclatura Ciudadano
                                $record[] = mb_convert_case($this->answer_written($register->codigo_usuario_encuesta, 6033), MB_CASE_TITLE, "UTF-8"); // Direccion Ciudadano
                                $record[] = str_replace('ESTRATO', "", $this->answer_select_sigle($register->codigo_usuario_encuesta, 6035)); //Estrato Socieconómico
                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6037), MB_CASE_TITLE, "UTF-8"); //Caracterizacion Ciudadano
                                $is_employed = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6038), MB_CASE_TITLE, "UTF-8"); //Empleado ?
                                $record[] = empty($is_employed) ? "No Aplica" :  $is_employed; //Empleado ?
                                $name_company_employed = mb_convert_case($this->answer_written($register->codigo_usuario_encuesta, 6039), MB_CASE_TITLE, "UTF-8"); //Nombre Empresa Donde Labora
                                $record[] =  empty($name_company_employed) ? "No Aplica" : $name_company_employed;//Nombre Empresa Donde Labora
                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6040), MB_CASE_TITLE, "UTF-8"); //Estado Civil
                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6041), MB_CASE_TITLE, "UTF-8"); //Cabeza Hogar
                                $record[] = mb_convert_case($this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12255, 12254])?: 'Otro', MB_CASE_TITLE, "UTF-8"); //Etnia:Afrocolombiano, Indigena
                                $record[] = mb_convert_case($this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12249]) ? 'Si' : 'No', MB_CASE_TITLE, "UTF-8"); //Discapacidad
                                $record[] = mb_convert_case($this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12252]) ? 'Si' : 'No', MB_CASE_TITLE, "UTF-8"); //Victima
                                $record[] = mb_convert_case($this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12253]) ? 'Si' : 'No', MB_CASE_TITLE, "UTF-8"); //Poblacion Campesina
                                $record[] = mb_convert_case($this->caracteristicas_diferenciales($register->codigo_usuario_encuesta, [12250]) ? 'Si' : 'No', MB_CASE_TITLE, "UTF-8");//LGBT

                                //Data entity
                                $record[] = trim($register->nombre_empresa); //Nombre Emprendimiento
                                $record[] = $register->nombre_clase_emprendimiento; //Tipo Entidad
                                $record[] = trim($register->idea_negocio); //Idea Negocio
                                $record[] = mb_convert_case($nombre_categoria_proyecto[0], MB_CASE_TITLE, "UTF-8"); // Macrosector
                                $record[] = mb_convert_case($nombre_categoria_proyecto[1], MB_CASE_TITLE, "UTF-8"); // Subsector
                                $record[] = $register->tipo_empresa;; //Tipo Emprendimiento
                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6044), MB_CASE_TITLE, "UTF-8"); //Empresa Registrada
                                $record[] = empty($register->identificacion_empresa) ? 'No Aplica' : $register->identificacion_empresa; //Nit

                                $legal_nature = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6046), MB_CASE_TITLE, "UTF-8"); //Naturaleza Juridica

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($legal_nature)) {
                                    $record[] = 'Sin Registro';

                                }else {
                                    $record[] = $legal_nature; //Naturaleza Juridica
                                }

                                $size_company = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6047), MB_CASE_TITLE, "UTF-8"); //Tamaño Empresa

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($size_company)) {
                                    $record[] = 'Sin Registro';

                                }else {
                                    $record[] = $size_company; //Tamaño Empresa
                                }

                                $neighborhood_company = $this->answer_select_sigle_divided($register->codigo_usuario_encuesta, 6059, 3); //Barrio-Comuna-Zona Empresa

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    for ($i=1; $i <= 3 ; $i++) {
                                        $record[] = 'No Aplica';
                                    }
                                }elseif (empty($neighborhood_company)) {
                                    for ($i=1; $i <= 3 ; $i++) {
                                        $record[] = 'Sin Registro';
                                    }
                                }else {
                                    $record[] = $neighborhood_company[0]; //Barrio-Comuna-Zona Empresa
                                    $record[] = $neighborhood_company[1];
                                    $record[] = $neighborhood_company[2];
                                }

                                $company_nomenclature = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6057), MB_CASE_TITLE, "UTF-8"); //Nomenclatura Empresa

                                if ( $register->codigo_clase_empre_insti == 60) {
                                        $record[] = 'No Aplica';
                                }elseif (empty($company_nomenclature)) {
                                        $record[] = 'Sin Registro';
                                }else {
                                    $record[] = $company_nomenclature; //Nomenclatura Empresa
                                }

                                $company_address = mb_convert_case($this->answer_written($register->codigo_usuario_encuesta, 6058), MB_CASE_TITLE, "UTF-8"); //Direccion Empresa

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($company_address)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                $record[] =  $company_address; //Direccion Empresa
                                }

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($register->telefono_empresa)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                $record[] =  $register->telefono_empresa; //Telefono Empresa
                                }

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($register->movil_empresa)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                $record[] =  $register->movil_empresa; //Movil Empresa
                                }

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($register->correo_empresa)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                $record[] = $register->correo_empresa; //Correo Empresa
                                }

                                $products_services = $this->answer_written($register->codigo_usuario_encuesta, 6048); // Productos/Servicios

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($products_services)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] =  $products_services; //Productos/Servicios
                                }

                                $number_employes = $this->answer_written($register->codigo_usuario_encuesta, 6049); // Número Empleados

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($number_employes)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] =  $number_employes; //Número Empleados
                                }

                                $import = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6051), MB_CASE_TITLE, "UTF-8"); //Importa

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($import)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] = $import; //Importa
                                }

                                $export = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6052), MB_CASE_TITLE, "UTF-8");//Exporta

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($export)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] = $export; //Exporta
                                }

                                $associated_red = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6053), MB_CASE_TITLE, "UTF-8"); //Asociada Red

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($associated_red)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] = $associated_red; //Asociada Red
                                }

                                $associated_upa = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6054), MB_CASE_TITLE, "UTF-8"); //Asociado UPA

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($associated_upa)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] = $associated_upa; //Asociado UPA
                                }

                                $belongs_cluster = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6055), MB_CASE_TITLE, "UTF-8"); //Pertenece Cluster

                                if ( $register->codigo_clase_empre_insti == 60) {
                                    $record[] = 'No Aplica';
                                }elseif (empty($belongs_cluster)) {
                                    $record[] = 'Sin Registro';
                                }else {
                                    $record[] = $belongs_cluster; //Pertenece Cluster
                                }

                                $belongs_creative_sector = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6173), MB_CASE_TITLE, "UTF-8"); //Pertenece Sector Creativo
                                $record[] = empty($belongs_creative_sector) ? "Sin Registro" : $belongs_creative_sector; //Pertenece Sector Creativo

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6060), MB_CASE_TITLE, "UTF-8"); //Identificación Iniciativa Negocio

                                $code_indetification_business =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6060);

                                switch ($code_indetification_business) {
                                    case 12669: //Tengo una idea que quiero comenzar a explorar.
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6061), MB_CASE_TITLE, "UTF-8"); //Fortalecimiento Pertinente
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6062), MB_CASE_TITLE, "UTF-8"); //Descripcion Componente Tecnologico
                                        for ($i=1; $i <= 13; $i++) {
                                            $record[] = 'No Aplica';
                                        }
                                        break;
                                    case 12670: //Cuento con un concepto de producto o servicio que quiero desarrollar y un modelo de negocio inicial.
                                        $record[] = 'No Aplica';
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6063), MB_CASE_TITLE, "UTF-8"); //Descripcion Componente Tecnologico
                                        for ($i=1; $i <= 13; $i++) {
                                            $record[] = 'No Aplica';
                                        }
                                        break;
                                    case 12671: //Tengo un producto o servicio y quiero promover o aumentar su participación en el mercado.
                                        $record[] = 'No Aplica';
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6065),   MB_CASE_TITLE, "UTF-8"); //Descripcion Componente Tecnologico
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6066)),  MB_CASE_TITLE, "UTF-8"); //Problema  Que Resuelven
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6067)),  MB_CASE_TITLE, "UTF-8"); //Solución Al Problema Que Resuelven
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6069)),  MB_CASE_TITLE, "UTF-8"); //Perfil URL
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6070)),  MB_CASE_TITLE, "UTF-8"); //Decripción Mercado
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6071),   MB_CASE_TITLE, "UTF-8"); //Sectores De Aplicación
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6072),   MB_CASE_TITLE, "UTF-8"); //Subsectores De Aplicación
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6073)),  MB_CASE_TITLE, "UTF-8"); //Tamaño Mercado
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6074)),  MB_CASE_TITLE, "UTF-8"); //Estrategia Monetización
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6075)),  MB_CASE_TITLE, "UTF-8"); //Competencia
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6076)),  MB_CASE_TITLE, "UTF-8"); //Ventaja Competitiva
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6077),   MB_CASE_TITLE, "UTF-8"); //Tipo Propiedad Intelectual
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6078)),  MB_CASE_TITLE, "UTF-8"); //Acceso A Mercados
                                        $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6079)),  MB_CASE_TITLE, "UTF-8"); //Necesidades Para Comercialización
                                        break;
                                    case 13646: //Sin Registro
                                        for ($i=1; $i <= 15; $i++) {
                                            $record[] = 'Sin Registro';
                                        }
                                        break;
                                    default:
                                    for ($i=1; $i <= 16; $i++) {
                                        $record[] =  "Sin Registro";
                                    }
                                    break;
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6080),   MB_CASE_TITLE, "UTF-8"); //Tiene Integrante 1
                                $has_member_1 =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6080);

                                if ($has_member_1 == 13022) { //Si
                                    $name_member_1 = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6081)),  MB_CASE_TITLE, "UTF-8"); //Nombre Integrante 1
                                    $record[] = empty($name_member_1) ? "Sin Registro" : $name_member_1;
                                    $rol_member_1 = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6082)),  MB_CASE_TITLE, "UTF-8"); //Rol Integrante 1
                                    $record[] = empty($rol_member_1) ? 'Sin Registro' : $rol_member_1;
                                    $resumen_member_1 = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6083)),  MB_CASE_TITLE, "UTF-8"); //Descripción Perfil Integrante 1
                                    $record[] = empty($resumen_member_1) ? 'Sin Registro' : $resumen_member_1;
                                }elseif ($has_member_1 == 13023) {//No
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = 'No Aplica';
                                    }
                                }else {
                                    for ($i=1; $i <=3 ; $i++) {
                                        $$record[] = "Sin Registro";
                                    }
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6084),   MB_CASE_TITLE, "UTF-8"); //Tiene Integrante 2
                                $has_member_2 =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6084);
                                if ($has_member_2 == 13024) { //Si
                                    $name_member_2 = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6085)),  MB_CASE_TITLE, "UTF-8"); //Nombre Integrante 2
                                    $record[] = empty($name_member_2) ? "Sin Registro" : $name_member_2;
                                    $rol_member_2 = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6086)),  MB_CASE_TITLE, "UTF-8"); //Rol Integrante 2
                                    $record[] = empty($rol_member_2) ? 'Sin Registro' : $rol_member_2;
                                    $resumen_member_2 = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6087)),  MB_CASE_TITLE, "UTF-8"); //Descripción Perfil Integrante 2
                                    $record[] = empty($resumen_member_2) ? 'Sin Registro' : $resumen_member_2;
                                }elseif ($has_member_2 == 13025) {//No
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = 'No Aplica';
                                    }
                                }else {
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = "Sin Registro";
                                    }
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6088),   MB_CASE_TITLE, "UTF-8"); //Tiene Integrante 3
                                $has_member_3 =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6088);
                                if ($has_member_3 == 13026) { //Si
                                    $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6089)),  MB_CASE_TITLE, "UTF-8"); //Nombre Integrante 3
                                    $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6090)),  MB_CASE_TITLE, "UTF-8"); //Rol Integrante 3
                                    $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6091)),  MB_CASE_TITLE, "UTF-8"); //Descripción Perfil Integrante 3
                                }elseif ($has_member_3 == 13027) {//No
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = 'No Aplica';
                                    }
                                }else {
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = "Sin Registro";
                                    }
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6092),   MB_CASE_TITLE, "UTF-8"); //Tiene Integrante 4
                                $has_member_4 =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6092);
                                if ($has_member_4 == 13028) { //Si
                                    $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6093)),  MB_CASE_TITLE, "UTF-8"); //Nombre Integrante 4
                                    $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6094)),  MB_CASE_TITLE, "UTF-8"); //Rol Integrante 4
                                    $record[] = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6095)),  MB_CASE_TITLE, "UTF-8"); //Descripción Perfil Integrante 4
                                }elseif ($has_member_4 == 13029) {//No
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = 'No Aplica';
                                    }
                                }else {
                                    for ($i=1; $i <=3 ; $i++) {
                                        $record[] = "Sin Registro";
                                    }
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6096),   MB_CASE_TITLE, "UTF-8"); //Cuenta Con Ventas Anuales (Producto/Servicio)
                                $has_sales =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6096);

                                switch ($has_sales) {
                                    case 13030: //Si
                                        $annual_sales = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6097)),  MB_CASE_TITLE, "UTF-8"); //Ventas Anuales (Producto/Servicio)
                                        $record[] =  empty($annual_sales) ? "Sin Registro" : (double) $annual_sales;
                                        $sold_units = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6098)),  MB_CASE_TITLE, "UTF-8"); //Unidades Vendidas Anuales
                                        $record[] =  empty($sold_units) ? "Sin Registro" : (double) $sold_units;
                                        $growth_rate = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6099)),  MB_CASE_TITLE, "UTF-8"); //Tasa De Crecimiento
                                        $record[] =  empty($growth_rate) ? "Sin Registro" : (string) $growth_rate;
                                        $customers = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6100)),  MB_CASE_TITLE, "UTF-8"); //Clientes Cautivos
                                        $record[] =  empty($customers) ? "Sin Registro" : (double) $customers;
                                        $recurrence_percentage = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6101)),  MB_CASE_TITLE, "UTF-8"); //Porcentaje Recurrencia De Compra
                                        $record[] =  empty($recurrence_percentage) ? "Sin Registro" : (string) $recurrence_percentage;
                                        $record[] =  "No Aplica";
                                        break;
                                    case 13031: //No
                                        for ($i=1; $i <= 5; $i++) {
                                            $record[] =  "No Aplica";
                                        }
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6103),   MB_CASE_TITLE, "UTF-8"); //Tiempo Estimado En Salir Al Mercado
                                        break;
                                    case 13650: //Sin Registro
                                        for ($i=1; $i <= 6; $i++) {
                                            $record[] =  "Sin Registro";
                                        }
                                        break;
                                    default:
                                        for ($i=1; $i <= 6; $i++) {
                                            $record[] =  "Sin Registro";
                                        }
                                        break;
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6104),   MB_CASE_TITLE, "UTF-8"); //Capitalizaciones Previas
                                $previous_capitalizations =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6104);

                                switch ($previous_capitalizations) {
                                    case 13042: //Si
                                        $value = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6105)),  MB_CASE_TITLE, "UTF-8"); //Valor Indicado
                                        $record[] =  empty($value) ? "Sin Registro" : (double) $value;
                                        $investors = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6106)),  MB_CASE_TITLE, "UTF-8"); //Número Inversionistas
                                        $record[] =  empty($investors) ? "Sin Registro" : (int) $investors;
                                        $capital_required = mb_convert_case(trim($this->answer_written($register->codigo_usuario_encuesta, 6107)),  MB_CASE_TITLE, "UTF-8"); //Capital Requerido
                                        $record[] =  empty($capital_required) ? "Sin Registro" : (double) $capital_required;
                                        break;
                                    case 13043: //No
                                        for ($i=1; $i <= 3; $i++) {
                                            $record[] =  "No Aplica";
                                        }
                                        break;
                                    case 13652: //Sin Registro
                                        for ($i=1; $i <= 3; $i++) {
                                            $record[] =  "Sin Registro";
                                        }
                                        break;
                                    default:
                                        for ($i=1; $i <= 3; $i++) {
                                            $record[] =  "Sin Registro";
                                        }
                                        break;
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6170), MB_CASE_TITLE, "UTF-8"); //Forma Inscripción Oferta
                                $way_inscription =  $this->answer_select_sigle_code($register->codigo_usuario_encuesta, 6170);

                                switch ($way_inscription) {
                                    case 13114: //Autónoma
                                        $record[] = "No Aplica"; //Sede Inscripción Oferta
                                        break;
                                    case 13115: //Asistida
                                        $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6171),   MB_CASE_TITLE, "UTF-8"); //Sede Inscripción
                                        break;
                                    case 13652: //Sin Registro
                                        $record[] =  "Sin Registro"; //Sede Inscripción
                                        break;
                                    default:
                                        $record[] =  "Sin Registro";
                                        break;
                                }

                                $record[] = mb_convert_case($this->answer_select_sigle($register->codigo_usuario_encuesta, 6172), MB_CASE_TITLE, "UTF-8"); //Cómo Se Enteró Del Servicio
                                $record[] = mb_convert_case($register->nombre_jurado, MB_CASE_TITLE, "UTF-8");

                                $status_judge = "";

                                switch (Estado::getNameState($register->estado_jurado)) {
                                    case 'pending':
                                        $status_judge = "Pendiente";
                                        break;
                                    case 'evaluated':
                                        $status_judge = "Evaluado";
                                        break;
                                }

                                $record[] = $status_judge;

                                $answers = $this->getAnswersEvaluation($register->codigo_resultado_evaluacion);

                                foreach ($answers as $item_answers) {
                                    if ($register->codigo_resultado_evaluacion == $item_answers->codigo_resultado_evaluacion) {
                                        foreach ($questions as $item_questions) {
                                            if ($item_questions->codigo_item_evaluacion == $item_answers->codigo_item_evaluacion) {                                                if ($item_questions->codigo_grupo_item_eval == $item_answers->codigo_grupo_item_eval) {

                                                $record[] = $item_answers->respuesta;

/*                                                     if ($item_answers->codigo_item_evaluacion == 255 || $item_answers->codigo_item_evaluacion == 256
                                                        || $item_answers->codigo_item_evaluacion == 257 || $item_answers->codigo_item_evaluacion == 258
                                                        || $item_answers->codigo_item_evaluacion == 259 || $item_answers->codigo_item_evaluacion == 260
                                                        || $item_answers->codigo_item_evaluacion == 261){
                                                            $record[] = $item_answers->respuesta;
                                                    } */


                                                }
                                            }
                                        }
                                    }
                                }

                                $record[] = $register->comentario_resultado_evaluacion;

                                $data[] = $record;
                            }

                        break;
                    }
                break;
            }
            Log::info("Fin Consulta \n");

            // Prepare file
            $filename = sprintf('%s_%s_%s.xlsx', 'profiling', $program->url_convocatoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data) {
                $excel->sheet('Data', function($sheet) use($header, $data) {
                    // Titles
                    $sheet->appendRow($header);
                    // Data
                    $sheet->rows($data);
                });
            })->string('xlsx');

            Log::info("Creo excel \n");

            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte registros perfilamiento File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
            Log::info("Cargo excel \n");

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "REPORTE PERFILAMIENTO RUTA N";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

            Log::info("Envio correo \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportProfiling:command', $e->getMessage()));
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
        
        return $count > 0 ? mb_strtoupper($query[0]->texto_opcion_encuesta) : 'Sin Registro';
    }

    public function answer_select_sigle_code(int $codigo_usuario_encuesta, $codigo_pregunta_encuesta): string
    {
        $query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.codigo_opcion_encuesta');
        $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', $codigo_pregunta_encuesta);
        $query->where('codigo_usuario_encuesta', $codigo_usuario_encuesta);
        $query = $query->get();
        $count = $query->count();
        
        return $count > 0 ? $query[0]->codigo_opcion_encuesta : 'Sin Registro';
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

    private function getQuestionsEvaluation($evaluation) {
        $query = EvaluacionEmprendimiento::query();
        $query->select('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento',
            'evaluacion_emprendimiento.nombre_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion',
            'item_evaluacion_emprendimiento.nombre_item_evaluacion', 'grupo_item_eval.codigo_grupo_item_eval',
            DB::raw("UPPER(grupo_item_eval.nombre_grupo_item_eval) as nombre_grupo_item_eval"));
        $query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=','evaluacion_emprendimiento.codigo_evaluacion_emprendimiento');
        $query->leftJoin('core.grupo_item_eval', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
        $query->where('evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', $evaluation);
        $query->orderBy('item_evaluacion_emprendimiento.codigo_item_evaluacion', 'ASC');
        $data = $query->get();
        return $data;
    }

    private function getAnswersEvaluation($id_result_evaluation) {
        $query = ResultadoEvaluacionEmprendimiento::query();
        $query->select('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion','opc_eval_pregunta.nombre_opc_eval_pregunta as respuesta',
            'item_evaluacion_emprendimiento.codigo_item_evaluacion', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');
        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
        $query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
        $query->where('resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', $id_result_evaluation);
        $query->orderBy('item_evaluacion_emprendimiento.codigo_item_evaluacion','ASC');
        $data = $query->get();
        return $data;
    }

}