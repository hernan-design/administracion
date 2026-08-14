@extends('emails.layout')
@section('content')
	<h5> Apreciado(s) {{ $_entity_name }}</h5>
	<p style="text-align:justify">
		Para la Corporación Ruta N es un gusto informarte sobre la aprobación de los beneficios definidos en tu plan de intervención
		de la Ruta del Emprendimiento, teniendo como objetivo contribuir con el crecimiento de tu emprendimiento/empresa desde la estrategia
		de los Centros del Valle del Software, la cual busca fomentar el emprendimiento, la innovación y la apropiación de tecnologías
		de la cuarta revolución industrial.
	</p>
	<p style="text-align:justify">
		Mi nombre es <b>{{ $_manager_voucher_name }}</b>, y soy la persona que te acompañará durante el proceso de redención de beneficios,
		con el fin de realizar seguimiento al cumplimiento de los objetivos y metas definidas según lo concertado entre las partes
		(proveedor del beneficio–emprendedor/empresario).
	</p>
	<p style="text-align:justify">
		Para esto, podrás contactarme a la dirección de correo electrónico
		<b>{{ $_manager_voucher_email }}</b>, o telefónicamente al <b>{{ $_manager_voucher_celphone }}</b>.
		Cabe resaltar que el desarrollador de negocio que venía apoyando tu proceso, seguirá realizando el acompañamiento técnico a
		través de la Ruta del emprendimiento.
	</p>
	<p style="text-align:justify">
		Los beneficios que han sido aprobados para tu empresa/emprendimiento son:
	</p>
	<table style="border: 1px solid #727272; border-collapse: collapse;">
		<thead style="border: 1px solid #727272; border-collapse: collapse;
			font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
			font-size: 13.5px;
			color: #727272;">
			<tr>
				<th "border: 1px solid #727272;
				border-collapse: collapse;
				font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
				font-size: 13.5px;
				color: #727272;">Servicios</th>
				<th "border: 1px solid #727272;
				border-collapse: collapse;
				font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
				font-size: 13.5px;
				color: #727272;">Potencializadores</th>
			</tr>
		</thead>
		<tbody>
			@if (!empty($_providers))
				@foreach ($_providers as $_provider)
					<tr style="border: 1px solid #727272; border-collapse: collapse;">
						<td style="border: 1px solid #727272;
						border-collapse: collapse;
						font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
						font-size: 13.5px;
						color: #727272;">{{ $_provider->nombre_categoria_producto }}</td>
						<td style="border: 1px solid #727272;
						border-collapse: collapse;
						font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
						font-size: 13.5px;
						color: #727272;">{{ $_provider->nombre_usuario ? $_provider->nombre_usuario : NULL }}</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	  </table>

	<p style="text-align:justify">
		Ten en cuenta que tienes un plazo de hasta 3 días hábiles contados desde la recepción de esta comunicación,
		para realizar la selección del proveedor de tu preferencia a través de a la plataforma Marketplace conforme
		los productos/servicios habilitados en el siguiente enlace:
		<a href="https://appcvs.rutan.co/marketplace" target="_blank">Marketplace</a>
	</p>
	<p style="text-align:justify">
		A continuación, te compartimos algunas recomendaciones:
		<ul>
			<li>
				Revisa la presencia del proveedor en internet y redes sociales, con la intención de buscar validación respecto a tus expectativas.
				¿Ya viste el perfil de LinkedIn del proveedor ofertante?
			</li>
			<li>
				Identifica la experiencia del proveedor en el sector del cual participa tu emprendimiento/empresa, con el fin de que la
				intervención resulte más efectiva y cercana a tus necesidades específicas.
			</li>
			<li>
				Ten en cuenta que, desde este momento se inicia el proceso de celebración de compromisos contractuales asociados a los beneficios,
				lo cual puede requerir de diferentes periodos de tiempo según la necesidad o requerimiento.
			</li>
			<li>
				En caso de que te haya sido asignado más de un beneficio, recuerda que el plazo para la ejecución de estos es de 2 meses. Por tal motivo,
				deberás organizar tu cronograma de la mano de los proveedores, para poder hacerlos efectivos en el marco de dicho periodo de tiempo.
			</li>
		</ul>
	</p>
	<p style="text-align:justify">
		Quedamos atentos a tu selección e inquietudes.
	</p>
	<p style="text-align:justify">
		Cordialmente, &nbsp;
		Equipo Váucher &nbsp;
		Corporación Ruta N.
	</p>
@stop
