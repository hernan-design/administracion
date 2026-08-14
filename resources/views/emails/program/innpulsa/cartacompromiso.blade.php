@extends('emails.layout')

@section('content')	
	<p style="text-align:justify">Bienvenido al Reto 3 de Aldea ¡Conéctate con tu asesor!</p>
	<p style="text-align:justify">Tu proceso de vinculación ha sido exitoso, ahora, puedes ingresar al marketplace para hacer uso de tu llave de acceso para adquirir servicios especializados con los que podrás potenciar tu emprendimiento.</p>
	<p style="text-align:justify">Antes de ingresar, ten en cuenta las siguientes recomendaciones que son claves para el antes, durante y después de la contratación de servicios: </p>
	<ul>
		<li>Explora el Marketplace y revisa los servicios que tienes a tu disposición.</li>
		<li>Contacta a el/los asesores con quienes quieres profundizar acerca de sus servicios.</li>
		<li>Puedes negociar las condiciones de los servicios que vas a adquirir, teniendo en cuenta:
			<ol>
				<li>El precio no puede superar el valor original del Marketplace.</li>
				<li>No es posible tomar servicios diferentes a los que se encuentran en el Marketplace.</li>
				<li>Puedes aumentar el número de actividades, alcance, entregables, menor precio y más sesiones de trabajo.</li>
				<li>No es necesario que consumas el 100% de la llave otorgada, los recursos que no se usen se devolverán a la bolsa del programa para ser usados por otros empresarios.</li>
			</ol>
		</li>
		<li>Contratar un servicio por cada categoría habilitada previamente.</li>
		<li>
			A partir de esta comunicación EL EMPRENDEDOR, debe definir y comprar todos los servicios con la llave de acceso otorgada.
		</li>
	</ul>	
	En la ejecución de los servicios:
	<br>
	<ul>
		<li>Desde iNNpulsa Colombia hemos validado la experiencia de los equipos de trabajo de los proveedores, razón por la cual, debes exigir que la persona relacionada en los acuerdos de negociación sea quien efectivamente te preste el servicio.</li>
		<li>Si tienes observaciones, inconformidades y requieres apoyo del equipo de Aldea de iNNpulsa Colombia comunícate al teléfono 7491000 o al correo electrónico aldea@innpulsacolombia.com</li>
		<li>Recuerda que tú y el asesor deben cumplir con las actividades y fechas establecidas para la ejecución de los servicios.</li>
		<li>Al finalizar cada servicio debes calificar la calidad, pertinencia y resultados de la prestación por parte del Asesor, así como el proveedor calificará tu gestión durante el mismo.</li>
		<li>Buscamos generar el mayor impacto a los emprendedores Aldea, por ello, la calificación que brindes nos permitirá conocer tu experiencia durante la prestación del servicio y la calidad de nuestros asesores, te agradecemos ser muy puntual y honesto con la puntuación que realices. Asimismo, es importante que conozcas que la satisfacción que tengas sobre el servicio que ha sido prestado, es un factor vital para el pago al asesor sobre el trabajo ejecutado.</li>
	</ul>
	@include('emails.calltoaction', ['label' => 'Visita el marketplace', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
@stop
