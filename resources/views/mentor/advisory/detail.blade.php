{{-- views/mentor/advisory/detail.blade.php --}}
@extends('mentor.advisory.advisory')

@section('advisory.content')
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Mentor</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
						<a href="{{ $_SERVER['HTTP_REFERER'] }}"><i class="fa fa-chevron-left"></i> Atrás</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
						<div class="x_content">
							<div class="row">
								<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12 profile_details">
		                          	<div class="well profile_view">
		                            	<div class="col-xs-12">
		                              		<h4 class="brief"><i><strong>MENTOR</strong></i></h4>
		                              		<div class="left col-md-7 col-sm-6 col-xs-7">
		                                		<h2>{{ $advisory->nombre_usuario }}</h2>
				                                <ul class="list-unstyled tp_list-unstyled">
				                                  	<li><i class="fa fa-at"></i> {{ $advisory->correo_electronico }}</li>
				                                  	<li><i class="fa fa-user"></i> {{ $advisory->nickname_usuario }}</li>
													<li>
														<a href="{{ $advisory->linkedin_usuario }}" title="Linkedin" class="{{ $advisory->linkedin_usuario ? 'linkedin' : 'disabled' }}" target="_blank"><i class="fa fa-linkedin" ></i> linkedin</a>
													</li>
													<li><i class="fa fa-star-half-empty"></i> {{ $advisory->promedio_evaluacion_mentor }}</li>
													@if ($advisory->attached_curriculum != "")
														<li>
															<a href="{{ $advisory->attached_curriculum }}">
																<i class="fa fa-download"></i> Hoja de vida
															</a>
														</li>
													@else
														<h5><em>No se ha adjuntado hoja de vida</em></h5>
													@endif
				                                </ul>
		                              		</div>
		                              		<div class="right col-md-5 col-sm-6 col-xs-5 text-center tp_content-image-profile">
		                                		<img src="{{ $advisory->imagen_usuario }}" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="" class="img-circle img-responsive">
		                              		</div>
		                            	</div>
		                          	</div>
		                        </div>
		                	</div>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="x_panel">
										<h3 class="brief"><i><strong>En qué puedo ayudar</strong></i></h3>
										<p class="text-justify">
											{{ $advisory->forma_ayuda_mentoria }}
										</p>
										<div class="clearfix"></div>
										<h3 class="brief"><i><strong>Mi experiencia</strong></i></h3>
										<p class="text-justify">
											{{ strip_tags($advisory->experiencia_usuario) }}
										</p>
										<div class="clearfix"></div>
										<h3 class="brief"><i><strong>Nivel de desarrollo</strong></i></h3>
										<ul class="to_do">
											@foreach (App\Models\Core\EtapaProyecto::getStages() as $key => $stage)
												@if( array_key_exists ($key, $advisory->stage_mentoring) )
													<li><i class="glyphicon glyphicon-ok green"></i> <strong>{{ $stage->nombre_etapa_proyecto }}:</strong> {{ $stage->descripcionetapa_proyecto }}</li>
												@endif
											@endforeach
										</ul>
			                		</div>
		                		</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="x_panel">
										<h3 class="brief"><i><strong>Especialidades (áreas de experiencia)</strong></i></h3>
										<ul class="to_do">
											@foreach ($advisory->specialties as $value)
												<li>{{ $value->nombre_especialidad }}</li>
											@endforeach
										</ul>
										<div class="clearfix"></div>
										<h3 class="brief"><i><strong>Sectores económicos (industrias)</strong></i></h3>
										<ul class="to_do">
											@foreach ($advisory->economic_sector_person as $value)
												<li>{{ $value->nombre_sector_economico }}</li>
											@endforeach
										</ul>
			                		</div>
		                		</div>
		                	</div>
		                </div>
                	</div>
                </div>
            </div>
        </div>
    </div>
@stop
