@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		Has sido seleccionado como proveedor del Mercatón WEB, ingresa en el siguiente enlace para publicar tu primer producto.
	</p>

	@include('emails.calltoaction', ['label' => 'Click aquí', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
@stop
