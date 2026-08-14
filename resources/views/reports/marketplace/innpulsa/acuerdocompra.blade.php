@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
	    <tbody>
	        <tr>
	            <th class="center titleespecial">ANEXO AL CONTRATO DE ASESORÍA No. {{ $data->numero_contrato }} DE {{ Carbon\Carbon::now()->year }} SUSCRITO ENTRE {{ strtoupper($data->nombre_proveedor) }} Y FIDUCIARIA COLOMBIANA DE COMERCIO EXTERIOR S.A., FIDUCOLDEX, QUIEN ACTÚA COMO VOCERA DE LA UNIDAD DE GESTIÓN DE CRECIMIENTO EMPRESARIAL-INNPULSA COLOMBIA</th>
	        </tr>
	    </tbody>
	</table>
	<br><br>
	<p class="justify">Los suscritos,
		@if( $data->codigo_clase_emprendimiento ==  App\Models\Core\ClaseEmprendimiento::getClass('enterprise'))
			{{ strtoupper($data->nombre_representante_legal_comprador) }} {{ strtoupper($data->apellido_representante_legal_comprador) }}, identificado con {{ $data->tipo_identificacion_comprador }} número {{ $data->identificador_representante_legal_comprador }} expedida en {{ $data->ciudad_id_representante_legal_comprador }} actuando en calidad de Representante Legal de {{ strtoupper($data->comprador) }}, con NIT. {{ $data->nit_comprador }},
		@else
			{{ strtoupper($data->nombre_representante_legal_comprador) }} {{ strtoupper($data->apellido_representante_legal_comprador) }}, identificado con {{ $data->tipo_identificacion_comprador }} número {{ $data->identificador_representante_legal_comprador }} expedida en {{ strtoupper($data->ciudad_id_representante_legal_comprador) }},
		@endif
		quien en adelante se denominará <strong>LA EMPRESA</strong> y {{ strtoupper(trim($data->nombre_representante_legal_proveedor)) }}, identificado con
		@if($data->internacional_proveedor)
			{{ $data->tipo_identificacion_vendedor }} número {{ trim($data->identificacion_representate_legal_proveedor) }}, en representación de {{ strtoupper($data->nombre_proveedor) }}
		@else
			{{ $data->tipo_identificacion_vendedor }} número {{ $data->identificacion_representate_legal_proveedor }} expedida en {{ $data->seller_city }},
			@if($data->tipo_usuario_proveedor == App\Models\Seguridad\Usuario::typeUser('entity'))
				actuando en calidad de Representante Legal de {{ strtoupper($data->nombre_proveedor) }} con NIT {{ $data->nit_proveedor }},
			@endif
		@endif
		quien en adelante se denominará <strong>EL ASESOR</strong>, hemos acordado celebrar el presente Acuerdo de Negociación el cual se rige por las siguientes cláusulas:</p>
	<ol>
		<li class="justify"><strong>OBJETIVO DEL ACUERDO DE NEGOCIACIÓN:</strong> identificar y consignar las características y particularidades del servicio a ofrecer por parte del <strong>ASESOR</strong> al <strong>EMPRESARIO</strong> beneficiario de la llave de acceso otorgada por iNNpulsa Colombia, una vez surtido el proceso de negociación correspondiente, así como las obligaciones de las partes durante la prestación del servicio. <br><br> </li>
		<li class="justify"><strong>NOMBRE DEL SERVICIO HABILITADO POR INNPULSA COLOMBIA:</strong> {{ trim($data->nombre_categoria_producto) }} - {{ trim($data->nombre_producto) }}. <br><br> </li>
		<li class="justify"><strong>DESCRIPCIÓN Y ALCANCE DEL SERVICIO A OFRECER:</strong> Conforme al proceso de negociación surtido a continuación se consignan las particularidades del servicio correspondiente:
			<br><br>
			<table class="rtable" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="text-center" colspan="4">ALCANCE DEL SERVICIO</th>
						<th colspan="4">{{ $data->alcance_acuerdo_compra }}</th>
					</tr>
					<tr>
						<th colspan="8">Actividades a cargo del Asesor/Proveedor de Servicios Especializados</th>
					</tr>
					<tr>
						<th class="left"><h4> Actividades a desarrollar</h4></th>
						<th class="center">Responsable</th>
						<th class="center">No. de sesiones de trabajo presenciales </th>
						<th class="center">No. horas por sesión presencial </th>
						<th class="center">No. de sesiones de trabajo virtuales </th>
						<th class="center">No. horas por sesión virtual </th>
						<th class="center">Fecha estimada de inicio</th>
						<th class="center">Fecha estimada de terminación</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data->actividad_acuerdo as $activities)
					<tr>
						<td class="left">{{ $activities->desc_actividad_acuerdo }}</td>
						<td class="center">{{ $activities->resp_comprador_actividad == true ? 'Empresario' :  'Proveedor'}}</td>
						<td class="center">{{ $activities->sesiones_presencial}}</td>
						<td class="center">{{ $activities->sesiones_presencial_horas}}</td>
						<td class="center">{{ $activities->sesiones_virtual}}</td>
						<td class="center">{{ $activities->sesiones_virtual_horas}}</td>
						<td class="right">{{ $activities->fecha_inicial_actividad_acuerdo}}</td>
						<td class="right">{{ $activities->fecha_final_actividad_acuerdo}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<table class="rtable" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="text-center"> Entregables acordados</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data->entregable_acuerdo as $deliverable)
					<tr>
						<td class="left">{{ $deliverable->descripcion_entregable }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<table class="rtable" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="text-center"> Resultados a lograr</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="left">{{ $data->resultados_acuerdo_compra}}</td>
					</tr>
				</tbody>
			</table>
		<br><br>
		</li>
		<li class="justify"><strong>PLAZO: </strong>La prestación del servicio tendrá un plazo de hasta {{ $data->plazo }} meses, contados a partir de la fecha de firma del presente documento. Plazo que en ningún caso podrá superar el término del contrato suscrito entre INNPULSA COLOMBIA y <strong>EL ASESOR</strong>. En caso de que el plazo del acuerdo supere la duración del contrato suscrito entre INNPULSA COLOMBIA y <strong>EL ASESOR</strong>, <strong>EL ASESOR</strong> deberá informar y solicitar la ampliación del contrato a INNPULSA COLOMBIA, quien deberá evaluar y responder formalmente dicha solicitud.
		<br><br>
		Este plazo podrá ser prorrogado por las partes con anterioridad al vencimiento del servicio, mediante comunicación formal firmada por <strong>EL ASESOR</strong> y <strong>EL EMPRESARIO</strong>, dirigida a INNPULSA COLOMBIA, quien deberá  dar la aprobación correspondiente.
		<br><br>
		</li>
		<li class="justify"><strong>EQUIPO DE TRABAJO:</strong> Para la prestación del servicio las partes disponen del siguiente equipo de trabajo:
			<br><br>
			<table class="rtable" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th colspan="3" class="text-center">Equipo de trabajo del asesor</th>
					</tr>
					<tr>
						<th Class="left">Nombre</th>
						<th class="center">Cargo</th>
						<th class="center">Datos de Contacto</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data->asesor_acuerdo as $user_agreement)
						<tr>
							<td lass="left">{{ $user_agreement->nombre_usuario_acuerdo }}</td>
							<td class="center">{{ $user_agreement->cargo_usuario_acuerdo }}</td>
							<td class="center">{{ $user_agreement->correo_usuario_acuerdo }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<br><br>
			<table class="rtable" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th colspan="3" class="text-center">Equipo de trabajo del empresario</th>
					</tr>
					<tr>
						<th Class="left">Nombre</th>
						<th class="center">Cargo</th>
						<th class="center">Datos de Contacto</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($data->empresario_acuerdo as $businessman_agreement)
						<tr>
							<td lass="left">{{ $businessman_agreement->nombre_usuario_empresario }}</td>
							<td class="center">{{ $businessman_agreement->cargo_usuario_empresario }}</td>
							<td class="center">{{ $businessman_agreement->correo_usuario_empresario }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<br><br>
			<p>El equipo de trabajo por parte del asesor debe ser el habilitado previamente por INNPULSA COLOMBIA. En caso de ser un asesor diferente al habilitado, el asesor debe enviar una comunicación formal a INNPULSA COLOMBIA, solicitando la inclusión o cambio en el equipo de trabajo, anexando con ello la hoja de vida y soportes correspondientes. INNPULSA COLOMBIA debe hacer la evaluación a partir del perfil y experiencia del asesor en la prestación del servicio y emitir una comunicación formal autorizando o rechazando la solicitud correspondiente.</p>
		</li>
		<li class="justify"><strong>TARIFA NEGOCIADA DEL SERVICIO A OFRECER:</strong>
			<br><br>
			<table class="rtable" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th Class="center">Servicio</th>
						<th class="center">Tarifa</th>
						<th class="center">Moneda</th>
						<th class="center">Valor asumido por LA UNIDAD (el cual no podrá superar el valor de la Llave de Acceso)</th>
						<th class="center">Valor asumido por <strong>EL EMPRESARIO</strong> (en el evento que la tarifa del servicio supere el valor de la Llave de Acceso)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="left">{{ $data->nombre_producto }}</td>
						<td class="right">{{ $data->valor_total_compra }}</td>
						<td class="center">COP</td>
						<td class="right">$ {{ $data->valor_total_compra }}</td>
						<td class="right">$ 0</td>
					</tr>
					<tr>
						<td class="left"><strong>Total</strong></td>
						<td class="right">&nbsp;</td>
						<td class="center">&nbsp;</td>
						<td class="right">$ {{ $data->valor_total_compra }}</td>
						<td class="right">$ 0</td>
					</tr>
				</tbody>
			</table>
			<p class="justify">PARÁGRAFO 1: Los valores acordados por los servicios establecidos en el presente acuerdo incluyen los impuestos y retenciones correspondientes según la ley.</p>
			<p class="justify">PARÁGRAFO 2: Los valores acordados por los servicios establecidos en el presente anexo no podrán superar en ningún caso las tarifas establecidas por servicio en el contrato suscrito entre el asesor e INNPULSA COLOMBIA.</p>
		</li>
		<li class="justify"><strong>FORMA DE PAGO DEL SERVICIO A OFRECER: </strong> El servicio prestado por <strong>EL ASESOR</strong> será pagado por INNPULSA COLOMBIA así:
			<ul>
				<li class="justify"> Un  único pago a cargo de INNPULSA COLOMBIA contra la aprobación del presente Acuerdo por parte del Supervisor designado para el efecto por INNPULSA COLOMBIA, entrega y recibo a satisfacción del servicio correspondiente por parte del <strong>EMPRESARIO</strong> y una vez presentados los siguientes documentos:
					<ul>
						<li class="justify">Informe que evidencie la prestación del servicio conforme a lo consignado en el presente documento. El informe debe incluir como mínimo la ejecución de las actividades correspondientes al servicio prestado y resultados alcanzados, información sobre el desempeño, disposición y compromiso de la empresa que recibe el servicio y las lecciones aprendidas, conclusiones y recomendaciones con sus respectivos soportes (listados de asistencia, evidencias fotográficas, entre otros).
							<br><br>
							<ol>
								@foreach($data->entregable_acuerdo as $deliverable)
									<li>{{ $deliverable->descripcion_entregable }}</li>
								@endforeach
							</ol>
							<br><br>
						</li>
						<li class="justify">Documento que presente la evaluación del servicio realizada por parte del <strong>EMPRESARIO</strong>, conforme al formato establecido previamente por INNPULSA COLOMBIA en el cual se manifieste expresamente por <strong>EL EMPRESARIO</strong> el recibo a satisfacción de los servicios.</li>
						<li class="justify">Factura emitida en legal forma conforme a la política de pagos establecida por INNPULSA COLOMBIA previamente informada al ASESOR, en la cual se discrimine por <strong>EMPRESARIO</strong> atendido los servicios prestados y el precio de los mismos. El pago de las facturas presentadas deberá ser aprobado de manera previa por el supervisor del contrato.</li>
					</ul>
					<p class="justify">PARÁGRAFO: En caso de que el valor del servicio negociado entre <strong>EL EMPRESARIO</strong> y <strong>EL ASESOR</strong> sea cubierto en su totalidad con el valor de la Llave de Acceso otorgada por INNPULSA COLOMBIA al <strong>EMPRESARIO</strong>, el cual incluye los impouestos y retenciones correspondientes, el pago al <strong>ASESOR</strong> se realizará en único pago a cargo de INNPULSA COLOMBIA contra entrega y recibo a satisfacción de los documentos anteriormente nombrados.</p>
					<p class="justify">El desembolso de los recursos por parte de INNPULSA COLOMBIA se hará contra la prestación de la totalidad de los servicios al <strong>EMPRESARIO</strong>, por lo tanto, no habrá pagos parciales.</p>
					<p class="justify">Si <strong>EL EMPRESARIO</strong> no acepta satisfactoriamente la totalidad de los servicios prestados por <strong>EL ASESOR</strong>, INNPULSA COLOMBIA no hará ningún pago a favor del <strong>ASESOR</strong>.</p>
				</li>
			</ul>
		</li>
		<li class="justify"><strong>LUGAR DE PRESTACIÓN DEL SERVICIO:</strong> La prestación del servicio se realizará
			@if( $data->modalidad_prestacion_servicio == App\Models\Core\Producto::getModality('presencial') )
				presencial en el domicilio que las partes acuerden.
			@endif
			@if( $data->modalidad_prestacion_servicio == App\Models\Core\Producto::getModality('blended') )
				presencial en el domicilio que las partes acuerden, y de forma virtual por el medio que las partes acuerden.
			@endif
			@if( $data->modalidad_prestacion_servicio == App\Models\Core\Producto::getModality('virtual') )
				de forma virtual por el medio que las partes acuerden.
			@endif
			<br><br>
		</li>
		<li class="justify"><strong>ASPECTOS A CUMPLIR POR PARTE DE LOS ASESORES: </strong>
			<ol>
				<li class="justify"><strong>OBLIGACIONES ESPECÍFICAS DE LOS ASESORES:</strong>
					<ol class="upper-roman-list">
						<li class="justify">Prestar a <strong>LA EMPRESA</strong> los servicios aprobados por INNPULSA COLOMBIA conforme al alcance, actividades, número de sesiones de trabajo, entregables, resultados y condiciones pactadas y consignadas en el presente documento el cual forma parte integral del contrato suscrito con INNPULSA.</li>
						<li class="justify">Transferir conocimientos y herramientas a <strong>LA EMPRESA</strong> conforme a los servicios seleccionados por <strong>LA EMPRESA</strong>.</li>
						<li class="justify">Llevar un control de asistencia de las sesiones de trabajo realizadas con <strong>LA EMPRESA</strong> de acuerdo con las condiciones pactadas y consignadas en el presente documento.</li>
						<li class="justify">En ningún caso <strong>EL ASESOR</strong> podrá prestar servicios a una EMPRESA diferente a la que suscriba el acuerdo de negociación, de presentarse esta situación no se pagará el servicio.</li>
						<li class="justify">Documentar adecuadamente cada una de las actividades realizadas con <strong>LA EMPRESA</strong> que servirán como insumo para la construcción y presentación de los entregables correspondientes, adicionalmente los entregables deben contener como mínimo lo siguiente:</li>
						<li class="justify">Entregar la información que INNPULSA COLOMBIA requiera sobre el estado de la prestación del servicio en cualquier momento.</li>
						<li class="justify">Informar a INNPULSA COLOMBIA acerca del desempeño, disposición y compromiso del <strong>EMPRESARIO</strong> que recibe el servicio.</li>
						<li class="justify">Poner a disposición de <strong>LA EMPRESA</strong> el equipo de trabajo ofrecido en su propuesta garantizando que sea éste el que efectivamente preste el respectivo servicio. <br><br>Si durante la prestación del servicio se requieren cambios en el equipo de trabajo designado por <strong>EL ASESOR</strong>, este deberá remitir para evaluación y aprobación por parte de INNPULSA COLOMBIA la hoja de vida del profesional propuesto con los respectivos soportes de ésta. <br><br> En ningún caso <strong>EL ASESOR</strong> podrá prestar servicios si el equipo de trabajo no está aprobado por INNPULSA COLOMBIA</li>
					</ol>
				<br><br>
				</li>
				<li class="justify"><strong>ENTREGABLES TÉCNICOS: </strong>
					<ul>
						<li class="justify">Descripción y desarrollo de Metodología empleada.</li>
						<li class="justify">Descripción del estado inicial y final del empresario respecto de la implementación del servicio.</li>
						<li class="justify">Resultados logrados con la implementación del servicio (Indicadores, herramientas, certificaciones, planes, estrategias implementadas, ventas, empleos, fotografías (sí aplica), entre otros.)</li>
					</ul>
				<br><br>
				</li>
				<li class="justify"><strong>INFORME FINAL: </strong>
						<ul>
							<li class="justify">Documento que evidencie la prestación del servicio, conforme a lo consignado en el acuerdo de negociación entre<strong> LA EMPRESA</strong> y <strong>EL ASESOR</strong>.<br><br>Este informe final debe incluir como mínimo: la ejecución de las actividades correspondientes al servicio prestado, las actividades desarrolladas, los resultados alcanzados gracias a la prestación del servicio y las recomendaciones al empresario, así como la descripción de los entregables y la utilidad de estos para el empresario, falencias y oportunidades empresariales que permitan el crecimiento de la empresa atendida y las lecciones aprendidas, conclusiones y recomendaciones, entre otra información que INNPULSA COLOMBIA considere conforme a la dinámica del programa con sus respectivos soportes (listados de asistencia, evidencias fotográficas, entre otros).</li>
						</ul>
				<br><br>		
				</li>
				<li class="justify"><strong>ENTREGABLES AL FINALIZAR EL SERVICIO: </strong>
						Al finalizar la prestación del respectivo servicio, <strong>EL ASESOR</strong> entregará a INNPULSA COLOMBIA, en el medio que se solicite los documentos que se relacionan a continuación:
						<br><br>
						<ul>
							<li class="justify">Entregables definidos en cada acuerdo de negociación: se refiere a los documentos que recibe <strong>LA EMPRESA</strong> como resultado de la prestación del servicio.<br><br></li>
							<li class="justify">Evaluación del servicio realizada por <strong>LA EMPRESA</strong> al asesor, conforme al formato establecido previamente por parte de la INNPULSA COLOMBIA, en el cual se manifieste expresamente por <strong>LA EMPRESA</strong> el recibo a satisfacción de los servicios.<br><br>Esta evaluación deberá ser realizada en el Marketplace o en el medio que disponga INNPULSA COLOMBIA para ello, por parte del representante legal de la empresa beneficiaria conforme a las indicaciones que INNPULSA COLOMBIA defina para ello y el resultado de dicha evaluación no podrá ser inferior a 3.5 puntos.<br><br></li>
							<li class="justify">Evaluación a <strong>LA EMPRESA</strong> por parte del <strong>ASESOR</strong>. Esta evaluación deberá ser diligenciada y suscrita por parte del representante legal del <strong>ASESOR</strong> conforme a las indicaciones impartidas por INNPULSA COLOMBIA.<br><br> <strong>EL ASESOR</strong> deberá realizar la evaluación de <strong>EL EMPRESARIO</strong> en un tiempo no mayor a treinta (30) días calendario, después de finalizar el servicio conforme al procedimiento, tiempos y criterios establecidos por INNPULSA COLOMBIA.<br><br></li>
							<li class="justify">Informe que contenga la identificación de las falencias y oportunidades empresariales que permitan el crecimiento de las empresas atendidas.</li>
						</ul>
				<br><br>
				</li>
				<li class="justify"><strong>OBLIGACIONES GENERALES: </strong>
					<ul class="disc-style-list">
						<li class="justify">Desarrollar las actividades que se requieren para la efectiva prestación de los servicios ofrecidos a <strong>LA EMPRESA</strong> de conformidad con lo establecido en el acuerdo de negociación suscrito entre <strong>LA EMPRESA</strong> y <strong>EL ASESOR</strong>.</li>
						<li class="justify">Prestar el/los servicios a <strong>LA EMPRESA</strong> dentro del tiempo estimado en el acuerdo de negociación, aprobado entre <strong>LA EMPRESA</strong> y <strong>EL ASESOR</strong> para el desarrollo de las actividades correspondientes. En caso de requerirse un plazo adicional para la culminación de las actividades definidas en el mencionado acuerdo, <strong>EL ASESOR</strong> y<strong> EL EMPRESARIO</strong> deberán informar a INNPULSA COLOMBIA para su aprobación con <span class="bold">treinta (30) días calendario de anterioridad al vencimiento del servicio</span>, la justificación y el tiempo adicional requerido para la culminación de las actividades, el cual <span class="bold">en ningún caso podrá ser superior a sesenta (60) días calendario adicionales al plazo inicialmente establecido.</span></li>
						<li class="justify">Informar a INNPULSA COLOMBIA para aprobación, cualquier modificación de las condiciones de ejecución del acuerdo de servicios suscrito entre <strong>LA EMPRESA</strong> y <strong>EL ASESOR</strong>.</li>
						<li class="justify">Presentar a consideración de INNPULSA COLOMBIA el(los) acuerdo(s) de negociación previa suscripción de este con<strong> LA EMPRESA</strong>.</li>
						<li class="justify">Transferir el conocimiento y las herramientas a <strong>LA EMPRESA</strong> conforme a los servicios prestados.</li>
						<li class="justify">Cumplir con la presentación de los entregables establecidos en el acuerdo de negociación firmado entre <strong>LA EMPRESA</strong> y <strong>EL ASESOR</strong>.</li>
						<li class="justify">Presentar a INNPULSA COLOMBIA los entregables definidos en el presente contrato.</li>
						<li class="justify">Presentar los informes requeridos por el supervisor del contrato o por otros organismos de seguimiento y control en las condiciones y plazos establecidos por los mismos y atender las visitas de seguimiento que adelanten dichos entes.</li>
						<li class="justify">Formalizar con <strong>LA EMPRESA</strong> y bajo su exclusiva responsabilidad la forma de pago del precio que exceda el monto de la llave de acceso otorgada a <strong>LA EMPRESA</strong> en los casos que aplique, lo cual en ningún caso hará parte del acuerdo de servicio que se pagará con cargo a los recursos de la llave de acceso que otorgará INNPULSA COLOMBIA.</li>
						<li class="justify">Asistir a las reuniones que se requieran para atender cualquier requerimiento efectuado por INNPULSA COLOMBIA y/o<strong> LA EMPRESA</strong></li>
						<li class="justify">Suministrar al SUPERVISOR del contrato toda la información que éste requiera y sea pertinente sobre el desarrollo del contrato y en general, todos los requerimientos que el mismo formule y que se relacionen directamente con el mismo.</li>
						<li class="justify">Abstenerse de realizar cualquier acto fraudulento en desarrollo del presente contrato.</li>
						<li class="justify">Guardar absoluta reserva y no utilizar total o parcialmente la información de carácter confidencial que reciba directa o indirectamente de INNPULSA COLOMBIA y del <strong>EMPRESARIO</strong> para propósitos diferentes al cumplimiento del presente contrato.</li>
						<li class="justify">Adoptar las medidas necesarias para mantener la confidencialidad de la información recibida por INNPULSA COLOMBIA y<strong> LA EMPRESA</strong>.</li>
						<li class="justify">Abstenerse de adquirir compromisos a nombre de INNPULSA COLOMBIA ante <strong>LA EMPRESA</strong> beneficiario del servicio.</li>
						<li class="justify">Informar oportunamente a INNPULSA COLOMBIA de cualquier cambio de dirección de notificación, representación legal o de su calidad tributaria.</li>
						<li class="justify"><span class="bold underlined">Las demás obligaciones pactadas en el Contrato Celebrado con INNPULSA y las que contribuyan a garantizar su cabal cumplimiento, ejecución y finalización.</span></li>
					</ul>
				</li>
			</ol>
		<br><br>	
		</li>
		<li class="justify"><strong>ASPECTOS A CUMPLIR POR PARTE DE <strong>LA EMPRESA</strong>: </strong>
			<ol>
				<li class="justify"><strong>OBLIGACIONES DE <strong>LA EMPRESA</strong>: </strong>
					<ol>
						<li class="justify"><strong>LA EMPRESA</strong> debe disponer del equipo de trabajo necesario para el desarrollo de las actividades que permitan la prestación del servicio por parte del <strong>ASESOR</strong>.</li>	
						<li class="justify"><strong>LA EMPRESA</strong> debe recibir y aplicar los conocimientos y herramientas transferidos por <strong>EL ASESOR</strong> conforme a los servicios prestados y consignados en el acuerdo de negociación firmado.</li>
						<li class="justify"><strong>LA EMPRESA</strong> debe informar de manera permanente a INNPULSA COLOMBIA sobre el desempeño, disposición y compromiso del <strong>ASESOR</strong> prestador del servicio.</li>
						<li class="justify"><strong>LA EMPRESA</strong> deberá informar a INNPULSA COLOMBIA, cualquier situación que afecte la prestación normal del servicio y/o que altere la ejecución del acuerdo de negociación suscrito, con el fin de identificar soluciones y tomar las medidas correspondientes.</li>
						<li class="justify"><strong>LA EMPRESA</strong> dentro de los treinta (30) días calendario siguientes de finalizado la prestación del servicio por parte del <strong>ASESOR</strong> deberá presentar a INNPULSA COLOMBIA una <span class="bold">evaluación integral del servicio prestado por <strong>EL ASESOR</strong> por medio de la plataforma de ALDEA</span> o en el medio que INNPULSA COLOMBIA disponga para ello.</li>
						<li class="justify">El precio del servicio a negociar entre <strong>LA EMPRESA</strong> y asesor no podrá superar en ningún caso el valor del servicio presentado por el asesor y publicado en el Marketplace o en el medio que INNPULSA COLOMBIA disponga para ello.</li>
						<li class="justify">INNPULSA COLOMBIA no tendrá ninguna responsabilidad si <strong>LA EMPRESA</strong> decide contratar servicios por un valor superior al monto de la Llave de Acceso otorgada. Por lo anterior, es obligación de <strong>LA EMPRESA</strong> reconocer y pagar a <strong>EL ASESOR</strong> la remuneración que le corresponda, conforme a la diferencia que llegare a existir entre el valor total del servicio seleccionado y el valor del servicio habilitado por INNPULSA COLOMBIA.</li>
						<li class="justify"><strong>LA EMPRESA</strong> autoriza expresa e irrevocablemente a INNPULSA COLOMBIA para reproducir, distribuir, comunicar públicamente en cualquier forma y por cualquier medio la información relacionada con su experiencia en el programa ALDEA de INNPULSA COLOMBIA.</li>
						<li class="justify"><strong>LA EMPRESA</strong> debe hacer uso de el/los servicios contratados en el tiempo estimado en el acuerdo de negociación.</li>
						<li class="justify">En caso de requerirse un plazo adicional para la culminación de las actividades definidas en el acuerdo, <span class="bold"><strong>EL ASESOR</strong> y<strong> LA EMPRESA</strong> deberán informar a INNPULSA COLOMBIA para su aprobación con treinta (30) días calendario de anterioridad al vencimiento del servicio, la justificación y el tiempo adicional requerido para la culminación de las actividades, el cual en ningún caso podrá ser superior a sesenta (60) días calendario adicionales al plazo</span></li>
						<li class="justify">El uso de la de la Llave de Acceso estará sujeto a la disponibilidad de recursos de iNNpulsa Colombia.</li>
						<li class="justify"><strong>LA EMPRESA</strong> debe cumplir con todas y cada una de las condiciones que se pacten en el acuerdo de negociación.</li>
						<li class="justify"><strong>LA EMPRESA</strong> debe participar en cada parte del proceso a que se refiere este reto; si decide abandonarlo, perderá el beneficio otorgado y no podrá postularse al programa ALDEA hasta un año después de la notificación de pérdida de beneficio enviada por parte de iNNpulsa Colombia.</li>
						<li class="justify">Si existe alguna inconformidad deberá reportar oportunamente a iNNpulsa Colombia.</li>
						<li class="justify">iNNpulsa Colombia podrá solicitarle información de la evolución de la empresa en cualquier momento, así como información necesaria para conocer el impacto de cara a los servicios recibidos.</li>
						<li class="justify"><strong>LA EMPRESA</strong> no podrá trasladar o trasferir la llave de acceso a otras empresas. <strong>LA EMPRESA</strong> solo podrá utilizar las llaves de acceso para prestaciones de servicios de asesores sobre ella, quien fue la que surtió los retos anteriores del programa ALDEA, en ningún caso <strong>LA EMPRESA</strong> podrá hacer uso de los servicios especializados para fortalecer otras personas diferente a ella misma.</li>
						<li class="justify">Las demás obligaciones indicadas en los términos de invitación del programa ALDEA y sus respectivas adendas.</li>
					</ol>
				</li>	
			</ol>
		<br><br>	
		</li>
		<li class="justify"><strong>OTRAS CONSIDERACIONES:</strong>
			<ul>
				<p class="justify"><strong>CONFIDENCIALIDAD DE LA INFORMACIÓN:</strong> Las partes se obligan a mantener bajo absoluta confidencialidad y reserva toda la información y documentación que intercambien en desarrollo del servicio, en especial la información entregada por <strong>EL EMPRESARIO</strong>, para lo cual deberán tomar todas las precauciones necesarias y apropiadas para la guarda de la confidencialidad y comunicará a todos sus empleados que puedan tener acceso a dicha información y documentos, que la misma es de carácter estrictamente confidencial y reservado y que por lo tanto, no puede ser divulgado a terceros, ni usada para ningún propósito diferente al consignado en el presente documento.</p>
				<p class="justify"><strong>PROPIEDAD INTELECTUAL:</strong> <strong>EL ASESOR</strong> entiende y acepta que <strong>LA EMPRESA</strong> adquiere todos los derechos sobre los bienes protegibles por vía de la propiedad intelectual e industrial, creados o desarrollados en virtud del presente acuerdo de negociación. <br><br>Respecto de las obras protegibles por el derecho de autor, que sean creadas en ejecución del presente instrumento, estas serán obras por encargo, en virtud del artículo 28 de la Ley 1450 de 2011, y por lo tanto <strong>EL ASESOR</strong> cederá a favor de <strong>LA EMPRESA</strong>, la totalidad de los derechos patrimoniales de autor sobre las mencionadas obras, para todos los países del mundo, y por el término de protección de dichas obras. <br><br>Los derechos patrimoniales que adquiere <strong>LA EMPRESA</strong>, en virtud de este documento, incluyen, pero no están limitados a, la reproducción, edición, publicación, transformación, adaptación, comunicación, distribución y en general, cualquier tipo de explotación sobre las obras creadas, reproducidas o adaptadas en virtud del presente acuerdo, incluyendo el derecho de puesta a disposición. <strong>LA EMPRESA</strong> podrá reproducir, comunicar, distribuir o explotar dichas obras por cualquier medio conocido o por conocerse, incluyendo, pero sin limitarse a, publicación electrónica, en forma de mensaje de datos (Internet, EDI, Correo electrónico etc.), podcast, webcast.<br><br><strong>EL ASESOR</strong> se obliga a obtener de terceros, personas naturales y jurídicas, que participen en las actividades necesarias para la ejecución del presente acuerdo, las cesiones, autorizaciones y releases necesarios para asegurar la cesión a <strong>LA EMPRESA</strong> de cualquier derecho de propiedad intelectual e industrial sobre las obras producidas o creadas que resulten de la ejecución del presente contrato. <br><br><strong>EL ASESOR</strong> declara que entiende y consiente que <strong>LA EMPRESA</strong> podrá disponer libre e indefinidamente de todos y cada uno de los derechos patrimoniales de autor de que tratan los artículos 12 y 76 de la Ley 23 de 1982, teniendo como único límite los derechos morales que la ley reconoce a los autores de las obras protegibles por el derecho de autor. <br><br><strong>LA EMPRESA</strong> y <strong>EL ASESOR</strong> manifiestan que los derechos sobre los bienes protegibles por vía de la propiedad intelectual e industrial, que hayan sido creados con anterioridad a la firma de este contrato, seguirán siendo propiedad de la parte que corresponda.<br><br><strong>EL ASESOR</strong> conservará la facultad de utilizar para trabajos futuros las metodologías, documentos y herramientas desarrollados por éste. <br><br><strong>LA EMPRESA</strong> por su parte deberá respetar los derechos de propiedad de la metodología, documentos y herramientas suministradas por <strong>EL ASESOR</strong> para la prestación del servicio. <br><br><strong>EL ASESOR</strong> declara y acepta que <strong>LA EMPRESA</strong> está facultado para conceder sublicencias a terceros.</p>
				<p class="justify"><strong>SOLUCIÓN DE CONTROVERSIAS: </strong> Si se presentan diferencias entre <strong>EL ASESOR</strong> y <strong>EL EMPRESARIO</strong> en relación con la prestación de los servicios, deberán ser solucionadas a través de un acuerdo directo, donde se buscará llegar a un acuerdo, cuando no se haya logrado, podrán resolverse acudiendo ante un centro de arbitraje y conciliación de la cámara de comercio o de consultorios jurídicos que sea definido por las partes, la decisión deberá ser en derecho. De no encontrar solución deberán explorar mecanismos alternativos de solución de conflictos.</p>
			</ul>
		</li>
	
	</ol>
	<br><br>
	<p class="justify">Para constancia de su celebración se aprueba este instrumento por las partes el:</p>
	<br><br>
	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<td class="center"><strong>ASESOR</strong></td>
				<td class="center"><strong>EMPRESARIO</strong></td>
			</tr>
			<tr>
				<td class="center">{{ strtoupper(trim($data->nombre_representante_legal_proveedor)) }}</td>
				<td class="center">{{ strtoupper($data->nombre_representante_legal_comprador) }} {{ strtoupper($data->apellido_representante_legal_comprador) }}</td>
			</tr>
			<tr>
				<td class="center">REPRESENTANTE LEGAL</td>
				<td class="center">REPRESENTANTE LEGAL</td>
			</tr>
			<tr>
				<td class="center">{{ $data->fecha_aprobacion_asesor_acuerdo_compra }}</td>
				<td class="center">{{ $data->fecha_acuerdo_aceptacion_compra }}</td>
			</tr>
		</thead>
		<tbody>

		</tbody>
	</table>
	<br><br>
	<p class="text-left">
		Revisado por: {{ $data->nombre_funcionario }} el {{ $data->fecha_acuerdo_aprobacion_compra }}
	</p>
@stop
