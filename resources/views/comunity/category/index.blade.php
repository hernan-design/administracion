{{-- views/comunity/category/index.blade.php --}}
@extends('comunity.category.category')

@section('category.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Categoría <small>Lista</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div id="category-filter-content" class="well">
					{!! Form::open(array('route' => null,'id' => 'category-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-2 col-sm-4 col-xs-12">
								<label>Nombre</label>
								<input type="text" name="nombre_categoria_canal" id="nombre_categoria_canal" class="form-control">
							</div>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($states as $state)
										<option value="{{ $state->codigo_estado }}">{{  ucfirst( strtolower($state->nombre_estado) ) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
							<a href="{{ route('comunidad.categoria.create') }}" class="btn btn-success btn-create">Crear</a>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>
				<div class="table-responsive">
					<table id="category-list-search-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title">Nombre</th>
							<th class="column-title">Acción</th>
						</tr>
						</thead>
						<tbody >
						<!-- Render view  -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@stop
