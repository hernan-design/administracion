@extends('layout.layout')

@section('content')

	<section id="report-monitoreo-12-wrapper">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Reporte Proyectos</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
						@if (session('message'))
						    <div class="alert alert-info">
						        {{ session('message') }}
						    </div>
						@endif
					  	<div class="well">
			                {!! Form::open(['route' => 'reportes.proyectos.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
							    <div class="row">
							    	<div class="col-md-2 col-md-offset-5 col-sm-12 col-xs-12 text-center">
				                      	<button type="submit" class="btn btn-block btn-success">Generar</button>
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