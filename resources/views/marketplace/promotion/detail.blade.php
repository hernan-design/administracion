{{-- views/marketplace/promotion/detail.blade.php --}}
@extends('marketplace.promotion.promotion')

@section('promotion.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>{{ $type }}</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
					<a href="{{ $_SERVER['HTTP_REFERER'] }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="well">
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="x_panel">
								<div class="row">
									<div class="col-md-12 text-left">
										<img src="{{ $promotion->imagen_promocion }}" style="width: 100%; height: auto;">
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-6 text-left">
										<span><strong>Fecha inicio:</strong> {{ $promotion->fecha_inicio_promocion }}</span>
									</div>
									<div class="col-md-6 text-left">
										<span><strong>Fecha fin:</strong> {{ $promotion->fecha_fin_promocion }}</span>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-12 text-left">
										<span><strong>Descripción:</strong> {{ $promotion->descripcion_promocion }}</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop
