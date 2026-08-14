@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		<span> {!! $comment !!} </span>
		<br><br>
	</p>

	@include('emails.calltoaction', ['label' => 'Asignar', 'action' => "$_url_action", 'color' => $_inst_color])
@stop
