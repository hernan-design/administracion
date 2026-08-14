@extends('layout.layout')

@section('content')

	<section id="report-convocatoria-rutan-wrapper" class="content">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Reporte único Ruta N</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                	@if (session('message'))
						    <div class="alert alert-info">
						        {{ session('message') }}
						    </div>
						@endif
					  	<div class="well">
							{!! Form::open(['route' => null,'id' => 'form-report-rutan-209', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										<input type="hidden" name="id_report" value="209">
										<button
											type="submit"
											class="btn btn-block btn-success"
											id="report_convocatoria_rutan"
											>
											Reporte único 209
										</button>
									</div>
								</div>
							{!! Form::close() !!}
		
							{!! Form::open(['route' => null,'id' => 'form-report-rutan-200', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										<input type="hidden" name="id_report" value="200">
										<button
											type="submit"
											class="btn btn-block btn-success"
											id="report_convocatoria_rutan_200"
											>
											Reporte único 200
										</button>
									</div>
								</div>
							{!! Form::close() !!}
                		</div>
	           		</div>
        		</div>
			</div>
		</div>

		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
	                <div class="x_title">
	                	<h2>Reportes de perfilamiento</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
					  	<div class="well">
							{!! Form::open(['route' => null,'id' => 'form-general_convocatoria_ruta_emprendimiento', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										 <!--convocatoria 634 ruta del emprendimiento -->
										<input type="hidden" name="id_report" value="General Ruta Emprendimiento">
										<button
											type="submit"
											class="btn btn-block btn-success"
											id="general_convocatoria_ruta_emprendimiento"
										>
											Ruta del Emprendimiento
										</button>
									</div>
								</div>
							{!! Form::close() !!}
                		</div>
	           		</div>
        		</div>
			</div>
		</div>

		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
	                <div class="x_title">
	                	<h2>Reportes de acompañamientos</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
					  	<div class="well">
							{!! Form::open(['route' => null,'id' => 'form-report-mentorias', 'class' =>'']) !!}
								<div class="row">
									<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
										 <!--convocatoria 634 ruta del emprendimiento -->
										<input type="hidden" name="id_report" value="Mentorias">
										<button
											type="submit"
											class="btn btn-block btn-success"
											id="report-mentorias"
										>
											Reporte acompañamientos
										</button>
									</div>
								</div>
							{!! Form::close() !!}

							{!! Form::open(['route' => null,'id' => 'form-report-sessions-rutan', 'class' =>'']) !!}
							<div class="row">
								<div class="col-md-3 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
									 <!--convocatoria 634 ruta del emprendimiento -->
									<input type="hidden" name="id_report" value="Sessions_rutan">
									<button
										type="submit"
										class="btn btn-block btn-success"
										id="report-sessions-rutan"
									>
										Reporte de sesiones
									</button>
								</div>
							</div>
						{!! Form::close() !!}
					</div>
                		</div>
	           		</div>
        		</div>
			</div>
		</div>
	</section>

	{{-- confirms messages templates --}}
	<script type="text/template" id="confirm-export-data-tpl">
		<div>¿Estás seguro de exportar reporte <%= id_report %>?</div>
	</script>

@stop
