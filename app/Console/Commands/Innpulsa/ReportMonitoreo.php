<?php
namespace App\Console\Commands\Innpulsa;
use App\Models\Core\CategoriaProducto;
use App\Models\Core\PreguntaEncuesta;
use App\Models\Core\TipoCampoPregunta;
use Illuminate\Console\Command;
use App\Models\Seguridad\Usuario, App\Models\Param\Institucion, App\Models\Core\RegistroConvocatoria, App\Models\Core\Estado;
use DB, Log, Excel, Storage, SendEmail;
class ReportMonitoreo extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'innpulsa:monitoreo {user}';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Reporte seguimiento unico Aldea iNNpulsa';
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
      $query->select('registro_convocatoria.numero_registro_convocatoria', 'rfu_reto2.codigo_registro_fase_usuario as crfu_reto2', 'rfu_reto2.valor_asignacion_rfu as llaves_de_acceso', 'fecha_registro_convocatoria', 'hora_registro_convocatoria', 'ue.nombre_usuario as nombre_proyecto', 'uo.nombre_usuario', 'persona.genero_persona', 'ue.identificacion_usuario', 'uo.correo_electronico', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'categoria_proyecto.nombre_categoria_proyecto', 'ce.nombre_categoria_proyecto as nombre_categoria_proyecto_evaluador', 'usuario_encuesta.puntaje_total_encuesta', 'resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion', 'resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', 'asignacion_jurado.fecha_asignacion_jurado', 'rfu_reto1.rango_inferior_eval_fase as rango_reto1', 'rfu_reto12.rango_inferior_eval_fase as rango_inferior_reto12', 'rfu_reto12.rango_superior_eval_fase as rango_superior_reto12',
        DB::raw("(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN rfu_reto12.notificacion_cambio_registro_fase ELSE rfu_reto1.notificacion_cambio_registro_fase END) as notificacion_cambio_registro_fase"), 'rfu_reto2.notificacion_cambio_registro_fase as notificacion_cambio_registro_fase2', 'rfu_reto2.observaciones_registro_fase_usuario', 'nombre_clase_emprendimiento', 'carta_compormiso_compra.nombre_representante_legal', 'carta_compormiso_compra.apellidos_representante_legal', 'carta_compormiso_compra.identificacion_rep_legal', 'ciudadrepresentante.nombre_ciudad as ciudad_expedicion_epresentante', 'cue.region_ciudad', 'carta_compormiso_compra.fecha_aceptacion_carta_compormiso_compra', 'estado.nombre_estado', 'rfu_reto2.codigo_registro_fase_usuario as register_phase_2',
        DB::raw("trim(initcap(ciudad.distrito_ciudad)) as departamento_usuario"),
        DB::raw("trim(initcap(ciudad.nombre_ciudad)) as ciudad_usuario"),
        DB::raw("trim(initcap(cue.nombre_ciudad)) as ciudad_usuario_entidad"),
        DB::raw("trim(initcap(cue.distrito_ciudad)) as departamento_usuario_entidad"),
        DB::raw("(uev.nombre_usuario || ' ' || COALESCE(puev.primer_apellido_persona, '') || ' ' || COALESCE(puev.segundo_apellido_persona, '')) as nombre_evaluador"),
        DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1913 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as empresa_constituida"),
        DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1222 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as tipo_documento"),
        DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1245 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as empresa_cuenta_con"),
        DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1246 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as producto_compite_gana"),
        DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1250 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as perfil_equipo_emprendedor"),
        DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1254 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as estado_desarrollo_producto"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1223 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as numero_documento"),
        DB::raw("(SELECT nivel_educacion.nombre_nivel_educacion FROM core.respuesta_encuesta as pgencu LEFT JOIN core.nivel_educacion ON nivel_educacion.codigo_nivel_educacion = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1224 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta ) as nivel_estudio_usuario"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1225 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as telefono_usuario"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1226 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as movil_usuario"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1244 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as cantidad_empleados"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1255 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as describe_producto"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1260 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ventas_actuales_producto"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1286 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as como_genera_ingreso_empresa"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1928 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as direccion_usuario"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1929 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_postal_usuario"),
        DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1965 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ventas_actuales_negocio"),
        DB::raw("(SELECT nombre_ciudad FROM core.respuesta_encuesta as pgencu JOIN core.ciudad ON codigo_ciudad = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1954 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as lugar_expedicion_documento"),
        DB::raw("(SELECT ciudpreg.nombre_ciudad FROM core.respuesta_encuesta as ciudpregenc JOIN core.ciudad as ciudpreg ON ciudpreg.codigo_ciudad = ciudpregenc.texto_respuesta_encuesta::integer WHERE ciudpregenc.codigo_pregunta_encuesta = 1228 and ciudpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciudad_pregunta"),
        DB::raw("(SELECT ciuddistripre.distrito_ciudad FROM core.respuesta_encuesta as distrpregenc JOIN core.ciudad as ciuddistripre ON ciuddistripre.codigo_ciudad = distrpregenc.texto_respuesta_encuesta::integer WHERE distrpregenc.codigo_pregunta_encuesta = 1228 and distrpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as distrito_pregunta"),
        DB::raw("(SELECT ciudregpregenc.region_ciudad FROM core.respuesta_encuesta as regpregenc JOIN core.ciudad as ciudregpregenc ON ciudregpregenc.codigo_ciudad = regpregenc.texto_respuesta_encuesta::integer WHERE regpregenc.codigo_pregunta_encuesta = 1228 and regpregenc.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as region_pregunta"),
        DB::raw("(SELECT codigo_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_ciiu"),
        DB::raw("(SELECT nombre_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciiu"),
        DB::raw("(SELECT nombre_sector_economico FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer JOIN core.sector_economico ON sector_interes.codigo_sector_economico = sector_economico.codigo_sector_economico WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as actividad_economica"),
        DB::raw("
                (CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
                    (
                        SELECT ROUND(SUM( ( t.evaluacion + t.scoring ) / 2 ), 2)::text
                            FROM (
                                SELECT codigo_grupo_item_eval,
                                SUM( COALESCE(puntaje_resul_eval_empren, 0) ) AS evaluacion,
                                (
                                    SELECT SUM(puntaje_respuesta_encuesta)
                                    FROM core.respuesta_encuesta
                                    INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
                                    WHERE pregunta_encuesta.codigo_encuesta = 49
                                    AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                                    AND pregunta_encuesta.codigo_pregunta_encuesta in (1245, 1249, 1246, 1247)
                                    AND respuesta_encuesta.codigo_estado = 8
                                ) AS scoring
                                FROM core.item_evaluacion_emprendimiento
                                LEFT JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                                LEFT JOIN core.resultado_evaluacion_emprendimiento ON resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion = evaluacion_item_emprendimiento.codigo_resultado_evaluacion
                                LEFT JOIN core.asignacion_jurado ON resultado_evaluacion_emprendimiento.codigo_asignacion_jurado = asignacion_jurado.codigo_asignacion_jurado
                                WHERE
                                item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9 AND codigo_grupo_item_eval = 1
                                AND (asignacion_jurado.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario OR asignacion_jurado.codigo_registro_fase_usuario IS NULL)
                                GROUP BY codigo_grupo_item_eval
                                ORDER BY codigo_grupo_item_eval
                            ) as t
                        )
                    ELSE ''
                END ) as average_differential"
        ),
        DB::raw("
                (CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
                    (
                        SELECT ROUND(SUM( ( t.evaluacion + t.scoring ) / 2 ), 2)::text
                            FROM (
                                SELECT codigo_grupo_item_eval,
                                SUM( COALESCE(puntaje_resul_eval_empren, 0) ) AS evaluacion,
                                (
                                    ( SELECT
                                        SUM((CASE pregunta_encuesta.codigo_pregunta_encuesta
                                            WHEN 1395
                                            THEN (
                                                (CASE WHEN (pregunta_encuesta.codigo_pregunta_encuesta = 1395 and respuesta_encuesta.codigo_opcion_encuesta = 1851)
                                                    THEN ( CASE
                                                        WHEN (
                                                            (SELECT COALESCE(r1260.texto_respuesta_encuesta::double precision, 0)
                                                                FROM core.respuesta_encuesta as r1260
                                                                WHERE r1260.codigo_usuario_encuesta = respuesta_encuesta.codigo_usuario_encuesta
                                                                AND r1260.codigo_pregunta_encuesta = 1260
                                                            ) >= 300000
                                                        )
                                                    THEN 7.5 ELSE -7.5 END)
                                                ELSE 0 END)
                                            )
                                            ELSE puntaje_respuesta_encuesta
                                        END))
                                    FROM core.respuesta_encuesta
                                    INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
                                    WHERE pregunta_encuesta.codigo_encuesta = 49 AND respuesta_encuesta.codigo_estado = 8
                                    AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                                    AND pregunta_encuesta.codigo_pregunta_encuesta IN (1395, 1252, 1253, 1248) )
                                ) AS scoring
                                FROM core.item_evaluacion_emprendimiento
                                LEFT JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                                LEFT JOIN core.resultado_evaluacion_emprendimiento ON resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion = evaluacion_item_emprendimiento.codigo_resultado_evaluacion
                                LEFT JOIN core.asignacion_jurado ON resultado_evaluacion_emprendimiento.codigo_asignacion_jurado = asignacion_jurado.codigo_asignacion_jurado
                                WHERE
                                item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9 AND codigo_grupo_item_eval = 2
                                AND (asignacion_jurado.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario OR asignacion_jurado.codigo_registro_fase_usuario IS NULL)
                                GROUP BY codigo_grupo_item_eval
                                ORDER BY codigo_grupo_item_eval
                            ) as t
                        )
                    ELSE ''
                END ) as average_commercial"
        ),
        DB::raw("
                (CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
                    (SELECT SUM(puntaje_respuesta_encuesta)::text
                        FROM core.respuesta_encuesta
                        INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
                        WHERE pregunta_encuesta.codigo_encuesta = 49 AND respuesta_encuesta.codigo_estado = 8
                        AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                        AND pregunta_encuesta.codigo_pregunta_encuesta IN (1254, 1395, 1256) )
                    ELSE ''
                END ) as validation_commercial"),
        DB::raw("
                (CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
                    (SELECT SUM(puntaje_respuesta_encuesta)::text
                        FROM core.respuesta_encuesta
                        INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
                        WHERE pregunta_encuesta.codigo_encuesta = 49 AND respuesta_encuesta.codigo_estado = 8
                        AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                        AND pregunta_encuesta.codigo_pregunta_encuesta IN (1250, 1251) )
                    ELSE ''
                END ) as average_work_team"),
        DB::raw("
				        ( CASE WHEN usuario_encuesta.puntaje_total_encuesta > 55 THEN
                    CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
                        CASE
                             WHEN resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi >= 55 AND resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi <= 59.99  THEN 
                              'No pasa'
                            WHEN resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi >= 60 AND resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi <= 85  THEN
                              'Pasa'
                            WHEN  resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi > 85  THEN
                              'No pasa'
                            WHEN resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi < 55  THEN
                              'No pasa'
                          ELSE ''
                        END
                    END
                 ELSE 'No absoluto' 
				        END) as result_reto12"),
        // Reto 2
        DB::raw("
                (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                    THEN
                        ( SELECT asigjug1_reto2.lugar_evaluacion_jurado
                            FROM core.asignacion_jurado as asigjug1_reto2
                            WHERE rfu_reto2.codigo_registro_fase_usuario = asigjug1_reto2.codigo_registro_fase_usuario
                            AND asigjug1_reto2.codigo_estado != 13
                            ORDER BY asigjug1_reto2.codigo_asignacion_jurado ASC
                            LIMIT 1
                        )
                    ELSE ''
                END )
                AS lugar_conversatorio"),
        DB::raw("
                (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                    THEN
                        ( SELECT asigjug1_reto2.fecha_evaluacion_jurado
                            FROM core.asignacion_jurado as asigjug1_reto2
                            WHERE rfu_reto2.codigo_registro_fase_usuario = asigjug1_reto2.codigo_registro_fase_usuario
                            AND asigjug1_reto2.codigo_estado != 13
                            ORDER BY asigjug1_reto2.codigo_asignacion_jurado ASC
                            LIMIT 1
                        )
                    ELSE null
                END )
                AS fecha_conversatorio"),
        DB::raw("
                (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                    THEN
                        ( SELECT (ujug1_reto2.nombre_usuario || ' ' || COALESCE(pjug1_reto2.primer_apellido_persona, '') || ' ' || COALESCE(pjug1_reto2.segundo_apellido_persona, '')) FROM core.asignacion_jurado as asigjug1_reto2
                            JOIN seguridad.usuario as ujug1_reto2 ON ujug1_reto2.codigo_usuario = asigjug1_reto2.codigo_usuario
                            JOIN core.persona as pjug1_reto2 ON ujug1_reto2.codigo_usuario = pjug1_reto2.codigo_usuario
                            WHERE rfu_reto2.codigo_registro_fase_usuario = asigjug1_reto2.codigo_registro_fase_usuario
                            AND asigjug1_reto2.codigo_estado != 13
                            AND asigjug1_reto2.codigo_especialidad = 24
                            LIMIT 1
                        )
                    ELSE ''
                END )
                AS expert_investor"),
        DB::raw("
                (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                    THEN
                        ( SELECT (ujug1_reto2.nombre_usuario || ' ' || COALESCE(pjug1_reto2.primer_apellido_persona, '') || ' ' || COALESCE(pjug1_reto2.segundo_apellido_persona, '')) FROM core.asignacion_jurado as asigjug1_reto2
                            JOIN seguridad.usuario as ujug1_reto2 ON ujug1_reto2.codigo_usuario = asigjug1_reto2.codigo_usuario
                            JOIN core.persona as pjug1_reto2 ON ujug1_reto2.codigo_usuario = pjug1_reto2.codigo_usuario
                            WHERE rfu_reto2.codigo_registro_fase_usuario = asigjug1_reto2.codigo_registro_fase_usuario
                            AND asigjug1_reto2.codigo_estado != 13
                            AND asigjug1_reto2.codigo_especialidad = 23
                            LIMIT 1
                        )
                    ELSE ''
                END )
                AS expert_business"),
        DB::raw("
                (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                    THEN
                        ( SELECT (ujug1_reto2.nombre_usuario || ' ' || COALESCE(pjug1_reto2.primer_apellido_persona, '') || ' ' || COALESCE(pjug1_reto2.segundo_apellido_persona, '')) FROM core.asignacion_jurado as asigjug1_reto2
                            JOIN seguridad.usuario as ujug1_reto2 ON ujug1_reto2.codigo_usuario = asigjug1_reto2.codigo_usuario
                            JOIN core.persona as pjug1_reto2 ON ujug1_reto2.codigo_usuario = pjug1_reto2.codigo_usuario
                            WHERE rfu_reto2.codigo_registro_fase_usuario = asigjug1_reto2.codigo_registro_fase_usuario
                            AND asigjug1_reto2.codigo_estado != 13
                            AND asigjug1_reto2.codigo_especialidad = 22
                            LIMIT 1
                        )
                    ELSE ''
                END )
                AS sector_expert"),
        DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT round( ( ( sum(valor_opc_eval_pregunta) / 3) / 40) * 100) FROM core.resultado_evaluacion_emprendimiento as revem_reto2
                            JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_resultado_evaluacion = revem_reto2.codigo_resultado_evaluacion
                            JOIN core.item_evaluacion_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                            JOIN core.opc_eval_pregunta ON opc_eval_pregunta.codigo_opc_eval_pregunta = evaluacion_item_emprendimiento.valor_evaluacion_item
                            JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = revem_reto2.codigo_asignacion_jurado
                            WHERE asignacion_jurado.codigo_registro_fase_usuario = rfu_reto2.codigo_registro_fase_usuario
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 10
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 5
                        )
                    ELSE null
                END )
                AS enterprising_team"),
        DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT round( ( ( sum(valor_opc_eval_pregunta) / 3) / 25) * 100) FROM core.resultado_evaluacion_emprendimiento as revem_reto2
                            JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_resultado_evaluacion = revem_reto2.codigo_resultado_evaluacion
                            JOIN core.item_evaluacion_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                            JOIN core.opc_eval_pregunta ON opc_eval_pregunta.codigo_opc_eval_pregunta = evaluacion_item_emprendimiento.valor_evaluacion_item
                            JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = revem_reto2.codigo_asignacion_jurado
                            WHERE asignacion_jurado.codigo_registro_fase_usuario = rfu_reto2.codigo_registro_fase_usuario
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 10
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 6
                        )
                    ELSE null
                END )
                AS market"),
        DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT round( ( ( sum(valor_opc_eval_pregunta) / 3) / 25) * 100) FROM core.resultado_evaluacion_emprendimiento as revem_reto2
                            JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_resultado_evaluacion = revem_reto2.codigo_resultado_evaluacion
                            JOIN core.item_evaluacion_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                            JOIN core.opc_eval_pregunta ON opc_eval_pregunta.codigo_opc_eval_pregunta = evaluacion_item_emprendimiento.valor_evaluacion_item
                            JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = revem_reto2.codigo_asignacion_jurado
                            WHERE asignacion_jurado.codigo_registro_fase_usuario = rfu_reto2.codigo_registro_fase_usuario
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 10
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 7
                        )
                    ELSE null
                END )
                AS product"),
        DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT round( ( ( sum(valor_opc_eval_pregunta) / 3) / 10) * 100) FROM core.resultado_evaluacion_emprendimiento as revem_reto2
                            JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_resultado_evaluacion = revem_reto2.codigo_resultado_evaluacion
                            JOIN core.item_evaluacion_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                            JOIN core.opc_eval_pregunta ON opc_eval_pregunta.codigo_opc_eval_pregunta = evaluacion_item_emprendimiento.valor_evaluacion_item
                            JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = revem_reto2.codigo_asignacion_jurado
                            WHERE asignacion_jurado.codigo_registro_fase_usuario = rfu_reto2.codigo_registro_fase_usuario
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 10
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 8
                        )
                    ELSE null
                END )
                AS productive_chains"),
