@extends('layout.layout')

@section('content')
	<section id="institution-wrapper">
		<section id="institution-content" class="institution-content">
			{{-- institution dinamic content  --}}
			@yield('institution.content')
		</section>
	</section>
@stop
