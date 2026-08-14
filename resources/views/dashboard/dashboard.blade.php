@extends('layout.layout')

@section('content')
	<section id="dashboard-wrapper">
		<section id="dashboard-content" class="dashboard-home-content clearfix">
			{{-- dashboard dinamic content  --}}
			@yield('dashboard.content')
		</section>
	</section>
@stop
