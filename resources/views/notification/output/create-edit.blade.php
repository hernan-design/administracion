{{-- views/notification/output/create-edit.blade.php --}}
@extends('notification.output.output')

@section('output.content')
	<div class="col-xs-12">
		<div class="x_panel content-notification">
			<div class="x_title">
				<h2>Notificación</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a href="{{route('notificacion.output.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content section_content">
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-output-notifications', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Tipo <span class="required">*</span></label>
								<select type="text" name="tipo_notificacion" id="tipo_notificacion" class="form-control">
									@foreach ($types as $key => $type)
										<option value="{{ $key }}" {{ App\Models\Core\Notificacion::getTypeNotification('text') == $key ? 'selected' : '' }} >{{ $type }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Método de notificación <span class="required">*</span></label>
								<p>
									<div class="checkbox-inline">
										<label class="checkbox-inline">
											<input type="checkbox" name="envio_email" class="flat" checked> <strong>Correo electrónico</strong>
										</label>
									</div>
									<div class="checkbox-inline">
										<label class="checkbox-inline">
											<input type="checkbox" name="envio_plataforma" class="flat"> <strong>Plataforma</strong>
										</label>
									</div>
								</p>	
							</div>
						</div>
						<br>
						<div class="row">						
							<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
								<div id="type-notification">
									{{-- Render type notification --}}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Destinatarios <span class="required">*</span></label>
								<select type="text" name="destinatarios_notificacion" id="destinatarios_notificacion" class="form-control">
									@foreach ($addressees as $key => $type)
										<option value="{{ $key }}" {{ App\Models\Core\Notificacion::getAddresseeNotification('global') == $key ? 'selected' : '' }} >{{ $type }}</option>
									@endforeach
								</select>
							</div>	
						</div>
						<br>
						<div class="row">
							<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
								<div id="addressee-notification">
									{{-- Render template id="addressee-notification-tpl" --}}
								</div>	
							</div>	
						</div>	
						<br>
						<div class="row">
							<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
								<div id="section-channel-content">
									{{-- Tempalete id=multiselect-channel-content-tpl --}}
								</div>
							</div>	
						</div>	
						<br>
						<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
							<button type="submit" class="btn btn-success btn-block">Notificar</button>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>

	{{-- Type text notification ---}}
	<script type="text/template" id="type-text-notification-tpl">
		<div id="section-text-notification">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label for="titulo_notificacion">Título <span class="required">*</span></label>
					<input type="text" id="titulo_notificacion" class="form-control" name="titulo_notificacion" data-parsley-maxlength="80" required>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label for="texto_notificacion">Texto <span class="required">*</span></label>
					<textarea name="texto_notificacion" id="texto_notificacion" required="required" class="form-control" data-parsley-maxlength="180" rows="3" required></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label for="url_notificacion">Url <span class="required">*</span></label>
					<input type="text" id="url_notificacion" class="form-control" name="url_notificacion" required>
				</div>
			</div>
		</div>
	</script>

	{{-- Type module notification ---}}
	<script type="text/template" id="type-module-notification-tpl">
		<div id="section-module-notification">
			<div class="row">
				<div class="col-md-6 col-sm-12 col-xs-12">
					<label>Módulo <span class="required">*</span></label>
					<select type="text" name="codigo_modulo" id="codigo_modulo" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($modules as $value)
							<option value="{{ $value->codigo_modulo }}">{{ $value->nombre_modulo }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-6 col-sm-12 col-xs-12">
					<div id="module-resource-notification">

					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div id="notification-text">
						{{-- Render template id="notification-text-tpl" --}}
					</div>
				</div>
			</div>		
		</div>
	</script>

	<script type="text/template" id="notification-text-tpl">
		<label for="texto_notificacion">Texto <span class="required">*</span></label>
		
		<% if(module_code == window._tp.getModule['calendar']) { %>
			<textarea name="texto_notificacion" id="texto_notificacion" required="required" class="form-control" data-parsley-maxlength="180" rows="3" required>{!! transtp('text.calendar') !!}</textarea>
		<% } %>	

		<% if(module_code == window._tp.getModule['course']) { %>
			<textarea name="texto_notificacion" id="texto_notificacion" required="required" class="form-control" data-parsley-maxlength="180" rows="3" required>{!! transtp('text.course') !!}</textarea>
		<% } %>	

		<% if(module_code == window._tp.getModule['blog']) { %>
			<textarea name="texto_notificacion" id="texto_notificacion" required="required" class="form-control" data-parsley-maxlength="180" rows="3" required>{!! transtp('text.blog') !!}</textarea>
		<% } %>	

		<% if(module_code == window._tp.getModule['program']) { %>
			<textarea name="texto_notificacion" id="texto_notificacion" required="required" class="form-control" data-parsley-maxlength="180" rows="3" required>{!! transtp('text.program') !!}</textarea>
		<% } %>	
		
	</script>	

	{{-- List calendar ---}}
	<script type="text/template" id="module-resource-calendar-tpl">
		<div id="section-content-resource">
			<label for="codigo_recurso_modulo">Eventos <span class="required">*</span></label>
			<select name="codigo_recurso_modulo" id="codigo_recurso_modulo" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull( Route.route('evento.search') ) %>" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." tyle="width: 100%">
			</select>
		</div>
	</script>

	{{-- List courses ---}}
	<script type="text/template" id="module-resource-course-tpl">
		<div id="section-content-resource">
			<label for="codigo_recurso_modulo">Cursos <span class="required">*</span></label>
			<select name="codigo_recurso_modulo" id="codigo_recurso_modulo" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull( Route.route('curso.search') ) %>" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." tyle="width: 100%">
			</select>
		</div>
	</script>

	{{-- List blog ---}}
	<script type="text/template" id="module-resource-blog-tpl">
		<div id="section-content-resource">
			<label for="codigo_recurso_modulo">Contenido <span class="required">*</span></label>
			<select name="codigo_recurso_modulo" id="codigo_recurso_modulo" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull( Route.route('contenido.search') ) %>" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." tyle="width: 100%">
			</select>
		</div>
	</script>

	{{-- List program ---}}
	<script type="text/template" id="module-resource-program-tpl">
		<div id="section-content-resource">
			<label for="codigo_recurso_modulo">Convocatoria <span class="required">*</span></label>
			<select name="codigo_recurso_modulo" id="codigo_recurso_modulo" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull( Route.route('convocatoria.search') ) %>" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." tyle="width: 100%">
			</select>
		</div>
	</script>

	<script type="text/template" id="addressee-notification-tpl">
		<div class="x_panel">
			<div id="users-search-toggle" class="well">
				{{--  {!! Form::open(array('route' => null, 'id' => 'form-user-notification', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}  --}}
				<div class="row">
					<div class="col-md-5 col-sm-5 col-xs-12 form-group has-feedback">
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
					<div class="col-md-6 col-sm-6 col-xs-10">
						<label>Nombre</label>
						<input type="text" name="nombre_usuario" id="nombre_usuario_filter_data" class="form-control" readonly>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
						<button type="submit" id="add-user-notification" class="btn btn-success btn-sm" data-field="correo_electronico_filter_data" title="Agregar usuario"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				{{--  {!! Form::close() !!}  --}}
			</div>
		
			<div id="wrapper-render-users">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="article-list">
							<ul id="list-content-article" class="list-unstyled msg_list browse-list-userchannel">
								{{-- Render userchannel list --}}
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>	

	{{-- Template search users toggle --}}
	<script type="text/template" id="search-users-toggle-tpl">
		<div id="users-search-toggle" class="well">
			{{--  {!! Form::open(array('route' => null, 'id' => 'form-user-notification', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}  --}}
			<div class="row">
				<div class="col-md-5 col-sm-5 col-xs-12 form-group has-feedback">
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
				<div class="col-md-6 col-sm-6 col-xs-10">
					<label>Nombre</label>
					<input type="text" name="nombre_usuario" id="nombre_usuario_filter_data" class="form-control" readonly>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
					<button type="submit" id="add-user-notification" class="btn btn-success btn-sm" data-field="correo_electronico_filter_data" title="Agregar usuario"><i class="fa fa-plus"></i></button>
				</div>
			</div>
			{{--  {!! Form::close() !!}  --}}
		</div>
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

	<script type="text/template" id="user-notification-list-tpl">
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
									<a href="#" title="Eliminar usuario" class="danger"><i class="fa fa-trash"></i> Eliminar</a>
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

	<script type="text/template" id="multiselect-channel-notification-tpl">
		<select multiple="multiple" class="defaultMulti my-select" id="channels" name="channels">
			<% _.each(channels, function (option) { %>
				<option value="<%- option.codigo_canal %>" ><%- option.nombre_canal %></option>
			<% }); %>
		</select>
	</script>

	<script type="text/template" id="confirm-notification-tpl">
		<p>¿Estás seguro de enviar esta notificación?</p>
	</script>

	{{-- Template filters addressee--}}
	<script type="text/template" id="filters-addressee-tpl">
		<div class="well">
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Programas</label>
					<select name="codigo_convocatoria" id="codigo_convocatoria" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('convocatoria.index') }}" data-placeholder="Selecciona un programa" placeholder="Selecciona un programa" data-param="list" style="width: 100%">
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Fecha de nacimiento</label>
					<input type="text" name="fecha_nacimiento_usuario" id="fecha_nacimiento_usuario" class="form-control dateDefault"  data-inputmask="'mask':'9999-99-99'">
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Nivel de educación</label>
					<select type="text" name="codigo_nivel_educacion" id="codigo_nivel_educacion" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($levels as $level)
							<option value="{{ $level->codigo_nivel_educacion }}" >{{ $level->nombre_nivel_educacion }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Ocupación actual</label>
					<select type="text" name="codigo_lista_ocupacion" id="codigo_lista_ocupacion" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($employment as $value)
							<option value="{{ $value->codigo_lista_ocupacion }}">{{ $value->nombre_lista_ocupacion }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Ciudad</label>
					<select name="codigo_ciudad_usuario" id="codigo_ciudad_usuario" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..."  placeholder="Seleccione..." style="width: 100%">
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Comuna/Corregimiento</label>
					<select type="text" name="codigo_localidad_ciudad" id="codigo_localidad_ciudad" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($locations as $location)
							<option value="{{ $location->id }}">{{ $location->text }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Barrio</label>
					<select name="codigo_barrio_localidad" id="codigo_barrio_localidad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.localidades.barrios.index') }}" data-placeholder="Selecciona un barrio" placeholder="Selecciona un barrio" data-param="list" style="width: 100%">
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Estrato</label>
					<select type="text" name="codigo_estrato" id="codigo_estrato" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($stratum as $value)
							<option value="{{ $value->codigo_estrato }}">{{ $value->nombre_estrato }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Tipo de vivienda</label>
					<select type="text" name="codigo_tipo_vivienda" id="codigo_tipo_vivienda" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($housingType as $value)
							<option value="{{ $value->codigo_tipo_vivienda }}">{{ $value->nombre_tipo_vivienda }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Cabeza de familia</label>
					<select type="text" name="jefe_hogar" id="jefe_hogar" class="form-control">
						<option value="">Seleccione...</option>
						<option value="true">Sí</option>
						<option value="false">No</option>
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Sector donde trabaja</label>
					<select type="text" name="codigo_sector_trabajo" id="codigo_sector_trabajo" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($workSector as $value)
							<option value="{{ $value->codigo_sector_trabajo }}">{{ $value->nombre_sector_trabajo }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Tiene empresa o unidad productiva</label>
					<select type="text" name="enterprise" id="enterprise" class="form-control">
						<option value="">Seleccione...</option>
						<option value="true">Sí</option>
						<option value="false">No</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Autodenominación</label>
					<select type="text" name="codigo_autodenominacion" id="codigo_autodenominacion" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($selfDomination as $value)
							<option value="{{ $value->codigo_autodenominacion }}">{{ $value->nombre_autodenominacion }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Grupo poblacional</label>
					<select type="text" name="codigo_grupo_poblacional" id="codigo_grupo_poblacional" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($populationGroup as $value)
							<option value="{{ $value->codigo_grupo_poblacional }}">{{ $value->nombre_grupo_poblacional }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Grupo comunitario</label>
					<select type="text" name="codigo_grupo_comunitario" id="codigo_grupo_comunitario" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($communityGroup as $value)
							<option value="{{ $value->codigo_grupo_comunitario }}">{{ $value->nombre_grupo_comunitario }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Grupo social</label>
					<select type="text" name="codigo_grupo_social" id="codigo_grupo_social" class="form-control">
						<option value="">Seleccione...</option>
						@foreach ($socialGroup as $value)
							<option value="{{ $value->codigo_grupo_social }}">{{ $value->nombre_grupo_social }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label>Discapacidad</label>
					<select type="text" name="disability" id="disability" class="form-control">
						<option value="">Seleccione...</option>
						<option value="true">Sí</option>
						<option value="false">No</option>
					</select>
				</div>
			</div>
		</div>
	</script>
@stop
