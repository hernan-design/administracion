<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use App\Models\Param\Institucion, App\Models\Seguridad\Usuario, App\Models\Core\RegistroFaseUsuario;

use Custom\Innpulsa;

use Log, Storage, Excel, DB, SendEmail;

class ReportReto3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:reto3 {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte emprendimientos reto 3 Aldea iNNpulsa';

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

            // Prepare data
            $query = RegistroFaseUsuario::query();
            $query->select('numero_registro_convocatoria', 'fecha_registro_convocatoria', 'ue.nombre_usuario as nombre_proyecto', 'nombre_clase_emprendimiento', 'uo.nombre_usuario', 'persona.genero_persona', 'ue.identificacion_usuario', 'uo.identificacion_usuario as codigo_nit', 'uo.correo_electronico', 'uo.telefono_usuario', 'uo.movil_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'categoria_proyecto.nombre_categoria_proyecto', 'carta_compormiso_compra.nombre_representante_legal', 'carta_compormiso_compra.apellidos_representante_legal', 'carta_compormiso_compra.identificacion_rep_legal', 'ciudadrepresentante.nombre_ciudad as ciudad_expedicion_epresentante', 'rfu_reto2.valor_asignacion_rfu as llaves_de_acceso', 'fecha_aceptacion_carta_compormiso_compra', 'fecha_acuerdo_aceptacion_compra', 'fecha_acuerdo_aprobacion_compra', 'fecha_aprobacion_asesor_acuerdo_compra','rfu_reto2.notificacion_cambio_registro_fase as notificacion_cambio_registro_fase2','carta_compormiso_compra.fecha_carta_compormiso_compra', 'acuerdo_compra.fecha_finalizacion_actividades',
                DB::raw("COALESCE(usentity.nombre_usuario, '') as entity_name"), 'usentity.numero_contrato', 'usentity.fecha_inicio_contrato', 'usentity.fecha_fin_contrato', 'producto.nombre_producto', 'nombre_categoria_producto', 'nombre_cat_padre_producto', 'valor_total_compra', 'saldo_detalle_voucher', 'ue.biografia_usuario as descripcion_empresa', 'uo.direccion_usuario as direccion_user', 'rfu_reto2.codigo_registro_fase_usuario as crfu_reto2', 'acuerdo_compra.fecha_radicacion_factura', 'acuerdo_compra.numero_factura', 'acuerdo_compra.fecha_gestion_factura', 'acuerdo_compra.valor_factura', 'estado.nombre_estado', 'ucp.numero_contrato as contrato_asesor', 'ucp.nombre_usuario as asesor_contrato',
                DB::raw("(detalle_voucher.valor_detalle_voucher - detalle_voucher.saldo_detalle_voucher) as valor_total_acuerdos"),
                DB::raw("trim(initcap(ciudad.distrito_ciudad)) as departamento_usuario"),
                DB::raw("trim(initcap(ciudad.nombre_ciudad)) as ciudad_usuario"),
                DB::raw("trim(initcap(cien.distrito_ciudad)) as departamento_empresa"),
                DB::raw("trim(initcap(cien.nombre_ciudad)) as ciudad_empresa"),
                DB::raw("(CASE WHEN saldo_detalle_voucher = 0 THEN 'CERRADO' ELSE 'PENDIENTE' END) AS estado_llaves_acceso"),
                DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1913 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as empresa_constituida"),
                DB::raw("(SELECT opencu.texto_opcion_encuesta FROM core.respuesta_encuesta as pgencu JOIN core.opcion_encuesta as opencu ON opencu.codigo_opcion_encuesta = pgencu.codigo_opcion_encuesta WHERE pgencu.codigo_pregunta_encuesta = 1222 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as tipo_documento"),
                DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1223 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as numero_documento"),
                DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1225 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as telefono_usuario"),
                DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1226 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as movil_usuario"),
                DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1928 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as direccion_usuario"),
                DB::raw("(SELECT pgencu.texto_respuesta_encuesta FROM core.respuesta_encuesta as pgencu WHERE pgencu.codigo_pregunta_encuesta = 1929 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as codigo_postal_usuario"),
                DB::raw("(SELECT nombre_ciudad FROM core.respuesta_encuesta as pgencu JOIN core.ciudad ON codigo_ciudad = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1954 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as lugar_expedicion_documento"),
                DB::raw("(SELECT nombre_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciiu"),
                DB::raw("(SELECT codigo_sector_interes FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as ciiu_numero"),
                DB::raw("(SELECT nombre_sector_economico FROM core.respuesta_encuesta as pgencu JOIN core.sector_interes ON codigo_sector_interes = pgencu.texto_respuesta_encuesta::integer JOIN core.sector_economico ON sector_interes.codigo_sector_economico = sector_economico.codigo_sector_economico WHERE pgencu.codigo_pregunta_encuesta = 1234 and pgencu.codigo_usuario_encuesta = usuario_encuesta.codigo_usuario_encuesta) as actividad_economica"),
                DB::raw("(CASE WHEN evaluacion_compra.tipo_evaluacion_compra = 2 THEN evaluacion_compra.promedio_evaluacion ELSE null END) as promedio_empresario"),
                DB::raw("(CASE WHEN evaluacion_compra.tipo_evaluacion_compra = 1 THEN evaluacion_compra.promedio_evaluacion ELSE null END) as promedio_asesor"),
                DB::raw("
                    (CASE WHEN carta_compormiso_compra.codigo_carta_compormiso_compra IS NOT NULL
                        THEN
                        (
                            SELECT STRING_AGG(carta_institucion_doc.codigo_documento::text, ',') FROM core.carta_institucion_doc WHERE carta_institucion_doc.codigo_carta_compormiso_compra = carta_compormiso_compra.codigo_carta_compormiso_compra AND carta_institucion_doc.codigo_estado = 8
                        )
                    ELSE null END ) AS documentos_carta_compromiso"),
                DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT STRING_AGG(categoria_producto.nombre_categoria_producto::text, ',') FROM core.recom_producto_eval
                            JOIN core.categoria_producto ON recom_producto_eval.codigo_categoria_producto = categoria_producto.codigo_categoria_producto
                            WHERE rfu_reto2.codigo_registro_fase_usuario = recom_producto_eval.codigo_registro_fase_usuario
                        )
                    ELSE null END ) AS categorias_marketplace"),
                DB::raw("
                    (CASE WHEN rfu_reto2.codigo_registro_fase_usuario IS NOT NULL
                        THEN
                        (
                            SELECT MAX(fecha_adjunto_usuario) FROM core.adjunto
                            INNER JOIN core.adjunto_usuario ON adjunto.codigo_adjunto = adjunto_usuario.codigo_adjunto 
                            WHERE ue.codigo_usuario = adjunto_usuario.codigo_usuario
                        )
                    ELSE null END ) AS fecha_documentos"),
                DB::raw("
                    (CASE WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL
                        THEN
                        (
                            SELECT MIN(fecha_inicial_actividad_acuerdo) FROM core.actividad_acuerdo WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra AND codigo_estado = 8
                        )
                    ELSE null END ) AS fecha_inicio_actividades_acuerdo"),
                DB::raw("
                    (CASE WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL
                        THEN
                        (
                            SELECT MAX(fecha_final_actividad_acuerdo) FROM core.actividad_acuerdo WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra AND codigo_estado = 8
                        )
                    ELSE null END ) AS fecha_final_actividades_acuerdo"),
                DB::raw("
                    (CASE WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL
                        THEN
                        (
                            SELECT ceil( ABS( ( extract(EPOCH FROM MAX(fecha_final_actividad_acuerdo)::date ) - extract(EPOCH FROM MIN(fecha_inicial_actividad_acuerdo)::date) ) )/30.43/24/3600) FROM core.actividad_acuerdo WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra AND codigo_estado = 8
                        )
                    ELSE null END ) AS duracion_servicio_meses"),
                DB::raw("
                    (CASE WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL
                        THEN
                        (
                            SELECT STRING_AGG(usuario.correo_electronico::text, ',') FROM core.usuario_acuerdo
                            INNER JOIN core.grupo_usuario ON usuario_acuerdo.codigo_grupo_usuario = grupo_usuario.codigo_grupo_usuario
                            INNER JOIN seguridad.usuario ON grupo_usuario.seg_codigo_usuario = usuario.codigo_usuario
                            WHERE usuario_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra AND usuario_acuerdo.codigo_estado = 8
                        )
                    ELSE null END ) AS equipo_trabajo_acuerdo"),
                DB::raw("
                    (CASE
                        WHEN (carta_compormiso_compra.codigo_estado = 16 AND valor_detalle_voucher = saldo_detalle_voucher) THEN 'CATÁLOGO ENVIADO'
                        WHEN acuerdo_compra.codigo_estado = 8 THEN 'ACUERDO EN REVISIÓN ASESOR EMPRESARIO'
                        WHEN acuerdo_compra.codigo_estado = 6 THEN 'ACUERDO EN REVISIÓN DE INNPULSA'
                        WHEN fecha_acuerdo_aprobacion_compra IS NOT NULL THEN 'ACUERDO FIRMADO'
                        WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL AND ( now() > (SELECT MAX(fecha_final_actividad_acuerdo) FROM core.actividad_acuerdo WHERE actividad_acuerdo.codigo_acuerdo_compra = acuerdo_compra.codigo_acuerdo_compra AND codigo_estado = 8) ) THEN 'SERVICIO FINALIZADO'
                    ELSE null END)  AS estado_acuerdo"),
                DB::raw("
                    (
                        SELECT STRING_AGG(usuario.correo_electronico::text, ',') FROM core.asignacion_marketplace
                        INNER JOIN seguridad.usuario ON asignacion_marketplace.codigo_usuario = usuario.codigo_usuario
                        WHERE asignacion_marketplace.codigo_registro_fase_usuario = registro_fase_usuario.codigo_registro_fase_usuario AND asignacion_marketplace.codigo_estado = 8
                    ) AS responsable_proceso"),
                DB::raw("
                    (CASE WHEN acuerdo_compra.codigo_acuerdo_compra IS NOT NULL
                        THEN
                        (
                            (CASE
                                WHEN (compra.codigo_estado = 29 AND acuerdo_compra.codigo_estado = 29) THEN 'COMPLETOS'
                                WHEN (compra.codigo_estado = 7 AND acuerdo_compra.codigo_estado = 29) THEN 'INCOMPLETOS'
                                WHEN (compra.codigo_estado = 6 AND acuerdo_compra.codigo_estado = 7) THEN 'EN AJUSTES'
                            ELSE null END)
                        )
                    ELSE null END ) AS estado_entregables"),
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
                AS productive_chains")
            );
            $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
            $query->join('core.usuario_encuesta', 'registro_convocatoria.codigo_usuario_encuesta', '=', 'usuario_encuesta.codigo_usuario_encuesta');

            $query->join('seguridad.usuario as ue', 'registro_convocatoria.codigo_usuario', '=', 'ue.codigo_usuario');
            $query->join('core.entidad', 'ue.codigo_usuario', '=', 'entidad.codigo_usuario');
            $query->leftJoin('core.ciudad as cien', 'cien.codigo_ciudad', '=', 'ue.codigo_ciudad');
            $query->join('seguridad.usuario as uo', 'entidad.seg_codigo_usuario', '=', 'uo.codigo_usuario');
            $query->join('core.persona', 'uo.codigo_usuario', '=', 'persona.codigo_usuario');
            $query->join('core.ciudad', 'uo.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
            $query->join('core.usuario_institucion', function($join) {
                $join->on('usuario_institucion.codigo_usuario', '=', 'ue.codigo_usuario')
                    ->where('usuario_institucion.codigo_institucion', '=', Institucion::institution('innpulsa'));
            });

            $query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
            $query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
            $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
            $query->join('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');

            // Reto 2
            $query->join('core.registro_fase_usuario as rfu_reto2', function($join){
                $join->on('registro_convocatoria.codigo_registro_convocatoria', '=', 'rfu_reto2.codigo_registro_convocatoria')
                    ->where('rfu_reto2.codigo_fase', '=', Innpulsa::$stage_challenge_2);
            });

            // Reto 3
            $query->leftJoin('core.carta_compormiso_compra', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'carta_compormiso_compra.codigo_registro_fase_usuario');
            $query->leftJoin('core.ciudad as ciudadrepresentante', 'carta_compormiso_compra.codigo_ciudad', '=', 'ciudadrepresentante.codigo_ciudad');
            $query->leftJoin('core.estado', 'estado.codigo_estado', '=', 'registro_fase_usuario.codigo_estado');

            // Compra
            $query->leftJoin('core.detalle_voucher', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
            $query->leftJoin('core.consumo_voucher', 'detalle_voucher.codigo_detalle_voucher', '=', 'consumo_voucher.codigo_detalle_voucher');
            $query->leftJoin('core.compra', 'consumo_voucher.codigo_compra', '=', 'compra.codigo_compra');
            $query->leftJoin('core.evaluacion_compra', 'evaluacion_compra.codigo_compra', '=', 'compra.codigo_compra');
            $query->leftJoin('core.acuerdo_compra', 'compra.codigo_compra', '=', 'acuerdo_compra.codigo_compra');
            $query->leftJoin('core.producto', 'compra.codigo_producto', '=', 'producto.codigo_producto');
            $query->leftJoin('seguridad.usuario as uc', 'uc.codigo_usuario', '=', 'producto.codigo_usuario');
            $query->leftJoin('seguridad.usuario as ucp', 'ucp.codigo_usuario', '=', 'producto.seg_codigo_usuario');

            $query->leftJoin('core.categoria_producto_insti', function($join) {
                $join->on('producto.codigo_categoria_prod_insti', '=', 'categoria_producto_insti.codigo_categoria_prod_insti')
                    ->where('categoria_producto_insti.codigo_institucion', '=', Institucion::institution('innpulsa'));
            });
            $query->leftJoin('core.categoria_producto', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');

            $query->leftJoin('core.cat_padre_producto', 'categoria_producto_insti.codigo_cat_padre_producto', '=', 'cat_padre_producto.codigo_cat_padre_producto');

            $query->leftJoin('seguridad.usuario as usentity', 'producto.seg_codigo_usuario', '=', 'usentity.codigo_usuario');
            $query->leftJoin('core.entidad as sentity', 'usentity.codigo_usuario', '=', 'sentity.codigo_usuario');

            $query->where('registro_fase_usuario.codigo_fase', '=', Innpulsa::$stage_challenge_3);
            $query->where('registro_convocatoria.codigo_convocatoria', '=', Innpulsa::$challenge_reto1);
            $query->orderBy('fecha_registro_convocatoria','asc');
            $query->orderBy('hora_registro_convocatoria','asc');
            $data = $query->get();

            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'reporte_reto3', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', Institucion::institution('innpulsa'), $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($data) {
                $excel->sheet('Reporte reto 3', function($sheet) use($data) {
                    $sheet->setAllBorders('thin');

                    // One level titles
                    $sheet->appendRow([
                        'DATOS DE CONTACTO','','','','','','','','','','','','','','','','','','RETO 2','','','','','','VINCULACION','','CARTA DE COMPROMISO','','RETO 3','','','','','','','','','','','','','','','','','','FACTURACION',                    ]);

                    $sheet->mergeCells('A1:R1');
                    $sheet->mergeCells('S1:X1');
                    $sheet->mergeCells('Y1:Z1');
                    $sheet->mergeCells('AA1:AB1');
                    $sheet->mergeCells('AC1:AT1');
                    $sheet->mergeCells('AU1:AV1');

                    $sheet->cells('A1:R1', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('S1:X1', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('Y1:Z1', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('AA1:AB1', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('AC1:AU1', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('AV1:AV1', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    // Two level titles
                    $sheet->appendRow([
                        'Código Secuencial',
                        'Fecha de Postulación/Registro',
                        'Tipo de Empresa (Natural o Juridica)',
                        'Nombre Empresa',
                        'NIT',
                        'Descripción de la Empresa',
                        'Sector Económico o Industria',
                        'CIIU Numero',
                        'CIIU Descripcion',
                        'Género Representante Legal',
                        'Nombre del Representante Legal',
                        'No de Cedula',
                        'E-Mail',
                        'Teléfono',
                        'Celular',
                        'Ciudad de Registro de la Empresa',
                        'Departamento de Registro de la Empresa',
                        'Dirección Correspondencia',
                        'Puntaje Final Reto 2',
                        'Fecha de Notificación Reto 2',
                        'Valor de la Llave en Números',
                        'Categoría de Servicio  1',
                        'Categoría de Servicio  2',
                        'Categoría de Servicio  3',
                        'Estado Vinculacion',
                        'Fecha Documentos',
                        'Fecha Envio Carta Compromiso',
                        'Fecha de Aceptación Carta de Compromiso',
                        'No Contrato Asesor',
                        'Nombre Asesor',
                        'NIT Asesor',
                        'Nombre del Servicio',
                        'Categoría de servicio',
                        'Fecha inicio de actividades',
                        'Fecha de finalización del acuerdo',
                        'Nueva Fecha de Finalizacion (Prorrogas)',
                        'Duración del Servicio (Meses)',
                        'Fecha de Aceptación condiciones del acuerdo Asesor',
                        'Fecha de Aceptación condiciones del acuerdo Empresario',
                        'Fecha de Aceptación condiciones del acuerdo iNNpulsa',
                        'Valor del Servcio',
                        'Valor Total de los Acuerdos por empresa',
                        'Diferencia en valor respecto a llave otorgada',
                        'Estado Acuerdo',
                        'Estado Final de llave de acceso',
                        'Fecha Carta de Notificación - Cierre de la Llave',
                        'Calificación Empresario a Asesor',
                        'Calificación Asesor a Empresario'
                    ]);

                    $sheet->cells('A2:AV2', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                    });

                    $sheet->setMergeColumn([ 'columns' => ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV'], 'rows' => [ [2,4] ] ]);

                    foreach ($data as $value) {
                        $categorias_marketplace = explode(',', $value->categorias_marketplace);

                        $sheet->appendRow([
                            $value->numero_registro_convocatoria,
                            $value->fecha_registro_convocatoria,
                            $value->nombre_clase_emprendimiento,
                            $value->nombre_proyecto,
                            $value->identificacion_usuario,
                            $value->descripcion_empresa,
                            $value->nombre_categoria_proyecto,
                            $value->ciiu_numero,
                            $value->ciiu,
                            isset(Usuario::$gender[$value->genero_persona]) ? Usuario::$gender[$value->genero_persona]: '',
                            $value->nombre_representante_legal,
                            $value->identificacion_rep_legal,
                            $value->correo_electronico,
                            $value->telefono_usuario,
                            $value->movil_usuario,
                            $value->ciudad_empresa,
                            $value->departamento_empresa,
                            $value->direccion_user,
                            !is_null($value->crfu_reto2) ? round( (($value->enterprising_team / 100) * 40) + (($value->market / 100) * 25) + (($value->product / 100) * 25) + (($value->productive_chains / 100) * 10) ) : '',
                            $value->notificacion_cambio_registro_fase2,
                            (!empty($value->llaves_de_acceso) && $value->llaves_de_acceso > 0) ? $value->llaves_de_acceso : '',
                            isset($categorias_marketplace[0]) ? $categorias_marketplace[0] : '',
                            isset($categorias_marketplace[1]) ? $categorias_marketplace[1] : '',
                            isset($categorias_marketplace[2]) ? $categorias_marketplace[2] : '',
                            $value->nombre_estado,
                            $value->fecha_documentos,
                            $value->fecha_carta_compormiso_compra,
                            $value->fecha_aceptacion_carta_compormiso_compra,
                            $value->contrato_asesor,
                            $value->asesor_contrato,
                            $value->codigo_nit,
                            $value->nombre_categoria_producto,
                            $value->nombre_cat_padre_producto,
                            $value->fecha_inicio_actividades_acuerdo,
                            $value->fecha_final_actividades_acuerdo,
                            $value->fecha_finalizacion_actividades,
                            $value->duracion_servicio_meses,
                            $value->fecha_aprobacion_asesor_acuerdo_compra,
                            $value->fecha_acuerdo_aceptacion_compra,
                            $value->fecha_acuerdo_aprobacion_compra,
                            $value->valor_total_compra,
                            $value->valor_total_acuerdos,
                            $value->saldo_detalle_voucher,
                            $value->estado_acuerdo,
                            $value->estado_llaves_acceso,
                            '',
                            $value->promedio_empresario,
                            $value->promedio_asesor,
                        ]);
                    }
                });
            })->string('xlsx');

            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte reto 3 File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = 'Reporte reto 3';
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = Institucion::institution('innpulsa');
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportReto3:Command', $e->getMessage()));
            abort(500);
        }
    }
}