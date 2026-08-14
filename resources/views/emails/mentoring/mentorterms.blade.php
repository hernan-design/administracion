@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
    Hola {{ $_to_name }} :

    Hemos creado tu perfil mentor, para activarlo por favor ingresa al siguiente link, lee y acepta los  términos para convertirte en un mentor activo

		@include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Ir al perfil de mentor' ), 'action' => "$_inst_url$_url_action$_url_link", 'color' => $_inst_color])
	</p>
@stop

