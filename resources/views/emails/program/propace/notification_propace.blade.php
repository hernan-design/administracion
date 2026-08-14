@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		<span> {!! $comment !!} </span>
	</p>

  @include('emails.calltoaction', ['label' => 'Ingresar', 'action' => $_inst_url.'/aprende/micontenido', 'color' => $_inst_color])

@stop
