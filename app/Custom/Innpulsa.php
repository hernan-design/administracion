<?php

namespace Custom;

use App\Models\Core\ItemOpcionEncuesta;
use function foo\func;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;


use App\Models\Core\Convocatoria,
	App\Models\Core\GrupoItemEval,
	App\Models\Core\EvaluacionItemEmprendimiento,
	App\Models\Core\AsignacionJurado,
	App\Models\Core\ItemEvaluacionEmprendimiento,
	App\Models\Core\ResultadoEvaluation,
	App\Models\Core\RegistroConvocatoria,
	App\Models\Core\OrientacionConvocatoria,
	App\Models\Core\RespuestaEncuesta,
	App\Models\Core\SectorInteres,
	App\Models\Core\RegistroFaseUsuario,
	App\Models\Core\Estado,
	App\Models\Core\Fase,
	App\Models\Core\Encuesta,
	App\Models\Core\PreguntaEncuesta,
	App\Models\Core\OpcionPreguntaEncuesta,
	App\Models\Core\Ciudad,
	App\Models\Core\NivelEducacion,
	App\Models\Core\TipoCampoPregunta,
	App\Models\Core\Voucher,
	App\Models\Core\Compra,
	App\Models\Core\Producto,
	App\Models\Core\UsuarioEncuesta,
	App\Models\Core\VerPregEncuesta,
	App\Models\Core\Adjunto,
	App\Models\Seguridad\Usuario,
	App\Models\Param\Institucion;

use App\Console\Commands\Reports\ReportProgramAppsCoInnpulsa;

use GuzzleHttp\Client, JpGraph\JpGraph, RadarGraph, RadarPlot;

use DB, App, Auth, Input, Lang, SendEmail, TuProyecto, Storage, Datatables;

class Innpulsa
{
    public static $challenge = 49;
    public static $challenge_empodera = 92;
	public static $challenge_reto1 = 113;
	public static $stage_challenge_1 = 134;
	public static $stage_challenge_2 = 137;
	public static $stage_challenge_3 = 138;
	public static $stage_challenge_4 = 139;
	public static $stage_challenge_1_2 = 150;

	public static $survey_reto4 = 66;

	public static $challenge_empodera_reto_1 = 198;
	public static $stage_challenge_empodera_1 = 204;
    public static $stage_challenge_empodera_1_2 = 212;

	public static $challenge_empodera_reto_1_2020 = 471;
	public static $stage_challenge_empodera_1_2020 = 249;
	public static $stage_challenge_empodera_1_2_2020 = 251;

	public static $challenge_aldea_2020 = 504;
	public static $stage_challenge_aldea_2020 = 253;
	public static $stage_challenge3_aldea_2020 = 255;
	public static $stage_challenge4_aldea_2020 = 256;

	public static $program_aldea20 = 504;

	public static $email_user_institution = 'ruben.gantiva@innpulsacolombia.com';

	public static function getRegion__Moment_Four__Aldea20($type) {
		switch($type) {
			case 'bajo':
				return 3;
			break;
			case 'medioBajo':
				return 2;
			break;
			case 'medio':
				return 1;
			break;
		}
	}

	public static function getItemEvaluation($code) {
		$query = RegistroFaseUsuario::query();
		$query->select('opc_eval_pregunta.nombre_opc_eval_pregunta as description', 'evaluacion_item_emprendimiento.puntaje_resul_eval_empren as score');
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
		$query->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', 'evaluacion_item_emprendimiento.valor_evaluacion_item');
		$query->where('registro_fase_usuario.codigo_registro_fase_usuario', '=', $code);

		return $query->get();
	}

