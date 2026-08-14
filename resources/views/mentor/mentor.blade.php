@extends('layout.layout')

@section('content')
	<section id="mentoring-wrapper">
		<section id="mentor-content" class="mentor-content clearfix">
			{{-- mentor dinamic content  --}}
			@yield('mentor.content')
		</section>

		<script type="text/template" id="confirm-mentor-tpl">
			<p>¿Está seguro que desea realizar esta acción en esta mentoría?</p>
		</script>
	</section>
@stop
