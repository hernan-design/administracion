@extends('emails.layout')

@section('content')	
	<p style="text-align:justify">Bienvenido a esta nueva etapa de Empodera</p>
	<p style="text-align:justify">Tu proceso de vinculación ha sido exitoso, hemos revisado y validado tu documentación.</p>
	<p style="text-align:justify">Para continuar con el proceso ingresa a la plataforma descarga, lee y acepta la carta de compromiso</p>
	@include('emails.calltoaction', ['label' => 'Ver carta', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
@stop
