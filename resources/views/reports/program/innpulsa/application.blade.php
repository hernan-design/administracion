@extends('reports.layout', ['title' => $title])
@section('content')
{{--*/ $data_admin_management = Custom\Innpulsa::getResultScoreAdminManagement($answers); /*--}}
{{--*/ $data_human_management = Custom\Innpulsa::getResultScoreHumanManagement($answers); /*--}}
{{--*/ $data_financial_management = Custom\Innpulsa::getResultScoreFinancialManagement($answers); /*--}}
{{--*/ $data_commercial_management = Custom\Innpulsa::getResultScoreCommercialManagement($answers); /*--}}
{{--*/ $data_production_management = Custom\Innpulsa::getResultScoreProductionManagement($answers); /*--}}
{{--*/ $cash_flow = Custom\Innpulsa::getCashFlow($answers); /*--}}
{{--*/ $interpretation_cash_flow = Custom\Innpulsa::getInterpretationCashFlow($answers, $cash_flow); /*--}}
{{--*/ $financial_reasons = Custom\Innpulsa::getFinancialReasons($answers, $cash_flow); /*--}}
{{--*/ $interpretation_financial_reasons = Custom\Innpulsa::getInterpretationFinancialReasons($answers, $financial_reasons); /*--}}

	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Información del empresario/emprendedor</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th width="20%">Emprendedor</th>
				<td width="30%">{{ $user->user_name }}</td>
				<th width="30%">Número de identificación</th>
				<td width="20%">{{ $answers->where('question_id', 1223)->first()->answer }}</td>
			</tr>
			<tr>
				<th width="20%">Género</th>
				<td width="30%">{{ App\Models\Seguridad\Usuario::$gender[$user->genero_persona] }}</td>
				<th width="30%">Fecha de nacimiento</th>
				<td width="20%">{{ $user->fecha_nacimiento_usuario }}</td>
			</tr>
			<tr>
				<th width="20%">Correo electrónico</th>
				<td width="30%">{{ $user->correo_electronico }}</td>
				<th width="30%">Ciudad</th>
				<td width="20%">{{ $user->nombre_ciudad }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Información de la empresa</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th width="35%">Emprendimiento</th>
				<td width="15%">{{ $user->entity_name }}</th>
				<th width="35%">NIT</td>
				<td width="15%">{{ $user->entity_id }}</td>
			</tr>
			<tr>
				<th width="35%">Sector</th>
				<td width="15%">{{ App\Models\Core\SectorInteres::getSectorByActivity($answers->where('question_id', 1234)->first()->answer)->nombre_sector_economico }}</td>
				<th width="35%">Empleados contratados por nómina</th>
				<td width="15%">{{ $answers->where('question_id', 1244)->first()->answer }}</td>
			</tr>
			<tr>
				<th width="35%">Ventas del año en curso</th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1236)->first()->answer,2,'.',',') }}</td>
				<th width="35%">Ventas al exterior en USD del año en curso</th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1240)->first()->answer) }}</td>
			</tr>
			<tr>
				<th width="35%">Ventas en el año 2015 (COP)</th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1237)->first()->answer,2,'.',',') }}</td>
				<th width="35%">Ventas al exterior en USD en el año 2015</th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1241)->first()->answer) }}</td>
			</tr>
			<tr>
				<th width="35%">Ventas en el año 2014 (COP)</th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1238)->first()->answer,2,'.',',') }}</td>
				<th width="35%">Ventas al exterior en USD en el año 2014 </th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1242)->first()->answer) }}</td>
			</tr>
			<tr>
				<th width="35%">Ventas en el año 2013 (COP)</th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1239)->first()->answer,2,'.',',') }}</td>
				<th width="35%">Ventas al exterior en USD en el año 2013 </th>
				<td width="15%" class="currency">{{ number_format($answers->where('question_id', 1243)->first()->answer) }}</td>
			</tr>
			<tr>
				<th colspan="4">Describe en una frase a qué se dedica tu empresa</th>
			</tr>
			<tr>
				<td colspan="4">{{ $user->entity_description }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Información del nuevo producto (bien o servicio) o modelo de negocio</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="4">Describe en una frase el producto (bien o servicio) o modelo de negocio innovador</th>
			</tr>
			<tr>
				<td colspan="4">{{ $answers->where('question_id', 1255)->first()->answer }}</td>
			</tr>
			<tr>
				<th width="50%" colspan="2">Ventas totales del año en curso</th>
				<td width="50%" colspan="2">{{ number_format($answers->where('question_id', 1260)->first()->answer,2,'.',',') }}</td>
			</tr>
			<tr>
				<th width="50%" colspan="2">Ventas totales en el año 2015 (COP)</th>
				<td width="50%" colspan="2">{{ number_format($answers->where('question_id', 1259)->first()->answer,2,'.',',') }}</td>
			</tr>
			<tr>
				<th width="50%" colspan="2">Ventas totales en el año 2014 (COP)</th>
				<td width="50%" colspan="2">{{ number_format($answers->where('question_id', 1258)->first()->answer,2,'.',',') }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Gestión administrativa</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4">{!! $data_admin_management->texto_respuesta !!}<td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Gestión humana y cultura de la innovación</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4">{!! $data_human_management->texto_respuesta !!}<td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Gestión financiera</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4">{!! $data_financial_management->texto_respuesta !!}<td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Gestión comercial</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4" >{!! $data_commercial_management->texto_respuesta !!}<td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Gestión de la producción</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4">{!! $data_production_management->texto_respuesta !!}<td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Resultado por área de la empresa</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">Gestión administrativa</td>
				<td colspan="2">{{ $data_admin_management->score }}</td>
			</tr>
			<tr>
				<td colspan="2">Gestión humana y cultura de la innovación</td>
				<td colspan="2">{{ $data_human_management->score }}</td>
			</tr>
			<tr>
				<td colspan="2">Gestión financiera</td>
				<td colspan="2">{{ $data_financial_management->score }}</td>
			</tr>
			<tr>
				<td colspan="2">Gestión comercial</td>
				<td colspan="2">{{ $data_commercial_management->score }}</td>
			</tr>
			<tr>
				<td colspan="2">Gestión de la producción</td>
				<td colspan="2">{{ $data_production_management->score }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="1" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Flujo de caja</th>
			</tr>
			<tr>
				<th colspan="4">
					<h5>¿Qué es el flujo de caja? Son los flujos de entrada y de salida de efectivo en un periodo dado. En un indicador muy importante en la determinación de la liquidez de la empresa. Los flujos de caja sirven para determinar problemas de liquidez e la empresa y viabilidad de proyectos de inversión.</h5>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2"></td>
				<td>Diciembre 2014</td>
				<td>Diciembre 2015</td>
			</tr>
			<tr>
				<td colspan="4">FLUJO DE EFECTIVO GENERADO POR LA OPERACIÓN</td>
			</tr>
			<tr>
				<td colspan="2">EBITDA</td>
				<td class="right">{{ number_format($answers->where('question_id', 1393)->where('option_code_x', 1857)->where('option_code_y', 1934)->first()->answer,2,'.',',') }}</td>
				<td class="right">{{ number_format($answers->where('question_id', 1393)->where('option_code_x', 1858)->where('option_code_y', 1934)->first()->answer,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(Aumento) / Disminución Cartera</td>
				<td class="right">{{ number_format($cash_flow->disminucion_cartera2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminucion_cartera2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(Aumento)/ Disminución Inventarios</td>
				<td class="right">{{ number_format($cash_flow->disminucion_inventarios2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminucion_inventarios2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(Aumento)/ Disminución Otros Activos Corrientes</td>
				<td class="right">{{ number_format($cash_flow->disminucion_otros_activos_corrientes2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminucion_otros_activos_corrientes2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Aumento / (Disminución) Proveedores </td>
				<td class="right">{{ number_format($cash_flow->disminucion_proveedores2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminucion_proveedores2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Aumento / (Disminución) Otros Pasivos Corrientes</td>
				<td class="right">{{ number_format($cash_flow->disminucion_otros_pasivos_corrientes2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminucion_otros_pasivos_corrientes2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= INVERSION EN CAPITAL DE TRABAJO</td>
				<td class="right">{{ number_format($cash_flow->inversion_capital_trabajo2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->inversion_capital_trabajo2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(-) Impuestos</td>
				<td class="right">{{ number_format($cash_flow->impuestos2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->impuestos2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= EFECTIVO NETO OPERACIONAL (1)</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_operacional2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_operacional2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="4">FLUJO DE EFECTIVO EN ACTIVIDADES DE INVERSION</td>
			</tr>
			<tr>
				<td colspan="2">(Mantenimiento y Expansión) / Venta de Activos Fijos</td>
				<td class="right">{{ number_format($cash_flow->ventas_activos_fijos_2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->ventas_activos_fijos_2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(Aumento) / Disminución en Inversiones Permanentes</td>
				<td class="right">{{ number_format($cash_flow->disminuciones_inversiones_permanentes2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminuciones_inversiones_permanentes2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(Aumento) / Disminución en Activos Diferidos e Intangibles</td>
				<td class="right">{{ number_format($cash_flow->disminuciones_activos_diferidos2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->disminuciones_activos_diferidos2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= EFECTIVO NETO EN ACTIVIDADES DE INVERSION (2)</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_actividdes_inversion2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_actividdes_inversion2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= FLUJO DE CAJA LIBRE (1)+(2)=(3)</td>
				<td class="right">{{ number_format($cash_flow->flujo_caja_libre2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->flujo_caja_libre2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="4">FLUJO DE EFECTIVO EN ACTIVIDADES DE FINANCIACION</td>
			</tr>
			<tr>
				<td colspan="2">(-/+) Gastos Financieros</td>
				<td class="right">{{ number_format($cash_flow->gastos_financieros2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->gastos_financieros2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(+/-) Amortizacion Bancos y Bonos CP</td>
				<td class="right">{{ number_format($cash_flow->amortizacion_bancos_bonos_cp2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->amortizacion_bancos_bonos_cp2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(+/-) Amortizacion Bancos y Bonos LP</td>
				<td class="right">{{ number_format($cash_flow->amortizacion_bancos_bonos_lp2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->amortizacion_bancos_bonos_lp2015,2,'.',',')}}</td>
			</tr>
			<tr>
				<td colspan="2">= EFECTIVO NETO EN ACTIVIDADES DE FINANCIACION (4)</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_actividades_financiacion2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_actividades_financiacion2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= FLUJO DE CAJA LIBRE DESPUES DE FINANCIACION (3)+(4)=(5)</td>
				<td class="right">{{ number_format($cash_flow->flujo_caja_libre_despues_financiacion2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->flujo_caja_libre_despues_financiacion2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="4">OTROS FLUJOS DE EFECTIVO</td>
			</tr>
			<tr>
				<td colspan="2">Ingresos Financieros</td>
				<td class="right">{{ number_format($cash_flow->ingresos_financieros2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->ingresos_financieros2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Otros (Neto no Operacional)</td>
				<td class="right">{{ number_format($cash_flow->otros_neto_no_operacional2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->otros_neto_no_operacional2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(Aumento)/ Disminución Otros Activos</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_otros_activos2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_otros_activos2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Aumento / (Disminución) Otros Pasivos</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_otros_pasivos2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_otros_pasivos2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Capital Social</td>
				<td class="right">{{ number_format($cash_flow->capital_social2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->capital_social2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Aumento / (Disminución) Patrimonio de los Accionistas</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_patrimonio_accionistas2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_patrimonio_accionistas2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= EFECTIVO NETO EN OTROS (6)</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_otros2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->efectivo_neto_otros2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">= FLUJO DE CAJA NETO (5)+(6)=(7)</td>
				<td class="right">{{ number_format($cash_flow->flujo_caja_neto_2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->flujo_caja_neto_2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">SALDO INICIAL DISPONIBLE E INVERSIONES TEMPORALES</td>
				<td class="right">{{ number_format($cash_flow->saldo_inicial_disponible_inv_termporales2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->saldo_inicial_disponible_inv_termporales2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">SALDO FINAL DISPONIBLE E INVERSIONES TEMPORALES</td>
				<td class="right">{{ number_format($cash_flow->saldo_final_disponible_inv_termporales2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->saldo_final_disponible_inv_termporales2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">(AUMENTO) / DISMINUCION EN DISPONIBLE (8)</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_disponibles_2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($cash_flow->aumento_disminucion_disponibles_2015,2,'.',',') }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="1" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">INTERPRETACIÓN FLUJO DE EFECTIVO GENERADO POR LA OPERACIÓN</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>DEFINICIÓN</td>
				<td colspan="2">INTERPRETACIÓN</td>
				<td>PLAN</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_ebitda }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_ebitda }}</td>
				<td>{{ $interpretation_cash_flow->plan_ebitda }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_efectivo_neto_operacional }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_efectivo_neto_operacional }}</td>
				<td>{{ $interpretation_cash_flow->plan_efectivo_neto_operacional }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_efectivo_actividades_inversion }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_efectivo_actividades_inversion }}</td>
				<td>{{ $interpretation_cash_flow->plan_ebitda }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_ebitda }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_ebitda }}</td>
				<td>{{ $interpretation_cash_flow->plan_efectivo_actividades_inversion }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_flujo_caja_libre }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_flujo_caja_libre }}</td>
				<td>{{ $interpretation_cash_flow->plan_flujo_caja_libre }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_efectivo_actividades_financiacion }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_efectivo_actividades_financiacion }}</td>
				<td>{{ $interpretation_cash_flow->plan_efectivo_actividades_financiacion }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_caja_libre_despues_financiacion }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_caja_libre_despues_financiacion }}</td>
				<td>{{ $interpretation_cash_flow->plan_caja_libre_despues_financiacion }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_efectivo_neto_otros }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_efectivo_neto_otros }}</td>
				<td>{{ $interpretation_cash_flow->plan_efectivo_neto_otros }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_cash_flow->definition_flujo_caja_neto }}</td>
				<td colspan="2">{{ $interpretation_cash_flow->interpretation_flujo_caja_neto }}</td>
				<td>{{ $interpretation_cash_flow->plan_flujo_caja_neto }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="1" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">RAZONES FINANCIERAS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2"></td>
				<td>Diciembre 2014</td>
				<td>Diciembre 2015</td>
			</tr>
			<tr>
				<td colspan="4">EVOLUCIÓN OPERACIONAL Y RENTABILIDAD</td>
			</tr>
			<tr>
				<td colspan="2">Evolución de Ventas</td>
				<td class="right">{{ number_format($financial_reasons->evolucion_ventas2014,2,'.',',')."%" }}</td>
				<td class="right">{{ number_format($financial_reasons->evolucion_ventas2015,2,'.',',')."%" }}</td>
			</tr>
			<tr>
				<td colspan="2">Evolución EBITDA</td>
				<td class="right">{{ number_format($financial_reasons->evolucion_ebitda2014,2,'.',',')."%" }}</td>
				<td class="right">{{ number_format($financial_reasons->evolucion_ebitda2015,2,'.',',')."%" }}</td>
			</tr>
			<tr>
				<td colspan="2">CAPEX / Ventas</td>
				<td class="right">{{ number_format($financial_reasons->capex_ventas2014,2,'.',',')."%" }}</td>
				<td class="right">{{ number_format($financial_reasons->capex_ventas2015,2,'.',',')."%" }}</td>
			</tr>
			<tr>
				<td colspan="2">ROA</td>
				<td class="right">{{ number_format($financial_reasons->roa2014,2,'.',',')."%" }}</td>
				<td class="right">{{ number_format($financial_reasons->roa2015,2,'.',',')."%" }}</td>
			</tr>
			<tr>
				<td colspan="2">ROE</td>
				<td class="right">{{ number_format($financial_reasons->roe2014,2,'.',',')."%" }}</td>
				<td class="right">{{ number_format($financial_reasons->roe2015,2,'.',',')."%" }}</td>
			</tr>
			<tr>
				<td colspan="4">ENDEUDAMIENTO</td>
			</tr>
			<tr>
				<td colspan="2">Grado de Endeudamiento (Total Pasivo / Total Activo)</td>
				<td class="right">{{ number_format($financial_reasons->grado_endeudamiento2014,2,'.',',')."%" }}</td>
				<td class="right">{{ number_format($financial_reasons->grado_endeudamiento2015,2,'.',',')."%" }}</td>
			</tr>
			<tr>
				<td colspan="2">Obligaciones Financieras / Ventas</td>
				<td class="right">{{ number_format($financial_reasons->obligaciones_financieras_ventas2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($financial_reasons->obligaciones_financieras_ventas2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Obligaciones Financieras / EBITDA</td>
				<td class="right">{{ number_format($financial_reasons->obligaciones_financieras_ebitda2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($financial_reasons->obligaciones_financieras_ebitda2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="4">Cobertura Deuda</td>
			</tr>
			<tr>
				<td colspan="2">Ebitda / Gastos Financieros</td>
				<td class="right">{{ number_format($financial_reasons->ebitda_gastos_financieros2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($financial_reasons->ebitda_gastos_financieros2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Ebitda / (Total Deuda CP + Gastos Financieros)</td>
				<td class="right">{{ number_format($financial_reasons->ebitda_gastos_tdcp_gf2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($financial_reasons->ebitda_gastos_tdcp_gf2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="2">Flujo Caja Libre / Gastos Financieros</td>
				<td class="right">{{ number_format($financial_reasons->flujo_caja_libre2014,2,'.',',') }}</td>
				<td class="right">{{ number_format($financial_reasons->flujo_caja_libre2015,2,'.',',') }}</td>
			</tr>
			<tr>
				<td colspan="4">ACTIVIDAD</td>
			</tr>
			<tr>
				<td colspan="2">Días Plazo Cobro (Cartera)</td>
				<td class="right">{{ number_format($financial_reasons->dias_plazo_cobro2014,2,'.',',')." días" }}</td>
				<td class="right">{{ number_format($financial_reasons->dias_plazo_cobro2015,2,'.',',')." días" }}</td>
			</tr>
			<tr>
				<td colspan="2">Días de Inventarios</td>
				<td class="right">{{ number_format($financial_reasons->dias_inventario2014,2,'.',',')." días" }}</td>
				<td class="right">{{ number_format($financial_reasons->dias_inventario2015,2,'.',',')." días" }}</td>
			</tr>
			<tr>
				<td colspan="2">Días Plazo Pago (Proveedores)</td>
				<td class="right">{{ number_format($financial_reasons->dias_plazo_pago2014,2,'.',',')." días" }}</td>
				<td class="right">{{ number_format($financial_reasons->dias_plazo_pago2015,2,'.',',')." días" }}</td>
			</tr>
			<tr>
				<td colspan="2">Ciclo Operacional</td>
				<td class="right">{{ number_format($financial_reasons->ciclo_operacional2014,2,'.',',')." días" }}</td>
				<td class="right">{{ number_format($financial_reasons->ciclo_operacional2015,2,'.',',')." días" }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="1" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">INTERPRETACIÓN RAZONES FINANCIERAS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>CONCEPTO</td>
				<td colspan="2">INTERPRETACIÓN</td>
				<td>PLAN</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_interpretacion_evluacion_ventas }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_evluacion_ventas }}</td>
				<td>{{ $interpretation_financial_reasons->plan_interpretacion_evluacion_ventas }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_evolucion_ebitda }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_evolucion_ebitda }}</td>
				<td>{{ $interpretation_financial_reasons->plan_interpretacion_evolucion_ebitda }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_roa }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_roa }}</td>
				<td>{{ $interpretation_financial_reasons->plan_roa }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_roe }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_roe }}</td>
				<td>{{ $interpretation_financial_reasons->plan_roe }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_grado_endeudamiento }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_grado_endeudamiento }}</td>
				<td>{{ $interpretation_financial_reasons->plan_grado_endeudamiento }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_obligaciones_financieras_ventas }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_obligaciones_financieras_ventas }}</td>
				<td>{{ $interpretation_financial_reasons->plan_obligaciones_financieras_ventas }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_obligaciones_financieras_ebitda }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_obligaciones_financieras_ebitda }}</td>
				<td>{{ $interpretation_financial_reasons->plan_obligaciones_financieras_ebitda }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_ebitda_gf }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_ebitda_gf }}</td>
				<td>{{ $interpretation_financial_reasons->plan_ebitda_gf }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_ebitda_gf_dt }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_ebitda_gf_dt }}</td>
				<td>{{ $interpretation_financial_reasons->plan_ebitda_gf_dt }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_flujo_caja_libre }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_flujo_caja_libre }}</td>
				<td>{{ $interpretation_financial_reasons->plan_flujo_caja_libre }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_dias_plazo_cobro }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_dias_plazo_cobro }}</td>
				<td>{{ $interpretation_financial_reasons->plan_dias_plazo_cobro }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_dias_inventario }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_dias_inventario }}</td>
				<td>{{ $interpretation_financial_reasons->plan_dias_inventario }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_dias_plazo_pago }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_dias_plazo_pago }}</td>
				<td>{{ $interpretation_financial_reasons->plan_dias_plazo_pago }}</td>
			</tr>
			<tr>
				<td>{{ $interpretation_financial_reasons->concepto_ciclo_operacional }}</td>
				<td colspan="2">{{ $interpretation_financial_reasons->interpretacion_ciclo_operacional }}</td>
				<td>{{ $interpretation_financial_reasons->plan_ciclo_operacional }}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="global" border="1" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="4" class="subtitle">Preguntas y respuestas</th>
			</tr>
		</thead>
		<tbody>
 	@foreach($answers as $response)
 	{{--*/	$question_id = $response->question_id; /*--}}
 	{{--*/	$question_type = $response->question_type; /*--}}
 	{{--*/	$question_class = $response->question_class; /*--}}
 	{{--*/	$field_type = $response->field_type; /*--}}
 	{{--*/	$option_code_x = $response->option_code_x; /*--}}
 	{{--*/	$text_option_x = $response->text_option_x; /*--}}
 	{{--*/	$option_code_y = $response->option_code_y; /*--}}
 	{{--*/	$text_option_y = $response->text_option_y; /*--}}
 	{{--*/	$question = $response->question; /*--}}
 	{{--*/	$answer = $response->answer; /*--}}

 	@if(is_numeric($answer))
 		{{--*/	$answer = number_format($answer); /*--}}
 	@endif

 	@if($question_type == App\Models\Core\PreguntaEncuesta::getType('matrix'))<!-- Matriz -->
 		{{--*/	$question = $question.": ".$text_option_y." / ".$text_option_x; /*--}}
 		{{--*/	$question_id = $question_id.", ".$option_code_x.", ".$option_code_y; /*--}}
 	@elseif($question_type == App\Models\Core\PreguntaEncuesta::getType('single'))<!-- Seleccion siempre -->
 		{{--*/	$answer = $text_option_y; /*--}}
 	@endif
	 		<tr>
	 			{{-- <td colspan="2">{{$question_id}} {{$question}}</td> --}}
	 			<td colspan="2">{!!$question!!}</td>
	 			<td colspan="2">{{$answer}}</td>
	 		</tr>
 	@endforeach
 		</tbody>
 	</table>
@stop