@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		Además de los servicios especializados que has recibido en Reto 3: ¡Conéctate con tu Asesor!, ser emprendedor/empresario ALDEA, te permitirá tener  la  eventual oportunidad de recibir apoyo financiero para apalancar el crecimiento del negocio, a través del Reto 4: ¡Levantemos Capital!.
	</p>

	<p style="text-align:justify">
		Para conocer más acerca de las fuentes de financiación disponibles, haz click en el siguiente enlace:
	</p>

	@include('emails.calltoaction', ['label' => 'Reto 4: ¡Levantemos Capital! ', 'action' => "$_inst_url$url_action", 'color' => $_inst_color])
@stop
