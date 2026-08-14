@extends('layout.layout')

@section('content')
	<section id="marketplace-assignment-wrapper">
		<section id="marketplace-assignment-content" class="marketplace-assignment-content">
			{{-- marketplace assignment dinamic content  --}}
			@yield('assignment.content')
			@yield('assignment.detail.content')
		</section>
	</section>
@stop
