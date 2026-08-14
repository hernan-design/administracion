@extends('layout.layout')

@section('content')
	<section id="forms-wrapper">
		<section id="form-content" class="form-content clearfix">
			{{-- form dinamic content  --}}
			@yield('form.content')
		</section>
	</section>
@stop
