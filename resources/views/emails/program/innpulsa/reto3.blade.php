@extends('emails.layout')

@section('content')
	@if( $promoted )
		<p style="text-align:justify">
			Nos emociona mucho informarte que <strong>has superado</strong> el Segundo Reto de Aldea  "Conversa con Expertos" y por tanto has sido seleccionado(a) para participar en el Tercer Reto "Conéctate con tu Asesor".
		</p>

		<p style="text-align:justify">
			En la comunicación adjunta podrás encontrar las recomendaciones dadas por nuestra terna de expertos, el valor de la llave de acceso y las categorías de servicio en las cuales puedes usarla.
		</p>

		<p style="text-align:justify">
			Para hacer efectivo este beneficio debes surtir previamente un proceso de vinculación de clientes, por tanto debes cargar en el siguiente link de la plataforma estos documentos:
			<ul>
				<li>Registro Único Tributario - RUT.</li>
				<li>Certificado de existencia y representación legal.</li>
				<li>Fotocopia de cédula del representante legal por ambas caras.</li>
				{{-- <li>Certificación de aportes de seguridad social y parafiscales (carta emitida por contador y representante legal manifestando estar al día en aportes a seguridad social y parafiscales).</li> --}}
			</ul>
		</p>

		@include('emails.calltoaction', ['label' => 'Carga aquí los documentos', 'action' => "$_inst_url$url_action", 'color' => $_inst_color])

		<p style="text-align:justify">
			Una vez se surta el proceso de vinculación de clientes te informaremos los pasos siguientes.
		</p>

		<p style="text-align:justify">
			Si tienes alguna duda puedes comunicarte a los correos electrónicos <a href="mailto:info@innpulsacolombia.com">info@innpulsacolombia.com</a>.
		</p>

		<p style="text-align:justify">
			De nuevo no nos queda más que felicitarte por asumir con nosotros esta aventura.
		</p>
	@else
		<p style="text-align:justify">
			Recibe un afectuoso saludo de iNNpulsa Colombia.
		</p>

		<p style="text-align:justify">
			Después de haber conocido tu empresa durante el Reto 1 "Conozcámonos" y el Reto 2 "Conversa con Expertos", queremos informarte que de acuerdo con los criterios de evaluación establecidos en los términos de la invitación a participar en el programa ALDEA, tu emprendimiento obtuvo una calificación inferior a los 70 puntos mínimos requeridos, por lo cual debemos informarte que no has superado el reto 2 "Conversa con Expertos".
		</p>

		<p style="text-align:justify">
			En la comunicación adjunta podrás encontrar las recomendaciones dadas por nuestra terna de expertos, las cuales te serán de ayuda para fortalecer tu negocio y poder identificar las áreas en las cuales debes trabajar.
		</p>

		<p style="text-align:justify">
			Muchos éxitos con tu negocio y  gracias por mostrar interés en ser parte de Aldea.
		</p>
	@endif
@stop
