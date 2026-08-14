@extends('layout.layout')

@section('content')
	<section id="diagnostics-wrapper">
		<section id="diagnostics-content" class="diagnostics-content">
			{{-- event dinamic content  --}}
			@yield('diagnostics.content')
		</section>
	</section>
@stop
