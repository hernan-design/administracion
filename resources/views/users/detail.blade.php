@extends('users.user')

@section('users.content')

    {{-- Detail program view --}}
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Vista <small>Usuario</small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a href="{{ route('usuario.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
                    <div class="profile_img">
                        <div id="crop-avatar">
                            <!-- Current avatar -->
                            <img class="img-responsive avatar-view" src="{{ $user->imagen_usuario }}"
                                alt="{{ $user->user_fullname }}"
                                onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')">
                        </div>
                    </div>
                    <h3>{{ ucwords(strtolower($user->user_fullname)) }}</h3>

                    <ul class="list-unstyled user_data">
                        <li>
                            <i class="fa fa-user user-profile-icon"></i> {{ $user->nickname_usuario }}
                        </li>
                        <li>
                            <i class="fa fa-map-marker user-profile-icon"></i> {{ $user->direccion_usuario }}
                        </li>
                        <li>
                            <i class="fa fa-briefcase user-profile-icon"></i> {{ $user->profesion_usuario }}
                        </li>
                        <li>
                            <i class="fa fa-address-card"></i> {{ $user->identificacion_usuario }}
                        </li>
                        <li class="m-top-xs">
                            <i class="fa fa-at user-profile-icon"></i>
                            <a href="mailto:{{ $user->correo_electronico }}"
                                target="_top">{{ $user->correo_electronico }}</a>
                        </li>
                        <li>
                            <i class="fa fa-calendar user-profile-icon"></i>
                            {{ date('j F, Y', strtotime($user->fecha_nacimiento_usuario)) }}
                        </li>
                        <li>
                            <i class="fa fa-phone user-profile-icon"></i> {{ $user->telefono_usuario }}
                        </li>
                        <li>
                            <i class="fa fa-mobile user-profile-icon"></i> {{ $user->movil_usuario }}
                        </li>
                        <li>
                            <i class="fa fa-check"></i> Ultima conexion: {{ $user->ultimo_ingreso_usuario }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <div id="user-detail-nav-tab">
                            {{-- Render template id="user-detail-nav-tab-tpl" --}}
                        </div>

                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="roles" aria-labelledby="roles-tab">
                                <!-- start user roles -->
                                <div id="multiple-select">

                                </div>
                                <!-- end user roles -->
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="activities" aria-labelledby="activities-tab">
                                <!-- start activities log -->
                                <div id="activities-log">
                                    {{-- Render template id="activities-log-tpl" --}}
                                </div>
                                <!-- end activities log -->
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="update" aria-labelledby="update-tab">
                                <!-- start activities log -->
                                <div id="update-user">
                                    {{-- Render template id="update-user-tpl" --}}
                                </div>
                                <!-- end activities log -->
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="mentoring" aria-labelledby="mentoring-tab">
                                <!-- start mentoring  tab -->
                                <section id="mentoring-content" class="mentoring-content">
                                    {{-- Mentoring dinamic content  --}}

                                </section>
                                <!-- end mentoring -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/template" id="multiselect-tpl">
		<select multiple="multiple" class="keepOrder my-select" id="my-select-rol" name="my-select[]" data-route="usuario.rol" data-params='{"rol":0}' data-id="<%- codigo_usuario %>">
			<% _.each(roles, function (option) { %>
				<option value="<%- option.id %>" <%- option.comparator ? 'selected' : '' %> ><%- option.title %></option>
			<% }); %>
		</select>
  </script>

    <script type="text/template" id="update-user-tpl">
    {!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-update-user') ) !!}
    <input type="hidden" name="inp__CurrentEmail" id="inp__CurrentEmail" value={{$user->correo_electronico}}>
      <div class="row form-group">
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Nombre </label>
          <input type="text" name="inp__Name" id="inp__Name" class="form-control" maxlength="512" placeholder={{$user->nombre_usuario}} >
        </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Primer Apellido </label>
          <input type="text" name="inp__FirstLastName" id="inp__FirstLastName" class="form-control" maxlength="256" placeholder={{$user->primer_apellido_persona}}>
        </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Segundo Apellido </label>
          <input type="text" name="inp__SecondLastName" id="inp__SecondLastName" class="form-control" maxlength="256" placeholder={{$user->segundo_apellido_persona}}>
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Correo Electronico </label>
          <input type="email" name="inp__Mail" id="inp__Mail" class="form-control" maxlength="250" placeholder={{$user->correo_electronico}}>
      </div>
      <div class="col-md-4 col-sm-12 col-xs-12">
        <label> Número Identificación </label>
        <input type="number" name="inp__Number" id="inp__Number" class="form-control" maxlength="512" placeholder={{$user->identificacion_usuario}}>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12">
      <label> Fecha Nacimiento </label>
      <input type="date" name="inp__BirthDay" id="inp__BirthDay" class="form-control" data-inputmask="'mask':'9999-99-99'" placeholder={{$user->fecha_nacimiento_usuario}}>
    </div>
      </div>

      <div class="row form-group">
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Celular </label>
          <input type="number" name="inp__Cellphone" id="inp__Cellphone" class="form-control" minlength="10" maxlength="10" placeholder={{$user->movil_usuario}}>
          <span id="error__cellPhone" class="error-message" style="color: red;"></span>
      </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Telefono </label>
          <input type="number" name="inp__Phone" id="inp__Phone" class="form-control" minlength="7" maxlength="10" placeholder={{$user->telefono_usuario}}>
          <span id="error__Phone" class="error-message" style="color: red;"></span>
        </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
          <label> Dirección Domicilio </label>
          <input type="text" name="inp__Address" id="inp__Address" class="form-control" maxlength="250" placeholder={{$user->direccion_usuario}}>
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <button type="submit" class="btn btn-success"> Actualizar </button>
        </div>
      </div>
    {!! Form::close() !!}
  </script>

    <script type="text/template" id="user-detail-nav-tab-tpl">
    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
      <li role="presentation" class="active"><a href="#roles" id="roles-tab" role="tab" data-toggle="tab" aria-expanded="true">Roles</a>
      </li>
      <li role="presentation"><a href="#activities" id="activities-tab" role="tab" data-toggle="tab" aria-expanded="true">Actividad</a>
      </li>
      <li role="presentation"><a href="#update" id="update-tab" role="tab" data-toggle="tab" aria-expanded="true">Actualizacion</a>
      </li>
      <li role="presentation" id="mentoring-tab-list-item"><a href="#mentoring" id="mentoring-tab" role="tab" data-toggle="tab" aria-expanded="true">Mentoría</a>
      </li>
    </ul>
  </script>

    {{-- Section Detail Mentor --}}
    <script type="text/template" id="mentor-detail-tpl">
    <div class="wrapper-spinner"></div>
		<section id="profile-mentor-section" >
      {!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentor-profile') ) !!}

				<div id="content-profile-mentor">
							<div class="panel-content radius <%- owner ? 'editable' : '' %>">
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <label><h4 class="thin-header inst-color-secondary">En qué puedo ayudar</h4></label>
                    <textarea name="forma_ayuda_mentoria" id="forma_ayuda_mentoria" class="form-control input-required" data-parsley-maxlength="255" value="<%- typeof(mentor.forma_ayuda_mentoria) !== 'undefined' ?  mentor.forma_ayuda_mentoria : '' %>" ><%- typeof(mentor.forma_ayuda_mentoria) !== 'undefined' ?  mentor.forma_ayuda_mentoria : '' %></textarea>
                  </div>
                </div>
						  </div>
              <div class="panel-content radius <%- owner ? 'editable' : '' %>">
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <label><h4 class="thin-header inst-color-secondary">Mi experiencia</h4></label>
                    <textarea name="experiencia_usuario" id="experiencia_usuario" class="form-control input-required" data-parsley-maxlength="255" value="<%- typeof(mentor.experiencia_usuario) !== 'undefined' ? mentor.experiencia_usuario : '' %>" > <%-typeof(mentor.experiencia_usuario) !== 'undefined' ? mentor.experiencia_usuario : ''  %> </textarea>
                  </div>
                </div>
              </div>
          <div class="panel-content radius <%- owner ? 'editable' : '' %>">
            <div class="row">
              <div class="col-md-12 col-xs-12">
                <label><h4 class="thin-header inst-color-secondary">Linkedin</h4></label>
                <input type="text" class="form-control input-required text-center spinner-quantity" name="linkedin_usuario" id="linkedin_usuario" value="<%- typeof(mentor.linkedin_usuario) !== 'undefined' ? mentor.linkedin_usuario : '' %>">
              </div>
            </div>
          </div>
          <div class="panel-content radius <%- owner ? 'editable' : '' %>">
          <div class="row">
          <div class="tablet-6 columns">
            <div class="edit-field" data-dropdown="drop-edit-form-alt" aria-controls="drop-edit-form-alt" data-options="align:right" aria-expanded="false" data-edit-template="level-development">
              <h4 class="thin-header inst-color-secondary">
                <span class="field-text">Nivel de desarrollo</span>
              </h4>
              <ul class="no-bullet list-separator">
                @foreach( App\Models\Core\EtapaProyecto::getAllStagesForPrograms() as $value)
                  <li>
                    <input type="checkbox" name="codigo_etapa_proyecto_{{ $value->codigo_etapa_proyecto }}" id="codigo_etapa_proyecto_{{ $value->codigo_etapa_proyecto }}" value="{{ $value->codigo_etapa_proyecto }}" class="filled-in" <%- ( (typeof(mentor.stage_mentoring) !== 'undefined') && _.contains(mentor.stage_mentoring, {{ $value->codigo_etapa_proyecto }}) ? 'checked' : '' )%> >
                    <label for="codigo_etapa_proyecto_{{ $value->codigo_etapa_proyecto }}">
                      {{ $value->nombre_etapa_proyecto }}
                      <span class="text-sm">{{ $value->descripcionetapa_proyecto }}</span>
                    </label>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
          </div>
          </div>
          <div class="tablet-6 columns">

            <div class="tablet-6 columns">
              <div class="panel-content radius <%- owner ? 'editable' : '' %>">
                <div id="mentor-specialities-container" class="input-field">
                  <h4 class="thin-header inst-color-secondary">{!! transtp('mentoring.create.specialties') !!}</h4>
                  <select multiple="multiple" class="keepOrder my-select" id="my-select-especialidades" name="my-select[]" data-route="usuario.mentoria.especialidades" data-params='{"especialidad_id":0}' data-id="<%- codigo_usuario %>">
                    <% _.each(mentor.especialidades_usuario, function (option) { %>
                    <option value="<%- option.codigo_especialidad %>" <%- option.comparator ? 'selected' : '' %> ><%- option.nombre_especialidad %></option>
                    <% }); %>
                  </select>
                </div>
                <br>
              </div>
          <div class="tablet-6 columns">
            <div id="mentor-economic-sector-container" class="input-field">
              <h4 class="thin-header inst-color-secondary">{!! transtp('mentoring.create.sectors') !!}</h4>
              <select multiple="multiple" class="keepOrder my-select" id="my-select-sectores" name="my-select[]" data-route="usuario.mentoria.sectoreconomico" data-params='{"sector_id":0}' data-id="<%- codigo_usuario %>">
                <% _.each(mentor.sectores_economicos_usuario, function (option) { %>
                <option value="<%- option.codigo_sector_economico %>" <%- option.comparator ? 'selected' : '' %> ><%- option.nombre_sector_economico %></option>
                <% }); %>
              </select>
            </div>
          </div>
              <br>
              <div class="panel-content radius <%- owner ? 'editable' : '' %>">
                <div class="row">
                  <div class="col-md-12 form-group">
                    <label><h4 class="thin-header inst-color-secondary">Adjuntar archivos</h4></label>
                    <section id="upload-mentor-editor">
                      {{-- Render Upload @backend01--}}
                      <div class="edit-form">
                          <div id="fnuploader-attach-file" class="uploader-attachments"></div>
                      </div>
                    </section>
                  </div>
                </div>
              </div>

          <div class="small-12 tablet-6 medium-2  columns ">
              {!! Form::submit('Guardar', ['class' => 'btn btn-success ']) !!}
              @php
                $terms = App\Models\Param\TerminosCondiciones::termsAndConditions(App\Models\Param\TerminosCondiciones::getTypeTerms('mentoring'));
              @endphp
            @if($terms)
            <% if(!mentor.terminos_aceptados){ %>
              {!! Form::submit('Enviar Términos y Políticas', ['class' => 'btn btn-danger', 'id'=>'btn-mentor-terms']) !!}
            <% } %>
            @endif
          </div>
			{!! Form::close() !!}

		</section>
	</script>

    {{-- gallery image template --}}
    <script type="text/template" id="gallery-image-item-tpl">
    <div class="item-gallery-image <%- editable ? 'image-area-drop' : 'light-preview-image' %>" data-gallery-img="g-img-<%- codigo_adjunto %>" data-lightbox="<%- direccion_adjunto ? direccion_adjunto : '{{ config('tuproyecto.no_image') }}' %>">
      <a href="#" class="<%- editable ? 'change-image-gallery' : '' %>" data-gallery-img="g-img-<%- codigo_adjunto %>">
        <img src="<%- direccion_adjunto ? direccion_adjunto : '{{ config('tuproyecto.no_image') }}' %>" alt="">
        <i class="mdi mdi-plus hidesoft"></i>
      </a>
    </div>
  </script>
@stop
