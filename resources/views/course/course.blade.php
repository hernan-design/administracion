@extends('layout.layout')

@section('content')
	<section id="courses-wrapper">
		<section id="course-content" class="course-content clearfix">
			{{-- course dinamic content  --}}
			@yield('course.content')
		</section>

		<script type="text/template" id="confirm-course-tpl">
			<p>¿Estas seguro que deseas realizar esta acción en este curso?</p>
		</script>
	</section>
@stop
