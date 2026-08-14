@extends('layout.layout')

@section('content')

	<section id="report-monitoreo-12-wrapper">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Reporte único</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                	@if (session('message'))
						    <div class="alert alert-info">
						        {{ session('message') }}
						    </div>
						@endif
					  	<div class="well">
							{!! Form::open(['route' => 'reportes.monitoreo.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										<input type="hidden" name="codigo_convocatoria" value="{{ App\Models\Core\Convocatoria::getProgram('invitation_entrepreneurs') }}">
										<button type="submit" class="btn btn-block btn-success">Generar</button>
									</div>
								</div>
							{!! Form::close() !!}
							{!! Form::open(['route' => 'reportes.monitoreo.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										<input type="hidden" name="codigo_convocatoria" value="{{ App\Models\Core\Convocatoria::getProgram('aldea__20') }}">
										<button type="submit" class="btn btn-block btn-success">Generar Aldea 20</button>
									</div>
								</div>
							{!! Form::close() !!}
							{!! Form::open(['route' => 'reportes.monitoreo.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										<input type="hidden" name="codigo_convocatoria" value="{{ App\Models\Core\Convocatoria::getProgram('fase_modelo_negocios_programa_apps_co') }}">
										<button type="submit" class="btn btn-block btn-success">Reporte AppsCo</button>
									</div>
								</div>
							{!! Form::close() !!}
                		</div>
	           		</div>
        		</div>
    		</div>
		</div>
	</section>

@stop
