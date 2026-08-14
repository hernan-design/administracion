@extends('layout.layout')

@section('content')
	<section id="managements-wrapper">
		<section id="managements-content" class="managements-content">
			{{-- management dinamic content  --}}
			@yield('management.content')
		</section>
    </section>
@stop