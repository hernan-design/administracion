{{-- views/mentor/index.blade.php --}}
@extends('mentor.mentor')

@section('mentor.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Mentoría <small>Lista</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="mentor-filter-content" class="well">
					{!! Form::open(array('route' => null,'id' => 'mentor-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-6 col-sm-4 col-xs-12">
								<div class="x_panel">
									<div class="row">
										<div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
											<div class="control-group">
												<div class="controls">
													<label>Correo electrónico mentor</label>
													<div class="input-prepend input-group">
														<span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_mentor_filter_data"><i class="fa fa-search"></i></span>
														<input type="email" name="correo_electronico" id="correo_electronico_mentor_filter_data" class="form-control correo_electronico_user" data-name="nombre_mentor_filter_data" data-role="mentor"/>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-7 col-xs-10">
											<label>Nombre mentor</label>
											<input type="text" name="nombre_mentor" id="nombre_mentor_filter_data" class="form-control" readonly>
										</div>
									</div>
								</div>
							</div>
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
						<div class="row">	
							<div class="col-md-3 col-sm-3 col-xs-12">
								<label>Tema</label>
								<select type="text" name="codigo_tipo_asesoria" id="codigo_tipo_asesoria" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($mentorThemes as $value)
										<option value="{{ $value->codigo_tipo_asesoria }}">{{ $value->nombre_tipo_asesoria }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($states as $value)
										<option value="{{ $value->codigo_estado }}">{{ ucfirst( strtolower( $value->nombre_estado ) ) }}</option>
									@endforeach
								</select>
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
					<table id="mentor-list-search-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title">Id</th>
							<th class="column-title">Mentor</th>
							<th class="column-title">Emprendedor</th>
							<th class="column-title">Proyecto</th>
							<th class="column-title">Fecha</th>
							<th class="column-title">Hora</th>
							<th class="column-title">Estado</th>
							<th class="column-title">Acciones</th>
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

	{{-- buttons templateButtonsRemoveOrganizer --}}
	<script type="text/template" id="button-StateMentoring-tpl">
		<% if(full.codigo_estado == 9 || full.codigo_estado == 16 ){ %>
		<button class="btn btn-info btn-sm ActivateMentoring" value="<%- full.codigo_asesoria %>">Activar mentoría</button>
		<% } %>
		
		<!--button class="btn btn-danger btn-sm RemoveOrganizer" value="<%- full.id %>">Desactivar mentoría</!--button>-->

	</script>

	{{-- confirms messages templates --}}
	<script type="text/template" id="confirm-content-confirm-activate-mentoring-tpl">
		<div>¿Estás seguro de <strong><%- action %></strong> la mentoría <strong><%- article_name %></strong>?</div>
		<!--<div>¿Estás seguro de <strong>activar la mentoría </strong>?</div>-->
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
@stop
