<?php
namespace App\Console\Commands\Innpulsa;
use Illuminate\Console\Command;
use App\Models\Seguridad\Usuario, App\Models\Param\Institucion, App\Models\Core\RegistroConvocatoria, App\Models\Core\Estado;
use DB, Excel, Storage, SendEmail;

class ReportEmpodera extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'empodera:report {user}';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reporte seguimiento unico iNNpulsa Empodera';
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
			$user = Usuario::find($this->argument('user'));
			if(!$user instanceof Usuario) {
				throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
			}
			$query = RegistroConvocatoria::query();
			$query->select('registro_convocatoria.numero_registro_convocatoria', 'rfu_reto2.codigo_registro_fase_usuario as crfu_reto2', 'rfu_reto2.valor_asignacion_rfu as llaves_de_acceso', 'fecha_registro_convocatoria', 'hora_registro_convocatoria', 'ue.nombre_usuario as nombre_proyecto', 'uo.nombre_usuario', 'persona.genero_persona', 'ue.identificacion_usuario', 'uo.correo_electronico', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'categoria_proyecto.nombre_categoria_proyecto', 'ce.nombre_categoria_proyecto as nombre_categoria_proyecto_evaluador', 'usuario_encuesta.puntaje_total_encuesta', 'resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion', 'resultado_evaluacion_emprendimiento.fecha_resultado_evaluacion', 'asignacion_jurado.fecha_asignacion_jurado', 'rfu_reto1.rango_inferior_eval_fase as rango_reto1', 'rfu_reto12.rango_inferior_eval_fase as rango_inferior_reto12', 'rfu_reto12.rango_superior_eval_fase as rango_superior_reto12',
				DB::raw("(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN rfu_reto12.notificacion_cambio_registro_fase ELSE rfu_reto1.notificacion_cambio_registro_fase END) as notificacion_cambio_registro_fase"), 'rfu_reto2.notificacion_cambio_registro_fase as notificacion_cambio_registro_fase2', 'rfu_reto2.observaciones_registro_fase_usuario', 'nombre_clase_emprendimiento',  'ciudad.region_ciudad',
				DB::raw("trim(initcap(ciudad.distrito_ciudad)) as departamento_usuario"),
				DB::raw("trim(initcap(ciudad.nombre_ciudad)) as ciudad_usuario"),
				DB::raw("trim(initcap(cue.nombre_ciudad)) as ciudad_usuario_entidad"),
				DB::raw("trim(initcap(cue.distrito_ciudad)) as departamento_usuario_entidad"),
				DB::raw("(uev.nombre_usuario || ' ' || COALESCE(puev.primer_apellido_persona, '') || ' ' || COALESCE(puev.segundo_apellido_persona, '')) as nombre_evaluador"),
				DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 2794 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as tipo_documento"),
				DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 2795 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as numero_documento"),
				DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 2797 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as telefono_usuario"),
				DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 2798 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as movil_usuario"),
				DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 2800 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as direccion_usuario"),
				DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 2801 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_postal_usuario"),
				DB::raw("(SELECT nombre_ciudad FROM core.respuesta_encuesta as pgencu JOIN core.ciudad ON codigo_ciudad = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 2892 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as lugar_expedicion_documento"),
				DB::raw("(SELECT codigo_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 2805 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_ciiu"),
				DB::raw("(SELECT nombre_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 2805 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciiu"),
				DB::raw("(SELECT nombre_sector_economico FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer JOIN core.sector_economico ON sector_interes.codigo_sector_economico = sector_economico.codigo_sector_economico WHERE pgencu.codigo_pregunta_encuesta = 2805 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as actividad_economica"),
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
                                    WHERE pregunta_encuesta.codigo_encuesta = 92
                                    AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                                    AND pregunta_encuesta.codigo_pregunta_encuesta in (2809, 2821, 2814, 2831)
                                    AND respuesta_encuesta.codigo_estado = 8
                                ) AS scoring
                                FROM core.item_evaluacion_emprendimiento
                                LEFT JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                                LEFT JOIN core.resultado_evaluacion_emprendimiento ON resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion = evaluacion_item_emprendimiento.codigo_resultado_evaluacion
                                LEFT JOIN core.asignacion_jurado ON resultado_evaluacion_emprendimiento.codigo_asignacion_jurado = asignacion_jurado.codigo_asignacion_jurado
                                WHERE
                                item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18 AND codigo_grupo_item_eval = 21
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
                                            WHEN 2878
                                            THEN (
                                                (CASE WHEN (pregunta_encuesta.codigo_pregunta_encuesta = 2878 and respuesta_encuesta.codigo_opcion_encuesta = 4086)
                                                    THEN ( CASE
                                                        WHEN (
                                                            (SELECT COALESCE(r1260.texto_respuesta_encuesta::double precision, 0)
                                                                FROM core.respuesta_encuesta as r1260
                                                                WHERE r1260.codigo_usuario_encuesta = respuesta_encuesta.codigo_usuario_encuesta
                                                                AND r1260.codigo_pregunta_encuesta = 2877
                                                            ) >= 300000
                                                        )
                                                    THEN 7.5 ELSE -7.5 END)
                                                ELSE 0 END)
                                            )
                                            ELSE puntaje_respuesta_encuesta
                                        END))
                                    FROM core.respuesta_encuesta
                                    INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
                                    WHERE pregunta_encuesta.codigo_encuesta = 92 AND respuesta_encuesta.codigo_estado = 8
                                    AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                                    AND pregunta_encuesta.codigo_pregunta_encuesta IN (2878, 2829, 2825, 2823) )
                                ) AS scoring
                                FROM core.item_evaluacion_emprendimiento
                                LEFT JOIN core.evaluacion_item_emprendimiento ON evaluacion_item_emprendimiento.codigo_item_evaluacion = item_evaluacion_emprendimiento.codigo_item_evaluacion
                                LEFT JOIN core.resultado_evaluacion_emprendimiento ON resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion = evaluacion_item_emprendimiento.codigo_resultado_evaluacion
                                LEFT JOIN core.asignacion_jurado ON resultado_evaluacion_emprendimiento.codigo_asignacion_jurado = asignacion_jurado.codigo_asignacion_jurado
                                WHERE
                                item_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18 AND codigo_grupo_item_eval = 20
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
                        WHERE pregunta_encuesta.codigo_encuesta = 92 AND respuesta_encuesta.codigo_estado = 8
                        AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                        AND pregunta_encuesta.codigo_pregunta_encuesta IN (2815, 2817, 2878) )
                    ELSE ''
                END ) as validation_commercial"),
				DB::raw("
                (CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
                    (SELECT SUM(puntaje_respuesta_encuesta)::text
                        FROM core.respuesta_encuesta
                        INNER JOIN core.pregunta_encuesta ON respuesta_encuesta.codigo_pregunta_encuesta = pregunta_encuesta.codigo_pregunta_encuesta
                        WHERE pregunta_encuesta.codigo_encuesta = 92 AND respuesta_encuesta.codigo_estado = 8
                        AND respuesta_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta
                        AND pregunta_encuesta.codigo_pregunta_encuesta IN (2832, 2834) )
                    ELSE ''
                END ) as average_work_team"),
				DB::raw("
					(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
						(SELECT  ev.puntaje_resul_eval_empren::text
						FROM core.item_evaluacion_emprendimiento as item
						INNER JOIN core.evaluacion_item_emprendimiento as ev
						ON item.codigo_item_evaluacion = ev.codigo_item_evaluacion
						INNER JOIN core.resultado_evaluacion_emprendimiento as re
						ON ev.codigo_resultado_evaluacion = re.codigo_resultado_evaluacion
						INNER JOIN core.asignacion_jurado as asig_ju
						ON re.codigo_asignacion_jurado = asig_ju.codigo_asignacion_jurado 
						WHERE item.codigo_evaluacion_emprendimiento = 18
						AND item.codigo_grupo_item_eval = 20
						AND asig_ju.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario)
					ELSE ''
					END ) as result_product_diff"),
				DB::raw("
					(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
						(SELECT ev.puntaje_resul_eval_empren::text
						FROM core.item_evaluacion_emprendimiento as item
						INNER JOIN core.evaluacion_item_emprendimiento as ev
						ON item.codigo_item_evaluacion = ev.codigo_item_evaluacion
						INNER JOIN core.resultado_evaluacion_emprendimiento as re
						ON ev.codigo_resultado_evaluacion = re.codigo_resultado_evaluacion
						INNER JOIN core.asignacion_jurado as asig_ju
						ON re.codigo_asignacion_jurado = asig_ju.codigo_asignacion_jurado 
						WHERE item.codigo_evaluacion_emprendimiento = 18
						AND item.codigo_grup_opc_eval_pregunta = 60
						AND asig_ju.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario)
					ELSE ''
					END ) as result_profile_client"),
				DB::raw("
					(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
						(SELECT ev.puntaje_resul_eval_empren::text
						FROM core.item_evaluacion_emprendimiento as item
						INNER JOIN core.evaluacion_item_emprendimiento as ev
						ON item.codigo_item_evaluacion = ev.codigo_item_evaluacion
						INNER JOIN core.resultado_evaluacion_emprendimiento as re
						ON ev.codigo_resultado_evaluacion = re.codigo_resultado_evaluacion
						INNER JOIN core.asignacion_jurado as asig_ju
						ON re.codigo_asignacion_jurado = asig_ju.codigo_asignacion_jurado 
						WHERE item.codigo_evaluacion_emprendimiento = 18
						AND item.codigo_grup_opc_eval_pregunta = 61
						AND asig_ju.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario)
					ELSE ''
					END ) as result_best_of_competency"),
				DB::raw("
					(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
						(SELECT ev.puntaje_resul_eval_empren::text
						FROM core.item_evaluacion_emprendimiento as item
						INNER JOIN core.evaluacion_item_emprendimiento as ev
						ON item.codigo_item_evaluacion = ev.codigo_item_evaluacion
						INNER JOIN core.resultado_evaluacion_emprendimiento as re
						ON ev.codigo_resultado_evaluacion = re.codigo_resultado_evaluacion
						INNER JOIN core.asignacion_jurado as asig_ju
						ON re.codigo_asignacion_jurado = asig_ju.codigo_asignacion_jurado 
						WHERE item.codigo_evaluacion_emprendimiento = 18
						AND item.codigo_grup_opc_eval_pregunta = 62
						AND asig_ju.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario)
					ELSE ''
					END ) as result_market"),
				DB::raw("
					(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
						(SELECT ev.puntaje_resul_eval_empren::text
						FROM core.item_evaluacion_emprendimiento as item
						INNER JOIN core.evaluacion_item_emprendimiento as ev
						ON item.codigo_item_evaluacion = ev.codigo_item_evaluacion
						INNER JOIN core.resultado_evaluacion_emprendimiento as re
						ON ev.codigo_resultado_evaluacion = re.codigo_resultado_evaluacion
						INNER JOIN core.asignacion_jurado as asig_ju
						ON re.codigo_asignacion_jurado = asig_ju.codigo_asignacion_jurado 
						WHERE item.codigo_evaluacion_emprendimiento = 18
						AND item.codigo_grup_opc_eval_pregunta = 63
						AND asig_ju.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario)
					ELSE ''
					END ) as sales_projected"),
				DB::raw("
					(CASE WHEN rfu_reto12.codigo_registro_fase_usuario IS NOT NULL THEN
						(SELECT re.puntaje_total_eval_emprendi::text
						FROM core.item_evaluacion_emprendimiento as item
						INNER JOIN core.evaluacion_item_emprendimiento as ev
						ON item.codigo_item_evaluacion = ev.codigo_item_evaluacion
						INNER JOIN core.resultado_evaluacion_emprendimiento as re
						ON ev.codigo_resultado_evaluacion = re.codigo_resultado_evaluacion
						INNER JOIN core.asignacion_jurado as asig_ju
						ON re.codigo_asignacion_jurado = asig_ju.codigo_asignacion_jurado 
						WHERE item.codigo_evaluacion_emprendimiento = 18
						AND item.codigo_grup_opc_eval_pregunta = 63
						AND asig_ju.codigo_registro_fase_usuario = rfu_reto12.codigo_registro_fase_usuario)
					ELSE ''
					END ) as total_evaluation"),
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
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 8
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
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 19
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 25
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
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 19
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 26
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
                            AND revem_reto2.codigo_evaluacion_emprendimiento = 19
                            AND item_evaluacion_emprendimiento.codigo_grupo_item_eval = 27
                        )
                    ELSE null
                END )
                AS productive_chains"),
				DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT STRING_AGG(categoria_producto.nombre_categoria_producto::text, ',') FROM core.recom_producto_eval
                            JOIN core.categoria_producto ON recom_producto_eval.codigo_categoria_producto = categoria_producto.codigo_categoria_producto
                            WHERE rfu_reto2.codigo_registro_fase_usuario = recom_producto_eval.codigo_registro_fase_usuario
                        )
                    ELSE null
                END )
                AS categorias_marketplace")
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
			$query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
			$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
			$query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
			$query->join('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
			$query->leftJoin('core.categoria_proyecto as ce', 'dato_entidad.cor_codigo_categoria_proyecto', '=', 'ce.codigo_categoria_proyecto');
			$query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');
			// Reto1
			$query->join('core.registro_fase_usuario as rfu_reto1', function($join){
				$join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto1.codigo_registro_convocatoria')
					->where('rfu_reto1.codigo_fase', '=', 204);
			});
			// Reto1.2

			$query->leftJoin('core.registro_fase_usuario as rfu_reto12', function($join){
				$join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto12.codigo_registro_convocatoria')
					->where('rfu_reto12.codigo_fase', '=', 212);
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
					->where('rfu_reto2.codigo_fase', '=', 213);
			});
			$query->where('registro_convocatoria.codigo_estado', '!=', Estado::getState('removed'));
			$query->where('registro_convocatoria.codigo_convocatoria', '=', 198);
			$query->orderBy('fecha_registro_convocatoria','asc');
			$query->orderBy('hora_registro_convocatoria','asc');
			$data = $query->get();
			$filename = sprintf('%s_%s.xlsx', 'reporte_unico_empodera', date('YmdHms'));
			$filepath = sprintf('reports/%s/%s/%s', Institucion::institution('innpulsa'), $user->codigo_usuario, $filename);
			$file = Excel::create($filename, function($excel) use($data) {
				$excel->sheet('Reporte único', function($sheet)  use($data) {
					$sheet->setAllBorders('thin');
					$sheet->getStyle('AK:AP', $sheet->getHighestRow())->getAlignment()->setWrapText(true);
					// One level titles
					$sheet->appendRow([
						'TRANSVERSAL','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','RETO 1 - RETO 4','','','','','','','','','','','','','','','','','','',''
					]);
					$sheet->mergeCells('A1:AG1');
					$sheet->mergeCells('AH1:BA1');
					$sheet->cells('A1:AT1', function($cells) {
						$cells->setBackground('#A9F5F2');
						$cells->setAlignment('center');
					});
					$sheet->cells('AH1:AT1', function($cells) {
						$cells->setBackground('#F5D0A9');
						$cells->setAlignment('center');
					});
					// Two level titles
					$sheet->appendRow([
						'Código Secuencial',
						'Fecha de Postulación/Registro',
						'Tienes empresa legalmente constituida (SI/NO)',
						'Tipo de Postulación (Persona Natural / Persona Jurídica) Proyecto-Empresa',
						'Nombre proyecto/Empresa',
						'Ciudad del emprendimiento',
						'Departamento del emprendimiento',
						'Region',
						'Emprendedor (No tiene empresa legalmente constituida)',
						'','','','','','','','','','','','','',
						'Persona Jurídica',
						'','','','','','','','',
						'CIIU',
						'Actividad Económica',
						'Sector Económico o Industria',
						'Rango Evaluacion Reto 1',
						'Puntaje Inicial Reto 1',
						'Producto con diferencial (opción que selecciona el evaluador)',
						'¿Cuál es el perfil de tu cliente? (opción que selecciona el evaluador)',
						'¿Qué haces mejor que tu competencia? ¿En qué se diferencia lo que estás proponiendo frente a lo existente en el mercado? (opción que selecciona el evaluador)',
						'¿Cuál es el tamaño de mercado objetivo al que puede llegar tu producto (bien o servicio)? ¿Cuántos clientes, cuánto dinero, eso representa qué porcentaje del total? (opción que selecciona el evaluador)',
						'Ventas proyectadas (opción que selecciona el evaluador)',
						'Total Puntaje Final Reto 1
						(opción que selecciona el evaluador)',
						'Observaciones Evaluación Reto 1',
						'Nombre del Evaluador Reto 1.2',
						'Fecha de asignación de la postulación al evaluador',
						'Fecha Evaluación Reto 1.2',
					]);
					$sheet->mergeCells('I2:V2');
					$sheet->mergeCells('W2:AE2');
					$sheet->setWidth('AK', 50);
					$sheet->setWidth('AE', 20);
					$sheet->setWidth('AL', 50);
					$sheet->setWidth('AM', 60);
					$sheet->setWidth('AN', 80);
					$sheet->setWidth('AO', 50);
					$sheet->setWidth('AP', 80);
					$sheet->cells('A2:AT2', function($cells) {
						$cells->setBackground('#A9F5F2');
						$cells->setAlignment('center');
					});
					$sheet->cells('AH2:AT2', function($cells) {
						$cells->setBackground('#F5D0A9');
						$cells->setAlignment('center');
					});
					// Three level titles
					$sheet->appendRow([
						'','','','','','','','',
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
						'Datos de Contacto', '','','','','','',
						'NIT',
						'Razón Social',
					]);
					$sheet->mergeCells('W3:AC3');
					$sheet->cells('H3:AT3', function($cells) {
						$cells->setBackground('#A9F5F2');
						$cells->setAlignment('center');
					});
					// Headers
					$sheet->appendRow([
						'','','','','','','','','','','','','','','','','','','','','', '',
						'Nombres',
						'Primer Apellido',
						'Segundo Apellido',
						'Número de Teléfono',
						'Número Celular',
						'Correo Electrónico',
						'Dirección de Correspondencia',
						'Codigo Postal',
						'','','','','','',
						// RETO 1 - RETO 4
						'','','','','','','','','','','','','','','',
						// RETO 2 - RETO 4
						'','','','','','','','','','','','','','','','','','','',
						// RETO 2 - RETO 3 - RETO 4
						'','','','','',
						// TRANSVERSAL
						'',
						// RETO 3
						'','','','','','','',
						// RETO 4
						'','','','','','','','','','','',''
					]);
					$sheet->cells('W4:AO4', function($cells) {
						$cells->setBackground('#A9F5F2');
						$cells->setAlignment('center');
					});
					$sheet->setMergeColumn([ 'columns' => ['A','B','C','D','E','F','G', 'H', 'AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA'], 'rows' => [ [2,4] ] ]);
					$sheet->setMergeColumn([ 'columns' => ['I','J','K','L','M','N','O','P','Q','R','S', 'V', 'T','U','AP','AQ', 'AD', 'AE'], 'rows' => [ [3,4] ] ]);
					$sheet->setMergeColumn([ 'columns' => ['AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ'], 'rows' => [ [2,4] ] ]);
					foreach ($data as $value) {
						$ciiu = str_pad($value->codigo_ciiu,4,'0',STR_PAD_LEFT).'-'.$value->ciiu;
						$sheet->appendRow([
							$value->numero_registro_convocatoria,
							"$value->fecha_registro_convocatoria $value->hora_registro_convocatoria",
							$value->empresa_constituida,
							$value->nombre_clase_emprendimiento,
							$value->nombre_proyecto,
							$value->ciudad_usuario_entidad,
							$value->departamento_usuario_entidad,
							$value->region_ciudad,
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
							$value->nombre_usuario,
							$value->primer_apellido_persona,
							$value->segundo_apellido_persona,
							$value->telefono_usuario,
							$value->movil_usuario,
							$value->correo_electronico,
							$value->direccion_usuario,
							$value->identificacion_usuario,
							$value->nombre_proyecto,
							$ciiu,
							$value->actividad_economica,
							$value->nombre_categoria_proyecto,
							$value->rango_reto1,
							$value->puntaje_total_encuesta,
							$value->result_product_diff,
							$value->result_profile_client,
							$value->result_best_of_competency,
							$value->result_market,
							$value->sales_projected,
							$value->total_evaluation,
							$value->comentario_resultado_evaluacion,
							$value->nombre_evaluador,
							$value->fecha_asignacion_jurado,
							$value->fecha_resultado_evaluacion,
						]);
					}
				});
			})->string('xlsx');
			// Store file to s3
			Storage::put($filepath, $file, 'public');
			// File valid
			if(!Storage::has($filepath)) {
				throw new \Exception("Error recuperando xls reporte empodera File($filepath).");
			}
			// Get url
			$fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
			// Send mail
			$data = [];
			$data['_template'] = 'emails.report';
			$data['_subject'] = 'Reporte Empodera';
			$data['_to'] = $user->correo_electronico;
			$data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
			$data['_inst'] = Institucion::institution('innpulsa');
			$data['_url'] = $fileurl;
			SendEmail::notification($data);
		} catch (\Exception $e) {
			\Log::error(sprintf('%s:%s', 'ReportReto3:Command', $e->getMessage()));
			abort(500);
		}
	}
}
