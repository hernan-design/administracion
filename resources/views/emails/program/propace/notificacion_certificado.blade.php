@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		<span> {!! $comment !!} </span>
	</p>

  @include('emails.calltoaction', ['label' => 'Descarga tu certificado', 'action' => $_attach, 'color' => $_inst_color])

@stop
