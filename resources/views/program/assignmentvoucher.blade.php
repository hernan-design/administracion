@extends('program.program')

@section('program.content')
	{{-- ------------------ Program Templates JS -------------------- --}}

    <div class="col-xs-12" id="convocatoria-asignacion-voucher">
		<div class="x_panel">
			<div class="x_title">
				<h2>Asignación de voucher</h2>
				<div class="clearfix"></div>
			</div>
			
			<div class="x_content">
				<div class="well">
					<br/>
					{{-- Lista de proyectos para procesos de mentoria --}}
					<div class="table-responsive">
						<table id="mentorings-vouchers-table" class="table table-striped table-bordered" data-page-length="15">
							<thead>
							<tr class="headings">
								<th class="column-title"># Mentoria</th>
								<th class="column-title">Estado mentoria</th>
								<th class="column-title">Fase</th>
								<th class="column-title">Mentor</th>
								<th class="column-title"># sesión</th>
								<th class="column-title">Estado sesión</th>
								<th class="column-title"></th>
							</tr>
							</thead>
							<tbody >
							<!-- Render view  -->
							</tbody>
						</table>
					</div>

					<br/>
					<div id="section_question_voucher" class="text-center">
						{{-- section-question-voucher-tpl --}}
					</div>
				</div>
				<br/>

			</div>
			<div class="x_content ">
				<div id="form-voucher-toggle" class="well">
					{{-- form-voucher-toggle-tpl --}}
				</div>
			</div>
		</div>
	</div>

<script type="text/template" id="form-voucher-toggle-tpl">
	{!! Form::open(array('route' => null, 'id' => 'form-assignment-voucher', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
				<label>Observaciones</label>
				<textarea
					class="form-control"
					id="comentario"
					name="comentario"
					rows="2"
					<%- categoriasesion > 0 ? 'readonly' : '' %>
					><%- comentario %></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table class="table table-striped jambo_table bulk_action" id="assignmentvoucher-list-table">
						<thead>
							<tr>
								<th colspan="2" class="text-center">Asignación de Vouchers</th>
							</tr>
							<tr>
								<th class="text-center">Seleccionar</th>
								<th class="text-center">Categoria</th>
							</tr>
						</thead>
						<tbody >
							<!-- Render view  -->
						</tbody>
					</table>
					<!-- end Categories Product -->
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-2 col-md-offset-5">
				<button type="submit" class="btn btn-block btn-success data-filter" id="submit-voucher">Guardar</button>
			</div>
		</div>
	{!! Form::close() !!}
</script>

{{-- buttons acction --}}
<script type="text/template" id="buttons-action-tpl">
	<div class="btn-group tp_btn-group">
		<input
			{{-- class="form-control checkbox_select_entity" --}}
			type="checkbox"
			id="<%- row.row %>"
			value="<%- full.codigo_categoria_producto %>"
			name="codigo_categoria_producto"
			<%- !_.isNull(full.categoria_producto) ? 'checked="true"' : '' %>
		/>
	</div>
</script>

<script type="text/template" id="buttons-select-session-tpl">
	<div class="btn-group tp_btn-group">
		<button
			class="btn btn-xs btn-success select_session"
			type="button"
			id="<%- row.row %>"
			value="<%- full.codigo_mentoria %>"
			data-categoriasesion="<%- full.codigo_categoria_producto_sesion %>"
			data-observacion="<%- full.observacion %>"
		>
			Seleccionar
		</button>
	</div>
</script>

<script type="text/template" id="section-question-voucher-tpl">
	<div><h4>¿Asignar voucher a la sesion <%- session %>?</h4>
		<button
			class="btn btn-success"
			type="button"
			id="si_voucher"
			>Si
		</button>
		<button
			class="btn btn-warning"
			type="button"
			id="no_voucher"
			>No
		</button></div>
</script>

<script type="text/template" id="confirm-no-voucher-tpl">
	<label for="comentario_19">Agrega un comentario <span class="required">*</span></label>
	<textarea
		id="comentario_19"
		class="form-control"
		required></textarea>
</script>
<script type="text/template" id="confirm-voucher-tpl">
	<p>¿Está seguro de asignar vouchers?</p>
</script>
@stop
