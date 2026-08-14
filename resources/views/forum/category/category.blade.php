@extends('layout.layout')

@section('content')
	<section id="categories-wrapper">
		<section id="forum-category-content" class="forum-category-content clearfix">
			{{-- category forum dinamic content  --}}
			@yield('forum.category.content')
		</section>

		<script type="text/template" id="confirm-forum-category-tpl">
			<p>¿Está seguro que desea realizar esta acción en esta categoría?</p>
		</script>
	</section>
@stop
