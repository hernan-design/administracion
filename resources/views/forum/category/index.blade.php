{{-- views/forum/category/create-edit.blade.php --}}
@extends('forum.category.category')

@section('forum.category.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Categoría</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="forum-category-filter-content">
					{!! Form::open(array('route' => null,'id' => 'forum-category', 'class' =>'form-horizontal')) !!}
					<div class="row">
						<div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-10">
							<label>Categorías</label>
							<select name="codigo_categoria_foro" id="codigo_categoria_foro" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('foro.categoria.index') }}" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
						</div>
						<div class="col-md-1 col-sm-1 col-xs-2 tp_align-btn">
							<button type="submit" class="btn btn-default btn-add" title="Agregar categoría"><i class="fa fa-plus blue"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
					<div class="tp_top-btn text-left">
						<a title="Crear categoría" data-action="crear" class="btn btn-success btn-create"> Crear categoría</a>
					</div>
					<!-- start categoryforum -->
					<ul class="inline-list-grid">
						<li>
							<div class="wrap-result-count-category">
								{{-- Render browse result count template --}}
							</div>
						</li>
						<li>
							<div class="wrap-pagination-category">
								{{-- Render Pagination--}}
							</div>
						</li>
					</ul>
					<div id="wrapper-assignment-category" class="table-responsive">
						<table id="category-list-search-table" class="table table-striped table-bordered" data-page-length="15">
							<thead>
								<tr class="headings">
									<th class="column-title">Nombre</th>
									<th class="column-title text-center">Acciones</th>
								</tr>
							</thead>
							<tbody >
								<!-- Render view id="list-assignment-category-tpl" -->
							</tbody>
						</table>
						@include('public.noresults', ['message' => 'No hay resultados.'])
					</div>
					<ul class="inline-list-grid">
						<li>
							<div class="wrap-result-count-category">
								{{-- Render browse result count template --}}
							</div>
						</li>
						<li>
							<div class="wrap-pagination-category">
								{{-- Render Pagination--}}
							</div>
						</li>
					</ul>
					<!-- end categoryforum -->
				</div>
			</div>
		</div>
	</div>

	{{-- template list forum category --}}
	<script type="text/template" id="list-assignment-category-tpl">
		<td>
			<%- nombre_categoria_foro %>
		</td>
		<td class="text-center">
			<div class="btn-group tp_btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
					<i class="fa fa-ellipsis-v"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu">
					<% if(!_.isNull(codigo_institucion)){ %>
						<li>
							<a title="Editar categoría" data-id="<%- codigo_categoria_foro %>" data-name="<%- nombre_categoria_foro %>" class="edit"><i class="fa fa-pencil"></i> Editar
							</a>
						</li>
					<% } %>
					<li>
						<a title="Eliminar categoría" data-action="delete" data-id="<%- codigo_categoria_foro %>" data-name="<%- nombre_categoria_foro %>" data-state="<%- codigo_estado %>" class="danger"><i class="fa fa-trash"></i> Eliminar</a>
					</li>
				</ul>
			</div>
		</td>
	</script>

	{{-- Template confirm delete category --}}
	<script type="text/template" id="confirm-forum-category-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la categoría <strong><%- category_name %></strong>?</p>
	</script>

	{{-- Template edit category --}}
	<script type="text/template" id="create-edit-forum-category-tpl">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
				<label>Nombre</label>
				<input type="text" name="nombre_categoria_foro" id="nombre_categoria_foro" class="form-control" value="<%- typeof nombre_categoria_foro != 'undefined' && !_.isEmpty(nombre_categoria_foro) ? nombre_categoria_foro : '' %>" required>
			</div>
		</div>
	</script>
@stop
