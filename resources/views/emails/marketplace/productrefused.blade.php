@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
        Luego de revisar tú solicitud de publicación del producto <strong>{{ $_product }}</strong> se ha rechazado por los siguientes motivos:
		<br><br>
		{{ $_comment }}
	</p>
@stop