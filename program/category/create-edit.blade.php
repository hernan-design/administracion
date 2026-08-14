{{-- views/program/category/create-edit.blade.php --}}
@extends('program.category.category')

@section('categories.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="x_title">
					<h2>Categoría</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a href="{{route('convocatoria.categoria.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-category', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Nombre <span class="required">*</span></label>
								<input type="text" name="nombre_categoria_proceso" id="nombre_categoria_proceso" class="form-control" value="{{ isset($category) ? $category->nombre_categoria_proceso : '' }}" required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Descripción</label>
								<textarea type="text" name="descripcion_categoria_proceso" id="descripcion_categoria_proceso" class="form-control" data-parsley-maxlength="200" value="{{ isset($category) ? $category->descripcion_categoria_proceso : '' }}">{{ isset($category) ? $category->descripcion_categoria_proceso : '' }}</textarea>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
								<button type="submit" class="btn btn-success btn-block">{{ isset($category) ? 'Editar' : 'Guardar' }}</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@stop
