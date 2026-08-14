{{-- views/article/index.blade.php --}}
@extends('article.article')

@section('article.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Contenido <small>Lista</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="content-form-article-filter" class="well">
					{!! Form::open(array('route' => null,'id' => 'form-article-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Título</label>
								<input type="text" name="nombre_contenido" id="nombre_contenido" class="form-control">
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Landing</label>
								<select type="text" name="landing_contenido" id="landing_contenido" class="form-control">
									<option value="">Seleccione...</option>
									<option value="1">Si</option>
									<option value="0">No</option>
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Privacidad</label>
								<select type="text" name="privacidad_contenido" id="privacidad_contenido" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($privacyContent as $value)
										<option value="{{ $value->id }}">{{ $value->text }}
										</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Tipo contenido</label>
								<select type="text" name="codigo_tipo_cont_institucion" id="codigo_tipo_cont_institucion" class="form-control">
									<option value="">Seleccione...</option>
										@foreach ($contentTypes as $contentType)
											<option value="{{ $contentType->codigo_tipo_cont_institucion }}" {{  isset($content) ? $content->codigo_tipo_cont_institucion == $contentType->codigo_tipo_cont_institucion ? 'selected' : '' : '' }}>{{ $contentType->nombre_tipo_contenido }}</option>
										@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach($states as $state)
										<option value="{{ $state->codigo_estado }}">{{ ucfirst(mb_strtolower($state->nombre_estado, 'UTF-8')) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search">Buscar</button>
							<button type="button" class="btn btn-default clean-filters">Limpiar</button>
							<a href="{{ route('contenido.create') }}" class="btn btn-success btn-create">Crear</a>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>

				<!-- start articles -->
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
					<ul id="tp_list-content-article" class="list-unstyled msg_list browse-list-program">
						{{-- Render article list --}}
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
				<!-- end articles -->
			</div>
		</div>
	</div>

	<script type="text/template" id="content-list-tpl">
		<div id="tp_article-left-box" class="hover_item">
			<div class="row">
				<div class="col-xs-4 col-lg-2">
					<div class="image-list">
						<a href="<%= window.Misc.urlFull( Route.route('contenido.show', {contenido: codigo_contenido }) ) %>" class="image-thumb">
							<img src="<%- imagen_contenido %>"  onerror="imageDefault(this, '{{ config('tuproyecto.no_image') }}')" alt="<%- nombre_contenido %>" />
						</a>
					</div>
				</div>
				<div class="col-xs-8 col-lg-10">
					<div class="content-item-list">
						<div class="inner-content-list">
							<h5 class="title-item-list ellipsis">
								<a href="<%= window.Misc.urlFull( Route.route('contenido.show', {contenido: codigo_contenido }) ) %>" title="Ver contenido" class="hover-list"><%- nombre_contenido %>
								</a>
							</h5>
							<p class="description-project-item text-justify ellipsis"><%- resumen_contenido %></p>
						</div>
						<div class="tp_btn-content-edit text-right">
							<div class="btn-group tp_btn-group">
								<% if(codigo_estado == window._tp.getState['review']) { %>
									<button type="button" class="btn btn-danger dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
										<i class="fa fa-ellipsis-v"></i>
									</button>
								<% }else{ %>
									<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
										<i class="fa fa-ellipsis-v"></i>
									</button>
								<% } %>
		                      	<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<% if(codigo_estado == window._tp.getState['review']) { %>
										<li><a href="#" class="btn-approve" data-value="aprobar" data-name="<%- nombre_contenido %>"><i class="fa fa-check"></i> Aprobar</a>
										</li>
										<li><a href="#" class="btn-back" data-value="rechazar" data-name="<%- nombre_contenido %>"><i class="fa fa-close"></i> Rechazar</a>
										</li>
									<% } %>

									<% if(codigo_estado == window._tp.getState['inactive']) { %>
										<li><a href="#" class="btn-active" data-value="activar" data-name="<%- nombre_contenido %>"><i class="fa fa-power-off"></i> Activar</a>
										</li>
									<% } %>

									<% if(codigo_estado == window._tp.getState['active']) { %>
										<li><a href="#" class="btn-inactive" data-value="inactivar" data-name="<%- nombre_contenido %>"><i class="fa fa-power-off"></i> Inactivar</a>
										</li>
									<% } %>
									<% if(codigo_usuario == {{ Auth::user()->codigo_usuario }}) { %>
										<li class="divider"></li>
			                        	<li>
											<a href="<%= window.Misc.urlFull(Route.route('contenido.edit', {contenido: codigo_contenido}) ) %>" title="Editar contenido"><i class="fa fa-pencil"></i> Editar
											</a>
			                        	</li>
									<% } %>
		                      	</ul>
		                    </div>

							<div class="tp_article-list time text-right"><%- moment(fecha_contenido+' '+hora_contenido).format("L h:mm:ss a") %>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>

	{{-- confirms messages templates --}}
	<script type="text/template" id="confirm-content-confirm-tpl">
		<div>¿Estás seguro de <strong><%- action %></strong> el artículo <strong><%- article_name %></strong>?</div>
	</script>

@stop
