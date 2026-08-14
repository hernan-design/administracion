@extends('emails.layout')

@section('content')
	<p>Has sido registrado(a) en la plataforma {{ $_inst_name }}.</p>

	<p>Tus datos de acceso son:</p>

	<p>
		Usuario: {{ $_to }}
	</p>

	<p>Si quieres cambiar tu contraseña haz click en el siguiente botón: </p>

	@include('emails.calltoaction', ['label' => 'Cambiar mi contraseña', 'action' => "$_inst_url$_change_password", 'color' => $_inst_color])
@stop
