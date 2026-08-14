{{-- views/comunity/channel/create-edit.blade.php --}}
@extends('comunity.category.category')

@section('category.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Categoría</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a href="{{ route('comunidad.categoria.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="well">
					{!! Form::open(array('route' => null, 'id' => 'form-category', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-xs-12">
								<label>Nombre <span class="required">*</span></label>
								<input type="text" name="nombre_categoria_canal" id="nombre_categoria_canal" class="form-control" value="{{ isset($channelCategory) ? $channelCategory->nombre_categoria_canal : '' }}" required>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="submit" class="btn btn-success">{{ (isset($channelCategory)) ? 'Editar' : 'Guardar' }}</button>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@stop
