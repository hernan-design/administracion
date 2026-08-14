@extends('layout.layout')

@section('content')
	<section id="channels-wrapper">
		<section id="channel-content" class="channel-content clearfix">
			{{-- channel dinamic content  --}}
			@yield('channel.content')
		</section>

		<script type="text/template" id="confirm-user-tpl">
			<p>¿Está seguro que desea eliminar este usuario?</p>
		</script>

		<script type="text/template" id="confirm-activation-user-tpl">
			<p>¿Está seguro que desea <%- message === 'activate' ? 'activar' : 'inactivar' %> este usuario?</p>
		</script>

		<script type="text/template" id="confirm-channel-tpl">
			<% if( count > 0 && codigo_estado == window._tp.getState['active'] ){ %>
				<p>El canal <strong><%- channel %></strong> contiene <strong><%- count %></strong> usuarios inscritos,  ¿realmente desea <%- action %>?</p>
			<% }else{ %>
				<p>¿Está seguro de <strong><%- action %></strong> el canal <strong><%- channel %></strong>?</p>
			<% } %>
		</script>
	</section>
@stop
