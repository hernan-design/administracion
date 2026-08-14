{{-- views/mentor/detail.blade.php --}}
@extends('mentor.mentor')

@section('mentor.content')
	@php setlocale(LC_TIME,"es_ES"); @endphp
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content tp_x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div class="x_title title">
						<ul class="nav navbar-right panel_toolbox">
							<li>
							<a href="{{ $_SERVER['HTTP_REFERER'] }}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
					</div>
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
						<li role="presentation" class="active"><a href="#tab_mentoring" id="mentoring-tab" role="tab" data-toggle="tab" aria-expanded="true">Mentoría</a>
						</li>
						<li role="presentation"><a href="#tab_mentor" id="mentor-tab" role="tab" data-toggle="tab" aria-expanded="true">Mentor</a>
						</li>
						<li role="presentation"><a href="#tab_entrepreneur" id="mentors-tab" role="tab" data-toggle="tab" aria-expanded="false">Emprendedor</a>
						</li>
						<li
							role="presentation">
							<a
								href="#tab_sessions"
								id="sessions-tab"
								role="tab"
								data-toggle="tab"
								aria-expanded="false"
							>
								Sesiones
							</a>
						</li>
					</ul>
					<div id="myTabContent" class="tab-content">
				  		<div role="tabpanel" class="tab-pane fade active in" id="tab_mentoring" aria-labelledby="mentoring-tab">
							<div class="row">
								<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
									<div class="x_content">
										<div class="row">
											<div class="col-md-6 col-sm-6 col-xs-12 profile_details">
												<div class="well profile_view">
													<div class="col-xs-12">
														<h4 class="brief"><i>Mentor</i></h4>
														<div class="left col-md-7 col-sm-6 col-xs-7">
															<h2>{{ $mentor->nombre_mentor }}</h2>
															<ul class="list-unstyled tp_list-unstyled">
																<li><i class="fa fa-at"></i> {{ $mentor->correo_electronico_mentor }}</li>
																<li><i class="fa fa-user"></i> {{ $mentor->nickname_mentor }}</li>
																<li><i class="fa fa-star-half-empty"></i> {{ $mentor->promedio_evaluacion_mentor }}</li>
															</ul>
														</div>
														<div class="right col-md-5 col-sm-6 col-xs-5 text-center tp_content-image-profile">
															<img src="{{ $mentor->imagen_mentor }}" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="" class="img-circle img-responsive">
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-12 profile_details">
												<div class="well profile_view">
													<div class="col-xs-12">
														<h4 class="brief"><i>Emprendedor</i></h4>
														<div class="left col-md-7 col-sm-6 col-xs-7">
															<h2>{{ $mentor->nombre_emprendedor }}</h2>
															<h5>{{ $mentor->nombre_emprendimiento }}</h5>

															<ul class="list-unstyled tp_list-unstyled">
																<li><i class="fa fa-at"></i> {{ $mentor->correo_electronico_emprendedor }}</li>
																<li><i class="fa fa-user"></i> {{ $mentor->nickname_emprendedor }}</li>
																<li><i class="fa fa-star-half-empty"></i> {{ $mentor->promedio_evaluacion_emprendedor }}</li>
															</ul>
														</div>
														<div class="right col-md-5 col-sm-6 col-xs-5 text-center tp_content-image-profile">
															<img src="{{ $mentor->imagen_emprendedor }}" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="" class="img-circle img-responsive">
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6 col-sm-6 col-xs-12">
												<div class="x_panel">
													<p class="text-justify">
														{{ $mentor->descripcion_asesoria }}
													</p>
													<div class="clearfix"></div>
													<h3 class="brief"><i><strong>Documentos adjuntos:</strong></i></h3>
													<ul class="list-unstyled msg_list">
														@foreach($attached as $value)
															<li>
																<a href="{{ $value->direccion_adjunto }}"><i class="fa fa-download"></i> {{ mb_strimwidth($value->nombre_adjunto, 0, 25, "...") }}
																	<span>
																		<span class="time">{{ strftime("%B %d %Y, %l:%M %p", strtotime($value->fecha_adjunto)) }}</span>
																	</span>
																</a>
															</li>
														@endforeach
													</ul>
												</div>
											</div>
											<div class="col-md-6 col-sm-6 col-xs-12">
												<div id="wrapper-sale-agreement-discussion" class="panel panel-default" data-validate="true">
													<div class="panel-body">
														@include('public.noresults', ['message' => 'No hay comentarios'])
														<section id="discussion-posts-wrap" class="">
															{{-- Render Tasks template --}}
														</section>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
				  		<div role="tabpanel" class="tab-pane fade" id="tab_mentor" aria-labelledby="mentor-tab">
							@if(!is_null($evaluationAdviser))
								<div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<ul id="items-purchase-evaluation" class="list-unstyled">
												@foreach ($evaluationAdviser->respuestaevalasesoria as $adviser)
													<li>
														<div class="clearfix">
															<span><strong>{{ $adviser->nombre_item_general_mentoria }}</strong></span>
															<div class="pull-right stars-rating stars-30 readonly disabled">
																<input type="radio" name="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_5" @if($adviser->evaluacion_item_asesoria == 5) checked @endif value="5"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_5" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_4" @if($adviser->evaluacion_item_asesoria == 4) checked @endif value="4"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_4" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_3" @if($adviser->evaluacion_item_asesoria == 3) checked @endif value="3"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_3" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_2" @if($adviser->evaluacion_item_asesoria == 2) checked @endif value="2"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_2" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_1" @if($adviser->evaluacion_item_asesoria == 1) checked @endif value="1"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $adviser->codigo_item_general_mentoria }}_1" class="label-terms"><i class="fa fa-star"></i></label>
															</div>
														</div>
													</li>
													<br>
												@endforeach
											</ul>
										</div>
									</div>
									<div class="ln_solid"></div>
									<div class="row col-equal-height align-bottom">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label>Comentario Evaluación Asesoría:</label>
											<p>{{ $evaluationAdviser->evaluacionasesoria->comentario_evaluacion_asesoria }}</p>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label>Tipo comentario:</label>
											<p>{{ $evaluationAdviser->evaluacionasesoria->nombre_tipo_come_eval }}</p>
										</div>
									</div>
								</div>
							@else
              <div class="col-md-8 col-sm-12 col-xs-12">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <p>La evaluación por parte del mentor no ha sido realizada.</p>
                  </div>
                </div>
              </div>
              @endif
						</div>
				  		<div role="tabpanel" class="tab-pane fade" id="tab_entrepreneur" aria-labelledby="mentors-tab">
							@if(!is_null($evaluationEntrepreneur))
								<div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<ul id="items-purchase-evaluation" class="list-unstyled">
												@foreach ($evaluationEntrepreneur->respuestaevalasesoria as $entrepreneur)
													<li>
														<div class="clearfix">
															<span><strong>{{ $entrepreneur->nombre_item_general_mentoria }}</strong></span>
															<div class="pull-right stars-rating stars-30 readonly disabled">
																<input type="radio" name="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_5" @if($entrepreneur->evaluacion_item_asesoria == 5) checked @endif value="5"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_5" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_4" @if($entrepreneur->evaluacion_item_asesoria == 4) checked @endif value="4"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_4" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_3" @if($entrepreneur->evaluacion_item_asesoria == 3) checked @endif value="3"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_3" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_2" @if($entrepreneur->evaluacion_item_asesoria == 2) checked @endif value="2"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_2" class="label-terms"><i class="fa fa-star"></i></label>

																<input type="radio" name="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}" id="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_1" @if($entrepreneur->evaluacion_item_asesoria == 1) checked @endif value="1"  disabled="disabled" class="check-star">
																<label for="evaluacion_item_asesoria_{{ $entrepreneur->codigo_item_general_mentoria }}_1" class="label-terms"><i class="fa fa-star"></i></label>
															</div>
														</div>
													</li>
													<br>
												@endforeach
											</ul>
										</div>
									</div>
									<div class="ln_solid"></div>
									<div class="row col-equal-height align-bottom">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label>Comentario Evaluación Asesoría:</label>
											<p>{{ $evaluationEntrepreneur->evaluacionasesoria->comentario_evaluacion_asesoria }}</p>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label>Tipo comentario:</label>
											<p>{{ $evaluationEntrepreneur->evaluacionasesoria->nombre_tipo_come_eval }}</p>
										</div>
									</div>
								</div>
              @else
              <div class="col-md-8 col-sm-12 col-xs-12">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <p>La evaluación por parte del emprendedor no ha sido realizada.</p>
                  </div>
                </div>
              </div>
              @endif
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_sessions" aria-labelledby="mentors-tab">
									<div class="row" id="sessions-content">
										<div class="col-md-12 col-sm-12 col-xs-12 text-center well">
											<button type="button" class="btn btn-success data-export-sessions">Exportar</button>
											<p>Reporte de sesiones</p>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12">
											<div class="table-responsive">
												<input id="codigo_asesoria" type="hidden" value="{{ $mentor->codigo_asesoria }}"/>
												<table id="sessions-list-search-table" class="table table-striped table-bordered" data-page-length="15">
													<thead>
													<tr class="headings">
														<th class="column-title">Codigo sesión</th>
														<th class="column-title">Titulo de la sesión</th>
														<th class="column-title">Fecha programada de sesión</th>
														<th class="column-title">Fecha de finalización</th>
														<th class="column-title">Fecha de aceptación</th>
														<th class="column-title">Estado</th>
														<th class="column-title">Horas de duración</th>
													</tr>
													</thead>
													<tbody >
													<!-- Render view  -->
													</tbody>
												</table>
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
