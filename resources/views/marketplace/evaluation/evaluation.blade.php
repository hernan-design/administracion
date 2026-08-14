@extends('layout.layout')

@section('content')
	@switch(session('institution')->codigo_institucion)
		@case(61)
			@include('marketplace.evaluation.marketplaceswrapperrutan')
			@break
		@default
			@include('marketplace.evaluation.marketplaceswrapper')
	@endswitch

	{{-- section comment refused deliberables --}}
	<script type="text/template" id="comment-refused-deliverables-tpl">
		<label>Describa el por qué se hace la devolución de los entregables. <span class="required">*</span></label>
		<textarea id="comment_refused_deliverables" required="required" class="form-control" name="comment_refused_deliverables" data-parsley-trigger="keyup"></textarea>
	</script>

	{{-- section comment approve deliberables --}}
	<script type="text/template" id="comment-approve-deliverables-tpl">
		<p>¿Está seguro de aprobar estos entregables?</p>
	</script>

	{{-- template section button action deliverables --}}
  <script type="text/template" id="button-action-deliverables-tpl">
    <% console.log('estado', codigo_estado) %>
    <% console.log('state_aggremment', state_agreement) %>
		<% if(codigo_estado == window._tp.getState['confirmed'] && state_agreement == window._tp.getStateAgreement['to_finalize']) { %>
			<button class="btn btn-success btn-approve">Aceptar</button>
			<button class="btn btn-danger btn-refused">Rechazar</button>
		<% } %>
    <% if(codigo_estado == window._tp.getState['finalized'] && state_agreement == window._tp.getStateAgreement['finalized']) { %>
          <a href="https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/documents/acuerdos/acta_aprobacion_entregables_<%- codigo_acuerdo_compra + '.pdf' %>" class="btn btn-info btn-export"> Ver acta de aprobacion</a>
    <% } %>
	</script>
@stop
