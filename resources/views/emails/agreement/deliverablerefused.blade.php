@extends('emails.layout')

@section('content')
	<p style="text-align:center">
		iNNpulsa Colombia requiere ajustes a los entregables del servicio {{ $_project }} prestado al empresario {{ $_purchaser }}, con el siguiente comentario:
		<br><br>
		{{ $_comment }}
	</p>

	@include('emails.calltoaction', ['label' => 'Ajustar entregables aquí', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
@stop
