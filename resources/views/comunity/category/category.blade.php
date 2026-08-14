@extends('layout.layout')

@section('content')
	<section id="categories-wrapper">
		<section id="category-content" class="category-content clearfix">
			{{-- category dinamic content  --}}
			@yield('category.content')
		</section>

		<script type="text/template" id="confirm-category-tpl">
			<p>¿Está seguro que desea realizar esta acción en esta categoría?</p>
		</script>
	</section>
@stop
