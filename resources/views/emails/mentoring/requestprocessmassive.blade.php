@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
		{!! transtp('mentoring.processMassive.request') !!}
	</p>
	@foreach ($_history as $item)
		<p>
			{{ $item }}
		</p>
	@endforeach
@stop
