{{-- views/marketplace/promotion/index.blade.php --}}
@extends('marketplace.promotion.promotion')

@section('promotion.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Promociones</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-promotions-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-3 col-md-offset-3 col-sm-6 col-xs-12">
								<label>Correo</label>
								<input type="email" name="correo_electronico" id="correo_electronico" class="form-control">
							</div>
							<div class="col-md-3 col-sm-12 col-xs-12 form-group">
								<label>Estado</label>
								<select name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Selecciona una opción</option>
									@if(isset($states))
										@foreach( $states as $value)
											<option value="{{ $value->codigo_estado }}">{{ ucfirst(strtolower($value->nombre_estado))}}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 text-center">
								<button type="submit" class="btn btn-primary btn-search">Buscar</button>
								<button type="button" class="btn btn-default btn-clear">Limpiar</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>

				<!-- start promotions -->
				<div class="table-responsive">
					<table id="promotions-marketplace-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
							<tr>
								<th>Id Promoción</th>
								<th>Nombre</th>
								<th>Correo</th>
								<th>Estado</th>
								<th class="text-center">Activación</th>
								<th class="text-center">Acción</th>
							</tr>
						</thead>
						<tbody >
							<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<!-- end promotions -->
			</div>
		</div>
	</div>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if(full.codigo_estado == window._tp.getState['pending']) { %>
					<li><a href="#" class="btn-approve" data-action="aprobar" data-id="<%- full.codigo_promocion %>" data-name="<%- full.tipo %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-check"></i> Aprobar</a>
					</li>
					<li><a href="#" class="btn-back" data-action="rechazar" data-id="<%- full.codigo_promocion %>" data-name="<%- full.tipo %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-close"></i> Rechazar</a>
					</li>
				<% }else{ %>
					<li><a href="#"> No hay acciones </a>
					</li>
				<% } %>
			</ul>
		</div>
	</script>

	{{-- template text confirm --}}
	<script type="text/template" id="active-confirm-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> <%- tipo %> <strong><%- codigo_promocion %></strong>?</p>
	</script>

	{{-- template text activation --}}
	<script type="text/template" id="activation-promotion-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> <%- tipo %> <strong><%- codigo_promocion %></strong>?</p>
	</script>

	{{-- Template modal refused promotion --}}
	<script type="text/template" id="comment-refused-promotion-tpl">
		<label>Describa el por qué se rechaza la promoción <span class="required">*</span></label>
		<textarea id="comment_refused_promotion" required="required" class="form-control" name="comment_refused_promotion" data-parsley-trigger="keyup"></textarea>
	</script>
@stop
