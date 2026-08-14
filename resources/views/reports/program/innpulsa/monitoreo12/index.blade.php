@extends('layout.layout')

@section('content')

	<section id="report-monitoreo-12-wrapper">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Reporte monitoreo reto 1.2</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
					  	<div class="well">
			                {!! Form::open(['route' => 'reportes.monitoreo12.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
				                <div class="row">
				                	<div class="col-md-offset-4  col-md-1 col-sm-12 col-xs-12 form-group">
								        <label>Aplicación</label>
			                            <input type="number" name="number_register" id="number_register" class="form-control">
			                     	</div>
							        <div class="col-md-3 col-sm-12 col-xs-12 form-group">
							            <label>Notificado</label>
							            <select id="notified" name="notified" class=" form-control" tabindex="-1">
					                        <option value="">Selecciona una opción</option>
				                            <option value="true">Si</option>
				                            <option value="false">No</option>
				                        </select>
							        </div>
								</div>

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
