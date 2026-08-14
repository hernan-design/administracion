{{-- views/forum/index.blade.php --}}
@extends('forum.forum')

@section('forum.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Foro<small>Lista</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="forum-filter-content" class="well">
					{!! Form::open(array('route' => null,'id' => 'forum-filter', 'class' =>'form-horizontal')) !!}
						<div class="row" id="users-search-toggle">
							<div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="x_panel">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
											<div class="control-group">
												<div class="controls">
													<label>Correo Usuario</label>
													<div class="input-prepend input-group">
														<span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_usuario_filter_data"><i class="fa fa-search"></i></span>
														<input type="email" name="correo_electronico" id="correo_electronico_usuario_filter_data" class="form-control correo_electronico_user" data-name="nombre_usuario_filter_data" data-role="user"/>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
                                            <label>Usuario</label>
											<input type="text" name="nombre_usuario" id="nombre_usuario_filter_data" class="form-control" readonly>
										</div>
									</div>
								</div>
                            </div> 
                            <div class="col-md-3 col-sm-3 col-xs-12">
								<label>Categoría</label>
								<select type="text" name="codigo_categoria_foro" id="codigo_categoria_filter_data" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($forumCategories as $value)
										<option value="{{ $value->id}}">{{ $value->text }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado_filter_data" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($states as $value)
										<option value="{{ $value->codigo_estado }}">{{ ucfirst( strtolower( $value->nombre_estado ) ) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row">	
							<div class="col-md-2 col-sm-4 col-xs-12">
								<label>Inicio Fecha</label>
								<input type="text" name="fecha_inicio_foro" id="fecha_inicio_foro_filter_data" class="form-control dateDefault"  title="filtrar por fechas posteriores a" data-inputmask="'mask':'9999-99-99'" value="">
							</div>
							<div class="col-md-2 col-sm-4 col-xs-12">
								<label>Fin Fecha</label>
								<input type="text" name="fecha_fin_foro" id="fecha_fin_foro_filter_data" class="form-control dateDefault"  title="filtrar por fechas anteriores a" data-inputmask="'mask':'9999-99-99'" value="">
							</div>
						</div>
						
						<br>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>
				<div class="table-responsive">
					<table id="forum-list-search-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title">Titulo del Foro</th>
							<th class="column-title">Categoría del Foro</th>
                            <th class="column-title">Usuario</th>
                            <th class="column-title">Correo Usuario</th>
							<th class="column-title">Fecha Apertura</th>
                            <th class="column-title">Hora Apertura</th>
							<th class="column-title">Estado</th>
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
			<% if(full.codigo_estado != window._tp.getState['removed'] ) { %>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_foro %>" data-name="<%- full.titulo_foro %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-on"></i> Activar</a>
					</li>
				<% } %>
				<% if(full.codigo_estado == window._tp.getState['active'] ) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_foro %>" data-name="<%- full.titulo_foro %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
			<% } %>
			</ul>
		</div>
	</script>
@stop
