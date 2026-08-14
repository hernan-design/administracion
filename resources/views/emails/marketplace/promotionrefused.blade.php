@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
		Luego de revisar tú solicitud de publicación de la promoción <strong>No. {{ $_promotion }}</strong> se ha rechazado por los siguientes motivos:
		<br><br>
		{{ $_comment }}
	</p>
@stop