{{-- views/comunity/channel/create-edit.blade.php --}}
@extends('comunity.channel.channel')

@section('channel.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Canal</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a href="{{ route('comunidad.canal.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content tp_x_content">
				<div class="well">
					{!! Form::open(array('route' => null, 'id' => 'form-channel', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-xs-12">
								<label>Nombre <span class="required">*</span></label>
								<input type="text" name="nombre_canal" id="nombre_canal" class="form-control" value="{{ isset($channel) ? $channel->nombre_canal : '' }}" required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-xs-12">
								<label>Tipo</label>
								<select name="canal_abierto" id="canal_abierto" class="form-control" required>
									@foreach ($channelType as $key => $type)
										<option value="{{ $key }}" {{ isset($channel) ? $channel->canal_abierto == $key ? 'selected' : '' : '' }}>{{ $type }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-11 col-xs-9">
								<label>Categoría</label>
								<select name="codigo_categoria_canal" id="codigo_categoria_canal" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('comunidad.categoria.categories') }}" data-initial-value="{{ isset($channel) ? $channel->codigo_categoria_canal : '' }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
								</select>
							</div>
							<div class="col-md-1 col-sm-1 col-xs-2">
								<div class="round-button">
									<div class="round-button-circle">
										<a href="" title="Crear categoría" class="round-button">
											<i class="fa fa-plus"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-xs-12">
								<label>Descripción <span class="required">*</span></label>
								<textarea name="descripcion_canal" id="descripcion_canal" class="form-control" data-parsley-maxlength="200" value="{{ isset($channel) ? $channel->descripcion_canal : '' }}" required>{{ isset($channel) ? $channel->descripcion_canal : '' }}</textarea>
							</div>
						</div>
						<br>
						<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
							<button type="submit" class="btn btn-success btn-block">{{ (isset($channel)) ? 'Editar' : 'Guardar' }}</button>
						</div>
					{!! Form::close() !!}
				</div>
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					@if(isset($channel))
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                        <li role="users" class="active">
							<a href="#tab_users" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Usuarios</a>
                        </li>
                        <li role="professions">
							<a href="#tab_professions" id="profession-tab" role="tab" data-toggle="tab" aria-expanded="true">Profesiones</a>
                        </li>
                  	</ul>
					<div id="myTabContent" class="tab-content">
						<div role="tabpanel" class="tab-pane fade active in" id="tab_users" aria-labelledby="home-tab">
							<div class="x_content">
								<div id="channel-user-filter-content" class="well">
									{!! Form::open(array('route' => null,'id' => 'channel-user-filter', 'class' =>'form-horizontal')) !!}
										<div class="row">
											<div class="col-md-4 col-md-offset-2 col-xs-12">
												<label>Correo electrónico</label>
												<input type="text" name="correo_electronico" id="channel_user_correo_electronico" class="form-control">
											</div>
											<div class="col-md-4 col-xs-12">
												<label>Nombre</label>
												<input type="text" name="nombre_usuario" id="channel_user_nombre_usuario" class="form-control">
											</div>
										</div>
										<br>
										<div class="text-center">
											<button type="button" class="btn btn-primary btn-search"> Buscar</button>
											<button type="button" class="btn btn-default clean-filters"> Limpiar</button>
											<button type="button" class="btn btn-success btn-add"> Agregar</button>
										</div>
									{!! Form::close() !!}
								</div>
							</div>
							<div class="row">
								<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
									<div id="search-users-toggle">
										{{-- Render id=search-users-toggle-tpl --}}
									</div>
								</div>
							</div>
							<div id="wrapper-render-users">
								<div class="row">
									<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
										<!-- start userchannel -->
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
											<ul id="list-content-article" class="list-unstyled msg_list browse-list-userchannel">
												{{-- Render userchannel list --}}
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
										<!-- end userchannel -->
									</div>
								</div>
                        	</div>
                        </div>
						<div role="tabpanel" class="tab-pane fade" id="tab_professions" aria-labelledby="profession-tab">
							<div class="x_content">
								<div id="channel-profession-filter-content" class="well">
									{!! Form::open(array('route' => null,'id' => 'channel-profession-filter', 'class' =>'form-horizontal')) !!}
									<div class="row">
										<div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
											<label>Nombre</label>
											<input type="text" name="nombnre_profesion_persona" id="nombnre_profesion_persona" class="form-control">
										</div>
									</div>
									<br>
									<div class="text-center">
										<button type="button" class="btn btn-primary btn-search"> Buscar</button>
										<button type="button" class="btn btn-default clean-filters"> Limpiar</button>
									</div>
									{!! Form::close() !!}
								</div>
							</div>
							<div class="row">
								<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
									<button type="button" class="btn btn-success btn-add-profession"> Agregar</button>
									<div id="search-professions-toggle">
										{{-- Render id=search-professions-toggle-tpl --}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
									<!-- start professionschannel -->
									<ul class="inline-list-grid">
										<li>
											<div class="wrap-result-count-professions">
												{{-- Render browse result count template --}}
											</div>
										</li>
										<li>
											<div class="wrap-pagination-professions">
												{{-- Render Pagination--}}
											</div>
										</li>
									</ul>
									<div id="wrapper-assignment-profession" class="table-responsive">
										<table id="profession-list-search-table" class="table table-striped table-bordered" data-page-length="15">
											<thead>
												<tr class="headings">
													<th class="column-title">Nombre</th>
													<th class="column-title text-center">Acciones</th>
												</tr>
											</thead>
											<tbody >
												<!-- Render view id="list-assignment-profession-tpl" -->
											</tbody>
										</table>
										@include('public.noresults', ['message' => 'No hay resultados.'])
									</div>
									<ul class="inline-list-grid">
										<li>
											<div class="wrap-result-count-professions">
												{{-- Render browse result count template --}}
											</div>
										</li>
										<li>
											<div class="wrap-pagination-professions">
												{{-- Render Pagination--}}
											</div>
										</li>
									</ul>
									<!-- end professionschannel -->
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
		<% if(typeof(codigo_usuario) !== "undefined"){ %>
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-9">
					<div class="tp_content-list">
						<a href="<%= window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_usuario }) ) %>" class="hover-list">
						  	<span class="image">
								<img src="<%= imagen_usuario %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
						  	</span>
						  	<span>
								<span>
									<strong class="text-capitalize"><%- user_name.toLowerCase() %></strong>
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
							<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-ellipsis-v"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu">
								<li>
									<a href="#" title="Eliminar usuario" class="danger" data-type="delete" ><i class="fa fa-trash"></i> Eliminar</a>
									<a href="#" title="<%- codigo_estado === window._tp.getState['inactive'] ? 'Activar usuario' : 'Inactivar usuario' %>" class="activate" data-type="activation" data-message="<%- codigo_estado === window._tp.getState['inactive'] ? 'activate' : 'inactivate' %>" ><i class="fa fa-power-off"></i> <%- codigo_estado === window._tp.getState['inactive'] ? 'Activar' : 'Inactivar' %> </a>
								</li>
							</ul>
						</div>
						<% if(!_.isNull(nombre_ciudad)){ %>
							<div class="time tp_hide-information">
								<i class="fa fa-map-marker"></i> <%- nombre_ciudad %>
							</div>
						<% } %>
					</div>
				</div>
			</div>
		<% } %>
	</script>

	{{-- Template section option channel --}}
	<script type="text/template" id="form-modal-tpl">
		<label>Nombre <span class="required">*</span></label>
		<input type="text" name="nombre_categoria_canal" id="nombre_categoria_canal" class="form-control" required>
	</script>

	{{-- Template section option channel --}}
	<script type="text/template" id="confirm-channel-profession-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la profesión <strong><%- profession_name %></strong> de este canal?</p>
	</script>

	{{-- Template search users toggle --}}
	<script type="text/template" id="search-users-toggle-tpl">
		<div class="x_content">
			<div id="users-search-toggle" class="well">
				{!! Form::open(array('route' => null, 'id' => 'form-user-channel', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
						<div class="control-group">
					  		<div class="controls">
								<label>Correo electrónico <span class="required">*</span></label>
								<div class="input-prepend input-group">
						  			<span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_filter_data"><i class="fa fa-search"></i></span>
						  			<input type="email" name="correo_electronico" id="correo_electronico_filter_data" class="form-control correo_electronico_user" data-name="nombre_usuario_filter_data"/>
								</div>
					  		</div>
						</div>
					</div>
					<div class="col-md-7 col-sm-7 col-xs-10">
						<label>Nombre</label>
						<input type="text" name="nombre_usuario" id="nombre_usuario_filter_data" class="form-control" readonly>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
						<button type="submit" class="btn btn-success btn-sm" data-field="correo_electronico_filter_data" title="Agregar usuario"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</script>

	{{-- Template search professions toggle --}}
	<script type="text/template" id="search-professions-toggle-tpl">
		<div class="x_content">
			<div id="professions-search-toggle" class="well">
				{!! Form::open(array('route' => null, 'id' => 'form-profession-channel', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
				<div class="row">
					<div class="row">
						<div class="col-md-4 col-md-offset-4 col-sm-11 col-xs-9">
							<label>Profesión</label>
							<select name="codigo_recurso" id="codigo_recurso" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('usuario.profesion.index')) %>" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
						</div>
						<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
							<button type="submit" class="btn btn-default btn-sm" data-field="" title="Agregar profesión"><i class="fa fa-plus blue"></i></button>
						</div>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</script>

	{{-- template list profession assignment to channel --}}
	<script type="text/template" id="list-assignment-profession-tpl">
		<td>
			<%- nombnre_profesion_persona %>
		</td>
		<td class="text-center">
			<div class="btn-group tp_btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
					<i class="fa fa-ellipsis-v"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu">
					<li>
						<a href="#" title="Eliminar profesión" data-action="eliminar" data-id="<%- codigo_vinculo_canal %>" data-name="<%- nombnre_profesion_persona %>" data-state="<%- codigo_estado %>" class="danger"><i class="fa fa-trash"></i> Eliminar</a>
					</li>
				</ul>
			</div>
		</td>
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
