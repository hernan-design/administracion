@extends('layout.layout')

@section('content')
	<section id="marketplaces-wrapper">
		<section id="marketplaces-content" class="marketplaces-content">
			{{-- marketplace dinamic content  --}}
			@yield('marketplace.content')
		</section>

		{{-- Confirm edit agreement --}}
		<script type="text/template" id="confirm-agreement-confirm-tpl">
			<p>¿Estas seguro que deseas confirmar este acuerdo?</p>
		</script>

		{{-- Confirm send review agreement --}}
		<script type="text/template" id="send-review-agreement-confirm-tpl">
			<p>Se enviará un correo para la revisión de los ajustes realizados</p>
			<p>¿Estas seguro que deseas realizar estos cambios?</p>
		</script>

		{{-- Confirm remove item agreement --}}
		<script type="text/template" id="remove-item-agreement-confirm-tpl">
			<p>¿Estas seguro que deseas remover este item?</p>
		</script>

		{{-- edit form select field--}}
		<script type="text/template" id="edit-form-select-field-tpl">

			{!! Form::open( array('action' => null, 'id' => 'form-edit-select-field', 'data-parsley-validate' => '') ) !!}
				<div class="col-xs-12">
					<select name="resp_actividad_acuerdo" class="edit-field-text" required>
		                <option value="resp_comprador_actividad" class="edit-field-text" <%- (resp_comprador_actividad === true && !resp_actividad_acuerdo) || resp_actividad_acuerdo == 'resp_comprador_actividad' ? 'selected' : ''%> >Empresario</option>
		                <option value="resp_proveedor_actividad" class="edit-field-text" <%- (resp_proveedor_actividad === true && !resp_actividad_acuerdo) || resp_actividad_acuerdo == 'resp_proveedor_actividad' ? 'selected' : ''%> >Proveedor</option>
		          	</select>
				</div>

			{!! Form::close() !!}
		</script>

	</section>

	{{-- Include edit templates  --}}
	@include('public.editingtemplates')
@stop
