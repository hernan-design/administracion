@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
		{!! transtp('mentoring.sessionMassive.request') !!}
	</p>
	@include('emails.calltoaction', [
										'label' => 'Descargar Excel',
										'action' => "$_url_action",
										'color' => $_inst_color
									])
@stop
