@extends('emails.layout')

@section('content')

	<p style="text-align:justify">
		<span>{{{ $_inst_name }}} </span>te quiere recordar el evento <b>{{ $_name_event }}</b> 
		 
	</p>

	<p><b>Fecha:</b> {{ $_date }}</p>
	<p><b>Hora:</b> {{ $_hour }}</p>
 
	<p>Para ver más detalles del evento:</p>
	@include('emails.calltoaction', [
										'label' => (isset($label_action) ? $label_action : 'Ver detalles del evento'), 
										'action' => $_url_event, 
										'color' => $_inst_color
									])

	
	@if ($_url_reunion)
		<p>Para unirse a la reunión:</p>
		@include('emails.calltoaction', [
											'label' => (isset($label_action) ? $label_action : 'Ir al evento'), 
											'action' => $_url_reunion, 
											'color' => $_inst_color
											])
	@endif								

@stop
