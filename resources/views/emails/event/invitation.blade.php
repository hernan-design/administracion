@extends('emails.layout')

@section('content')
	<p style="text-align:justify">

    Estimado(a) {{ $_name_user }} <br><br>

    Te invitamos a participar en la actividad  {{ $_name_event }}. Para mayor información ingrese al siguiente link.

    @include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Ver detalle del evento' ), 'action' => "$_inst_url$_url_action$_url_link", 'color' => $_inst_color])
	</p>
@stop
