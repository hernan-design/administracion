@extends('project.project')

@section('projects.content')
    {{-- ------------------ Project projects Templates JS -------------------- --}}
    {{-- template project projects aside filter --}}
    <script type="text/template" id="projects-list-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Proyectos <small>Lista</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="well">
						{!! Form::open(array('route' => null, 'id' => 'form-projects-filter', 'class' =>'')) !!}
						<div class="row">
							<div class="col-md-4 col-sm-12 col-xs-12 form-group">
								<label>Número identificación</label>
								<input type="text" name="numero_identificacion" id="numero_identificacion" class="form-control">
							</div>
							<div class="col-md-4 col-sm-12 col-xs-12 form-group">
								<label>Nombre</label>
								<input type="text" name="nombre_proyecto" id="nombre_proyecto" class="form-control">
							</div>

							<div class="col-md-4 col-sm-12 col-xs-12 form-group">
								<label>Etapa</label>
								<select name="codigo_etapa_proyecto" id="codigo_etapa_proyecto" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('entidad.etapa.index') }}" data-param="true" data-placeholder="Seleccione..."  placeholder="Seleccione..." style="width: 100%">
								</select>
							</div>

							<div class="col-md-4 col-sm-12 col-xs-12 form-group">
								<label>Categoría</label>
								<select name="codigo_categoria_proyecto" id="codigo_categoria_proyecto" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('entidad.industria.index') }}" data-param="true" data-placeholder="Seleccione..."  placeholder="Seleccione..." style="width: 100%">
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-4 col-xs-12">
								<div class="x_panel">
									<div class="row">
										<div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
											<div class="control-group">
												<div class="controls">
													<label>Correo electrónico emprendedor</label>
													<div class="input-prepend input-group">
														<span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_emprendedor_filter_data"><i class="fa fa-search"></i></span>
														<input type="email" name="correo_electronico" id="correo_electronico_emprendedor_filter_data" class="form-control correo_electronico_user" data-name="nombre_emprendedor_filter_data"/>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-7 col-xs-10">
											<label>Nombre emprendedor</label>
											<input type="text" name="nombre_emprendedor" id="nombre_emprendedor_filter_data" class="form-control" readonly>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
							<a href="{{ route('entidad.create') }}" class="btn btn-success btn-create">Crear</a>
							<button type="button" class="btn btn-info btn-export">Exportar</button>
							{{-- @if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/usuario']) ) --}}
							@if ( session('institution')->codigo_institucion == 30)
								<a
									href="#upload-documents-container"
									type="button"
									class="btn btn-warning data-import"
									title="Importar archivo para cargue usuarios"
									>
									Importar
								</a>
							@endif
							{{-- @endif --}}
						</div>
						{!! Form::close() !!}
					</div>
					<br/>
                    <div class="table-responsive">
                        <table id="projects-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Número de documento</th>
                                <th class="column-title">Nombre</th>
                                <th class="column-title">Creador</th>
								<th class="column-title">Etapa</th>
                                <th class="column-title">Categoría</th>
                                <th class="column-title"></th>
                            </tr>
                            </thead>
                            <tbody >
                            <!-- Render view  -->
                            </tbody>
                        </table>
                    </div>
                </div>
				<div class="x_content">
					<div class="row">
						<div class="small-12 col-md-12 columns">
								<div id="upload-documents-container" class="editable-item">
									<input type="hidden" id="codigo_usuario" value="{{ \Auth::user()->codigo_usuario }}">
									<section id="wrap-documents" class="">
										{{-- Render  Documents --}}
									</section>
								</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </script>

	<script type="text/template" id="confirm-projects-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la etapa <strong><%- project_name %></strong>?</p>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['active']) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
				<li>
					<a href="<%= window.Misc.urlFull(Route.route('entidad.edit', {entidad: full.nickname_usuario}) ) %>" title="Editar entidad"><i class="fa fa-pencil"></i> Editar
					</a>
					</a>
				</li>
			</ul>
		</div>
	</script>

	{{-- confirms messages templates --}}
	<script type="text/template" id="confirm-project-tpl">
		<div>¿Estás seguro de <strong><%- action %></strong> la entidad <strong><%- project_name %></strong>?</div>
	</script>

	{{-- confirms messages templates --}}
	<script type="text/template" id="confirm-export-data-tpl">
		<div>¿Estás seguro de exportar reporte de <strong>entidad</strong>?</div>
	</script>

	{{-- Template section option channel users --}}
	<script type="text/template" id="form-users-modal-tpl">
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Correo electrónico</label>
				<input type="text" name="correo_electronico_datatable" id="correo_electronico_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nombre</label>
				<input type="text" name="nombre_usuario_datatable" id="nombre_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nickname</label>
				<input type="text" name="nickname_usuario_datatable" id="nickname_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Identificación usuario</label>
				<input type="number" name="cedula_usuario_datatable" id="cedula_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Ciudad</label>
				<select name="codigo_ciudad_datatable" id="codigo_ciudad_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
				</select>
			</div>
		</div>
		<br>
		<div class="text-center">
			<button type="button" class="btn btn-primary btn-search-user"> Buscar</button>
			<button type="button" class="btn btn-default btn-clear-user"> Limpiar</button>
		</div>
		<br>
		<div class="table-responsive">
			<table id="user-list-search-table" class="table table-striped table-bordered" data-page-length="15">
				<thead>
				<tr class="headings">
					<th class="column-title">Nombre</th>
					<th class="column-title">Nick Name</th>
					<th class="column-title">Correo</th>
					<th class="column-title">Ciudad</th>
				</tr>
				</thead>
				<tbody >
				<!-- Render view  -->
				</tbody>
			</table>
		</div>
	</script>

	@switch(session('institution')->codigo_institucion)
		@case(30)
			<script type="text/template" id="upload-entities-documents-form-tpl">
				{!! Form::open(array('data-abide' => '', 'id' => 'form-upload-entities-documents')) !!}
					<div class="row" style="background = #eeeeee">
						<div class="col-xs-12 col-sm-4 col-md-12  text-center">
							<h5>Archivos adjuntos</h5>
							<p>
								<a
									href="https://s3.us-west-2.amazonaws.com/static.tuproyecto.com/file/users_entities_compensar+(1).xlsx"
									type="button"
									title="Descargar Plantilla"
									>
									<strong>Paso: 1. Decarga la plantilla.</strong> <i class="fa fa-cloud-download" aria-hidden="true"></i>
								</a>
							</p>
							<p>
								<strong>Paso: 2. En esta sección debes adjuntar el excel para cargar usuarios y entidades.</strong>
							</p>
						</div>
					</div>
					{{-- <input type="hidden" id="name_project" name="name_project" value="{{ $entityBuild->nombre_usuario }}"> --}}
					<div class="row">
						<div class="col-xs-12 col-sm-4 col-md-12 columns">
							<div id="fnuploader-attach-file" class="uploader-attachments"></div>
						</div>
					</div>
					<div class="row save-section" style="justify-content: center;margin-top: 5px;">
						<div>
						{!! Form::submit('Cargar archivos', array('class' => 'btn btn-success btn-block sky-blue btn-shadow radius')) !!}
						</div>
					</div>
				{!! Form::close() !!}
			</script>
		@break
	@endswitch
@stop