	public static function getResultScoreAdminManagement(Collection $answers)
	{
		$result = new \stdClass();
		$score_admin_management = $answers->where('question_id', 1261)->first()->score_option+$answers->where('question_id', 1262)->first()->score_option+$answers->where('question_id', 1263)->first()->score_option+$answers->where('question_id', 1264)->first()->score_option;
		$texto_respuesta = "";
		if($score_admin_management<8){
			$texto_respuesta = "<p>Para el crecimiento de una compañía es muy importante definir un norte claro y una meta de hacia donde quiere llevar su empresa, el cual debe ser comunicado e interiorizado por cada uno de los colaboradores de la compañía.</p><p>Estar en contacto con sus clientes permanentemente es primordial, para identificar que les “duele” y como el producto o servicio desarrollado por su empresa ayuda a resolver ese dolor,  preparare un discurso de su propuesta de valor, claro y contundente, que le permita explicarle a sus clientes por qué deben comprarle su producto o servicio y no el de la competencia. Fortalecer esta área de la compañía le permitirá aprovechar de mejor manera las oportunidades y amenazas que constantemente influirán en el desempeño de su compañía.</p>";
		}
		else if($score_admin_management<16){
			$texto_respuesta = "<p>Es importante revisar las metas de crecimiento de mediano plazo de su empresa, teniendo en cuenta  el diferencial, el potencial de mercado, y la retroalimentación que realizan los clientes de su producto o servicio.</p><p>Fortalecer constantemente esta área de la compañía le permitirá aprovechar de mejor manera las oportunidades y amenazas que constantemente influirán en el desempeño de su compañía.</p>";
		}
		else{
			$texto_respuesta = "<p>Su empresa tiene claramente definido el norte y las metas a alcanzar en el mediano plazo, es importante asegurarse que este norte y las metas sean conocidas y acogidas por cada uno de los colaboradores de su empresa, que cada uno de ellos se sienta identificado con la razón de ser de la compañía, y  que este sentido de pertenencia se trasmita a sus clientes y aliados.</p><p>Es primordial seguir fortaleciendo las relaciones con sus clientes, pues ellos son la fuente de potenciales mejoras a sus productos y servicios actuales, y junto con información del entorno ofrecerán insumos para desarrollar nuevos productos o servicios.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->score = round($score_admin_management/4);
		$result->title = "Gestión Administrativa";
		return $result;
	}

	public static function getResultScoreHumanManagement(Collection $answers)
	{
		$result = new \stdClass();
		$score_human_management = $answers->where('question_id', 1265)->first()->score_option+$answers->where('question_id', 1266)->first()->score_option+$answers->where('question_id', 1267)->first()->score_option+$answers->where('question_id', 1268)->first()->score_option;
		$texto_respuesta = "";
		if($score_human_management<8){
			$texto_respuesta = "<p>La empresa cuenta con oportunidades de crecer y ser mas competitiva en el mercado si empieza a innovar en su interior, estas innovaciones puede ser enfocadas en productos/servicios, procesos o modelo de negocio. Es importante que, en primera medida, la compañía establezca mecanismos para que los colaboradores planteen sus ideas; y procesos para evaluar, seleccionar y ejecutar las ideas con mayor potencial, y de esta manera comenzar a crear una cultura innovadora dentro la compañía.</p><p>Para esto es primordial ejecutar acciones encaminadas a fortalecer el desarrollo del capital humano de la empresa, en pro de un mayor sentido de pertenencia, una mayor motivación y un clima laboral optimo para el libre desarrollo profesional.</p>";
		}
		else if($score_human_management<16){
			$texto_respuesta = "La empresa cuenta con oportunidades de crecer y ser mas competitiva en el mercado si fortalece la innovación en su interior, estas innovaciones puede ser enfocadas en productos, procesos o modelo de negocio. Es importante acelerar las acciones que están implementando para fortalecer el desarrollo del capital humano de la empresa, y hacer de las innovaciones una realidad en la compañía.";
		}
		else{
			$texto_respuesta = "La empresa ya implementa innovaciones al interior de la compañía, es recomendable que fortalezcan su articulación con el ecosistema de innovación y emprendimiento, y se apalanquen en instrumentos de fomento a la Ciencia, tecnología e innovación, lo cual les permitirá acelerar este proceso innovador y mantenerse a la vanguardia en su industria.";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->score = round($score_human_management/4);
		$result->title = "Gestión humana y cultura de la innovación";
		return $result;
	}

	public static function getResultScoreFinancialManagement(Collection $answers)
	{
		$result = new \stdClass();
		$score_financial_management = $answers->where('question_id', 1269)->first()->score_option+$answers->where('question_id', 1270)->first()->score_option+$answers->where('question_id', 1271)->first()->score_option+$answers->where('question_id', 1272)->first()->score_option;
		$texto_respuesta = "";
		if($score_financial_management<8){
			$texto_respuesta = "<p>Es primordial que siempre la compañía maneje cuentas separadas de las cuentas personales de sus dueños, pues esto le permite conocer,  analizar y tomar los correctivos necesarios para tener una buena salud financiera de la empresa.</p><p>Es importante implementar un mecanismo financiero en su empresa que le permita identificar de manera clara cuáles son sus ingresos, costos  y gastos,  para establecer si su negocio es rentable o no, es decir, saber si está perdiendo o ganando dinero.</p><p>Es recomendable construir estados financieros como balance, estado de resultados y flujo de caja, los cuales le permitirán organizar la información financiera de su empresa de manera periódica para tomar decisiones estratégicas e inteligentes que lo lleven al crecimiento económico de su empresa.</p>";
		}
		else if($score_financial_management<16){
			$texto_respuesta = "<p>La organización de la información financiera en una empresa es un factor determinante para el funcionamiento de la misma, llevar un control diario de los costos, gastos, ingresos  es imprescindible para estar al tanto de todas las transacciones y poder solventar de forma rápida los problemas de liquidez que puedan darse durante la operación de la empresa.</p><p>Es recomendable implementar al interior de su empresa procedimientos, normas o políticas para el manejo de las diferentes transacciones como: compras, ventas, entradas y salidas de caja, pagos, etc.; esto le permitirá mantener unificada la información.</p>";
		}
		else{
			$texto_respuesta = "Su empresa cuenta con una organización financiera que le permite estar al tanto de las transacciones realizadas por su empresa, se recomienda hacer una revisión y comparación periódica de estos y utilizar y analizar  indicadores  que le permitan determinar el estado de su empresa en términos de crecimiento,  liquidez, eficiencia, desempeño, productividad, entre otros. Se recomienda hacer una revisión periódica de los indicadores financieros para medir la efectividad de sus acciones en el mercado y de su negocio en el tiempo.";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->score = round($score_financial_management/4);
		$result->title = "Gestión Financiera";
		return $result;
	}

	public static function getResultScoreCommercialManagement(Collection $answers)
	{
		$result = new \stdClass();
		$score_comercial_management = $answers->where('question_id', 1273)->first()->score_option+$answers->where('question_id', 1274)->first()->score_option+$answers->where('question_id', 1275)->first()->score_option+$answers->where('question_id', 1276)->first()->score_option;
		$texto_respuesta = "";
		if($score_comercial_management<8){
			$texto_respuesta = "<p>Su empresa debe concentrar esfuerzos en definir e identificar con claridad a su cliente, sus necesidades, sus gustos y sus patrones de comportamiento, de manera que pueda garantizar que su oferta de productos y/o servicios es adecuada para su cliente objetivo. Una vez usted conoce a su cliente, es importante diseñar estrategias adecuadas para encontrar los canales para poder llegar a él y de ésta manera aumentar la probabilidad de incrementar  sus ventas.</p><p>Es importante  estar en constante contacto con su cliente validando que sus productos y/o servicios sean adecuados para él, pues es por éste que su compañía se mantiene en el mercado.</p>";
		}
		else if($score_comercial_management<16){
			$texto_respuesta = "La empresa debe fortalecer su estrategia  para llegar a sus clientes, utilizando los canales óptimos y manteniendo un diferencial claro de sus productos y/o servicios frente a los de su competencia. Es importante  estar en constante contacto con su cliente validando que sus productos y/o servicios sean adecuados para él, pues es por éste que su compañía se mantiene en el mercado.";
		}
		else{
			$texto_respuesta = "Su empresa se encuentra en un nivel óptimo en el  área de mercadeo y ventas dado que tiene plenamente identificado a su cliente objetivo y su diferencial vs la competencia. No obstante, es importante que la empresa alimente el deseo de ser la número uno en el mercado que atiende, para lo cual se le recomienda que desarrolle plan de expansión de mercado, en dónde contemple nuevos nichos de mercado, posibilidades de internacionalización y su potencial de desarrollar nuevos productos y/o servicios.";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->score = round($score_comercial_management/4);
		$result->title = "Gestión Comercial";
		return $result;
	}

	public static function getResultScoreProductionManagement(Collection $answers)
	{
		$result = new \stdClass();
		$score_production_management = $answers->where('question_id', 1277)->first()->score_option+$answers->where('question_id', 1278)->first()->score_option+$answers->where('question_id', 1279)->first()->score_option+$answers->where('question_id', 1280)->first()->score_option;
		$texto_respuesta = "";
		if($score_production_management<8){
			$texto_respuesta = "<p>La empresa debe conocer en detalle cómo calcular los precios de venta de sus productos y servicios, partiendo del calculo de los costos y gastos de producción como pueden ser las materias primas, mano de obra, infraestructura, entre otros; y la rentabilidad que esperan obtener, sin dejar de lado que esta información debe ser contrastada con los precios de mercado y de la competencia.</p><p>El ciclo productivo y la capacidad instalada de la compañía debe ser estimada adecuadamente por la empresa, pues esta información es útil para gestionar mejoras que le permitan capturar eficiencias de producción que redunden en una mayor productividad.</p>";
		}
		else if($score_production_management<16){
			$texto_respuesta = "La empresa debe identificar oportunidades de mejora dentro de su proceso productivo que le permitan  ser más eficiente y alcanzar las metas futuras de la compañia,  partiendo del conocimiento de los precios de venta de sus productos y servicios, así como del cumplimiento de la normatividad vigente.";
		}
		else{
			$texto_respuesta = "<p>Su empresa se encuentra en un nivel óptimo en el área de producción, puesto que tiene plenamente definido un proceso de calculo de precio de venta de sus productos y servicios, determinada su capacidad instalada, y cumple con la normatividad vigente.</p><p>Es importante, gestionar una comunicación eficiente entre las áreas de mercadeo y la de producción para incorporar mejora a sus procesos de cara a la satisfacción de los clientes.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->score = round($score_production_management/4);
		$result->title = "Gestión de la producción";
		return $result;
	}

	public static function getCashFlow(Collection $answers){
		$cash_flow = new \stdClass();

		@$disminucion_cartera2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1813)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1813)->first()->answer;
		@$disminucion_cartera2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1813)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1813)->first()->answer;

		@$disminucion_inventarios2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1814)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1814)->first()->answer;
		@$disminucion_inventarios2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1814)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1814)->first()->answer;

		@$disminucion_otros_activos_corrientes2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1815)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1815)->first()->answer;
		@$disminucion_otros_activos_corrientes2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1815)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1815)->first()->answer;

		@$disminucion_proveedores2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1828)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1833)->where('option_code_y', 1828)->first()->answer;
		@$disminucion_proveedores2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1828)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1828)->first()->answer;

		@$disminucion_otros_pasivos_corrientes2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1830)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1833)->where('option_code_y', 1830)->first()->answer;
		@$disminucion_otros_pasivos_corrientes2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1830)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1830)->first()->answer;

		@$inversion_capital_trabajo2014 = $disminucion_cartera2014 + $disminucion_inventarios2014 + $disminucion_otros_activos_corrientes2014 + $disminucion_proveedores2014 + $disminucion_otros_pasivos_corrientes2014;
		@$inversion_capital_trabajo2015 = $disminucion_cartera2015 + $disminucion_inventarios2015 + $disminucion_otros_activos_corrientes2015 + $disminucion_proveedores2015 + $disminucion_otros_pasivos_corrientes2015;

		@$impuestos2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1829)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1833)->where('option_code_y', 1829)->first()->answer - $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1855)->first()->answer;
		@$impuestos2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1829)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1829)->first()->answer - $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1855)->first()->answer;

		@$efectivo_neto_operacional2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer + $inversion_capital_trabajo2014 + $impuestos2014;
		@$efectivo_neto_operacional2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer + $inversion_capital_trabajo2015 + $impuestos2015;

		@$ventas_activos_fijos_2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1820)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1820)->first()->answer;
		@$ventas_activos_fijos_2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1820)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1820)->first()->answer;

		@$disminuciones_inversiones_permanentes2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1819)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1819)->first()->answer;
		@$disminuciones_inversiones_permanentes2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1819)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1819)->first()->answer;

		@$disminuciones_activos_diferidos2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1817)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1817)->first()->answer;
		@$disminuciones_activos_diferidos2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1817)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1817)->first()->answer;

		@$efectivo_neto_actividdes_inversion2014 = $ventas_activos_fijos_2014+$disminuciones_inversiones_permanentes2014+$disminuciones_activos_diferidos2014;
		@$efectivo_neto_actividdes_inversion2015 = $ventas_activos_fijos_2015+$disminuciones_inversiones_permanentes2015+$disminuciones_activos_diferidos2015;

		@$flujo_caja_libre2014 = $efectivo_neto_operacional2014 + $efectivo_neto_actividdes_inversion2014;
		@$flujo_caja_libre2015 = $efectivo_neto_operacional2015 + $efectivo_neto_actividdes_inversion2015;

		@$gastos_financieros2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer * -1;
		@$gastos_financieros2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer * -1;

		@$amortizacion_bancos_bonos_cp2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1827)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1833)->where('option_code_y', 1827)->first()->answer;
		@$amortizacion_bancos_bonos_cp2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1827)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1827)->first()->answer;

		@$amortizacion_bancos_bonos_lp2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1831)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1833)->where('option_code_y', 1831)->first()->answer;
		@$amortizacion_bancos_bonos_lp2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1831)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1831)->first()->answer;

		$efectivo_neto_actividades_financiacion2014 = $gastos_financieros2014 + $amortizacion_bancos_bonos_cp2014 + $amortizacion_bancos_bonos_lp2014;
		$efectivo_neto_actividades_financiacion2015 = $gastos_financieros2015 + $amortizacion_bancos_bonos_cp2015 + $amortizacion_bancos_bonos_lp2015;

		$flujo_caja_libre_despues_financiacion2014 = $flujo_caja_libre2014 + $efectivo_neto_actividades_financiacion2014;
		$flujo_caja_libre_despues_financiacion2015 = $flujo_caja_libre2015 + $efectivo_neto_actividades_financiacion2015;


		$cash_flow->disminucion_cartera2014 = $disminucion_cartera2014;
		$cash_flow->disminucion_cartera2015 = $disminucion_cartera2015;
		$cash_flow->disminucion_inventarios2014 = $disminucion_inventarios2014;
		$cash_flow->disminucion_inventarios2015 = $disminucion_inventarios2015;
		$cash_flow->disminucion_otros_activos_corrientes2014 = $disminucion_otros_activos_corrientes2014;
		$cash_flow->disminucion_otros_activos_corrientes2015 = $disminucion_otros_activos_corrientes2015;
		$cash_flow->disminucion_proveedores2014 = $disminucion_proveedores2014;
		$cash_flow->disminucion_proveedores2015 = $disminucion_proveedores2015;
		$cash_flow->disminucion_otros_pasivos_corrientes2014 = $disminucion_otros_pasivos_corrientes2014;
		$cash_flow->disminucion_otros_pasivos_corrientes2015 = $disminucion_otros_pasivos_corrientes2015;
		$cash_flow->inversion_capital_trabajo2014 = $inversion_capital_trabajo2014;
		$cash_flow->inversion_capital_trabajo2015 = $inversion_capital_trabajo2015;
		$cash_flow->impuestos2014 = $impuestos2014;
		$cash_flow->impuestos2015 = $impuestos2015;
		$cash_flow->efectivo_neto_operacional2014 = $efectivo_neto_operacional2014;
		$cash_flow->efectivo_neto_operacional2015 = $efectivo_neto_operacional2015;
		$cash_flow->ventas_activos_fijos_2014 = $ventas_activos_fijos_2014;
		$cash_flow->ventas_activos_fijos_2015 = $ventas_activos_fijos_2015;
		$cash_flow->disminuciones_inversiones_permanentes2014 = $disminuciones_inversiones_permanentes2014;
		$cash_flow->disminuciones_inversiones_permanentes2015 = $disminuciones_inversiones_permanentes2015;
		$cash_flow->disminuciones_activos_diferidos2014 = $disminuciones_activos_diferidos2014;
		$cash_flow->disminuciones_activos_diferidos2015 = $disminuciones_activos_diferidos2015;
		$cash_flow->efectivo_neto_actividdes_inversion2014 = $efectivo_neto_actividdes_inversion2014;
		$cash_flow->efectivo_neto_actividdes_inversion2015 = $efectivo_neto_actividdes_inversion2015;
		$cash_flow->flujo_caja_libre2014 = $flujo_caja_libre2014;
		$cash_flow->flujo_caja_libre2015 = $flujo_caja_libre2015;
		$cash_flow->gastos_financieros2014 = $gastos_financieros2014;
		$cash_flow->gastos_financieros2015 = $gastos_financieros2015;
		$cash_flow->amortizacion_bancos_bonos_cp2014 = $amortizacion_bancos_bonos_cp2014;
		$cash_flow->amortizacion_bancos_bonos_cp2015 = $amortizacion_bancos_bonos_cp2015;
		$cash_flow->amortizacion_bancos_bonos_lp2014 = $amortizacion_bancos_bonos_lp2014;
		$cash_flow->amortizacion_bancos_bonos_lp2015 = $amortizacion_bancos_bonos_lp2015;
		$cash_flow->efectivo_neto_actividades_financiacion2014 = $efectivo_neto_actividades_financiacion2014;
		$cash_flow->efectivo_neto_actividades_financiacion2015 = $efectivo_neto_actividades_financiacion2015;
		$cash_flow->flujo_caja_libre_despues_financiacion2014 = $flujo_caja_libre_despues_financiacion2014;
		$cash_flow->flujo_caja_libre_despues_financiacion2015 = $flujo_caja_libre_despues_financiacion2015;


		@$ingresos_financieros2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1848)->first()->answer;
		@$ingresos_financieros2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1848)->first()->answer;

		@$otros_neto_no_operacional2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1850)->first()->answer;
		@$otros_neto_no_operacional2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1850)->first()->answer;

		@$aumento_disminucion_otros_activos2014 = ($answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1816)->first()->answer + $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1818)->first()->answer) - ($answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1816)->first()->answer + $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1818)->first()->answer);
		@$aumento_disminucion_otros_activos2015 = ($answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1816)->first()->answer + $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1818)->first()->answer) - ($answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1816)->first()->answer + $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1818)->first()->answer);

		@$aumento_disminucion_otros_pasivos2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1832)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1833)->where('option_code_y', 1832)->first()->answer;
		@$aumento_disminucion_otros_pasivos2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1832)->first()->answer - $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1832)->first()->answer;

		@$capital_social2014 = $answers->where('question_id', 1392)->where('option_code_x', 1840)->where('option_code_y', 1836)->first()->answer - $answers->where('question_id', 1392)->where('option_code_x', 1839)->where('option_code_y', 1836)->first()->answer;
		@$capital_social2015 = $answers->where('question_id', 1392)->where('option_code_x', 1841)->where('option_code_y', 1836)->first()->answer - $answers->where('question_id', 1392)->where('option_code_x', 1840)->where('option_code_y', 1836)->first()->answer;

		@$aumento_disminucion_patrimonio_accionistas2014 = -1*($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1846)->first()->answer + $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1847)->first()->answer + $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1819)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1819)->first()->answer + $disminuciones_inversiones_permanentes2014 + $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1919)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1919)->first()->answer + $ventas_activos_fijos_2014 + $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1823)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1823)->first()->answer + $answers->where('question_id', 1392)->where('option_code_x', 1839)->where('option_code_y', 1928)->first()->answer - $answers->where('question_id', 1392)->where('option_code_x', 1840)->where('option_code_y', 1928)->first()->answer + $capital_social2014 + $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1937)->first()->answer);

		@$aumento_disminucion_patrimonio_accionistas2015 = -1*($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1846)->first()->answer + $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1847)->first()->answer + $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1819)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1819)->first()->answer + $disminuciones_inversiones_permanentes2015 + $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1919)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1919)->first()->answer + $ventas_activos_fijos_2015 + $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1823)->first()->answer - $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1823)->first()->answer + $answers->where('question_id', 1392)->where('option_code_x', 1840)->where('option_code_y', 1928)->first()->answer - $answers->where('question_id', 1392)->where('option_code_x', 1841)->where('option_code_y', 1928)->first()->answer + $capital_social2015 + $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1937)->first()->answer);

		@$efectivo_neto_otros2014 = $ingresos_financieros2014 + $otros_neto_no_operacional2014 + $aumento_disminucion_otros_activos2014 + $aumento_disminucion_otros_pasivos2014 + $capital_social2014 + $aumento_disminucion_patrimonio_accionistas2014;
		@$efectivo_neto_otros2015 = $ingresos_financieros2015 + $otros_neto_no_operacional2015 + $aumento_disminucion_otros_activos2015 + $aumento_disminucion_otros_pasivos2015 + $capital_social2015 + $aumento_disminucion_patrimonio_accionistas2015;

		@$flujo_caja_neto_2014 = $flujo_caja_libre_despues_financiacion2014 + $efectivo_neto_otros2014;
		@$flujo_caja_neto_2015 = $flujo_caja_libre_despues_financiacion2015 + $efectivo_neto_otros2015;

		@$saldo_inicial_disponible_inv_termporales2014 = $answers->where('question_id', 1390)->where('option_code_x', 1824)->where('option_code_y', 1812)->first()->answer;
		@$saldo_inicial_disponible_inv_termporales2015 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1812)->first()->answer;
		@$saldo_final_disponible_inv_termporales2014 = $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1812)->first()->answer;
		@$saldo_final_disponible_inv_termporales2015 = $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1812)->first()->answer;

		@$aumento_disminucion_disponibles_2014 = $saldo_final_disponible_inv_termporales2014 - $saldo_inicial_disponible_inv_termporales2014;
		@$aumento_disminucion_disponibles_2015 = $saldo_final_disponible_inv_termporales2015 - $saldo_inicial_disponible_inv_termporales2015;

		$cash_flow->ingresos_financieros2014 = $ingresos_financieros2014;
		$cash_flow->ingresos_financieros2015 = $ingresos_financieros2015;

		$cash_flow->otros_neto_no_operacional2014 = $otros_neto_no_operacional2014;
		$cash_flow->otros_neto_no_operacional2015 = $otros_neto_no_operacional2015;

		$cash_flow->aumento_disminucion_otros_activos2014 = $aumento_disminucion_otros_activos2014;
		$cash_flow->aumento_disminucion_otros_activos2015 = $aumento_disminucion_otros_activos2015;

		$cash_flow->aumento_disminucion_otros_pasivos2014 = $aumento_disminucion_otros_pasivos2014;
		$cash_flow->aumento_disminucion_otros_pasivos2015 = $aumento_disminucion_otros_pasivos2015;

		$cash_flow->capital_social2014 = $capital_social2014;
		$cash_flow->capital_social2015 = $capital_social2015;

		$cash_flow->aumento_disminucion_patrimonio_accionistas2014 = $aumento_disminucion_patrimonio_accionistas2014;
		$cash_flow->aumento_disminucion_patrimonio_accionistas2015 = $aumento_disminucion_patrimonio_accionistas2015;

		$cash_flow->efectivo_neto_otros2014 = $efectivo_neto_otros2014;
		$cash_flow->efectivo_neto_otros2015 = $efectivo_neto_otros2015;

		$cash_flow->flujo_caja_neto_2014 = $flujo_caja_neto_2014;
		$cash_flow->flujo_caja_neto_2015 = $flujo_caja_neto_2015;

		$cash_flow->saldo_inicial_disponible_inv_termporales2014 = $saldo_inicial_disponible_inv_termporales2014;
		$cash_flow->saldo_inicial_disponible_inv_termporales2015 = $saldo_inicial_disponible_inv_termporales2015;

		$cash_flow->saldo_final_disponible_inv_termporales2014 = $saldo_final_disponible_inv_termporales2014;
		$cash_flow->saldo_final_disponible_inv_termporales2015 = $saldo_final_disponible_inv_termporales2015;

		$cash_flow->aumento_disminucion_disponibles_2014 = $aumento_disminucion_disponibles_2014;
		$cash_flow->aumento_disminucion_disponibles_2015 = $aumento_disminucion_disponibles_2015;


		return $cash_flow;
	}

	public static function getInterpretationCashFlow(Collection $answers, $cash_flow){

		$date = "diciembre de 2015";
		$ebitda_2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer;
		$efectivo_neto_operacional_2015 = $cash_flow->efectivo_neto_operacional2015;
		$efectivo_neto_actividades_inversion_2015 = $cash_flow->efectivo_neto_actividdes_inversion2015;
		$flujo_caja_libre_2015 = $cash_flow->flujo_caja_libre2015;
		$efectivo_neto_Actividades_financiacion_2015 = $cash_flow->efectivo_neto_actividades_financiacion2015;
		$caja_libre_despues_financiacion_2015 = $cash_flow->flujo_caja_libre_despues_financiacion2015;
		$efectivo_neto_otros_2015 = $cash_flow->efectivo_neto_otros2015;
		$flujo_caja_neto_2015 = $cash_flow->flujo_caja_neto_2015;

		$interpretation_cash_flow = new \stdClass();

		$interpretation_cash_flow->definition_ebitda = "El EBITDA (por sus siglas en ingles, se traduce como Utilidades antes de intereses, impuestos, depreciación y amortización) es un indicador del desempeño financiero de la empresa y se utiliza para medir el potencial de ingresos del negocio. Generalmente se utiliza para analizar la rentabilidad de una empresa, y compararla frente a otras y a la industria.";
		$interpretation_cash_flow->interpretation_ebitda = "Para ".$date." la empresa generó un EBITDA de $ ".number_format($ebitda_2015,2,'.',',').", es decir que, después de restarle a las ventas / ingresos de la empresa los costos y gastos de operación, y sin tener en cuenta los gastos que no implican salida de efectivo  como son las depreciaciones y amortizaciones, la empresa cuenta con $ ".number_format($ebitda_2015,2,'.',',')." para atender los requerimientos de efectivo del negocio.";
		$interpretation_cash_flow->plan_ebitda = "ESTRATÉGICO";


		$interpretation_cash_flow->definition_efectivo_neto_operacional = "El efectivo neto operacional, está relacionado con la adquisición, gestión y disposición de activos de corto plazo (inferiores a un año). Muestra cómo, operativamente, se esta gestionando la compañía.";
		$interpretation_cash_flow->interpretation_efectivo_neto_operacional = "Luego de descontar los requerimientos operativos del negocio la empresa cuenta en ".$date." con $ ".number_format($efectivo_neto_operacional_2015,2,'.',',').", para atender los requerimientos de efectivo para activos de largo plazo y atención de actividades de financiación.";
		$interpretation_cash_flow->plan_efectivo_neto_operacional = "TÁCTICO";

		$interpretation_cash_flow->definition_efectivo_actividades_inversion = "El flujo de efectivo en actividades de inversión, está relacionado con la adquisición y disposición de activos a largo plazo, no relacionadas con el equivalente a efectivo";
		if($efectivo_neto_actividades_inversion_2015>0){
			$interpretation_cash_flow->interpretation_efectivo_actividades_inversion = "Para ".$date." hubo una aumento en los activos  por un valor de $ ".number_format($efectivo_neto_actividades_inversion_2015,2,'.',',').", catalogados como inversión a largo plazo.";
		}
		else{
			$interpretation_cash_flow->interpretation_efectivo_actividades_inversion = "Para ".$date." hubo una disminución en los activos  por un valor de $ ".number_format($efectivo_neto_actividades_inversion_2015,2,'.',',').", catalogados como inversión a largo plazo.";
		}
		$interpretation_cash_flow->plan_efectivo_actividades_inversion = "ESTRATÉGICO";

		$interpretation_cash_flow->definition_flujo_caja_libre = "El flujo de caja libre es un indicador que muestra la cantidad de dinero disponible que tiene la empresa  para pagar los accionistas y para cubrir el servicio de deuda";
		if($flujo_caja_libre_2015>0){
			$interpretation_cash_flow->interpretation_flujo_caja_libre = "Para ".$date." se tuvo un monto de $ ".number_format($flujo_caja_libre_2015,2,'.',',')." para atender la deuda y pagar a los accionistas.";
		}
		else{
			$interpretation_cash_flow->interpretation_flujo_caja_libre = "Para ".$date." se tuvo un monto de $ ".number_format($flujo_caja_libre_2015,2,'.',',')." para atender la deuda y pagar a los accionistas.";
		}
		$interpretation_cash_flow->plan_flujo_caja_libre = "ESTRATÉGICO";


		$interpretation_cash_flow->definition_efectivo_actividades_financiacion = "El flujo de efectivo en actividades de financiación está relacionado con el endeudamiento financiero de la empresa.";
		if($efectivo_neto_Actividades_financiacion_2015>0){
			$interpretation_cash_flow->interpretation_efectivo_actividades_financiacion = "Para ".$date." se requirío un monto de $ ".number_format($efectivo_neto_Actividades_financiacion_2015,2,'.',',')." para atender la deuda y pagar a los accionistas";
		}
		else{
			$interpretation_cash_flow->interpretation_efectivo_actividades_financiacion = "Para ".$date." no se requirió apalancar la operación con fuentes externas";
		}

		$interpretation_cash_flow->plan_efectivo_actividades_financiacion = "TÁCTICO";


		$interpretation_cash_flow->definition_caja_libre_despues_financiacion = "El flujo de caja libre despues de financiación muestra de manera clara la cantidad de efectivo que posee la empresa, incluyendo el que proviene de fuentes externas, es un índice que sirve para medir la liquidez de la empresa ";
		$interpretation_cash_flow->interpretation_caja_libre_despues_financiacion = "Para ".$date." el monto disponible es de $ ".number_format($caja_libre_despues_financiacion_2015,2,'.',',');
		$interpretation_cash_flow->plan_caja_libre_despues_financiacion = "TÁCTICO";


		$interpretation_cash_flow->definition_efectivo_neto_otros = "El efectivo neto en otros, refleja el ingreso o salida de efectivo por el comportamiento de otras cuentas no corrientes del activo, pasivo o patrimonio de la empresa, adicionando los ingresos financieros. ";
		$interpretation_cash_flow->interpretation_efectivo_neto_otros = "Los requerimientos no operativos de la empresa a ".$date." son $ ".number_format($efectivo_neto_otros_2015,2,'.',',');
		$interpretation_cash_flow->plan_efectivo_neto_otros = "TÁCTICO";


		$interpretation_cash_flow->definition_flujo_caja_neto = "Flujo de caja neto es el efectivo disponible después de atender deuda y requerimientos operativos";
		$interpretation_cash_flow->interpretation_flujo_caja_neto = "Para ".$date." generó disponibilidad de recursos por un monto de $ ".number_format($flujo_caja_neto_2015,2,'.',',');
		$interpretation_cash_flow->plan_flujo_caja_neto = "ESTRATÉGICO";

		return $interpretation_cash_flow;
	}

	public static function getFinancialReasons(Collection $answers, $cash_flow){

		$financial_reasons = new \stdClass();

		@$evolucion_ventas2014 = ($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1856)->where('option_code_y', 1842)->first()->answer) - 1;

		@$evolucion_ventas2015 = ($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer) - 1;

		@$evolucion_ebitda2014 = ($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1856)->where('option_code_y', 1934)->first()->answer) - 1;

		@$evolucion_ebitda2015 = ($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer) - 1;

		$capex_ventas2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer == 0){
			$capex_ventas2014 = 0;
		}
		else{
			@$capex_ventas2014 = (-1*$cash_flow->ventas_activos_fijos_2014)/$answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer;
		}

		$capex_ventas2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer == 0){
			$capex_ventas2015 = 0;
		}
		else{
			@$capex_ventas2015 = (-1*$cash_flow->ventas_activos_fijos_2015)/$answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer;
		}

		@$roa2014 = 0;
		if($answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1921)->first()->answer == 0){
			$roa2014 = 0;
		}
		else{
			@$roa2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1937)->first()->answer / $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1921)->first()->answer;
		}

		@$roa2015 = 0;
		if($answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1921)->first()->answer == 0){
			$roa2015 = 0;
		}
		else{
			@$roa2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1937)->first()->answer / $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1921)->first()->answer;
		}

		@$roe2014 = 0;
		if($answers->where('question_id', 1392)->where('option_code_x', 1840)->where('option_code_y', 1928)->first()->answer == 0){
			$roe2014 = 0;
		}
		else{
			@$roe2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1937)->first()->answer / $answers->where('question_id', 1392)->where('option_code_x', 1840)->where('option_code_y', 1928)->first()->answer;
		}

		@$roe2015 = 0;
		if($answers->where('question_id', 1392)->where('option_code_x', 1841)->where('option_code_y', 1928)->first()->answer == 0){
			$roe2015 = 0;
		}
		else{
			@$roe2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1937)->first()->answer / $answers->where('question_id', 1392)->where('option_code_x', 1841)->where('option_code_y', 1928)->first()->answer;
		}

		@$grado_endeudamiento2014 = 0;
		if($answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1921)->first()->answer == 0){
			$grado_endeudamiento2014 = 0;
		}
		else{
			@$grado_endeudamiento2014 = $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1924)->first()->answer / $answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1921)->first()->answer;
		}

		@$grado_endeudamiento2015 = 0;
		if($answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1921)->first()->answer == 0){
			$grado_endeudamiento2015 = 0;
		}
		else{
			@$grado_endeudamiento2015 = $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1924)->first()->answer / $answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1921)->first()->answer;
		}

		@$obligaciones_financieras_ventas2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer == 0){
			$obligaciones_financieras_ventas2014 = 0;
		}
		else{
			@$obligaciones_financieras_ventas2014 = ($answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1827)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1831)->first()->answer) / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer;
		}

		@$obligaciones_financieras_ventas2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer == 0){
			$obligaciones_financieras_ventas2015 = 0;
		}
		else{
			@$obligaciones_financieras_ventas2015 = ($answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1827)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1831)->first()->answer) / $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer;
		}

		@$obligaciones_financieras_ebitda2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer == 0){
			$obligaciones_financieras_ebitda2014 = 0;
		}
		else{
			@$obligaciones_financieras_ebitda2014 = ($answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1827)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1831)->first()->answer) / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer;
		}

		@$obligaciones_financieras_ebitda2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer == 0){
			$obligaciones_financieras_ebitda2015 = 0;
		}
		else{
			@$obligaciones_financieras_ebitda2015 = ($answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1827)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1831)->first()->answer) / $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer;
		}

		@$ebitda_gastos_financieros2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer == 0){
			$ebitda_gastos_financieros2014 = 0;
		}
		else{
			@$ebitda_gastos_financieros2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer;
		}

		@$ebitda_gastos_financieros2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer == 0){
			$ebitda_gastos_financieros2015 = 0;
		}
		else{
			@$ebitda_gastos_financieros2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer;
		}

		@$ebitda_gastos_tdcp_gf2014 = 0;
		if(($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1827)->first()->answer) == 0){
			$ebitda_gastos_tdcp_gf2014 = 0;
		}
		else{
			@$ebitda_gastos_tdcp_gf2014 = $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer / ($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1827)->first()->answer);
		}

		@$ebitda_gastos_tdcp_gf2015 = 0;
		if(($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1827)->first()->answer) == 0){
			$ebitda_gastos_tdcp_gf2015 = 0;
		}
		else{
			@$ebitda_gastos_tdcp_gf2015 = $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer / ($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer + $answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1827)->first()->answer);
		}

		@$flujo_caja_libre2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer == 0){
			$flujo_caja_libre2014 = 0;
		}
		else{
			@$flujo_caja_libre2014 = $cash_flow->flujo_caja_libre2014/$answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1849)->first()->answer;
		}

		@$flujo_caja_libre2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer == 0){
			$flujo_caja_libre2015 = 0;
		}
		else{
			@$flujo_caja_libre2015 = $cash_flow->flujo_caja_libre2015/$answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1849)->first()->answer;
		}

		@$dias_plazo_cobro2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer == 0){
			$dias_plazo_cobro2014 = 0;
		}
		else{
			@$dias_plazo_cobro2014 = ($answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1813)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1842)->first()->answer)*(12*30);
		}

		@$dias_plazo_cobro2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer == 0){
			$dias_plazo_cobro2015 = 0;
		}
		else{
			@$dias_plazo_cobro2015 = ($answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1813)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1842)->first()->answer)*(12*30);
		}

		@$dias_inventario2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1843)->first()->answer == 0){
			$dias_inventario2014 = 0;
		}
		else{
			@$dias_inventario2014 = ($answers->where('question_id', 1390)->where('option_code_x', 1825)->where('option_code_y', 1814)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1843)->first()->answer)*(12*30);
		}

		@$dias_inventario2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1843)->first()->answer == 0){
			$dias_inventario2015 = 0;
		}
		else{
			@$dias_inventario2015 = ($answers->where('question_id', 1390)->where('option_code_x', 1826)->where('option_code_y', 1814)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1843)->first()->answer)*(12*30);
		}

		@$dias_plazo_pago2014 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1843)->first()->answer == 0){
			$dias_plazo_pago2014 = 0;
		}
		else{
			@$dias_plazo_pago2014 = ($answers->where('question_id', 1391)->where('option_code_x', 1834)->where('option_code_y', 1828)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1843)->first()->answer)*(12*30);
		}

		@$dias_plazo_pago2015 = 0;
		if($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1843)->first()->answer == 0){
			$dias_plazo_pago2015 = 0;
		}
		else{
			@$dias_plazo_pago2015 = ($answers->where('question_id', 1391)->where('option_code_x', 1835)->where('option_code_y', 1828)->first()->answer / $answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1843)->first()->answer)*(12*30);
		}

		$ciclo_operacional2014 = $dias_plazo_cobro2014 + $dias_inventario2014 - $dias_plazo_pago2014;
		$ciclo_operacional2015 = $dias_plazo_cobro2015 + $dias_inventario2015 - $dias_plazo_pago2015;

		$financial_reasons->evolucion_ventas2014 = $evolucion_ventas2014*100;
		$financial_reasons->evolucion_ventas2015 = $evolucion_ventas2015*100;
		$financial_reasons->evolucion_ebitda2014 = $evolucion_ebitda2014*100;
		$financial_reasons->evolucion_ebitda2015 = $evolucion_ebitda2015*100;
		$financial_reasons->capex_ventas2014 = $capex_ventas2014*100;
		$financial_reasons->capex_ventas2015 = $capex_ventas2015*100;
		$financial_reasons->roa2014 = $roa2014*100;
		$financial_reasons->roa2015 = $roa2015*100;
		$financial_reasons->roe2014 = $roe2014*100;
		$financial_reasons->roe2015 = $roe2015*100;
		$financial_reasons->grado_endeudamiento2014 = $grado_endeudamiento2014*100;
		$financial_reasons->grado_endeudamiento2015 = $grado_endeudamiento2015*100;
		$financial_reasons->obligaciones_financieras_ventas2014 = $obligaciones_financieras_ventas2014;
		$financial_reasons->obligaciones_financieras_ventas2015 = $obligaciones_financieras_ventas2015;
		$financial_reasons->obligaciones_financieras_ebitda2014 = $obligaciones_financieras_ebitda2014;
		$financial_reasons->obligaciones_financieras_ebitda2015 = $obligaciones_financieras_ebitda2015;
		$financial_reasons->ebitda_gastos_financieros2014 = $ebitda_gastos_financieros2014;
		$financial_reasons->ebitda_gastos_financieros2015 = $ebitda_gastos_financieros2015;
		$financial_reasons->ebitda_gastos_tdcp_gf2014 = $ebitda_gastos_tdcp_gf2014;
		$financial_reasons->ebitda_gastos_tdcp_gf2015 = $ebitda_gastos_tdcp_gf2015;
		$financial_reasons->flujo_caja_libre2014 = $flujo_caja_libre2014;
		$financial_reasons->flujo_caja_libre2015 = $flujo_caja_libre2015;
		$financial_reasons->dias_plazo_cobro2014 = $dias_plazo_cobro2014;
		$financial_reasons->dias_plazo_cobro2015 = $dias_plazo_cobro2015;
		$financial_reasons->dias_inventario2014 = $dias_inventario2014;
		$financial_reasons->dias_inventario2015 = $dias_inventario2015;
		$financial_reasons->dias_plazo_pago2014 = $dias_plazo_pago2014;
		$financial_reasons->dias_plazo_pago2015 = $dias_plazo_pago2015;
		$financial_reasons->ciclo_operacional2014 = $ciclo_operacional2014;
		$financial_reasons->ciclo_operacional2015 = $ciclo_operacional2015;
		return $financial_reasons;
	}

	public static function getInterpretationFinancialReasons(Collection $answers, $financial_reasons){
		$interpretation_financial_reasons = new \stdClass();

		$date = "diciembre de 2015";

		$interpretation_financial_reasons->concepto_interpretacion_evluacion_ventas = "Evolución de Ventas";
		if($financial_reasons->evolucion_ventas2015 > 0){
			$interpretation_financial_reasons->interpretacion_evluacion_ventas = "Para ".$date." existe un crecimiento en ventas del ".number_format($financial_reasons->evolucion_ventas2015,2,'.',',')."%";
		}
		else{
			$interpretation_financial_reasons->interpretacion_evluacion_ventas = "Para ".$date." existe una disminución de las ventas del ".number_format($financial_reasons->evolucion_ventas2015,2,'.',',')."%";
		}
		$interpretation_financial_reasons->plan_interpretacion_evluacion_ventas = "OPERATIVO";

		$interpretation_financial_reasons->concepto_evolucion_ebitda = "Evolución EBITDA";
		if($financial_reasons->evolucion_ebitda2015 > 0){
			$interpretation_financial_reasons->interpretacion_evolucion_ebitda = "Para ".$date." existe un crecimiento en el ebitda de ".number_format($financial_reasons->evolucion_ebitda2015,2,'.',',')."%";
		}
		else{
			$interpretation_financial_reasons->interpretacion_evolucion_ebitda = "Para ".$date." existe una disminución del ebitda en ".number_format($financial_reasons->evolucion_ebitda2015,2,'.',',')."%";
		}
		$interpretation_financial_reasons->plan_interpretacion_evolucion_ebitda = "OPERATIVO";

		$interpretation_financial_reasons->concepto_roa = "ROA";
		$interpretation_financial_reasons->interpretacion_roa = "Para ".$date." hubo una rentabilidad de ".number_format($financial_reasons->roa2015,2,'.',',')."% con respecto al activo total de la empresa.";
		$interpretation_financial_reasons->plan_roa = "OPERATIVO";

		$interpretation_financial_reasons->concepto_roe = "ROE";
		$interpretation_financial_reasons->interpretacion_roe = "Para ".$date." hubo una rentabilidad de ".number_format($financial_reasons->roe2015,2,'.',',')."% del patrimonio de la empresa.";
		$interpretation_financial_reasons->plan_roe = "OPERATIVO";

		$interpretation_financial_reasons->concepto_grado_endeudamiento = "Grado de Endeudamiento (Total Pasivo / Total Activo)";
		$interpretation_financial_reasons->interpretacion_grado_endeudamiento = "Para ".$date." el nivel de endeudamiento de la empresa es de ".number_format($financial_reasons->grado_endeudamiento2015,2,'.',',')."%";
		$interpretation_financial_reasons->plan_grado_endeudamiento = "OPERATIVO";

		$interpretation_financial_reasons->concepto_obligaciones_financieras_ventas = "Obligaciones Financieras / Ventas";
		if($financial_reasons->obligaciones_financieras_ventas2015 > 0){
			$interpretation_financial_reasons->interpretacion_obligaciones_financieras_ventas = "Para ".$date." las obligaciones financieras representa ".number_format($financial_reasons->obligaciones_financieras_ventas2015,2,'.',',')." veces las ventas";
		}
		else{
			$interpretation_financial_reasons->interpretacion_obligaciones_financieras_ventas = "Para ".$date." las obligaciones financieras son menores en ".number_format($financial_reasons->obligaciones_financieras_ventas2015,2,'.',',')." veces las ventas";
		}
		$interpretation_financial_reasons->plan_obligaciones_financieras_ventas = "OPERATIVO";

		$interpretation_financial_reasons->concepto_obligaciones_financieras_ebitda = "Obligaciones Financieras / EBITDA";
		if($financial_reasons->obligaciones_financieras_ebitda2015 > 0){
			$interpretation_financial_reasons->interpretacion_obligaciones_financieras_ventas = "Para ".$date." las obligaciones financieras representa ".number_format($financial_reasons->obligaciones_financieras_ebitda2015,2,'.',',')." veces el ebitda";
		}
		else{
			$interpretation_financial_reasons->interpretacion_obligaciones_financieras_ebitda = "Para ".$date." las obligaciones financieras son menores en ".number_format($financial_reasons->obligaciones_financieras_ebitda2015,2,'.',',')." veces el ebitda";
		}
		$interpretation_financial_reasons->plan_obligaciones_financieras_ebitda = "OPERATIVO";

		$interpretation_financial_reasons->concepto_ebitda_gf = "Ebitda / Gastos Financieros";
		$interpretation_financial_reasons->interpretacion_ebitda_gf = "Para ".$date." el ebitda que atiende la deuda ".number_format($financial_reasons->ebitda_gastos_financieros2015,2,'.',',')." veces";
		$interpretation_financial_reasons->plan_ebitda_gf = "OPERATIVO";

		$interpretation_financial_reasons->concepto_ebitda_gf_dt = "Ebitda / (Total Deuda CP + Gastos Financieros)";
		$interpretation_financial_reasons->interpretacion_ebitda_gf_dt = "Para ".$date." el ebitda da cobertura a la deuda de corto plazo más sus gastos financieros ".number_format($financial_reasons->ebitda_gastos_tdcp_gf2015,2,'.',',')." veces";
		$interpretation_financial_reasons->plan_ebitda_gf_dt = "OPERATIVO";

		$interpretation_financial_reasons->concepto_flujo_caja_libre = "Flujo Caja Libre / Gastos Financieros";
		$interpretation_financial_reasons->interpretacion_flujo_caja_libre = "Para ".$date." el flujo de caja para atender la deuda es de ".number_format($financial_reasons->flujo_caja_libre2015,2,'.',',')." veces";
		$interpretation_financial_reasons->plan_flujo_caja_libre = "OPERATIVO";

		$interpretation_financial_reasons->concepto_dias_plazo_cobro = "Días Plazo Cobro (Cartera)";
		$interpretation_financial_reasons->interpretacion_dias_plazo_cobro = "Para ".$date." el promedio de días de cobro de la cartera es de ".number_format($financial_reasons->dias_plazo_cobro2015,2,'.',',')." veces";
		$interpretation_financial_reasons->plan_dias_plazo_cobro = "OPERATIVO";

		$interpretation_financial_reasons->concepto_dias_inventario = "Días de Inventarios";
		$interpretation_financial_reasons->interpretacion_dias_inventario = "Para ".$date." los días de inventario acumulados son ".number_format($financial_reasons->dias_inventario2015,2,'.',',')." veces";
		$interpretation_financial_reasons->plan_dias_inventario = "OPERATIVO";

		$interpretation_financial_reasons->concepto_dias_plazo_pago = "Días Plazo Pago (Proveedores)";
		$interpretation_financial_reasons->interpretacion_dias_plazo_pago = "Para ".$date." el pago promedio de proveedores en días es de ".number_format($financial_reasons->dias_plazo_pago2015,2,'.',',');
		$interpretation_financial_reasons->plan_dias_plazo_pago = "OPERATIVO";

		$interpretation_financial_reasons->concepto_ciclo_operacional = "Ciclo Operacional";
		if($financial_reasons->ciclo_operacional2015 < 0){
			$interpretation_financial_reasons->interpretacion_ciclo_operacional = "Para ".$date." el pago a proveedores está tomando mayor tiempo que la recolección de cartera, indicando mayor liquidez (deseable)";
		}
		else{
			$interpretation_financial_reasons->interpretacion_ciclo_operacional = "Para ".$date." el cobro de cartera toma más tiempo que el pago a proveedores poniéndolo en riesgo de liquidez";
		}
		$interpretation_financial_reasons->plan_ciclo_operacional = "OPERATIVO";

		return $interpretation_financial_reasons;
	}

	public static function getParticipants(Convocatoria $program, array $filters = [])
	{
		$participants = [];
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', 'registro_convocatoria.codigo_usuario_encuesta', "fase.codigo_evaluacion_emprendimiento", "observaciones_registro_fase_usuario", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario", DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "registro_fase_usuario.codigo_fase", "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "codigo_orientacion_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'url_encuesta', 'encuesta.codigo_encuesta', 'nombre_estado',
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("initcap(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'nombre_fase', 'numero_registro_convocatoria',
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 134
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND asignacion_jurado.codigo_estado = 15
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 150
							), 0)
						)
				END) AS total_evaluation"
			),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 137 THEN registro_fase_usuario.codigo_registro_fase_usuario
					ELSE (
						SELECT rfud.codigo_registro_fase_usuario
						FROM core.registro_fase_usuario as rfud
						WHERE rfud.codigo_usuario = registro_fase_usuario.codigo_usuario
							AND rfud.codigo_registro_convocatoria = registro_fase_usuario.codigo_registro_convocatoria
							AND rfud.codigo_fase = 137
					)
				END) AS registro_fase_documentador"
			),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 138 THEN registro_fase_usuario.codigo_registro_fase_usuario
					ELSE (
						SELECT rfud.codigo_registro_fase_usuario
						FROM core.registro_fase_usuario as rfud
						WHERE rfud.codigo_usuario = registro_fase_usuario.codigo_usuario
							AND rfud.codigo_registro_convocatoria = registro_fase_usuario.codigo_registro_convocatoria
							AND rfud.codigo_fase = 138
					)
				END) AS registro_fase_reto_3"
			)
		);

		$query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
			->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

		if( isset($filters['asignacion']) && !empty($filters['asignacion']) ){

			if($filters['asignacion'] == 'si'){
				$query->join('core.asignacion_marketplace', function($join){
					$join->on('registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_marketplace.codigo_registro_fase_usuario')
						->where('asignacion_marketplace.codigo_estado', Estado::getState('active'))
						->whereIn('asignacion_marketplace.codigo_registro_fase_usuario', function($query)
							{
								$query->select(DB::raw(' codigo_registro_fase_usuario
									FROM "core"."registro_fase_usuario"
									WHERE codigo_fase = 138 ')
								);
							});
				});
			}

			if($filters['asignacion'] == 'no'){
				$query->whereNotIn('registro_fase_usuario.codigo_registro_fase_usuario', function($query)
				{
					$query->select(DB::raw(' "asignacion_marketplace"."codigo_registro_fase_usuario"
						FROM "core"."asignacion_marketplace"
						INNER JOIN "core"."registro_fase_usuario" ON "asignacion_marketplace"."codigo_registro_fase_usuario" = "registro_fase_usuario"."codigo_registro_fase_usuario"
						WHERE codigo_fase = 138 AND "asignacion_marketplace"."codigo_estado" = '.Estado::getState('active').' ')
					);
				});
			}
		}

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));

		// States filter
		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->whereNotIn('registro_fase_usuario.codigo_estado', [Estado::getState('inactive')]);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("lower(trim(person.correo_electronico)) ilike lower(trim('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->whereIn('registro_fase_usuario.codigo_fase', explode(',',$filters['phase']));
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( (isset($filters['sortby']) && !empty($filters['sortby'])) && (isset($filters['scoring']) && !empty($filters['scoring'])) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 134
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 150
							), 0)
						)
				END) $sortby $scoring");
		}

		if( (isset($filters['sortby_2']) && !empty($filters['sortby_2'])) && (isset($filters['scoring_2']) && !empty($filters['scoring_2'])) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 134
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 150
							), 0)
						)
				END) $sortby_2 $scoring_2");
		}

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('registro_fase_documentador', function ($data) {
				return $data->registro_fase_documentador;
			})
			->addColumn('registro_fase_reto_3', function ($data) {
				return TuProyecto::encryptSrcParams([$data->registro_fase_reto_3]);
			})
			->addColumn('codigo_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}

	public static function getResultEvaluationGeneralAldea(Collection $arQuestions, $program) {
		$object = new \stdClass();

		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_usuario', 'registro_convocatoria.codigo_usuario_encuesta', 'fecha_registro_fase_usuario', 'hora_registro_fase_usuario', 'registro_fase_usuario.codigo_fase', 'convocatoria.codigo_convocatoria','codigo_orientacion_convocatoria', 'entity.nombre_usuario as nombre_proyecto', 'entity.identificacion_usuario as nit', 'nombre_clase_emprendimiento', 'numero_registro_convocatoria', 'genero_persona', 'person.fecha_nacimiento_usuario', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',
			// Person data
			DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"),
			DB::raw("person.correo_electronico as correo_electronico"),
			DB::raw('
				COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_encuesta = 49
				AND usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0) AS total_reto1
			')
		);

		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', Institucion::institution('innpulsa'))
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
		$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// Detalle Voucher Left Join
		$query->leftJoin('core.detalle_voucher', 'detalle_voucher.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		$query->where('detalle_voucher.codigo_estado', Estado::getState('finalized'));
		$query->orderBy('numero_registro_convocatoria', 'asc');
		$usuarios = $query->get();

		// Prepare header
		$matriz = [];
		$header = [];
		$header[] = 'Aplicación';
		$header[] = 'Email';
		$header[] = 'Usuario';
		$header[] = 'Genero';
		$header[] = 'Fecha de nacimiento';
		$header[] = 'Proyecto / Empresa';
		$header[] = 'Tipo';
		$header[] = 'Nit';
		$header[] = 'Fecha postulación';
		$header[] = 'Fecha registro';

		foreach ($arQuestions as $question) {
			if(!in_array($question->tipo_campo_pregunta, [6, 7])) {

				switch ($question->tipo_pregunta_encuesta) {

					case PreguntaEncuesta::getType('open'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						// Tipo ciudad
						if($question->tipo_campo_pregunta == 5){
							$header[] = 'Departamento';
						}
					break;

					case PreguntaEncuesta::getType('single'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						if($question->puntaje_pregunta_encuesta) {
							$header[] = 'Puntaje';
						}

					break;

					case PreguntaEncuesta::getType('matriz'):

						// Choices closed questions
						$matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						$matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
							   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$header[] = strip_tags("{$question->texto_pregunta_encuesta} {$choice->texto_opcion_encuesta} {$choicey->texto_opcion_encuesta}");
								}
							}
						}
					break;

					default:
						$header[] = strip_tags($question->texto_pregunta_encuesta);
					break;
				}
			}
		}

		// Preparar Respuestas
    	$query = RespuestaEncuesta::query();
		$query->select('opc_enc.texto_opcion_encuesta','opc_enc.puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta', 'cor_opc.tipo_campo_opcion');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->leftjoin('core.opcion_encuesta as opc_enc','respuesta_encuesta.codigo_opcion_encuesta','=','opc_enc.codigo_opcion_encuesta');
    	$query->leftjoin('core.opcion_encuesta as cor_opc','respuesta_encuesta.cor_codigo_opcion_encuesta', '=','cor_opc.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $usuarios->pluck('codigo_usuario_encuesta')->all() );
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		$allAnswers = $query->get();

		$answers = [];
		$itemOpcion = [];
		foreach ($allAnswers as $answer) {
			switch ($answer->tipo_pregunta_encuesta) {
				case PreguntaEncuesta::getType('open'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('single'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('multiple'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
				break;

				case PreguntaEncuesta::getType('upload'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('matriz'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
					if ($answer->tipo_campo_opcion  == TipoCampoPregunta::getTypeQuestion('select') && $answer->texto_respuesta_encuesta != null && is_numeric($answer->texto_respuesta_encuesta) ) {
            $queryItemOption = ItemOpcionEncuesta::query();
            $queryItemOption->select('item_opcion_encuesta.codigo_item_opcion_encuesta', 'item_opcion_encuesta.codigo_opcion_encuesta', 'item_opcion_encuesta.nombre_item_opcion_encuesta');
            $queryItemOption->where('item_opcion_encuesta.codigo_opcion_encuesta', '=', $answer->cor_codigo_opcion_encuesta);
            $queryItemOption->where('item_opcion_encuesta.codigo_item_opcion_encuesta', '=', $answer->texto_respuesta_encuesta);
            $resultItemOption = $queryItemOption->get();
            if ($resultItemOption->count() > 0) {
              foreach ($resultItemOption as $item) {
                $itemOpcion["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $item;
              }
            }
          }
				break;
			}
		}

		$item = 1;
		$data = collect([]);
		foreach ($usuarios as $usuario)
		{
			unset($record);
			$record[] = $usuario->numero_registro_convocatoria;
			$record[] = $usuario->correo_electronico;
			$record[] = $usuario->nombre_usuario;
			$record[] =  isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
			$record[] = $usuario->fecha_nacimiento_usuario;
			$record[] = $usuario->nombre_proyecto;
			$record[] = $usuario->nombre_clase_emprendimiento;
			$record[] = $usuario->nit;
			$record[] = $usuario->fecha_registro_convocatoria.' '.$usuario->hora_registro_convocatoria;
			$record[] = $usuario->fecha_registro_fase_usuario.' '.$usuario->hora_registro_fase_usuario;

			// Questions Survey
			foreach ($arQuestions as $question)
			{
				if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
				{
					switch ($question->tipo_pregunta_encuesta)
					{
						case PreguntaEncuesta::getType('open'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							// Tipo ciudad
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')) {
								if($answer instanceof RespuestaEncuesta) {
									$key = "{$answer->texto_respuesta_encuesta}";
									$city = isset($cities[$key]) ? $cities[$key] : '';
									if($city instanceof Ciudad){
										$respuesta = $city->distrito_ciudad;
										$record[] = $city->nombre_ciudad;
									}
								}else{
									$record[] = '';
								}
							}
							// Tipo Sector interes
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';
							}
							// Tipo nivel educación
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('single'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_opcion_encuesta;
							}
							$record[] = $respuesta;
							if($question->puntaje_pregunta_encuesta) {
								$puntajeOpcion = "";
								if($answer instanceof RespuestaEncuesta) {
									$puntajeOpcion = $answer->puntaje_opcion_encuesta;
								}
								$record[] = $puntajeOpcion;
							}

						break;

						case PreguntaEncuesta::getType('multiple'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answerop = isset($answers[$key]) ? $answers[$key] : [];
							foreach ($answerop as $option) {
								if($option instanceof RespuestaEncuesta){
									$respuesta.= "{$option->texto_opcion_encuesta} ";
								}
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('upload'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('matriz'):

							if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
								foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
								   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
										$respuesta = '';
										$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
										$answer = isset($answers[$key]) ? $answers[$key] : '';
										if($answer instanceof RespuestaEncuesta){
										  if(isset($itemOpcion[$key])){
                        $item_o = isset($itemOpcion[$key]) ? $itemOpcion[$key] : '';
                        if($item_o instanceof ItemOpcionEncuesta){
                          $respuesta = $item_o->nombre_item_opcion_encuesta;
                        }
                      }else{
                        $respuesta = $answer->texto_respuesta_encuesta;
                      }
										}
										$record[] = $respuesta;
									}
								}
							}
						break;
					}
				}
			}
			$data[] = $record;
		}

		$object->headers = $header;
		$object->data = $data;

		return $object;
	}

	public static function getResultExportAldea(Collection $arQuestions, $program, $filters = [])
	{
		$object = new \stdClass();

		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_usuario', 'registro_convocatoria.codigo_usuario_encuesta', 'fecha_registro_fase_usuario', 'hora_registro_fase_usuario', 'registro_fase_usuario.codigo_fase', 'convocatoria.codigo_convocatoria','codigo_orientacion_convocatoria', 'entity.nombre_usuario as nombre_proyecto', 'entity.identificacion_usuario as nit', 'nombre_clase_emprendimiento', 'numero_registro_convocatoria', 'genero_persona', 'person.fecha_nacimiento_usuario', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',

		// Person data
		DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"),
		DB::raw("person.correo_electronico as correo_electronico"),

		DB::raw('
				COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_encuesta = 49
						AND usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0) AS total_reto1
			'),
		DB::raw('
				COALESCE(
			 		(
			 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
			 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
			 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
						WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
						AND resultado_evaluacion_emprendimiento.codigo_estado = 8
						AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
						AND rfe.codigo_fase = 150
					), 0) AS total_reto1_2
			')
		);

		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', Institucion::institution('innpulsa'))
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
		$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
//    $query->where('registro_convocatoria.codigo_registro_convocatoria', '=', 9601);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( isset($filters['sortby']) && !empty($filters['sortby']) && isset($filters['scoring']) && !empty($filters['scoring']) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 134
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 150
							), 0)
						)
				END)$sortby $scoring");
		}

		if( isset($filters['sortby_2']) && !empty($filters['sortby_2']) && isset($filters['scoring_2']) && !empty($filters['scoring_2']) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 134
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 150
							), 0)
						)
				END)$sortby_2 $scoring_2");
		}

		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		$query->orderBy('numero_registro_convocatoria', 'asc');
		$usuarios = $query->get();

		// Prepare header
		$matriz = [];
		$header = [];
		$header[] = 'Aplicación';
		$header[] = 'Email';
		$header[] = 'Usuario';
		$header[] = 'Genero';
		$header[] = 'Fecha de nacimiento';
		$header[] = 'Proyecto / Empresa';
		$header[] = 'Tipo';
		$header[] = 'Nit';
		$header[] = 'Fecha postulación';
		$header[] = 'Fecha registro';

		// Build headers
		foreach ($arQuestions as $question) {
			if(!in_array($question->tipo_campo_pregunta, [6, 7])) {

				switch ($question->tipo_pregunta_encuesta) {

					case PreguntaEncuesta::getType('open'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						// Tipo ciudad
						if($question->tipo_campo_pregunta == 5){
							$header[] = 'Departamento';
						}
					break;

					case PreguntaEncuesta::getType('single'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						if($question->puntaje_pregunta_encuesta) {
							$header[] = 'Puntaje';
						}

					break;

					case PreguntaEncuesta::getType('matriz'):

						// Choices closed questions
						$matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						$matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
							   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$header[] = strip_tags("{$question->texto_pregunta_encuesta} {$choice->texto_opcion_encuesta} {$choicey->texto_opcion_encuesta}");
								}
							}
						}
					break;

					default:
						$header[] = strip_tags($question->texto_pregunta_encuesta);
					break;
				}
			}
		}

		$header[] ="Puntaje primer reto";
		$header[] ="Puntaje reto 1.2";

		// Preparar Respuestas
    	$query = RespuestaEncuesta::query();
		$query->select('opc_enc.texto_opcion_encuesta','opc_enc.puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta', 'cor_opc.tipo_campo_opcion');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->leftjoin('core.opcion_encuesta as opc_enc','respuesta_encuesta.codigo_opcion_encuesta','=','opc_enc.codigo_opcion_encuesta');
    	$query->leftjoin('core.opcion_encuesta as cor_opc','respuesta_encuesta.cor_codigo_opcion_encuesta', '=','cor_opc.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $usuarios->pluck('codigo_usuario_encuesta')->all() );
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		$allAnswers = $query->get();

		$answers = [];
		$itemOpcion = [];
		foreach ($allAnswers as $answer) {
			switch ($answer->tipo_pregunta_encuesta) {
				case PreguntaEncuesta::getType('open'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('single'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('multiple'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
				break;

				case PreguntaEncuesta::getType('upload'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('matriz'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
					if ($answer->tipo_campo_opcion  == TipoCampoPregunta::getTypeQuestion('select') && $answer->texto_respuesta_encuesta != null && is_numeric($answer->texto_respuesta_encuesta) ) {
            $queryItemOption = ItemOpcionEncuesta::query();
            $queryItemOption->select('item_opcion_encuesta.codigo_item_opcion_encuesta', 'item_opcion_encuesta.codigo_opcion_encuesta', 'item_opcion_encuesta.nombre_item_opcion_encuesta');
            $queryItemOption->where('item_opcion_encuesta.codigo_opcion_encuesta', '=', $answer->cor_codigo_opcion_encuesta);
            $queryItemOption->where('item_opcion_encuesta.codigo_item_opcion_encuesta', '=', $answer->texto_respuesta_encuesta);
            $resultItemOption = $queryItemOption->get();
            if ($resultItemOption->count() > 0) {
              foreach ($resultItemOption as $item) {
                $itemOpcion["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $item;
              }
            }
          }
				break;
			}
		}
//		dd($itemOpcion);
		$cities = [];
		$citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
		foreach ($citys as $city) {
			 $cities["{$city->codigo_ciudad}"] = $city;
		}
		// Preparar sectores
		$sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

		// Preparar Nivel de educación
		$levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

		$item = 1;
		$data = collect([]);
		foreach ($usuarios as $usuario)
		{
			unset($record);
			$record[] = $usuario->numero_registro_convocatoria;
			$record[] = $usuario->correo_electronico;
			$record[] = $usuario->nombre_usuario;
			$record[] =  isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
			$record[] = $usuario->fecha_nacimiento_usuario;
			$record[] = $usuario->nombre_proyecto;
			$record[] = $usuario->nombre_clase_emprendimiento;
			$record[] = $usuario->nit;
			$record[] = $usuario->fecha_registro_convocatoria.' '.$usuario->hora_registro_convocatoria;
			$record[] = $usuario->fecha_registro_fase_usuario.' '.$usuario->hora_registro_fase_usuario;

			// Questions Survey
			foreach ($arQuestions as $question)
			{
				if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
				{
					switch ($question->tipo_pregunta_encuesta)
					{
						case PreguntaEncuesta::getType('open'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							// Tipo ciudad
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')) {
								if($answer instanceof RespuestaEncuesta) {
									$key = "{$answer->texto_respuesta_encuesta}";
									$city = isset($cities[$key]) ? $cities[$key] : '';
									if($city instanceof Ciudad){
										$respuesta = $city->distrito_ciudad;
										$record[] = $city->nombre_ciudad;
									}
								}else{
									$record[] = '';
								}
							}
							// Tipo Sector interes
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';
							}
							// Tipo nivel educación
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('single'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_opcion_encuesta;
							}
							$record[] = $respuesta;
							if($question->puntaje_pregunta_encuesta) {
								$puntajeOpcion = "";
								if($answer instanceof RespuestaEncuesta) {
									$puntajeOpcion = $answer->puntaje_opcion_encuesta;
								}
								$record[] = $puntajeOpcion;
							}

						break;

						case PreguntaEncuesta::getType('multiple'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answerop = isset($answers[$key]) ? $answers[$key] : [];
							foreach ($answerop as $option) {
								if($option instanceof RespuestaEncuesta){
									$respuesta.= "{$option->texto_opcion_encuesta} ";
								}
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('upload'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('matriz'):

							if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
								foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
								   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
										$respuesta = '';
										$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
										$answer = isset($answers[$key]) ? $answers[$key] : '';
										if($answer instanceof RespuestaEncuesta){
										  if(isset($itemOpcion[$key])){
                        $item_o = isset($itemOpcion[$key]) ? $itemOpcion[$key] : '';
                        if($item_o instanceof ItemOpcionEncuesta){
                          $respuesta = $item_o->nombre_item_opcion_encuesta;
                        }
                      }else{
                        $respuesta = $answer->texto_respuesta_encuesta;
                      }
										}
										$record[] = $respuesta;
									}
								}
							}
						break;
					}
				}
			}
			$record[] = $usuario->total_reto1;
			$record[] = $usuario->total_reto1_2;
			$data[] = $record;
		}
		$object->headers = $header;
		$object->data = $data;

		return $object;
	}

	public static function getResultExportAldea20(Collection $arQuestions, $program, $filters = [])
	{
		$object = new \stdClass();

		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_usuario', 'sector_interes.nombre_sector_interes', 'sector_interes.codigo_sector_interes', 'registro_convocatoria.codigo_usuario_encuesta', 'fecha_registro_fase_usuario', 'hora_registro_fase_usuario', 'registro_fase_usuario.codigo_fase', 'convocatoria.codigo_convocatoria','codigo_orientacion_convocatoria', 'entity.nombre_usuario as nombre_proyecto', 'entity.identificacion_usuario as nit', 'nombre_clase_emprendimiento', 'numero_registro_convocatoria', 'genero_persona', 'person.fecha_nacimiento_usuario', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',

		// Person data
		DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"),
		DB::raw("person.correo_electronico as correo_electronico"),

		DB::raw('
				COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_encuesta = 49
						AND usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0) AS total_reto1
			'),
		DB::raw('
				COALESCE(
					(
						SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
						JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
						JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
						WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 9
						AND resultado_evaluacion_emprendimiento.codigo_estado = 8
						AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
						AND rfe.codigo_fase = 150
					), 0) AS total_reto1_2
			')
		);

		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->leftJoin('core.sector_interes', 'sector_interes.codigo_sector_interes', '=', 'entidad.codigo_sector_interes');

		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', Institucion::institution('innpulsa'))
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
		$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( isset($filters['sortby']) && !empty($filters['sortby']) && isset($filters['scoring']) && !empty($filters['scoring']) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
				WHEN 253
					THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
						(
								(puntaje_registro_fase.valor_puntaje_registro_fase)
							), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta))
						)
				END)$sortby $scoring");
		}

		if( isset($filters['sortby_2']) && !empty($filters['sortby_2']) && isset($filters['scoring_2']) && !empty($filters['scoring_2']) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 253
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
							(
								(puntaje_registro_fase.valor_puntaje_registro_fase)
							), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta))
						)
				END)$sortby_2 $scoring_2");
		}

		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('pendinginstitution'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		$query->orderBy('numero_registro_convocatoria', 'asc');
		$usuarios = $query->get();

		// Prepare header
		$matriz = [];
		$header = [];
		$header[] = 'Aplicacion';
		$header[] = 'Mail_0';
		$header[] = 'Usuario';
		$header[] = 'Genero_0';
		$header[] = 'Nacimiento_0';
		$header[] = 'RazonSocial';
		$header[] = 'Sector';
		$header[] = 'IdEmpres';
		$header[] = 'CIIU_0';
		$header[] = 'Nit';
		$header[] = 'Postula';
		$header[] = 'Registro';

		// Build headers
		foreach ($arQuestions as $question) {
			if(!in_array($question->tipo_campo_pregunta, [6, 7])) {

				switch ($question->tipo_pregunta_encuesta) {

					case PreguntaEncuesta::getType('open'):
						$header[] = isset($question->nemotecnico_pregunta_encuesta) ? strip_tags($question->nemotecnico_pregunta_encuesta) : strip_tags($question->texto_pregunta_encuesta);
						// Tipo ciudad
						if($question->tipo_campo_pregunta == 5){
							$header[] = isset($question->nemotecnico_pregunta_encuesta) ? strip_tags($question->nemotecnico_pregunta_encuesta."_DEPTO") : 'Departamento';
						}
					break;

					case PreguntaEncuesta::getType('single'):
						$header[] = isset($question->nemotecnico_pregunta_encuesta) ? strip_tags($question->nemotecnico_pregunta_encuesta) : strip_tags($question->texto_pregunta_encuesta);
						if($question->puntaje_pregunta_encuesta) {
							$header[] = isset($question->nemotecnico_pregunta_encuesta) ? strip_tags($question->nemotecnico_pregunta_encuesta) : 'Puntaje';
						}

					break;

					case PreguntaEncuesta::getType('matriz'):
						// Choices closed questions
						$matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

							$matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
							   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$header[] = strip_tags(isset($question->nemotecnico_pregunta_encuesta) ? $question->nemotecnico_pregunta_encuesta.trim($choice->nemotecnico_opcion_encuesta).trim($choicey->nemotecnico_opcion_encuesta) : $question->texto_pregunta_encuesta."{$choice->texto_opcion_encuesta} {$choicey->texto_opcion_encuesta}");
								}
							}
						}
					break;

					default:
						$header[] = isset($question->nemotecnico_pregunta_encuesta) ? strip_tags($question->nemotecnico_pregunta_encuesta) : strip_tags($question->texto_pregunta_encuesta);
					break;
				}
			}
		}

		// Preparar Respuestas
		$query = RespuestaEncuesta::query();
		$query->select('opc_enc.texto_opcion_encuesta','opc_enc.puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta', 'cor_opc.tipo_campo_opcion', 'opc_enc.tipo_campo_opcion as opcion_y');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->leftjoin('core.opcion_encuesta as opc_enc','respuesta_encuesta.codigo_opcion_encuesta','=','opc_enc.codigo_opcion_encuesta');
		$query->leftjoin('core.opcion_encuesta as cor_opc','respuesta_encuesta.cor_codigo_opcion_encuesta', '=','cor_opc.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $usuarios->pluck('codigo_usuario_encuesta')->all() );
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		$allAnswers = $query->get();

		$answers = [];
		$itemOpcion = [];
		foreach ($allAnswers as $answer) {
			switch ($answer->tipo_pregunta_encuesta) {
				case PreguntaEncuesta::getType('open'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('single'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('multiple'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
				break;

				case PreguntaEncuesta::getType('upload'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('matriz'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;

					if ( $answer->tipo_campo_opcion  == TipoCampoPregunta::getTypeQuestion('select') && $answer->texto_respuesta_encuesta != null && is_numeric($answer->texto_respuesta_encuesta) ) {
						$queryItemOption = ItemOpcionEncuesta::query();
						$queryItemOption->select('item_opcion_encuesta.codigo_item_opcion_encuesta', 'item_opcion_encuesta.codigo_opcion_encuesta', 'item_opcion_encuesta.nombre_item_opcion_encuesta');
						$queryItemOption->where('item_opcion_encuesta.codigo_opcion_encuesta', '=', $answer->cor_codigo_opcion_encuesta);
						$queryItemOption->where('item_opcion_encuesta.codigo_item_opcion_encuesta', '=', (int)$answer->texto_respuesta_encuesta);
						$resultItemOption = $queryItemOption->get();
						if ($resultItemOption->count() > 0) {
							foreach ($resultItemOption as $item) {
								$itemOpcion["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $item;
							}
						}
					}

					if ( $answer->opcion_y == TipoCampoPregunta::getTypeQuestion('select') ) {
						$queryItem = ItemOpcionEncuesta::select('item_opcion_encuesta.codigo_item_opcion_encuesta', 'item_opcion_encuesta.codigo_opcion_encuesta', 'item_opcion_encuesta.nombre_item_opcion_encuesta')->where('codigo_item_opcion_encuesta', (int)$answer->texto_respuesta_encuesta)->first();
						$item = null;
						if (!$queryItem instanceof ItemOpcionEncuesta) {
							$item = null;
						} else {
							$item = $queryItem;
							$itemOpcion["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $item;
						}
					}
				break;
			}
		}
//		dd($itemOpcion);
		$cities = [];
		$citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
		foreach ($citys as $city) {
			 $cities["{$city->codigo_ciudad}"] = $city;
		}
		// Preparar sectores
		$sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

		// Preparar Nivel de educación
		$levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

		$item = 1;
		$data = collect([]);
		foreach ($usuarios as $usuario)
		{
			unset($record);
			$record[] = $usuario->numero_registro_convocatoria;
			$record[] = $usuario->correo_electronico;
			$record[] = $usuario->nombre_usuario;
			$record[] =  isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
			$record[] = $usuario->fecha_nacimiento_usuario;
			$record[] = $usuario->nombre_proyecto;
			$record[] = $usuario->nombre_sector_interes;
			$record[] = $usuario->nombre_clase_emprendimiento;
			$record[] = isset($usuario->nombre_sector_interes) ? $usuario->codigo_sector_interes.' - '.$usuario->nombre_sector_interes : '';
			$record[] = $usuario->nit;
			$record[] = $usuario->fecha_registro_convocatoria.' '.$usuario->hora_registro_convocatoria;
			$record[] = $usuario->fecha_registro_fase_usuario.' '.$usuario->hora_registro_fase_usuario;

			// Questions Survey
			foreach ($arQuestions as $question)
			{
				if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
				{
					switch ($question->tipo_pregunta_encuesta)
					{
						case PreguntaEncuesta::getType('open'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							// Tipo ciudad
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')) {
								if($answer instanceof RespuestaEncuesta) {
									$key = "{$answer->texto_respuesta_encuesta}";
									$city = isset($cities[$key]) ? $cities[$key] : '';
									if($city instanceof Ciudad){
										$respuesta = $city->distrito_ciudad;
										$record[] = $city->nombre_ciudad;
									}
								}else{
									$record[] = '';
								}
							}
							// Tipo Sector interes
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';
							}
							// Tipo nivel educación
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('single'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_opcion_encuesta;
							}
							$record[] = $respuesta;
							if($question->puntaje_pregunta_encuesta) {
								$puntajeOpcion = "";
								if($answer instanceof RespuestaEncuesta) {
									$puntajeOpcion = $answer->puntaje_opcion_encuesta;
								}
								$record[] = $puntajeOpcion;
							}

						break;

						case PreguntaEncuesta::getType('multiple'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answerop = isset($answers[$key]) ? $answers[$key] : [];
							foreach ($answerop as $option) {
								if($option instanceof RespuestaEncuesta){
									$respuesta.= "{$option->texto_opcion_encuesta} ";
								}
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('upload'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('matriz'):

							if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
								foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
								   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
										$respuesta = '';
										$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
										$answer = isset($answers[$key]) ? $answers[$key] : '';
										if($answer instanceof RespuestaEncuesta){
										  if(isset($itemOpcion[$key])){
                        $item_o = isset($itemOpcion[$key]) ? $itemOpcion[$key] : '';
                        if($item_o instanceof ItemOpcionEncuesta){
                          $respuesta = $item_o->nombre_item_opcion_encuesta;
                        }
                      }else{
                        $respuesta = $answer->texto_respuesta_encuesta;
                      }
										}
										$record[] = $respuesta;
									}
								}
							}
						break;
					}
				}
			}
			$record[] = $usuario->total_reto1;
			$record[] = $usuario->total_reto1_2;
			$data[] = $record;
		}
		$object->headers = $header;
		$object->data = $data;

		return $object;
	}

	public static function getStatisticsMarketplace()
	{
		$statistics = [];

		// Presupuesto
		$estimatevalue = Voucher::where('codigo_estado', Estado::getState('active'))->where('codigo_institucion', 51)->sum('valor_voucher');
		$estimate = new \stdClass();
		$estimate->title = 'Presupuesto';
		$estimate->icon = 'fa fa-dollar';
		$estimate->value = number_format($estimatevalue, 0,',','.');
		$statistics[] = $estimate;

		// Keys
		$keysvalue = RegistroFaseUsuario::where('codigo_fase', self::$stage_challenge_2)->sum('valor_asignacion_rfu');
		$keys = new \stdClass();
		$keys->title = 'Llaves asignadas';
		$keys->icon = 'fa fa-key';
		$keys->value = number_format($keysvalue, 0,',','.');
		$statistics[] = $keys;

		// Purchases
		$purchasesvalue = Compra::join('core.consumo_voucher', 'consumo_voucher.codigo_compra', '=', 'compra.codigo_compra')->sum('valor_total_compra');

		$purchases = new \stdClass();
		$purchases->title = 'Compras realizadas';
		$purchases->icon = 'fa fa-shopping-cart';
		$purchases->value = number_format($purchasesvalue,0,',','.');
		$statistics[] = $purchases;

		// Pay out
		$payout = new \stdClass();
		$payout->title = 'Total desembolsado';
		$payout->icon = 'fa fa-users';
		$payout->value = number_format(0, 0,',','.');
		$statistics[] = $payout;

		return $statistics;
	}

	public static function notificationLetterEngagement($codigo_registro_fase, $businessman)
	{
		// Send notification
		\Log::info('Correo de notificación carta compromiso para innpulsa');
		$data = [];
		$data['_template'] = 'emails.program.innpulsa.notificacionliderr3';
		$data['_inst'] = session('institution')->codigo_institucion;
		$data['_subject'] = 'Notificación carta de compromiso';
		$data['_to'] = self::$email_user_institution;
		$data['_to_name'] = 'Darlin Rodriguez';
		$data['_url_action'] = route('marketplace.asignaciones.show', $codigo_registro_fase);
		$data['comment'] = trans('program.mail.body.letter.engagement', ['businessman' => ucwords( strtolower($businessman->user_name) )]);
		SendEmail::notification($data);
	}

	public static function sendEmailConfirmDeliverables(Compra $purchase, Usuario $purchaser, Usuario $seller, Producto $product, $comment, $reject_deliverables = null, $awsActaAprobacion, $ActaAprobacionPago = null)
	{
		// Send notification
		$data = [];
		$data['_inst'] = session('institution')->codigo_institucion;
		$data['_to'] = $seller->correo_electronico;
		$data['_to_name'] = ucwords(strtolower($seller->nombre_usuario));
		$data['_project'] = ucwords(strtolower($product->nombre_producto));
		$data['_purchaser'] = ucwords(strtolower($purchaser->nombre_usuario));

		if(!is_null($reject_deliverables)){
			$data['_subject'] = 'Devolución entregables';
			$data['_template'] = 'emails.agreement.deliverablerefused';
			$data['_comment'] = $comment;
			$data['_url_action']  = '/marketplace/ventas/entregables/'.TuProyecto::encryptSrcParams([$purchase->codigo_compra]);
		}else{
			$data['_subject'] = 'Aceptación entregables';
			$data['_template'] = 'emails.agreement.deliverableapprove';
      $data['_urldocumentapprove'] = $awsActaAprobacion['ObjectURL'];
			$data['_urlagreementaprove'] = $ActaAprobacionPago ?  $ActaAprobacionPago->direccion_adjunto : null;
		}

		SendEmail::notification($data);
	}

	public static function report(UsuarioEncuesta $userSurvey, &$data)
	{
		// Init Graph
		$graph = new JpGraph();
		$graph->load();
		$graph->module('radar');

		$query = UsuarioEncuesta::query();
		$query->select('usuario.nombre_usuario', 'usuario.imagen_usuario', 'usuario.biografia_usuario', 'nombre_categoria_proyecto', DB::raw("COALESCE(initcap(ciudad.nombre_ciudad), '') as nombre_ciudad"),
			DB::raw("COALESCE(initcap(ciudad.distrito_ciudad), '') as distrito_ciudad"),
			DB::raw("(CASE WHEN founder.tipo_usuario = ".Usuario::typeUser('person')." THEN (trim(founder.nombre_usuario) || ' ' || trim(personaf.primer_apellido_persona) || ' ' || trim(personaf.segundo_apellido_persona)) ELSE founder.nombre_usuario END) as nombre_founder"),
			DB::raw("trim(initcap(ciudadf.nombre_ciudad)) as ciudad_founder"),
			DB::raw("COALESCE(initcap(ciudadf.distrito_ciudad), '') as distrito_ciudad_founder")
		);
		$query->join('seguridad.usuario', 'usuario_encuesta.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario');
		$query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
		$query->join('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');
		$query->leftjoin('core.ciudad as ciudadf', 'founder.codigo_ciudad', '=', 'ciudadf.codigo_ciudad');
		$query->leftjoin('core.pais as paisf', 'ciudadf.codigo_pais', '=', 'paisf.codigo_pais');
		$query->leftjoin('core.persona as personaf', 'personaf.codigo_usuario', '=', 'founder.codigo_usuario');
		$query->where('codigo_usuario_encuesta', '=', $userSurvey->codigo_usuario_encuesta);
		$proyect = $query->first();

		$user = Usuario::find($userSurvey->codigo_usuario);
        if(!$user instanceof Usuario) {
        	abort(403);
        }

	 	//Obtiene las respuestas de la encuesta
		$answers = RespuestaEncuesta::getUserResponseSurveyCustom($userSurvey);

		$report = new \stdClass();
		switch ($userSurvey->codigo_encuesta) {
			case self::$challenge:
				$report->identificacion_usuario = $user->identificacion_usuario;
				$report->report = self::aldeaReport($answers, $userSurvey);
            break;
            case self::$challenge_empodera:
				$report->identificacion_usuario = $user->identificacion_usuario;
				$report->report = self::aldeaReport($answers, $userSurvey);
            break;
		}


		// call api report
		// $user_survey = TuProyecto::encryptSrcParams([$userSurvey->codigo_usuario_encuesta]);
		/* $user_survey = $userSurvey->codigo_usuario_encuesta;
		$api_url = sprintf('%s/api/convocatoria/informe/%s', (App::environment('local') ? 'http://localhost/plataforma/public' : session('institution')->url_institucion), $user_survey);

		$client = new Client;
		$report = $client->request('GET', $api_url)->getBody()->getContents();
		$report = json_decode($report); */








		$titles = [utf8_decode("Gestión administrativa"), utf8_decode("Gestión de la producción"), utf8_decode("Gestión comercial"), utf8_decode("Gestión financiera"), utf8_decode("Gestión humana y cultura de innovación")];

		// Score
		$score = array($report->report->data_admin_management->score, $report->report->data_production_management->score, $report->report->data_commercial_management->score, $report->report->data_financial_management->score, $report->report->data_human_management->score);

		// General score
		$generalScore = array($report->report->data_admin_management->score_general, $report->report->data_production_management->score_general, $report->report->data_commercial_management->score_general, $report->report->data_financial_management->score_general, $report->report->data_human_management->score_general);

		// Economy score
		$economyScore = array($report->report->data_admin_management->score_economy, $report->report->data_production_management->score_economy, $report->report->data_commercial_management->score_economy, $report->report->data_financial_management->score_economy, $report->report->data_human_management->score_economy);


		$graph = new RadarGraph (950,400);
		$graph->img->SetImgFormat('jpeg');
		$graph->title->Set(utf8_decode("Resultado por área de la empresa"));

		$graph->SetTitles($titles);
		$graph->SetCenter(0.5,0.55);
		$graph->HideTickMarks();
		$graph->axis->SetColor('darkgray@0.7');
		$graph->grid->Show();

		$graph->SetGridDepth(DEPTH_BACK);

		// Score plot
		$scorePlot = new RadarPlot($score);
		$scorePlot->SetColor('blue@0.1');
		$scorePlot->SetLineWeight(1);
		$scorePlot->SetFillColor('blue@0.9');
		$scorePlot->mark->SetType(MARK_IMG_SBALL,'navy');

		// General score plot
		$generalScorePlot = new RadarPlot($generalScore);
		$generalScorePlot->SetColor('darkgray@0.1');
		$generalScorePlot->SetLineWeight(1);
		$generalScorePlot->SetFillColor('darkgray@0.7');
		$generalScorePlot->mark->SetType(MARK_IMG_SBALL,'darkgray');

		// Economy score plot
		$economyScorePlot = new RadarPlot($economyScore);
		$economyScorePlot->SetColor('brown@0.1');
		$economyScorePlot->SetLineWeight(1);
		$economyScorePlot->SetFillColor('brown@0.9');
		$economyScorePlot->mark->SetType(MARK_IMG_SBALL,'red');

		$graph->Add($scorePlot);
		$graph->Add($generalScorePlot);
		$graph->Add($economyScorePlot);

        // Prepare file
        $filename = sprintf('%s_%s.jpeg', 'image_result', $userSurvey->codigo_usuario_encuesta);
        $filepath = sprintf('%s/temp/%s', public_path(), $filename);

		// Save temp file
		$graph->Stroke(_IMG_HANDLER);
		$graph->img->Stream($filepath);

		$data['title'] = $proyect->nombre_usuario;
		$data['graph'] = asset("temp/$filename");
		$data['proyect'] = $proyect;
		$data['report'] = $report->report;
	}

	public static function getParticipantsEmpodera(Convocatoria $program, array $filters = [])
	{
		$participants = [];
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', 'registro_convocatoria.codigo_usuario_encuesta', "fase.codigo_evaluacion_emprendimiento", "observaciones_registro_fase_usuario", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "registro_fase_usuario.codigo_fase", "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "codigo_orientacion_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'url_encuesta', 'encuesta.codigo_encuesta', 'nombre_estado',
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("initcap(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END) as nombre_usuario"), 'nombre_fase', 'numero_registro_convocatoria',
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND asignacion_jurado.codigo_estado = 15
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END) AS total_evaluation"
			),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 213 THEN registro_fase_usuario.codigo_registro_fase_usuario
					ELSE (
						SELECT rfud.codigo_registro_fase_usuario
						FROM core.registro_fase_usuario as rfud
						WHERE rfud.codigo_usuario = registro_fase_usuario.codigo_usuario
							AND rfud.codigo_registro_convocatoria = registro_fase_usuario.codigo_registro_convocatoria
							AND rfud.codigo_fase = 213
					)
				END) AS registro_fase_documentador"
			),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 214 THEN registro_fase_usuario.codigo_registro_fase_usuario
					ELSE (
						SELECT rfud.codigo_registro_fase_usuario
						FROM core.registro_fase_usuario as rfud
						WHERE rfud.codigo_usuario = registro_fase_usuario.codigo_usuario
							AND rfud.codigo_registro_convocatoria = registro_fase_usuario.codigo_registro_convocatoria
							AND rfud.codigo_fase = 214
					)
				END) AS registro_fase_reto_3"
			)
		);

		$query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');

		if( isset($filters['asignacion']) && !empty($filters['asignacion']) ){
			if($filters['asignacion'] == 'si'){
				$query->join('core.asignacion_marketplace', function($join){
					$join->on('registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_marketplace.codigo_registro_fase_usuario')
						->where('asignacion_marketplace.codigo_estado', Estado::getState('active'))
						->whereIn('asignacion_marketplace.codigo_registro_fase_usuario', function($query)
							{
								$query->select(DB::raw(' codigo_registro_fase_usuario
									FROM "core"."registro_fase_usuario"
									WHERE codigo_fase = 214 ')
								);
							});
				});
			}

			if($filters['asignacion'] == 'no'){
				$query->whereNotIn('registro_fase_usuario.codigo_registro_fase_usuario', function($query)
				{
					$query->select(DB::raw(' "asignacion_marketplace"."codigo_registro_fase_usuario"
						FROM "core"."asignacion_marketplace"
						INNER JOIN "core"."registro_fase_usuario" ON "asignacion_marketplace"."codigo_registro_fase_usuario" = "registro_fase_usuario"."codigo_registro_fase_usuario"
						WHERE codigo_fase = 214 AND "asignacion_marketplace"."codigo_estado" = '.Estado::getState('active').' ')
					);
				});
			}
		}

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));

		// States filter
		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->whereNotIn('registro_fase_usuario.codigo_estado', [Estado::getState('inactive')]);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("lower(trim(person.correo_electronico)) ilike lower(trim('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->whereIn('registro_fase_usuario.codigo_fase', explode(',',$filters['phase']));
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( (isset($filters['sortby']) && !empty($filters['sortby'])) && (isset($filters['scoring']) && !empty($filters['scoring'])) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END) $sortby $scoring");
		}

		if( (isset($filters['sortby_2']) && !empty($filters['sortby_2'])) && (isset($filters['scoring_2']) && !empty($filters['scoring_2'])) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END) $sortby_2 $scoring_2");
		}

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('registro_fase_documentador', function ($data) {
				return $data->registro_fase_documentador;
			})
			->addColumn('registro_fase_reto_3', function ($data) {
				return TuProyecto::encryptSrcParams([$data->registro_fase_reto_3]);
			})
			->addColumn('codigo_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}

	public static function getParticipantsEmpodera2020(Convocatoria $program, array $filters = []) {
		$participants = [];
		$query = RegistroFaseUsuario::query();
		$query->select('encuesta.codigo_encuesta', 'registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario", "registro_fase_usuario.codigo_registro_convocatoria",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'nombre_estado',
			DB::raw("initcap((CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END)) as nombre_usuario"), 'numero_registro_convocatoria', 'fase.codigo_fase', 'nombre_fase',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw("COALESCE((sum(resultado_evaluacion_emprendimiento.puntaje_total_eval_emprendi + usuario_encuesta.puntaje_total_encuesta)), usuario_encuesta.puntaje_total_encuesta) as puntaje"),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 251 THEN registro_fase_usuario.codigo_registro_fase_usuario
					ELSE (
						SELECT rfud.codigo_registro_fase_usuario
						FROM core.registro_fase_usuario as rfud
						WHERE rfud.codigo_usuario = registro_fase_usuario.codigo_usuario
							AND rfud.codigo_registro_convocatoria = registro_fase_usuario.codigo_registro_convocatoria
							AND rfud.codigo_fase = 251
					)
				END) AS registro_fase_reto_3"
			)
		);

		$query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User / Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->leftJoin('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
		$query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
		$query->leftJoin('core.usuario_encuesta', 'usuario_encuesta.codigo_usuario_encuesta', '=', 'registro_convocatoria.codigo_usuario_encuesta');

		// City / Country
		$query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query-> where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query-> where('convocatoria.codigo_estado', Estado::getState('active'));
		// States filter
		$query-> whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('linked'), Estado::getState('returned'), Estado::getState('rejected')
		]);

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['emprendimiento']) && !empty($filters['emprendimiento'])) {
			$query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if(isset($filters['city']) && !empty($filters['city'])){
			$query->where('person.codigo_ciudad', $filters['city']);
		}

		if(isset($filters['phase']) && !empty($filters['phase'])){
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if(isset($filters['state']) && !empty($filters['state'])){
			$query->where('registro_fase_usuario.codigo_estado', '=', $filters['state']);
		}

		$query->groupBy('usuario_encuesta.puntaje_total_encuesta', 'nombre_estado', 'fase.codigo_fase', 'person.tipo_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'registro_convocatoria.numero_registro_convocatoria', 'ciudad.nombre_ciudad', 'ciudad.distrito_ciudad', 'pais.codigo_pais', 'person.nombre_usuario', 'encuesta.codigo_encuesta', 'registro_fase_usuario.codigo_registro_fase_usuario', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario", "entity.nombre_usuario", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico");

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
		->make(true);
	}

	public static function getParticipantsAldea20(Convocatoria $program, array $filters = []) {
		$participants = [];;
		$query = RegistroFaseUsuario::query();
		$query->select('encuesta.codigo_encuesta', 'fase.codigo_fase', 'usuario_encuesta.codigo_usuario_encuesta', 'registro_fase_usuario.codigo_registro_fase_usuario as codigo_registro_fase', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario",  DB::raw("CONCAT(fecha_registro_fase_usuario, ' ', hora_registro_fase_usuario) as fecha_registro_fase_usuario"), "entity.nombre_usuario as nombre_entidad", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico as correo_electronico", 'nombre_estado', 'url_encuesta',
			DB::raw("initcap((CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN person.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE person.nombre_usuario END)) as nombre_usuario"), 'numero_registro_convocatoria', 'nombre_fase', 'numero_registro_convocatoria',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 256 THEN registro_fase_usuario.codigo_registro_fase_usuario
					ELSE (
						SELECT rfud.codigo_registro_fase_usuario
						FROM core.registro_fase_usuario as rfud
						WHERE rfud.codigo_usuario = registro_fase_usuario.codigo_usuario
							AND rfud.codigo_registro_convocatoria = registro_fase_usuario.codigo_registro_convocatoria
							AND rfud.codigo_fase = 256
					)
				END) AS registro_fase_documentador"
			),
			DB::raw("
				(CASE fase.codigo_fase
					WHEN 255
						THEN (
							COALESCE((puntaje_registro_fase.valor_puntaje_registro_fase), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta))
						)
					WHEN 256
						THEN (
							COALESCE((puntaje_registro_fase.valor_puntaje_registro_fase), 0)
						)
					WHEN 271
						THEN (
							COALESCE((puntaje_registro_fase.valor_puntaje_registro_fase), 0)
						)
					ELSE (
						COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
						WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
				END) AS puntaje"
			)
		);

		$query->join('core.estado', 'registro_fase_usuario.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->leftJoin('core.puntaje_registro_fase', 'puntaje_registro_fase.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');

		// User / Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});
		$query->join('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->leftJoin('core.asignacion_jurado', 'asignacion_jurado.codigo_registro_fase_usuario', '=', 'registro_fase_usuario.codigo_registro_fase_usuario');
		$query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
		$query->leftJoin('core.usuario_encuesta', 'usuario_encuesta.codigo_usuario_encuesta', '=', 'registro_convocatoria.codigo_usuario_encuesta');

		// City / Country
		$query->leftjoin('core.ciudad', 'person.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query-> where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query-> where('convocatoria.codigo_estado', Estado::getState('active'));
		// States filter
		$query-> whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('pendinginstitution')
		]);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("lower(trim(person.correo_electronico)) ilike lower(trim('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->whereIn('registro_fase_usuario.codigo_fase', explode(',',$filters['phase']));
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".trim($filters['emprendimiento'])."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( (isset($filters['sortby']) && !empty($filters['sortby'])) && (isset($filters['scoring']) && !empty($filters['scoring'])) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 253
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
						(
								(puntaje_registro_fase.valor_puntaje_registro_fase)
							), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta))
						)
				END) $sortby $scoring");
		}

		if( (isset($filters['sortby_2']) && !empty($filters['sortby_2'])) && (isset($filters['scoring_2']) && !empty($filters['scoring_2'])) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 253
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
					 	COALESCE(
					 		(
								(puntaje_registro_fase.valor_puntaje_registro_fase)
							), (SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta))
						)
				END) $sortby_2 $scoring_2");
		}

		$query->groupBy('usuario_encuesta.puntaje_total_encuesta', 'estado.nombre_estado', 'puntaje_registro_fase.valor_puntaje_registro_fase', 'registro_convocatoria.codigo_usuario_encuesta', 'registro_convocatoria.codigo_registro_convocatoria', 'fase.codigo_fase', 'usuario_encuesta.codigo_usuario_encuesta', 'person.tipo_usuario', 'persona.primer_apellido_persona', 'persona.segundo_apellido_persona', 'registro_convocatoria.numero_registro_convocatoria', 'ciudad.nombre_ciudad', 'ciudad.distrito_ciudad', 'pais.codigo_pais', 'person.nombre_usuario', 'encuesta.codigo_encuesta', 'registro_fase_usuario.codigo_registro_fase_usuario', "fase.codigo_evaluacion_emprendimiento", "registro_fase_usuario.codigo_registro_fase_usuario", "registro_fase_usuario.codigo_usuario", "entity.nombre_usuario", "registro_fase_usuario.visualizacion_usuario", "convocatoria.codigo_convocatoria", "entity.nickname_usuario", "person.correo_electronico");

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
		->make(true);
	}

	public static function getParticipantsProgramAppsCo(Convocatoria $program, array $filters = []) {
		$query = RegistroFaseUsuario::query();
		$query->select(
			"fase.codigo_evaluacion_emprendimiento",
			"observaciones_registro_fase_usuario",
			"registro_fase_usuario.codigo_registro_fase_usuario",
			"registro_fase_usuario.codigo_usuario",
			"fecha_registro_fase_usuario",
			"hora_registro_fase_usuario",
			"registro_fase_usuario.codigo_fase",
			"registro_fase_usuario.visualizacion_usuario",
			"convocatoria.codigo_convocatoria",
			"codigo_orientacion_convocatoria",
			"usuario.correo_electronico as correo_electronico",
			//'url_encuesta',
			//'encuesta.codigo_encuesta',
			'nombre_fase',
			'numero_registro_convocatoria',
			'registro_convocatoria.codigo_usuario_encuesta',
			DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '')
				|| ' - ' || COALESCE(pais.codigo_pais, '')) as nombre_ciudad"),
			DB::raw('(CASE  WHEN observaciones_registro_fase_usuario IS NULL THEN false ELSE true END) as documentado'),
			DB::raw("initcap((CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." 
				THEN usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '')
				|| ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE usuario.nombre_usuario END)) as nombre_usuario")
		);

		$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');

		// User Program
		$query->join('seguridad.usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', session('institution')->codigo_institucion);
		});

		//$query->leftJoin('core.encuesta', 'convocatoria.codigo_encuesta', '=', 'encuesta.codigo_encuesta');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		// City / Country
		$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
		$query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

		$query->leftjoin('core.pais_institucion', function($join) {
			$join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
				->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
				->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
		});
		$query->where('ciudad.codigo_estado', '=', Estado::getState('active'));
		$query->whereNotNull('distrito_ciudad');
		$query->whereRaw("distrito_ciudad != ''");

		if(isset($filters['email']) && !empty($filters['email'])){
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(usuario.correo_electronico)) = trim(lower('$email'))");
		}

		if(isset($filters['number_register']) && !empty($filters['number_register'])){
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if(isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if(isset($filters['city']) && !empty($filters['city'])){
			$query->where('usuario.codigo_ciudad', $filters['city']);
		}

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
		$query = $query->get();
    	// States filter
		//$query->whereIn('registro_fase_usuario.codigo_estado', [ Estado::getState('finalized'), Estado::getState('active')	]);

		return Datatables::of($query)
			->addColumn('codigo_registro_fase_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_registro_fase_usuario]);
			})
			->addColumn('codigo_usuario', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario]);
			})
			->addColumn('codigo_usuario_encuesta', function ($data) {
				return TuProyecto::encryptSrcParams([$data->codigo_usuario_encuesta]);
			})
			->addColumn('puntaje', function($data){
				return ReportProgramAppsCoInnpulsa::scoringProgramAppsco([$data->codigo_usuario_encuesta]);
			})
			->make(true);
	}

	public static function getResultExportAldeaEmpodera(Collection $arQuestions, $program, $filters = [])
	{
		$object = new \stdClass();

		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_usuario', 'registro_convocatoria.codigo_usuario_encuesta', 'fecha_registro_fase_usuario', 'hora_registro_fase_usuario', 'registro_fase_usuario.codigo_fase', 'convocatoria.codigo_convocatoria','codigo_orientacion_convocatoria', 'entity.nombre_usuario as nombre_proyecto', 'entity.identificacion_usuario as nit', 'nombre_clase_emprendimiento', 'numero_registro_convocatoria', 'genero_persona', 'person.fecha_nacimiento_usuario', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',

		// Person data
		DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN initcap(trim(person.nombre_usuario)) || ' ' || COALESCE(initcap(trim(persona.primer_apellido_persona)), '') || ' ' || COALESCE(initcap(trim(persona.segundo_apellido_persona)), '') ELSE person.nombre_usuario END) as nombre_usuario"),
		DB::raw("person.correo_electronico as correo_electronico"),

		DB::raw('
				COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_encuesta = 92
						AND usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0) AS total_reto1
			'),
		DB::raw('
				COALESCE(
			 		(
			 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
			 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
			 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
						WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
						AND resultado_evaluacion_emprendimiento.codigo_estado = 8
						AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
						AND rfe.codigo_fase = 212
					), 0) AS total_reto1_2
			'),
		DB::raw("
				COALESCE(
					(
						SELECT (' ' || initcap(trim(uev.nombre_usuario)) || ' ' || initcap(trim(puev.primer_apellido_persona)) || ' ' || initcap(trim(puev.segundo_apellido_persona)) ) FROM core.resultado_evaluacion_emprendimiento
						JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
						JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
						JOIN seguridad.usuario as uev ON uev.codigo_usuario = asignacion_jurado.codigo_usuario
						JOIN core.persona as puev ON puev.codigo_usuario = uev.codigo_usuario
						WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
						AND resultado_evaluacion_emprendimiento.codigo_estado = 8
						AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
						AND rfe.codigo_fase = 212
					),'') AS nombre_evaluador_reto1_2
			")
		);

		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', Institucion::institution('innpulsa'))
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
		$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( isset($filters['sortby']) && !empty($filters['sortby']) && isset($filters['scoring']) && !empty($filters['scoring']) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END)$sortby $scoring");
		}

		if( isset($filters['sortby_2']) && !empty($filters['sortby_2']) && isset($filters['scoring_2']) && !empty($filters['scoring_2']) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END)$sortby_2 $scoring_2");
		}

		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		$query->orderBy('numero_registro_convocatoria', 'asc');
		$usuarios = $query->get();

		// Prepare header
		$matriz = [];
		$header = [];
		$header[] = 'Aplicación';
		$header[] = 'Email';
		$header[] = 'Usuario';
		$header[] = 'Genero';
		$header[] = 'Fecha de nacimiento';
		$header[] = 'Proyecto / Empresa';
		$header[] = 'Tipo';
		$header[] = 'Nit';
		$header[] = 'Fecha postulación';
		$header[] = 'Fecha registro';

		// Build headers
		foreach ($arQuestions as $question) {
			if(!in_array($question->tipo_campo_pregunta, [6, 7])) {

				switch ($question->tipo_pregunta_encuesta) {

					case PreguntaEncuesta::getType('open'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						// Tipo ciudad
						if($question->tipo_campo_pregunta == 5){
							$header[] = 'Departamento';
						}
					break;

					case PreguntaEncuesta::getType('single'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						if($question->puntaje_pregunta_encuesta) {
							$header[] = 'Puntaje';
						}

					break;

					case PreguntaEncuesta::getType('matriz'):

						// Choices closed questions
						$matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						$matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
							   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$header[] = strip_tags("{$question->texto_pregunta_encuesta} {$choice->texto_opcion_encuesta} {$choicey->texto_opcion_encuesta}");
								}
							}
						}
					break;

					default:
						$header[] = strip_tags($question->texto_pregunta_encuesta);
					break;
				}
			}
		}

		$header[] ="Puntaje primer reto empodera";
		$header[] ="Puntaje reto 1.2 empodera";
		$header[] = "Evaluador reto 1.2 empodera";
		// Preparar Respuestas
    	$query = RespuestaEncuesta::query();
		$query->select('texto_opcion_encuesta','puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $usuarios->pluck('codigo_usuario_encuesta')->all() );
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		$allAnswers = $query->get();

		$answers = [];
		foreach ($allAnswers as $answer) {
			switch ($answer->tipo_pregunta_encuesta) {
				case PreguntaEncuesta::getType('open'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('single'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('multiple'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
				break;

				case PreguntaEncuesta::getType('upload'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('matriz'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
				break;
			}
		}

		$cities = [];
		$citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
		foreach ($citys as $city) {
			 $cities["{$city->codigo_ciudad}"] = $city;
		}
		// Preparar sectores
		$sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

		// Preparar Nivel de educación
		$levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

		$item = 1;
		$data = collect([]);
		foreach ($usuarios as $usuario)
		{
			unset($record);
			$record[] = $usuario->numero_registro_convocatoria;
			$record[] = $usuario->correo_electronico;
			$record[] = $usuario->nombre_usuario;
			$record[] =  isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
			$record[] = $usuario->fecha_nacimiento_usuario;
			$record[] = $usuario->nombre_proyecto;
			$record[] = $usuario->nombre_clase_emprendimiento;
			$record[] = $usuario->nit;
			$record[] = $usuario->fecha_registro_convocatoria.' '.$usuario->hora_registro_convocatoria;
			$record[] = $usuario->fecha_registro_fase_usuario.' '.$usuario->hora_registro_fase_usuario;

			// Questions Survey
			foreach ($arQuestions as $question)
			{
				if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
				{
					switch ($question->tipo_pregunta_encuesta)
					{
						case PreguntaEncuesta::getType('open'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							// Tipo ciudad
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')) {
								if($answer instanceof RespuestaEncuesta) {
									$key = "{$answer->texto_respuesta_encuesta}";
									$city = isset($cities[$key]) ? $cities[$key] : '';
									if($city instanceof Ciudad){
										$respuesta = $city->distrito_ciudad;
										$record[] = $city->nombre_ciudad;
									}
								}else{
									$record[] = '';
								}
							}
							// Tipo Sector interes
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';
							}
							// Tipo nivel educación
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('single'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_opcion_encuesta;
							}
							$record[] = $respuesta;
							if($question->puntaje_pregunta_encuesta) {
								$puntajeOpcion = "";
								if($answer instanceof RespuestaEncuesta) {
									$puntajeOpcion = $answer->puntaje_opcion_encuesta;
								}
								$record[] = $puntajeOpcion;
							}

						break;

						case PreguntaEncuesta::getType('multiple'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answerop = isset($answers[$key]) ? $answers[$key] : [];
							foreach ($answerop as $option) {
								if($option instanceof RespuestaEncuesta){
									$respuesta.= "{$option->texto_opcion_encuesta} ";
								}
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('upload'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('matriz'):

							if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
								foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
								   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
										$respuesta = '';
										$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
										$answer = isset($answers[$key]) ? $answers[$key] : '';
										if($answer instanceof RespuestaEncuesta){
											$respuesta = $answer->texto_respuesta_encuesta;
										}
										$record[] = $respuesta;
									}
								}
							}
						break;
					}
				}
			}
			$record[] = $usuario->total_reto1;
			$record[] = $usuario->total_reto1_2;
			$record[] = $usuario->nombre_evaluador_reto1_2;
			$data[] = $record;
		}
		$object->headers = $header;
		$object->data = $data;

		return $object;
	}

	public static function getResultExportAldeaEmpodera20(Collection $arQuestions, $program, $filters = [])
	{
		$object = new \stdClass();

		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.codigo_usuario', 'registro_convocatoria.codigo_usuario_encuesta', 'fecha_registro_fase_usuario', 'hora_registro_fase_usuario', 'registro_fase_usuario.codigo_fase', 'convocatoria.codigo_convocatoria','codigo_orientacion_convocatoria', 'entity.nombre_usuario as nombre_proyecto', 'entity.identificacion_usuario as nit', 'nombre_clase_emprendimiento', 'numero_registro_convocatoria', 'genero_persona', 'person.fecha_nacimiento_usuario', 'fecha_registro_convocatoria', 'hora_registro_convocatoria',

		// Person data
		DB::raw("(CASE person.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN initcap(trim(person.nombre_usuario)) || ' ' || COALESCE(initcap(trim(persona.primer_apellido_persona)), '') || ' ' || COALESCE(initcap(trim(persona.segundo_apellido_persona)), '') ELSE person.nombre_usuario END) as nombre_usuario"),
		DB::raw("person.correo_electronico as correo_electronico"),

		DB::raw('
				COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta WHERE usuario_encuesta.codigo_encuesta = 92
						AND usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0) AS total_reto1
			'),
		DB::raw('
				COALESCE(
			 		(
			 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
			 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
			 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
						WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
						AND resultado_evaluacion_emprendimiento.codigo_estado = 8
						AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
						AND rfe.codigo_fase = 212
					), 0) AS total_reto1_2
			'),
		DB::raw("
				COALESCE(
					(
						SELECT (' ' || initcap(trim(uev.nombre_usuario)) || ' ' || initcap(trim(puev.primer_apellido_persona)) || ' ' || initcap(trim(puev.segundo_apellido_persona)) ) FROM core.resultado_evaluacion_emprendimiento
						JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
						JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
						JOIN seguridad.usuario as uev ON uev.codigo_usuario = asignacion_jurado.codigo_usuario
						JOIN core.persona as puev ON puev.codigo_usuario = uev.codigo_usuario
						WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
						AND resultado_evaluacion_emprendimiento.codigo_estado = 8
						AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
						AND rfe.codigo_fase = 212
					),'') AS nombre_evaluador_reto1_2
			")
		);

		$query->join('core.fase', 'registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');

		// User Program
		$query->join('seguridad.usuario as entity', 'registro_fase_usuario.codigo_usuario', '=', 'entity.codigo_usuario');
		$query->join('core.entidad', 'entity.codigo_usuario', '=', 'entidad.codigo_usuario');

		$query->join('seguridad.usuario as person', 'entidad.seg_codigo_usuario', '=', 'person.codigo_usuario');
		$query->leftJoin('core.persona', 'person.codigo_usuario', '=', 'persona.codigo_usuario');

		$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');

		$query->join('core.convocatoria_institucion', function($join) {
			$join->on('convocatoria.codigo_convocatoria', '=', 'convocatoria_institucion.codigo_convocatoria')
				->where('convocatoria_institucion.codigo_institucion', '=', Institucion::institution('innpulsa'))
				->where('convocatoria_institucion.codigo_estado', '=', Estado::getState('active'));
		});

		$query->join('core.clase_emprendimiento_insti', 'entidad.codigo_clase_empre_insti', '=', 'clase_emprendimiento_insti.codigo_clase_empre_insti');
		$query->join('core.clase_emprendimiento', 'clase_emprendimiento_insti.codigo_clase_emprendimiento', '=', 'clase_emprendimiento.codigo_clase_emprendimiento');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');

		$query->where('convocatoria.codigo_convocatoria', $program->codigo_convocatoria);

		if( isset($filters['email']) && !empty($filters['email']) ) {
			$email = trim($filters['email']);
			$query->whereRaw("trim(lower(person.correo_electronico)) ilike trim(lower('%$email%'))");
		}

		if( isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio']) ) {
			$query->whereBetween('fecha_registro_fase_usuario',[$filters['fecha_inicio'], $filters['fecha_fin']]);
		}

		if( isset($filters['visualizacion']) && !empty($filters['visualizacion']) ) {
			$query->where('registro_fase_usuario.visualizacion_usuario', '=', $filters['visualizacion']);
		}

		if( isset($filters['phase']) && !empty($filters['phase']) && $filters['phase'] != Fase::getIdPhase('general')) {
			$query->where('registro_fase_usuario.codigo_fase', '=', $filters['phase']);
		}

		if( isset($filters['emprendimiento']) && !empty($filters['emprendimiento']) ) {
			$query->whereRaw("entity.nombre_usuario ilike '%".$filters['emprendimiento']."%'");
		}

		if( isset($filters['number_register']) && !empty($filters['number_register']) ) {
			$query->where('registro_convocatoria.numero_registro_convocatoria', '=', $filters['number_register']);
		}

		if( isset($filters['state']) && !empty($filters['state']) ) {
			$query->where('registro_fase_usuario.codigo_estado', $filters['state']);
		}

		if( isset($filters['sortby']) && !empty($filters['sortby']) && isset($filters['scoring']) && !empty($filters['scoring']) )
		{
			$sortby = $filters['sortby'];
			$scoring = $filters['scoring'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END)$sortby $scoring");
		}

		if( isset($filters['sortby_2']) && !empty($filters['sortby_2']) && isset($filters['scoring_2']) && !empty($filters['scoring_2']) )
		{
			$sortby_2 = $filters['sortby_2'];
			$scoring_2 = $filters['scoring_2'];
			$query->whereRaw("
				(CASE fase.codigo_fase
					WHEN 204
						THEN (
							COALESCE((SELECT puntaje_total_encuesta FROM core.usuario_encuesta
							WHERE usuario_encuesta.codigo_usuario_encuesta = registro_convocatoria.codigo_usuario_encuesta), 0)
						)
					ELSE (
						COALESCE(
					 		(
					 			SELECT puntaje_total_eval_emprendi FROM core.resultado_evaluacion_emprendimiento
					 			JOIN core.asignacion_jurado ON asignacion_jurado.codigo_asignacion_jurado = resultado_evaluacion_emprendimiento.codigo_asignacion_jurado
					 			JOIN core.registro_fase_usuario as rfe ON rfe.codigo_registro_fase_usuario = asignacion_jurado.codigo_registro_fase_usuario
								WHERE resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento = 18
								AND resultado_evaluacion_emprendimiento.codigo_estado = 8
								AND rfe.codigo_registro_convocatoria = registro_convocatoria.codigo_registro_convocatoria
								AND rfe.codigo_fase = 212
							), 0)
						)
				END)$sortby_2 $scoring_2");
		}

		$query->whereIn('registro_fase_usuario.codigo_estado', [
			Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected'), Estado::getState('linked')
		]);
		$query->where('convocatoria.codigo_estado', Estado::getState('active'));
		$query->orderBy('numero_registro_convocatoria', 'asc');
		$usuarios = $query->get();

		// Prepare header
		$matriz = [];
		$header = [];
		$header[] = 'Aplicación';
		$header[] = 'Email';
		$header[] = 'Usuario';
		$header[] = 'Genero';
		$header[] = 'Fecha de nacimiento';
		$header[] = 'Proyecto / Empresa';
		$header[] = 'Tipo';
		$header[] = 'Nit';
		$header[] = 'Fecha postulación';
		$header[] = 'Fecha registro';

		// Build headers
		foreach ($arQuestions as $question) {
			if(!in_array($question->tipo_campo_pregunta, [6, 7])) {

				switch ($question->tipo_pregunta_encuesta) {

					case PreguntaEncuesta::getType('open'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						// Tipo ciudad
						if($question->tipo_campo_pregunta == 5){
							$header[] = 'Departamento';
						}
					break;

					case PreguntaEncuesta::getType('single'):
						$header[] = strip_tags($question->texto_pregunta_encuesta);
						if($question->puntaje_pregunta_encuesta) {
							$header[] = 'Puntaje';
						}

					break;

					case PreguntaEncuesta::getType('matriz'):

						// Choices closed questions
						$matriz["{$question->codigo_pregunta_encuesta}_x"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('x'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						$matriz["{$question->codigo_pregunta_encuesta}_y"] = OpcionPreguntaEncuesta::select('opcion_encuesta.*')
							->join('core.pregunta_encuesta', 'opcion_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta')
							->join('core.encuesta', 'pregunta_encuesta.codigo_encuesta', '=', 'encuesta.codigo_encuesta')
							->where('encuesta.codigo_encuesta', $question->codigo_encuesta)
							->where('pregunta_encuesta.codigo_pregunta_encuesta', $question->codigo_pregunta_encuesta)
							->where('eje_opcion', OpcionPreguntaEncuesta::getAxis('y'))
							->where('opcion_encuesta.codigo_estado', Estado::getState('active'))
							->orderBy('orden_opcion_encuesta', 'asc')
							->orderBy('codigo_opcion_encuesta', 'asc')
							->get();

						if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
							foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
							   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
									$header[] = strip_tags("{$question->texto_pregunta_encuesta} {$choice->texto_opcion_encuesta} {$choicey->texto_opcion_encuesta}");
								}
							}
						}
					break;

					default:
						$header[] = strip_tags($question->texto_pregunta_encuesta);
					break;
				}
			}
		}

		// Preparar Respuestas
    	$query = RespuestaEncuesta::query();
		$query->select('texto_opcion_encuesta','puntaje_opcion_encuesta','pregunta_encuesta.codigo_pregunta_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta', 'respuesta_encuesta.codigo_opcion_encuesta', 'respuesta_encuesta.cor_codigo_opcion_encuesta', 'respuesta_encuesta.codigo_usuario', 'tipo_pregunta_encuesta', 'codigo_usuario_encuesta');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_usuario_encuesta', $usuarios->pluck('codigo_usuario_encuesta')->all() );
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('pregunta_encuesta.visibilidad_pregunta_encuesta', true);
		$allAnswers = $query->get();

		$answers = [];
		foreach ($allAnswers as $answer) {
			switch ($answer->tipo_pregunta_encuesta) {
				case PreguntaEncuesta::getType('open'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('single'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('multiple'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"][$answer->codigo_opcion_encuesta] = $answer;
				break;

				case PreguntaEncuesta::getType('upload'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}"] = $answer;
				break;

				case PreguntaEncuesta::getType('matriz'):
					$answers["{$answer->codigo_usuario_encuesta}_{$answer->codigo_pregunta_encuesta}_x{$answer->cor_codigo_opcion_encuesta}_y{$answer->codigo_opcion_encuesta}"] = $answer;
				break;
			}
		}

		$cities = [];
		$citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
		foreach ($citys as $city) {
			 $cities["{$city->codigo_ciudad}"] = $city;
		}
		// Preparar sectores
		$sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

		// Preparar Nivel de educación
		$levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

		$item = 1;
		$data = collect([]);
		foreach ($usuarios as $usuario)
		{
			unset($record);
			$record[] = $usuario->numero_registro_convocatoria;
			$record[] = $usuario->correo_electronico;
			$record[] = $usuario->nombre_usuario;
			$record[] =  isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
			$record[] = $usuario->fecha_nacimiento_usuario;
			$record[] = $usuario->nombre_proyecto;
			$record[] = $usuario->nombre_clase_emprendimiento;
			$record[] = $usuario->nit;
			$record[] = $usuario->fecha_registro_convocatoria.' '.$usuario->hora_registro_convocatoria;
			$record[] = $usuario->fecha_registro_fase_usuario.' '.$usuario->hora_registro_fase_usuario;

			// Questions Survey
			foreach ($arQuestions as $question)
			{
				if(!in_array($question->tipo_campo_pregunta, [TipoCampoPregunta::getTypeQuestion('password'),TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
				{
					switch ($question->tipo_pregunta_encuesta)
					{
						case PreguntaEncuesta::getType('open'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							// Tipo ciudad
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('municipality')) {
								if($answer instanceof RespuestaEncuesta) {
									$key = "{$answer->texto_respuesta_encuesta}";
									$city = isset($cities[$key]) ? $cities[$key] : '';
									if($city instanceof Ciudad){
										$respuesta = $city->distrito_ciudad;
										$record[] = $city->nombre_ciudad;
									}
								}else{
									$record[] = '';
								}
							}
							// Tipo Sector interes
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('ciiu')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '';
							}
							// Tipo nivel educación
							if($question->tipo_campo_pregunta == TipoCampoPregunta::getTypeQuestion('level_study')) {
								$respuesta = ($answer instanceof RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '';
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('single'):

							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_opcion_encuesta;
							}
							$record[] = $respuesta;
							if($question->puntaje_pregunta_encuesta) {
								$puntajeOpcion = "";
								if($answer instanceof RespuestaEncuesta) {
									$puntajeOpcion = $answer->puntaje_opcion_encuesta;
								}
								$record[] = $puntajeOpcion;
							}

						break;

						case PreguntaEncuesta::getType('multiple'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answerop = isset($answers[$key]) ? $answers[$key] : [];
							foreach ($answerop as $option) {
								if($option instanceof RespuestaEncuesta){
									$respuesta.= "{$option->texto_opcion_encuesta} ";
								}
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('upload'):
							$respuesta = '';
							$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}";
							$answer = isset($answers[$key]) ? $answers[$key] : '';
							if($answer instanceof RespuestaEncuesta){
								$respuesta = $answer->texto_respuesta_encuesta;
							}
							$record[] = $respuesta;
						break;

						case PreguntaEncuesta::getType('matriz'):

							if($matriz["{$question->codigo_pregunta_encuesta}_x"]->count() > 0) {
								foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice) {
								   foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choicey) {
										$respuesta = '';
										$key = "{$usuario->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice->codigo_opcion_encuesta}_y{$choicey->codigo_opcion_encuesta}";
										$answer = isset($answers[$key]) ? $answers[$key] : '';
										if($answer instanceof RespuestaEncuesta){
											$respuesta = $answer->texto_respuesta_encuesta;
										}
										$record[] = $respuesta;
									}
								}
							}
						break;
					}
				}
			}
			$data[] = $record;
		}
		$object->headers = $header;
		$object->data = $data;

		return $object;
	}

	// Innovacion

	public static function getResponseEvaluationAldea20__three__moment__innovacion($register, $title){
		$query = RespuestaEncuesta::query();
		switch($title) {
			case 'propiedad':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4357 AND respuesta_encuesta.codigo_opcion_encuesta = 7207 THEN 0.5 ELSE 0.0 END  ) as propiedad_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4357);
			break;
			case 'motivo':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4389 AND respuesta_encuesta.codigo_opcion_encuesta IN (7320, 7319, 7318)
					THEN 0.0
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4389 AND respuesta_encuesta.codigo_opcion_encuesta IN (7315, 7317)
						THEN 0.30
						ELSE
							( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4389 AND respuesta_encuesta.codigo_opcion_encuesta = 7314
							THEN
								0.75
							ELSE
								1.50
							END
							)
						END
						)
					END  ) as motivo_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4389);
			break;
			case 'tipo';
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta IN (7137, 7138)
				THEN 0.0
				ELSE
					( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta = 7139
					THEN 0.80
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta = 7140
						THEN 1.20
						ELSE
							( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta = 7141
							THEN 1.60
							ELSE 2.00
							END )
						END )
					END )
				END  ) as tipo_score'));
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4334);
			break;
		}
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));

		return $query->first();
	}

	public static function Calificacion__Aldea20__three_moment__innovacion($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 81);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 174);

		return $query->get();
	}

	// Escalabilidad

	public static function getResponseEvaluationAldea20__three__moment__escalabilidad($register, $title){
		$query = RespuestaEncuesta::query();
		switch($title) {
			case 'idioma':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4280 AND respuesta_encuesta.codigo_opcion_encuesta = 7033 THEN 1.0 ELSE 0.0 END  ) as idioma_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4280);
			break;
			case 'metas':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4351 AND respuesta_encuesta.codigo_opcion_encuesta IN (7198, 7197)
					THEN 0.0
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4351 AND respuesta_encuesta.codigo_opcion_encuesta IN (7200, 7201, 7202)
						THEN 1.00
						ELSE 0.50
						END
						)
					END  ) as metas_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4351);
			break;
			case 'tipo';
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta IN (7137, 7138)
				THEN 0.0
				ELSE
					( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta = 7139
					THEN 0.80
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta = 7140
						THEN 1.20
						ELSE
							( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4334 AND respuesta_encuesta.codigo_opcion_encuesta = 7141
							THEN 1.60
							ELSE 2.00
							END )
						END )
					END )
				END  ) as tipo_score'));
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4334);
			break;
		}
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));

		return $query->first();
	}

	public static function Calificacion__potential__Aldea20__three_moment__equipo($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			DB::raw('( CASE WHEN evaluacion_item_emprendimiento.valor_evaluacion_item = 302 OR evaluacion_item_emprendimiento.valor_evaluacion_item = 303
			THEN 0
			ELSE
				( CASE WHEN evaluacion_item_emprendimiento.valor_evaluacion_item = 304
				THEN 1.5
				ELSE
					( CASE WHEN evaluacion_item_emprendimiento.valor_evaluacion_item = 305
					THEN 3
					ELSE
						( CASE WHEN evaluacion_item_emprendimiento.valor_evaluacion_item = 306
						THEN 4
						ELSE 5
						END)
					END)
				END)
			END) as total_score'),
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 83);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 176);

		return $query->get();
	}

	// Traccion

	public static function getResponseEvaluationAldea20__three__moment__traccion($register, $title) {
		$query = RespuestaEncuesta::query();
		switch($title) {
			case 'alcance':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4330 AND respuesta_encuesta.codigo_opcion_encuesta = 7133
					THEN 0.0
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4330 AND respuesta_encuesta.codigo_opcion_encuesta = 7134
						THEN 0.45
						ELSE
							( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4330 AND respuesta_encuesta.codigo_opcion_encuesta = 7135
							THEN 1.05
							ELSE 1.50
							END )
						END)
					END  ) as alcance_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4330);
			break;
			case 'cuentas':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4338 AND respuesta_encuesta.codigo_opcion_encuesta IN (7154, 7155)
					THEN 0.0
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4338 AND respuesta_encuesta.codigo_opcion_encuesta IN (7156, 7160, 7158)
						THEN 0.75
						ELSE
							( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4338 AND respuesta_encuesta.codigo_opcion_encuesta IN (7157, 7159)
							THEN 1.50
							END)
						END
						)
					END  ) as cuentas_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4338);
			break;
			case 'estados';
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4339 AND respuesta_encuesta.codigo_opcion_encuesta = 7162
				THEN 1.50
				ELSE 0.0
				END  ) as estados_score'));
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4339);
			break;
			case 'indicadores':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4367 AND respuesta_encuesta.codigo_opcion_encuesta IN (7215, 7218)
					THEN 0.20
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4367 AND respuesta_encuesta.codigo_opcion_encuesta IN (7216, 7217, 7219)
						THEN 1.00
						END
						)
					END  ) as indicadores_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4367);
			break;
		}
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));

		return $query->first();
	}

	// Equipo

	public static function getResponseEvaluationAldea20__three__moment__equipo($register, $title) {
		$query = RespuestaEncuesta::query();
		switch($title) {
			case 'laboral':
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta',
					DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.texto_respuesta_encuesta::INTEGER >= 7
					THEN 1.00
					ELSE
						( CASE WHEN respuesta_encuesta.texto_respuesta_encuesta::INTEGER >= 3 AND respuesta_encuesta.texto_respuesta_encuesta::INTEGER <= 6
						THEN 0.50
						ELSE 0.0
						END)
					END) as laboral_score'))
				);
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
				$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7637);
				$query->where('respuesta_encuesta.cor_codigo_opcion_encuesta', '=', 7269);
			break;
			case 'decisiones';
				$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
					DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4368 AND respuesta_encuesta.codigo_opcion_encuesta = 7220
				THEN 0.0
				ELSE
					( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4368 AND respuesta_encuesta.codigo_opcion_encuesta = 7221
					THEN 0.25
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4368 AND ( respuesta_encuesta.codigo_opcion_encuesta = 7222 OR respuesta_encuesta.codigo_opcion_encuesta = 7223 OR respuesta_encuesta.codigo_opcion_encuesta = 7224 )
						THEN 0.50
						END)
					END)
				END  ) as decisiones_score'));
				$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
				$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
				$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4368);
			break;
		}
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		return $query->first();
	}

	public static function getSectorExprerienceAldea20__three__moment__equipo($register) {
		$query = RespuestaEncuesta::query();
		$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
			DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'));
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7264);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269, 7270, 7271, 7272]);
		$data = $query->get();
		$response = [];

		foreach($data as $item) {
			$response['texto_pregunta_encuesta'] = $item->texto_pregunta_encuesta;
			$response['texto_opcion_encuesta'] = $item->texto_opcion_encuesta;
			$response['texto_respuesta_encuesta'] = (int)$item->texto_respuesta_encuesta;

			if ($item->texto_respuesta_encuesta >= 3) {
				$data = '1.00';
				$response['sector_scoring'] = $data;

				return $response;
			} else {
				$data = '0.0';
				$response['sector_scoring'] = $data;
			}
		}

		return $response;
	}

	public static function getRiesgoAldea20__three__moment__equipo($register) {
		$query = RespuestaEncuesta::query();
		$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta',
			DB::raw(DB::raw('( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4381 AND respuesta_encuesta.codigo_opcion_encuesta = 7302
			THEN 0.0
			ELSE
				( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4383 AND respuesta_encuesta.codigo_opcion_encuesta = 7304
				THEN 0.10
				ELSE
					( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4384 AND respuesta_encuesta.codigo_opcion_encuesta = 7306
					THEN 0.25
					ELSE
						( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4385 AND respuesta_encuesta.codigo_opcion_encuesta = 7308
						THEN
							0.40
						ELSE
							( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4387 AND respuesta_encuesta.codigo_opcion_encuesta = 7311
							THEN 0.50
							ELSE
								( CASE WHEN respuesta_encuesta.codigo_pregunta_encuesta = 4387 AND respuesta_encuesta.codigo_opcion_encuesta = 7310
								THEN 0.50
								END )
							END )
						END)
					END)
				END)
			END  ) as riesgo_score'))
		);
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->whereIn('respuesta_encuesta.codigo_pregunta_encuesta', [4381, 4383, 4384, 4385, 4387]);
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->orderBy('respuesta_encuesta.codigo_pregunta_encuesta', 'asc');
		$data = $query->get();

		$response = new \stdClass();
		$response->texto_pregunta_encuesta = "No se encontro pregunta";
		$response->texto_opcion_encuesta = "No se encontro opción";
		$response->riesgo_score = 0;

		foreach($data as $item) {
			if($item->riesgo_score != ""){
				$response->texto_pregunta_encuesta = $item->texto_pregunta_encuesta;
				$response->texto_opcion_encuesta = $item->texto_opcion_encuesta;
				$response->riesgo_score = $item->riesgo_score;
			}
		}
		return $response;
	}

	public static function getEmprendorExprerienceAldea20__three__moment__equipo($register) {
		$query = RespuestaEncuesta::query();
		$query->select('pregunta_encuesta.texto_pregunta_encuesta', 'opcion_encuesta.texto_opcion_encuesta', 'item_opcion_encuesta.nombre_item_opcion_encuesta', 'respuesta_encuesta.texto_respuesta_encuesta');
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->leftJoin('core.item_opcion_encuesta', function($join) {
			$join->on(DB::raw('item_opcion_encuesta.codigo_item_opcion_encuesta::varchar(255)'), '=', 'respuesta_encuesta.texto_respuesta_encuesta');
		});
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7267);
		$query->whereNotNull('pregunta_encuesta.texto_pregunta_encuesta');
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269, 7270, 7271, 7272]);
		$data = $query->get();
		$response= [];

		foreach($data as $item) {
			$response['texto_pregunta_encuesta'] = $item->texto_pregunta_encuesta;
			$response['texto_opcion_encuesta'] = $item->texto_opcion_encuesta;
			$response['texto_respuesta_encuesta'] = $item->nombre_item_opcion_encuesta;

			if ((int)$item->texto_respuesta_encuesta == 123 || (int)$item->texto_respuesta_encuesta == 124 || (int)$item->texto_respuesta_encuesta == 125) {
				$data = '1.00';
				$response['emprendor_scoring'] = $data;

				return $response;
			} else {
				$data = '0.0';

				$response['emprendor_scoring'] = $data;
			}
		}

		return $response;
	}

	public static function getRolesAldea20__three__moment__equipo($register) {
		$cantidad_gerenciales = 0;
        $cantidad_comerciales = 0;
        $cantidad_creativas = 0;
        $cantidad_tecnicas = 0;
        $total_obligatorias = 0;
        $total_opcionales = 0;

        $gerenciales = RespuestaEncuesta::query();
		$gerenciales->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$gerenciales->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$gerenciales->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$gerenciales->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$gerenciales->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$gerenciales->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$gerenciales->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$gerenciales->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$gerenciales->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['108']);
        $i1 = $gerenciales->first(); 

        if($i1->cantidad > 0 && $i1->cantidad != ""){
            $cantidad_gerenciales = 1;
		}
        
        $comerciales = RespuestaEncuesta::query();
		$comerciales->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$comerciales->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$comerciales->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$comerciales->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$comerciales->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$comerciales->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$comerciales->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$comerciales->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$comerciales->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['112']);
        $i2 = $comerciales->first(); 

        if($i2->cantidad > 0 && $i2->cantidad != ""){
            $cantidad_comerciales = 1;
        }
        
        $total_obligatorias = $cantidad_gerenciales + $cantidad_comerciales;
        
        $creativas = RespuestaEncuesta::query();
		$creativas->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$creativas->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$creativas->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$creativas->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$creativas->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$creativas->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$creativas->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$creativas->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$creativas->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['116']);
        $i3 = $creativas->first();

        if($i3->cantidad > 0 && $i3->cantidad != ""){
            $cantidad_creativas = 1;
		}
        
        $tecnicas = RespuestaEncuesta::query();
		$tecnicas->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as cantidad'));
		$tecnicas->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$tecnicas->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$tecnicas->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$tecnicas->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$tecnicas->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$tecnicas->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7263);
		$tecnicas->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269,7270,7271, 7272]);
		$tecnicas->whereIn(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), ['113']);
        $i4 = $tecnicas->first();
        
        if($i4->cantidad > 0 && $i4->cantidad != ""){
            $cantidad_tecnicas = 1;
        }

        if($cantidad_creativas > 0){
            $total_opcionales = 1;
        }else if($cantidad_tecnicas > 0){
            $total_opcionales = 1;
        }
        
        $total_perfiles = $total_obligatorias + $total_opcionales;

		if ($total_perfiles == 3) {
			$score = '2.00';
		} else if ($total_perfiles == 2) {
			$score = '1.00';
		} else {
			$score = '0.0';
		}

		return $score;
	}

	public static function getGenderAldea20__three__moment__equipo($register) {
		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as gender'));
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7252);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269, 7270, 7271, 7272]);
		$query->where(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), '=', '85');
		$data = $query->first();
		$hombre = 0;
		if($data->gender != 0 && $data->gender != ""){
			$hombre = 1;
		}

		$query = "";

		$query = RespuestaEncuesta::query();
		$query->select(DB::raw('COUNT(respuesta_encuesta.texto_respuesta_encuesta::INTEGER) as gender'));
		$query->join('core.pregunta_encuesta', 'respuesta_encuesta.codigo_pregunta_encuesta', '=', 'pregunta_encuesta.codigo_pregunta_encuesta');
		$query->join('core.opcion_encuesta', 'opcion_encuesta.codigo_opcion_encuesta', '=', 'respuesta_encuesta.codigo_opcion_encuesta');
		$query->where('respuesta_encuesta.codigo_usuario_encuesta', '=', $register);
		$query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
		$query->where('respuesta_encuesta.codigo_pregunta_encuesta', '=', 4373);
		$query->where('respuesta_encuesta.codigo_opcion_encuesta', '=', 7252);
		$query->whereIn('respuesta_encuesta.cor_codigo_opcion_encuesta', [7269, 7270, 7271, 7272]);
		$query->where(DB::raw('respuesta_encuesta.texto_respuesta_encuesta::varchar(255)'), '=', '86');
		$data1 = $query->first();
		$mujer = 0;
		if($data1->gender != 0 && $data1->gender != ""){
			$mujer = 1;
		}

		$total = $mujer + $hombre;

		if ($total == 2) {
			$data = '1.00';
		} else {
			$data = '0.0';
		}

		return $data;
	}

	public static function Calificacion__Aldea20__three_moment__traccion($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 82);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 175);

		return $query->get();
	}

	public static function Calificacion__Aldea20__three_moment__equipo($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 84);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 177);

		return $query->get();
	}

	public static function get__Response__Keys__4501__Aldea20($codigo_usuario_encuesta) {
		$query = OpcionPreguntaEncuesta::query();
		$query->select('texto_opcion_encuesta', 'codigo_opcion_encuesta',
		DB::raw('(
			SELECT respuesta_encuesta.codigo_opcion_encuesta
			FROM core.respuesta_encuesta
			WHERE respuesta_encuesta.codigo_pregunta_encuesta = 4501 AND respuesta_encuesta.codigo_usuario_encuesta = '.$codigo_usuario_encuesta.') as option_selected'));
		$query->where('opcion_encuesta.codigo_pregunta_encuesta', '=', 4501);

		return $query->get();
	}

	public static function get__Balance__Aldea20($codigo_usuario_encuesta) {
		$query = RespuestaEncuesta::query();
		$query->select('codigo_opcion_encuesta');
		$query->where('codigo_usuario_encuesta', '=', $codigo_usuario_encuesta);
		$query->where('codigo_pregunta_encuesta', '=', 4501);
		$data = $query->first();

		$queryBalance = Voucher::query();
		$queryBalance->select('saldo_voucher');
		if($data->codigo_opcion_encuesta == 7641) {
			$queryBalance->where('voucher.codigo_voucher', '=', 'V20200707-1');
		}else if($data->codigo_opcion_encuesta == 7642){
			$queryBalance->where('voucher.codigo_voucher', '=', 'V20200707-2');
		}else if($data->codigo_opcion_encuesta == 8073){
			$queryBalance->where('voucher.codigo_voucher', '=', 'V20200827-1');
		}else if($data->codigo_opcion_encuesta == 8074){
			$queryBalance->where('voucher.codigo_voucher', '=', 'V20200827-2');
		}else if($data->codigo_opcion_encuesta == 8075){
			$queryBalance->where('voucher.codigo_voucher', '=', 'V20200827-3');
		}

		return $queryBalance->first();
	}

	public static function getScoreDocumenter__4287__aldea20($city) {
		$city = Ciudad::find((int)$city);

		$score = 0;
		switch ($city->indice_innovacion) {
			case Innpulsa::getRegion__Moment_Four__Aldea20('bajo'):
				$score = 5;
			break;
			case Innpulsa::getRegion__Moment_Four__Aldea20('medioBajo'):
				$score = 3;
			break;
			case Innpulsa::getRegion__Moment_Four__Aldea20('medio'):
				$score = 2;
			break;
			default:
				$score = 0;
			break;
		}

		return $score;
	}

	public static function getScoreDocumenter__CIIU__aldea20($sector) {
		$query = SectorInteres::find($sector);
		$score = 0;

		if($query->ciiu_economia_naranja) {
			$score = 3;
		}

		return $score;
	}

	public static function getScoreDocumenter__Moment_Three_aldea20($score) {
		$result = 0;
		if(isset($score) && $score > 0) {
			$result = $score * 0.50;
		}

		return round($result, 2);
	}

	public static function getScoreDocumenter__Moment_Four_aldea20($score) {
		$result = 0;
		if(isset($score) && $score > 0) {
			$result = $score * 0.50;
		}

		return round($result, 2);
	}

	// Get Dates acces keys Aldea 20
	public static function getDataKeysAccessAprove($register, $program) {

		$registerProgram = RegistroConvocatoria::find($program);
		if(!$registerProgram instanceof RegistroConvocatoria) {
			abort(404);
		}

		// Result Survey
		$responseSurvey = RespuestaEncuesta::query();
		$responseSurvey->select('codigo_pregunta_encuesta', 'texto_respuesta_encuesta');
		$responseSurvey->where('codigo_usuario_encuesta', '=', $registerProgram->codigo_usuario_encuesta);
		$responseSurvey->where('codigo_pregunta_encuesta', '=', 4295);
		$dataResponse = $responseSurvey->first();

		$responseSurveyCity = RespuestaEncuesta::query();
		$responseSurveyCity->select('nombre_ciudad', 'distrito_ciudad');
		$responseSurveyCity->join('core.ciudad', 'ciudad.codigo_ciudad', '=', DB::raw('texto_respuesta_encuesta::INTEGER'));
		$responseSurveyCity->where('codigo_usuario_encuesta', '=', $registerProgram->codigo_usuario_encuesta);
		$responseSurveyCity->where('codigo_pregunta_encuesta', '=', 4287);
		$dataResponseCity = $responseSurveyCity->first();

		// User and Entity
		$query = Usuario::query();
		$query->select('usuario.nombre_usuario as nombre_empresa', 'userOwner.nombre_usuario as nombre', 'userOwner.correo_electronico',
		'persona.genero_persona');
		$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.entidad', 'entidad.codigo_usuario', '=', 'usuario.codigo_usuario');

		// Get Owner
		$query->join('seguridad.usuario as userOwner', 'userOwner.codigo_usuario', '=', 'entidad.seg_codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'userOwner.codigo_usuario');
		$query->where('registro_fase_usuario.codigo_registro_fase_usuario', '=', $register);
		$data = $query->first();

		$object = new \stdClass();
		$object->nombre_empresa = $data->nombre_empresa;
		$object->nombre_lider = $data->nombre;
		$object->correo_electronico = $data->correo_electronico;
		$object->genero = Usuario::getGender($data->genero_persona);
		$object->direccion = $dataResponse->texto_respuesta_encuesta;
		$object->ciudad = $dataResponseCity->nombre_ciudad;
		$object->departamento = $dataResponseCity->distrito_ciudad;

		return $object;
	}

	/**
	 * Response evaluation Aldea 20
	 */

	public static function get__Total_Response__Aldea20($codigo_usuario_encuesta, $codigo_registro_fase_usuario) {
		// Innovacion
        $propiedad_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($codigo_usuario_encuesta, 'propiedad');
        $motivo_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($codigo_usuario_encuesta, 'motivo');
        $tipo_innovacion = Innpulsa::getResponseEvaluationAldea20__three__moment__innovacion($codigo_usuario_encuesta, 'tipo');
        $calificacion_innovacion = Innpulsa::Calificacion__Aldea20__three_moment__innovacion($codigo_registro_fase_usuario);
        $total_innovacion = ( ( isset($propiedad_innovacion->propiedad_score) ? $propiedad_innovacion->propiedad_score : 0 ) + ( isset($motivo_innovacion->motivo_score) ? $motivo_innovacion->motivo_score : 0 ) + ( isset($tipo_innovacion->tipo_score) ? $tipo_innovacion->tipo_score : 0 ) );

        $total_cal_innovacion = 0;
        if (count($calificacion_innovacion) > 0) {
            foreach($calificacion_innovacion as $cal) {
                $total_cal_innovacion = $total_cal_innovacion + $cal->total_scoring;
            }
        }

        $total_innovacion = $total_innovacion + ($total_cal_innovacion * 0.6);
        $total_innovacion_res = $total_innovacion * 0.3;

        // Escalabilidad

        $idioma_escalabilidad = Innpulsa::getResponseEvaluationAldea20__three__moment__escalabilidad($codigo_usuario_encuesta, 'idioma');
        $metas_escalabilidad = Innpulsa::getResponseEvaluationAldea20__three__moment__escalabilidad($codigo_usuario_encuesta, 'metas');
        $calificacion_escalabilidad = Innpulsa::Calificacion__potential__Aldea20__three_moment__equipo($codigo_registro_fase_usuario);
        $total_escalabilidad = ( ( isset($idioma_escalabilidad->idioma_score) ? $idioma_escalabilidad->idioma_score : 0 ) + ( isset($metas_escalabilidad->metas_score) ? $metas_escalabilidad->metas_score : 0 ) );

        $total_cal_escalabilidad = 0;
        if (count($calificacion_escalabilidad) > 0) {
            foreach($calificacion_escalabilidad as $cal) {
                $total_cal_escalabilidad = $total_cal_escalabilidad + $cal->total_score;
            }
        }

        $total_escalabilidad = $total_escalabilidad + ($total_cal_escalabilidad * 0.8);
        $total_escalabilidad_res = $total_escalabilidad * 0.15;

        // Traccion

        $alcance_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'alcance');
        $cuentas_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'cuentas');
        $estados_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'estados');
        $indicadores_traccion = Innpulsa::getResponseEvaluationAldea20__three__moment__traccion($codigo_usuario_encuesta, 'indicadores');
        $calificacion_traccion = Innpulsa::Calificacion__Aldea20__three_moment__traccion($codigo_registro_fase_usuario);
        $total_traccion = ( ( isset($alcance_traccion->alcance_score) ? $alcance_traccion->alcance_score : 0 ) + ( isset($cuentas_traccion->cuentas_score) ? $cuentas_traccion->cuentas_score : 0 ) + ( isset($estados_traccion->estados_score) ? $estados_traccion->estados_score : 0 ) + ( isset($indicadores_traccion->indicadores_score) ? $indicadores_traccion->indicadores_score : 0 ) );

        $total_cal_traccion = 0;
        if (count($calificacion_traccion) > 0) {
            foreach($calificacion_traccion as $cal_t) {
                $total_cal_traccion += (double)$cal_t->total_scoring;
            }
        }

        $total_traccion = $total_traccion + ( round($total_cal_traccion * 0.45, 2) );
        $total_traccion_res = $total_traccion * 0.1;

        // Equipo
        $experiencia_laboral_equipo = Innpulsa::getResponseEvaluationAldea20__three__moment__equipo($codigo_usuario_encuesta, 'laboral');
        $experiencia_sector_equipo = Innpulsa::getSectorExprerienceAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $experiencia_emprendedora_equipo = Innpulsa::getEmprendorExprerienceAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $roles_equipo = Innpulsa::getRolesAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $genero_equipo = Innpulsa::getGenderAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $riesgo_equipo = Innpulsa::getRiesgoAldea20__three__moment__equipo($codigo_usuario_encuesta);
        $decisiones_equipo = Innpulsa::getResponseEvaluationAldea20__three__moment__equipo($codigo_usuario_encuesta, 'decisiones');
        $calificacion_equipo = Innpulsa::Calificacion__Aldea20__three_moment__equipo($codigo_registro_fase_usuario);
        $total_equipo = ( ( isset($experiencia_laboral_equipo->laboral_score) ? $experiencia_laboral_equipo->laboral_score : 0 ) + ( isset($experiencia_sector_equipo['sector_scoring']) ? $experiencia_sector_equipo['sector_scoring'] : 0 ) + ( isset($experiencia_emprendedora_equipo['emprendor_scoring']) ? $experiencia_emprendedora_equipo['emprendor_scoring'] : 0 ) + ( isset($roles_equipo) ? $roles_equipo : 0 ) + ( isset($genero_equipo) ? $genero_equipo : 0 ) + ( isset($riesgo_equipo->riesgo_score) ? $riesgo_equipo->riesgo_score : 0 ) + ( isset($decisiones_equipo->decisiones_score) ?  $decisiones_equipo->decisiones_score : 0 ) );

        $total_cal_equipo= 0;
        if (count($calificacion_equipo) > 0) {
            foreach($calificacion_equipo as $cal) {
                $total_cal_equipo = $total_cal_equipo + (int)$cal->total_scoring;
            }
        }


        $total_equipo = $total_equipo + ($total_cal_equipo * 0.3);
        $total_equipo_res = $total_equipo * 0.45;

        $total_score = ( $total_equipo_res + $total_traccion_res + $total_escalabilidad_res + $total_innovacion_res );
		$total_score_res = $total_score * 10;

		return $total_score_res;
	}

	// Challenge 2

	// Innovacion

	public static function Calificacion__Aldea20__second_moment__innovacion($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 85);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 178);

		return $query->get();
	}

	// Mercado

	public static function Calificacion__Aldea20__second_moment__mercado($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 86);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 179);

		return $query->get();
	}

	public static function Calificacion__Aldea20__second_moment__mercado__pot($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 87);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 180);

		return $query->get();
	}

	public static function Calificacion__Aldea20__second_moment__mercado__bar($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 88);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 181);

		return $query->get();
	}

	// Equipo

	public static function Calificacion__Aldea20__second_moment__equipo($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 89);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 182);

		return $query->get();
	}

	public static function Calificacion__Aldea20__second_moment__equipo_cap($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 90);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 183);

		return $query->get();
	}

	// Alineacion

	public static function Calificacion__Aldea20__second_moment__alineacion($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 91);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 184);

		return $query->get();
	}

	// Recomendaciones

	public static function Calificacion__Aldea20__second_moment__recomendaciones($register) {
		$query = ResultadoEvaluation::query();
		$query->select(
			'opc_eval_pregunta.nombre_opc_eval_pregunta', 'resultado_evaluacion_emprendimiento.comentario_resultado_evaluacion',
			'opc_eval_pregunta.valor_opc_eval_pregunta as total_scoring',
			DB::raw("initcap(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')	) as nombre_usuario")
		);

		// Get items
		$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=','resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
		$query->join('core.opc_eval_pregunta', 'opc_eval_pregunta.codigo_opc_eval_pregunta', '=', 'evaluacion_item_emprendimiento.valor_evaluacion_item');

		// Get user information
		$query->leftJoin('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
		$query->leftJoin('core.persona', 'persona.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');

		$query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);
		$query->where('opc_eval_pregunta.codigo_grup_opc_eval_pregunta', '=', 92);
		$query->where('evaluacion_item_emprendimiento.codigo_item_evaluacion', '=', 185);

		return $query->get();
	}

	public static function getScoring__Aldea20__Challenge_Two($result) {
		if (isset($result)) {

			// Get Items Evaluation
			$query = ItemEvaluacionEmprendimiento::query();
			$query->select('item_evaluacion_emprendimiento.codigo_grupo_item_eval','grupo_item_eval.nombre_grupo_item_eval', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', 'item_evaluacion_emprendimiento.codigo_tipo_evaluacion_item','item_evaluacion_emprendimiento.codigo_grup_opc_eval_pregunta', 'item_evaluacion_emprendimiento.nombre_item_evaluacion', 'nombre_grup_opc_eval_pregunta');
			$query->leftJoin('core.grup_opc_eval_pregunta', 'item_evaluacion_emprendimiento.codigo_grup_opc_eval_pregunta', '=', 'grup_opc_eval_pregunta.codigo_grup_opc_eval_pregunta');
			$query->leftJoin('core.grupo_item_eval', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval', '=', 'grupo_item_eval.codigo_grupo_item_eval');
			$query->where('item_evaluacion_emprendimiento.codigo_estado', '=', Estado::getState('active'));
			$query->where('codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento);
			$query->orderBy('item_evaluacion_emprendimiento.orden_item_eval_emprendi','asc');
			$arItems = $query->get();

			// Groups Item Evaluation
			$query = GrupoItemEval::query();
			$query->select('texto_comentario_documentador','item_evaluacion_emprendimiento.codigo_grupo_item_eval','nombre_grupo_item_eval', 'promedio_conmutado');
			$query->join('core.item_evaluacion_emprendimiento', 'grupo_item_eval.codigo_grupo_item_eval', '=', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval');

			$query->leftjoin('core.comen_documentador', function($join) use($result){
				$join->on('comen_documentador.codigo_grupo_item_eval', '=', 'grupo_item_eval.codigo_grupo_item_eval')
					->where('comen_documentador.codigo_estado', '=', Estado::getState('active'))
					->where('comen_documentador.codigo_registro_fase_usuario', '=', $result->codigo_registro_fase_usuario);
			});

			$query->where('codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento);
			$query->where('item_evaluacion_emprendimiento.codigo_estado', Estado::getState('active'));
			$query->orderBy('grupo_item_eval.orden_grupo_item_eval','asc');
			$query->groupBy('grupo_item_eval.codigo_grupo_item_eval','item_evaluacion_emprendimiento.codigo_grupo_item_eval','nombre_grupo_item_eval','texto_comentario_documentador');
			$groups = $query->get();

			$query = EvaluacionItemEmprendimiento::query();
			$query->select('asignacion_jurado.codigo_usuario', 'evaluacion_item_emprendimiento.codigo_item_evaluacion', 'valor_evaluacion_item', 'item_evaluacion_emprendimiento.codigo_grupo_item_eval', DB::raw('COALESCE(opc_eval_pregunta.valor_opc_eval_pregunta, 0) AS valor_opc_eval_pregunta'));
			$query->join('core.item_evaluacion_emprendimiento', 'item_evaluacion_emprendimiento.codigo_item_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_item_evaluacion');
			$query->leftJoin('core.opc_eval_pregunta', 'evaluacion_item_emprendimiento.valor_evaluacion_item', '=', 'opc_eval_pregunta.codigo_opc_eval_pregunta');
			$query->join('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion', '=', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion');
			$query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
			$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
			$query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
			$query->where('resultado_evaluacion_emprendimiento.codigo_evaluacion_emprendimiento', $result->codigo_evaluacion_emprendimiento);
			$query->where('registro_convocatoria.codigo_registro_convocatoria', $result->codigo_registro_convocatoria);
			$califications = $query->get();

			// Experts
			$query = AsignacionJurado::query();
			$query->select('asignacion_jurado.codigo_usuario', 'usuario.nombre_usuario', 'fecha_resultado_evaluacion', 'hora_resultado_evaluacion');
			$query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
			$query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'asignacion_jurado.codigo_usuario');
			$query->leftJoin('core.resultado_evaluacion_emprendimiento', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado', '=', 'asignacion_jurado.codigo_asignacion_jurado');
			$query->where('registro_fase_usuario.codigo_registro_fase_usuario',  $result->codigo_registro_fase_usuario);
			$query->where('asignacion_jurado.codigo_estado', '!=',  Estado::getState('removed'));
			$experts = $query->get();

			$total = 0;
			$innovation = 0;
			$market = 0;
			$enterprising = 0;
			$objetives = 0;
			$recomendations = 0;
			foreach ($groups as $group) {
				$itemsEval = $arItems->where('codigo_grupo_item_eval', $group->codigo_grupo_item_eval);
				if($itemsEval->count() > 0) {
					foreach ($itemsEval as $itemeval) {
						// Porcentajes
						foreach ($experts as $expert) {
							unset($calification);

							$itemx = new \stdClass();
							$itemx->codigo_usuario = $expert->codigo_usuario;
							$calification = $califications->where('codigo_usuario', $expert->codigo_usuario)->where('codigo_item_evaluacion', $itemeval->codigo_item_evaluacion)->where('codigo_grupo_item_eval', $group->codigo_grupo_item_eval)->first();
							$valor_experto = 0;

							if($calification instanceof EvaluacionItemEmprendimiento) {

								if ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('innovation_component')]) ) {
									$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.32) * 10), 2);
									$total += $valor_experto;
									$innovation = $valor_experto;
								}
								elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('market_2020')]) ) {
									$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.11) * 10), 2);
									$total += $valor_experto;
									$market = $valor_experto;
								}
								elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('enterprising_team_2020')]) ) {
									switch ($calification->codigo_item_evaluacion) {
										case 182:
											$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.10) * 10), 2);
										break;
										case 183:
											$valor_experto = round((($calification->valor_opc_eval_pregunta * 0.05) * 10), 2);
										break;
									}
									$total += $valor_experto;
									$enterprising = $valor_experto;
								}
								elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('objetives')]) ) {
									$valor_experto = round(((($calification->valor_opc_eval_pregunta * 0.12) * 100) / 12), 2);
									$total += $valor_experto;
									$objetives = $valor_experto;
								}
								elseif ( in_array($group->codigo_grupo_item_eval, [GrupoItemEval::getGroup('recomendations')]) ) {
									$valor_experto = round(((($calification->valor_opc_eval_pregunta * 0.08) * 100) / 8), 2);
									$total += $valor_experto;
									$recomendations = $valor_experto;
								}
							}
						}
					}
				}
			}


			$object = new \stdClass();
			$object->innovation = $innovation;
			$object->market = $market;
			$object->enterprising = $enterprising;
			$object->objetives = $objetives;
			$object->recomendations = $recomendations;
			$object->total = $total = $total / 3;

			return $object;
		} else {
			return null;
		}
	}

	/**
	 * Custom data report.
	 *
	 */
	public static function aldeaReport(Collection $answers, UsuarioEncuesta $userSurvey)
	{
        $report = new \stdClass();

        switch ($userSurvey->codigo_encuesta) {
			case self::$challenge:
				// Empresa
                $report->sector_interes = SectorInteres::getSectorByActivity($answers->where('question_id', 1234)->first()->answer)->nombre_sector_economico;

                // Diagnostico
                $report->data_admin_management = self::getResultScoreAdminManagementCustom($answers, $userSurvey);
                $report->data_human_management = self::getResultScoreHumanManagementCustom($answers, $userSurvey);
                $report->data_financial_management = self::getResultScoreFinancialManagementCustom($answers, $userSurvey);
                $report->data_commercial_management = self::getResultScoreCommercialManagementCustom($answers, $userSurvey);
                $report->data_production_management = self::getResultScoreProductionManagementCustom($answers, $userSurvey);
                $report->data_general = self::getResultGeneralCustom($answers, $userSurvey, $report);
                return $report;
            break;
            case self::$challenge_empodera:
                $report->sector_interes = SectorInteres::getSectorByActivity($answers->where('question_id', 2805)->first()->answer)->nombre_sector_economico;

                // Diagnostico
                $report->data_admin_management = self::getResultScoreAdminManagementCustom($answers, $userSurvey);
                $report->data_human_management = self::getResultScoreHumanManagementCustom($answers, $userSurvey);
                $report->data_financial_management = self::getResultScoreFinancialManagementCustom($answers, $userSurvey);
				$report->data_commercial_management = self::getResultScoreCommercialManagementCustom($answers, $userSurvey);
                $report->data_production_management = self::getResultScoreProductionManagementCustom($answers, $userSurvey);
                $report->data_general = self::getResultGeneralCustom($answers, $userSurvey, $report);
                return $report;
			break;
		}

	}

	public static function getResultScoreAdminManagementCustom(Collection $answers, UsuarioEncuesta $userSurvey)
	{
        switch ($userSurvey->codigo_encuesta) {
            case Innpulsa::$challenge:
                $general_1 = 1261;
                $general_2 = 1262;
                $general_3 = 1263;
                $general_4 = 1264;
                $average_general = [$general_1, $general_2, $general_3, $general_4];
                $answer_sector = 1234;
            break;
            case Innpulsa::$challenge_empodera:
                $general_1 = 2865;
                $general_2 = 2866;
                $general_3 = 2867;
                $general_4 = 2868;
                $average_general = [$general_1, $general_2, $general_3, $general_4];
                $answer_sector = 2805;
            break;
        }
		$result = new \stdClass();
		$score_admin_management = $answers->where('question_id', $general_1)->first()->score_option+$answers->where('question_id', $general_2)->first()->score_option+$answers->where('question_id', $general_3)->first()->score_option + $answers->where('question_id', $general_4)->first()->score_option;
		$texto_respuesta = "";

		// Average General
		$averageGeneral = self::getAverages($average_general);

		// Answer Sector
		$sector = $answers->where('question_id', $answer_sector)->first();
		// Sector Economic
		$activityEconomic = SectorInteres::find($sector->answer)->first();

		if($activityEconomic instanceof SectorInteres){
			// Average Sector
			$averageEconomy = self::getAverages($average_general, $activityEconomic->codigo_sector_economico);
		}

		if($score_admin_management<8){
			$texto_respuesta = "<p>Para el crecimiento de una compañía es muy importante definir un norte claro y una meta de hacia dónde quiere llevar su empresa, el cual debe ser comunicado e interiorizado por cada uno de los colaboradores de la compañía.</p><p>Estar en contacto con sus clientes permanentemente es primordial, para identificar que les “duele” y como el producto o servicio desarrollado por su empresa ayuda a resolver ese dolor,  preparare un discurso de su propuesta de valor, claro y contundente, que le permita explicarle a sus clientes por qué deben comprarle su producto o servicio y no el de la competencia. Fortalecer esta área de la compañía le permitirá aprovechar de mejor manera las oportunidades y amenazas que constantemente influirán en el desempeño de su compañía.</p>";
		}else if($score_admin_management<16){
			$texto_respuesta = "<p>Es importante revisar las metas de crecimiento de mediano plazo de su empresa, teniendo en cuenta  el diferencial, el potencial de mercado, y la retroalimentación que realizan los clientes de su producto o servicio.</p><p>Fortalecer constantemente esta área de la compañía le permitirá aprovechar de mejor manera las oportunidades y amenazas que constantemente influirán en el desempeño de su compañía.</p>";
		}else{
			$texto_respuesta = "<p>Su empresa tiene claramente definido el norte y las metas a alcanzar en el mediano plazo, es importante asegurarse que este norte y las metas sean conocidas y acogidas por cada uno de los colaboradores de su empresa, que cada uno de ellos se sienta identificado con la razón de ser de la compañía, y  que este sentido de pertenencia se trasmita a sus clientes y aliados.</p><p>Es primordial seguir fortaleciendo las relaciones con sus clientes, pues ellos son la fuente de potenciales mejoras a sus productos y servicios actuales, y junto con información del entorno ofrecerán insumos para desarrollar nuevos productos o servicios.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->total = $score_admin_management;
		$result->score = round($score_admin_management/4);
		$result->score_general = $averageGeneral;
		$result->score_economy = $averageEconomy;
		return $result;
	}
	public static function getResultScoreHumanManagementCustom(Collection $answers, UsuarioEncuesta $userSurvey)
	{
        switch ($userSurvey->codigo_encuesta) {
            case Innpulsa::$challenge:
                $general_1 = 1265;
                $general_2 = 1266;
                $general_3 = 1267;
                $general_4 = 1268;
                $average_general = [1265, 1266, 1267, 1268];
                $answer_sector = 1234;
            break;
            case Innpulsa::$challenge_empodera:
                $general_1 = 2861;
                $general_2 = 2862;
                $general_3 = 2863;
                $general_4 = 2864;
                $average_general = [$general_1, $general_2, $general_3, $general_4];
                $answer_sector = 2805;
            break;
        }

		$result = new \stdClass();
		$score_human_management = $answers->where('question_id', $general_1)->first()->score_option+$answers->where('question_id', $general_2)->first()->score_option+$answers->where('question_id', $general_3)->first()->score_option+$answers->where('question_id', $general_4)->first()->score_option;

		$averageGeneral = Innpulsa::getAverages($average_general);

		// Answer Sector
		$sector = $answers->where('question_id', $answer_sector)->first();
		// Sector Economic
		$activityEconomic = SectorInteres::find($sector->answer)->first();

		if($activityEconomic instanceof SectorInteres){
			// Average Sector
			$averageEconomy = Innpulsa::getAverages($average_general, $activityEconomic->codigo_sector_economico);
		}

		$texto_respuesta = "";
		if($score_human_management<8){
			$texto_respuesta = "<p>La empresa cuenta con oportunidades de crecer y ser más competitiva en el mercado si empieza a innovar en su interior, estas innovaciones puede ser enfocadas en productos/servicios, procesos o modelo de negocio. Es importante que, en primera medida, la compañía establezca mecanismos para que los colaboradores planteen sus ideas; y procesos para evaluar, seleccionar y ejecutar las ideas con mayor potencial, y de esta manera comenzar a crear una cultura innovadora dentro la compañía.</p><p>Para esto es primordial ejecutar acciones encaminadas a fortalecer el desarrollo del capital humano de la empresa, en pro de un mayor sentido de pertenencia, una mayor motivación y un clima laboral óptimo para el libre desarrollo profesional.</p>";
		}else if($score_human_management<16){
			$texto_respuesta = "<p>La empresa cuenta con oportunidades de crecer y ser más competitiva en el mercado si fortalece la innovación en su interior, estas innovaciones puede ser enfocadas en productos, procesos o modelo de negocio. Es importante acelerar las acciones que están implementando para fortalecer el desarrollo del capital humano de la empresa, y hacer de las innovaciones una realidad en la compañía.</p>";
		}else{
			$texto_respuesta = "<p>La empresa ya implementa innovaciones al interior de la compañía, es recomendable que fortalezcan su articulación con el ecosistema de innovación y emprendimiento, y se apalanquen en instrumentos de fomento a la Ciencia, tecnología e innovación, lo cual les permitirá acelerar este proceso innovador y mantenerse a la vanguardia en su industria.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->total = $score_human_management;
		$result->score = round($score_human_management/4);
		$result->score_general = $averageGeneral;
		$result->score_economy = $averageEconomy;
		return $result;
	}
	public static function getResultScoreFinancialManagementCustom(Collection $answers, UsuarioEncuesta $userSurvey)
	{
        switch ($userSurvey->codigo_encuesta) {
            case Innpulsa::$challenge:
                $general_1 = 1269;
                $general_2 = 1270;
                $general_3 = 1271;
                $general_4 = 1272;
                $average_general = [1269, 1270, 1271, 1272];
                $answer_sector = 1234;
            break;
            case Innpulsa::$challenge_empodera:
                $general_1 = 2840;
                $general_2 = 2841;
                $general_3 = 2842;
                $general_4 = 2843;
                $average_general = [$general_1, $general_2, $general_3, $general_4];
                $answer_sector = 2805;
            break;
        }
		$result = new \stdClass();
		@$score_financial_management = $answers->where('question_id', $general_1)->first()->score_option + $answers->where('question_id', $general_2)->first()->score_option + $answers->where('question_id', $general_3)->first()->score_option+$answers->where('question_id', $general_4)->first()->score_option;

		$averageGeneral = Innpulsa::getAverages($average_general);

		// Answer Sector
		$sector = $answers->where('question_id', $answer_sector)->first();
		// Sector Economic
		$activityEconomic = SectorInteres::find($sector->answer)->first();

		if($activityEconomic instanceof SectorInteres){
			// Average Sector
			$averageEconomy = Innpulsa::getAverages($average_general, $activityEconomic->codigo_sector_economico);
		}

		$texto_respuesta = "";
		if($score_financial_management<8){
			$texto_respuesta = "<p>Es primordial que siempre la compañía maneje cuentas separadas de las cuentas personales de sus dueños, pues esto le permite conocer,  analizar y tomar los correctivos necesarios para tener una buena salud financiera de la empresa.</p><p>Es importante implementar un mecanismo financiero en su empresa que le permita identificar de manera clara cuáles son sus ingresos, costos  y gastos,  para establecer si su negocio es rentable o no, es decir, saber si está perdiendo o ganando dinero.</p><p>Es recomendable construir estados financieros como balance, estado de resultados y flujo de caja, los cuales le permitirán organizar la información financiera de su empresa de manera periódica para tomar decisiones estratégicas e inteligentes que lo lleven al crecimiento económico de su empresa.</p>";
		}else if($score_financial_management<16){
			$texto_respuesta = "<p>La organización de la información financiera en una empresa es un factor determinante para el funcionamiento de la misma, llevar un control diario de los costos, gastos, ingresos  es imprescindible para estar al tanto de todas las transacciones y poder solventar de forma rápida los problemas de liquidez que puedan darse durante la operación de la empresa.</p><p>Es recomendable implementar al interior de su empresa procedimientos, normas o políticas para el manejo de las diferentes transacciones como: compras, ventas, entradas y salidas de caja, pagos, etc.; esto le permitirá mantener unificada la información.</p>";
		}else{
			$texto_respuesta = "<p>Su empresa cuenta con una organización financiera que le permite estar al tanto de las transacciones realizadas por su empresa, se recomienda hacer una revisión y comparación periódica de estos y utilizar y analizar  indicadores  que le permitan determinar el estado de su empresa en términos de crecimiento,  liquidez, eficiencia, desempeño, productividad, entre otros. Se recomienda hacer una revisión periódica de los indicadores financieros para medir la efectividad de sus acciones en el mercado y de su negocio en el tiempo.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->total = $score_financial_management;
		$result->score = round($score_financial_management/4);
		$result->score_general = $averageGeneral;
		$result->score_economy = $averageEconomy;
		return $result;
	}
	public static function getResultScoreCommercialManagementCustom(Collection $answers, UsuarioEncuesta $userSurvey)
	{
        switch ($userSurvey->codigo_encuesta) {
            case Innpulsa::$challenge:
                $general_1 = 1273;
                $general_2 = 1274;
                $general_3 = 1275;
                $general_4 = 1276;
                $average_general = [1273, 1274, 1275, 1276];
                $answer_sector = 1234;
            break;
            case Innpulsa::$challenge_empodera:
                $general_1 = 2853;
                $general_2 = 2854;
                $general_3 = 2855;
                $general_4 = 2856;
                $average_general = [$general_1, $general_2, $general_3, $general_4];
                $answer_sector = 2805;
            break;
        }
		$result = new \stdClass();
		$score_comercial_management = $answers->where('question_id', $general_1)->first()->score_option + $answers->where('question_id', $general_2)->first()->score_option + $answers->where('question_id', $general_3)->first()->score_option + $answers->where('question_id', $general_4)->first()->score_option;

		$averageGeneral = Innpulsa::getAverages($average_general);

		// Answer Sector
		$sector = $answers->where('question_id', $answer_sector)->first();
		// Sector Economic
		$activityEconomic = SectorInteres::find($sector->answer)->first();

		if($activityEconomic instanceof SectorInteres){
			// Average Sector
			$averageEconomy = Innpulsa::getAverages($average_general, $activityEconomic->codigo_sector_economico);
		}

		$texto_respuesta = "";
		if($score_comercial_management<8){
			$texto_respuesta = "<p>Su empresa debe concentrar esfuerzos en definir e identificar con claridad a su cliente, sus necesidades, sus gustos y sus patrones de comportamiento, de manera que pueda garantizar que su oferta de productos y/o servicios es adecuada para su cliente objetivo. Una vez usted conoce a su cliente, es importante diseñar estrategias adecuadas para encontrar los canales para poder llegar a él y de ésta manera aumentar la probabilidad de incrementar  sus ventas.</p><p>Es importante  estar en constante contacto con su cliente validando que sus productos y/o servicios sean adecuados para él, pues es por éste que su compañía se mantiene en el mercado.</p>";
		}else if($score_comercial_management<16){
			$texto_respuesta = "<p>La empresa debe fortalecer su estrategia  para llegar a sus clientes, utilizando los canales óptimos y manteniendo un diferencial claro de sus productos y/o servicios frente a los de su competencia. Es importante  estar en constante contacto con su cliente validando que sus productos y/o servicios sean adecuados para él, pues es por éste que su compañía se mantiene en el mercado.</p>";
		}else{
			$texto_respuesta = "<p>Su empresa se encuentra en un nivel óptimo en el  área de mercadeo y ventas dado que tiene plenamente identificado a su cliente objetivo y su diferencial vs la competencia. No obstante, es importante que la empresa alimente el deseo de ser la número uno en el mercado que atiende, para lo cual se le recomienda que desarrolle plan de expansión de mercado, en dónde contemple nuevos nichos de mercado, posibilidades de internacionalización y su potencial de desarrollar nuevos productos y/o servicios.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->total = $score_comercial_management;
		$result->score = round($score_comercial_management/4);
		$result->score_general = $averageGeneral;
		$result->score_economy = $averageEconomy;
		return $result;
	}
	public static function getResultScoreProductionManagementCustom(Collection $answers, UsuarioEncuesta $userSurvey)
	{
        switch ($userSurvey->codigo_encuesta) {
            case Innpulsa::$challenge:
                $general_1 = 1277;
                $general_2 = 1278;
                $general_3 = 1279;
                $general_4 = 1280;
                $average_general = [1277, 1278, 1279, 1280];
                $answer_sector = 1234;
            break;
            case Innpulsa::$challenge_empodera:
                $general_1 = 2857;
                $general_2 = 2858;
                $general_3 = 2859;
                $general_4 = 2860;
                $average_general = [$general_1, $general_2, $general_3, $general_4];
                $answer_sector = 2805;
            break;
        }
		$result = new \stdClass();
		$score_production_management = $answers->where('question_id', $general_1)->first()->score_option+$answers->where('question_id', $general_2)->first()->score_option+$answers->where('question_id', $general_3)->first()->score_option+$answers->where('question_id', $general_4)->first()->score_option;

		$averageGeneral = Innpulsa::getAverages($average_general);

		// Answer Sector
		$sector = $answers->where('question_id', $answer_sector)->first();
		// Sector Economic
		$activityEconomic = SectorInteres::find($sector->answer)->first();

		if($activityEconomic instanceof SectorInteres){
			// Average Sector
			$averageEconomy = Innpulsa::getAverages($average_general, $activityEconomic->codigo_sector_economico);
		}

		$texto_respuesta = "";
		if($score_production_management<8){
			$texto_respuesta = "<p>La empresa debe conocer en detalle cómo calcular los precios de venta de sus productos y servicios, partiendo del cálculo de los costos y gastos de producción como pueden ser las materias primas, mano de obra, infraestructura, entre otros; y la rentabilidad que esperan obtener, sin dejar de lado que esta información debe ser contrastada con los precios de mercado y de la competencia.</p><p>El ciclo productivo y la capacidad instalada de la compañía debe ser estimada adecuadamente por la empresa, pues esta información es útil para gestionar mejoras que le permitan capturar eficiencias de producción que redunden en una mayor productividad.</p>";
		}else if($score_production_management<16){
			$texto_respuesta = "<p>La empresa debe identificar oportunidades de mejora dentro de su proceso productivo que le permitan  ser más eficiente y alcanzar las metas futuras de la compañía,  partiendo del conocimiento de los precios de venta de sus productos y servicios, así como del cumplimiento de la normatividad vigente.</p>";
		}else{
			$texto_respuesta = "<p>Su empresa se encuentra en un nivel óptimo en el área de producción, puesto que tiene plenamente definido un proceso de cálculo de precio de venta de sus productos y servicios, determinada su capacidad instalada, y cumple con la normatividad vigente.</p><p>Es importante, gestionar una comunicación eficiente entre las áreas de mercadeo y la de producción para incorporar mejora a sus procesos de cara a la satisfacción de los clientes.</p>";
		}
		$result->texto_respuesta = $texto_respuesta;
		$result->total = $score_production_management;
		$result->score = round($score_production_management/4);
		$result->score_general = $averageGeneral;
		$result->score_economy = $averageEconomy;
		return $result;
	}

	public static function getResultGeneralCustom(Collection $answers, UsuarioEncuesta $userSurvey, $report)
	{
		$result = new \stdClass();
		$texto_respuesta = '';
        $texto_carta = '';
        switch ($userSurvey->codigo_encuesta) {
            case Innpulsa::$challenge:
                $general_program_challenge = self::$challenge_reto1;
                $general_stage_challenge = self::$stage_challenge_1;
                $general_stage_challenge_1_2 = self::$stage_challenge_1_2;

                $validacion_comercial_1 = 1256;
                $validacion_comercial_2 = 1667;

                $validacion_venta_1 = 1395;
                $validacion_venta_2 = 1851;
                $validacion_venta_3 = 1258;
                $validacion_venta_4 = 1259;
                $validacion_venta_5 = 1260;
            break;
            case Innpulsa::$challenge_empodera:
                $general_program_challenge = self::$challenge_empodera_reto_1;
                $general_stage_challenge = self::$stage_challenge_empodera_1;
                $general_stage_challenge_1_2 = self::$stage_challenge_empodera_1_2;

                $validacion_comercial_1 = 2817;
                $validacion_comercial_2 = '';

                $validacion_venta_1 = 2878;
                $validacion_venta_2 = '';
                $validacion_venta_3 = 2875;
                $validacion_venta_4 = 2876;
                $validacion_venta_5 = 2877;
            break;
        }

		// Nombre usuario
		$query = Usuario::query();
		$query->select(DB::raw("initcap(owner.nombre_usuario) as nombre_usuario"), 'owner.correo_electronico as correo_electronico');
		$query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
		$query->join('seguridad.usuario as owner', 'entidad.seg_codigo_usuario', '=', 'owner.codigo_usuario');
		$query->where('usuario.codigo_usuario', $userSurvey->codigo_usuario);
		$founder = $query->first();

		// Proxima aplicacion
		$query = RegistroFaseUsuario::query();
		$query->select('tiempo_espera_nueva_aplicacion', 'fecha_registro_convocatoria', DB::raw('COALESCE(registro_fase_usuario.rango_inferior_eval_fase, fase.rango_inferior_evaluacion) as rango_inferior_eval_fase'));
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.convocatoria','registro_convocatoria.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
		$query->join('seguridad.usuario','registro_fase_usuario.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.fase','registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->where('registro_convocatoria.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
		$query->where('registro_convocatoria.codigo_convocatoria', $general_program_challenge);
		$query->where('registro_fase_usuario.codigo_fase', $general_stage_challenge);
		$registered = $query->first();
		$proximo_registro = date('Y-m-d', strtotime("+$registered->tiempo_espera_nueva_aplicacion day", strtotime($registered->fecha_registro_convocatoria)));

		$carta = '
			<div class="inner-section-program-review text-justify">
				<div class="text-md lighter-text">
					<label class="label title text-md lighter-text">Estimado(a), <strong>%s</strong></label>

					<p class="lighter-text">Recibe un afectuoso saludo de <strong>iNNpulsa Colombia</strong>, la agencia del Gobierno Nacional que trabaja para forjar las empresas del futuro, empresas preparadas para asumir el reto de crecer colaborando y conectándose.</p>
					%s
				</div>
				<br />
				<ul class="list-detail-program-review no-bullet">
					<li>
						<div class="heavy-text dark-text text-md">ALDEA de iNNpulsa Colombia</div>
					</li>
				</ul>
			</div>
			%s';

		$recomendacion_a = "
			<p>Aunque nos dijiste que tu producto tiene validación comercial, no nos reportaste la información de ventas de tu negocio, por lo que no fue posible encontrar soporte a esa validación comercial.</p>
			<p>Te invitamos a seguir avanzando con tu iniciativa, es importante materializar la idea y no esperar a tener la versión final del producto (bien o servicio) para probarlo con posibles clientes, ellos pueden ser una fuente de mejora y representan el punto de partida de tu negocio.</p>
			<p>Cuando hayas validado comercialmente tu producto, puedes volver a presentarte.</p>";

		$recomendacion_b = "
			<p>Todavía estás en etapa de idea y no has validado comercialmente tu producto, por eso te invitamos a seguir avanzando con tu iniciativa, es importante materializar la idea y no esperar a tener la versión final del producto (bien o servicio) para probarlo con posibles clientes, ellos pueden ser una fuente de mejora y representan el punto de partida de tu negocio.</p>
			<p>Cuando tengas un producto (bien o servicio) desarrollado, con un diferencial para el mercado y lo hayas validado comercialmente, puedes volver a presentarte.</p>";

		$recomendacion_c = "
			<p>Te recomendamos fortalecer  el valor agregado que tu producto o servicio aporta a los clientes, para que puedas identificar cómo tu  producto resuelve la necesidad de tu cliente, qué beneficios debe esperar el cliente de tu producto y por qué te debe elegir a ti y no a tu competencia.</p>
			<p>Te invitamos a seguir adelante y a  incluir mejoras que le aporten un mayor valor a tu oferta y te diferencien de la competencia, y a su vez, te permitan tener una mejor percepción de tus clientes.</p>";

		// Scoring reto 1
		$scoring_1 = $userSurvey->puntaje_total_encuesta;

		// Scoring reto 1.2
		$query = RegistroFaseUsuario::query();
		$query->select(DB::raw('COALESCE(registro_fase_usuario.rango_inferior_eval_fase, fase.rango_inferior_evaluacion) as rango_inferior_eval_fase'), DB::raw('COALESCE(registro_fase_usuario.rango_superior_eval_fase, fase.rango_superior_evaluacion) as rango_superior_eval_fase'), 'puntaje_total_eval_emprendi');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.asignacion_jurado', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'asignacion_jurado.codigo_registro_fase_usuario');
		$query->join('core.resultado_evaluacion_emprendimiento', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
		$query->join('core.fase','registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->where('registro_convocatoria.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
		$query->where('registro_convocatoria.codigo_convocatoria', $general_program_challenge);
		$query->where('registro_fase_usuario.codigo_fase', $general_stage_challenge_1_2);
		$scoring_1_2 = $query->first();

		// Registe reto 1.2
		$query = RegistroFaseUsuario::query();
		$query->select('registro_fase_usuario.*');
		$query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
		$query->join('core.fase','registro_fase_usuario.codigo_fase', '=', 'fase.codigo_fase');
		$query->where('registro_convocatoria.codigo_usuario_encuesta', $userSurvey->codigo_usuario_encuesta);
		$query->where('registro_convocatoria.codigo_convocatoria', $general_program_challenge);
		$query->where('registro_fase_usuario.codigo_fase', $general_stage_challenge_1_2);
		$register_1_2 = $query->first();

		// Validaciones
		if( $scoring_1 <= $registered->rango_inferior_eval_fase || ($scoring_1_2 != null && ($scoring_1_2->puntaje_total_eval_emprendi < $scoring_1_2->rango_inferior_eval_fase || $scoring_1_2->puntaje_total_eval_emprendi > $scoring_1_2->rango_superior_eval_fase)) || ($register_1_2 instanceof RegistroFaseUsuario && $register_1_2->codigo_estado == Estado::getState('returned')) || (!$scoring_1_2 instanceof RegistroFaseUsuario))
		{
			// Carta no pasan
			$texto_carta = sprintf($carta, $founder->nombre_usuario, '
				<p class="lighter-text">En esta ocasión, el área emprendimiento de iNNpulsa Colombia te informa que <strong>no has sido seleccionado(a)</strong> para participar en el segundo reto "Conversa con expertos". Sin embargo, tienes en tus manos algunos consejos de acuerdo a las respuestas que nos diste en el primer reto "Conozcámonos", que esperamos sean de gran utilidad.</p>

				<p class="lighter-text">Que puedes hacer ahora:</p>

				<ul class="disc">
					<li>Seguir los consejos que te entrega ALDEA <span class="text-sm">(Ver sección Diagnóstico)</span>.</li>
					<li>Desarrollar los cursos virtuales de nuestros aliados y conocer la oferta pública de programas para el fomento empresarial <span class="text-sm">(Ver sección Oportunidades)</span>.</li>
					<li>Profundizar en las finanzas de tu negocio.</li>
				</ul>
				<br/>
				<p class="lighter-text">Gracias por mostrar interés en ser parte de ALDEA. ¡Recuerda que eres el motor de nuestra organización!</p>',

				'<hr class="no-margin">
				<div class="inner-section-program-review">
					<ul class="no-bullet text-md lighter-text">
						<li class="heavy-text dark-text">
							Solo podrás volver a aplicar a esta convocatoria desde el <span class="text-md lighter-text">'.$proximo_registro.'</span>.
						</li>
						<li>
							Para volver a aplicar a esta convocatoria <a href="https://emprendimientoinnpulsa.com/convocatoria/reto1-aldea-innpulsa" target="_blank" title="Invitación para empresarios y emprendedores">haz clic aquí</a>.
						</li>
					</ul>
				</div>'
			);

			// Validacion comercial
			if($answers->where('question_id', $validacion_comercial_1)->first()->option_code_y == $validacion_comercial_2)
			{
				// Validacion con ventas
				$vcomencial = $answers->where('question_id', $validacion_venta_1)->first();
				if($vcomencial instanceof RespuestaEncuesta && $vcomencial->option_code_y == $validacion_venta_2)
				{
					$_1258 = $answers->where('question_id', $validacion_venta_3)->first();
					$_1259 = $answers->where('question_id', $validacion_venta_4)->first();
					$_1260 = $answers->where('question_id', $validacion_venta_5)->first();

					$ventas = 0;
					if($_1258 instanceof RespuestaEncuesta) $ventas += $_1258->answer;
					if($_1259 instanceof RespuestaEncuesta) $ventas += $_1259->answer;
					if($_1260 instanceof RespuestaEncuesta) $ventas += $_1260->answer;

					if($ventas <= 300000) {
						$texto_respuesta = $recomendacion_a;
					}else{
						$texto_respuesta = $recomendacion_c;
					}
				}else{
					$texto_respuesta = $recomendacion_c;
				}
			}else{
				$_1258 = $answers->where('question_id', $validacion_venta_1)->first();
				$_1259 = $answers->where('question_id', $validacion_venta_2)->first();
				$_1260 = $answers->where('question_id', $validacion_venta_3)->first();
				$ventas = 0;
				if($_1258 instanceof RespuestaEncuesta) $ventas += $_1258->answer;
				if($_1259 instanceof RespuestaEncuesta) $ventas += $_1259->answer;
				if($_1260 instanceof RespuestaEncuesta) $ventas += $_1260->answer;

				if($ventas <= 300000) {
					$texto_respuesta = $recomendacion_b;
				}else{
					$texto_respuesta = $recomendacion_c;
				}
			}
		}else{
			// Avanzan a segunda fase
			$texto_respuesta = '';
			$texto_carta = sprintf($carta, $founder->nombre_usuario, '
				<p class="lighter-text">El área de emprendimiento de iNNpulsa Colombia te informa que <strong>has sido seleccionado(a)</strong> para participar en el segundo reto "Conversemos con expertos". Tienes en tus manos algunos consejos de acuerdo a las respuestas que nos diste en el primer reto "Conozcámonos", que esperamos sean de gran utilidad.</p>

				<p class="lighter-text">Que puedes hacer ahora:</p>

				<ul class="disc">
					<li>Seguir los consejos que te entrega ALDEA <span class="text-sm">(Ver sección Diagnóstico)</span>.</li>
					<li>Desarrollar los cursos virtuales de nuestros aliados y conocer la oferta pública de programas para el fomento empresarial <span class="text-sm">(Ver sección Oportunidades)</span>.</li>
					<li>Profundizar en las finanzas de tu negocio.</li>
				</ul>
				<br/>
				<p class="lighter-text">En un plazo máximo de 60 días estaremos en contacto contigo para indicarte lugar, fecha y hora de tu cita para conversar con expertos, el segundo reto de Aldea, en el que tendrás la oportunidad de competir para ganarte una llave de acceso a servicios especializados.</p>',
				''
			);
		}

		$result->texto_carta = $texto_carta;
		$result->texto_respuesta = $texto_respuesta;
		$result->oportunidades = self::getOportunidades($answers, $report);
		return $result;
	}
	public static function getOportunidades(Collection $answers, $report)
	{
		$oportunidades = $cursos = [];

		$cursos['derechos_de_autor_y_emprendimiento_digital'] = ['url' => 'https://apps.co/cursos/propiedad-intelectual-derecho-de-autor-y-emprendim/', 'titulo' => 'Derechos de autor y emprendimiento digital', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/motivacion-emprendedores.jpg'];

		$cursos['pacto_de_socios'] = ['url' => 'https://apps.co/cursos/el-pacto-de-socios/', 'titulo' => 'Pacto de socios', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/plan-financiero-servicios.jpg'];

		// $cursos['introduccion_a_la_inversion_angel'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/introduccion-a-la-inversion-angel', 'titulo' => 'Introducción a la inversión Ángel', 'descripcion' => '', 'imagen' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/2016/05/rnai-vehiculos-Inversion-angel.png'];

		// $cursos['negociacion_con_inversionistas'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/negociacion-con-inversionistas', 'titulo' => 'Negociación con inversionistas', 'descripcion' => 'Fortalecer competencias en cuento a la negociación con inversionistas, mejora las habilidades para un proceso de negociación exitoso.', 'imagen' => 'http://static.tuproyecto.com/images/courses/negociacion-inversionistas.jpg'];

		// $cursos['planeacion_financiera_para_emprendedores'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/planeacion-financiera-emprendedores', 'titulo' => 'Planeación financiera para emprendedores', 'descripcion' => 'Fortalecer competencias en cuanto a la planeación financiera.', 'imagen' => 'http://static.tuproyecto.com/images/courses/planeacion-financiera.jpg'];

		// $cursos['persuasion_para_ser_mas_efectivo_en_tu_negocio'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/persuasion', 'titulo' => 'Persuasión para ser mas efectivo en tu negocio', 'descripcion' => 'Persuasión para ser mas efectivo en tu negocio al interactuar con clientes, inversionistas y colaboradores.', 'imagen' => 'http://static.tuproyecto.com/images/courses/persuasion.jpg'];

		// $cursos['marca_personal'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/marca-personal', 'titulo' => 'Marca personal', 'descripcion' => 'Fortalecer competencia respecto al uso de la marca para fortalecer las acciones de la empresa.', 'imagen' => 'http://static.tuproyecto.com/images/courses/marca-personal.jpg'];

		// $cursos['investigacion_de_mercados_para_emprendedores'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/investigacion-mercados-emprendedores', 'titulo' => 'Investigación de mercados para emprendedores', 'descripcion' => 'La investigación de mercados nos da la fuente principal de la información, para analizar la pertinencia y validez de los supuestos planteados en tu modelo de negocios.', 'imagen' => 'http://static.tuproyecto.com/images/courses/investigacion-mercado-emprende.jpg'];

		// $cursos['accion_comercial_y_la_venta_moderna'] = ['url' => 'http://aldeainnpulsa.com/aprende/cursos/emprendimiento/accion-comercia-venta-moderna', 'titulo' => 'Acción comercial y la venta moderna', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/e-comerce.jpg'];

		$cursos['how_to_build_a_startup'] = ['url' => 'https://classroom.udacity.com/courses/ep245/lessons/48743167/concepts/487500570923', 'titulo' => 'How to build a startup',  'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/canvas-model-1.jpg'];

		$cursos['app_marketing'] = ['url' => 'https://classroom.udacity.com/courses/ud719', 'titulo' => 'App Marketing', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/industry-1.jpg'];

		$cursos['monetize_your_android_app_with_ads'] = ['url' => 'https://classroom.udacity.com/courses/ud876-3/lessons/3987338872/concepts/58279000180923', 'titulo' => 'Monetize your android App with ads', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/e-comerce.jpg'];

		$cursos['get_your_startup_started'] = ['url' => 'https://classroom.udacity.com/courses/ud806/lessons/7585925729/concepts/77601697060923', 'titulo' => 'Get your startup started', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/comunicacion-escrita.jpg'];

		$cursos['generacion_de_idea_de_negocio'] = ['url' => 'http://www.unimooc.com/course/generacion-de-ideas-de-negocio/', 'titulo' => 'Generación de idea de negocio', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/investigacion-mercado-emprende.jpg'];

		$cursos['coaching_para_emprendedores'] = ['url' => 'http://www.cursopedia.com/Ficha-Curso-de-coaching-para-emprendedores', 'titulo' => 'Coaching para emprendedores', 'descripcion' => '', 'imagen' => 'http://static.tuproyecto.com/images/courses/plan-financiero-comerciales.jpg'];

		// Administrativo 0 - 8
		if($report->data_admin_management->total < 8)
		{
			$oportunidades[] = $cursos['derechos_de_autor_y_emprendimiento_digital'];
			$oportunidades[] = $cursos['pacto_de_socios'];
		}

		// Financiera 0 - 16

		// if($report->data_financial_management->total < 16)
		// {
		// 	$oportunidades[] = $cursos['introduccion_a_la_inversion_angel'];
		// 	$oportunidades[] = $cursos['negociacion_con_inversionistas'];
		// 	$oportunidades[] = $cursos['planeacion_financiera_para_emprendedores'];
		// }

		// Comercial 0 - 16
		// if($report->data_commercial_management->total < 16)
		// {
		// 	$oportunidades[] = $cursos['persuasion_para_ser_mas_efectivo_en_tu_negocio'];
		// 	$oportunidades[] = $cursos['marca_personal'];
		// 	$oportunidades[] = $cursos['investigacion_de_mercados_para_emprendedores'];
		// 	$oportunidades[] = $cursos['accion_comercial_y_la_venta_moderna'];
		// 	// $oportunidades[] = $cursos['app_marketing'];
		// 	// $oportunidades[] = $cursos['monetize_your_android_app_with_ads'];
		// }

		// Humano 0 - 16
		if($report->data_human_management->total < 16)
		{
			$oportunidades[] = $cursos['coaching_para_emprendedores'];
		}

		// $oportunidades[] = $cursos['get_your_startup_started'];
		$oportunidades[] = $cursos['generacion_de_idea_de_negocio'];
		$oportunidades[] = $cursos['how_to_build_a_startup'];
		return $oportunidades;
	}
	public static function getAverages($questions, $sector = null)
	{
		$query = RegistroFaseUsuario::query();

		$query->select(DB::raw('SUM(puntaje_opcion_encuesta) / 4 as promedio', 'registro_fase_usuario.codigo_usuario'));

		$query->join('core.respuesta_encuesta','registro_fase_usuario.codigo_usuario','=','respuesta_encuesta.codigo_usuario');
		$query->join('core.pregunta_encuesta','respuesta_encuesta.codigo_pregunta_encuesta','=','pregunta_encuesta.codigo_pregunta_encuesta');
     	$query->leftjoin('core.opcion_encuesta','respuesta_encuesta.codigo_opcion_encuesta','=','opcion_encuesta.codigo_opcion_encuesta');

        $query->where('pregunta_encuesta.codigo_encuesta', '=', self::$challenge);
        $query->where('pregunta_encuesta.codigo_estado', '=', Estado::getState('active'));
        $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));
        $query->where('registro_fase_usuario.codigo_estado', Estado::getState('active'));
        $query->where('registro_fase_usuario.codigo_fase', self::$stage_challenge_1);
        $query->whereIn('pregunta_encuesta.codigo_pregunta_encuesta', $questions);

        if($sector != ''){
        	$query->whereIn('registro_fase_usuario.codigo_usuario', function($query) use ($sector)
		    {
		        $query->select(DB::raw(' codigo_usuario
			 		from "core"."respuesta_encuesta"
					left join "core"."sector_interes" on "respuesta_encuesta"."texto_respuesta_encuesta"::integer = "sector_interes"."codigo_sector_interes"
					where respuesta_encuesta.codigo_pregunta_encuesta = 1234
			 		and sector_interes.codigo_sector_economico = '.$sector.' '));
		    });
        }

        $query->groupBy('registro_fase_usuario.codigo_usuario');

        $average = DB::table( DB::raw("({$query->toSql()}) as query") )
		    ->mergeBindings($query->getQuery())
		    ->avg('promedio');

	    return round($average,1);
	}
}
