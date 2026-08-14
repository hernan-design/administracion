@extends('layout.layout')

@section('content')
	<section id="evaluations-wrapper">
		<section id="evaluation-content" class="evaluation-content clearfix">
			{{-- evaluation dinamic content  --}}
			@yield('evaluation.content')
		</section>
	</section>
@stop
