@extends('institution.institution')

@section('institution.content')
<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                @if(isset($institution))
                <div class="x_title title">
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{ route('institucion.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
					</div>
                </div>
                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    <li role="institution" class="active"><a href="#tab_institution" id="tab-institution" role="tab" data-toggle="tab" aria-expanded="true">General</a>
                    </li>
                    <li role="homepage"><a href="#tab_homepage" role="tab" id="home-page-tab" data-toggle="tab" aria-expanded="false">HomePage</a>
                    </li>
                    <li role="modules"><a href="#tab_modules" role="tab" id="tab-modules" data-toggle="tab" aria-expanded="false">Modulos</a>
                    </li>
                    <li role="terms"><a href="#tab_terms" role="tab" id="tab-terms" data-toggle="tab" aria-expanded="false">Términos</a>
                    </li>
                </ul>
                @else
                <div class="x_title">
						<h2>Institución</h2>
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{route('institucion.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
						<div class="clearfix"></div>
                </div>
                @endif

                <div id="myTabContent" class="tab-content">
                    <div role="institution" class="tab-pane fade active in" id="tab_institution" aria-labelledby="tab-institution">
                        <div class="well">
                            <h3><label>Datos Generales</label></h3>
                            <br>
                            {!! Form::open(array('route' => null,'id' => 'form-create-institution', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Nombre <span class="required">*</span></label>
                                        <input type="text" name="nombre_institucion" id="nombre_institucion" class="form-control" value="{{ isset($institution) ? $institution->nombre_institucion : '' }}" required>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Tipo de institucion <span class="required">*</span></label>
                                        <select name="codigo_tipo_institucion" id="codigo_tipo_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="{{ $type_institution->codigo_tipo_institucion }}"> {{ $type_institution->nombre_tipo_institucion }} </option>
                                            @else
                                                <option value="">Seleccione...</option>
                                                @foreach ($institutionType as $value)
                                                    <option value="{{ $value->codigo_tipo_institucion }}" {{ isset($institution) ? $value->codigo_tipo_institucion == $institution->codigo_tipo_institucion ? 'selected' : '' : '' }}>{{ $value->nombre_tipo_institucion }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Slug <span class="required">*</span></label>
                                        <input type="text" name="slug_institucion" id="slug_institucion" class="form-control" value="{{ isset($institution) ? $institution->slug_institucion : '' }}" required>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Url <span class="required">*</span></label>
                                        <input type="text" name="url_institucion" id="url_institucion" class="form-control" value="{{ isset($institution) ? $institution->url_institucion : '' }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Descripcion <span class="required">*</span></label>
                                        <textarea name="descripcion_institucion" id="descripcion_institucion" cols="30" rows="5" class="form-control" required> {{ isset($institution) ? $institution->descripcion_institucion : '' }} </textarea>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Estado inicial <span class="required">*</span></label>
                                        <select name="estado_inicial_institucion" id="estado_inicial_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="{{ $institution->codigo_estado_inicial }}" {{ $institution->codigo_estado_inicial == App\Models\Core\Estado::getState('active') ? 'selected' : '' }}> {{ $institution->codigo_estado_inicial == App\Models\Core\Estado::getState('active') ? 'Activo' : 'Inactivo' }} </option>
                                                <option value="{{ $institution->codigo_estado_inicial }}" {{ $institution->codigo_estado_inicial != App\Models\Core\Estado::getState('active') ? 'selected' : '' }}> {{ $institution->codigo_estado_inicial == App\Models\Core\Estado::getState('active') ? 'Inactivo' : 'Activo' }} </option>
                                            @else
                                                <option value="" selected> Seleccione </option>
                                                <option value="{{ App\Models\Core\Estado::getState('active') }}"> Activo </option>
                                                <option value="{{ App\Models\Core\Estado::getState('inactive') }}"> Inactivo </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Boton registro</label>
                                        <input type="text" name="boton_registro_institucion" id="boton_registro_institucion" class="form-control" value="{{ isset($institution) ? $institution->boton_registro_institucion : '' }}" maxlength="128">
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Login Social</label>
                                        <select name="social_login_institucion" id="social_login_institucion" class="form-control">
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->social_login_institucion ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->social_login_institucion ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Home <span class="required">*</span></label>
                                        <select name="home_institucion" id="home_institucion" class="form-control" required>
                                            @if (isset($institution))                                               
                                                @foreach (array('muro', 'cursos', 'programas') as $item)
                                                    <option value="{{ $institution->home == $item ? $institution->home : $item  }}" {{ $institution->home == $item ? 'selected' : ''  }}> {{ $institution->home == $item ? App\Models\Param\Institucion::getHomeInstitution($institution->home) : App\Models\Param\Institucion::getHomeInstitution($item)  }} </option>
                                                @endforeach
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="muro"> Comunidad </option>
                                                <option value="cursos"> Cursos </option>
                                                <option value="programas"> Convocatoria </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Video </label>
                                        <input type="text" name="video_institucion" id="video_institucion" class="form-control" value="{{ isset($institution) ? $institution->video_institucion : '' }}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Correo envio notificaciones </label>
                                        <input type="email" name="correo_notificaciones_institucion" id="correo_notificaciones_institucion" class="form-control" value="{{ isset($institution->correo_envio_notificaciones) ? $institution->correo_envio_notificaciones : '' }}" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Slider <span class="required">*</span></label>
                                        <select name="carrousel_institucion" id="carrousel_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->carrousel_institucion ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->carrousel_institucion ? 'selected' : '' }}> No </option>
                                            @else 
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Incluir modelo de negocio en entidad <span class="required">*</span></label>
                                        <select name="modelo_negocio_institucion" id="modelo_negocio_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->modelo_negocio_institucion ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->modelo_negocio_institucion ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option> 
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Url admin </label>
                                        <input type="text" name="url_admin_institucion" id="url_admin_institucion" class="form-control" value="{{ isset($institution) ? $institution->url_admin_institucion : '' }}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Personalización landing <span class="required">*</span></label>
                                        <select id="personalizacion_landing_institucion" name="personalizacion_landing_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->personalizacion_landing ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->personalizacion_landing ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Fuentes personalizadas <span class="required">*</span></label>
                                        <select name="fuentes_personalizadas_institucion" id="fuentes_personalizadas_institucion" class="form-control" required>
                                        @if (isset($institution))
                                            <option value="0" {{ $institution->fuentes_personalizadas ? 'selected' : '' }}> Si </option>
                                            <option value="1" {{ !$institution->fuentes_personalizadas ? 'selected' : '' }}> No </option>
                                        @else
                                            <option value="" selected> Seleccione </option>
                                            <option value="0"> Si </option>
                                            <option value="1"> No </option>
                                        @endif
                                        </select>
                                    </div>    
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Contador de artículos pendientes (Administrador) <span class="required">*</span></label>
                                        <select name="contador_articulos_pendientes_institucion" id="contador_articulos_pendientes_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->contador_articulos_pendientes ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->contador_articulos_pendientes ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Url helpdesk </label>
                                        <input type="text" name="url_helpdesk_institucion" id="url_helpdesk_institucion" class="form-control" value="{{ isset($institution) ? $institution->url_helpdesk_institucion : '' }}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Clave sitio captcha </label>
                                        <input type="text" name="clave_sitio_captcha_institucion" id="clave_sitio_captcha_institucion" class="form-control" value="{{ isset($institution) ? $institution->clave_sitio_captcha : '' }}" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Clave secreta captcha </label>
                                        <input name="clave_secreta_captcha_institucion" id="clave_secreta_captcha" class="form-control" value="{{ isset($institution) ? $institution->clave_secreta_captcha : '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Codigo userguiding </label>
                                        <input type="text" name="codigo_userguiding_institucion" id="codigo_userguiding_institucion" class="form-control" maxlength="128" value="{{ isset($institution) ? $institution->codigo_userguiding : '' }}" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Pasos complementarios (En el registro de usuario) <span class="required">*</span></label>
                                        <select name="pasos_complementarios_registro_institucion" id="pasos_complementarios_registro_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->pasos_complementarios_registro ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->pasos_complementarios_registro ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Visibilidad información vendedor (Marketplace) <span class="required">*</span></label>
                                        <select name="visibilidad_info_vendedor_institucion" id="visibilidad_info_vendedor_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->visibilidad_info_vendedor ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->visibilidad_info_vendedor ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selcciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Datos complemetarios (Registro de usuario) <span class="required">*</span></label>
                                        <select name="datos_complementarios_institucion" id="datos_complementarios_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->datos_complementarios ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->datos_complementarios ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Raíz comunidad (click al logo) <span class="required">*</span></label>
                                        <select name="raiz_comunidad_institucion" id="raiz_comunidad_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->raiz_comunidad ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->raiz_comunidad ? 'selected' : '' }}> No </option>
                                            @else 
                                                <option value="" selected> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Esquema de pasos en compra <span class="required">*</span></label>
                                        <select name="pasos_compra_institucion" id="pasos_compra_institucion" class="form-control" required>
                                            @if(isset($institution))
                                                <option value="0" {{ $institution->pasos_compra ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->pasos_compra ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Contenido usuario </label>
                                        <select name="contenido_usuario_institucion" id="contenido_usuario_institucion" class="form-control" >
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->contenido_usuario ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->contenido_usuario ? 'selected' : '' }}> No </option>                                               
                                            @else
                                                <option value="" selected> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Solicitud directa a mentor <span class="required">*</span></label>
                                        <select name="solicitud_directa_mentoria_institucion" id="solicitud_directa_mentoria_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->solicitud_directa_mentoria ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->solicitud_directa_mentoria ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Seleccione </option>        
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Certificado evento </label>
                                        <select id="certificado_evento_institucion" name="certificado_evento_institucion" class="form-control" >
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->certificado_evento ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->certificado_evento ? 'selected' : '' }}> No </option>
                                            @else 
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Validación identificación  con fecha nacimiento (Registro) <span class="required">*</span></label>
                                        <select id="validacion_identificacion_institucion" name="validacion_identificacion_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->validacion_identificacion ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->validacion_identificacion ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Incluir identificación en el registro. <span class="required">*</span></label>
                                        <select name="identificacion_registro_institucion" id="identificacion_registro_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->identificacion_registro ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->identificacion_registro ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>  Forma de registro <span class="required">*</span></label>
                                        <select name="tipo_registro_institucion" name="tipo_registro_institucion" class="form-control" required>
                                            @if (isset($institution))
                                                <option value="1" {{ $institution->identificacion_registro == 1 ? 'selected' : '' }}> Directo </option>
                                                <option value="2" {{ $institution->identificacion_registro == 2 ? 'selected' : '' }}> HTML </option>
                                            @else 
                                                <option value="" selected> Selecciona </option> 
                                                <option value="1"> Directo </option>
                                                <option value="2"> HTML </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Contacto institución <span class="required">*</span></label>
                                        <section id="contact-institution-editor"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Texto registro </label>
                                        <section id="texto-registro-institution-editor"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Contenido registro </label>
                                        <section id="contenido-registro-institution-editor"></section>
                                    </div>
                                </div>
                                <br>
                                <h3><label>Redes Sociales</label></h3>
                                <br>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Twitter </label>
                                        <input type="text" name="twitter_institucion" id="twitter_institucion" class="form-control" value="{{ isset($institution) ? $institution->twitter_institucion : '' }}" maxlength="128" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Widget twitter </label>
                                        <input type="text" name="widget_twitter_institucion" id="widget_twitter_institucion" class="form-control" value="{{ isset($institution) ? $institution->widget_twitter : '' }}" maxlength="128" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Sigla </label>
                                        <input type="text" name="sigla_institucion" id="sigla_institucion" class="form-control" value="{{ isset($institution) ? $institution->sigla_institucion : '' }}" maxlength="13">
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">                                
                                        <label> Privacidad </label>
                                        <select name="privacidad_institucion" id="privacidad_institucion" class="form-control" >
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->privacidad_institucion ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->privacidad_institucion ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Facebook api key </label>
                                        <input type="text" name="facebook_api_key_institucion" id="facebook_api_key_institucion" class="form-control" value="{{ isset($institution) ? $institution->facebook_api_key : '' }}" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Facebook secret key </label>
                                        <input type="text" name="facebook_secret_key_institucion" id="facebook_secret_key_institucion" class="form-control" value="{{ isset($institution) ? $institution->facebook_secret_key : '' }}" >
                                    </div>
                                </div>
                                <div class="row">                                    
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Linkedin api key </label>
                                        <input type="text" name="linkedin_api_key_institucion" id="linkedin_api_key_institucion" class="form-control" value="{{ isset($institution) ? $institution->linkedin_api_key : '' }}" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Linkedin secret key </label>
                                        <input type="text" name="linkedin_secret_key_institucion" id="linkedin_secret_key_institucion" class="form-control" value="{{ isset($institution) ? $institution->linkedin_secret_key : '' }}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Twitter api key </label>
                                        <input type="text" name="twitter_api_key_institucion" id="twitter_api_key_institucion" class="form-control" value="{{ isset($institution) ? $institution->twitter_api_key : '' }}" >
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Twitter secret key </label>
                                        <input type="text" name="twitter_secret_key_institucion" id="twitter_secret_key_institucion" class="form-control" value="{{ isset($institution) ? $institution->twitter_secret_key : '' }}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Google analytics </label>
                                        <input type="text" name="google_analytics_institucion" id="google_analytics_institucion" class="form-control" value="{{ isset($institution) ? $institution->google_analytics : '' }}" >
                                    </div>
                                </div>   
                                <br>
                                <h3><label>Marketplace</label></h3>
                                <br>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Permitir creación de productos con entidades </label>
                                        <select id="marketplace_entidad_institucion" name="marketplace_entidad_institucion" class="form-control" >
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->marketplace_entidad ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->marketplace_entidad ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Permitir creación de productos con usuarios </label>
                                        <select id="marketplace_servicios_institucion" name="marketplace_servicios_institucion" class="form-control" >
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->marketplace_servicios ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->marketplace_servicios ? 'selected' : '' }}> No </option>
                                            @else 
                                                <option value="" selected> Selecciona </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Revision productos (por parte de institución)</label>
                                        <select name="revision_productos_marketplace_institucion" id="revision_productos_marketplace_institucion" class="form-control" >
                                            @if (isset($institution))
                                                <option value="0" {{ $institution->revision_productos_marketplace ? 'selected' : '' }}> Si </option>
                                                <option value="1" {{ !$institution->revision_productos_marketplace ? 'selected' : '' }}> No </option>
                                            @else
                                                <option value=""> Seleccione </option>
                                                <option value="0"> Si </option>
                                                <option value="1"> No </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Recomendaciones marketplace </label>
                                        <section id="recomendaciones_marketplace_institucion" name="recomendaciones_marketplace_institucion"></section>
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success">Enviar</button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div role="homepage" class="tab-pane fade" id="tab_homepage" aria-labelledby="home-page-tab">
                        <div class="well">
                            {!! Form::open(array('route' => null, 'id' => 'form-create-homepage-institution', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
                                <h3><label>Imagenes</label></h3>
                                <br>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Logo Homepage <i> (Tamaño Requerido: 130x60) </i></label>
                                        <section id="upload_logo_institucion"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Imagen notificación correo  <i> (Tamaño Requerido: 130x60) </i></label>
                                        <section id="upload_imagen_mail_institucion"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Logo administrador  <i> (Tamaño Requerido: 130x60) </i></label>
                                        <section id="upload_imagen_administrador_institucion"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Logo reporte  <i> (Tamaño Requerido: 130x60) </i></label>
                                        <section id="upload_imagen_reporte_institucion"></section>
                                    </div>
                                </div>
                                <br>
                                <div id="render-parameters-institution-tpl">
                                    {{--  Render parameters institution  --}}   
                                </div>
                                <h3><label>Redes Sociales</label></h3>
                                <br>
                                <div class="row">
                                    @if (isset($socialNetwork) && count($socialNetworkInsti) <= 0)
                                        @foreach ($socialNetwork as $item)
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label> {{ ucfirst($item->descripcion_red_social) }} </label>
                                            <input type="text" value="" name="social_{{ $item->nombre_red_social }}_param_institution-{{ $item->codigo_red_social }}" id="social_{{ $item->nombre_red_social }}_param_institution" class="form-control">
                                            </div>
                                        @endforeach       
                                    @endif

                                    @if (isset($socialNetworkInsti) && count($socialNetworkInsti) > 0)
                                        @foreach ($socialNetworkInsti as $item)
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <label> {{ ucfirst($item->descripcion_red_social) }} </label>
                                            <input type="text" value="{{ $item->valor_red_social_institucion }}" name="social_{{ $item->nombre_red_social }}_param_institution-{{ $item->codigo_red_social }}" id="social_{{ $item->nombre_red_social }}_param_institution" class="form-control">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div id="render-meta-institution-tpl">
                                    {{--  Render parameters institution  --}}
                                </div>
                                <div class="text-center">
                                    <br>
                                    <button type="submit" class="btn btn-success">Enviar</button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                        
                    </div>
                    <div role="modules" class="tab-pane fade" id="tab_modules" aria-labelledby="tab-modules">
                        <div class="well">
                            <div id="render-accordion-modules-institution-tpl">
                                {{-- Render accordion modules --}}
                            </div>
                        </div>
                    </div>
                    <div role="terms" class="tab-pane fade" id="tab_terms" aria-labelledby="tab-terms">
                        <div class="well">
                            {!! Form::open(array('route' => null, 'id' => 'form-create-terms-institution', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Fecha Final Términos</label>
                                        <div class="form-group">
                                        <input type='text' class="form-control datepicker"  data-inputmask="'mask':'9999-99-99'" name="fecha_fin_termino_condiciones" id="fecha_fin_termino_condiciones" value="{{ isset($versionTerms) ? $versionTerms->fecha_fin_terminos : '' }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Términos y condiciones </label>
                                        <section id="terminos_condiciones" name="terminos_condiciones"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Fecha Final Términos</label>
                                        <div class="form-group">
                                            <input type='text' class="form-control datepicker"  data-inputmask="'mask':'9999-99-99'" name="fecha_fin_politicas_privacidad" id="fecha_fin_politicas_privacidad" value="{{ isset($versionPrivacy) ? $versionPrivacy->fecha_fin_terminos : '' }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Políticas de privacidad </label>
                                        <section id="politicas_privacidad" name="politicas_privacidad"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Fecha Final Términos</label>
                                        <div class="form-group">
                                            <input type='text' class="form-control datepicker"  data-inputmask="'mask':'9999-99-99'" name="fecha_fin_politica_ventas" id="fecha_fin_politica_ventas" value="{{ isset($versionVentas) ? $versionVentas->fecha_fin_terminos : '' }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Políticas de ventas </label>
                                        <section id="politica_ventas" name="politica_ventas"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Fecha Final Términos</label>
                                        <div class="form-group">
                                            <input type='text' class="form-control datepicker"  data-inputmask="'mask':'9999-99-99'"name="fecha_fin_politica_mentoria" id="fecha_fin_politica_mentoria" value="{{ isset($versionMentoring) ? $versionMentoring->fecha_fin_terminos : '' }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Políticas Mentoria </label>
                                        <section id="politicas_mentoria" name="politicas_mentoria"></section>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label>Fecha Final Términos</label>
                                        <div class="form-group">
                                            <input type='text' class="form-control datepicker"  data-inputmask="'mask':'9999-99-99'" name="fecha_fin_politica_evaluadores" id="fecha_fin_politica_evaluadores" value="{{ isset($versionEvaluators) ? $versionEvaluators->fecha_fin_terminos : '' }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label> Políticas de evaluadores </label>
                                        <section id="politicas_evaluadores" name="politicas_evaluadores"></section>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <br>
                                    <button type="submit" class="btn btn-success">Enviar</button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<script type="text/template" id="template-parameters-institution-tpl">
    <h3><label> Parametros Generales </label></h3>
    <div class="row">
        @if (isset($parametroInstitucion) && count($parametroInstitucion) > 0)
            @foreach ($parametroInstitucion as $item)
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <label data-format="alias">{{ $item->descripcion_parametro }}</label>
                    <div class="input-group colorpicker-component sample-selector">
                        <input type="text" value="{{ $item->valor_parametro }}" name="parameters_{{ $item->nombre_parametro }}-{{ $item->codigo_parametro }}" id="parameters_{{ $item->nombre_parametro }}-{{ $item->codigo_parametro }}" class="form-control sample-selector" />
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>

               <%  if (window._tp.typeParameterInstitution['image_logo_header'] == {{ $item->codigo_tipo_parametro }} ) { %>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <label> {{ $item->descripcion_parametro }} <i> (Tamaño Requerido: 130x60) </i> </label>
                    <div id="{{ $item->nombre_parametro }}" data-parametro="{{ $item->codigo_parametro }}" class="uploader-attachments">
                        {{-- Render file uploader --}}
                    </div>
                </div>
               <% } %>
            @endforeach
            
        @else
            <% if (!_.isEmpty(parameters)) { 
                parameters.forEach(function(element) {
                    if (window._tp.typeParameterInstitution['image_logo_header'] == element.codigo_tipo_parametro) {
            %>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label> <%= element.descripcion_parametro %> <i> (Tamaño Requerido: 130x60) </i> </label>
                            <div id="<%= element.nombre_parametro %>" data-parametro="<%= element.codigo_parametro %>" class="uploader-attachments">
                                {{-- Render file uploader --}}
                            </div>
                        </div>
                    <% } else { %>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <label data-format="alias"><%= element.descripcion_parametro %> <i> (Tamaño Requerido: 130x60) </i></label>
                            <div class="input-group colorpicker-component sample-selector">
                                <input type="text" value="" name="parameters_<%=  element.nombre_parametro %>-<%= element.codigo_parametro %>" id="parameters_<%=  element.nombre_parametro %>-<%= element.codigo_parametro %>" class="form-control sample-selector" />
                                <span class="input-group-addon"><i></i></span>
                            </div>
                        </div>
                    <% } %>
                <% }) %>
            <% } else { %>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <label> No hay parametros </label>
                </div>
            <% } %>
        @endif
    </div>
</script>


<script type="text/template" id="template-meta-institution-tpl">
    <h3><label>Meta datos</label></h3>
    <br>
    <div class="row">
        <% if (!_.isEmpty(meta)) { 
            meta.forEach(function(element) {
                if(element.nombre_meta == 'favicon') {      
        %>      
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label><%= element.descripcion_meta %> <i> (Tamaño Requerido: 16x16) </i></label>
                        <div id="<%= element.nombre_meta %>_meta_institucion" data-meta="<%= element.codigo_meta %>" class="uploader-attachments">
                            {{-- Render file uploader --}}
                        </div>
                    </div>
                <% 
                    } else if(element.nombre_meta == 'social_image') { 
                %>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                            <label><%= element.descripcion_meta %> <i> (Tamaño Requerido: 623x351) </i></label>
                            <div id="<%= element.nombre_meta %>_meta_institucion" data-meta="<%= element.codigo_meta %>" class="uploader-attachments">
                                {{-- Render file uploader --}}
                            </div>
                    </div>
                <% } else { %>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label><%= element.descripcion_meta %> </label>
                        <div id="<%= element.nombre_meta %>_meta_institucion" data-meta="<%= element.codigo_meta %>" class="uploader-attachments">
                            {{-- Render file uploader --}}
                        </div>
                    </div>
                <% } %>    
        <% 
            });
        %>
        <% } else { %>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label> No hay parametros </label>
            </div>
        <% } %>
    </div>
</script>

<script type="text/template" id="template-accordion-models-institution-tpl">
    <h3><label>Modulos</label></h3>
    <br>
    <div class="row">
        
            <div class="col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(array('route' => null, 'id' => 'form-create-modules-institution', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <% if (!_.isEmpty(modulos)) { 
                        modulos.forEach(function(element) {
                    %>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="<%=  element.nombre_modulo.toLowerCase() %>_heading">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#<%=  element.nombre_modulo.toLowerCase() %>_collapse" aria-expanded="true" aria-controls="<%=  element.nombre_modulo.toLowerCase() %>_collapse">
                                    <%= element.descripcion_modulo %>
                                </a>
                            </h4>
                            </div>
                            <div id="<%=  element.nombre_modulo.toLowerCase() %>_collapse" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<%=  element.nombre_modulo.toLowerCase() %>_heading">
                            <div class="panel-body">
                                <div class="row">
                                    <% if (!_.isEmpty(parametros)) { 
                                        parametros.forEach(function(elementPlataform) {
                                            if (!_.isEmpty(valueParameters)) {
                                                if (elementPlataform.codigo_modulo == element.codigo_modulo) {
                                                    if (window._tp.typeParameterInstitution['color'] == elementPlataform.codigo_tipo_parametro) {
                                            %>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <label data-format="alias"><%= elementPlataform.descripcion_parametro %></label>
                                                        <div class="input-group colorpicker-component sample-selector">
                                                            <input type="text" value="<%= elementPlataform.valor_parametro_modulo %>" name="<%=  elementPlataform.nombre_parametro.toLowerCase() %>-<%= element.codigo_modulo %>-<%= elementPlataform.codigo_parametro %>" id="<%=  elementPlataform.nombre_parametro.toLowerCase() %>_<%= element.codigo_modulo %>_<%= elementPlataform.codigo_parametro %>" class="form-control sample-selector" />
                                                            <span class="input-group-addon"><i></i></span>
                                                        </div>
                                                    </div>
                                            <% 
                                                    } else if (window._tp.typeParameterInstitution['image'] == elementPlataform.codigo_tipo_parametro) {
                                            %>
                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                        <label data-format="alias"> Imagen </label>
                                                        <div id="imagen_modulos_institucion_<%= element.codigo_modulo %>" data-parametro="<%= elementPlataform.codigo_parametro %>" data-modulo="<%= element.codigo_modulo %>" class="uploader-attachments">
                                                            {{-- Render file uploader --}}
                                                        </div>                 
                                                    </div>
                                            <%
                                                    } else if (window._tp.typeParameterInstitution['value'] == elementPlataform.codigo_tipo_parametro) {
                                            %>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <label><%= elementPlataform.descripcion_parametro %> </label>
                                                        <input type="text" value="<%= elementPlataform.valor_parametro_modulo %>" name="<%=  elementPlataform.nombre_parametro.toLowerCase() %>-<%= element.codigo_modulo %>-<%= elementPlataform.codigo_parametro %>" id="<%=  elementPlataform.nombre_parametro.toLowerCase() %>_<%= element.codigo_modulo %>_<%= elementPlataform.codigo_parametro %>" class="form-control">
                                                    </div>
                                            <%
                                                    }                                        
                                                }
                                            } else {
                                                if (window._tp.typeParameterInstitution['color'] == elementPlataform.codigo_tipo_parametro) {
                                                %>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <label data-format="alias"><%= elementPlataform.descripcion_parametro %></label>
                                                        <div class="input-group colorpicker-component sample-selector">
                                                            <input type="text" value="" name="<%=  elementPlataform.nombre_parametro.toLowerCase() %>-<%= element.codigo_modulo %>-<%= elementPlataform.codigo_parametro %>" id="<%=  elementPlataform.nombre_parametro.toLowerCase() %>_<%= element.codigo_modulo %>_<%= elementPlataform.codigo_parametro %>" class="form-control sample-selector" />
                                                            <span class="input-group-addon"><i></i></span>
                                                        </div>
                                                    </div>
                                                <%
                                                } else if (window._tp.typeParameterInstitution['image'] == elementPlataform.codigo_tipo_parametro) {

                                                %>
                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                        <label data-format="alias"> Imagen </label>
                                                        <div id="imagen_modulos_institucion_<%= element.codigo_modulo %>" data-parametro="<%= elementPlataform.codigo_parametro %>" data-modulo="<%= element.codigo_modulo %>" class="uploader-attachments">
                                                            {{-- Render file uploader --}}
                                                        </div>                 
                                                    </div>
                                                <%
                                                } else if (window._tp.typeParameterInstitution['value'] == elementPlataform.codigo_tipo_parametro) {

                                                %>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <label><%= elementPlataform.descripcion_parametro %> </label>
                                                        <input type="text" value="" name="<%=  elementPlataform.nombre_parametro.toLowerCase() %>-<%= element.codigo_modulo %>-<%= elementPlataform.codigo_parametro %>" id="<%=  elementPlataform.nombre_parametro.toLowerCase() %>_<%= element.codigo_modulo %>_<%= elementPlataform.codigo_parametro %>" class="form-control">
                                                    </div>
                                                <%

                                                }
                                            }
                                        });
                                    }
                                    %>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label> Estado </label>
                                        <select name="modulo_institucion_estado-<%= element.codigo_modulo %>" id="modulo_institucion_estado-<%= element.codigo_modulo %>" class="form-control">
                                            <option value="{{ App\Models\Core\Estado::getState('active') }}" <% !_.isEmpty(valueParameters) ? 'selected' : '' %> > Activo </option>
                                            <option value="{{ App\Models\Core\Estado::getState('inactive') }}" <% _.isEmpty(valueParameters) ? 'selected' : '' %>> Inactivo </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    <%
                        });
                    }
                    %>
                    <div class="text-center">
                        <br>
                        <button type="submit" class="btn btn-success">Enviar</button>
                    </div>
                {!! Form::close() !!}
            </div>
        
    </div>
</script>

@stop