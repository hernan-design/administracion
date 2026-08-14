@extends('program.program')

@section('program.content')
	<div class="col-xs-12">
        <div class="x_panel">
	        <div class="x_title">
	            <h2>Convocatoria <small>Lista</small></h2>
	            <div class="clearfix"></div>
	        </div>
			<div class="x_content">
				<div id="content-form-program-filter" class="well">
					{!! Form::open(array('route' => null,'id' => 'form-program-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<label>Títulos</label>
								<input type="text" name="nombre_convocatoria" id="nombre_convocatoria" class="form-control">
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<label>Tipo</label>
								<select type="text" name="tipo_convocatoria" id="tipo_convocatoria" class="form-control">
									<option value="">Seleccione...</option>
									@foreach($types as $key => $type)
										<option value="{{ $key }}">{{ $type }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<label>Privacidad</label>
								<select type="text" name="privacidad_contenido" id="privacidad_contenido" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($privacyContent as $value)
										<option value="{{ $value->id }}">{{ $value->text }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach($states as $state)
										<option value="{{ $state->codigo_estado }}">{{ ucfirst(strtolower($state->nombre_estado)) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search">Buscar</button>
							<button type="button" class="btn btn-default clean-filters">Limpiar</button>
							<a href="{{ route('convocatoria.create') }}" class="btn btn-success btn-create">Crear</a>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>

				<!-- start programs -->
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
						{{-- Render program list --}}
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
			</div>
		</div>
	</div>

	{{-- program list tpl --}}
	<script type="text/template" id="program-list-tpl">
		<div id="tp_article-left-box">
			<div class="row">
				<div class="col-xs-4 col-lg-2">
					<div class="image-list">
						<a href="<%= window.Misc.urlFull( Route.route('convocatoria.show', {convocatorium: codigo_convocatoria}) ) %>" class="image-thumb">
							<img src="<%= imagen_convocatoria %>" onerror="imageDefault(this, '{{ config('tuproyecto.no_image') }}')" alt="img" />
						</a>
					</div>
				</div>
				<div class="col-xs-8 col-lg-10">
					<div class="content-item-list">
						<div class="inner-content-list">
							<h5 class="title-item-list ellipsis">
								<a href="<%= window.Misc.urlFull( Route.route('convocatoria.show', {convocatorium: codigo_convocatoria}) ) %>" title="Ver convocatoria" class="hover-list"><%- nombre_convocatoria %>
								</a>
							</h5>
							<p class="description-project-item text-justify ellipsis"><%- resumen_convocatoria %></p>
							<ul class="list-inline data-inline-list">
								<li>
									<div class="accountant-item-list">
										<i class="fa fa-users"></i> Total inscritos <strong> <%- contador_inscritos %></strong>
									</div>
								</li>
							</ul>
						</div>

						<div class="tp_btn-content-edit pull-right text-right">
							<div class="btn-group tp_btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<% if(codigo_estado == window._tp.getState['review']) { %>
										<li><a href="#" class="btn-approve" data-value="aprobar"><i class="fa fa-check"></i> Aprobar</a>
										</li>
										<li><a href="#" class="btn-back" data-value="rechazar"><i class="fa fa-close"></i> Rechazar</a>
										</li>
									<% } %>

									<% console.log(codigo_convocatoria, codigo_estado); if(codigo_estado == window._tp.getState['inactive']) { %>
										<li><a href="#" class="btn-active" data-value="activar"><i class="fa fa-power-off"></i> Activar</a>
										</li>
									<% } %>

									<% if(codigo_estado == window._tp.getState['active']) { %>
										<li><a href="#" class="btn-inactive" data-value="inactivar"><i class="fa fa-power-off"></i> Inactivar</a>
										</li>
									<% } %>
									<li class="divider"></li>
		                        	<li>
										<a href="<%= window.Misc.urlFull(Route.route('convocatoria.edit', {convocatorium: codigo_convocatoria}) ) %>" title="Editar contenido"><i class="fa fa-pencil"></i> Editar
										</a>
		                        	</li>
		                      	</ul>
		                    </div>
							<div class="tp_article-list time">
								<%- moment(fecha_creacion_convocatoria+' '+hora_creacion_convocatoria).format("L h:mm:ss a") %>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/template" id="confirm-program-tpl">
    <div>¿Estás seguro de <strong><%- action %></strong> esta convocatoria?
    <% if (action == 'activar') { %>
        <strong>(si la convocatoria tiene fecha de cierre se eliminará esta información)</strong>
    <% } %>
    </div>
</script>


@stop
