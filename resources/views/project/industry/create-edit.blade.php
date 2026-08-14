{{-- views/project/industry/create-edit.blade.php --}}
@extends('project.industry.industry')

@section('industries.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="x_title">
					<h2>Industria</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a href="{{route('entidad.industria.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-industry', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Nombre <span class="required">*</span></label>
								<input type="text" name="nombre_categoria_proyecto" id="nombre_categoria_proyecto" class="form-control" value="{{ isset($industry) ? $industry->nombre_categoria_proyecto : '' }}" required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Descripción</label>
								<textarea type="text" name="descripcion_categoria_proyecto" id="descripcion_categoria_proyecto" class="form-control" data-parsley-maxlength="200" value="{{ isset($industry) ? $industry->descripcion_categoria_proyecto : '' }}">{{ isset($industry) ? $industry->descripcion_categoria_proyecto : '' }}</textarea>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
								<button type="submit" class="btn btn-success btn-block">{{ isset($industry) ? 'Editar' : 'Guardar' }}</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@stop
