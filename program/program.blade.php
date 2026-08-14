@extends('layout.layout')

@section('content')
	<section id="programs-wrapper">
		<section id="programs-content" class="programs-content">
			{{-- program dinamic content  --}}
			@yield('program.content')
		</section>
	</section>
@stop
