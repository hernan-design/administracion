{{-- views/project/create-edit.blade.php --}}
@extends('project.project')

@section('projects.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div class="x_title">
						<h2>Entidad</h2>
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{route('entidad.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div id="myTabContent" class="tab-content">
						<div role="project" class="tab-pane fade active in" id="tab_project" aria-labelledby="project-tab">
							<div class="well">
								{!! Form::open(array('route' => null,'id' => 'form-project', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Nombre de la entidad<span class="required">*</span></label>
											<input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="{{ isset($project) ? $project->nombre_usuario : '' }}" required>
										</div>
                    <div class="col-lg-2 col-md-4 col-sm-4 col-sm-offset-2  col-xs-12">
                      <br>
                      <div class="checkbox">
                        <label class="checkbox">
                          <input type="checkbox" name="entidad_apoyo_emprendimiento" {{ isset($project) ? $project->entidad_apoyo_emprendimiento ? 'checked' : '' : '' }} class="flat"> <strong>Entidad Aliada</strong>
                        </label>
                      </div>
                    </div>
									</div>
									<div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label>Tipo de Entidad <span class="required">*</span></label>
                      <select name="codigo_clase_empre_insti" id="codigo_clase_empre_insti" class="form-control" required>
                        <option value="">Seleccione...</option>
                        @foreach ($entityTypes as $value)
                          <option value="{{ $value->codigo_clase_empre_insti }}" {{ isset($project) ? $project->codigo_clase_empre_insti == $value->codigo_clase_empre_insti ? 'selected' : '' : '' }} data-clase_emprendimiento="{{  $value->codigo_clase_emprendimiento}}">{{ $value->nombre_clase_emprendimiento }}</option>
                        @endforeach
                      </select>
                    </div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<div id="tipo-identificacion">
												{{-- Render input template id="tipo-identificacion-tpl" --}}
											</div>
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <div id="numero-identificacion">
                        {{-- Render input template id="numero-identificacion-tpl" --}}
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label>Ciudad<span class="required">*</span></label>
                      <select name="codigo_ciudad" id="codigo_ciudad" class="form-control" required>
                        <option value="">Seleccione...</option>
                        @foreach ($cities as $city)
                          <option value="{{ $city->key }}" {{ isset($project) ? $project->codigo_ciudad == $city->key ? 'selected' : '' : '' }}>{{ $city->value}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label>Telefono de contacto<span class="required">*</span></label>
                      <input type="text" class="form-control text-center spinner-quantity" name="telefono_usuario" id="telefono_usuario" value="{{ isset($project) ? $project->telefono_usuario ? $project->telefono_usuario : '' : '' }}"  required>
                    </div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<div id="url-external-section">
												<label>Página Web</label>
												<input type="text" name="pagina_web_usuario" id="pagina_web_usuario" class="form-control text-center spinner-quantity"  value="{{ isset($project) ? $project->pagina_web_usuario : '' }}">
											</div>
										</div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label><i class="fa fa-facebook fa-sm"></i> Facebook</label>

                      <input type="text" class="form-control text-center spinner-quantity" name="facebook_usuario" id="facebook_usuario" value="{{ isset($project) ? $project->facebook_usuario ? $project->facebook_usuario : '' : '' }}">
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label><i class="fa fa-twitter fa-sm"></i> Twitter</label>
                      <input type="text" class="form-control text-center spinner-quantity" name="twitter_usuario" id="twitter_usuario" value="{{ isset($project) ? $project->twitter_usuario ? $project->twitter_usuario : '' : '' }}">
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <label> Etapas </label>
                      <select type="text" class="form-control" name="etapa_usuario" id="etapa_usuario" required>
                        @if (isset($stages))
                          <option value="" selected> Seleccione </option>
                          @foreach ($stages as $item)
                            <option value="{{ isset($stage) ? $stage->codigo_etapa_proyecto == $item->codigo_etapa_proyecto ? $stage->codigo_etapa_proyecto :  $item->codigo_etapa_proyecto :  $item->codigo_etapa_proyecto }}" {{ isset($stage) ? $stage->codigo_etapa_proyecto == $item->codigo_etapa_proyecto ? 'selected' : '' : '' }}> {{ isset($stage) ? $stage->codigo_etapa_proyecto == $item->codigo_etapa_proyecto ? $stage->nombre_etapa_proyecto : $item->nombre_etapa_proyecto : $item->nombre_etapa_proyecto }} </option>
                          @endforeach

                        @else
                          <option value="" selected> Seleccione </option>
                        @endif
                      </select>
                    </div>
                  </div>

                <div class="row">
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label><i class="fa fa-instagram fa-sm"></i> Instagram</label>

                    <input type="text" class="form-control text-center spinner-quantity" name="instagram_usuario" id="instagram_usuario" value="{{ isset($project) ? $project->instagram_usuario ? $project->instagram_usuario : '' : '' }}">
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label><i class="fa fa-linkedin fa-sm"></i>  Linkedin</label>
                    <input type="text" class="form-control text-center spinner-quantity" name="linkedin_usuario" id="linkedin_usuario" value="{{ isset($project) ? $project->linkedin_usuario ? $project->linkedin_usuario : '' : '' }}">
                  </div>
                </div>
                
                <br>
                <div id="persona-encargada">
                  {{-- Render input template id="persona-encargada-tpl" --}}
                </div>

                <br>
                <div class="row">
                  <div class="col-md-12">
                    <label>Imagen <span class="required">*</span></label>
                    <div class="x_panel">
                      {{-- Content crop and uploader image --}}
                      <div id="wrap-project-uploader-tocrop" class="content-area-crop-image">
                        <div class="row __fx-main-content">
                          <div class="col-xs-12 col-md-6 __fx-column">
                            <h5 class="green">Ajustar la imagen:</h5>
                            <div class="clearfix">
                              <p class="pull-left">Arrastra el cuadrado para cambiar la posición y el tamaño.</p>
                              <div id="uploader-project-image" class="uploader-article-img iuc__image-uploader">
                                <!-- Render image uploader -->
                              </div>
                            </div>
                            <div id="image-crop-project" class="iuc__content-image-to-crop image-area-drop" iuc__drop-area-text="Arrastra una imagen aquí" style="width:100%">
                              <img id="img-project-tocrop" src="{{ isset($project) ? $project->imagen_usuario : '' }}" class="iuc__image-to-crop" />
                            </div>
                          </div>

                          <div class="col-xs-12 col-md-6 __fx-column">
                            <h5 class="green">Vista previa:</h5>
                            <p class="">Tamaño real de la imagen (590px X 354px)</p>
                            <div id="preview-article-img" class="iuc__content-image-preview" style="width:100%">
                              <img id="project-preview-tocrop" src="" />
                            </div>
                          </div>
                        </div>

                        <input type="hidden" name="imagen_usuario" value="{{ isset($project) ? $project->imagen_usuario : '' }}" required>
                        <button id="btn-to-crop" class="btn btn-primary pull-right"><i class="fa fa-scissors fa-lg fa-fw"></i> Recortar imagen</button>
                      </div>
                    </div>
                  </div>
                </div>

                <div id="section-descriptions-project-content" class="tab-content">
                  <div role="tabpanel" class="tab-pane fade active in" id="tab_description" aria-labelledby="home-tab">
                    <div class="row">
                      <div class="col-md-12 form-group">
                        <label>Descripción <span class="required">*</span></label>
                        <section id="description-project-editor">
                          {{-- Render component pulse-editor --}}
                          <input type="hidden" name="biografia_usuario" value="{{ isset($project) ? $project->biografia_usuario : '' }}" required>
                        </section>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
                  <button type="submit" class="btn btn-success btn-block">{{ (isset($project)) ? 'Editar' : 'Guardar' }}</button>
                </div>
								{!! Form::close() !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- template tipo identificacion --}}
	<script type="text/template" id="tipo-identificacion-tpl">
		<div id="tipo-identificacion-section">
			<label>Tipo de identificación <span class="required">*</span></label>
			<select name="tipo_identificacion_usuario" id="tipo_identificacion_usuario" class="form-control" required>
				<option value="">Seleccione...</option>
				@foreach ($identificationType as $value)
					<option value="{{ $value->codigo_tipo_identificacion }}" {{ isset($project) ? $value->codigo_tipo_identificacion == $project->tipo_identificacion_usuario ? 'selected' : '' : '' }}>{{ $value->nombre_tipo_identificacion }}</option>
				@endforeach
			</select>
		</div>
	</script>

    {{-- template numero identificacion --}}
    <script type="text/template" id="numero-identificacion-tpl">
      <div id="numero-identificacion-section">
        <label>Numero identificacion<span class="required">*</span></label>
        <input type="text" class="form-control text-center spinner-quantity" name="identificacion_usuario" id="identificacion_usuario" value="{{ isset($project) ? $project->identificacion_usuario ? $project->identificacion_usuario : '' : '' }}"  required>
      </div>
    </script>

    {{-- template persona encargada --}}
    <script type="text/template" id="persona-encargada-tpl">
      <div id="users-search-toggle" class="well">
        <h6><b>Persona encargada </b></h6>
        <hr>
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 form-group has-feedback">
            <div class="control-group">
              <div class="controls">
                <label>Correo electrónico<span class="required">*</span></label>
                <div class="input-prepend input-group">
                  <span class="add-on input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_filter_data"><i class="fa fa-search tp_input-email-search"></i></span>
                  <input type="email" name="correo_electronico_encargado" value="{{ isset($person) ? $person->correo_electronico: '' }}" id="correo_electronico_filter_data" class="form-control correo_electronico_user" data-name="nombre_usuario_filter_data"/>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5 col-md-6 col-sm-5 col-xs-12">
            <label>Nombre</label>
            <input type="text" name="nombre_encargado" value="{{ isset($person) ? $person->fullname : '' }}" id="nombre_usuario_filter_data" class="form-control" readonly>
          </div>
        </div>
      </div>
    </script>

@stop
