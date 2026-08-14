{{-- views/program/create-edit.blade.php --}}
@extends('program.program')

@section('program.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="x_title">
					<h2>Rol</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a href="{{route('rol.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-role', 'data-parsley-validate'=>'')) !!}
					<div class="row">
						<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
							<label>Nombre <span class="required">*</span></label>
							<input type="text" name="title" id="title" class="form-control" value="{{ isset($role) ? $role->title : '' }}" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
							<label>Descripción<span class="required">*</span></label>
							<textarea type="text" name="description" id="description" class="form-control" data-parsley-maxlength="500" value="{{ isset($role) ? $role->description : '' }}" required>{{ isset($role) ? $role->description : '' }}</textarea>
						</div>
					</div>

					@if(!isset($role))
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Tipo <span class="required">*</span></label>
								<select type="text" name="tipo_rol" id="tipo_rol" class="form-control" required>
									<option value="">Seleccione</option>
									<option value="platform">Plataforma</option>
									<option value="administrator">Administrador</option>
								</select>
							</div>
						</div>
					@endif
					<br>
					<div class="row">
						<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
							<button type="submit" class="btn btn-success btn-block">{{ isset($role) ? 'Editar' : 'Guardar' }}</button>
						</div>
					</div>
					{!! Form::close() !!}

					<div class="content-btn-view-more">
						<a href='' class='activity-view-more'>Mostrar historial de actividad</a>
					</div>

					<!-- start activities log -->
					<div id="activities-log">
						@include('public.noresults', ['message' => 'No hay resultados.'])
						{{-- Render template id="activities-log-tpl" --}}
					</div>
					<!-- end activities log -->
				</div>
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					@if(isset($role))
	                  	<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
							@if(!$admin)
								@if(in_array($role->tipo_rol, [App\Models\Seguridad\Rol::getTypeRole('administrator')]))
									<li role="permissions_administrator" @if(!$admin) class="active" @endif><a href="#tab_permissions_administrator" role="tab" id="permissions-administrator-tab" data-toggle="tab" aria-expanded="false">Permisos administrador</a>
									</li>
								@endif
								<li role="permissions_platform" @if(in_array($role->tipo_rol, [App\Models\Seguridad\Rol::getTypeRole('platform')])) class="active" @endif><a href="#tab_permissions_platform" role="tab" id="permissions-platform-tab" data-toggle="tab" aria-expanded="false">Permisos plataforma</a>
								</li>
							@endif
	                        <li role="users" @if($admin) class="active" @endif><a href="#tab_users" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Usuarios</a>
	                        </li>
	                  	</ul>

	                    <div id="myTabContent" class="tab-content">
							<div role="tabpanel" class="tab-pane fade @if(!$admin && in_array($role->tipo_rol, [App\Models\Seguridad\Rol::getTypeRole('administrator')])) active in @endif" id="tab_permissions_administrator" aria-labelledby="permissions-administrator-tab">
								<!-- start user roles administrator -->
								<div id="multiple-select-administrator">

								</div>
								<!-- end user roles administrator -->
							</div>
							<div role="tabpanel" class="tab-pane fade @if(in_array($role->tipo_rol, [App\Models\Seguridad\Rol::getTypeRole('platform')])) active in @endif" id="tab_permissions_platform" aria-labelledby="permissions-platform-tab">
								<!-- start user roles platform -->
								<div id="multiple-select-platform">

								</div>
								<!-- end user roles platform -->
							</div>
	                        <div role="tabpanel" class="tab-pane fade @if($admin) active in @endif" id="tab_users" aria-labelledby="home-tab">
								<div id="wrapper-render-users">
									<div class="row">
										<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
											<!-- start usersrol -->
											<ul class="inline-list-grid">
												<li>
													<div class="wrap-result-count">
														{{-- Render browse result count template --}}
													</div>
												</li>
												<li>
													<div class="wrap-pagination">
														{{-- Render Pagination--}}
													</div>
												</li>
											</ul>
											<div id="article-list">
												@include('public.noresults', ['message' => 'No hay resultados.'])
												<ul id="list-content-article" class="list-unstyled msg_list browse-list-usersrol">
													{{-- Render usersrol list --}}
												</ul>
							                </div>
											<ul class="inline-list-grid">
												<li>
													<div class="wrap-result-count">
														{{-- Render browse result count template --}}
													</div>
												</li>
												<li>
													<div class="wrap-pagination">
														{{-- Render Pagination--}}
													</div>
												</li>
											</ul>
											<!-- end usersrol -->
										</div>
									</div>
	                        	</div>
	                        </div>
	                  	</div>
					@endif
                </div>
			</div>
		</div>
	</div>

	<script type="text/template" id="user-role-list-tpl">
		<a href="<%- window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_usuario }) ) %>" class="hover-list">
		  	<span class="image">
				<img src="<%= imagen_usuario %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
		  	</span>
		  	<span>
				<span>
					<strong class="text-capitalize"><%- user_name.toLowerCase() %></strong>
				</span>
				<span class="time tp_hide-information">
					<i class="fa fa-map-marker"></i> <%- nombre_ciudad %>
				</span>
		  	</span>
		  	<span class="message">
				<i class="fa fa-at"></i> <%- correo_electronico %><br>
				<div class="tp_hide-information">
					<i class="fa fa-user"></i> <%- nickname_usuario %>
		  		</div>
		  	</span>
		</a>
	</script>

	{{-- Template multi-select-administrator --}}
	<script type="text/template" id="multiselect-administrator-tpl">
		<select multiple="multiple" class="my-select selectableOptgroup" id="my-select" name="my-select[]" data-route="rol.permiso" data-params='{"permiso":0}' data-id="<%- role %>">
			<% _.each(_.groupBy(permissionsAdministrator, 'nombre_modulo'), function (value, index ) { %>
				<optgroup label='<%- index.toUpperCase() %>'>
					<% _.each(_.sortBy(value, 'permission_id'), function (option) { %>
						<option value='<%- option.permission_id+'_'+option.codigo_modulo %>' <%- option.comparator ? 'selected' : '' %> ><%- option.display_name %></option>
					<% }); %>
				</optgroup>
			<% }); %>
		</select>
	</script>

	{{-- Template multi-select-platform --}}
	<script type="text/template" id="multiselect-platform-tpl">
		<select multiple="multiple" class="my-select selectableOptgroup" id="my-select-platform" name="my-select[]" data-route="rol.permiso" data-params='{"permiso":0}' data-id="<%- role %>">
			<% _.each(_.groupBy(permissionsPlatform, 'nombre_modulo'), function (value, index ) { %>
				<optgroup label='<%- index.toUpperCase() %>'>
					<% _.each(_.sortBy(value, 'permission_id'), function (option) { %>
						<option value='<%- option.permission_id+'_'+option.codigo_modulo %>' <%- option.comparator ? 'selected' : '' %> ><%- option.display_name %></option>
					<% }); %>
				</optgroup>
			<% }); %>
		</select>
	</script>
@stop
