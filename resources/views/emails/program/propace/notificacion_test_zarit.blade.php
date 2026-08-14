@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		<span> {!! $comment !!} </span>
	</p>

  @include('emails.calltoaction', ['label' => 'Ingresa', 'action' => '', 'color' => $_inst_color])

@stop
