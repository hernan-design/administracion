@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		El presente diagnostico busca determinar cuál es la ruta de formación que debe seguir cada emprendedor. Su objetivo final es identificar cuáles de las sesiones de capacitación deben ser cursadas.
	</p>

	@include('emails.calltoaction', ['label' => 'Aplicar diagnostico', 'action' => "$_inst_url$url_action", 'color' => $_inst_color])
@stop
