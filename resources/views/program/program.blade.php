@if($errors->any())
	<div class="alert alert-warning alert-dismissible" style="width:25%; float:right; margin: 10px 2em 0 auto;">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<strong>{{$errors->first()}}</strong>
	</div>
@endif

@extends('layout.layout')

@section('content')
	<section id="programs-wrapper">
		<section id="programs-content" class="programs-content">
			{{-- program dinamic content  --}}
			@yield('program.content')
		</section>
	</section>
@stop
