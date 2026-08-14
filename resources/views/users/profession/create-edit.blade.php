{{-- views/users/profession/create-edit.blade.php --}}
@extends('users.profession.profession')

@section('professions.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="x_title">
					<h2>Profesión</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a href="{{route('usuario.profesion.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-profession', 'data-parsley-validate'=>'')) !!}
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
								<label>Nombre <span class="required">*</span></label>
								<input type="text" name="nombnre_profesion_persona" id="nombnre_profesion_persona" class="form-control" value="{{ isset($profession) ? $profession->nombnre_profesion_persona : '' }}" required>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
								<button type="submit" class="btn btn-success btn-block">{{ isset($profession) ? 'Editar' : 'Guardar' }}</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@stop
