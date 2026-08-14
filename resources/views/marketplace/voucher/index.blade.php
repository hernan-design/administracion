{{-- views/marketplace/voucher/index.blade.php --}}
@extends('marketplace.voucher.voucher')

@section('voucher.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Vouchers</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="well">
					{!! Form::open(array('route' => null,'id' => 'form-vouchers-detail-filter', 'class' =>'form-horizontal')) !!}
						<div class="row">
							<div class="col-md-3 col-md-offset-3 col-sm-12 col-xs-12">
								<label>Correo</label>
								<input type="email" name="correo_electronico" id="correo_electronico" class="form-control">
							</div>
							<div class="col-md-3 col-sm-12 col-xs-12">
								<label>Emprendimiento</label>
								<input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4 col-md-offset-4 text-center">
								<button type="submit" class="btn btn-primary btn-search">Buscar</button>
								<button type="button" class="btn btn-default btn-clear">Limpiar</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
				<br/>

				<!-- start vouchers -->
				<div class="table-responsive">
					<table id="vouchers-marketplace-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
							<tr>
								<th>Id Voucher</th>
								<th>Nombre</th>
								<th>Correo</th>
								<th>Emprendimiento</th>
								<th>Valor inicial</th>
								<th>Valor saldo</th>
								<th>Estado</th>
							</tr>
						</thead>
						<tbody >
							<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<!-- end vouchers -->
			</div>
		</div>
	</div>
@stop
