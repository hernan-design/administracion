@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		Tú solicitud de registro como mentor ha sido aceptada. Bienvenido(a) a nuestra red de mentores Ingresa a tu perfil aquí

		@include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Ver perfil'), 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
	</p>
@stop