//        DB::raw("
//                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
//                        THEN
//                        (
//                            SELECT STRING_AGG(cat_padre_producto.nombre_cat_padre_producto::text, ',') FROM core.categoria_producto
//                            JOIN core.categoria_producto_insti ON categoria_producto.codigo_categoria_producto = categoria_producto_insti.codigo_categoria_producto
//                            JOIN core.recom_producto_eval ON recom_producto_eval.codigo_categoria_producto = categoria_producto_insti.codigo_categoria_producto
//                            JOIN core.cat_padre_producto ON cat_padre_producto.codigo_cat_padre_producto = categoria_producto_insti.codigo_cat_padre_producto
//                            WHERE rfu_reto2.codigo_registro_fase_usuario = recom_producto_eval.codigo_registro_fase_usuario AND categoria_producto_insti.codigo_institucion = 51 AND categoria_producto_insti.codigo_estado = 8
//
//                        )
//                    ELSE null
//                END )
//                AS categorias_marketplace"),
        DB::raw("
                    (CASE WHEN rfu_reto3.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT COALESCE(COUNT(acuerdo_compra.codigo_acuerdo_compra), 0)
                            FROM core.registro_fase_usuario
                            JOIN core.detalle_voucher ON detalle_voucher.codigo_registro_fase_usuario = registro_fase_usuario.codigo_registro_fase_usuario
                            JOIN core.consumo_voucher ON detalle_voucher.codigo_detalle_voucher = consumo_voucher.codigo_detalle_voucher
                            JOIN core.compra ON consumo_voucher.codigo_compra = compra.codigo_compra
                            JOIN core.acuerdo_compra ON compra.codigo_compra = acuerdo_compra.codigo_compra
                            WHERE registro_fase_usuario.codigo_registro_fase_usuario = rfu_reto3.codigo_registro_fase_usuario
                            AND acuerdo_compra.codigo_estado IN (7, 29)
                        )
                    ELSE 0
                END )
                AS acuerdos_validados_innpulsa"),
        DB::raw("
                    (CASE WHEN rfu_reto3.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT COALESCE(SUM(consumo_voucher.valor_consumo_voucher), 0)
                            FROM core.registro_fase_usuario
                            JOIN core.detalle_voucher ON detalle_voucher.codigo_registro_fase_usuario = registro_fase_usuario.codigo_registro_fase_usuario
                            JOIN core.consumo_voucher ON detalle_voucher.codigo_detalle_voucher = consumo_voucher.codigo_detalle_voucher
                            JOIN core.compra ON consumo_voucher.codigo_compra = compra.codigo_compra
                            JOIN core.acuerdo_compra ON compra.codigo_compra = acuerdo_compra.codigo_compra
                            WHERE registro_fase_usuario.codigo_registro_fase_usuario = rfu_reto3.codigo_registro_fase_usuario
                            AND acuerdo_compra.codigo_estado IN (7, 29)
                        )
                    ELSE 0
                END )
                AS valor_acuerdos_validados_innpulsa"),
        DB::raw("
                    (CASE WHEN rfu_reto3.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT MAX(actividad_acuerdo.fecha_final_actividad_acuerdo)
                            FROM core.registro_fase_usuario
                            JOIN core.detalle_voucher ON detalle_voucher.codigo_registro_fase_usuario = registro_fase_usuario.codigo_registro_fase_usuario
                            JOIN core.consumo_voucher ON detalle_voucher.codigo_detalle_voucher = consumo_voucher.codigo_detalle_voucher
                            JOIN core.compra ON consumo_voucher.codigo_compra = compra.codigo_compra
                            JOIN core.acuerdo_compra ON compra.codigo_compra = acuerdo_compra.codigo_compra
                            JOIN core.actividad_acuerdo ON actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra
                            WHERE registro_fase_usuario.codigo_registro_fase_usuario = rfu_reto3.codigo_registro_fase_usuario
                        )
                    ELSE null END ) AS fecha_final_actividades_acuerdo")
      );
      // User entity
      $query->join('seguridad.usuario as ue', 'registro_convocatoria.codigo_usuario', '=', 'ue.codigo_usuario');
      $query->join('core.entidad', 'ue.codigo_usuario', '=', 'entidad.codigo_usuario');
      $query->leftJoin('core.ciudad as cue', 'ue.codigo_ciudad', '=', 'cue.codigo_ciudad');
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
      // Reto1
      $query->join('core.registro_fase_usuario as rfu_reto1', function($join){
        $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto1.codigo_registro_convocatoria')
          ->where('rfu_reto1.codigo_fase', '=', 134);
      });
      // Reto1.2
      $query->leftJoin('core.registro_fase_usuario as rfu_reto12', function($join){
        $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto12.codigo_registro_convocatoria')
          ->where('rfu_reto12.codigo_fase', '=', 150);
      });
      $query->leftJoin('core.asignacion_jurado', function($join) {
        $join->on('rfu_reto12.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario')
          ->where('asignacion_jurado.codigo_estado', '!=', Estado::getState('removed'));
      });
      $query->leftJoin('core.resultado_evaluacion_emprendimiento', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
      $query->leftJoin('seguridad.usuario as uev', 'asignacion_jurado.codigo_usuario', '=', 'uev.codigo_usuario');
      $query->leftJoin('core.persona as puev', 'uev.codigo_usuario', '=', 'puev.codigo_usuario');
      // Reto 2
      $query->leftJoin('core.registro_fase_usuario as rfu_reto2', function($join){
        $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto2.codigo_registro_convocatoria')
          ->where('rfu_reto2.codigo_fase', '=', 137);
      });
      // Reto 3
      $query->leftJoin('core.registro_fase_usuario as rfu_reto3', function($join){
        $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto3.codigo_registro_convocatoria')
          ->where('rfu_reto3.codigo_fase', '=', 138);
      });
      $query->leftJoin('core.carta_compormiso_compra', 'rfu_reto3.codigo_registro_fase_usuario', '=', 'carta_compormiso_compra.codigo_registro_fase_usuario');
      $query->leftJoin('core.ciudad as ciudadrepresentante', 'carta_compormiso_compra.codigo_ciudad', '=', 'ciudadrepresentante.codigo_ciudad');
      $query->leftJoin('core.detalle_voucher', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'rfu_reto3.codigo_registro_fase_usuario');
      $query->where('registro_convocatoria.codigo_convocatoria', '=', 113);
      $query->where('registro_convocatoria.codigo_estado', '!=', Estado::getState('removed'));      
      $query->orderBy('fecha_registro_convocatoria', 'asc');
      $query->orderBy('hora_registro_convocatoria','asc');
      $data = $query->get();
      \Log::info("termino consulta \n");
      // Prepare file
      $filename = sprintf('%s_%s.xlsx', 'reporte_unico', date('YmdHms'));
      $filepath = sprintf('reports/%s/%s/%s', Institucion::institution('innpulsa'), $user->codigo_usuario, $filename);
      \Log::info("empezo excel \n");
      $file = Excel::create($filename, function($excel) use($data) {
        // Set sheet alignement
        $excel->getDefaultStyle()
          ->getAlignment()
          ->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->sheet('Reporte único', function($sheet)  use($data) {
          // One level titles
          $sheet->appendRow([
            'TRANSVERSAL','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','RETO 1 - RETO 4','','','','','','','','','','','','','','','','RETO 2 - RETO 4','','','','','','','','','','','','','','','','','','','','RETO 2 - RETO 3 - RETO 4','','','','','TRANSVERSAL','RETO 3','','','','','','','RETO 4','','','','',
          ]);
          $sheet->mergeCells('A1:AU1');
          $sheet->mergeCells('AV1:BK1');
          $sheet->mergeCells('BL1:CE1');
          $sheet->mergeCells('CF1:CJ1');
          $sheet->mergeCells('CL1:CR1');
          $sheet->mergeCells('CS1:DA1');
          $sheet->cells('A1:AU1', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setAlignment('center');
          });
          $sheet->cell('DB1', function($cell) {
            $cell->setBorder('none', 'none', 'none', 'thin');
          });
          $sheet->cells('AV1:BK1', function($cells) {
            $cells->setBackground('#F5D0A9');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('BL1:CE1', function($cells) {
            $cells->setBackground('#F5BCA9');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CF1:CJ1', function($cells) {
            $cells->setBackground('#A9A9F5');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CK1', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CL1:CR1', function($cells) {
            $cells->setBackground('#9FF781');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CS1:DA1', function($cells) {
            $cells->setBackground('#81BEF7');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          // Two level titles
          $sheet->appendRow([
            '','','','','','','','',
            'Emprendedor (No tiene empresa legalmente constituida)',
            '','','','','','','','','','','','','',
            'Persona Jurídica',
            '','','','','','','','','','','','','','','','','','','','','','','','',
            '','','','','','','','','','','','','','','',
            // RETO 2 - RETO 4
            '','','','','','','','','','','','','','','','','','','','',
            // RETO 2 - RETO 3 - RETO 4
            '','','','','',
            // TRANSVERSAL
            '',
            // RETO 3
            '','','','','','','',
            // RETO 4
            '','','','','','','','','',
          ]);
          $sheet->mergeCells('I2:V2');
          $sheet->mergeCells('W2:AQ2');
          $sheet->setBorder('W2:AQ2', 'thin');
          $sheet->cells('A2:AU2', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setAlignment('center');
          });
          $sheet->cells('I2:V2', function($cells) {
            $cells->setBorder('thin', 'thin', 'none', 'thin');
          });
          $sheet->cells('W2', function ($cells) {
            $cells->setBorder('none', 'none', 'none', 'thin');
          });
          $sheet->cells('AI2:AO2', function($cells) {
            $cells->setBorder('none', 'none', 'thin', 'thin');
          });
          $sheet->cells('AV2:BK2', function($cells) {
            $cells->setBackground('#F5D0A9');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('AV2:BK2', function($cells) {
            $cells->setBackground('#F5D0A9');
            $cells->setAlignment('center');
          });
          $sheet->cells('CS2:DA2', function($cells) {
            $cells->setBackground('#81BEF7');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('BL2:CE2', function($cells) {
            $cells->setBackground('#F5BCA9');
            $cells->setAlignment('center');
          });
          $sheet->cells('CL2:CR2', function($cells) {
            $cells->setBackground('#9FF781');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CF2:CJ2', function($cells) {
            $cells->setBackground('#A9A9F5');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CK2', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'thin', 'none', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CL2:CR2', function($cells) {
            $cells->setBackground('#9FF781');
            $cells->setAlignment('center');
          });
          $sheet->cells('CS2:DA2', function($cells) {
            $cells->setBackground('#81BEF7');
            $cells->setAlignment('center');
          });
          // Three level titles
          $sheet->appendRow([
            '','','','','','','','',
            '','','','','','','','','','','','','','',
            // Datos de Representante Legal
            'Datos de Representante Legal',
            '','','','','','','','','','','',
            'Datos de Contacto','','','','','','',
            //'Razón Social','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
          ]);
          $sheet->setBorder('AI3:AO3', 'thin');
          $sheet->mergeCells('W3:AH3');
          $sheet->mergeCells('AI3:AO3');
          $sheet->cells('W3:AH3', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('BL3:CE3', function($cells) {
            $cells->setBackground('#F5BCA9');
            $cells->setAlignment('center');
          });
          $sheet->cells('CS3:DA3', function($cells) {
            $cells->setBackground('#81BEF7');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('AR3:AU3', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CK3', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CF3:CJ3', function($cells) {
            $cells->setBackground('#A9A9F5');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('CL3:CR3', function($cells) {
            $cells->setBackground('#9FF781');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('AV3:BK3', function($cells) {
            $cells->setBackground('#F5D0A9');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('AP3:AQ3', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'none', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('AI3:AO3', function($cells) {
            $cells->setBorder('thin', 'none', 'thin', 'thin');
            $cells->setBackground('#A9F5F2');
            $cells->setAlignment('center');
          });
          $sheet->cells('I3:V3', function($cells) {
            $cells->setBackground('#A9F5F2');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          // Three level titles
          $sheet->appendRow([
            'Código Secuencial',
            'Fecha de Postulación/Registro',
            'Tienes empresa legalmente constituida (SI/NO)',
            'Tipo de Postulación (Persona Natural / Persona Jurídica) Proyecto-Empresa',
            'Nombre proyecto/Empresa',
            'Ciudad del emprendimiento',
            'Departamento del emprendimiento',
            'Región',
            'Género',
            'Nombres',
            'Primer Apellido',
            'Segundo Apellido',
            'Tipo de Documento',
            'No. Identificación',
            'Lugar de Expedición',
            'Número de Teléfono',
            'Número de Celular',
            'Correo Electrónico',
            'Ciudad de Residencia',
            'Departamento',
            'Dirección de Correspondencia',
            'Código Postal',
            'Nombres Completos',
            'Apellidos Completos',
            'Tipo de Documento',
            'No. Identificación',
            'Lugar de Expedición',
            'Número de Teléfono',
            'Número de Celular',
            'Correo Electrónico',
            'Ciudad de Residencia',
            'Departamento',
            'Dirección de Domicilio',
            'Nombres',
            'Primer Apellido',
            'Segundo Apellido',
            'Número de Teléfono',
            'Número Celular',
            'Correo Electrónico',
            'Dirección de Correspondencia',
            'Codigo Postal',
            'NIT',
            'Razón Social',
            'CIIU',
            'Actividad Económica',
            'Sector Económico o Industria',
            'Nivel de estudio del Emprendedor o Empresario',
            // RETO 1 - RETO 4
            'Rango Evaluacion Reto 1',
            'Puntaje Inicial Reto 1',
            'Puntaje Criterio 1 Reto 1',
            'Puntaje Criterio 2 Reto 1',
            'Puntaje Criterio 3 Reto 1',
            'Puntaje Criterio 4 Reto 1',
            'Total Puntaje Final Reto 1',
            'Rango Inferior Reto 1.2',
            'Rango Superior Reto 1.2',
            'Sector Económico o Industria Ajustado por Evaluador',
            'Observaciones Evaluación Reto 1',
            'Nombre del Evaluador Reto 1.2',
            'Fecha de asignación de la postulación al evaluador',
            'Fecha Evaluación Reto 1.2',
            'Fecha de Notificación',
            'Estado final Reto 1.2',
            // RETO 2 - RETO 4
            'Fecha del Conversatorio',
            'Lugar del conversatorio',
            'Experto 1. Inversionista',
            'Experto 2. Desarrollo de Negocios',
            'Experto 3. Experto Sectorial',
            'General',
            'Puntaje Criterio 1 Reto 2',
            'Puntaje Criterio 2 Reto 2',
            'Puntaje Criterio 3 Reto 2',
            'Puntaje Criterio 4 Reto 2',
            'Puntaje Final Reto 2',
            'Recomendaciones / Comentarios de Líder Reto 2 a Reto 4',
            'Fecha Notificación Reto 2',
            'Número de Notificación',
            'Recomendación 1',
            'Recomendación 2',
            'Recomendación 3',
            'Recomendación 4',
            'Recomendación 5',
            'Recomendación 6',
            // RETO 2 - RETO 3 - RETO 4
            'Valor de la Llave en Números',
            'Valor de la Llave en Letras',
            'Categoría de Servicio  1',
            'Categoría de Servicio  2',
            'Categoría de Servicio  3',
            // TRANSVERSAL
            'Estado del Empresario',
            // RETO 3
            'Fecha de aceptación carta de compromiso',
            'Estado de la llave',
            'Número de acuerdos suscritos (validados por iNNpulsa Colombia)',
            'Valor de los acuerdos suscritos (Validados por iNNpulsa Colombia)',
            'Valor recursos liberados',
            'Fecha de finalización servicios',
            'Valor total de los servicios pagados',
            // RETO 4
            '¿Cómo genera ingresos tu empresa o negocio?',
            'Tu empresa o emprendimiento cuenta con',
            'Describe tu producto (bien o servicio) innovador',
            'Tu producto o servicio compite y gana en el mercado debido a',
            '¿Cuál es el estado de desarrollo del producto(bien o servicio), proceso o modelo?',
            'Tu negocio cuenta con un equipo emprendedor o directivo conformado por los siguientes perfiles:',
            'Indica el número de empleados formales de tu empresa o emprendimiento',
            '¿De las ventas de este año, cúanto ha ganado la empresa?',
            'Indica las ventas específicas de TU PRODUCTO (bien o servicio innovador)  en pesos colombianos del año en curso'
          ]);
          $sheet->setBorder('AR4:AU4', 'thin');
          $sheet->setBorder('A4:H4', 'thin');
          $sheet->setBorder('I4:V4', 'thin');
          $sheet->setBorder('W4:AO4', 'thin');
          $sheet->setBorder('AP4:AQ4', 'thin');
          $sheet->setBorder('AV4:BK4', 'thin');
          $sheet->setBorder('BL4:CE4', 'thin');
          $sheet->setBorder('CF4:CJ4', 'thin');
          $sheet->setBorder('CK4', 'thin');
          $sheet->setBorder('CL4:CR4', 'thin');
          $sheet->setBorder('CS4:DA4', 'thin');
          $sheet->cells('A3:H3', function ($cells) {
            $cells->setBorder('none', 'none', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
          });
          $sheet->cells('AP4:AQ4', function ($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
          });
          $sheet->cells('BL4:CE4', function($cells) {
            $cells->setBackground('#F5BCA9');
            $cells->setBorder('thin', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('AR4:AU4', function($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
            $cells->setAlignment('center');
          });
          $sheet->cells('CS4:DA4', function($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#81BEF7');
            $cells->setAlignment('center');
          });
          $sheet->cells('CL4:CR4', function($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#9FF781');
            $cells->setAlignment('center');
          });
          $sheet->cells('CF4:CJ4', function($cells) {
            $cells->setBackground('#A9A9F5');
            $cells->setBorder('none', 'thin', 'thin', 'thin');
            $cells->setAlignment('center');
          });
          $sheet->cells('I4:V4', function ($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
          });
          $sheet->cells('AV4:BK4', function($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#F5D0A9');
            $cells->setAlignment('center');
          });
          $sheet->cells('A4:H4', function ($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
          });
          $sheet->cells('CK4', function($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
            $cells->setAlignment('center');
          });
          $sheet->cells('W4:AO4', function($cells) {
            $cells->setBorder('none', 'thin', 'thin', 'none');
            $cells->setBackground('#A9F5F2');
            $cells->setAlignment('center');
          });
          foreach ($data as $value) {
            $categorias_marketplace = []; // explode(',', $value->categorias_marketplace)
            if (isset($value->register_phase_2)) {
              foreach ($this->getCategoriesSelected($value->register_phase_2) as $category) {
                $categorias_marketplace[] = $category->nombre_cat_padre_producto;
              }
            }
            $ciiu = str_pad($value->codigo_ciiu,4,'0',STR_PAD_LEFT).'-'.$value->ciiu;
            $sheet->appendRow([
              $value->numero_registro_convocatoria,
              $value->fecha_registro_convocatoria . " " .$value->hora_registro_convocatoria,
              $value->empresa_constituida,
              $value->nombre_clase_emprendimiento,
              $value->nombre_proyecto,
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
              $value->codigo_postal_usuario,
              // TRANSVERSAL
              $value->nombre_representante_legal,
              $value->apellidos_representante_legal,
              '',
              $value->identificacion_rep_legal,
              $value->ciudad_expedicion_epresentante,
              '',
              '',
              '',
              '',
              '',
              '',
              $value->nombre_usuario,
              $value->primer_apellido_persona,
              $value->segundo_apellido_persona,
              $value->telefono_usuario,
              $value->movil_usuario,
              $value->correo_electronico,
              $value->direccion_usuario ,
              $value->codigo_postal_usuario,
              $value->identificacion_usuario,
              $value->nombre_proyecto,
              $ciiu,
              $value->actividad_economica,
              $value->nombre_categoria_proyecto,
              $value->nivel_estudio_usuario,
              // RETO 1 - RETO 4
              $value->rango_reto1,
              $value->puntaje_total_encuesta,
              $value->average_differential,
              $value->average_commercial,
              $value->validation_commercial,
              $value->average_work_team,
              $value->puntaje_total_eval_emprendi,
              $value->rango_inferior_reto12,
              $value->rango_superior_reto12,
              $value->nombre_categoria_proyecto_evaluador,
              $value->comentario_resultado_evaluacion,
              $value->nombre_evaluador,
              $value->fecha_asignacion_jurado,
              $value->fecha_resultado_evaluacion,
              $value->notificacion_cambio_registro_fase,
              $value->result_reto12,
              // RETO 2 - RETO 4
              $value->fecha_conversatorio,
              $value->lugar_conversatorio,
              $value->expert_investor,
              $value->expert_business,
              $value->sector_expert,
              $value->observaciones_registro_fase_usuario,
              $value->enterprising_team,
              $value->market,
              $value->product,
              $value->productive_chains,
              !is_null($value->crfu_reto2) ? round( (($value->enterprising_team / 100) * 40) + (($value->market / 100) * 25) + (($value->product / 100) * 25) + (($value->productive_chains / 100) * 10) ) : '',
              $value->observaciones_registro_fase_usuario,
              $value->notificacion_cambio_registro_fase2,
              '',
              '',
              '',
              '',
              '',
              '',
              '',
              // RETO 2 - RETO 3 - RETO 4
              (isset($value->llaves_de_acceso) && !empty($value->llaves_de_acceso) && $value->llaves_de_acceso > 0) ? $value->llaves_de_acceso : '',
              (isset($value->llaves_de_acceso) && !empty($value->llaves_de_acceso) && $value->llaves_de_acceso > 0) ? \NumeroALetras::convertir($value->llaves_de_acceso) : '',
              isset($categorias_marketplace[0]) ? $categorias_marketplace[0] : '',
              isset($categorias_marketplace[1]) ? $categorias_marketplace[1] : '',
              isset($categorias_marketplace[2]) ? $categorias_marketplace[2] : '',
              // TRANSVERSAL
              $value->nombre_estado,
              // RETO 3 - RETO 4
              $value->fecha_aceptacion_carta_compormiso_compra,
              (($value->llaves_de_acceso > 0 && $value->llaves_de_acceso == $value->valor_acuerdos_validados_innpulsa) ? 'CERRADA' : 'PENDIENTE'),
              $value->acuerdos_validados_innpulsa,
              $value->valor_acuerdos_validados_innpulsa,
              '',
              $value->fecha_final_actividades_acuerdo,
              '',
              // RETO 4
              $value->como_genera_ingreso_empresa,
              $value->empresa_cuenta_con,
              $value->describe_producto,
              $value->producto_compite_gana,
              $value->estado_desarrollo_producto,
              $value->perfil_equipo_emprendedor,
              $value->cantidad_empleados,
              $value->ventas_actuales_negocio,
              $value->ventas_actuales_producto
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
      $data['_inst'] = Institucion::institution('innpulsa');
      $data['_url'] = $fileurl;
      SendEmail::notification($data);
      \Log::info("envio correo \n");
    }catch(\Exception $e) {
      \Log::error(sprintf('%s:%s', 'ReportUnicoInnpulsa:Command', $e->getMessage()));
      abort(500);
    }
  }


  public function getCategoriesSelected($register){
    $categories = [];
    $query = CategoriaProducto::query();
    $query->select('cat_padre_producto.nombre_cat_padre_producto');
    $query->join('core.categoria_producto_insti', function($join) {
      $join->on('categoria_producto.codigo_categoria_producto', '=', 'categoria_producto_insti.codigo_categoria_producto')
        ->where('categoria_producto_insti.codigo_institucion', '=', Institucion::institution('innpulsa'))
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
}