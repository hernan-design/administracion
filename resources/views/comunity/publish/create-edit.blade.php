{{-- views/comunity/publish/create-edit.blade.php --}}
@extends('comunity.publish.publish')

@section('publish.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Publicaciones</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content section_content">
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-publish-channels', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-2 col-sm-4 col-xs-12">
								<label>Módulo <span class="required">*</span></label>
								<select type="text" name="codigo_modulo" id="codigo_modulo" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($modules as $value)
										<option value="{{ $value->codigo_modulo }}">{{ $value->nombre_modulo }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<div id="module-resource">

								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-4 col-xs-12">
								<br>
								<div class="checkbox-inline">
									<label class="checkbox-inline">
										<input type="checkbox" name="channel" id="publish_general" class="flat"> <strong>General</strong>
									</label>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-4 col-xs-12">
								<div class="x_panel">
									<div id="content-list-channels">
				                      	<ul id="list-channels-publish" class="to_do">
											@include('public.noresults', ['message' => 'No hay resultados.'])
											{{-- Render input template id="list-channels-publish-tpl" --}}
				                      	</ul>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
							<button type="submit" class="btn btn-success btn-block">Publicar</button>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>

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

	{{-- List channels notifications-tpl --}}
	<script type="text/template" id="list-channels-publish-tpl">
		<% if( typeof (codigo_canal) != 'undefined' ){ %>
	  	<p>
			<input type="checkbox" name="channel" id="channel" value="<%- codigo_canal %>" class="flat publish_channels"> <%- nombre_canal %>
		</p>
		<% } %>
	</script>
@stop
