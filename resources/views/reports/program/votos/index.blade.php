@extends('layout.layout')

@section('content')
	<section id="report-votos-convocatoria-wrapper">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Reporte votos</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
						@if (session('message'))
						    <div class="alert alert-info">
						        {{ session('message') }}
						    </div>
						@endif
					  	<div class="well">
			                {!! Form::open(['route' => 'reportes.votos.index','id' => 'form-tracing-filter', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 form-group">
										<label>Convocatoria</label>
										<select name="convocatoria" id="convocatoria" class="form-control select-convocatoria" required data-placeholder="Selecciona una convocatoria" placeholder="Selecciona una convocatoria">
											<option value="">Selecciona una convocatoria</option>
											@foreach( App\Models\Core\Convocatoria::getProgramList() as $value)

												<option value="{{ $value->key }}">{{ $value->value}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="row">
							    	<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
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
