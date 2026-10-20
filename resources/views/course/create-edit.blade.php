{{-- views/course/create-edit.blade.php --}}
@extends('course.course')

@section('course.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					@if(isset($course))
						<div class="x_title title">
							<ul class="nav navbar-right panel_toolbox">
								<li>
									<a href="{{ route('curso.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
								</li>
							</ul>
						</div>
						<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
							<li role="presentation" class="active"><a href="#tab_course" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Curso</a>
							</li>
							<li role="presentation"><a href="#tab_themes" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Temas</a>
							</li>
							@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
								<li role="channel"><a href="#tab_channels" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Canales</a>
								</li>
							@endif
							<li role="seo"><a href="#tab_seo" id="seo-tab" role="tab" data-toggle="tab" aria-expanded="true">SEO</a>
							</li>
							<li role="organizadores" class="aqui"><a href="#tab_organizadores" id="tab-organizadores" role="organizadores" data-toggle="tab" aria-expanded="true">Organizadores</a>
							</li>
						</ul>
					@else
						<div class="x_title">
							<h2>Curso</h2>
							<ul class="nav navbar-right panel_toolbox">
								<li>
									<a href="{{route('curso.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
								</li>
							</ul>
							<div class="clearfix"></div>
						</div>
					@endif
					<div id="myTabContent" class="tab-content">
						<div role="tabpanel" class="tab-pane fade active in" id="tab_course" aria-labelledby="home-tab">
							<div class="well">
								{!! Form::open(array('route' => null,'id' => 'form-course', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Título <span class="required">*</span></label>
										<input type="text" name="nombre_curso" id="nombre_curso" class="form-control" value="{{ (isset($course)) ? $course->nombre_curso : '' }}" required data-parsley-maxlength="100">
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Tipo <span class="required">*</span></label>
										<select type="text" name="tipo_curso" id="tipo_curso" class="form-control" required>
											<option value="">Seleccione...</option>
											@foreach ($coursesType as $courseType)
												<option value="{{ $courseType->codigo_tipo_curso }}" {{ (isset($course)) ? ($course->tipo_curso == $courseType->codigo_tipo_curso) ? 'selected' : '' : '' }} >{{ $courseType->nombre_tipo_curso }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Categoría <span class="required">*</span></label>
										<select type="text" name="codigo_categoria_curso" id="codigo_categoria_curso" class="form-control" required>
											<option value="">Seleccione...</option>
											@foreach ($courseCategories as $courseCategory)
												<option value="{{ $courseCategory->codigo_categoria_curso }}" {{ (isset($course)) ? ($course->codigo_categoria_curso == $courseCategory->codigo_categoria_curso) ? 'selected' : '' : '' }}>{{ $courseCategory->nombre_categoria_curso }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Privacidad <span class="required">*</span></label>
										<select type="text" name="codigo_privacidad_contenido" id="codigo_privacidad_contenido" class="form-control" required>
											<option value="">Seleccione...</option>
											@foreach ($privacyContent as $value)
												<option value="{{ $value->id }}" {{ (isset($course)) ? (($course->codigo_privacidad_contenido == $value->id) ? 'selected' : '') : (($value->id == $public) ? 'selected' : '') }}>{{ $value->text }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Fecha apertura <span class="required">*</span></label>
										<input type="text" name="fecha_apertura_curso" id="fecha_apertura_curso" class="form-control datepicker calendar" data-inputmask="'mask':'9999-99-99'" value="{{ (isset($course)) ? $course->fecha_apertura_curso : '' }}" required>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Fecha cierre <span class="required">*</span></label>
										<input type="text" name="fecha_cierre_curso" id="fecha_cierre_curso" class="form-control datepicker calendar" data-inputmask="'mask':'9999-99-99'" value="{{ (isset($course)) ? $course->fecha_cierre_curso : '' }}" required>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Orientación <span class="required">*</span></label>
										<select type="text" name="codigo_orientacion_curso" id="codigo_orientacion_curso" class="form-control" required>
											<option value="">Seleccione...</option>
											@foreach ($courseOrientation as $value)
												<option value="{{ $value->codigo_orientacion_curso }}" {{ (isset($course)) ? ($course->codigo_orientacion_curso == $value->codigo_orientacion_curso) ? 'selected' : '' : '' }} >{{ ucwords( strtolower($value->nombre_orientacion_curso) ) }}</option>
											@endforeach
										</select>
									</div>
                  <div class="col-md-4 col-sm-4 col-xs-12">
										<br>
										<div class="checkbox-inline">
											<label class="checkbox-inline">
												<input type="checkbox" name="log_curso" id="log_curso" {{ (isset($course)) ? $course->log_curso ? 'checked' : '' : '' }} class="flat"> <strong>Control de Ventana</strong>
											</label>
										</div>
									</div>
                  <div class="col-md-4 col-sm-4 col-xs-12">
										<br>
										<div class="checkbox-inline">
											<label class="checkbox-inline">
												<input type="checkbox" name="log_biofacial" id="log_biofacial" {{ (isset($course)) ? $course->log_biofacial ? 'checked' : '' : '' }} class="flat"> <strong>Validación de identidad BioFacial</strong>
											</label>
										</div>
									</div>
									@if(isset($course))
										<div class="col-md-8 col-sm-8 col-xs-12">
											<label>Orden <span class="required">*</span></label>
											<select name="orden_curso" id="orden_curso" class="form-control">
												<option value="{{ $course->orden_curso != null ? $course->orden_curso : '' }}"> {{ $course->orden_curso != null ? $course->orden_curso : 'Selecciona...'}} </option>
												@for ($i = 1; $i <= $course->total_cursos; $i++)
													@if($course->orden_curso != $i)
														<option value="{{ $i }}" >{{ $i }}</option>
													@endif
												@endfor
											</select>
										</div>
									@endif
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<br>
										<div class="checkbox-inline">
											<label class="checkbox-inline">
												<input type="checkbox" name="curso_gratis" id="curso_gratis" {{ (isset($course)) ? $course->curso_gratis ? 'checked' : '' : '' }} class="flat"> <strong>Curso abierto</strong>
											</label>
										</div>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<div id="url-external-course">
											{{-- Render input template id="url-external-course-tpl" --}}
										</div>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-12 col-xs-12">
										<label>Descripción <span class="required">*</span></label>
										<textarea name="descripcion_curso" id="descripcion_curso" class="form-control" value="{{ (isset($course)) ? $course->descripcion_curso : '' }}" required>{{ (isset($course)) ? $course->descripcion_curso : '' }}</textarea>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<label>Imagen <span class="required">*</span></label>
										<div class="x_panel">
											{{-- Content crop and uploader image --}}
											<div id="wrap-course-uploader-tocrop" class="content-area-crop-image">
												<div class="row __fx-main-content">
													<div class="col-xs-12 col-md-6 __fx-column">
														<h5 class="green">Ajustar la imagen:</h5>
														<div class="clearfix">
															<p class="pull-left">Arrastra el cuadrado para cambiar la posición y el tamaño.</p>
															<div id="uploader-course-image" class="uploader-article-img iuc__image-uploader">
																<!-- Render image uploader -->
															</div>
														</div>
														<div id="image-crop-course" class="iuc__content-image-to-crop image-area-drop" iuc__drop-area-text="Arrastra una imagen aquí" style="width:100%">
															<img id="img-course-tocrop" src="{{ isset($course) ? $course->imagen_curso : '' }}" class="iuc__image-to-crop" />
														</div>
													</div>

													<div class="col-xs-12 col-md-6 __fx-column">
														<h5 class="green">Vista previa:</h5>
														<p class="">Tamaño real de la imagen (651px X 453px)</p>
														<div id="preview-article-img" class="iuc__content-image-preview" style="width:100%">
															<img id="course-preview-tocrop" src="" />
														</div>
													</div>
												</div>

												<input type="hidden" name="imagen_curso" value="{{ isset($course) ? $course->imagen_curso : '' }}" required>
												<button id="btn-to-crop" class="btn btn-primary pull-right"><i class="fa fa-scissors fa-lg fa-fw"></i> Recortar imagen</button>
											</div>
										</div>
									</div>
								</div>
								@if (isset($course))
									<ul id="attachs-course" class="nav nav-tabs bar_tabs" role="tablist">
										<li role="presentation" class="active"><a href="#attachs_course" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Adjuntos</a>
										</li>
									</ul>
									<div id="section-attachs-courses-content" class="tab-content">
										<div role="tabpanel" class="tab-pane fade active in" id="attachs_course" aria-labelledby="home-tab">
											<div class="row">
												<div class="col-md-12 form-group">
													<label>Adjuntar archivos</label>
													<section id="upload-attach-course">
														{{-- Uploader files --}}
													</section>
												</div>
											</div>
										</div>
									</div>
								@endif
								<br>
								<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
									<button type="submit" class="btn btn-success btn-block">{{ (isset($course)) ? 'Editar' : 'Guardar' }}</button>
								</div>
								{!! Form::close() !!}
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_themes" aria-labelledby="profile-tab">
							<div id="wrapper-render-themes">
								{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-themes-course', 'data-parsley-validate' => '') ) !!}
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<!-- Table themes course -->
											<fieldset class="well panel-section-form" name="fields_themes_course" id="fields_themes_course">
												<div class="form-group">
													<div class="row">
														<div class="col-md-12 col-xs-12" >
															<div class="x_panel">
																<div class="row">
																	<div class="col-md-6 col-sm-6 col-xs-12">
																		<label for="nombre_tema_curso">Nombre <span class="required">*</span></label>
																		<input type="text" name="nombre_tema_curso" class="form-control" required="true">
																	</div>
																	<div class="col-md-6 col-sm-6 col-xs-12">
																		<label for="objetivo_tema_curso">Descripción <span class="required">*</span></label>
																		<input type="text" name="objetivo_tema_curso" class="form-control" required="true">
																	</div>
																</div>
																<br>
																<div class="text-center">
																	<button type="submit" id="add-course-theme" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>
																</div>
															</div>
															<br>
															<div class="x_panel">
																<div class="x_panel">
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
																	{{-- Render item them course id:item-theme-course-tpl --}}
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
						@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
							<div role="tabpanel" class="tab-pane fade" id="tab_channels" aria-labelledby="profile-tab">
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
										<input type="text" class="form-control" value="{{ (isset($course)) ? $course->nombre_curso : '' }}" readonly>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
										<label>Descripción</label>
										<textarea class="form-control" data-parsley-maxlength="200" value="{{ (isset($course)) ? $course->descripcion_curso : '' }}" readonly>{{ (isset($course)) ? strip_tags($course->descripcion_curso) : '' }}</textarea>
									</div>
								</div>
								<ul id="seo_social" class="nav nav-tabs bar_tabs" role="tablist">
									<li role="general" class="active"><a href="#tab_general" id="general-tab" role="tab" data-toggle="tab" aria-expanded="true">General</a>
									</li>
									<li role="facebook"><a href="#tab_facebook" id="facebook-tab" role="tab" data-toggle="tab" aria-expanded="true">Facebook</a>
									</li>
									<li role="twitter"><a href="#tab_twitter" id="twitter-tab" role="tab" data-toggle="tab" aria-expanded="true">Twitter</a>
									</li>
								</ul>
								<div id="seo_social" class="tab-content">
									<div role="general" class="tab-pane fade active in" id="tab_general" aria-labelledby="general-tab">
										<div class="x_panel">
											{!! Form::open(array('route' => null, 'id' => 'form-course-seo-general', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Slug</label>
													<input type="text" name="url_curso" id="url_curso" class="form-control" value="{{ (isset($course)) ? $course->url_curso : '' }}" required>
												</div>
												<div class="col-md-1 col-sm-12 col-xs-12 tp_align-btn">
													<button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="" data-original-title="Recuerda que al modificar esta propiedad de contenido puedes perder los enlaces que hayas publicado antes del cambio."><i class="fa fa-exclamation"></i></button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Url canonical</label>
													<input type="text" name="url_canonica" id="url_canonica" class="form-control" value="{{ (isset($course)) ? $course->url_canonica : '' }}">
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Input Tags</label>
													<input name="tags_curso" id="tags_curso" value="{{ (isset($course)) ? $course->tags : '' }}" class="form-control ui-autocomplete-input"/>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<div class="checkbox-inline">
														<label class="checkbox-inline">
															<input type="checkbox" name="enlaces_motores_busqueda" {{ isset($course) ? $course->enlaces_motores_busqueda ? 'checked' : '' : '' }} class="flat"> <strong>Enlaces motores de busqueda</strong>
														</label>
													</div>
												</div>
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<div class="checkbox-inline">
														<label class="checkbox-inline">
															<input type="checkbox" name="indexar_motores" class="flat" {{ isset($course) ? $course->indexar_motores ? 'checked' : '' : '' }}> <strong>Indexar motores</strong>
														</label>
													</div>
												</div>
											</div>
											<br>
											<div class="row">
												<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
													<button type="submit" class="btn btn-success btn-block">{{ (isset($course)) ? 'Editar' : 'Guardar' }}</button>
												</div>
											</div>
											{!! Form::close() !!}
										</div>
									</div>
									@if(isset($course))
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
						<div class="tab-pane fade" role="organizadores" id="tab_organizadores" aria-labelledby="organizadores-tab">
							<div class="well">

								<!--<div id="wrapper-render-organizers">-->
									<div class="row">
											<div class="col-xs-12">
													<div class="form-group">
														<div class="row">
															<div class="col-md-12 col-xs-12" >

																<div  id="crear_organizador">
																	<form id="formAddOrganizers" enctype="multipart/form-data">
																		<div class="x_panel">
																			<div class="row">
																				<div class="col-md-3 col-sm-6 col-xs-12">
																					<label>Nombre <span class="required">*</span></label>
																					<input type="text" name="nombre_patrocinador" id="nombre_patrocinador" class="form-control" required>
																				</div>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
																					<label>Descripción <span class="required">*</span></label>
																					<input type="text" name="descripcion_patrocinador" id="descripcion_patrocinador" class="form-control" required>
																				</div>
																				<div class="col-md-3 col-sm-6 col-xs-12">
																					<label>Imagen <span class="required">*</span></label>
																					<input type="file" name="imagen_patrocinador" id="imagen_patrocinador" class="form-control" accept="image/png, image/jpeg" required>
																				</div>
                                        <input type="hidden" name="codigo_curso" id="codigo_curso" value="{{isset($course) ? $course->codigo_curso : ""}}">
																				<div class="col-md-3 col-sm-6 col-xs-12">
																					<div class="text-center">
																						<button type="submit" class="btn btn-success" title="Agregar organizador"><i class="fa fa-plus"></i> Agregar</button>
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
									<div class="row">
										<div class="col-xs-12">
											<div class="x_panel">
												<div id="list_organizers_add">
													 <!--Render template id="list-organizers-add-tpl"-->
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											<div class="x_panel">
												<div id="list_organizers">
													 <!--Render template id="organizers-list-tpl"-->
												</div>
											</div>
										</div>
									</div>

								<!--</div>-->
					 		<!--END CLASS WELL-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

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

	<script type="text/template" id="organizers-list-tpl">
		<div class="x_panel">
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group">
						<div class="table-responsive">
							<h4>Organizadores existentes</h4>

						<!--	<div class="col-md-4 col-sm-6 ">
								<div style="display: flex">
									<input type="text"  id="nombre_patrocinador" class="form-control">
									<button class="btn-success btn-sm btn-search"><i class="fa fa-search"></i></button>

								</div>
							</div> -->

							<table id="organizer-search-table" class="table table-striped table-bordered" data-page-length="15">
								<thead>
									<tr class="headings">
										<th class="column-title">Código</th>
										<th class="column-title">Nombre del organizador</th>
                    <th class="column-title">Descripción del organizador</th>
										<th class="column-title">Imagen</th>
										<th class="column-title">Agregar/Quitar del Curso</th>
										<th class="column-title">Eliminar Organizador de DB</th>

									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Código</th>
										<th>Nombre del organizador</th>
                    <th>Descripción del organizador</th>
										<th>Imagen</th>
										<th>Agregar/Quitar del Curso</th>
										<th>Eliminar Organizador de DB	</th>
									</tr>
								</tfoot>
								<tbody >
									<!-- Render view  -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/template" id="list-organizers-add-tpl">

			<div class="row">
				<div class="col-xs-12">
					<div class="form-group">
						<h4>Organizadores agregados a este curso</h4>
						<button class="btn btn-info btn-sm ShowOrganizersAdd">Ver Lista</button>
						<div class="table-responsive tableListOrganizersAdds">
							<table id="organizerAdd-search-table" class="table table-striped table-bordered " data-page-length="10">
								<thead>
									<tr class="headings">
										<th class="column-title">Código Organizador</th>
										<th class="column-title">Nombre Organizador</th>
										<th class="column-title">&nbsp</th>

									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Código Organizador</th>
										<th>Nombre Organizador</th>&nbsp
										<th>&nbsp</th>

									</tr>
								</tfoot>
								<tbody >
									<!-- Render view  -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-organizer-tpl">
	<!--	<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li>
					<a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
				</li>
			</ul>

		</div>
		<a href="#" class="btn btn-success btn-sm ">Agregar</a>
		<a href="#" class="btn btn-danger btn-sm ">Quitar</a>-->
		<button class="btn btn-success btn-sm AddOrganizer" value="<%- full.codigo_patrocinador %>">Agregar</button>

	</script>
	{{-- buttons templateButtonsRemoveOrganizer --}}
	<script type="text/template" id="buttons-RemoveOrganizer-tpl">
		<button class="btn btn-danger btn-sm RemoveOrganizer" value="<%- full.id %>">Quitar</button>
	</script>

	{{-- buttons-delete-organizer-tpl  --}}
	<script type="text/template" id="buttons-delete-organizer-tpl">
		<button class="btn btn-danger btn-sm DeleteOrganizerDB" value="<%- full.codigo_patrocinador %>">Eliminar de DB</button>
	</script>

	{{-- template url external course --}}
	<script type="text/template" id="url-external-course-tpl">
		<div id="url-external-section">
			<label>Url externa <span class="required">*</span></label>
			<input type="text" name="url_externa_curso" id="url_externa_curso" class="form-control" value="{{ (isset($course)) ? $course->url_externa_curso : '' }}" required>
		</div>
	</script>

	{{-- Template modal edit  theme course --}}
	<script type="text/template" id="theme-modal-tpl">
		<label>Título <span class="required">*</span></label>
		<section id="name-course-theme">
			{{-- Render component pulse-editor --}}
			<input type="hidden" name="nombre_tema_curso" required>
		</section>
		<label>Descripción</span></label>
		<section id="description-course-theme">
			{{-- Render component pulse-editor --}}
			<input type="hidden" name="objetivo_tema_curso">
		</section>
		<label>Orden</label>
		<section id="order-course-theme">
			<select name="slc_order_course" id="slc_order_course" class="form-control" required="true">
				<option value="<%- orden_tema == null ? '' : orden_tema  %>" selected><%- orden_tema == null ? 'Selecciona...' : orden_tema  %> </option>
				<% for(var i = 1; i <= totalTheme; i++ ) { %>
					<% if(orden_tema != i){ %>
						<option value="<%- i %>"><%- i %></option>
					<% } %>
				<% } %>
			</select>
		</section>
	</script>

	{{-- template theme course --}}
	<script type="text/template" id="item-theme-course-tpl">
		<% typeof(codigo_tema_curso) !== "undefined" || (codigo_tema_curso = this.model.cid);  %>
		<div class="panel">
			<a class="panel-heading collapsed attachment-accordion" role="tab" id="heading<%- codigo_tema_curso %>" data-toggle="collapse" data-parent="#accordion" href="#collapse<%- codigo_tema_curso %>" aria-expanded="false" aria-controls="collapseOne">
				<div class="row">
					<div class="col-lg-11 col-md-10 col-sm-10 col-xs-8">
						<div class="pull-left">
							<i class="fa fa-chevron-right fa-pull-left collaps-arrow"></i>
						</div>
						<div class="media-body">
							<h4 class="panel-title ellipsis">
								<%= nombre_tema_curso %>
							</h4>
						</div>
					</div>
					<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4">
						<div class="text-center">
							<button title="Editar tema" class="btn btn-default btn-xs primary edit-course-theme close-hover-circle">
							<i class="glyphicon glyphicon-pencil blue"></i>
							</button>
							<% if(codigo_estado == window._tp.getState['active']) { %>
							<button title="Inactivar tema" class="btn btn-default btn-xs active-theme close-hover-circle"><i class="glyphicon glyphicon-ok green"></i></button>
							<% }else{ %>
							<button title="Activar tema" class="btn btn-default btn-xs inactive-theme close-hover-circle"><i class="glyphicon glyphicon-remove red"></i></button>
							<% } %>
						</div>
					</div>
				</div>
			</a>
			<div id="collapse<%- codigo_tema_curso %>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<%- codigo_tema_curso %>" aria-expanded="false" style="height: 0px;">
				<div class="panel-body">
					<p class="text-justify"><%= objetivo_tema_curso %></p>
					<div class="text-left">
						<button title="Agregar adjunto" class="btn btn-default btn-xs add-attachment-theme close-hover-circle"><i class="glyphicon glyphicon-plus green"></i> Agregar</button>
					</div>
					<div class="dd" id="nestable">
						<ol class="dd-list">
							{{-- Render list attachment id::item-attachment-theme-tpl --}}
						</ol>
					</div>
				</div>
			</div>
		</div>
	</script>

	{{-- template item attachment --}}
	<script type="text/template" id="item-attachment-theme-tpl">
		<div class="dd-handle dd3-handle">
			<i class="<%- codigo_tipo_adjunto != '' && !_.isNull(codigo_tipo_adjunto) ? window._tp.getIconTypeAttachment[codigo_tipo_adjunto] : window._tp.getIconTypeCourseContent[tipo_contenido_curso] %>"></i>
		</div>
		<div class="dd3-content">
			<div class="col-md-4 col-sm-4 col-xs-4">
				<div class="collapsed-item-content ellipsis">
					<%- titulo_contenido_curso %>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-4">
				<p class="collapsed-item-content ellipsis">
					<a href="<%= typeof(direccion_adjunto) !=='undefined' ? direccion_adjunto : '' %>" target="_blank"><%- typeof(direccion_adjunto) !== "undefined" ? direccion_adjunto : '' %></a>
				</p>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-4">
				<div class="text-right">
					<button title="Editar adjunto" class="btn btn-default btn-xs primary edit-course-content close-hover-circle">
						<i class="glyphicon glyphicon-pencil blue"></i>
					</button>
					<% if( typeof(codigo_estado) === "undefined" || (codigo_estado == window._tp.getState['active']) ) { %>
						<button title="Inactivar adjunto" class="btn btn-default btn-xs active-course-content-theme close-hover-circle"><i class="glyphicon glyphicon-ok green"></i></button>
					<% }else{ %>
						<button title="Activar adjunto" class="btn btn-default btn-xs inactive-course-content-theme close-hover-circle"><i class="glyphicon glyphicon-remove red"></i></button>
					<% } %>
				</div>
			</div>
		</div>
	</script>

	{{-- section option course content --}}
	<script type="text/template" id="form-modal-tpl">
		<label>Tipo contenido <span class="required">*</span></label>
		<select type="text" name="tipo_contenido_curso" id="tipo_contenido_curso" class="form-control data-option" required>
			<option value="">Seleccione...</option>
			<option value="<%- window._tp.getTypeCourseContent['attachment'] %>" <%- !_.isUndefined(tipo_contenido_curso) && (tipo_contenido_curso == window._tp.getTypeCourseContent['attachment']) ? 'selected' : '' %> >Adjunto</option>
			<!-- <option value="<%= window._tp.getTypeCourseContent['evaluation']%>">Evaluación</option>
			<option value="<%= window._tp.getTypeCourseContent['homework']%>">Tarea</option> -->
		</select>
		<div id="course-content-type">

		</div>
	</script>



	{{-- section option attachment --}}
	<script type="text/template" id="attachment-type-tpl">
		<label>Tipo adjunto <span class="required">*</span></label>
		<select type="text" name="codigo_tipo_adjunto" id="codigo_tipo_adjunto" class="form-control type-attachment" required>
			<option value="">Seleccione...</option>

			@foreach ($attachmentTypes as $attachmentType)
				<option value="{{ $attachmentType->codigo_tipo_adjunto }}" <%- !_.isUndefined(codigo_tipo_adjunto) && (codigo_tipo_adjunto == {{ $attachmentType->codigo_tipo_adjunto }} ) ? 'selected' : '' %>  >{{ $attachmentType->nombre_tipo_adjunto }}</option>
			@endforeach
		</select>
		<div id="attachment-type">

		</div>
	</script>


	{{-- section option evaluation --}}
	<script type="text/template" id="evaluation-tpl">

	</script>


	{{-- section option homework --}}
	<script type="text/template" id="homework-tpl">

	</script>

	{{-- section attachment type video --}}
	<script type="text/template" id="attachment-type-video-tpl">
		<label>Título vídeo <span class="required">*</span></label>
		<input type="text" name="nombre_adjunto" id="nombre_adjunto" class="form-control" value="<%- !_.isUndefined(titulo_contenido_curso) ? titulo_contenido_curso : '' %>" required>
		<label>Url vídeo <span class="required">*</span></label>
		<input type="text" name="direccion_adjunto" id="direccion_adjunto" class="form-control" value="<%- !_.isUndefined(direccion_adjunto) ? direccion_adjunto : '' %>" required>
	</script>

	{{-- section attachment type link --}}
	<script type="text/template" id="attachment-type-article-tpl">
		<label>Título<span class="required">*</span></label>
		<input type="text" name="nombre_adjunto" id="nombre_adjunto" class="form-control" value="<%- !_.isUndefined(titulo_contenido_curso) ? titulo_contenido_curso : '' %>" required>
		<label>Url  <span class="required">*</span></label>
		<input type="text" name="direccion_adjunto" id="direccion_adjunto" class="form-control" value="<%- !_.isUndefined(direccion_adjunto) ? direccion_adjunto : '' %>" required>
	</script>


	{{-- section attachment type survey --}}
	<script type="text/template" id="attachment-type-survey-tpl">
		<label>Título Encuesta<span class="required">*</span></label>
		<input type="text" name="nombre_adjunto" id="nombre_adjunto" class="form-control" value="<%- !_.isUndefined(titulo_contenido_curso) ? titulo_contenido_curso : '' %>" required>
		<%typeof(codigo_encuesta_institucion) !== 'undefined' ? console.log('codigoei',codigo_encuesta_institucion) : ''%>
		<select name="codigo_encuesta" id="codigo_encuesta" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('formulario.index'))%>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-initial-value="<%- typeof(codigo_encuesta) !== 'undefined' ? codigo_encuesta : '' %>" style="width: 100%" value="<%- typeof(codigo_encuesta) !== 'undefined' ? codigo_encuesta : '' %>" data-param="" required>
		</select>
	</script>


	{{-- section attachment type file --}}
	<script type="text/template" id="attachment-type-file-tpl">
		<div class="row">
			<div class="col-md-12">
				<label>Nombre archivo <span class="required">*</span></label>
				<input type="text" name="nombre_adjunto" id="nombre_adjunto" class="form-control" value="<%- !_.isUndefined(titulo_contenido_curso) ? titulo_contenido_curso : '' %>" required>
				<label>Url archivo <span class="required">*</span></label>
				<input type="text" name="direccion_adjunto" id="direccion_adjunto" class="form-control" value="<%- !_.isUndefined(direccion_adjunto) ? direccion_adjunto : '' %>" required>
				<label>Archivo</label>
				<div id="attachment-course-themes" class="uploader-attachments">
					{{-- Render file uploader --}}
				</div>
			</div>
		</div>
	</script>

	{{-- section course theme delete --}}
	<script type="text/template" id="course-theme-delete-tpl">
		<p>Realmente desea realizar esta acción para este tema?</p>
	</script>

	{{-- section theme attachments delete --}}
	<script type="text/template" id="course-attachment-delete-tpl">
		<p>Realmente desea realizar esta acción para este adjunto?</p>
	</script>
@stop
