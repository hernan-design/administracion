@extends('layout.layout')

@section('content')
	<section id="articles-wrapper">
		<section id="articles-content" class="articles-content clearfix">
			{{-- article dinamic content  --}}
			@yield('article.content')
		</section>
	</section>
@stop
