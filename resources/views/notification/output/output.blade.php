@extends('layout.layout')

@section('content')
	<section id="output-wrapper">
		<section id="output-content" class="outputs-content">
			{{-- output dinamic content  --}}
			@yield('output.content')
		</section>
		
		<script type="text/template" id="confirm-user-tpl">
			<p>¿Está seguro que desea eliminar este usuario?</p>
		</script>
	</section>

@stop
