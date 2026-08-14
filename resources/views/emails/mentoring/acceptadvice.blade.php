@extends('emails.layout')
@section('content')
	<p style="text-align:center">
		<!-- Cordialmente te informamos que tu solicitud realizada al mentor {{ $_requesting }} ha sido aceptada para iniciar el proceso de acompañamiento. -->

		{{ transtp('mentoring.processMassive.processAccept', ['requesting' => $_requesting]) }}
	</p>

	@include('emails.calltoaction', ['label' => 'Revisa aquí', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
@stop
