@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		{{ $_content }}
	</p>
	
	{{ $_url }}
@stop
