{{-- views/notification/output/index.blade.php --}}
@extends('notification.output.output')

@section('output.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Notificación <small>Lista</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="notification-filter-content" class="well">
					{!! Form::open(array('route' => null,'id' => 'notification-output-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-2 col-sm-4 col-xs-12">
								<label>Tipo</label>
								<select type="text" name="tipo_notificacion" id="tipo_notificacion" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($types as $key => $type)
										<option value="{{ $key }}">{{ $type }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<label>Fecha</label>
								<input type="text" name="fecha_notificacion" id="fecha_notificacion" class="daterange form-control"/>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
							<a href="{{ route('notificacion.output.create') }}" class="btn btn-success btn-create">Crear</a>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>
				<div class="table-responsive">
					<table id="notification-list-search-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title">Nombre</th>
							<th class="column-title">Descripción</th>
							<th class="column-title text-center">Acciones</th>
						</tr>
						</thead>
						<tbody >
						<!-- Render view  -->
						</tbody>
					</table>
				</div>
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
				<li><a href="#" class="btn-active success" data-action="enviar" data-id="<%- full.codigo_notificacion %>" data-name="<%- full.titulo_notificacion %>" data-state=""><i class="fa fa-bell"></i> Notificar</a>
				</li>
				<li><a href="#" class="btn-inactive danger" data-action="eliminar" data-id="<%- full.codigo_notificacion %>" data-name="<%- full.titulo_notificacion %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-trash"></i> Eliminar</a>
				</li>
			</ul>
		</div>
	</script>

	<script type="text/template" id="confirm-notification-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la notificación <strong><%- noti_name %></strong>?</p>
	</script>
@stop
