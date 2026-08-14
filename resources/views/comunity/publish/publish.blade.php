@extends('layout.layout')

@section('content')
	<section id="publish-wrapper">
		<section id="publish-content" class="publications-content">
			{{-- publish dinamic content  --}}
			@yield('publish.content')
		</section>

		<script type="text/template" id="confirm-publish-tpl">
			<p>¿Estas seguro que deseas realizar esta acción en esta publicación?</p>
		</script>
	</section>
@stop
