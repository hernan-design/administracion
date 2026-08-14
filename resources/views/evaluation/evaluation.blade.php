@extends('layout.layout')

@section('content')
	<div class="clearfix"></div>
	<section id="evaluations-wrapper">
		<section id="evaluations-content" class="evaluations-content">
			{{-- evaluation dinamic content  --}}
			@yield('evaluation.content')
		</section>
	</section>
@stop
