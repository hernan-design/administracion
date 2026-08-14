@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
		<!-- Nos alegra informarte que te hemos asignado la empresa {{ $_entity }}, ya puedes iniciar el proceso de acompañamiento con el(a) empresario(a) {{ $_requesting }}. -->
		{!! transtp('mentoring.process.request', ['entity' => $_entity, 'requesting' => $_requesting]) !!}

		@include('emails.calltoaction', ['label' => 'Aceptar solicitud', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
	</p>
@stop
