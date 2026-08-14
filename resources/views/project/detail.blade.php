{{-- views/marketplace/detail.blade.php --}}
@extends('marketplace.marketplace')

@section('marketplace.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content tp_x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div class="x_title title">
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{ route('entidad.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
					</div>
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    	<li role="presentation" class="active"><a href="#tab_project" id="project-tab" role="tab" data-toggle="tab" aria-expanded="true">Proyecto</a>
                        </li>
                        <li role="presentation"><a href="#tab_mentors" role="tab" id="mentors-tab" data-toggle="tab" aria-expanded="false">Mentores</a>
                        </li>
						<li role="presentation">
							<a
								href="#tab_records"
								role="tab"
								id="records-tab"
								data-toggle="tab"
								aria-expanded="false"
							>
								Archivos
							</a>
                        </li>
                  	</ul>
					<div id="myTabContent" class="tab-content">
				  		<div role="tabpanel" class="tab-pane fade active in" id="tab_project" aria-labelledby="home-tab">
							<div class="x_content">
								<section id="marketplace-info" class="panel">
					                <div class="bs-example" data-example-id="simple-jumbotron">
					                    <div class="jumbotron">
											<div class="row">
												<div class="col-md-3 col-sm-6 col-xs-12">
													<div class="profile_img">
								                        <div id="crop-avatar">
								                            <img class="img-responsive avatar-view" src="{{ $entityBuild->imagen_usuario }}" alt="{{ $entityBuild->nombre_usuario }}" />
								                        </div>
								                    </div>
							                    </div>
												<div class="col-md-8 col-sm-6 col-xs-12">
													<h3><strong>{{ $entityBuild->nombre_usuario }}</strong></h3>
													<span><i class="fa fa-address-card" aria-hidden="true"></i> <strong>{{ $entityBuild->cedula_founder }}</strong></span><br>
													<span><i class="fa fa-envelope" aria-hidden="true"></i> <strong>{{ $entityBuild->correo_founder }}</strong></span><br>
													<span><i class="fa fa-phone" aria-hidden="true"></i> <strong>{{ $entityBuild->movil_founder }}</strong></span>
												</div>
											</div>
											<br>
											<div class="row">
												<div class="col-md-12 col-sm-12 col-xs-12">
													<div class="text-justify">
														<span>{{ $entityBuild->biografia_usuario }}</span>
													</div>
												</div>
											</div>

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
					                </div>
								</section>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_mentors" aria-labelledby="profile-tab">
							<div class="x_content">
								<div id="user-filter-content" class="well">
									{!! Form::open(array('route' => null,'id' => 'user-filter', 'class' =>'form-horizontal')) !!}
										<div class="row">
											<div class="col-md-4 col-md-offset-2 col-xs-12">
												<label>Correo electrónico</label>
												<input type="text" name="correo_electronico" id="user_correo_electronico" class="form-control">
											</div>
											<div class="col-md-4 col-xs-12">
												<label>Nombre</label>
												<input type="text" name="nombre_usuario" id="user_nombre_usuario" class="form-control">
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
										<!-- start user -->
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
											<ul id="list-content-article" class="list-unstyled msg_list browse-list-user">
												{{-- Render user list --}}
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
										<!-- end user -->
									</div>
								</div>
                        	</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_records" aria-labelledby="records-tab">

							{{-- Content project documents --}}
							<div class="content <%- tab=='documentos' ? 'active' : '' %>" id="project-document">
								<div class="row">
									<div class="small-12 col-md-12 columns">
										<div class="panel-info with-title <%- owner ? '' : '' %>">
											<div id="project-documents-container" class="editable-item">
												<h5 class="title-panel text-left">
													<i class="mdi mdi-paperclip"></i> <span class="field-text">Documentos</span>
												</h5>
												<input type="hidden" id="codigo_usuario" value="{{ $entityBuild->codigo_usuario }}">
												<section id="wrap-documents" class="">
													{{-- Render  Documents --}}
													
												</section>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/template" id="edit-project-documents-form-tpl">

		{!! Form::open(array('route' => 'adjunto.proyecto.index', 'data-abide' => '', 'id' => 'form-edit-project-documents')) !!}
			<div class="row">
				<div 
					class="col-xs-12 col-sm-4 col-md-12  text-center"
					style=""
				>
					<h4>Archivos adjuntos</h4>
			 
						<p class="">
							En esta sección debes adjuntar los siguientes 
							documentos del emprendimiento: Cámara de Comercio <br>
							con una vigencia no mayor a 3 meses, RUT y cédula del 
							representante legal.
						</p>
		 
				</div>
			</div>
			<input type="hidden" id="name_project" name="name_project" value="{{ $entityBuild->nombre_usuario }}">
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-md-12 columns">
					<div id="fnuploader-attach-file" class="uploader-attachments"></div>
				</div>
			</div>
		 
				<div
					class="row save-section"
					style="justify-content: center;margin-top: 5px;"
				>
					<div class="">
						{!! Form::submit('Cargar archivos', array('class' => 'btn btn-success btn-block sky-blue btn-shadow radius')) !!}
					</div>
				</div>
	 
		{!! Form::close() !!}

	</script>

	{{-- Template attachments file uploader --}}
<script type="text/template" id="qq-attachments-template">

	<div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Documentos">
		<div
			class="row"
			style="justify-content: center;"
		>
			<div class="clases borradas">
				<div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file btn btn-success btn-block">
					<div>Selecciona tu archivo</div>
				</div>
			</div>
		</div>
		<div id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
			<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Processing dropped files...</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<div class="content-list-attachments scroll-general-v">
			<ul
				class="qq-upload-list-selector qq-upload-list-attachments"
				aria-live="polite"
				aria-relevant="additions removals"
				style="list-style: none;padding: 0;"
			>
				<li
					style="padding: 5px;
						   border-style: dotted;
						   border-width: 1px;"
				>
					<div class="qq-progress-bar-container-selector">
						<div
							role="progressbar"
							aria-valuenow="0"
							aria-valuemin="0"
							aria-valuemax="100"
							class="qq-progress-bar-selector qq-progress-bar"
						>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 columns">
							<span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
							<img class="qq-thumbnail-selector" qq-max-size="50" qq-server-scale>
							<a class="preview-link" target="_blank">
								<span class="qq-upload-file-selector qq-upload-file"></span>
							</a>
							<span class="qq-upload-size-selector qq-upload-size"></span>

							<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
							<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
							<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
							<button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
							<button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
							<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
						</div>
						<div class="col-md-6 columns">
							<div
								class="row"
								style="font-size: 10px;"
							>
								<div class="col-xs-12  col-sm-4 col-md-6">
									<label class="label-codigo-documento dark-text" for="codigo_documento">
										Tipo de documento
									</label>
									<select
										name="codigo_documento"
										id="codigo_documento"
										class="browser-default valid  form-control parsley-success"
										required
										data-placeholder="Tipo"
										placeholder="Tipo">
										@foreach( App\Models\Param\Documento::getDocuments('entity') as $document)
											<option
												value="{{ $document->codigo_documento }}">
												{{ $document->nombre_documento }}
											</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-12  col-sm-4 col-md-6">
									<label class="label-codigo_privacidad dark-text">
										Privacidad
										<select
											name="codigo_privacidad_contenido"
											id="codigo_privacidad_contenido"
											class="browser-default valid form-control parsley-success"
											required
											data-placeholder="Privacidad"
											placeholder="Privacidad">
											@foreach( App\Models\Core\PrivacidadContenido::getPrivacyContent() as $item)
												<option
													value="{{ $item->codigo_privacidad_contenido }}"
													{{ App\Models\Core\PrivacidadContenido::getPrivacy('public') == $item->codigo_privacidad_contenido ? 'selected' : '' }}>
													{{ $item->nombre_privacidad_contenido }}
												</option>
											@endforeach
										</select>
									</label>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</script>

	{{-- Template search users toggle --}}
	<script type="text/template" id="search-users-toggle-tpl">
		<div class="x_content">
			<div id="users-search-toggle" class="well">
				{!! Form::open(array('route' => null, 'id' => 'form-user', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
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
					<div class="col-md-3 col-sm-3 col-xs-12">
						<label>Nombre mentor</label>
						<input type="text" name="nombre_mentor" id="nombre_mentor_filter_data" class="form-control" readonly>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-10">
						<label>Tema</label>
						<select name="codigo_tipo_asesoria" id="codigo_tipo_asesoria" class="form-control" required>
							<option value="">Seleccione...</option>
							@foreach ($mentorThemes as $type)
								<option value="{{ $type->codigo_tipo_asesoria }}">{{ $type->nombre_tipo_asesoria }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
						<button type="submit" class="btn btn-success btn-sm" data-field="correo_electronico_filter_data" title="Agregar usuario"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</script>

	<script type="text/template" id="user-mentor-list-tpl">
		<% if(typeof(codigo_mentor) !== "undefined"){ %>
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-9">
					<div class="tp_content-list">
						<a href="<%= window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_mentor }) ) %>" class="hover-list">
						  	<span class="image">
								<img src="<%= imagen_mentor %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
						  	</span>
						  	<span>
								<span>
									<strong class="text-capitalize"><%- nombre_mentor.toLowerCase() %></strong>
								</span>
								<span class="message">
									<i class="fa fa-at"></i> <%- correo_mentor %><br>
									<div class="tp_hide-information">
										<i class="fa fa-user"></i> <%- nickname_mentor %>
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
								<% if(codigo_estado != window._tp.getState['pending']){ %>
									<li>
										<a href="#" title="Eliminar usuario" data-action="eliminar" data-id="<%- codigo_asesoria %>" data-name="<%- nombre_mentor %>" data-state="<%- codigo_estado %>" class="danger"><i class="fa fa-trash"></i> Eliminar</a>
									</li>
								<% }else{ %>
									<li>
										<a>No se puede remover este mentor</a>
									</li>
								<% } %>
							</ul>
						</div>
						<% if(!_.isEmpty(ciudad_mentor)){ %>
							<div class="time tp_hide-information">
								<i class="fa fa-map-marker"></i> <%- ciudad_mentor %>
							</div>
						<% } %>
					</div>
				</div>
			</div>
		<% } %>
	</script>

	{{-- Template section option  users --}}
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

	{{-- Template section option mentoring --}}
	<script type="text/template" id="confirm-mentoring-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> el mentor <strong><%- nombre_mentor %></strong> de este proyecto?</p>
	</script>
@stop
