@extends('layout.layout')

@section('content')
	<section id="events-wrapper">
		<section id="events-content" class="events-content">
			{{-- event dinamic content  --}}
			@yield('event.content')
		</section>
	</section>
@stop
