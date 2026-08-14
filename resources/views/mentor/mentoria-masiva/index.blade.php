{{-- views/mentor/index.blade.php --}}
@extends('mentor.mentor')

@section('mentor.content')
	<div class="col-xs-12" id="mentoria-masiva">
		<div class="x_panel">
			<div class="x_title">
				<h2>Mentorías masivas</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content ">
				<h4>1. Selecciona mentor</h4>
				<div id="users-search-toggle" class="well">
					{!! Form::open(array('route' => null, 'id' => 'form-user', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
							<div class="col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
								<div class="control-group">
									<div class="controls">
										<label>Correo electrónico mentor</label>
										<div class="input-prepend input-group">
											<span
												class="add-on input-group-addon tp_input-group-addon"
												data-title="Buscador de usuarios"
												data-field="correo_electronico_mentor_filter_data">
												<i class="fa fa-search"></i>
											</span>
											<input
												type="email"
												name="correo_electronico"
												id="correo_electronico_mentor_filter_data"
												class="form-control correo_electronico_user"
												data-name="nombre_mentor_filter_data"
												data-role="mentor"
											/>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12">
								<label>Nombre mentor</label>
								<input
									type="text"
									name="nombre_mentor"
									id="nombre_mentor_filter_data"
									class="form-control" readonly
								/>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-10">
								<label>Tema</label>
								<select
									name="codigo_tipo_asesoria"
									id="codigo_tipo_asesoria"
									class="form-control"
									required>
									@foreach ($mentorThemes as $type)
										@if ($loop->first)
											<option selected value="{{ $type->codigo_tipo_asesoria }}">{{ $type->nombre_tipo_asesoria }}</option>
										@endif
										<option value="{{ $type->codigo_tipo_asesoria }}">{{ $type->nombre_tipo_asesoria }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
								<button
									type="submit"
									class="btn btn-success btn-sm"
									data-field="correo_electronico_filter_data"
									title="Agregar usuario"><i class="fa fa-plus"></i>
								</button>
							</div>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
			<div id="wrapper-render-users">
				<div class="row">
					<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
						<!-- start user -->
						<div id="avaluator-list">
							{{-- @include('public.noresults', ['message' => 'No hay resultados.']) --}}
							<ul id="list-mentor" class="list-unstyled msg_list browse-list-user">
								{{-- Render user-evaluator-list-tpl --}}
							</ul>
						</div>
						<!-- end user -->
					</div>
				</div>
			</div>
			<div class="x_content">
				<h4>2. Selecciona entidades:</h4>
				<div class="well">
					{!! Form::open(array('route' => null, 'id' => 'form-projects-filter', 'class' =>'')) !!}
					<div class="row">
		                <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				         	<label>Aplicante</label>
				            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
				        </div>
						<div class="col-md-4 col-sm-12 col-xs-12 form-group">
							<label>Fase</label>
							<select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
									<option value="1">General</option>
									{{-- <option value="345">Registro</option> --}}
									{{-- <option value="346">Registro</option> --}}
									<option value="350">Ideación </option>
									<option value="351">Preincubación</option>
									<option value="352">Incubación</option>
									<option value="353">Aceleración</option>
									<option value="354">Consolidación</option>
							</select>
						 </div>
				    </div>
					<div class="text-center">
						<button type="button" class="btn btn-primary btn-search"> Buscar</button>
						<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
					</div>
					{!! Form::close() !!}

					<br/>
					{{-- Lista de proyectos para procesos de mentoria --}}
					<div class="table-responsive">
						<table id="projects-list-table" class="table table-striped table-bordered" data-page-length="15">
							<thead>
							<tr class="headings">
								<th>Aplicante</th>
								<th>Usuario</th>
								<th>Ciudad</th>
								<th>Registro</th>
								<th>Fase</th>
								<th>Estado</th>
								<th class="column-title"></th>
							</tr>
							</thead>
							<tbody >
							<!-- Render view  -->
							</tbody>
						</table>
					</div>
				</div>
				<br/>

			</div>
			<div class="x_content ">
				<h4>3. Confirma entidades:</h4>
				<div id="confirm-entities" class="well">
					{{-- {!! Form::open(array('route' => null, 'id' => 'form-user', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!} --}}
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="item-entity"></div>
						</div>
						<div class="text-center col-md-12 col-sm-12 col-xs-12  tp_align-btn">
							<button
								type="submit"
								class="btn btn-success btn-sm"
								id="add_mentorships"
								data-field="correo_electronico_filter_data"
								title="Registrar mentorias">
								<i class="fa fa-plus"></i>
								Registrar mentorias
							</button>
						</div>
					</div>
					{{-- {!! Form::close() !!} --}}
				</div>
			</div>
			<div class="x_content ">
				<div class="col-md-12 col-sm-12 col-xs-12 text-center well">
					<p>Lista de mentorias masivas</p>
					<a
						class="btn btn-success data-export-sessions"
						href="{{ route('mentorias.masivas.lista') }}"
						type="button">
						Ir la lista
					</a>
				</div>
			</div>
		</div>
	</div>


	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
				<input
					class="form-control checkbox_select_entity"
					type="checkbox"
					id="<%- row.row %>"
					value="<%- JSON.stringify(full) %>"
				/>
		</div>
	</script>

	{{-- template evaluator list --}}
	<script type="text/template" id="mentor-assignment-massive-list-tpl">
		<% if(typeof(codigo_usuario) !== "undefined"){ %>
			<li>
				<div class="row">
					<div class="col-md-10 col-sm-10 col-xs-9">
						<div class="tp_content-list">
							<a
								href="<%= window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_usuario }) ) %>"
								class="hover-list"
							>
								<span class="image">
									<img
										src="<%= imagen_usuario %>"
										onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')"
										alt="img"
									/>
								</span>
								<span>
									<span>
										<strong class="text-capitalize"><%- nombre_usuario.toLowerCase() %></strong>
									</span>
									<span class="message">
										<i class="fa fa-at"></i> <%- correo_electronico %><br>
										<div class="tp_hide-information">
											<i class="fa fa-user"></i> <%- nickname_usuario %>
										</div>
									</span>
								</span>
							</a>
						</div>
					</div>
					<div class="col-md-2 col-sm-2 col-xs-3">
						<div class="tp_button-group pull-right text-right">
							<div class="btn-group tp_btn-group">
								<button
									type="button"
									class="btn btn-default dropdown-toggle"
									title="Opciones"
									data-toggle="dropdown"
									aria-expanded="false"
								>
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<li>
										<a
											title="Eliminar usuario"
											data-action="eliminar"
											data-email="<%- correo_electronico %>"
											data-name="<%- nombre_usuario %>"
											class="danger"
											id="delete-mentor">
											<i class="fa fa-trash"></i>
											Eliminar
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</li>
		<% } %>
	</script>

	{{-- template item-entity --}}
	<script type="text/template" id="item-entity-tpl">
		<% _.each(obj, function (option) { %>
			{{-- <option value="<%- option.id %>" <%- option.comparator ? 'selected' : '' %> ><%- option.title %></option> --}}
			{{-- <span class="badge"><%- option.nombre_usuario %> - <%- option.nombre_founder %></span> --}}
			<span class="badge rounded-pill text-bg-success position-relative">
				<%- option.nombre_entidad %> - <%- option.nombre_usuario %>
				<button
				  type="button"
				  class="position-absolute top-0 start-100 translate-middle p-1 badge rounded-pill bg-danger border border-light rounded-circle close-button"
				  id="<%- option.row %>"
				>
				  &#x2718;
				</button>
			  </span>
		<% }); %>
	</script>

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
