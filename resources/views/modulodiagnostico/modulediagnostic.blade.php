@extends('layout.layout')

@section('content')
	<section id="module-diagnostic-wrapper">
		<section id="module-diagnostics-content" class="module-diagnostics-content">
			{{-- event dinamic content  --}}
			@yield('modulediagnostics.content')
		</section>
	</section>
@stop
