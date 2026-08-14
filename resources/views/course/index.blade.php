{{-- views/course/index.blade.php --}}
@extends('course.course')

@section('course.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Cursos <small>Lista</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="content-form-course-filter" class="well">
					{!! Form::open(array('route' => null,'id' => 'form-course-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Título</label>
								<input type="text" name="nombre_curso" id="nombre_curso" class="form-control">
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Tipo</label>
								<select type="text" name="tipo_curso" id="tipo_curso" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($courseTypes as $courseType)
										<option value="{{ $courseType->codigo_tipo_curso }}">{{ $courseType->nombre_tipo_curso }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Categoría</label>
								<select type="text" name="codigo_categoria_curso" id="codigo_categoria_curso" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($courseCategories as $courseCategory)
										<option value="{{ $courseCategory->codigo_categoria_curso }}">{{ $courseCategory->nombre_categoria_curso }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Privacidad</label>
								<select type="text" name="codigo_privacidad_contenido" id="codigo_privacidad_contenido" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($privacyContent as $value)
										<option value="{{ $value->id }}">{{ $value->text }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach($states as $state)
										<option value="{{ $state->codigo_estado }}" >{{ ucfirst(strtolower($state->nombre_estado)) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search">Buscar</button>
							<button type="button" class="btn btn-default clean-filters">Limpiar</button>
							<a href="{{ route('curso.create') }}" class="btn btn-success btn-create">Crear</a>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>

				<!-- start courses -->
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
					<ul id="tp_list-content-article" class="list-unstyled msg_list browse-list-course">
						{{-- Render course list --}}
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
				<!-- end courses -->
			</div>
		</div>
	</div>

	<script type="text/template" id="course-list-tpl">
		<div id="tp_article-left-box" class="hover_item">
			<div class="row">
				<div class="col-xs-4 col-lg-2">
					<div class="image-list">
						<a href="<%= window.Misc.urlFull( Route.route('curso.show', {curso: codigo_curso }) ) %>" class="image-thumb">
							<img src="<%= imagen_curso %>" onerror="imageDefault(this, '{{ config('tuproyecto.no_image') }}')" alt="img" />
						</a>
					</div>
				</div>
				<div class="col-xs-8 col-lg-10">
					<div class="content-item-list">
						<div class="inner-content-list">
							<h5 class="title-item-list ellipsis">
								<a href="<%= window.Misc.urlFull( Route.route('curso.show', {curso: codigo_curso }) ) %>" title="Ver curso" class="hover-list"><%- nombre_curso %>
								</a>
							</h5>
							<p class="description-project-item text-justify ellipsis"><%- descripcion_curso %></p>
						</div>
						<div class="tp_btn-content-edit pull-right text-right">
							<div class="btn-group tp_btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<% if(codigo_estado == window._tp.getState['inactive']) { %>
										<li><a href="#" class="btn-active" data-value="activar"><i class="fa fa-power-off"></i> Activar</a>
										</li>
									<% } %>

									<% if(codigo_estado == window._tp.getState['active']) { %>
										<li><a href="#" class="btn-inactive" data-value="inactivar"><i class="fa fa-power-off"></i> Inactivar</a>
										</li>
									<% } %>
									<li class="divider"></li>
		                        	<li>
										<a href="<%= window.Misc.urlFull(Route.route('curso.edit', {curso: codigo_curso}) ) %>" title="Editar curso"><i class="fa fa-pencil"></i> Editar
										</a>
		                        	</li>
									<li class="divider"></li>
									<li>
										<a 	href="#" 
											class="btn-report" 
											data-value="<%- codigo_curso %>"
											title="Crear reporte de curso">
											<i class="fa fa-file-excel-o"></i>
											Reporte
										</a>
									</li>
		                      	</ul>
		                    </div>
							<div class="tp_article-list time text-right">
								<%- moment(fecha_apertura_curso).format("L") %>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>
@stop
