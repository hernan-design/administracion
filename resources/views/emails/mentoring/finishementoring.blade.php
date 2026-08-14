@extends('emails.layout')
@section('content')
	<p style="text-align:center">

		Tu Mentor {{ $_requesting }} ha finalizado la sesión de mentoría 
		{{ $_title_mentoring }}, 
		ingresa al siguiente link para validar y aceptar lo realizado.
	</p>
	<ul>
		<li>Fecha y hora programada para la sesión: <b>{{ $_dateAndHour }}</b></li>
		<li>Duración registrada por el mentor: <b>{{ $_duration_mentoring }}</b></li>
		<li>Descripción de la sesión: <b>{{ $_description_mentoring }}</b></li>
		<li>Código de la sesión: <b>{{ $_sessionID }}</b></li>
	</ul>

	@include('emails.calltoaction', ['label' => 'Ir a procesos de mentoria', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
@stop
