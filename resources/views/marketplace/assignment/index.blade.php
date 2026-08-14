{{-- views/marketplace/assignment/index.blade.php --}}
@extends('marketplace.assignment.assignment')

@section('assignment.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Asignaciones <small>(Marketplace)</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-assignments-members-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-1 col-md-offset-2 col-sm-12 col-xs-12">
								<label>Aplicación</label>
								<input type="number" name="number_register" id="number_register" class="form-control">
							</div>
							<div class="col-md-3 col-sm-12 col-xs-12">
								<label>Emprendimiento</label>
								<input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
							</div>
							<div class="col-md-3 col-sm-12 col-xs-12">
								<label>Con asignación</label>
								<select name="asignacion" id="asignacion" class="form-control">
									<option value="">Selecciona una opción</option>
									<option value="si">Si</option>
									<option value="no">No</option>
								</select>
							</div>
						</div>
						<br>
						<div class="text-center">
							<button type="submit" class="btn btn-primary">Buscar</button>
							<button type="button" class="btn btn-default btn-clear-filter">Limpiar</button>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>

				<!-- start projects -->
				<div class="table-responsive">
					<table id="assignments-marketplace-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
							<tr>
								<th>Aplicación</th>
								<th>Emprendimiento</th>
								<th>Usuario</th>
								<th class="text-center">Asignación</th>
							</tr>
						</thead>
						<tbody >
							<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<!-- end projects -->
			</div>
		</div>
	</div>
@stop
