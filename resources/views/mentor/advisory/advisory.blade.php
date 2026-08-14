@extends('layout.layout')

@section('content')
	<section id="advisories-wrapper">
		<section id="advisory-content" class="advisory-content clearfix">
			{{-- mentor dinamic content  --}}
			@yield('advisory.content')
		</section>

		<script type="text/template" id="confirm-advisory-tpl">
			<p>¿Está seguro que desea <strong><%- action %></strong> al mentor <strong><%- advisory %></strong>?</p>
		</script>
	</section>
@stop
