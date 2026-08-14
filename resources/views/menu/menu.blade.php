@extends('layout.layout')

@section('content')
	<section id="menu-wrapper">
		<div class="row">
	        <div class="page-title">
	          <div class="title_left">
	            <h3>Menu <small> Lista </small></h3>
	          </div>
	        </div>
	    </div>
		<section id="menu-content" class="menu-content">
			{{-- Menu dinamic content  --}}
			@yield('menu.content')
		</section>
	</section>
@stop
