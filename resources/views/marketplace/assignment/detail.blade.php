{{-- views/marketplace/assignment/detail.blade.php --}}
@extends('marketplace.assignment.assignment')

@section('assignment.detail.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Asignación de integrantes <small>({{ $register->nombre_fase }})</small></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a href="{{ route('marketplace.asignaciones.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			{{-- {{ dd($register) }} --}}
			<div class="x_content">
				{{-- <h1>Detalle de la asignación {{ $register->codigo_registro_fase_usuario }}</h1> --}}
				<section id="marketplace-assignment-info" class="panel">
					<div class="well">
						<h3><strong>{{ $register->nombre_usuario }}</strong></h3>
						<dl class="">
							<dt>Usuario</dt>
							<dd>
								<p>{{ $register->nombre_usuario_creador }}</p>
							</dd>
							<dt>Fecha registro</dt>
							<dd>
								<p>{{ $register->fecha_registro_convocatoria }}</p>
							</dd>
						</dl>

						{!! Form::open(array('route' => null, 'id' => 'form-assignment-marketplace-member', 'data-parsley-validate' => '')) !!}
							<div class="row">
								<div class="col-md-4 col-xs-12 item form-group">
									<label>Correo <span class="required">*</span></label>
									<input type="email" required="required" class="form-control" placeholder="Email" id="email" name="email">
								</div>
							</div>
							<div class="ln_solid"></div>
							<div class="row">
								<div class="col-md-2 col-md-offset-5col-md-2 col-md-offset-5">
									{!! Form::submit('Agregar', array('class' => 'btn btn-success btn-block')); !!}
								</div>
							</div>
						{!! Form::close() !!}
					</div>

					<div id="wrapper-assignment-members" class="table-responsive">
						<table id="data-marketplace-assignments-members" class="table table-striped jambo_table bulk_action">
							<thead>
								<tr>
									<th colspan="4" class="text-center">Integrantes asignados</th>
								</tr>
								<tr>
									<th>Integrante</th>
									<th>Fecha asignación</th>
									<th>Estado</th>
									<th>Eliminar</th>
								</tr>
							</thead>
							<tbody>
								{{-- Render template assignment member - id:marketplace-assignment-member-tpl --}}
							</tbody>
						</table>
						@include('public.noresults', ['message' => 'No hay integrantes asignados'])
					</div>
				</section>
			</div>
		</div>
	</div>

	{{-- template marketplace assignment member --}}
	<script type="text/template" id="marketplace-assignment-member-tpl">
		<td>
			<%- nombre_usuario %></br>
			<%- correo_electronico %>
		</td>
		<td><%- window.moment(fecha_asignacion_mp).format('YYYY/MM/DD') %></td>
		<td><%- nombre_estado %></td>
		<td>
			<a href="#" class="btn btn-default btn-xs remove-member" title="Quitar integrante"><i class="fa fa-trash-o"></i></a>
		</td>
	</script>

	{{-- template confirm delete member --}}
	<script type="text/template" id="msg-confirm-remove-member-tpl">
		<p>¿Desea quitar al integrante <strong><%- nombre_usuario || correo_electronico %></strong> de la asignación?</p>
	</script>
@stop
