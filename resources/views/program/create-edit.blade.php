{{-- views/program/create-edit.blade.php --}}
@extends('program.program')

@section('program.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					@if(isset($program))
					<div class="x_title title">
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{ route('convocatoria.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
					</div>
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
						<li role="program" class="active"><a href="#tab_program" id="program-tab" role="tab" data-toggle="tab" aria-expanded="true">Convocatoria</a>
						</li>
						<li role="phase"><a href="#tab_phases" role="tab" id="phase-tab" data-toggle="tab" aria-expanded="false">Fases</a>
						</li>
						@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
	                        <li role="channel"><a href="#tab_channels" role="tab" id="channel-tab" data-toggle="tab" aria-expanded="false">Canales</a>
							</li>
						@endif
						@if($program->codigo_orientacion_convocatoria == App\Models\Core\OrientacionConvocatoria::getOrientation('user'))
							<li role="users"><a href="#tab_users" role="tab" id="users-tab" data-toggle="tab" aria-expanded="false">Usuarios</a>
							</li>
						@endif
						<li role="entity"><a href="#tab_entity" id="entity-tab" role="tab" data-toggle="tab" aria-expanded="true">Entidades</a>
						</li>
						<li role="entity"><a href="#tab_asignaciones" id="entity-tab" role="tab" data-toggle="tab" aria-expanded="true">Asignaciones</a>
						</li>
						@if($program->tipo_convocatoria == 2)
						<li role="results"><a href="#tab_results" id="results-tab" role="tab" data-toggle="tab" aria-expanded="true">{{transtp('programs.results')}}</a>
						</li>
						@endif
					</ul>
					@else
					<div class="x_title">
						<h2>Convocatoria</h2>
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{route('convocatoria.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					@endif
					<div id="myTabContent" class="tab-content">
						<div role="program" class="tab-pane fade active in" id="tab_program" aria-labelledby="program-tab">
							<div class="well">
								{!! Form::open(array('route' => null,'id' => 'form-program', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Título <span class="required">*</span></label>
											<input type="text" name="nombre_convocatoria" id="nombre_convocatoria" class="form-control" value="{{ isset($program) ? $program->nombre_convocatoria : '' }}" required>
										</div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Orientación <span class="required">*</span></label>
											<select name="codigo_orientacion_convocatoria" id="codigo_orientacion_convocatoria" class="form-control" required>
												<option value="">Seleccione...</option>
												@foreach ($programOrentation as $value)
													<option value="{{ $value->codigo_orientacion_convocatoria }}" {{ isset($program) ? $value->codigo_orientacion_convocatoria == $program->codigo_orientacion_convocatoria ? 'selected' : '' : '' }}>{{ $value->nombre_orientacion_convocatoria }}</option>
												@endforeach
											</select>
										</div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label>Privacidad <span class="required">*</span></label>
                      <select name="codigo_privacidad_contenido" id="codigo_privacidad_contenido" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach ($privacyContent as $value)
                          <option value="{{ $value->id }}" {{ isset($program) ?  $program->codigo_privacidad_contenido == $value->id ? 'selected' :  '' : $value->id == App\Models\Core\PrivacidadContenido::getPrivacy('public') ? 'selected' : '' }}>{{ $value->text }}</option>
                        @endforeach
                      </select>
                    </div>
									</div>
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Texto botón </label><small> por defecto 'participa'</small>
											<input type="text" name="texto_boton" id="texto_boton" class="form-control"  value="{{ isset($program) ? $program->texto_boton : '' }}" >
										</div>
										<div class="col-md-2 col-sm-4 col-xs-12">
											<label>Tiempo espera </label>
											<input type="number" class="form-control text-center spinner-quantity" name="tiempo_espera_nueva_aplicacion" id="tiempo_espera_nueva_aplicacion" value="{{ isset($program) ? $program->tiempo_espera_nueva_aplicacion ? $program->tiempo_espera_nueva_aplicacion : 0 : 0 }}" min="0">
										</div>
										<div class="col-md-2 col-sm-4 col-xs-12">
											<label>Fecha cierre</label>
											<input type="text" name="fecha_cierre_convocatoria" id="fecha_cierre_convocatoria" class="form-control datepicker"  data-inputmask="'mask':'9999-99-99'" value="{{ isset($program) ? $program->fecha_cierre_convocatoria : '' }}">
										</div>
                    <div class="col-md-2 col-sm-4 col-xs-12">
											<label>Hora cierre</label>
											<input type="time" name="hora_cierre_convocatoria" id="hora_cierre_convocatoria" class="form-control" value="{{ isset($program) ? $program->hora_cierre_convocatoria : '23:59' }}">
										</div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Tipo <span class="required">*</span></label>
											<select name="tipo_convocatoria" id="tipo_convocatoria" class="form-control" required>
												<option value="">Seleccione...</option>
												@foreach ($types as $key => $type)
													<option value="{{ $key }}" {{ isset($program) ? $program->tipo_convocatoria == $key ? 'selected' : '' : '' }}>{{ $type }}</option>
												@endforeach
											</select>
										</div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<div id="survey-program">
												{{-- Render select template id="survey-program-tpl" --}}
											</div>
											<div id="url-external-program">
												{{-- Render input template id="url-external-program-tpl" --}}
											</div>
										</div>
                    @if (session('institution')->codigo_institucion === 69)
                      <div class="col-md-4 col-sm-4 col-xs-12">
                        <label>Eje</label>
                        <select name="eje_convocatoria" id="eje_convocatoria" class="form-control">
                          <option value="">Seleccione...</option>
                          @if (isset($program))
                            @foreach ($allEjes as $ejes)
                              <option value="{{ $ejes->codigo_eje }}" {{ $program->codigo_eje === $ejes->codigo_eje ? 'selected' : ''  }}>{{ ucwords(strtolower($ejes->nombre_eje)) }}</option>
                            @endforeach
                          @else
                            @foreach ($allEjes as $ejes)
                              <option value="{{ $ejes->codigo_eje }}">{{ ucwords(strtolower($ejes->nombre_eje)) }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    @endif
									</div>
									<div class="row">
										<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<br>
											<div class="checkbox-inline">
												<label class="checkbox-inline">
													<input type="checkbox" name="permanente_convocatoria" {{ isset($program) ? $program->permanente_convocatoria ? 'checked' : '' : '' }} class="flat"> <strong>Permanente</strong>
												</label>
											</div>
										</div>
										<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<br>
											<div class="checkbox-inline">
												<label class="checkbox-inline">
													<input type="checkbox" name="evaluacion_convocatoria" class="flat" {{ isset($program) ? $program->evaluacion_convocatoria ? 'checked' : '' : '' }}> <strong>Evaluación</strong>
												</label>
											</div>
										</div>
										@if(Auth::user()->hasRole('super-admin'))
											<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
												<br>
												<div class="checkbox-inline">
													<label class="checkbox-inline">
														<input type="checkbox" name="visualizacion_usuario" class="flat" {{ isset($program) ? $program->visualizacion_usuario ? 'checked' : '' : 'checked' }}> <strong>Visualización usuario</strong>
													</label>
												</div>
											</div>
										@endif
                    @if(isset($program))
                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
											<br>
                    <div class="checkbox-inline">
                      <label class="checkbox-inline">
                        <input type="checkbox" name="ocultar_al_finalizar" class="flat" {{ isset($program) ? $program->ocultar_al_finalizar ? 'checked' : '' : '' }}> <strong>Ocultar al cerrar convocatoria</strong>
                      </label>
                    </div>
                    </div>
                    @endif
									</div>
									<br>
									<div class="row">
										<div class="col-md-12 col-xs-12">
											<label>Resumen <span class="required">*</span></label>
											<textarea name="resumen_convocatoria" id="resumen_convocatoria" class="form-control" data-parsley-maxlength="512" value="{{ isset($program) ? $program->resumen_convocatoria : '' }}" required>{{ isset($program) ? $program->resumen_convocatoria : '' }}</textarea>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<label>Imagen <span class="required">*</span></label>
											<div class="x_panel">
												{{-- Content crop and uploader image --}}
												<div id="wrap-program-uploader-tocrop" class="content-area-crop-image">
													<div class="row __fx-main-content">
														<div class="col-xs-12 col-md-6 __fx-column">
															<h5 class="green">Ajustar la imagen:</h5>
															<div class="clearfix">
																<p class="pull-left">Arrastra el cuadrado para cambiar la posición y el tamaño.</p>
																<div id="uploader-program-image" class="uploader-article-img iuc__image-uploader">
																	<!-- Render image uploader -->
																</div>
															</div>
															<div id="image-crop-program" class="iuc__content-image-to-crop image-area-drop" iuc__drop-area-text="Arrastra una imagen aquí" style="width:100%">
																<img id="img-program-tocrop" src="{{ isset($program) ? $program->imagen_convocatoria : '' }}" class="iuc__image-to-crop" />
															</div>
														</div>

														<div class="col-xs-12 col-md-6 __fx-column">
															<h5 class="green">Vista previa:</h5>
															<p class="">Tamaño real de la imagen (590px X 354px) - Peso maximo 8MB</p>
															<div id="preview-article-img" class="iuc__content-image-preview" style="width:100%">
																<img id="program-preview-tocrop" src="" />
															</div>
														</div>
													</div>

													<input type="hidden" name="imagen_convocatoria" value="{{ isset($program) ? $program->imagen_convocatoria : '' }}" required>
													<button id="btn-to-crop" class="btn btn-primary pull-right"><i class="fa fa-scissors fa-lg fa-fw"></i> Recortar imagen</button>
												</div>
											</div>
										</div>
									</div>
                  @if (isset($program))
									<ul id="descriptions-program" class="nav nav-tabs bar_tabs" role="tablist">
										<li role="presentation" class="active"><a href="#tab_description" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Descripción</a>
										</li>
										<li role="presentation"><a href="#tab_terms" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Términos</a>
										</li>
										<li role="presentation"><a href="#tab_acceptance-terms" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Aceptación de términos</a>
										</li>
										<li role="presentation"><a href="#tab_policies" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Políticas</a>
										</li>
                      <li role="presentation">
                        <a href="#tab_faq" role="tab" id="faq-tab" data-toggle="tab" aria-expanded="false">Preguntas frecuentes</a>
                      </li>
                      <li role="presentation"><a href="#tab_upload_files" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Cargar Archivo</a>
                      </li>
                      <li role="presentation"><a href="#tab_categories_and_stages" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Categorías y Etapas</a>
                      </li>

									</ul>
                  @endif

									<div id="section-descriptions-program-content" class="tab-content">
										<div role="tabpanel" class="tab-pane fade active in" id="tab_description" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Descripción <span class="required">*</span></label>
													<div id="CloneDiv" style="display: none;"></div>
													<section id="description-program-editor">
														{{-- Render component pulse-editor --}}
														<input type="hidden" name="descripcion_convocatoria" value="{{ isset($program) ? $program->descripcion_convocatoria : '' }}" required>
													</section>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane fade" id="tab_terms" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Términos</label>
													<section id="terms-program-editor">
														{{-- Render component pulse-editor --}}
														<input type="hidden" name="terminos_convocatoria" value="{{ isset($program) ? $program->terminos_convocatoria : '' }}">
													</section>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane fade" id="tab_acceptance-terms" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Aceptación términos</label>
													<section id="content-acceptance-terms-editor">
														{{-- Render component pulse-editor --}}
														<input type="hidden" name="contenido_aceptacion_terminos" value="{{ isset($program) ? $program->contenido_aceptacion_terminos : '' }}">
													</section>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane fade" id="tab_policies" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Políticas</label>
													<section id="policies-program-editor">
														{{-- Render component pulse-editor --}}
														<input type="hidden" name="politicas_convocatoria" value="{{ isset($program) ? $program->politicas_convocatoria : '' }}">
													</section>
												</div>
											</div>
										</div>
                    @if (isset($program))
                    <div role="tabpanel" class="tab-pane fade" id="tab_faq" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Preguntas frecuentes</label>
                          <div id="faq-container">
                            <div v-if="faq == null">
                              <a href="javascript:void(0)" class="btn btn-success" @click="setFaq()">Registrar pregunta frecuente</a>
                              <table class="table">
                                <thead class="thead-light">
                                  <tr>
                                    <th scope="col">Pregunta</th>
                                    <th scope="col">Acciones</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-for="faq in faqs">
                                    <td>@{{ faq.texto_preguntas_frecuentes }}</td>
                                    <td>
                                      <a href="javascript:void(0)" @click="setFaq(faq)" class="btn btn-success">Editar</a>
                                      <a href="javascript:void(0)" @click="changeStatus(faq)" class="btn" v-bind:class="faq.codigo_estado == 8 ? 'btn-danger' : 'btn-warning'">
                                        @{{ faq.codigo_estado == 8 ? 'Deshabilitar' : 'Habilitar' }}
                                      </a>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>

                            <div v-else>
                              <div class="card">
                                <div class="card-body">
                                  <h3 class="card-title">@{{ faq.codigo_preguntas_frecuentes ? 'Editar' : 'Registrar' }} pregunta frecuente</h3>
                                  <hr />
                                  <a href="javascript:void(0)" @click="faq = null" class="btn btn-danger">Cancelar</a>
                                  <div class="container">
                                    <div class="form-group">
                                      <label>Pregunta <span class="text-danger">*</span></label>
                                      <input type="text" name="" v-model="faq.texto_preguntas_frecuentes" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                      <label>Respuesta <span class="text-danger">*</span></label>
                                      <div id="editor"></div>
                                    </div>
                                    <div class="form-group">
                                      <a href="javascript:void(0)" class="btn btn-primary" @click="saveFaq()" v-bind:disabled="!faq.texto_preguntas_frecuentes">Guardar</a>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
												</div>
											</div>
										</div>
                    @endif
										<div role="tabpanel" class="tab-pane fade" id="tab_upload_files" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Adjuntar archivos</label>
													<section id="upload-program-editor">
                                {{-- Render Upload @backend01--}}
													</section>
												</div>
											</div>
										</div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_categories_and_stages" aria-labelledby="home-tab">
                      <div class="row">
                        <div class="col-md-12 form-group">
                          <section id="render-categories-entities">
                            {{-- Render Tables @backend01 --}}
                          </section>
                        </div>
                      </div>
                    </div>
									</div>
									<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
										<button type="submit" class="btn btn-success btn-block">{{ (isset($program)) ? 'Editar' : 'Guardar' }}</button>
									</div>
								{!! Form::close() !!}
							</div>
						</div>

            <div role="entity" class="tab-pane fade" id="tab_entity" aria-labelledby="tab_entity">
              <div>
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group">
                      <fieldset class="well panel-section-form" name="fields_entity" id="fields_entity">
                        <div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
                          <div class="control-group">
                            <div class="controls">
                              <label>Entidad</label>
                              <div class="input-prepend input-group">
                                <span class="add-on input-group-addon tp_input-search_entity" data-title="Buscador de entidades" data-field="entidad_filter_data"><i class="fa fa-search"></i></span>
                                <input type="email" name="nombre_entidad" id="entidad_filter_data" class="form-control" data-name="id_entidad_filter_data"/>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-7 col-xs-10">
                          <label>Identificacion entidad</label>
                          <input type="text" name="id_entidad" id="id_entidad_filter_data" class="form-control" readonly>
                        </div>
                        <div class="row">
                          <div class="col-md-12 col-sm-12 col-xs-12 text-center">
{{--                            <button type="button" class="btn btn-primary btn-search-user-entity"> Buscar </button>--}}
                            <button type="button" class="btn btn-default btn-clear-user-entity"> Limpiar </button>
                            <button type="button" class="btn btn-success btn-add-user-entity"> Añadir </button>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                </div>
                <!-- start transactions -->
                <div class="table-responsive">
                  <table id="entity-search-table" class="table table-striped table-bordered" data-page-length="15">
                    <thead>
                    <tr class="headings">
                      <th class="column-title">Nombre</th>
                      <th class="column-title">Nick Name</th>
                      <th class="column-title">Ciudad</th>
                      <th class="column-title">Codigo</th>
                      <th class="column-title">Acciones</th>
                    </tr>
                    </thead>
                    <tbody >
                    <!-- Render view  -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

			{{-- start asignaciones --}}
			<div role="assignment" class="tab-pane fade" id="tab_asignaciones" aria-labelledby="tab_asignaciones">
					{{-- <div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h2>Asignación automática de evaluador </h2>
							<small>Para fase de registro</small>
							<div class="x_content">
								<section class="panel">
									<div id="assignment-auto-jury">
										<div class="panel-body">
											<div class="well">
												{!! Form::open(array('route' => null,'id' => 'form-assignment-auto-jury', 'class' =>'form-horizontal form-label-left', 'novalidate' => '', 'data-parsley-validate' => '')) !!}
													<div class="row justity-concent-center">
														<div class="col-sm-12 col-xs-12 col-md-4 col-md-offset-4 item">
															<label>Correo <span class="required">*</span></label>
															<input
																type="email"
																class="form-control"
																placeholder="Email"
																id="email"
																name="email"
																value="{{ $evaluador->asignacion_auto_evaluador? : '' }}"
																required="required"
															>
															<span id='error-email' class="alert alert-danger"></span>
														</div>
													</div>
													<div class="row">
														<div class="col-md-7 col-md-offset-5">
															<button type="submit" class="btn btn-dark add-jury">Agregar</button>
														</div>
													</div>
												{!! Form::close() !!}
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div> --}}
				<div class="row">
					<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
						<h6>Asignaciones automáticas para fase registro</h6>
						<div id="search-users-evaluator">
							{{-- Render id=search-users-evaluator-tpl --}}
						</div>
					</div>
				</div>
				<div id="wrapper-render-users">
					<div class="row">
						<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
							<!-- start user -->
							<div id="avaluator-list">
								@include('public.noresults', ['message' => 'No hay resultados.'])
								<ul id="list-content-evaluator" class="list-unstyled msg_list browse-list-user">
									{{-- Render user-evaluator-list-tpl --}}
								</ul>
							</div>
							<!-- end user -->
						</div>
					</div>
				</div>
			</div>
			{{-- End asignaciones --}}

            <div role="results" class="tab-pane fade" id="tab_results" aria-labelledby="tab_results">
              <div>
                <div class="row">
                  <div class="col-xs-12">
                    {!! Form::open(array('route' => null,'id' => 'program-add-results', 'class' =>'form-horizontal')) !!}
                    <div class="form-group">
                      <fieldset class="well panel-section-form" name="fields_results" id="fields_results">
                        <div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
                          <div class="control-group">
                            <div class="controls">
                              <label>Resultados</label>
                              @if (isset($program))
                                @if($program->tipo_convocatoria == 2)
                                  <input type="number" name="numero_resultados" id="numero_resultados" class="form-control" data-name="numero_resultados" value="{{$program->contador_inscritos}}" required/>
                                  <br>
                                  <button type="submit" class="btn btn-success btn-add-results"> Editar</button>
                                @else
                                  <input type="number" name="numero_resultados" id="numero_resultados" class="form-control" data-name="numero_resultados" readonly/>
                                @endif
                              @endif

                            </div>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                    {!! Form::close() !!}
                  </div>
                </div>
              </div>
            </div>
						<div role="phase" class="tab-pane fade" id="tab_phases" aria-labelledby="phase-tab">
							<div id="wrapper-render-phases">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group">
                      <!-- Table phases -->
                      <fieldset class="well panel-section-form" name="fields_themes_course" id="fields_themes_course">
                        <div class="form-group">
                          <div class="row">
                            <div class="col-md-12 col-xs-12" >
                              <div class="x_panel">
                                <div class="text-left">
                                  <button id="add-program-phase" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Agregar</button>
                                </div>
                              </div>
                              <br>
                              <div class="x_panel">
                                <div class="x_panel" >
                                  <div class="row">
                                    <div class="col-lg-11 col-md-10 col-sm-10 col-xs-8">
                                      <h4 class="panel-title"><strong>Nombre</strong></h4>
                                    </div>
                                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4">
                                      <div class="text-center">
                                        <h4 class="panel-title"><strong>Acción</strong></h4>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
                                  {{-- Render item them phases id:item-phases-tpl --}}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                      <!-- end Table phases-->
                    </div>
                  </div>
                </div>
							</div>
						</div>
						@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
							<div role="channel" class="tab-pane fade" id="tab_channels" aria-labelledby="channel-tab">
								<div id="section-channel-content">
									{{-- Tempalete id=multiselect-channel-content-tpl --}}
								</div>
							</div>
						@endif
						<div role="users" class="tab-pane fade" id="tab_users" aria-labelledby="users-tab">
							<div class="x_content">
								<div id="program-user-filter-content" class="well">
									{!! Form::open(array('route' => null,'id' => 'program-user-filter', 'class' =>'form-horizontal')) !!}
										<div class="row">
											<div class="col-md-4 col-md-offset-2 col-sm-6 col-xs-12">
												<label>Correo electrónico</label>
												<input type="text" name="correo_electronico" id="channel_user_correo_electronico" class="form-control">
											</div>
											<div class="col-md-4 col-sm-6 col-xs-12">
												<label>Nombre</label>
												<input type="text" name="nombre_usuario" id="channel_user_nombre_usuario" class="form-control">
											</div>
										</div>
										<br>
										<div class="text-center">
											<button type="button" class="btn btn-primary btn-search"> Buscar</button>
											<button type="button" class="btn btn-default clean-filters"> Limpiar</button>
										</div>
									{!! Form::close() !!}
								</div>
							</div>
							<div class="row">
								<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
									<button type="button" class="btn btn-success btn-add"> Agregar</button>
									<div id="search-users-toggle">
										{{-- Render id=search-users-toggle-tpl --}}
									</div>
								</div>
							</div>
							<div id="wrapper-render-users">
								<div class="row">
									<div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
										<!-- start userchannel -->
										<ul class="inline-list-grid">
											<li>
												<div class="wrap-result-count">
													{{-- Render browse result count template --}}
												</div>
											</li>
											<li>
												<div class="wrap-pagination">
													{{-- Render Pagination--}}
												</div>
											</li>
										</ul>
										<div id="article-list">
											@include('public.noresults', ['message' => 'No hay resultados.'])
											<ul id="list-content-article" class="list-unstyled msg_list browse-list-userchannel">
												{{-- Render userchannel list --}}
											</ul>
										</div>
										<ul class="inline-list-grid">
											<li>
												<div class="wrap-result-count">
													{{-- Render browse result count template --}}
												</div>
											</li>
											<li>
												<div class="wrap-pagination">
													{{-- Render Pagination--}}
												</div>
											</li>
										</ul>
										<!-- end userchannel -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Template search users evaluator --}}
	<script type="text/template" id="search-users-evaluator-tpl">
		<div class="x_content">
			<div id="users-search-toggle" class="well">
				{!! Form::open(array('route' => null, 'id' => 'form-user', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}
				<div class="row">
					<div class="col-md-5 col-sm-5 col-xs-12 form-group has-feedback">
						<div class="control-group">
							<div class="controls">
								<label>Correo electrónico evaluador</label>
								<div class="input-prepend input-group">
									<span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_mentor_filter_data"><i class="fa fa-search"></i></span>
									<input type="email" name="email" id="correo_electronico_mentor_filter_data" class="form-control correo_electronico_user" data-name="nombre_mentor_filter_data" data-role="evaluador"/>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 col-sm-5 col-xs-12">
						<label>Nombre evaluador</label>
						<input type="text" name="nombre_evaluador" id="nombre_mentor_filter_data" class="form-control" readonly>
					</div>
					<div class="col-md-2 col-sm-2 col-xs-2 tp_align-btn">
						<button type="submit" class="btn btn-success btn-sm" data-field="correo_electronico_filter_data" title="Agregar usuario"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</script>

	{{-- template evaluator list --}}
	<script type="text/template" id="user-evaluator-list-tpl">
		<% if(typeof(codigo_usuario) !== "undefined"){ %>
			<li>
				<div class="row">
					<div class="col-md-10 col-sm-10 col-xs-9">
						<div class="tp_content-list">
							<a
								href="<%= window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_usuario }) ) %>"
								class="hover-list"
							>
								<span class="image">
									<img
										src="<%= imagen_usuario %>"
										onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')"
										alt="img"
									/>
								</span>
								<span>
									<span>
										<strong class="text-capitalize"><%- nombre_usuario.toLowerCase() %></strong>
									</span>
									<span class="message">
										<i class="fa fa-at"></i> <%- correo_electronico %><br>
										<div class="tp_hide-information">
											<i class="fa fa-user"></i> <%- nickname_usuario %>
										</div>
									</span>
								</span>
							</a>
						</div>
					</div>
					<div class="col-md-2 col-sm-2 col-xs-3">
						<div class="tp_button-group pull-right text-right">
							<div class="btn-group tp_btn-group">
								<button
									type="button"
									class="btn btn-default dropdown-toggle"
									title="Opciones"
									data-toggle="dropdown"
									aria-expanded="false"
								>
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<li>
										<a
											href="#"
											title="Eliminar usuario"
											data-action="eliminar"
											data-email="<%- correo_electronico %>"
											data-name="<%- nombre_usuario %>"
											class="danger"
										>
											<i class="fa fa-trash"></i>
											Eliminar
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</li>
		<% } %>
	</script>

	{{-- Template action assignment evaluator --}}
	<script type="text/template" id="confirm-evaluator-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> a <strong><%- nombre_usuario %></strong> como evaluador?</p>
	</script>

	{{-- template url external program --}}
	<script type="text/template" id="url-external-program-tpl">
		<div id="url-external-section">
			<label>Url externa <span class="required">*</span></label>
			<input type="text" name="url_externa_convocatoria" id="url_externa_convocatoria" class="form-control"  value="{{ isset($program) ? $program->url_externa_convocatoria : '' }}" required>
		</div>
	</script>

	{{-- template survey program --}}
	<script  type="text/template" id="survey-program-tpl">
		<div id="survey-section">
			<label>Encuesta <span class="required">*</span></label>
      <select name="codigo_encuesta" id="codigo_encuesta" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('formulario.index'))%>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-initial-value="{{ isset($program) ? $program->codigo_encuesta : '' }}" style="width: 100%" value="{{ isset($program) ? $program->codigo_encuesta : '' }}" data-param="{{ isset($program) ? $program->codigo_encuesta : '' }}" required>
      </select>
		</div>
	</script>

  {{-- template stage program --}}
  <script  type="text/template" id="stage-program-tpl">
    <div id="stage-section">
      <label>Etapa</label>
      <select name="codigo_etapa" id="codigo_etapa" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('convocatoria.stage'))%>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-initial-value="{{ isset($program) ? $program->codigo_etapa_proyecto : '' }}" style="width: 100%" value="{{ isset($program) ? $program->codigo_etapa_proyecto : '' }}" data-param="{{ session('institution')->codigo_institucion }}">
      </select>
    </div>
  </script>

	{{-- section form program phases attachment --}}
	<script type="text/template" id="form-modal-phase-attachment-tpl">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group">
					<div class="row">
						<div class="col-md-12 col-xs-12" >
							<div class="x_panel">
								<form id="form-modal-all">
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <input type="hidden" id="id_resource_phase" name="id_resource_phase" value="<%- typeof codigo_adjunto !== 'undefined' ? codigo_adjunto : '' %>">
                      <label>Tipo de contenido</label>
                      <select name="tipo_contenido_phase" id="tipo_contenido_phase" class="form-control">
                        <% if(typeof codigo_adjunto !== 'undefined') { %>
                          <option value="1" selected> Adjunto </option>
                        <% } else { %>
                          <option value=""> Seleccione... </option>
                          <option value="1"> Adjunto </option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <label>Tipo de adjunto</label>
                      <select name="tipo_adjunto_phase" id="tipo_adjunto_phase" class="form-control">
                        <% if(typeof codigo_tipo_adjunto !== 'undefined') { %>
                            <option selected value="<%- codigo_tipo_adjunto %>"> Link </option>
                         <% } else { %>
                            <option value=""> Seleccione... </option>
                            <option value="8"> Link </option>
                         <% } %>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <label>Tipo de recurso</label>
                      <select name="tipo_recurso_phase" id="tipo_recurso_phase" class="form-control">
                        <option value=""> Seleccione... </option>
                        @if (isset($resourcesPhases))
                          @foreach ($resourcesPhases as $resource)
                            <option value="{{ $resource->codigo_tipo_recurso_fase  }}" <%- typeof tipo_recurso_phase !== 'undefined' ? tipo_recurso_phase === {{  $resource->codigo_tipo_recurso_fase }} ? 'selected' : '' : '' %> > {{ $resource->nombre_tipo_recurso_fase  }} </option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <label>Titulo</label>
                      <input name="nombre_adjunto" id="nombre_adjunto" class="form-control" value="<%- typeof nombre_adjunto !== 'undefined' ? nombre_adjunto : '' %>">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <label>Descripcion de recurso</label>
                      <textarea name="descripcion_adjunto_fase" id="descripcion_adjunto_fase" class="form-control" value="<%- typeof descripcion_adjunto_fase !== undefined ? descripcion_adjunto_fase ? descripcion_adjunto_fase : '' : '' %>"><%- typeof descripcion_adjunto_fase !== undefined ? descripcion_adjunto_fase ? descripcion_adjunto_fase : '' : '' %></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <label>Url</label>
                      <input name="direccion_adjunto" id="direccion_adjunto" class="form-control" value="<%- typeof direccion_adjunto !== 'undefined' ?  direccion_adjunto : '' %>">
                    </div>
                  </div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>

  <script type="text/template" id="form-modal-phase-tpl">
    <div class="row">
      <div class="col-xs-12">
        <div class="form-group">
          <div class="row">
            <div class="col-md-12 col-xs-12" >
              <div class="x_panel">
                <form id="form-modal-all">
                  <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <label for="nombre_fase">Título <span class="required">*</span></label>
                      <input type="text" name="nombre_fase" id="nombre_fase" class="form-control" value="<%- nombre_fase %>" required>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <label for="fecha_inicio_fase">Fecha inicio <span class="required">*</span></label>
                      <input type="text" name="fecha_inicio_fase" id="fecha_inicio_fase" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" value="<%- fecha_inicio_fase %>" required>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <label for="fecha_fin_fase">Fecha fin <span class="required">*</span></label>
                      <input type="text" name="fecha_fin_fase" id="fecha_fin_fase" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" value="<%- fecha_fin_fase %>" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <label for="codigo_privacidad_contenido">Privacidad <span class="required">*</span></label>
                      <select name="codigo_privacidad_contenido" id="codigo_privacidad_contenido" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('privacidad.index'))%>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-initial-value="<%- codigo_privacidad_contenido %>" data-denied="<%- window._tp.typePrivacy['private']%>" data-lang="program.phases.privacy" style="width: 100%" value="<%- codigo_privacidad_contenido %>" required>
                      </select>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <label for="codigo_evaluacion_emprendimiento">Evaluación emprendimiento</label>
                      <select name="codigo_evaluacion_emprendimiento" id="codigo_evaluacion_emprendimiento" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('evaluacion.emprendimiento'))%>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-initial-value="<%- codigo_evaluacion_emprendimiento %>" style="width: 100%" value="<%- codigo_evaluacion_emprendimiento %>">
                      </select>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                      <label for="rango_superior_evaluacion">Rango sup. eval.</label>
                      <input type="number" class="form-control text-center" name="rango_superior_evaluacion" id="rango_superior_evaluacion"  min="0" step="5" value="<%- rango_superior_evaluacion ? rango_superior_evaluacion : 0 %>">
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                      <label for="rango_inferior_evaluacion">Rango inrior. eval.</label>
                      <input type="number" class="form-control text-center" name="rango_inferior_evaluacion" id="rango_inferior_evaluacion"  min="0" step="5" value="<%- rango_inferior_evaluacion ? rango_inferior_evaluacion : 0 %>">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-2 col-sm-6 col-xs-12">
                      <label for="numero_maximo_evaluadores">Núm. evaluadores</label>
                      <input type="number" class="form-control text-center" name="numero_maximo_evaluadores" id="numero_maximo_evaluadores"  min="0" max="15" value="<%- numero_maximo_evaluadores ? numero_maximo_evaluadores : 0 %>">
                    </div>
                    <div class="col-md-10 col-sm-12 col-xs-12">
                      <br>
                      <div class="checkbox-inline">
                        <label class="checkbox-inline">
                          <input type="checkbox" name="asignacion_especialidad" class="flat" <%- asignacion_especialidad ? 'checked' : '' %> > <strong>Asignación especialidad de evaluador</strong>
                        </label>
                      </div>
                      <div class="checkbox-inline">
                        <label class="checkbox-inline">
                          <input type="checkbox" name="fase_entidad_editable" class="flat" <%- fase_entidad_editable ? 'checked' : '' %> > <strong>Emprendimiento editable por evaluador</strong>
                        </label>
                                    </div>
                      <div class="checkbox-inline">
                        <label class="checkbox-inline">
                          <input type="checkbox" name="fase_registro" class="flat" <%- fase_registro ? 'checked' : '' %> > <strong>Fase registro</strong>
                        </label>
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-4 col-sm-8 col-xs-12">
                      <div class="checkbox-inline">
                        <label class="checkbox-inline">
                          <input type="checkbox" name="edicion_formulario_registro" id="edicion_formulario_registro" class="flat" <%- edicion_formulario_registro ? 'checked' : '' %> > <strong>Edición formulario registro</strong>
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <div id="registry-form-edition-date">
{{--                         Render id:item-registry-form-edition-date-tpl--}}
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12 col-xs-12">
                      <label>Resumen <span class="required">*</span></label>
                      <textarea name="resumen_fase" id="resumen_fase" class="form-control" data-parsley-maxlength="200" value="<%- resumen_fase %>" required><%- resumen_fase %></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 form-group">
                      <label>Descripción <span class="required">*</span></label>
                      <section id="description-fase-editor">
{{--                         Render component pulse-editor--}}
                        <input type="hidden" name="descripcion_fase" required>
                      </section>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </script>

	<script type="text/template" id="item-registry-form-edition-date-tpl">
		<div id="form-edition-date">
			<label for="fecha_edicion_formulario_registro">Fecha edición formulario <span class="required">*</span></label>
			<input type="text" name="fecha_edicion_formulario_registro" id="fecha_edicion_formulario_registro" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" required>
		</div>
	</script>

  <script type="text/template" id="categories-and-entities-tables-tpl">
    <div class="x_panel">
      <div class="row">
        <div class="col-md-9 col-sm-9 col-xs-12">
          <label>Categorías</label>
          <select multiple="multiple" class="keepOrder my-select" id="my-select" name="my-select[]" data-route="convocatoria.convocatoriacategoria" data-params='{"categoria":0}' data-id="{{ isset($program) ? $program->codigo_convocatoria : ''}}">
            @if (isset($categoriesProgram))
              @foreach ($categoriesProgram as $category)
                <option value="{{ $category->codigo_categoria_proceso  }}" {{ $category->comparator === true ? 'selected' : '' }}>{{ $category->nombre_categoria_proceso }}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-9 col-sm-9 col-xs-12">
          <label>Etapas</label>
          <select multiple="multiple" class="keepOrder my-select" id="my-select" name="my-select[]" data-route="convocatoria.convocatoriaetapa" data-params='{"etapa":0}' data-id="{{ isset($program) ? $program->codigo_convocatoria : ''}}">
            @if (isset($stagesProgram))
              @foreach($stagesProgram  as $stage)
                <option value="{{ $stage->codigo_etapa_proyecto  }}" {{ $stage->comparator === true ? 'selected' : '' }}>{{ $stage->nombre_etapa_proyecto }}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
    </div>
  </script>

	{{-- template phases --}}
	<script type="text/template" id="item-phases-tpl">
    <div class="panel">
      <a class="panel-heading collapsed attachment-accordion" role="tab" id="heading<%- codigo_fase %>" data-toggle="collapse" data-parent="#accordion" href="#collapse<%- codigo_fase %>" aria-expanded="false" aria-controls="collapseOne">
        <div class="row">
          <div class="col-lg-11 col-md-10 col-sm-10 col-xs-8">
            <div class="pull-left">
              <i class="fa fa-chevron-right fa-pull-left collaps-arrow"></i>
            </div>
            <div class="media-body">
              <h4 class="panel-title ellipsis">
                <%- nombre_fase %>
              </h4>
            </div>
          </div>
          <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4">
            <div class="text-center">
              <button title="Editar fase" id="<%-codigo_fase %>" class="btn btn-default btn-xs primary edit-phase close-hover-circle">
                <i class="glyphicon glyphicon-pencil blue"></i>
              </button>
              <% if( typeof codigo_estado !== 'undefined' && codigo_estado == window._tp.getState['active']) { %>
              <button title="Inactivar fase" class="btn btn-default btn-xs active-phase close-hover-circle"><i class="glyphicon glyphicon-ok green"></i></button>
              <% }else{ %>
              <button title="Activar fase" class="btn btn-default btn-xs inactive-phase close-hover-circle"><i class="glyphicon glyphicon-remove red"></i></button>
              <% } %>
            </div>
          </div>
        </div>
      </a>
      <div id="collapse<%- codigo_fase %>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<%- codigo_fase %>" aria-expanded="false" style="height: 0px;">
        <div class="panel-body">
          <p class="text-justify">Recursos</p>
          <div class="text-left">
            <button title="Agregar adjunto" class="btn btn-default btn-xs add-attachment-phase close-hover-circle"><i class="glyphicon glyphicon-plus green"></i> Agregar</button>
          </div>
          <div class="dd" id="nestable">
            <ol class="dd-list">
              {{-- Render list attachment id::item-attachment-phase-tpl --}}
            </ol>
          </div>
        </div>
      </div>
    </div>
	</script>

  <script type="text/template" id="item-attachment-phase-tpl">
    <div class="dd-handle dd3-handle">
      <i class="fa fa-reorder"></i>
    </div>
    <div class="dd3-content">
      <div class="col-md-4 col-sm-4 col-xs-4">
        <div class="collapsed-item-content ellipsis">
          <a href="<%- direccion_adjunto %>"><%- nombre_adjunto %></a>
        </div>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-4">
        <p class="collapsed-item-content ellipsis">
          <a href="<%- direccion_adjunto %>"><%- descripcion_adjunto_fase %></a>
        </p>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-4">
        <div class="text-right">
          <button title="Editar recurso" id="<%- codigo_adjunto %>" class="btn btn-default btn-xs primary edit-phases close-hover-circle">
            <i class="glyphicon glyphicon-pencil blue"></i>
          </button>
{{--          <% if( typeof(codigo_estado) === "undefined" || (codigo_estado == window._tp.getState['active']) ) { %>--}}
{{--          <button title="Inactivar fase" class="btn btn-default btn-xs active-phase close-hover-circle"><i class="glyphicon glyphicon-ok green"></i></button>--}}
{{--          <% }else{ %>--}}
{{--          <button title="Activar fase" class="btn btn-default btn-xs inactive-phase close-hover-circle"><i class="glyphicon glyphicon-remove red"></i></button>--}}
{{--          <% } %>--}}
        </div>
      </div>
    </div>
  </script>

	{{-- section phase active --}}
	<script type="text/template" id="phase-active-tpl">
		<p>Realmente desea realizar esta acción para esta fase?</p>
	</script>

	<script type="text/template" id="user-program-list-tpl">
		<% if(typeof(codigo_usuario) !== "undefined"){ %>
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-9">
					<div class="tp_content-list">
						<a href="<%= window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_usuario }) ) %>" class="hover-list">
						  	<span class="image">
								<img src="<%- typeof imagen_usuario != 'undefined' ? imagen_usuario : '' %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
						  	</span>
						  	<span>
								<span>
									<strong class="text-capitalize"><%- user_name.toLowerCase() %></strong>
								</span>
								<span class="message">
									<i class="fa fa-at"></i> <%- correo_electronico %><br>
									<div class="tp_hide-information">
										<i class="fa fa-user"></i> <%- nickname_usuario %>
									</div>
								</span>
							</span>
						</a>
					</div>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-3">
					<div class="tp_button-group pull-right text-right">
						<div class="btn-group tp_btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-ellipsis-v"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu">
								{{-- <li>
									<a href="#" title="Eliminar usuario" class="danger"><i class="fa fa-trash"></i> Eliminar</a>
								</li> --}}
								<li>
									<a href="#"> No hay acciones</a>
								</li>
							</ul>
						</div>
						<% if(!_.isNull(nombre_ciudad)){ %>
							<div class="time tp_hide-information">
								<i class="fa fa-map-marker"></i> <%- nombre_ciudad %>
							</div>
						<% } %>
					</div>
				</div>
			</div>
		<% } %>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-entity-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
					<li><a href="#" class="btn-unlink-entities" role="button" data-entidad="<%- codigo_entidad %>" data-convocatoria="<%- codigo_convocatoria %>"><i class="fa fa-window-close"></i> Desvincular </a></li>
			</ul>
		</div>
	</script>

	{{-- Template search users toggle --}}
	<script type="text/template" id="search-users-toggle-tpl">
		<div class="x_content">
			<div id="users-search-toggle" class="well">
				{!! Form::open(array('route' => null, 'id' => 'form-user-program', 'class' => 'form-horizontal', 'data-parsley-validate' =>'')) !!}
					<div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
							<div class="control-group">
								<div class="controls">
									<label>Correo electrónico <span class="required">*</span></label>
									<div class="input-prepend input-group">
										<span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_filter_data"><i class="fa fa-search"></i></span>
										<input type="email" name="correo_electronico" id="correo_electronico_filter_data" class="form-control correo_electronico_user" data-name="nombre_usuario_filter_data"/>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-5 col-md-6 col-sm-5 col-xs-12">
							<label>Nombre</label>
							<input type="text" name="nombre_usuario" id="nombre_usuario_filter_data" class="form-control" readonly>
						</div>
						<div class="col-lg-3 col-md-2 col-sm-3 col-xs-12 tp_align-btn">
							<button type="submit" class="btn btn-success btn-sm" data-field="correo_electronico_filter_data" title="Agregar usuario"><i class="fa fa-plus"></i></button>
							<button type="button" class="btn btn-default btn-sm btn-create" data-title="Crear usuario" title="Crear usuario" data-program="{{ isset($program) ? $program->codigo_convocatoria : '' }}"> Crear</button>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</script>

	{{-- Template section option channel users --}}
	<script type="text/template" id="form-users-modal-tpl">
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Correo electrónico</label>
				<input type="text" name="correo_electronico_datatable" id="correo_electronico_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nombre</label>
				<input type="text" name="nombre_usuario_datatable" id="nombre_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nickname</label>
				<input type="text" name="nickname_usuario_datatable" id="nickname_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Ciudad</label>
				<select name="codigo_ciudad_datatable" id="codigo_ciudad_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
				</select>
			</div>
		</div>
		<br>
		<div class="text-center">
			<button type="button" class="btn btn-primary btn-search-user"> Buscar</button>
			<button type="button" class="btn btn-default btn-clear-user"> Limpiar</button>
		</div>
		<br>
		<div class="table-responsive">
			<table id="user-list-search-table" class="table table-striped table-bordered" data-page-length="15">
				<thead>
				<tr class="headings">
					<th class="column-title">Nombre</th>
					<th class="column-title">Nick Name</th>
					<th class="column-title">Correo</th>
					<th class="column-title">Ciudad</th>
				</tr>
				</thead>
				<tbody >
				<!-- Render view  -->
				</tbody>
			</table>
		</div>
	</script>


	{{-- Template modal search entity --}}
	<script type="text/template" id="form-users-entity-modal-tpl">
		<div class="row">
		  <div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nickname</label>
				<input type="text" name="nickname_usuario_entity_datatable" id="nickname_usuario_entity_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nombre entidad</label>
				<input type="text" name="nombre_usuario_entity_datatable" id="nombre_usuario_entity_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Ciudad</label>
				<select name="codigo_ciudad_entity_datatable" id="codigo_ciudad_entity_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
				</select>
			</div>
		</div>
		<br>
		<div class="text-center">
			<button type="button" class="btn btn-primary btn-search-entity-user"> Buscar</button>
			<button type="button" class="btn btn-default btn-clear-entity-user"> Limpiar</button>
		</div>
		<br>
		<div class="table-responsive">
			<table id="user-entity-list-search-table" class="table table-striped table-bordered" data-page-length="15">
				<thead>
				<tr class="headings">
					<th class="column-title">Nombre</th>
					<th class="column-title">Nick Name</th>
					<th class="column-title">Ciudad</th>
					<th class="column-title">Codigo</th>
				</tr>
				</thead>
				<tbody >
				<!-- Render view  -->
				</tbody>
			</table>
		</div>
	</script>

	<script>
		setTimeout(function(){
			$('#description-program-editor').clone().appendTo("#CloneDiv");
			element = document.getElementsByClassName('editor-content-preview x_panel').item(0);
			$("#CloneDiv").remove();
			element1 = document.getElementsByClassName('note-editable').item(0);
			element1.removeChild(element1.children[0]);
			element2 = document.getElementsByClassName('note-editing-area').item(0);
			element2.removeChild(element2.children[0]);
			for (let index = 0; index < element.children.length; index++) {
				cloneElement(element,element1)
			}
		}, 1000);

		function cloneElement(element,element1){
			setTimeout(function(){
				element1.appendChild(element.children[0]);
			}, 200);
		}
  </script>
@stop

@section('scripts')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.min.css" />
  <style>
    .ck-balloon-panel {
      z-index: 9998!important;
    }
  </style>
  <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.min.js"></script>
  @isset($program)
  <script>
    new Vue({
      el: "#faq-container",
      data: {
        program: '{{ $program->codigo_convocatoria }}',
        faqs: [],
        faq: null,
        message: 'Hi!',
        editor: null
      },
      methods: {
        getFaqs: function () {
          $.ajax({
            method: "GET",
            url: "{{ route('convocatoria.faqs.index', $program->codigo_convocatoria) }}",
            success: (data, xhr, status) => {
              this.faqs = data;
            }
          });
        },

        setFaq: function (faq = {}) {
          if (this.editor) { this.editor.destroy(); }
          this.faq = faq;
          setTimeout(() => {
            ClassicEditor
              .create(document.querySelector('#editor'), {
                toolbar: [ 'undo', 'redo', '|', 'bold', 'italic', '|', 'link', 'insertTable', '|', 'numberedList', 'bulletedList' ],
                htmlSupport: {
                  allow: [
                    {
                      name: /.*/,
                      attributes: true,
                      classes: true,
                      styles: true
                    }
                  ]
                },
                htmlEmbed: {
                  showPreviews: true
                },
                placeholder: '¡Escribe la respuesta aquí!'
              })
              .then((newEditor) => {
                this.editor = newEditor;
                this.editor.setData(this.faq.respuesta_preguntas_frecuentes || '');
              })
              .catch( error => {
                console.error( error );
              });
          }, 100);
        },

        saveFaq: function () {
          const question = this.faq.texto_preguntas_frecuentes;
          const answer = this.editor.getData();

          if (!question || question.trim() == '' || !answer || answer.trim() == '') {
            Swal.fire('Campos obligatorios', 'Los campos "Pregunta" y "Respuesta" son obligatorios', 'warning');
            return 0;
          }

          const urls = {
            'save': '{{ route('convocatoria.faqs.store', $program->codigo_convocatoria) }}',
            'update': '{{ route('convocatoria.faqs.update', [$program->codigo_convocatoria, '']) }}/',
          }

          const config = {
            method: this.faq.codigo_preguntas_frecuentes ? 'PATCH': 'POST',
            url: this.faq.codigo_preguntas_frecuentes ? urls.update + this.faq.codigo_preguntas_frecuentes : urls.save,
            contentType: "application/json",
            data: JSON.stringify({
              texto_preguntas_frecuentes: question,
              respuesta_preguntas_frecuentes: answer,
            }),
            success: (data, xhr) => {
              Swal.close();
              Swal.fire({
                title: this.faq.codigo_preguntas_frecuentes ? 'Pregunta frecuente actualizada' : 'Pregunta frecuente creada',
                icon: 'success'
              });
              $("#faqModal").modal('hide');
              this.getFaqs();
              this.faq = null;
            },
            error: () => {
              Swal.fire('Ha ocurrido un error', 'Ha ocurrido un error mientras se realizaba la operación, por favor inténtelo de nuevo', 'danger');
            }
          }

          Swal.fire({
            title: 'Guardando',
            text: 'Por favor espere...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => Swal.showLoading()
          });

          $.ajax(config);
        },

        changeStatus: function (faq) {
          Swal.fire({
            title: 'Realizando cambios',
            text: 'Por favor espere...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => Swal.showLoading()
          });

          $.ajax({
            method: 'DELETE',
            url: '{{ route('convocatoria.faqs.status', [$program->codigo_convocatoria, '']) }}/' + faq.codigo_preguntas_frecuentes,
            success: (data, xhr) => {
              this.getFaqs();
              Swal.fire('Cambios realizados', data.message, 'success');
            }
          })
        }
      },
      mounted() {
        this.getFaqs();
      }
    });
  </script>
  @endisset
  <script>
    $(function () {
      $("[data-toggle='tab']").on('click', function () {
        const id = $(this).attr('id');
        $("#form-program > div.col-md-2.col-md-offset-5.col-sm-4.col-sm-offset-4.col-xs-12 > button").css('display', (id == 'faq-tab' ? 'none' : 'block'));
      })
    });
  </script>
@endsection
