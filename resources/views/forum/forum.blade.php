@extends('layout.layout')

@section('content')
	<section id="forum-wrapper">
		<section id="forum-content" class="forum-content clearfix">
			{{-- mentor dinamic content  --}}
			@yield('forum.content')
		</section>

		<script type="text/template" id="confirm-forum-tpl">
			<p>¿Está seguro que desea <strong><%- action %></strong> al foro <strong><%- forum %></strong>?</p>
		</script>
	</section>
@stop
