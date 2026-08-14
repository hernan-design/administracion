{{-- views/event/create-edit.blade.php --}}
@extends('event.event')

@section('event.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					@if(isset($event))
						<div class="x_title title">
							<h2><small>Evento</small></h2>
							<ul class="nav navbar-right panel_toolbox">
								<li>
									<a
										href="{{ route('evento.index') }}">
										<i class="fa fa-chevron-left"></i>
										Atrás
									</a>
								</li>
							</ul>
							<div class="clearfix"></div>
						</div>
						<ul id="myTab" class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a
									href="#tab_event"
									id="event-tab"
									role="tab"
									data-toggle="tab"
									aria-expanded="true">
									Evento
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_organizers"
									role="tab"
									id="organizers-tab"
									data-toggle="tab"
									aria-expanded="false">
									Organizadores
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_paticipants"
									role="tab"
									id="paticipants-tab"
									data-toggle="tab"
									aria-expanded="false">
									Participantes
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_assist-control"
									role="tab"
									id="assist-control-tab"
									data-toggle="tab"
									aria-expanded="false">
									Control asistencia
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_sessions"
									role="tab"
									id="sessions-tab"
									data-toggle="tab"
									aria-expanded="false">
									Sesiones
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_benefits"
									role="tab"
									id="benefits-tab"
									data-toggle="tab"
									aria-expanded="false">
									Beneficios
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_guests"
									role="tab"
									id="guests-tab"
									data-toggle="tab"
									aria-expanded="false">
									Invitados
								</a>
							</li>
							@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
								<li role="channel">
									<a
										href="#tab_channels"
										role="tab"
										id="channels-tab"
										data-toggle="tab"
										aria-expanded="false">
										Canales
									</a>
								</li>
							@endif
							<li role="seo">
								<a
									href="#tab_seo"
									id="seo-tab"
									role="tab"
									data-toggle="tab"
									aria-expanded="true">
									SEO
								</a>
							</li>
							<li role="category_stages">
								<a
									href="#tab_category_stages"
									id="category-stages-tab"
									role="tab"
									data-toggle="tab"
									aria-expanded="true">
									Categorías y Etapas
								</a>
							</li>
							<li role="responsable">
								<a
									href="#responsable"
									id="responsable-tab"
									role="tab"
									data-toggle="tab"
									aria-expanded="true">
									Encargado
								</a>
							</li>
						</ul>
					@else
						<div class="x_title">
							<h2>Evento</h2>
							<ul class="nav navbar-right panel_toolbox">
								<li>
									<a href="{{route('evento.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
								</li>
							</ul>
							<div class="clearfix"></div>
						</div>
					@endif
					<div id="myTabContent" class="tab-content">
				  		<div role="tabpanel" class="tab-pane fade active in" id="tab_event" aria-labelledby="event-tab">
							<div class="well">
								{!! Form::open(array('route' => null,'id' => 'form-event', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Nombre <span class="required">*</span></label>
											<input
												type="text"
												name="nombre_evento"
												id="nombre_evento"
												class="form-control"
												value="{{ (isset($event)) ? $event->nombre_evento : '' }}"
												required
												data-parsley-maxlength="100"
											>
										</div>
										<div class="col-md-2 col-sm-4 col-xs-12">
											<label>Fecha inicio <span class="required">*</span></label>
											<input
												type="text"
												name="fecha_evento"
												id="fecha_evento"
												class="form-control datepicker"
												data-inputmask="'mask':'9999-99-99'"
												value="{{ (isset($event)) ? $event->fecha_evento : '' }}"
												required
											>
										</div>
										<div class="col-md-2 col-sm-4 col-xs-12">
											<label>Hora inicio</label>
											<div class="form-group">
								                <div class='input-group date datetimepicker'>
								                    <input
														type='text'
														class="form-control"
														name="hora_inicio_evento"
														id="hora_inicio_evento"
														value="{{ (isset($event)) ? $event->hora_inicio_evento : '' }}"
													>
								                    <span class="input-group-addon">
								                        <i class="glyphicon glyphicon-time"></i>
								                    </span>
								                </div>
								            </div>
										</div>
										<div class="col-md-2 col-sm-4 col-xs-12">
											<label>Fecha fin</label>
											<input
												type="text"
												name="fecha_fin_evento"
												id="fecha_fin_evento"
												class="form-control datepicker"
												data-inputmask="'mask':'9999-99-99'"
												value="{{ (isset($event)) ? $event->fecha_fin_evento : '' }}"
											>
										</div>
										<div class="col-md-2 col-sm-4 col-xs-12">
											<label>Hora fin</label>
											<div class="form-group">
												<div class='input-group date datetimepicker'>
													<input
														type='text'
														class="form-control"
														name="hora_fin_evento"
														id="hora_fin_evento"
														value="{{ (isset($event)) ? $event->hora_fin_evento : '' }}"
													>
													<span class="input-group-addon">
														<i class="glyphicon glyphicon-time"></i>
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Lugar</label>
											<input type="text" name="lugar_evento" id="lugar_evento" class="form-control" value="{{ (isset($event)) ? $event->lugar_evento : '' }}" >
										</div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Url evento organizador</label>
											<input type="text" name="url_evento_organizador" id="url_evento_organizador" class="form-control" value="{{ (isset($event)) ? $event->url_evento_organizador : '' }}" >
										</div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Calendario <span class="required">*</span></label>
											<select type="text" name="codigo_calendario" id="codigo_calendario" class="form-control slc-calendar" required>
												<option value="">Seleccione...</option>
												@foreach ($calendars as $calendar)
													<option value="{{ $calendar->codigo_calendario }}" @if(isset($event) && $calendar->codigo_calendario == $event->codigo_calendario) selected @endif >{{ $calendar->nombre_calendario }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="row">

										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Tipo <span class="required">*</span></label>
											<select type="text" name="codigo_tipo_evento_calendario" id="codigo_tipo_evento_calendario" class="form-control" required>
												<option value="">Seleccione...</option>
												@foreach ($eventType as $value)
													<option value="{{ $value->codigo_tipo_evento_calendario }}"
													@if(isset($event) && $value->codigo_tipo_evento == $event->codigo_tipo_evento)
														selected
													@endif>
													{{ $value->nombre_tipo_evento }}
													</option>
												@endforeach

											</select>
										</div>
										<div class="col-xs-12 col-sm-4 col-md-4">
											<label>Ciudad <span class="required">*</span></label>
											<select name="codigo_ciudad" id="codigo_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad" data-initial-value="{{ (isset($event)) ? $event->codigo_ciudad : '' }}" required>
											</select>
										</div>
										<div class="col-xs-12 col-sm-4 col-md-4">
											<label>Privacidad contenido <span class="required">*</span></label>
											<select type="text" name="codigo_privacidad_contenido" id="codigo_privacidad_contenido" class="form-control" required>
												<option value="">Seleccione...</option>
												@foreach ($privacyContent as $value)
													<option value="{{ $value->id }}" @if(isset($event) && $value->id == $event->codigo_privacidad_contenido) selected @endif>{{ $value->text }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-2 ">
											<div class="checkbox-inline">
												<label class="checkbox-inline">
													<input type="checkbox" name="pago_evento" class="flat" {{ isset($event) ? $event->pago_evento ? 'checked' : '' : '' }}> <strong>Evento Pago</strong>
												</label>
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-2 ">
											<div class="checkbox-inline">
												<label class="checkbox-inline">
													<input type="checkbox" name="participacion_evento" class="flat" {{ isset($event) ? $event->registro_interes_evento ? 'checked' : '' : 'checked' }}> <strong>Participacion evento</strong>
												</label>
											</div>
										</div>
										<div class="col-xs-12 col-md-8 ">
											<label>Descripción <span class="required">*</span></label>
											<textarea name="resumen_evento" id="resumen_evento" class="form-control" data-parsley-maxlength="255" value="{{ isset($event) ? $event->resumen_evento : '' }}" required>{{ isset($event) ? $event->resumen_evento : '' }}</textarea>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6 col-sm-4 col-md-2">
											<label>Cantidad de horas</label>
											<input type="number" name="cantidad_horas" id="cantidad_horas" class="form-control" value="{{ isset($event) ? $event->cantidad_horas : '' }}">
										</div>
										<div class="col-xs-6 col-sm-4 col-md-4">
											<label>Meet</label>
											<select name="inpMeet" id="inpMeet" class="form-control">
												@if (isset($event))
													@if (isset($event->meet))
														<option value="1" selected> Si </option>
														<option value="0"> No </option>
													@else
														<option value=""> Seleccione </option>
														<option value="1"> Si </option>
														<option value="0"> No </option>
													@endif
												@else
													<option value=""> Seleccione </option>
													<option value="1"> Si </option>
													<option value="0"> No </option>
												@endif
											</select>
										</div>
										<div class="col-xs-12 col-sm-8 col-md-6 others_urls_meeting">
											<label>Url de la reunión</label>
											<smal class=""> - Zoom, Teams, etc.</smal>
											<input
												type="text"
												name="others_urls_meeting"
												id="others_urls_meeting"
												class="form-control"
												value="{{ (isset($event)) ? $event->others_urls_meeting : '' }}"
											>
											<p class="green">Si la reunión no será por meet puede ingresar la url que desee aquí</p>
										</div>
									</div>
									<div class="row">
										@if (isset($event))
											<div class="col-md-4 col-sm-4 col-xs-2">
												<label> Url del Evento </label>
												<input type="text" class="form-control" name="" id="" value="{{ session('institution')->url_institucion.'/calendario/evento/'.$event->url_evento }}" readonly>
											</div>
											@if (session('institution')->eventos_facturables)
												<div class="col-md-4 col-sm-4 col-xs-2">
													<label for="numero_factura"> Numero de Factura </label>
													<input type="text" name="numero_factura" id="numero_factura" class="form-control" value="{{ $event->factura }}">
												</div>
												<div class="col-md-4 col-sm-4 col-xs-2">
													<label for="">Valor de la factura</label>
													<input type="number" name="precio_factura" id="precio_factura" class="form-control" value="{{ $event->costo_factura }}">
												</div>
											@endif
										@endif
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-6 col-md-6 ">
											<div class="checkbox-inline">
												<input
                                                    type="checkbox"
                                                    name="activar_recordatorio"
                                                    id="activar_recordatorio"
                                                    class="flat"
                                                    {{ isset($recordatorio[0]->enviar) ? $recordatorio[0]->enviar ? 'checked' : '' : '' }}
                                                >
												<label
                                                    for="activar_recordatorio"
                                                    class="checkbox-inline">
                                                    <strong>Activar recordatorio</strong>
                                                </label>
											</div>
										</div>
									</div>
									<br>
									@if (session('institution')->codigo_institucion == 30)
										<div class="row">
											<div class="col-xs-12 col-sm-6 col-md-6 ">
												<label
													for="facilitator">
													Facilitador
													<span class="required">*</span>
												</label>
												<input
													type="text"
													name="facilitator"
													id="facilitator"
													class="form-control"
													value="{{ (isset($event)) ? $event->facilitator : '' }}"
													required
												/>
											</div>
										</div>
									@endif
									<br>
									<div class="row">
										<div class="col-md-12">
											<label>Imagen</label>
											<div class="x_panel">
												{{-- Content crop and uploader image --}}
												<div id="wrap-event-uploader-tocrop" class="content-area-crop-image">
													<div class="row __fx-main-content">
														<div class="col-xs-12 col-md-6 __fx-column">
															<h5 class="green">Ajustar la imagen:</h5>
															<div class="clearfix">
																<p class="pull-left">Arrastra el cuadrado para cambiar la posición y el tamaño.</p>
																<div id="uploader-event-image" class="uploader-event-img iuc__image-uploader">
																	<!-- Render image uploader -->
																</div>
															</div>
															<div id="image-crop-event" class="iuc__content-image-to-crop image-area-drop" iuc__drop-area-text="Arrastra una imagen aquí" style="width:100%">
																<img id="img-event-tocrop" src="{{ isset($event) ? $event->imagen_evento : '' }}" class="iuc__image-to-crop" />
															</div>
														</div>

														<div class="col-xs-12 col-md-6 __fx-column">
															<h5 class="green">Vista previa:</h5>
															<p class="">Tamaño real de la imagen (780px X 520px)</p>
															<div id="preview-event-img" class="iuc__content-image-preview" style="width:100%">
																<img id="event-preview-tocrop" src="" class="" />
															</div>
														</div>
													</div>

													<input type="hidden" name="imagen_evento" value="{{ isset($event) ? $event->imagen_evento : '' }}" >
													<button id="btn-to-crop" class="btn btn-primary pull-right"><i class="fa fa-scissors fa-lg fa-fw"></i> Recortar imagen</button>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 form-group">
											<label>Contenido <span class="required">*</span></label>
											<div id="CloneDiv" style="display: none;"></div>
											<section id="event-project-editor">
												{{-- Render component pulse-editor --}}
												<input type="hidden" name="descripcion_evento" value="{{ (isset($event)) ? $event->descripcion_evento : '' }}" required>
											</section>
										</div>
									</div>
									<br>
									<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
										<button type="submit" class="btn btn-success btn-block">{{ (isset($event)) ? 'Editar' : 'Guardar' }}</button>
									</div>
								{!! Form::close() !!}
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="responsable" aria-labelledby="home-tab">
							<div class="row">
								<div class="col-xs-12">
								<div class="form-group">
									<fieldset class="well panel-section-form" name="field_responsable_event" id="field_responsable_event">
									<div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
										<div class="control-group">
										<div class="controls">
											<label>Usuario</label>
											<div class="input-prepend input-group">
											<span class="add-on input-group-addon tp_input-search_user_responsable" data-title="Buscador de usuarios" data-field="usuario_responsable_filter_data"><i class="fa fa-search"></i></span>
											<input type="text" name="nombre_entidad" id="usuario_responsable_filter_data" class="form-control" data-name="id_usuario_responsable_filter_data"/>
											</div>
										</div>
										</div>
									</div>
									<div class="col-md-6 col-sm-7 col-xs-10">
										<label>Identificacion usuario</label>
										<input type="text" name="id_usuario_responsable" id="id_usuario_responsable_filter_data" class="form-control" readonly>
									</div>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12 text-center">
										{{--                            <button type="button" class="btn btn-primary btn-search-user-entity"> Buscar </button>--}}
										<button type="button" class="btn btn-success btn-add-user-responsable"> Añadir </button>
										<button type="button" class="btn btn-default btn-clear-user-responsable"> Limpiar </button>
										</div>
									</div>
									</fieldset>
								</div>
								</div>
							</div>
							<div class="table-responsive">
								<table id="user-responsable-search-table" class="table table-striped table-bordered" data-page-length="15">
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
						</div>
            			<div role="tabpanel" class="tab-pane fade" id="tab_category_stages" aria-labelledby="home-tab">
						<div class="row">
							<div class="col-md-12 form-group">
							<div class="x_panel">
								<div class="row">
								<div class="col-md-9 col-sm-9 col-sm-offset-1 col-xs-12">
									<label>Categorías</label>
									<select multiple="multiple" class="keepOrder my-select" id="my-select" name="my-select[]" data-route="evento.eventocategoria" data-params='{"categoria":0}' data-id="{{ isset($event) ? $event->codigo_evento : ''}}">
									@if (isset($eventCategories))
										@foreach ($eventCategories as $category)
										<option value="{{ $category->codigo_categoria_contenido  }}" {{ $category->comparator === true ? 'selected' : '' }}>{{ $category->nombre_categoria_contenido }}</option>
										@endforeach
									@endif
									</select>
								</div>
								</div>
								<br>
								<div class="row">
								<div class="col-md-9 col-sm-9 col-sm-offset-1 col-xs-12">
									<label>Etapas</label>
									<select multiple="multiple" class="keepOrder my-select" id="my-select" name="my-select[]" data-route="evento.eventoetapa" data-params='{"etapa":0}' data-id="{{ isset($event) ? $event->codigo_evento : ''}}">
									@if (isset($eventStages))
										@foreach($eventStages  as $stage)
										<option value="{{ $stage->codigo_etapa_contenido  }}" {{ $stage->comparator === true ? 'selected' : '' }}>{{ $stage->nombre_etapa_contenido }}</option>
										@endforeach
									@endif
									</select>
								</div>
								</div>
							</div>
							</div>
						</div>
            		</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_organizers" aria-labelledby="organizers-tab">
						<div id="wrapper-render-organizers">
							{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-organizers-event', 'data-parsley-validate' => '') ) !!}
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<!-- Table themes course -->
											<fieldset class="well panel-section-form" name="fields_organizers-event" id="fields_organizers-event">
												<div>
													<div class="x_panel">
														<div class="row">
															<div class="col-xs-12">
																<div class="form-group">
																	<div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
																		<div class="control-group">
																			<div class="controls">
																				<label>Organizador</label>
																				<div class="input-prepend input-group">
																					<span class="add-on input-group-addon tp_input-search_organizer" data-title="Buscador de organizadores" data-field="organizador_filter_data"><i class="fa fa-search"></i></span>
																					<input type="email" name="nombre_organizador" id="organizador_filter_data" class="form-control" data-name="id_organizador_filter_data"/>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="col-md-6 col-sm-7 col-xs-10">
																		<label>Identificacion organizador</label>
																		<input type="text" name="id_organizador" id="id_organizador_filter_data" class="form-control" readonly>
																	</div>
																	<div class="row">
																		<div class="col-md-12 col-sm-12 col-xs-12 text-center">
																			<button type="button" class="btn btn-default btn-clear-user-organizer"> Limpiar </button>
																			<button type="button" class="btn btn-success btn-add-user-organizer"> Añadir </button>
																		</div>
																	</div>
																	<br>
																	<div class="table-responsive">
																		<table id="organizer-search-table" class="table table-striped table-bordered" data-page-length="15">
																			<thead>
																			<tr class="headings">
																			<th class="column-title">Nombre</th>
																			<th class="column-title">Nick Name</th>
																			<th class="column-title">Ciudad</th>
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
												<div class="form-group">
													<div class="row">
														<div class="col-md-12 col-xs-12" >
															<div class="x_panel">
																<div class="row">
																	<div class="col-md-4 col-md-offset-4 col-sm-11 col-xs-9">
																		<label>Organizadores <span class="required">*</span></label>
																		<select name="codigo_patrocinador" id="codigo_patrocinador" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('patrocinador.index') }}"  data-placeholder="Seleccione..." placeholder="Seleccione..." data-param="list" style="width: 100%">
																		</select>
																	</div>
																	<div class="col-md-1 col-sm-1 col-xs-2">
																		<div class="round-button">
																			<div class="round-button-circle">
																				<a href="" title="Crear organizador" class="round-button">
																					<i class="fa fa-plus"></i>
																				</a>
																			</div>
																		</div>
																	</div>
																</div>
																<br>
																<div class="text-center">
																	<button type="submit" class="btn btn-success" title="Agregar organizador"><i class="fa fa-plus"></i> Agregar</button>
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12 col-xs-12" >
															<div class="x_panel">
																<div class="row">
																	<div class="col-md-6 col-md-offset-3 col-xs-12" >
																		<div class="x_panel">
																			<div class="row">
																				<div class="col-md-10 col-sm-10 col-xs-9">
																					<h4 class="panel-title">
																						<strong>Nombre</strong>
																					</h4>
																				</div>
																				<div class="col-md-2 col-sm-2 col-xs-3">
																					<h4 class="panel-title text-center">
																						<strong>Acción</strong>
																					</h4>
																				</div>
																			</div>
																		</div>
																		<div class="">
																			<ul class="to_do">
																				{{-- Render item them course id:item-organizers-tpl --}}
																			</ul>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</fieldset>
											<!-- end Table themes coruse-->
										</div>
									</div>
								</div>
							{!! Form::close() !!}
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_paticipants" aria-labelledby="paticipants-tab">
						<div id="participants-event-grid">
							{{-- Render template id="participants-event-grid-tpl" --}}
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_assist-control" aria-labelledby="assist-control-tab">
						<div id="assist-control-event-grid">
							{{-- Render template id="assist-control-event-grid-tpl" --}}
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_sessions" aria-labelledby="sessions-tab">
						<div id="wrapper-render-sessions">
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<!-- Table event sessions -->
										<fieldset class="well panel-section-form" name="fields_themes_course" id="fields_themes_course">
											<div class="form-group">
												<div class="row">
													<div class="col-md-12 col-xs-12" >
														<div class="x_panel">
															<div class="text-left">
																<button id="add-event-session" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Agregar</button>
															</div>
															<br>
															<div class="dd" id="nestable">
																<ol class="dd-list">
																	{{-- Render list sessions id::item-sessions-tpl --}}
																</ol>
															</div>
														</div>
													</div>
												</div>
											</div>
										</fieldset>
										<!-- end Table event sessions -->
									</div>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_benefits" aria-labelledby="benefits-tab">
						<div id="wrapper-render-benefits">
							{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-benefits-event', 'data-parsley-validate' => '') ) !!}
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<!-- Table themes course -->
											<fieldset class="well panel-section-form" name="fields_benefits-event" id="fields_benefits-event">
												<div class="form-group">
													<div class="row">
														<div class="col-md-12 col-xs-12" >
															<div class="x_panel">
																<div class="row">
																	<div class="col-md-4 col-md-offset-3 col-sm-11 col-xs-9">
																		<label>Beneficios <span class="required">*</span></label>
																		<select name="codigo_beneficio_evento" id="codigo_beneficio_evento" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('beneficio.index') }}" data-placeholder="Seleccione un beneficio" placeholder="Seleccione un beneficio" style="width: 100%">
																		</select>
																	</div>
																	<div class="col-md-2 col-sm-3 col-xs-12 tp_align-btn">
																		<button type="submit" class="btn btn-success btn-sm" title="Agregar beneficio"><i class="fa fa-plus"></i></button>
																		<button type="button" class="btn btn-default btn-sm btn-create-benefit" data-title="Crear beneficio" title="Crear beneficio"> Crear</button>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12 col-xs-12" >
															<div class="x_panel">
																<div class="row">
																	<div class="col-md-6 col-md-offset-3 col-xs-12" >
																		<div class="x_panel">
																			<div class="row">
																				<div class="col-md-10 col-sm-10 col-xs-9">
																					<h4 class="panel-title">
																						<strong>Nombre</strong>
																					</h4>
																				</div>
																				<div class="col-md-2 col-sm-2 col-xs-3">
																					<h4 class="panel-title text-center">
																						<strong>Acción</strong>
																					</h4>
																				</div>
																			</div>
																		</div>
																		<div class="">
																			<ul class="to_do">
																				{{-- Render item them course id:item-benefits-tpl --}}
																			</ul>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</fieldset>
											<!-- end Table themes coruse-->
										</div>
									</div>
								</div>
							{!! Form::close() !!}
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_guests" aria-labelledby="guests-tab">
						<div id="guest-event-grid">
							{{-- Render template id="guest-event-grid-tpl" --}}
						</div>
					</div>
					@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
						<div role="tabpanel" class="tab-pane fade" id="tab_channels" aria-labelledby="channels-tab">
							<div id="section-channel-content">
								{{-- Tempalete id=multiselect-channel-content-tpl --}}
							</div>
						</div>
					@endif
					<div role="seo" class="tab-pane fade" id="tab_seo" aria-labelledby="seo-tab">
						<div class="well">
							<div class="row">
								<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
									<label>Título</label>
									<input type="text" class="form-control" value="{{ (isset($event)) ? $event->nombre_evento : '' }}" readonly>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
									<label>Contenido</label>
									<textarea class="form-control" data-parsley-maxlength="200" value="{{ (isset($event)) ? $event->resumen_evento : '' }}" readonly>{{ (isset($event)) ? strip_tags($event->resumen_evento) : '' }}</textarea>
								</div>
							</div>
							<ul id="seo_social" class="nav nav-tabs bar_tabs" data="tabs" role="tablist">
								<li role="general" class="active"><a href="#tab_general" id="general-tab" role="tab" data-toggle="tab" aria-expanded="true">General</a>
								</li>
								<li role="facebook"><a href="#tab_facebook" id="facebook-tab" role="tab" data-toggle="tab" aria-expanded="true">Facebook</a>
								</li>
								<li role="twitter"><a href="#tab_twitter" id="twitter-tab" role="tab" data-toggle="tab" aria-expanded="true">Twitter</a>
								</li>
							</ul>
							<div id="seo_social_content" class="tab-content">
								<div role="general" class="tab-pane fade active in" id="tab_general" aria-labelledby="general-tab">
									<div class="x_panel">
										{!! Form::open(array('route' => null, 'id' => 'form-event-seo-general', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Slug</label>
													<input type="text" name="url_evento" id="url_evento" class="form-control" value="{{ (isset($event)) ? $event->url_evento : '' }}" required>
												</div>
												<div class="col-md-1 col-sm-12 col-xs-12 tp_align-btn">
													<button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="" data-original-title="Recuerda que al modificar esta propiedad de contenido puedes perder los enlaces que hayas publicado antes del cambio."><i class="fa fa-exclamation"></i></button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Url canonical</label>
													<input type="text" name="url_canonica" id="url_canonica" class="form-control" value="{{ (isset($event)) ? $event->url_canonica : '' }}">
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Input Tags</label>
													<input name="tags_evento" id="tags_evento" value="{{ (isset($event)) ? $event->tags : '' }}" class="form-control ui-autocomplete-input"/>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<div class="checkbox-inline">
														<label class="checkbox-inline">
															<input type="checkbox" name="enlaces_motores_busqueda" {{ isset($event) ? $event->enlaces_motores_busqueda ? 'checked' : '' : '' }} class="flat"> <strong>Enlaces motores de busqueda</strong>
														</label>
													</div>
												</div>
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<div class="checkbox-inline">
														<label class="checkbox-inline">
															<input type="checkbox" name="indexar_motores" class="flat" {{ isset($event) ? $event->indexar_motores ? 'checked' : '' : '' }}> <strong>Indexar motores</strong>
														</label>
													</div>
												</div>
											</div>
											<br>
											<div class="row">
												<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
													<button type="submit" class="btn btn-success btn-block">{{ (isset($event)) ? 'Editar' : 'Guardar' }}</button>
												</div>
											</div>
										{!! Form::close() !!}
									</div>
								</div>
								@if(isset($event))
									<div role="facebook" class="tab-pane fade" id="tab_facebook" aria-labelledby="facebook-tab">
										{{-- Render template id="seo-social-tpl" facebook --}}
									</div>
									<div role="twitter" class="tab-pane fade" id="tab_twitter" aria-labelledby="twitter-tab">
										{{-- Render template id="seo-social-tpl" twitter --}}
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Render template participants grid --}}
	<script type="text/template" id="participants-event-grid-tpl">
		<div id="participant-list-event">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-event', 'class' =>'')) !!}
					<div class="row">
		                <div class="col-md-4 col-md-offset-2 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="correo_electronico" name="correo_electronico" class="form-control">
				        </div>
					    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
					        <label>Número de documento</label>
                            <input type="text" name="identificacion_usuario" id="identificacion_usuario" class="form-control">
                     	</div>
                    </div>

                    <div class="text-center">
                  		<button type="button" class="btn btn-primary btn-search">Buscar</button>
						<button type="button" class="btn btn-default btn-clear">Limpiar</button>
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user participants -->
            <div class="table-responsive">
				<table id="participants-event-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr class="headings">
							<th class="column-title">Nombre</th>
							<th class="column-title">Correo electrónico</th>
							<th class="column-title">Número de documento</th>
							<th class="column-title">Número de celular</th>
							<th class="column-title text-center" style="width: 10px">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>


  <script type="text/template" id="button-actions-certificate">
    <div class="btn-group tp_btn-group">
      <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-right" role="menu">
        @if (session('institution')->certificado_evento)
          <li><a href="#" class="btn-certificate" role="button" data-user="<%- codigo_usuario %>" data-user-event="<%- codigo_usuario_evento %>"><i class="fa fa-file-text"></i> Certificado </a></li>
        @else
          <li><a href="#" role="button"><i class="fa fa-file-text"></i> No hay acciones </a></li>
        @endif
      </ul>
    </div>
  </script>

	{{-- Render template assist control grid --}}
	<script type="text/template" id="assist-control-event-grid-tpl">
		<div id="participant-list-event">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-event', 'class' =>'')) !!}
					<div class="row">
		                <div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12 form-group">
				        	<label>Sesiones</label>
				            <select name="numero_sesion" id="numero_sesion" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull( Route.route('evento.sesion.index') ) %>" data-placeholder="Selecciona una sesión" placeholder="Selecciona una sesión" data-param="<%- codigo_evento %>" style="width: 100%">
							</select>
				        </div>
                    </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
			<!-- file upload -->
			<div class="content well" id="project-document">
				<div class="row">
					<div class="small-12 col-md-12 columns">
						<div class="panel-info with-title">
							<div id="project-documents-container" class="editable-item">
								<!--<h5 class="title-panel text-left">
									<i class="fa fa-paperclip"></i> <span class="field-text">Documentos</span>
								</h5>-->
								@if(isset($event))
									<input type="hidden" id="codigo_evento" value="{{ $event->codigo_evento }}">
									<section id="wrap-documents" class="">
										{{-- Render  Documents --}}
									</section>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--end file upload-->

			<!--start all assist-->
			<div class="well">
					<div class="row">
		                <div
							id="buttonAllAssist"
							class="col-sm-12 col-md-4 col-xs-12 col-sm-offset-7 col-md-offset-9 form-group"
						>

				        </div>
                    </div>
		   	</div>
            <!-- and all assist -->

            <!-- start user assist control -->
            <div class="table-responsive">
				<table id="assist-control-event-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Correo electrónico</th>
							<th>Número de documento</th>
							<th>Número de celular</th>
							<th>Asistió</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>

	<script type="text/template" id="edit-project-documents-form-tpl">

		{!! Form::open(array('route' => 'adjunto.evento.index', 'data-abide' => '', 'id' => 'form-edit-project-documents')) !!}
			<div class="row">
				<div
					class="col-xs-12 col-sm-12 col-md-12  text-center"
					style=""
				>
					<h4>
						<i class="fa fa-paperclip"></i>
						Archivos adjuntos
					</h4>

					<p class="">
						En esta sección puedes adjuntar la evidencia de asistencia,
						editar el nombre del archivo o borrar el achivo previamente cargado.
					</p>

				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12">
					<div id="fnuploader-attach-file" class="uploader-attachments"></div>
				</div>
			</div>

				<div
					class="row save-section"
					style="justify-content: center;margin-top: 5px;"
				>
					<div class="">
						{!! Form::submit('Cargar archivos', array('class' => 'btn btn-success btn-block sky-blue btn-shadow radius')) !!}
					</div>
				</div>

		{!! Form::close() !!}

	</script>

	{{-- Template attachments file uploader --}}
<script type="text/template" id="qq-attachments-template">

	<div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Documentos">
		<div
			class="row"
			style="justify-content: center;"
		>
			<div class="clases borradas">
				<div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file btn btn-success btn-block">
					<div>Selecciona tu archivo</div>
				</div>
			</div>
		</div>
		<div id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
			<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Processing dropped files...</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<div class="content-list-attachments scroll-general-v">
			<ul
				class="qq-upload-list-selector qq-upload-list-attachments"
				aria-live="polite"
				aria-relevant="additions removals"
				style="list-style: none;padding: 0;"
			>
				<li
					style="padding: 5px;
						   border-style: dotted;
						   border-width: 1px;"
				>
					<div class="qq-progress-bar-container-selector">
						<div
							role="progressbar"
							aria-valuenow="0"
							aria-valuemin="0"
							aria-valuemax="100"
							class="qq-progress-bar-selector qq-progress-bar"
						>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-12 columns">
							<span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
							<img class="qq-thumbnail-selector" qq-max-size="50" qq-server-scale>
							<a class="preview-link" target="_blank">
								<span class="qq-upload-file-selector qq-upload-file"></span>
							</a>
							<span class="qq-upload-size-selector qq-upload-size"></span>

							<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
							<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
							<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
							<button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
							<button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
							<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
						</div>

					</div>
				</li>
			</ul>
		</div>
	</div>
</script>


	{{-- Render template guest grid --}}
	<script type="text/template" id="guest-event-grid-tpl">
		<div id="participant-list-event">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-guest-event', 'class' =>'')) !!}
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
						<div class="control-group">
							<div class="controls">
							<label>Usuario</label>
							<div class="input-prepend input-group">
								<span class="add-on input-group-addon tp_input-search_entity_invitation" data-title="Buscador de entidades por invitar" data-field="entidad_filter_data_invitation"><i class="fa fa-search"></i></span>
								<input type="text" name="nombre_entidad" id="entidad_filter_data_invitation" class="form-control" data-name="id_entidad_filter_data_invitation"/>
							</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<label>Identificacion usuario</label>
						<input type="text" name="id_entidad_invitation" id="id_entidad_filter_data_invitation" class="form-control" readonly>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<label>Programas</label>
						<select name="codigo_convocatoria" id="codigo_convocatoria" class="form-control select-program choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull( Route.route('convocatoria.index') ) %>" data-placeholder="Selecciona un programa" placeholder="Selecciona un programa" data-param="list" style="width: 100%">
						</select>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<label>Fase</label>
						<select name="phase" id="phase" class="form-control" style="width: 100%">
							<option value=""> Selecciona una fase </option>
						</select>
					</div>
					<br>
					<div class="row">
						<div class="col-xs-12">
							<div id="buttons-action" class="text-center">
								<button type="button" class="btn btn-primary btn-search-user-invitation">Buscar</button>
								<button type="button" class="btn btn-default btn-clear-user-invitation">Limpiar</button>
								<button type="submit" class="btn btn-success btn-send-user-invitation-all" data-event="{{ isset($event) && !empty($event) ? $event->codigo_evento : '' }}">Invitar</button>
							</div>
						</div>
					</div>
				</div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user assist control -->
            <div class="table-responsive">
				<table id="guest-event-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Correo electrónico</th>
							<th>Número de documento</th>
							<th>Número de celular</th>
							<th>Acción</th>
              <th>Codigo</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>


  {{-- Template modal search entity invitation --}}
  <script type="text/template" id="form-users-entity-invitation-modal-tpl">
    <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12">
        <label>Nickname</label>
        <input type="text" name="nickname_usuario_entity_invitation_datatable" id="nickname_usuario_entity_invitation_datatable" class="form-control">
      </div>
      <div class="col-md-3 col-sm-6 col-xs-12">
        <label>Nombre entidad</label>
        <input type="text" name="nombre_usuario_entity_invitation_datatable" id="nombre_usuario_entity_invitation_datatable" class="form-control">
      </div>
      <div class="col-md-3 col-sm-6 col-xs-12">
        <label>Ciudad</label>
        <select name="codigo_ciudad_entity_invitation_datatable" id="codigo_ciudad_entity_invitation_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
        </select>
      </div>
      <div class="col-md-3 col-sm-6 col-xs-12">
        <label>Identificación</label>
        <input type="number" name="codigo_identification_invitation_datatable" id="codigo_identification_invitation_datatable" class="form-control">
      </div>
    </div>
    <br>
    <div class="text-center">
      <button type="button" class="btn btn-primary btn-search-entity-invitation-user"> Buscar</button>
      <button type="button" class="btn btn-default btn-clear-entity-invitations-user"> Limpiar</button>
    </div>
    <br>
    <div class="table-responsive">
      <table id="user-entity-invitation-list-search-table" class="table table-striped table-bordered" data-page-length="15">
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

	{{-- template item organizers --}}
	<script type="text/template" id="item-organizers-tpl">
		<% typeof(codigo_organizador_evento) !== "undefined" || (codigo_organizador_evento = this.model.cid);  %>
		<div class="row">
			<div class="col-md-10 col-sm-10 col-xs-9">
				<h4><%- nombre_patrocinador %></h4>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-3">
				<div class="text-center">
					<div class="btn-group tp_btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
							<i class="fa fa-ellipsis-h"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li>
								<a href="" title="Eliminar organizador" class="remove-theme"><i class="fa fa-trash"></i> Eliminar
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</script>

	{{-- template item benefits --}}
	<script type="text/template" id="item-benefits-tpl">
		<% typeof(codigo_beneficio_evento) !== "undefined" || (codigo_beneficio_evento = this.model.cid);  %>
		<div class="row">
			<div class="col-md-10 col-sm-10 col-xs-9">
				<h4><%- nombre_beneficio_evento %></h4>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-3">
				<div class="text-center">
					<div class="btn-group tp_btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
							<i class="fa fa-ellipsis-h"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							{{-- <li>
								<a href="" title="Eliminar beneficio" class="remove-theme"><i class="fa fa-trash"></i> Eliminar
								</a>
							</li>	 --}}
							<li>
								No hay acciones
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</script>

	{{-- Template section option channel --}}
	<script type="text/template" id="form-modal-tpl">
		<div class="row">
			<div class="col-md-4 col-sm-6 col-xs-12">
				<label>Nombre <span class="required">*</span></label>
				<input type="text" name="nombre_patrocinador" id="nombre_patrocinador" class="form-control" required>
			</div>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Descripción <span class="required">*</span></label>
        <input type="text" name="descripcion_patrocinador" id="descripcion_patrocinador" class="form-control" >
      </div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<label>Imagen <span class="required">*</span></label>
				<input type="file" name="imagen_patrocinador" id="imagen_patrocinador" class="form-control" accept="image/png, image/jpeg" required>
			</div>
		</div>
	</script>

	{{-- template sessions --}}
	<script type="text/template" id="item-sessions-tpl">
		<div class="dd-handle dd3-handle">
			<i class="fa fa-reorder"></i>
		</div>
		<div class="dd3-content">
			<div class="col-md-4 col-sm-4 col-xs-4">
				<div class="collapsed-item-content ellipsis">
					<%- numero_sesion %> Sesión
				</div>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-4">
				<div class="text-right">
					<div class="btn-group tp_btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
							<i class="fa fa-ellipsis-h"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							<%  if(codigo_estado == window._tp.getState['inactive']) { %>
								<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- codigo_sesion_evento %>" data-name="<%- numero_sesion %> Sesión" data-state="<%- codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
								</li>
							<% }

							if(codigo_estado == window._tp.getState['active']) { %>
								<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- codigo_sesion_evento %>" data-name="<%- numero_sesion %> Sesión" data-state="<%- codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
								</li>
							<% } %>
							<li>
								<a href="" title="Editar sesión" class="edit-session"><i class="fa fa-pencil"></i> Editar
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</script>

	{{-- section form event session --}}
	<script type="text/template" id="form-modal-session-event-tpl">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group">
					<div class="row">
						<div class="col-md-12 col-xs-12" >
							<div class="x_panel">
								<form id="form-modal-all">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label for="fecha_inicio_sesion">Fecha inicio <span class="required">*</span></label>
											<input type="text" name="fecha_inicio_sesion" id="fecha_inicio_sesion" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" value="<%- fecha_inicio_sesion %>" required>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label>Hora inicio <span class="required">*</span></label>
											<div class="form-group">
												<div class='input-group date datetimepicker dateTimeInitHour'>
													<input type='text' class="form-control" name="hora_inicio_sesion" id="hora_inicio_sesion" value="<%- hora_inicio_sesion %>" required/>
													<span class="input-group-addon">
														<i class="glyphicon glyphicon-time"></i>
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label for="fecha_fin_sesion">Fecha fin <span class="required">*</span></label>
											<input type="text" name="fecha_fin_sesion" id="fecha_fin_sesion" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" value="<%- fecha_fin_sesion %>" required>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<label>Hora fin <span class="required">*</span></label>
											<div class="form-group">
												<div class='input-group date datetimepicker dateTimeEndHour'>
													<input type='text' class="form-control" name="hora_fin_sesion" id="hora_fin_sesion" value="<%- hora_fin_sesion %>" required/>
													<span class="input-group-addon">
														<i class="glyphicon glyphicon-time"></i>
													</span>
												</div>
											</div>
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

	{{--  Template confirm session --}}
	<script type="text/template" id="confirm-session-event-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la <strong><%- event_name %></strong>?</p>
	</script>

	{{--  Template confirm invitation --}}
	<script type="text/template" id="confirm-invitation-event-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> a <strong><%- user_name %></strong>?</p>
	</script>

	{{--  Template create benefit --}}
	<script type="text/template" id="create-benefit-event-tpl">
		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
				<label>Nombre <span class="required">*</span></label>
				<input type="text" name="nombre_beneficio_evento" id="nombre_beneficio_evento" class="form-control" required>
			</div>
		</div>
	</script>

	{{-- Send assist --}}
	<script type="text/template" id="confirm-assist-control-tpl">
		<div class="well">
			<span><strong>Beneficios:</strong></span>
			<div class="x_panel">
				<div>
					<ul id="benefits-assist-control-event">
						{{-- Render template id="benefits-assist-control-event-tpl" --}}
					</ul>
				</div>
			</div>
		</div>
	</script>

	{{-- Template benefits assist control event --}}
	<script type="text/template" id="benefits-assist-control-event-tpl">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
				<input type="checkbox" id="assist_control_benefit" name="assist_control_benefit" class="filters-categories" value="<%- codigo_lista_ben_even %>" <%- benefit ? 'checked' : ''%>>
				<label class="check-right"><%- nombre_beneficio_evento %></label>
			</div>
		</div>
	</script>

	{{-- Template modal remove --}}
	<script type="text/template" id="confirm-remove-organizer-tpl">
		<p>¿Estás seguro de eliminar el organizador?</p>
	</script>

	{{-- Template modal remove --}}
	<script type="text/template" id="confirm-remove-benefit-tpl">
		<p>¿Estás seguro de eliminar el beneficio?</p>
	</script>

	{{-- Template modal remove --}}
	<script type="text/template" id="confirm-remove-assist-tpl">
		<p>¿Estás seguro de quitar la asistencia al usuario <strong><%- nombre_usuario %></strong>?</p>
	</script>

	{{-- Template modal remove --}}
	<script type="text/template" id="confirm-remove-all-assist-tpl">
		<p>¿Estás seguro de quitar la asistencia a todos los usuarios?</p>
	</script>

  {{-- Template modal confirm --}}
  <script type="text/template" id="confirm-assist-tpl">
    <p>¿Estás seguro de marcar la asistencia al usuario <strong><%- nombre_usuario %></strong>?</p>
  </script>

    {{-- Template modal confirm --}}
  <script type="text/template" id="confirm-all-assist-tpl">
    <p>¿Estás seguro de marcar la asistencia de todos usuarios?</p>
  </script>

  {{-- Template modal remove invitation --}}
  <script type="text/template" id="confirm-remove-invitation-tpl">
    <p>¿Estás seguro de quitar la invitación al usuario <strong><%- nombre_usuario %></strong>?</p>
  </script>

	{{-- Template modal remove --}}
	<script type="text/template" id="confirm-send-users-invitation-all-tpl">
		<p>¿Estás seguro de enviar la invitación?</p>
	</script>


  {{-- Template modal search entity --}}
  <script type="text/template" id="form-users-principal-organizer-modal-tpl">
    <div class="row">
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Nickname</label>
        <input type="text" name="nickname_usuario_principal_organizer_datatable" id="nickname_usuario_principal_organizer_datatable" class="form-control">
      </div>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Nombre entidad</label>
        <input type="text" name="nombre_usuario_principal_organizer_datatable" id="nombre_usuario_principal_organizer_datatable" class="form-control">
      </div>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Ciudad</label>
        <select name="codigo_ciudad_principal_organizer_datatable" id="codigo_ciudad_principal_organizer_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
        </select>
      </div>
    </div>
    <br>
    <div class="text-center">
      <button type="button" class="btn btn-primary btn-search-principal-organizer-user"> Buscar</button>
      <button type="button" class="btn btn-default btn-clear-principal-organizer-user"> Limpiar</button>
    </div>
    <br>
    <div class="table-responsive">
      <table id="user-principal-organizer-list-search-table" class="table table-striped table-bordered" data-page-length="15">
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

  <script type="text/template" id="form-user-responsable-modal-tpl">
    <div class="row">
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Nickname</label>
        <input type="text" name="nickname_usuario_responsable_datatable" id="nickname_usuario_responsable_datatable" class="form-control">
      </div>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Nombre usuario</label>
        <input type="text" name="nombre_usuario_responsable_datatable" id="nombre_usuario_responsable_datatable" class="form-control">
      </div>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <label>Ciudad</label>
        <select name="codigo_ciudad_usuario_responsable_datatable" id="codigo_ciudad_usuario_responsable_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
        </select>
      </div>
    </div>
    <br>
    <div class="text-center">
      <button type="button" class="btn btn-primary btn-search-user-responsable"> Buscar</button>
      <button type="button" class="btn btn-default btn-clear-user-responsable"> Limpiar</button>
    </div>
    <br>
    <div class="table-responsive">
      <table id="user-responsable-list-search-table" class="table table-striped table-bordered" data-page-length="15">
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

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.assist_to_event) { %>
					<li>
						<a href="#" title="Desmarcar invitación" class="btn btn-default btn-xs invitation" data-turn="false" data-register="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-remove="<%- full.codigo_usuario_evento %>" data-action="quitar la invitación">
							<i class="fa fa-close"></i> Desmarcar invitación
						</a>
					</li>
				<% }else{ %>
					<li>
						<a> No hay acciones
						</a>
					</li>
				<% } %>
			</ul>
		</div>
	</script>

  <script>
    document.getElementById("imagen_patrocinador").addEventListener("change", function(event) {
        var fileInput = event.target;
        var file = fileInput.files[0];
        if (file) {
            var fileType = file.type;
            var acceptedTypes = ["image/png", "image/jpeg"];
            if (!acceptedTypes.includes(fileType)) {
                alert("El formato de la imagen debe ser png o jpg");
                fileInput.value = "";

            }
        }
    });
    </script>

	<script>
			setTimeout(function(){
				$('#event-project-editor').clone().appendTo("#CloneDiv");
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
