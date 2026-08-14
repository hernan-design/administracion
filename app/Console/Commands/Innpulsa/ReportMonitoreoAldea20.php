<?php

namespace App\Console\Commands\Innpulsa;

use App\Models\Core\CategoriaProducto;
use App\Models\Core\PreguntaEncuesta, App\Models\Core\RespuestaEncuesta;
use App\Models\Core\TipoCampoPregunta;
use Illuminate\Console\Command;
use Custom\Innpulsa;
use App\Models\Seguridad\Usuario,
    App\Models\Param\Institucion,
    App\Models\Core\ResultadoEvaluation,
    App\Models\Core\RegistroFaseUsuario,
    App\Models\Core\Fase,
    App\Models\Core\PuntajeRegistroFase,
    App\Models\Core\AsignacionJurado,
    App\Models\Core\RegistroConvocatoria,
    App\Models\Core\Estado;
use DB, Log, Excel, Storage, SendEmail, DateTime;

class ReportMonitoreoAldea20 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:monitoreo20 {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte seguimiento unico Aldea20 iNNpulsa';

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
        // Valid user
        $user = Usuario::find($this->argument('user'));
        if(!$user instanceof Usuario) {
            throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
        }
        \Log::info("empezo consulta \n");
        $query = RegistroConvocatoria::query();
        $query->select(
            'registro_convocatoria.numero_registro_convocatoria',
            'rfu_reto13.codigo_registro_fase_usuario as crfu_reto13',
            'rfu_reto2.codigo_registro_fase_usuario as crfu_reto2',
            'rfu_reto2.codigo_usuario as cu_reto2', 'rfu_reto2.codigo_registro_convocatoria as crc_reto2',
            'rfu_reto2.valor_asignacion_rfu as valor_llaves', 'fecha_registro_convocatoria',
            'hora_registro_convocatoria', 'ue.nombre_usuario as nombre_proyecto', 'uo.nombre_usuario',
            'persona.genero_persona', 'ue.identificacion_usuario', 'uo.correo_electronico',
            'persona.primer_apellido_persona', 'persona.segundo_apellido_persona',
            'categoria_proyecto.nombre_categoria_proyecto',
            'ce.nombre_categoria_proyecto as nombre_categoria_proyecto_evaluador',
            'usuario_encuesta.puntaje_total_encuesta',
            'resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi',
            'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
            'resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion',
            'asignacion_jurado.fecha_asignacion_jurado',
            'rfu_reto1.rango_inferior_eval_fase as rango_reto1',
            'rfu_reto12.rango_inferior_eval_fase as rango_inferior_reto12',
            'rfu_reto12.rango_superior_eval_fase as rango_superior_reto12',
            'rfu_reto12.codigo_registro_fase_usuario as crfu_reto12', 'entidad.descripcion_entidad',
            DB::raw("(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN rfu_reto12.notificacion_cambio_registro_fase ELSE rfu_reto1.notificacion_cambio_registro_fase END) as notificacion_cambio_registro_fase"), 'rfu_reto13.notificacion_cambio_registro_fase as notificacion_cambio_registro_fase2', 'rfu_reto13.observaciones_registro_fase_usuario', 'nombre_clase_emprendimiento', 'carta_compormiso_compra.apellidos_representante_legal', 'cue.region_ciudad', 'carta_compormiso_compra.fecha_aceptacion_carta_compormiso_compra', 'estado.nombre_estado', 'rfu_reto13.codigo_registro_fase_usuario as register_phase_13', 'sector_interes.nombre_sector_interes', 'sector_interes.codigo_sector_interes', 'sector_economico.nombre_sector_economico', 'registro_convocatoria.codigo_usuario_encuesta', 'puntaje_registro_fase.valor_puntaje_registro_fase as puntaje_reto2',
            DB::raw("trim(initcap(ciudad.distrito_ciudad)) as departamento_usuario"),
            DB::raw("trim(initcap(ciudad.nombre_ciudad)) as ciudad_usuario"),
            DB::raw("trim(initcap(cue.nombre_ciudad)) as ciudad_usuario_entidad"),
            DB::raw("trim(initcap(cue.distrito_ciudad)) as departamento_usuario_entidad"),
            DB::raw("(uev.nombre_usuario || ' ' || COALESCE(puev.primer_apellido_persona, '') || ' ' || COALESCE(puev.segundo_apellido_persona, '')) as nombre_evaluador"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 4263 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as empresa_constituida"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 4313 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as tipo_identificacion_rep_legal"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 4268 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as tipo_documento"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1245 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as empresa_cuenta_con"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1246 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as producto_compite_gana"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1250 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as perfil_equipo_emprendedor"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1254 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as estado_desarrollo_producto"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 4263 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as empresa_constituida"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 4282 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as personeria_juridica"),
            DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 5633 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as emprendimiento_femenino"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 5693 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as cantidad_clientes_actual"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4269 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as numero_documento"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4325 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as descripcion_emprendimiento"),
            DB::raw("(SELECT nivel_educacion.nombre_nivel_educacion FROM core.respuesta_encuesta as pgencu LEFT JOIN core.nivel_educacion ON nivel_educacion.codigo_nivel_educacion = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 4271 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta ) as nivel_estudio_usuario"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4272 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as telefono_usuario"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4284 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as menos_anos"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4372 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as equipo_emprendedor"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4336 and pgencu.cor_codigo_opcion_encuesta = 7146 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as total_ano_actual"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4336 and pgencu.cor_codigo_opcion_encuesta = 7147 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as total_ano_anterior"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4342 and pgencu.cor_codigo_opcion_encuesta = 7174 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as mas_de_100"),
            DB::raw("(SELECT item_opcion_encuesta.nombre_item_opcion_encuesta FROM core.respuesta_encuesta as pgencu INNER JOIN core.item_opcion_encuesta ON item_opcion_encuesta.codigo_item_opcion_encuesta::varchar(255) = pgencu.texto_respuesta_encuesta::varchar(255) WHERE pgencu.codigo_pregunta_encuesta = 4373 and pgencu.cor_codigo_opcion_encuesta = 7269 AND pgencu.codigo_opcion_encuesta = 7262 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as dedicacion"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4317 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as telefono_rep_legal"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4324 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as direccion_rep_legal"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4318 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as celular_rep_legal"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4319 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as correo_rep_legal"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4311 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as nombre_representante_legal"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4314 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as identificacion_rep_legal"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4273 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as movil_usuario"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1244 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as cantidad_empleados"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1255 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as describe_producto"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1260 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ventas_actuales_producto"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1286 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as como_genera_ingreso_empresa"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 4279 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as direccion_usuario"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1929 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_postal_usuario"),
            DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1965 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ventas_actuales_negocio"),
            DB::raw("(SELECT nombre_ciudad FROM core.respuesta_encuesta as pgencu JOIN core.ciudad ON codigo_ciudad = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 4270 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as lugar_expedicion_documento"),
            DB::raw("(SELECT ciudpreg.nombre_ciudad FROM core.respuesta_encuesta as ciudpregenc JOIN core.ciudad as ciudpreg ON ciudpreg.codigo_ciudad = ciudpregenc.texto_respuesta_encuesta::integer WHERE ciudpregenc.codigo_pregunta_encuesta = 4287 and ciudpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciudad_pregunta"),
            DB::raw("(SELECT ciudpreg.nombre_ciudad FROM core.respuesta_encuesta as ciudpregenc JOIN core.ciudad as ciudpreg ON ciudpreg.codigo_ciudad = ciudpregenc.texto_respuesta_encuesta::integer WHERE ciudpregenc.codigo_pregunta_encuesta = 4315 and ciudpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciudad_expedicion_epresentante"),
            DB::raw("(SELECT ciudpreg.nombre_ciudad FROM core.respuesta_encuesta as ciudpregenc JOIN core.ciudad as ciudpreg ON ciudpreg.codigo_ciudad = ciudpregenc.texto_respuesta_encuesta::integer WHERE ciudpregenc.codigo_pregunta_encuesta = 4322 and ciudpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciudad_residencia_rep"),
            DB::raw("(SELECT ciuddistripre.distrito_ciudad FROM core.respuesta_encuesta as distrpregenc JOIN core.ciudad as ciuddistripre ON ciuddistripre.codigo_ciudad = distrpregenc.texto_respuesta_encuesta::integer WHERE distrpregenc.codigo_pregunta_encuesta = 4322 and distrpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as distrito_residencia_rep"),
            DB::raw("(SELECT ciuddistripre.distrito_ciudad FROM core.respuesta_encuesta as distrpregenc JOIN core.ciudad as ciuddistripre ON ciuddistripre.codigo_ciudad = distrpregenc.texto_respuesta_encuesta::integer WHERE distrpregenc.codigo_pregunta_encuesta = 4287 and distrpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as distrito_pregunta"),
            DB::raw("(SELECT ciudregpregenc.region_ciudad FROM core.respuesta_encuesta as regpregenc JOIN core.ciudad as ciudregpregenc ON ciudregpregenc.codigo_ciudad = regpregenc.texto_respuesta_encuesta::integer WHERE regpregenc.codigo_pregunta_encuesta = 4287 and regpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as region_pregunta"),
            DB::raw("(SELECT codigo_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 4296 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_ciiu"),
            DB::raw("(SELECT nombre_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 4296 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciiu"),
            DB::raw("(SELECT nombre_sector_economico FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer JOIN core.sector_economico ON sector_interes.codigo_sector_economico = sector_economico.codigo_sector_economico WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as actividad_economica")
        );
        // User entity
        $query->join('seguridad.usuario as ue', 'registro_convocatoria.codigo_usuario', '=', 'ue.codigo_usuario');
        $query->join('core.entidad', 'ue.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->leftJoin('core.ciudad as cue', 'ue.codigo_ciudad', '=', 'cue.codigo_ciudad');
        $query->leftJoin('core.sector_interes', 'sector_interes.codigo_sector_interes','=', 'entidad.codigo_sector_interes');
        $query->leftJoin('core.sector_economico', 'sector_economico.codigo_sector_economico','=', 'sector_interes.codigo_sector_economico');
        // User
        $query->join('seguridad.usuario as uo', 'entidad.seg_codigo_usuario', '=', 'uo.codigo_usuario');
        $query->join('core.persona', 'uo.codigo_usuario', '=', 'persona.codigo_usuario');
        $query->join('core.ciudad', 'uo.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
        $query->join('core.usuario_institucion', function($join) {
            $join->on('usuario_institucion.codigo_usuario', '=', 'ue.codigo_usuario')
            ->where('usuario_institucion.codigo_institucion', '=', 51);
        });
        $query->join('core.estado', 'estado.codigo_estado', '=', 'registro_convocatoria.codigo_estado');
        $query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
        $query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
        $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
        $query->join('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
        $query->leftJoin('core.categoria_proyecto as ce', 'dato_entidad.cor_codigo_categoria_proyecto', '=', 'ce.codigo_categoria_proyecto');
        $query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');
        // Reto 1
        $query->join('core.registro_fase_usuario as rfu_reto1', function($join){
            $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto1.codigo_registro_convocatoria')
            ->where('rfu_reto1.codigo_fase', '=', 253);
        });
        // Reto 1.2
        $query->leftJoin('core.registro_fase_usuario as rfu_reto12', function($join){
            $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto12.codigo_registro_convocatoria')
            ->where('rfu_reto12.codigo_fase', '=', 254);
        });
        $query->leftJoin('core.asignacion_jurado', function($join) {
            $join->on('rfu_reto12.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario')
            ->where('asignacion_jurado.codigo_estado', '!=', Estado::getState('removed'));
        });
        $query->leftJoin('core.resultado_evaluacion_emprendimiento', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
        $query->leftJoin('seguridad.usuario as uev', 'asignacion_jurado.codigo_usuario', '=', 'uev.codigo_usuario');
        $query->leftJoin('core.persona as puev', 'uev.codigo_usuario', '=', 'puev.codigo_usuario');
        // Reto 1.3
        $query->leftJoin('core.registro_fase_usuario as rfu_reto13', function($join){
            $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto13.codigo_registro_convocatoria')
            ->where('rfu_reto13.codigo_fase', '=', 255);
        });

        // Reto 2
        $query->leftJoin('core.registro_fase_usuario as rfu_reto2', function($join){
            $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto2.codigo_registro_convocatoria')
            ->where('rfu_reto2.codigo_fase', '=', 256);
        });
        $query->leftJoin('core.carta_compormiso_compra', 'rfu_reto2.codigo_registro_fase_usuario', '=', 'carta_compormiso_compra.codigo_registro_fase_usuario');
        $query->leftJoin('core.ciudad as ciudadrepresentante', 'carta_compormiso_compra.codigo_ciudad', '=', 'ciudadrepresentante.codigo_ciudad');
        $query->leftJoin('core.detalle_voucher', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'rfu_reto2.codigo_registro_fase_usuario');
        $query->leftJoin('core.puntaje_registro_fase', 'puntaje_registro_fase.codigo_registro_fase_usuario', '=', 'rfu_reto2.codigo_registro_fase_usuario');
        $query->where('registro_convocatoria.codigo_convocatoria', '=', 504);
        // $query->whereIn('registro_convocatoria.numero_registro_convocatoria', [8363]);
        $query->where('registro_convocatoria.codigo_estado', '!=', Estado::getState('removed'));
        $query->orderBy('fecha_registro_convocatoria', 'asc');
        $query->orderBy('hora_registro_convocatoria','asc');
        // $query->limit(10);
        $data = $query->get();
        \Log::info("termino consulta \n");
        // Prepare file
        $filename = sprintf('%s_%s.xlsx', 'reporte_unico_aldea_20', date('YmdHms'));
        $filepath = sprintf('reports/%s/%s/%s', 51, $user->codigo_usuario, $filename);
        \Log::info("empezo excel \n");
        $file = Excel::create($filename, function($excel) use($data) {
            // Set sheet alignement
            $excel->getDefaultStyle()
            ->getAlignment()
            ->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $excel->sheet('Reporte único', function($sheet)  use($data) {
            // One level titles
            $sheet->appendRow([
                'TRANSVERSAL','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
                'Reto 1 y Reto 1.2','','','','','','','','','','','','','','','','','','','','','','', '',
                'Reto 1.3','','','','','','','','','','','','','','','','','','','','','','','','','','','', '',
                'Reto 2'
            ]);
            $sheet->mergeCells('A1:AN1');
            $sheet->cells('A1:AN1', function($cells) {
                $cells->setBackground('#A9F5F2');
                $cells->setAlignment('center');
            });
            $sheet->mergeCells('AO1:BL1');
            $sheet->cells('AO1:BL1', function($cells) {
                $cells->setBackground('#F5D0A9');
                $cells->setBorder('none', 'thin', 'none', 'thin');
                $cells->setAlignment('center');
            });

            $sheet->mergeCells('BM1:CO1');
            $sheet->cells('BM1:CO1', function($cells) {
                $cells->setBackground('#F5BCA9');
                $cells->setBorder('none', 'thin', 'none', 'thin');
                $cells->setAlignment('center');
            });

            $sheet->mergeCells('CP1:EF1');
            $sheet->cells('CP1:EF1', function($cells) {
                $cells->setBackground('#90C978');
                $cells->setBorder('none', 'thin', 'none', 'thin');
                $cells->setAlignment('center');
            });

            // Two level titles
            $sheet->appendRow([
                '','','','','','','','','',
                'Emprendedor (No tiene empresa legalmente constituida)',
                '','','','','','','','','','','','','',
                'Persona Juridica',
                '','','','','','','','','','','','','','','','','','','','','','','','',
                '','','','','','','','','','','','','','','',
                // RETO 2 - RETO 4
                '','','','','','','','','','','','','','','','','','','','',
                // // RETO 2 - RETO 3 - RETO 4
                // '','','','','',
                // // TRANSVERSAL
                // '',
                // // RETO 3
                // '','','','','','','',
                // // RETO 4
                // '','','','','','','','','',
            ]);
            $sheet->mergeCells('J2:V2');
            $sheet->cells('J2:V2', function($cells) {
                $cells->setBorder('thin', 'thin', 'none', 'thin');
            });
            $sheet->mergeCells('W2:AN2');
            $sheet->setBorder('W2:AN2', 'thin');
            $sheet->cells('A2:AN2', function($cells) {
                $cells->setBackground('#A9F5F2');
                $cells->setAlignment('center');
            });

            $sheet->cells('W2', function ($cells) {
                $cells->setBorder('none', 'none', 'none', 'thin');
            });
            // $sheet->cells('AI2:AO2', function($cells) {
            //     $cells->setBorder('none', 'none', 'none', 'thin');
            // });
            $sheet->cells('AO2:BL2', function($cells) {
                $cells->setBackground('#F5D0A9');
                $cells->setBorder('none', 'thin', 'none', 'thin');
                $cells->setAlignment('center');
            });
            // $sheet->cells('AN2:BK2', function($cells) {
            //     $cells->setBackground('#F5D0A9');
            //     $cells->setAlignment('center');
            // });
            $sheet->cells('BM2:CO2', function($cells) {
                $cells->setBackground('#F5BCA9');
                $cells->setBorder('none', 'thin', 'none', 'thin');
                $cells->setAlignment('center');
            });

            $sheet->cells('CP2:EF2', function($cells) {
                $cells->setBackground('#90C978');
                $cells->setBorder('none', 'thin', 'none', 'thin');
                $cells->setAlignment('center');
            });

            // Three level titles
            $sheet->appendRow([
                '','','','','','','','',
                '','','','','','','','','','','','','','',
                // Datos de Representante Legal
                'Datos de Representante Legal',
                '','','','','','','','','','','',
                '','','','','','','',
                //'Razón Social','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
            ]);

            $sheet->mergeCells('W3:AN3');

            $sheet->cells('A3:I3', function ($cells) {
                $cells->setBorder('none', 'none', 'thin', 'none');
                $cells->setBackground('#A9F5F2');
            });

            $sheet->cells('W3:AN3', function($cells) {
                $cells->setBackground('#A9F5F2');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });
            $sheet->cells('BM3:CO3', function($cells) {
                $cells->setBackground('#F5BCA9');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });

            $sheet->cells('CP3:EF3', function($cells) {
                $cells->setBackground('#90C978');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });

            $sheet->cells('AK3:AN3', function($cells) {
                $cells->setBackground('#A9F5F2');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });
            $sheet->cells('AO3:BL3', function($cells) {
                $cells->setBackground('#F5D0A9');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });
            $sheet->cells('J3:V3', function($cells) {
                $cells->setBackground('#A9F5F2');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });
            // Three level titles
            $sheet->appendRow([
                'Aplicacion',
                'postula',
                'EMP',
                'idempres',
                'RazonSocial',
                'Descripción del Emprendimiento',
                'CiudadEmprendimiento',
                'DepartamentoEmprendimiento',
                'Region',
                'Genero',
                'Nombres',
                'Primer Apellido',
                'Segundo Apellido',
                'Tipo de Documento',
                'No. Identificacion',
                'Lugar de Expedicion',
                'Numero de Telefono',
                'Numero de Celular',
                'Correo Electronico',
                'Ciudad de Residencia',
                'Departamento',
                'Direccion de Correspondencia',
                'Nombres Completos',
                'Apellidos Completos',
                'Tipo de Documento 1',
                'No. Identificacion 1',
                'Lugar de Expedicion 1',
                'Numero de Telefono 1',
                'Numero de Celular 1',
                'Correo Electronico 1',
                'Ciudad de Residencia 1',
                'Departamento 1',
                'Direccion de Domicilio 1',
                'Nombres 1',
                'NIT',
                'Razon Social',
                'CIIU_0',
                'Actividad',
                'Sector',
                'EstudioLider',
                // Reto 1
                'EmpresaConstituida',
                'PersoneriaJuridica',
                'Score_PersoneriaJuridica',
                'Menos7años',
                'Score_Menos7años',
                'Emprendimiento_Femenino',
                'Score_Emprendimiento_Femenino',
                'Cantidad clientes actualmente',
                'VENTAS_AÑO_ANT',
                'VENTAS_AÑO_ACT',
                'VENTAS_AÑO_ACT_PROY',
                'Score_Proy_3años_100M',
                'Score_Mas_100',
                'EquipoEmprendedor',
                'Score_EquipoEmprendedor',
                'Dedicacion100',
                'Score_Dedicacion100',
                'NoBuscandoTrabajo',
                'Score_NoBuscandoTrabajo',
                'Score_final_1_1',
                // Reto  1.2
                'Obs_Eva_1_2',
                'Score_Resul_1_2',
                'Nom_Eva_1_2',
                'Fecha de asignacion de la postulacion al evaluador',
                'Fecha Evaluacion Reto 1.2',
                'Fecha de Notificacion',
                'Score_final_1_2',
                // Reto 1.3
                'Nom_Eva1_1_3',
                'Fecha asignacion evaluador 1',
                'Score_Inn_Eva1_1_3',
                'Score_Val_Eva1_1_3',
                'Score_Pot_Eva1_1_3',
                'Score_Equi_Eva1_1_3',
                'Coment_Eva1_1_3',
                'Fecha asignacion evaluador 2',
                'Nom_Eva2_1_3',
                'Score_Inn_Eva2_1_3',
                'Score_Val_Eva2_1_3',
                'Score_Pot_Eva2_1_3',
                'Score_Equi_Eva2_1_3',
                'Coment_Eva2_1_3',
                'Score_Comp_Inn_1_3',
                'Score_Comp_Val_1_3',
                'Score_Comp_Pot_1_3',
                'Score_Comp_Equi_1_3',
                'Score_final_1_3',
                'Total Documentador 1_3',
                'Val_recursos_1_3',
                'NomLider',
                'GENEROLider',
                'NomInt_2',
                'GeneroInt_2',
                'NomInt_3',
                'GeneroInt_3',
                'NomInt_4',
                'GeneroInt_4',
                // Reto 2
                'Nom_Exp1_2',
                'Score_Inn_Exp1_2',
                'Score_Mer_Exp1_2',
                'Score_Mer_Pot_Exp1_2',
                'Score_Mer_Barr_Exp1_2',
                'Score_Equi_Exp1_2',
                'Score_Equi_Cap_Exp1_2',
                'Score_Ali_Exp1_2',
                'Score_Reco_Exp1_2',
                'Comen_Exp1_2',
                'Nom_Exp2_2',
                'Score_Inn_Exp2_2',
                'Score_Mer_Exp2_2',
                'Score_Mer_Pot_Exp2_2',
                'Score_Mer_Barr_Exp2_2',
                'Score_Equi_Exp2_2',
                'Score_Equi_Cap_Exp2_2',
                'Score_Ali_Exp2_2',
                'Score_Reco_Exp2_2',
                'Comen_Exp2_2',
                'Nom_Exp3_2',
                'Score_Inn_Exp3_2',
                'Score_Mer_Exp3_2',
                'Score_Mer_Pot_Exp3_2',
                'Score_Mer_Barr_Exp3_2',
                'Score_Equi_Exp3_2',
                'Score_Equi_Cap_Exp3_2',
                'Score_Ali_Exp3_2',
                'Score_Reco_Exp3_2',
                'Comen_Exp3_2',
                'Score_Comp_inn_2',
                'Score_Comp_mer_2',
                'Score_Comp_equipo_2',
                'Score_Comp_ali_2',
                'Score_Comp_reco_2',
                'Fecha de conversatorio',
                'Resultado',
                'Score_final_llave',
                'Llave_asignada',
                'CategoriaAsignada1',
                'CategoriaAsignada2',
                'CategoriaAsignada3',
                'Score_final_2'
            ]);

            $sheet->setBorder('A4:AN4', 'thin');
            // $sheet->setBorder('AV4:BR4', 'thin');
            $sheet->setBorder('J4:W4', 'thin');
            // $sheet->setBorder('W4:AH4', 'thin');
            $sheet->setBorder('BM4:CO4', 'thin');
            // $sheet->setBorder('CC4:CR4', 'thin');

            $sheet->cells('A4:AN4', function ($cells) {
                $cells->setBorder('none', 'thin', 'thin', 'none');
                $cells->setBackground('#A9F5F2');
            });
            $sheet->cells('AO4:BL4', function($cells) {
                $cells->setBackground('#F5D0A9');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });
            $sheet->cells('J4:V4', function($cells) {
                $cells->setBackground('#A9F5F2');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });

            // $sheet->cells('W4:AH4', function($cells) {
            //     $cells->setBackground('#A9F5F2');
            //     $cells->setBorder('none', 'thin', 'thin', 'thin');
            //     $cells->setAlignment('center');
            // });
            $sheet->cells('BM4:CO4', function($cells) {
                $cells->setBackground('#F5BCA9');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });

            $sheet->cells('CP4:EF4', function($cells) {
                $cells->setBackground('#90C978');
                $cells->setBorder('none', 'thin', 'thin', 'thin');
                $cells->setAlignment('center');
            });

            foreach ($data as $value) {
                $categorias_marketplace = [];
                $categorias_marketplace_reto2 = []; // explode(',', $value->categorias_marketplace)
                if (isset($value->register_phase_13)) {
                    foreach ($this->getCategoriesSelected($value->register_phase_13) as $category) {
                        $categorias_marketplace[] = $category->nombre_cat_padre_producto;
                    }
                }

                if (isset($value->crfu_reto2)) {
                    foreach ($this->getCategoriesSelected($value->crfu_reto2) as $category) {
                        $categorias_marketplace_reto2[] = $category->nombre_cat_padre_producto;
                    }
                }
                $ciiu = str_pad($value->codigo_sector_interes,4,'0',STR_PAD_LEFT).'-'.$value->nombre_sector_interes;

                // Evaluadores reto 1.3
                $promedio_reto13 = 0;
                $puntaje_evaluador1_reto13 = 0;
                $puntaje_evaluador2_reto13 = 0;
                $evaluador1_reto13 = "";
                $comentario_evaluador1_reto13 = "";
                $puntaje_evaluador1_reto13 = 0;
                $evaluador2_reto13 = "";
                $comentario_evaluador2_reto13 = "";
                $puntaje_evaluador2_reto13 = 0;
                $fecha_asignacion_evaluador1_reto13 = "";
                $fecha_asignacion_evaluador2_reto13 = "";

                // Innovacion

                $innovation_challenge_13 = $this->getInnovation__Challenge_1_3($value->crfu_reto13, $value->codigo_usuario_encuesta);

                // Traccion

                $traccion_challenge_13 = $this->getTraccion__Challenge_1_3($value->crfu_reto13, $value->codigo_usuario_encuesta);

                // Escalabilidad

                $escalabilidad_challenge_13 = $this->getEscalabilidad__Challenge_1_3($value->crfu_reto13, $value->codigo_usuario_encuesta);

                // Equipo
                $equipo_challenge_13 = $this->getEquipo__Challenge_1_3($value->crfu_reto13, $value->codigo_usuario_encuesta);


                // Total Challenge 13
                $total_challenge_13 = ( $innovation_challenge_13->total + $traccion_challenge_13->total + $escalabilidad_challenge_13->total + $equipo_challenge_13->total );
                $total_challenge_13_res = $total_challenge_13 * 10;

                // Evaluadores reto 2
                $promedio_reto2 = 0;
                $puntaje_evaluador1_reto2 = 0;
                $puntaje_evaluador2_reto2 = 0;
                $puntaje_evaluador3_reto2 = 0;
                $evaluador1_reto2 = "";
                $comentario_evaluador1_reto2 = "";
                $puntaje_evaluador1_reto2 = 0;
                $evaluador2_reto2 = "";
                $comentario_evaluador2_reto2 = "";
                $puntaje_evaluador2_reto2 = 0;
                $evaluador3_reto2 = "";
                $comentario_evaluador3_reto2 = "";
                $puntaje_evaluador3_reto2 = 0;
                $fecha_conversatorio = "";

                $evaluadores_reto13 = $this->getEvaluators($value->crfu_reto13);
                $evaluadrores_reto2 = $this->getEvaluators($value->crfu_reto2);

                // Innovacion

                $innovacion_challenge_2 = $this->getInnovation__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);

                // Mercado

                $mercado_challenge_2 = $this->getMercado__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);
                $mercado_pot_challenge_2 = $this->getMercado_Pot__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);
                $mercado_bar_challenge_2 = $this->getMercado_Bar__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);

                // Equipo

                $equipo_challenge_2 = $this->getEquipo__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);
                $equipo_cap_challenge_2 = $this->getEquipo__cap__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);

                // Alineacion

                $alineacion_challenge_2 = $this->getAlineacion__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);

                // Recomendaciones

                $recomendacion_challenge_2 = $this->getRecomendacion__Challenge_2($value->crfu_reto2, $value->codigo_usuario_encuesta);

                // Total Challenge 2

                $result = $this->getRegisterPhase($value->crfu_reto2);
                $total_challenge_2 = Innpulsa::getScoring__Aldea20__Challenge_Two($result);

                if(count($evaluadores_reto13) > 0){
                    $evaluador1_reto13 = $evaluadores_reto13[0]->nombre_evaluador_momento3;
                    $comentario_evaluador1_reto13 = $evaluadores_reto13[0]->comentario_resultado_evaluacion;
                    $puntaje_evaluador1_reto13 = $evaluadores_reto13[0]->puntaje_total_eval_emprendi;
                    $fecha_asignacion_evaluador1_reto13 = $evaluadores_reto13[0]->fecha_asignacion_jurado;
                    if(count($evaluadores_reto13) > 1){
                        $evaluador2_reto13 = $evaluadores_reto13[1]->nombre_evaluador_momento3;
                        $comentario_evaluador2_reto13 = $evaluadores_reto13[1]->comentario_resultado_evaluacion;
                        $puntaje_evaluador2_reto13 = $evaluadores_reto13[1]->puntaje_total_eval_emprendi;
                        $fecha_asignacion_evaluador2_reto13 = $evaluadores_reto13[1]->fecha_asignacion_jurado;
                    }
                }

                if(count($evaluadrores_reto2) == 1){
                    $evaluador1_reto2 = $evaluadrores_reto2[0]->nombre_evaluador_momento3;
                    $comentario_evaluador1_reto2 = $evaluadrores_reto2[0]->comentario_resultado_evaluacion;
                    $puntaje_evaluador1_reto2 = $evaluadrores_reto2[0]->puntaje_total_eval_emprendi;
                    $fecha_conversatorio = $evaluadrores_reto2[0]->fecha_evaluacion_jurado;
                } else if(count($evaluadrores_reto2) == 2){
                    $evaluador1_reto2 = $evaluadrores_reto2[0]->nombre_evaluador_momento3;
                    $comentario_evaluador1_reto2 = $evaluadrores_reto2[0]->comentario_resultado_evaluacion;
                    $puntaje_evaluador1_reto2 = $evaluadrores_reto2[0]->puntaje_total_eval_emprendi;
                    $evaluador2_reto2 = $evaluadrores_reto2[1]->nombre_evaluador_momento3;
                    $comentario_evaluador2_reto2 = $evaluadrores_reto2[1]->comentario_resultado_evaluacion;
                    $puntaje_evaluador2_reto2 = $evaluadrores_reto2[1]->puntaje_total_eval_emprendi;
                    $fecha_conversatorio = $evaluadrores_reto2[1]->fecha_evaluacion_jurado;
                } else if(count($evaluadrores_reto2) == 3){
                    $evaluador1_reto2 = $evaluadrores_reto2[0]->nombre_evaluador_momento3;
                    $comentario_evaluador1_reto2 = $evaluadrores_reto2[0]->comentario_resultado_evaluacion;
                    $puntaje_evaluador1_reto2 = $evaluadrores_reto2[0]->puntaje_total_eval_emprendi;
                    $evaluador2_reto2 = $evaluadrores_reto2[1]->nombre_evaluador_momento3;
                    $comentario_evaluador2_reto2 = $evaluadrores_reto2[1]->comentario_resultado_evaluacion;
                    $puntaje_evaluador2_reto2 = $evaluadrores_reto2[1]->puntaje_total_eval_emprendi;
                    $evaluador3_reto2 = $evaluadrores_reto2[2]->nombre_evaluador_momento3;
                    $comentario_evaluador3_reto2 = $evaluadrores_reto2[2]->comentario_resultado_evaluacion;
                    $puntaje_evaluador3_reto2 = $evaluadrores_reto2[2]->puntaje_total_eval_emprendi;
                    $fecha_conversatorio = $evaluadrores_reto2[2]->fecha_evaluacion_jurado;
                }

                $promedio_reto13 = ($puntaje_evaluador1_reto13+$puntaje_evaluador2_reto13)/2;
                $promedio_reto2 = ($puntaje_evaluador1_reto2+$puntaje_evaluador2_reto2+$puntaje_evaluador3_reto2)/3;

                $promedio_documentador = isset($value->crfu_reto2) ?
                    $this->getTotal($value->crfu_reto2, $value->cu_reto2, $value->crc_reto2)->puntaje
                    :
                    null;

                $sheet->appendRow([
                    $value->numero_registro_convocatoria,
                    $value->fecha_registro_convocatoria . " " .$value->hora_registro_convocatoria,
                    $value->empresa_constituida,
                    $value->nombre_clase_emprendimiento,
                    $value->nombre_proyecto,
                    strip_tags($value->descripcion_emprendimiento),
                    $value->ciudad_pregunta,
                    $value->distrito_pregunta,
                    $value->region_pregunta,
                    isset(Usuario::$gender[$value->genero_persona]) ? Usuario::$gender[$value->genero_persona]: '',
                    $value->nombre_usuario,
                    $value->primer_apellido_persona,
                    $value->segundo_apellido_persona,
                    $value->tipo_documento,
                    $value->numero_documento,
                    $value->lugar_expedicion_documento,
                    $value->telefono_usuario,
                    $value->movil_usuario,
                    $value->correo_electronico,
                    $value->ciudad_usuario,
                    $value->departamento_usuario,
                    $value->direccion_usuario,
                    $value->nombre_representante_legal,
                    $value->apellidos_representante_legal,
                    $value->tipo_identificacion_rep_legal,
                    $value->identificacion_rep_legal,
                    $value->ciudad_expedicion_epresentante,
                    $value->telefono_rep_legal,
                    $value->celular_rep_legal,
                    $value->correo_rep_legal,
                    $value->ciudad_residencia_rep,
                    $value->distrito_residencia_rep,
                    $value->direccion_rep_legal,
                    $value->nombre_usuario,
                    $value->identificacion_usuario,
                    $value->nombre_proyecto,
                    $ciiu,
                    $value->nombre_sector_economico,
                    $value->nombre_categoria_proyecto,
                    $value->nivel_estudio_usuario,
                    isset($this->getEmpresaConstituida__4263($value->codigo_usuario_encuesta)->response) ? $this->getEmpresaConstituida__4263($value->codigo_usuario_encuesta)->response : 0, //scoreEmpresaConstituida
                    $value->personeria_juridica,
                    isset($this->getPersonaJuridica__4282($value->codigo_usuario_encuesta)->response) ? $this->getPersonaJuridica__4282($value->codigo_usuario_encuesta)->response : 0, //ScorePersoneriaJuridica
                    $value->menos_anos,
                    $this->getDifferenceYears($value->codigo_usuario_encuesta), //ScoreAñosVentas
                    $value->emprendimiento_femenino,
                    isset($this->getEmprendimientoFemenino__5633($value->codigo_usuario_encuesta)->response) ? $this->getEmprendimientoFemenino__5633($value->codigo_usuario_encuesta)->response : 0,
                    isset($value->cantidad_clientes_actual) ? $value->cantidad_clientes_actual : 0,
                    money_format("%i", $this->getSoldLastYear($value->codigo_usuario_encuesta)),
                    // $value->total_ano_actual > 100000000 ? money_format("%i", $value->total_ano_actual)." - 4" : money_format("%i", $value->total_ano_anterior)." - 4",
                    $value->total_ano_anterior,
                    money_format("%i", $value->mas_de_100),
                    $this->getProyecionMas__4342($value->codigo_usuario_encuesta), //score proyeccion 3 años
                    $this->getMasDe100__4336($value->codigo_usuario_encuesta) instanceof RespuestaEncuesta ? 10 : 0, //Score mas de 100 millones
                    $value->equipo_emprendedor,
                    isset($this->getEquipoEmprendedor__4372($value->codigo_usuario_encuesta)->response) ? $this->getEquipoEmprendedor__4372($value->codigo_usuario_encuesta)->response : 0,
                    $value->dedicacion,
                    $this->getDedicacion__4373($value->codigo_usuario_encuesta),
                    $this->getQuestion7323($value->codigo_usuario_encuesta) instanceof RespuestaEncuesta ? $this->getQuestion7323($value->codigo_usuario_encuesta)->texto_opcion_encuesta : 0,
                    $this->getBuscandoTrabajo__4392($value->codigo_usuario_encuesta)->value > 0 ? 10 : 0,
                    $value->puntaje_total_encuesta,
                    $value->comentario_resultado_evaluacion,
                    isset($this->getResultEvaluationChallenge__1_2($value->crfu_reto12)->componente) ? $this->getResultEvaluationChallenge__1_2($value->crfu_reto12)->componente : '',
                    $value->nombre_evaluador,
                    $value->fecha_asignacion_jurado,
                    $value->fecha_resultado_evaluacion,
                    $value->notificacion_cambio_registro_fase,
                    $value->puntaje_total_eval_emprendi,
                    $evaluador1_reto13,
                    $fecha_asignacion_evaluador1_reto13,
                    !count($innovation_challenge_13->evaluators) ? '' : $innovation_challenge_13->evaluators[0]->total_scoring,
                    !count($traccion_challenge_13->evaluators) ? '' : $traccion_challenge_13->evaluators[0]->total_scoring,
                    !count($escalabilidad_challenge_13->evaluators) ? '' : $escalabilidad_challenge_13->evaluators[0]->total_score,
                    !count($equipo_challenge_13->evaluators) ? '' : $equipo_challenge_13->evaluators[0]->total_scoring,
                    $comentario_evaluador1_reto13,
                    $fecha_asignacion_evaluador2_reto13,
                    $evaluador2_reto13,
                    count($innovation_challenge_13->evaluators) === 2 ? $innovation_challenge_13->evaluators[1]->total_scoring : '',
                    count($traccion_challenge_13->evaluators) === 2 ? $traccion_challenge_13->evaluators[1]->total_scoring : '',
                    count($escalabilidad_challenge_13->evaluators) === 2 ? $escalabilidad_challenge_13->evaluators[1]->total_score : '',
                    count($equipo_challenge_13->evaluators) === 2 ? $equipo_challenge_13->evaluators[1]->total_scoring : '',
                    $comentario_evaluador2_reto13,
                    $innovation_challenge_13->total,
                    $traccion_challenge_13->total,
                    $escalabilidad_challenge_13->total,
                    $equipo_challenge_13->total,
                    $total_challenge_13_res,
                    isset($value->crfu_reto13) ? $this->getTotalDocumenter13($value->crfu_reto13) : '',
                    $this->getFinalScore($value->crfu_reto13),
                    isset($this->getNameAndGenderTeam('Leader__Name', $value->codigo_usuario_encuesta)->nombre) ? $this->getNameAndGenderTeam('Leader__Name', $value->codigo_usuario_encuesta)->nombre : '',
                    isset($this->getNameAndGenderTeam('Leader__Gender', $value->codigo_usuario_encuesta)->genero) ? $this->getNameAndGenderTeam('Leader__Gender', $value->codigo_usuario_encuesta)->genero : '',
                    isset($this->getNameAndGenderTeam('SecondIntegrant__Name', $value->codigo_usuario_encuesta)->nombre) ? $this->getNameAndGenderTeam('SecondIntegrant__Name', $value->codigo_usuario_encuesta)->nombre : '',
                    isset($this->getNameAndGenderTeam('SecondIntegrant__Gender', $value->codigo_usuario_encuesta)->genero) ? $this->getNameAndGenderTeam('SecondIntegrant__Gender', $value->codigo_usuario_encuesta)->genero : '',
                    isset($this->getNameAndGenderTeam('ThreeIntegrant__Name', $value->codigo_usuario_encuesta)->nombre) ? $this->getNameAndGenderTeam('ThreeIntegrant__Name', $value->codigo_usuario_encuesta)->nombre : '',
                    isset($this->getNameAndGenderTeam('ThreeIntegrant__Gender', $value->codigo_usuario_encuesta)->genero) ? $this->getNameAndGenderTeam('ThreeIntegrant__Gender', $value->codigo_usuario_encuesta)->genero : '',
                    isset($this->getNameAndGenderTeam('FourIntegrant__Name', $value->codigo_usuario_encuesta)->nombre) ? $this->getNameAndGenderTeam('FourIntegrant__Name', $value->codigo_usuario_encuesta)->nombre : '',
                    isset($this->getNameAndGenderTeam('FourIntegrant__Gender', $value->codigo_usuario_encuesta)->genero) ? $this->getNameAndGenderTeam('FourIntegrant__Gender', $value->codigo_usuario_encuesta)->genero : '',
                    $evaluador1_reto2,
                    count($innovacion_challenge_2->evaluators) == 3 ? $innovacion_challenge_2->evaluators[0]->total_scoring : '',
                    count($mercado_challenge_2->evaluators) == 3 ? $mercado_challenge_2->evaluators[0]->total_scoring : '',
                    count($mercado_pot_challenge_2->evaluators) == 3 ? $mercado_pot_challenge_2->evaluators[0]->total_scoring : '',
                    count($mercado_bar_challenge_2->evaluators) == 3 ? $mercado_bar_challenge_2->evaluators[0]->total_scoring : '',
                    count($equipo_challenge_2->evaluators) == 3 ? $equipo_challenge_2->evaluators[0]->total_scoring : '',
                    count($equipo_cap_challenge_2->evaluators) == 3 ? $equipo_cap_challenge_2->evaluators[0]->total_scoring : '',
                    count($alineacion_challenge_2->evaluators) == 3 ? $alineacion_challenge_2->evaluators[0]->total_scoring : '',
                    count($recomendacion_challenge_2->evaluators) == 3 ? $recomendacion_challenge_2->evaluators[0]->total_scoring : '',
                    $comentario_evaluador1_reto2,
                    $evaluador2_reto2,
                    count($innovacion_challenge_2->evaluators) == 3 ? $innovacion_challenge_2->evaluators[1]->total_scoring : '',
                    count($mercado_challenge_2->evaluators) == 3 ?  $mercado_challenge_2->evaluators[1]->total_scoring : '',
                    count($mercado_pot_challenge_2->evaluators) == 3 ? $mercado_pot_challenge_2->evaluators[1]->total_scoring : '',
                    count($mercado_bar_challenge_2->evaluators) == 3 ? $mercado_bar_challenge_2->evaluators[1]->total_scoring : '',
                    count($equipo_challenge_2->evaluators) == 3 ? $equipo_challenge_2->evaluators[1]->total_scoring : '',
                    count($equipo_cap_challenge_2->evaluators) == 3 ? $equipo_cap_challenge_2->evaluators[1]->total_scoring : '',
                    count($alineacion_challenge_2->evaluators) == 3 ? $alineacion_challenge_2->evaluators[1]->total_scoring : '',
                    count($recomendacion_challenge_2->evaluators) == 3 ?  $recomendacion_challenge_2->evaluators[1]->total_scoring : '',
                    $comentario_evaluador2_reto2,
                    $evaluador3_reto2,
                    count($innovacion_challenge_2->evaluators) == 3 ?  $innovacion_challenge_2->evaluators[2]->total_scoring : '',
                    count($mercado_challenge_2->evaluators) == 3 ? $mercado_challenge_2->evaluators[2]->total_scoring : '',
                    count($mercado_pot_challenge_2->evaluators) == 3 ? $mercado_pot_challenge_2->evaluators[2]->total_scoring : '',
                    count($mercado_bar_challenge_2->evaluators) == 3 ? $mercado_bar_challenge_2->evaluators[2]->total_scoring : '',
                    count($equipo_challenge_2->evaluators) == 3 ? $equipo_challenge_2->evaluators[2]->total_scoring : '',
                    count($equipo_cap_challenge_2->evaluators) == 3 ? $equipo_cap_challenge_2->evaluators[2]->total_scoring : '',
                    count($alineacion_challenge_2->evaluators) == 3 ?  $alineacion_challenge_2->evaluators[2]->total_scoring : '',
                    count($recomendacion_challenge_2->evaluators) == 3 ? $recomendacion_challenge_2->evaluators[2]->total_scoring : '',
                    $comentario_evaluador3_reto2,
                    isset($total_challenge_2) ? $total_challenge_2->innovation : '',
                    isset($total_challenge_2) ? $total_challenge_2->market : '',
                    isset($total_challenge_2) ? $total_challenge_2->enterprising : '',
                    isset($total_challenge_2) ? $total_challenge_2->objetives : '',
                    isset($total_challenge_2) ? $total_challenge_2->recomendations : '',
                    $fecha_conversatorio,
                    isset($total_challenge_2) ? round($total_challenge_2->total, 2) : '',
                    $promedio_documentador,
                    $value->valor_llaves,
                    isset($categorias_marketplace_reto2[0]) ? $categorias_marketplace_reto2[0] : '',
                    isset($categorias_marketplace_reto2[1]) ? $categorias_marketplace_reto2[1] : '',
                    isset($categorias_marketplace_reto2[2]) ? $categorias_marketplace_reto2[2] : '',
                    $value->puntaje_reto2
                ]);
            }

            });
        })->string('xlsx');
        \Log::info("Termino excel \n");
        // Store file to s3
        Storage::put($filepath, $file, 'public');
        // File valid
        if(!Storage::has($filepath)) {
            throw new \Exception("Error recuperando xlsx reporte reto monitoreo File($filepath).");
        }
        // Get url
        $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
        \Log::info("Almaceno archivo \n");
        // Send mail
        $data = [];
        $data['_template'] = 'emails.report';
        $data['_subject'] = 'Reporte único';
        $data['_to'] = $user->correo_electronico;
        $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
        $data['_inst'] = 51;
        $data['_url'] = $fileurl;
        SendEmail::notification($data);
        \Log::info("envio correo \n");
        }catch(\Exception $e) {
            \Log::error(sprintf('%s:%s', 'ReportUnicoInnpulsa:Command', $e->getMessage()));
            abort(500);
        }
    }

    public function getQuestion7323($user) {
        $query = RespuestaEncuesta::query();
        $query->select('opcion_encuesta.texto_opcion_encuesta');
        $query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4392);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);
        return $query->first();
    }

    public function getCategoriesSelected($register){
        $categories = [];
        $query = CategoriaProducto::query();
        $query->select('cat_padre_producto.nombre_cat_padre_producto');
        $query->join('core.categoria_producto_insti', function($join) {
            $join->on('categoria_producto.codigo_categoria_producto', '=', 'categoria_producto_insti.codigo_categoria_producto')
                ->where('categoria_producto_insti.codigo_institucion', '=', 51)
                ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
        });

        $query->join('core.recom_producto_eval', function($join) use($register){
            $join->on('recom_producto_eval.codigo_categoria_producto', '=', 'categoria_producto_insti.codigo_categoria_producto')
                ->where('recom_producto_eval.codigo_registro_fase_usuario', '=', $register);
        });

        $query->join('core.cat_padre_producto', 'cat_padre_producto.codigo_cat_padre_producto', '=', 'categoria_producto_insti.codigo_cat_padre_producto');

        // Get total posts
        $query->groupBy('cat_padre_producto.codigo_cat_padre_producto');
        // Get posts
        return $query->get();
    }

    /**
	 * Get difference of years
	 */

	public function getDifferenceYears($user) {
        $query = RespuestaEncuesta::query();
        $query->select('respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.fecha_respuesta_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4284);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);
        $data = $query->first();
        \Log::info("envio correo \n");
        $response = 0;
        if($data instanceof RespuestaEncuesta) {
            $response__Data = new DateTime($data->texto_respuesta_encuesta);
            \Log::info("Respuesta usuario $data->texto_respuesta_encuesta\n");
            $actual__Date = new DateTime($data->fecha_respuesta_encuesta);
            \Log::info("Fecha aplicación $data->fecha_respuesta_encuesta\n");
            $diff__years = $actual__Date->diff($response__Data);
            \Log::info("Diferencia $diff__years->y\n");
            if ($diff__years->y < 7) {
                $response = 10;
            } else if($diff__years->y == 7){
                if($diff__years->m < 1){
                    $response = 10;
                }
            } else if($diff__years->y > 7){
                $response = 0;
            }
            \Log::info("Score $response\n");
        }
        return $response;
    }

    /**
     * Get Empresa Constituida
     */

    public function getEmpresaConstituida__4263($user) {
        $query = RespuestaEncuesta::query();
        $query->select(DB::raw('COALESCE( (
            CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4263 AND respuesta_encuesta.codigo_opcion_encuesta = 7015
            THEN 10
            ELSE 0
            END
        ), 0) as response'));
        $query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4263);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);

        return $query->first();
    }

        /**
     * Obtener puntos para pregunta 5633
     */

    public function getEmprendimientoFemenino__5633(int $user)
    {
        $query = RespuestaEncuesta::query();
        $query->select(DB::raw('COALESCE( (
            CASE 
                WHEN respuesta_encuesta.codigo_pregunta_encuesta = 5633 AND respuesta_encuesta.codigo_opcion_encuesta = 10760 
                THEN -80
                ELSE 0
            END
        ), 0) as response'));
        $query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 5633);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);

        return $query->first();
    }

    /**
     * Get Persona Juridica
     */

    public function getPersonaJuridica__4282($user) {
        $query = RespuestaEncuesta::query();
        $query->select(DB::raw('COALESCE( (
            CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4282 AND respuesta_encuesta.codigo_opcion_encuesta = 7045
            THEN 10
            ELSE 0
            END
        ), 0) as response'));
        $query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4282);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);

        return $query->first();
    }

    /**
     * Get Mas de 100
     */

    public function getMasDe100__4336($user) {
		$query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4336);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7146, 7147]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);
        if($user > 36618 && $user <= 39350){
            \Log::info("Se evalua entre 300 y 20000 primera \n");
            $query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta::BIGINT BETWEEN 300000000 AND 2000000000');
        }
        else if(in_array($user, [28906])){
            \Log::info("Se evalua entre 300 y 20000 segunda \n");
            $query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta::BIGINT BETWEEN 300000000 AND 2000000000');
        }
        else if($user > 39350){
            \Log::info("Se evalua mayor O igual a 100M \n");
            $query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta::BIGINT >= 100000000');
        }	    
        else if($user <= 36618){
            \Log::info("Se evalua mayor a 100 \n");
            $query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta::BIGINT > 100000000');
        }            
		return $query->first();
    }

    /**
     * Get Proyecion a mas de 3
     */

    public function getProyecionMas__4342($user) {
		$query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4342);
		$query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', '=', 7174);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);

		$total__score = 0;
		$expected__response = 0;
        \Log::info("Va aca -------------------------------------- \n");
		if (self::getMasDe100__4336($user) != null) {
            \Log::info("Anterior: ".(int)self::getMasDe100__4336($user)->texto_respuesta_encuesta." nueva: ".(int)$query->first()->texto_respuesta_encuesta." \n");
			$expected__response = (int)self::getMasDe100__4336($user)->texto_respuesta_encuesta;
			if((int)$query->first()->texto_respuesta_encuesta > $expected__response) {
				$total__score = 10;
			}
		}

		return $total__score;
    }

    /**
     * Get equipo emprendedor
    */
    public function getEquipoEmprendedor__4372($user) {
        $query = RespuestaEncuesta::query();
        $query->select(DB::raw('COALESCE( (
            CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4372 AND respuesta_encuesta.texto_respuesta_encuesta::INTEGER >= 2
            THEN 10
            ELSE 0
            END
        ), 0) as response'));
        $query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4372);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);

        return $query->first();
    }

    /**
     * Get dedicacion
     */

    public static function getDedicacion__4373($usuario) {
		$query = RespuestaEncuesta::query();
		$query->select('respuesta_encuesta.texto_respuesta_encuesta');
		$query->where('respuesta_encuesta.codigo_estado', '=', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7262);
		$query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', '=', 7269);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $usuario);
		$query->whereRaw('respuesta_encuesta.texto_respuesta_encuesta IN (102::varchar(255), 105::varchar(255))');
        $data = $query->first();

        $result = 0;
        if($data instanceof RespuestaEncuesta) {
            $result = 10;
        }

		return $result;
    }
    
    /**
     * Get esta buscando trabajo
     */

    public function getBuscandoTrabajo__4392($user) {
        $query = RespuestaEncuesta::query();
        $query->select(DB::raw('COUNT(respuesta_encuesta.codigo_respuesta_encuesta) as value'));
        $query->join('core.pregunta_encuesta', 'pregunta_encuesta.codigo_pregunta_encuesta', '=', 'respuesta_encuesta.codigo_pregunta_encuesta');
        $query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4392);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '!=', 7323);
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $user);

        return $query->first();
    }

    public function getEvaluators($codigo_registro_fase_usuario) {
        $query = AsignacionJurado::query();
        $query->select('evaluador.nombre_usuario as nombre_evaluador_momento3','puntaje_total_eval_emprendi','comentario_resultado_evaluacion', 'fecha_asignacion_jurado', 'fecha_evaluacion_jurado');
        $query->join('core.resultado_evaluacion_emprendimiento as ree', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'ree.codigo_asignacion_jurado');
        $query->join('seguridad.usuario as evaluador', 'asignacion_jurado.codigo_usuario', '=', 'evaluador.codigo_usuario');
        $query->join('core.persona as pevaluador', 'evaluador.codigo_usuario', '=', 'pevaluador.codigo_usuario');
        $query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $codigo_registro_fase_usuario);
        $data = $query->get();
        return $data;
    }

    /**
     * Get Result Evaluation Challenge 1.2
     */

    public function getResultEvaluationChallenge__1_2($register){
        $query = RegistroFaseUsuario::query();
        $query->select('opc_eval_pregunta.nombre_opc_eval_pregunta as componente');
        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
        $query->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
        $query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
        $query->where('registro_fase_usuario.codigo_registro_fase_usuario', $register);

        return $query->first();
    }

    /**
     * Get Name and Gender of Team
     */

    public function getNameAndGenderTeam($integrant, $userSurvey) {
        $query = RespuestaEncuesta::query();
        switch ($integrant) {
            case 'Leader__Name':
                $query->select('texto_respuesta_encuesta as nombre');
                $query->where('codigo_opcion_encuesta', 7251);
                $query->where('cor_codigo_opcion_encuesta', 7269);
            break;
            case 'SecondIntegrant__Name':
                $query->select('texto_respuesta_encuesta as nombre');
                $query->where('codigo_opcion_encuesta', 7251);
                $query->where('cor_codigo_opcion_encuesta', 7270);
            break;
            case 'ThreeIntegrant__Name':
                $query->select('texto_respuesta_encuesta as nombre');
                $query->where('codigo_opcion_encuesta', 7251);
                $query->where('cor_codigo_opcion_encuesta', 7271);
            break;
            case 'FourIntegrant__Name':
                $query->select('texto_respuesta_encuesta as nombre');
                $query->where('codigo_opcion_encuesta', 7251);
                $query->where('cor_codigo_opcion_encuesta', 7272);
            break;
            case 'Leader__Gender':
                $query->select('nombre_item_opcion_encuesta as genero');
                $query->join('core.item_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', '=', DB::raw('item_opcion_encuesta.codigo_item_opcion_encuesta::varchar(255)'));
                $query->where('respuesta_encuesta.codigo_opcion_encuesta', 7252);
                $query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', 7269);
            break;
            case 'SecondIntegrant__Gender':
                $query->select('nombre_item_opcion_encuesta as genero');
                $query->join('core.item_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', '=', DB::raw('item_opcion_encuesta.codigo_item_opcion_encuesta::varchar(255)'));
                $query->where('respuesta_encuesta.codigo_opcion_encuesta', 7252);
                $query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', 7270);
            break;
            case 'ThreeIntegrant__Gender':
                $query->select('nombre_item_opcion_encuesta as genero');
                $query->join('core.item_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', '=', DB::raw('item_opcion_encuesta.codigo_item_opcion_encuesta::varchar(255)'));
                $query->where('respuesta_encuesta.codigo_opcion_encuesta', 7252);
                $query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', 7271);
            break;
            case 'FourIntegrant__Gender':
                $query->select('nombre_item_opcion_encuesta as genero');
                $query->join('core.item_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', '=', DB::raw('item_opcion_encuesta.codigo_item_opcion_encuesta::varchar(255)'));
                $query->where('respuesta_encuesta.codigo_opcion_encuesta', 7252);
                $query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', 7272);;
            break;
        }
        $query->where('codigo_pregunta_encuesta', 4373);
        $query->where('codigo_usuario_encuesta', $userSurvey);

        return $query->first();
    }

    public function getFinalScore($register) {
        $query = PuntajeRegistroFase::query();
        $query->select('valor_puntaje_registro_fase');
        $query->where('codigo_registro_fase_usuario', $register);
        $query->where('codigo_estado', Estado::getState('active'));
        $data = $query->first();
        $response = '';

        if($data instanceof PuntajeRegistroFase) {
            if ($data->valor_puntaje_registro_fase > 60) {
                $response = 'PASA';
            } else {
                $response = 'NO PASA';
            }
        }

        return $response;
    }

    public function getInnovation__Challenge_1_3($register, $userSurvey) {
        $object = new \stdClass();

        $propiedad_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($userSurvey, 'propiedad');
        $motivo_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($userSurvey, 'motivo');
        $tipo_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($userSurvey, 'tipo');
        $evaluators_innovation = Innpulsa::Calificacion__Aldea20__three_moment__innovacion($register);
        $total_innovacion = ( ( isset($propiedad_innovacion->propiedad_score) ? $propiedad_innovacion->propiedad_score : 0 ) + ( isset($motivo_innovacion->motivo_score) ? $motivo_innovacion->motivo_score : 0 ) + ( isset($tipo_innovacion->tipo_score) ? $tipo_innovacion->tipo_score : 0 ) );

        $total_cal_innovacion = 0;
        if (count($evaluators_innovation) > 0) {
            foreach($evaluators_innovation as $cal) {
                $total_cal_innovacion = $total_cal_innovacion + $cal->total_scoring;
            }
        }

        $total_innovacion = $total_innovacion + ($total_cal_innovacion * 0.6);
        $total_innovacion_res = $total_innovacion * 0.3;

        $object->total = $total_innovacion_res;
        $object->evaluators = $evaluators_innovation;
        return $object;
    }

    public function getTraccion__Challenge_1_3($register, $userSurvey) {
        $object = new \stdClass();

        $alcance_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($userSurvey, 'alcance');
        $cuentas_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($userSurvey, 'cuentas');
        $estados_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($userSurvey, 'estados');
        $indicadores_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($userSurvey, 'indicadores');
        $evaluators_traccion = Innpulsa::Calificacion__Aldea20__three_moment__traccion($register);
        $total_traccion = ( ( isset($alcance_traccion->alcance_score) ? $alcance_traccion->alcance_score : 0 ) + ( isset($cuentas_traccion->cuentas_score) ? $cuentas_traccion->cuentas_score : 0 ) + ( isset($estados_traccion->estados_score) ? $estados_traccion->estados_score : 0 ) + ( isset($indicadores_traccion->indicadores_score) ? $indicadores_traccion->indicadores_score : 0 ) );

        $total_cal_traccion = 0;
        if (count($evaluators_traccion) > 0) {
            foreach($evaluators_traccion as $cal_t) {
                $total_cal_traccion += (double)$cal_t->total_scoring;
            }
        }

        $total_traccion = $total_traccion + ( round($total_cal_traccion * 0.45, 2) );
        $total_traccion_res = $total_traccion * 0.1;

        $object->total = $total_traccion_res;
        $object->evaluators = $evaluators_traccion;
        return $object;
    }

    public function getEscalabilidad__Challenge_1_3($register, $userSurvey){
        $object = new \stdClass();

        $idioma_escalabilidad = Innpulsa::getResponseEvaluationAldea20__three__moment__escalabilidad($userSurvey, 'idioma');
        $metas_escalabilidad = Innpulsa::getResponseEvaluationAldea20__three__moment__escalabilidad($userSurvey, 'metas');
        $evaluators_escalabilidad = Innpulsa::Calificacion__potential__Aldea20__three_moment__equipo($register);
        $total_escalabilidad = ( ( isset($idioma_escalabilidad->idioma_score) ? $idioma_escalabilidad->idioma_score : 0 ) + ( isset($metas_escalabilidad->metas_score) ? $metas_escalabilidad->metas_score : 0 ) );

        $total_cal_escalabilidad = 0;
        if (count($evaluators_escalabilidad) > 0) {
            foreach($evaluators_escalabilidad as $cal) {
                $total_cal_escalabilidad = $total_cal_escalabilidad + $cal->total_score;
            }
        }

        $total_escalabilidad = $total_escalabilidad + ($total_cal_escalabilidad * 0.8);
        $total_escalabilidad_res = $total_escalabilidad * 0.15;

        $object->total = $total_escalabilidad_res;
        $object->evaluators = $evaluators_escalabilidad;
        return $object;
    }

    public function getEquipo__Challenge_1_3($register, $userSurvey) {
        $object = new \stdClass();
        // \Log::info("esta es la primera posible error $userSurvey \n");
        $experiencia_laboral_equipo = Innpulsa::getResponseEvaluationAldea20__three__moment__equipo($userSurvey, 'laboral');        
        $experiencia_sector_equipo = Innpulsa::getSectorExprerienceAldea20__three__moment__equipo($userSurvey);
        $experiencia_emprendedora_equipo = Innpulsa::getEmprendorExprerienceAldea20__three__moment__equipo($userSurvey);
        $roles_equipo = Innpulsa::getRolesAldea20__three__moment__equipo($userSurvey);
        $genero_equipo = Innpulsa::getGenderAldea20__three__moment__equipo($userSurvey);
        $riesgo_equipo = Innpulsa::getRiesgoAldea20__three__moment__equipo($userSurvey);
        // \Log::info("esta es el segundo posible error $userSurvey \n");
        $decisiones_equipo = Innpulsa::getResponseEvaluationAldea20__three__moment__equipo($userSurvey, 'decisiones');        
        $evaluators_team = Innpulsa::Calificacion__Aldea20__three_moment__equipo($register);
        $total_equipo = ( ( isset($experiencia_laboral_equipo->laboral_score) ? $experiencia_laboral_equipo->laboral_score : 0 ) + ( isset($experiencia_sector_equipo['sector_scoring']) ? $experiencia_sector_equipo['sector_scoring'] : 0 ) + ( isset($experiencia_emprendedora_equipo['emprendor_scoring']) ? $experiencia_emprendedora_equipo['emprendor_scoring'] : 0 ) + ( isset($roles_equipo) ? $roles_equipo : 0 ) + ( isset($genero_equipo) ? $genero_equipo : 0 ) + ( isset($riesgo_equipo->riesgo_score) ? $riesgo_equipo->riesgo_score : 0 ) + ( isset($decisiones_equipo->decisiones_score) ?  $decisiones_equipo->decisiones_score : 0 ) );

        $total_cal_equipo= 0;
        if (count($evaluators_team) > 0) {
            foreach($evaluators_team as $cal) {
                $total_cal_equipo = $total_cal_equipo + (int)$cal->total_scoring;
            }
        }

        $total_equipo = $total_equipo + ($total_cal_equipo * 0.3);
        $total_equipo_res = $total_equipo * 0.45;

        $object->total = $total_equipo_res;
        $object->evaluators = $evaluators_team;
        return $object;
    }

    public function getInnovation__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_innovation = Innpulsa::Calificacion__Aldea20__second_moment__innovacion($register);

        $object->evaluators = $evaluators_innovation;
        return $object;
    }

    public function getMercado__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_mercado = Innpulsa::Calificacion__Aldea20__second_moment__mercado($register);

        $object->evaluators = $evaluators_mercado;
        return $object;
    }

    public function getMercado_Pot__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_mercado = Innpulsa::Calificacion__Aldea20__second_moment__mercado__pot($register);

        $object->evaluators = $evaluators_mercado;
        return $object;
    }

    public function getMercado_Bar__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_mercado = Innpulsa::Calificacion__Aldea20__second_moment__mercado__bar($register);

        $object->evaluators = $evaluators_mercado;
        return $object;
    }

    public function getEquipo__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_equipo = Innpulsa::Calificacion__Aldea20__second_moment__equipo($register);

        $object->evaluators = $evaluators_equipo;
        return $object;
    }

    public function getEquipo__cap__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_equipo = Innpulsa::Calificacion__Aldea20__second_moment__equipo_cap($register);

        $object->evaluators = $evaluators_equipo;
        return $object;
    }

    public function getAlineacion__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_alineacion = Innpulsa::Calificacion__Aldea20__second_moment__alineacion($register);

        $object->evaluators = $evaluators_alineacion;
        return $object;
    }

    public function getRecomendacion__Challenge_2($register, $userSurvey) {
        $object = new \stdClass();

        $evaluators_recomendacion = Innpulsa::Calificacion__Aldea20__second_moment__recomendaciones($register);

        $object->evaluators = $evaluators_recomendacion;
        return $object;
    }

    public function getTotal($program, $user, $register) {
		$total = 0;
		$total += ResultadoEvaluation::getCityOfEntrepreneurship($user)->puntaje;
		$total += ResultadoEvaluation::getTotalScoreMomentThree($register)->puntaje;
		$total += ResultadoEvaluation::getTotalScoreMomentFour($program)->puntaje;
		$total += ResultadoEvaluation::getUserEntity($user)->puntaje;

		$object = new \stdClass();
		$object->puntaje = round($total, 2);

		return $object;
	}

    public function getRegisterPhase($registro)
	{
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario', 'registro_fase_usuario.valor_asignacion_rfu', 'registro_fase_usuario.codigo_usuario','fase.codigo_evaluacion_emprendimiento', 'evaluacion_emprendimiento.comentario_evaluacion_cerrado', 'registro_fase_usuario.codigo_registro_convocatoria',
			DB::raw("(person.nombre_usuario || ' ' || COALESCE(creador.primer_apellido_persona, '') || ' ' || COALESCE(creador.segundo_apellido_persona, '')) as nombre_usuario_creador" ),
			DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END) as nombre_usuario"),'convocatoria.codigo_convocatoria','observaciones_registro_fase_usuario', 'usuario.biografia_usuario', 'registro_convocatoria.fecha_registro_convocatoria', 'registro_convocatoria.hora_registro_convocatoria',
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'), 'nombre_fase'
		);
		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona as creador', 'person.codigo_usuario', '=', 'creador.codigo_usuario');
		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', 51)
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->leftJoin('core.evaluacion_emprendimiento', 'evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', '=', 'fase.codigo_evaluacion_emprendimiento');
		$query->where('registro_fase_usuario.codigo_registro_fase_usuario', $registro);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		return  $query->first();
    }

    public function getSoldLastYear($userSurvey) {
        $query = RespuestaEncuesta::query();
        $query->select('texto_respuesta_encuesta');
        $query->where('codigo_usuario_encuesta', $userSurvey);
        $query->where('codigo_pregunta_encuesta', 4336);
        $query->where('cor_codigo_opcion_encuesta', 7146);
        $data = $query->first();

        if (isset($data->texto_respuesta_encuesta) && $data->texto_respuesta_encuesta != '') {
            return $data->texto_respuesta_encuesta;
        } else {
            return null;
        }
    }

    public function getTotalDocumenter13($value){
        $register = RegistroFaseUsuario::find($value);

        if (!$register instanceof RegistroFaseUsuario) {
            return null;
        }

        $total =  ResultadoEvaluation::buildResultEvaluationProgram($register);
        return $total->response[4]->puntaje;
    }
}