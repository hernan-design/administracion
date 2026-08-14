
@extends('users.user')

@section('users.content')
    {{-- ------------------ User Templates JS -------------------- --}}
    {{-- template User aside filter --}}
    <script type="text/template" id="user-list-tpl">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Usuarios <small>Lista</small></h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              @if (session('message'))
                <div class="alert alert-info">
                  {{ session('message') }}
                </div>
              @endif
              <div class="well">
                {!! Form::open(array('route' => null, 'id' => 'form-user-filter', 'class' =>'')) !!}
                <div class="row">
                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control">
                  </div>
                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Correo</label>
                    <input type="text" name="correo_electronico" id="correo_electronico" class="form-control">
                  </div>
                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Nick Name</label>
                    <input type="text" name="nickname_usuario" id="nickname_usuario" class="form-control">
                  </div>
                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Municipio</label>
                    <select name="codigo_ciudad" id="codigo_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-param="user_city" data-placeholder="Seleccione..."  placeholder="Seleccione..." style="width: 100%">
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Numero de identificación</label>
                    <input type="text" name="identificacion_usuario" id="identificacion_usuario" class="form-control">
                  </div>

                  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                    <label>Tipo de registro</label>
                    <select name="tipo_usuario" id="tipo_usuario" class="form-control">
                      <option value="">Selecciona...</option>
                      <option value="entity">Entidad</option>
                      <option value="person">Persona</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="text-center">
                      <button type="button" class="btn btn-primary btn-search"> Buscar</button>
                      <button type="button" class="btn btn-default btn-clear"> Limpiar</button>
                      @if( Auth::user()->ability('admin,super-admin', 'create', ['module'  => '/usuario']) )
                                            <a href="usuario/create" class="btn btn-success"> Crear</a>
                                        @endif
                      @if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/usuario']) )
                        <button type="button" class="btn btn-info data-export"> Exportar</button>
                      @endif
                      @if ( session('institution')->codigo_institucion == 30 || session('institution')->codigo_institucion == 51 ||
                      session('institution')->codigo_institucion == 75)
                        <a
                        href="#upload-documents-container"
                        type="button"
                        class="btn btn-warning data-import"
                        title="Importar archivo para cargue usuarios"
                        >
                          Importar
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
                {!! Form::close() !!}
              </div>
              <br/>
              <div class="table-responsive">
                  <table id="user-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                      <thead>
                      <tr class="headings">
                          <th class="column-title">Nombre</th>
                          <th class="column-title">Nick Name</th>
                          <th class="column-title">Correo</th>
                          <th class="column-title">Fecha de Nacimiento</th>
                          <th class="column-title">Municipio</th>
                          <th class="column-title">Fecha de Aceptación de Términos</th>
                          <th class="column-title"> Actividad Detallada </th>
                      </tr>
                      </thead>
                      <tbody >
                      <!-- Render view  -->
                      </tbody>
                  </table>
              </div>
            </div>
            <div class="x_content">
                <div class="row">
									<div class="small-12 col-md-12 columns">
											<div id="upload-documents-container" class="editable-item">
												<input type="hidden" id="codigo_usuario" value="{{ \Auth::user()->codigo_usuario }}">
												<section id="wrap-documents" class="">
													{{-- Render  Documents --}}
												</section>
											</div>
									</div>
								</div>
            </div>
          </div>
      </div>
    </script>

    <script type="text/template" id="buttons-action-activity-detail">
      <div class="btn-group tp_btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
          <i class="fa fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" role="menu">
          <li>
            <a href="#" title="Detalles de actividad" class="btn btn-default btn-xs detail-activity" data-user="<%- full.codigo_usuario %>" data-turn="false" data-action="Detalle de actividad">
              <i class="fa fa-eye"></i> Ver detalles de actividad
            </a>
          </li>
        </ul>
      </div>
    </script>

    @switch(session('institution')->codigo_institucion)
      @case(30)
        <script type="text/template" id="edit-upload-documents-form-tpl">
          {!! Form::open(array('data-abide' => '', 'id' => 'form-edit-upload-documents')) !!}
          <div class="row" style="background = #eeeeee">
            <div class="col-xs-12 col-sm-4 col-md-12  text-center">
              <h5 >Archivos adjuntos</h5>
              <p>
                <a
                  href="https://s3.us-west-2.amazonaws.com/static.tuproyecto.com/file/cargue_datos_compensar.xlsx"
                  type="button"
                  title="Descargar Plantilla"
                  >
                  <strong>Paso: 1. Decarga la plantilla.</strong> <i class="fa fa-cloud-download" aria-hidden="true"></i>
                </a>
              </p>
              <p>
                <strong>Paso: 2. En esta sección debes adjuntar el excel para cargar usuarios.</strong>
              </p>
            </div>
          </div>
            {{-- <input type="hidden" id="name_project" name="name_project" value="{{ $entityBuild->nombre_usuario }}"> --}}
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-12 columns">
              <div id="fnuploader-attach-file" class="uploader-attachments"></div>
            </div>
          </div>
          <div class="row save-section" style="justify-content: center;margin-top: 5px;">
            <div>
              {!! Form::submit('Cargar archivos', array('class' => 'btn btn-success btn-block sky-blue btn-shadow radius')) !!}
            </div>
          </div>
          {!! Form::close() !!}
        </script>
      @break
      @case(51)
        <script type="text/template" id="edit-upload-documents-form-tpl">
          {!! Form::open(array('data-abide' => '', 'id' => 'form-edit-upload-documents')) !!}
          <div class="row" style="background = #eeeeee">
            <div class="col-xs-12 col-sm-4 col-md-12  text-center">
              <h5 >Archivos adjuntos</h5>
              <p>
                <a
                  href="https://s3.us-west-2.amazonaws.com/static.tuproyecto.com/file/cargue_datos_innpulsa+(1).xlsx"
                  type="button"
                  title="Descargar Plantilla"
                  >
                  <strong>Paso: 1. Decarga la plantilla.</strong> <i class="fa fa-cloud-download" aria-hidden="true"></i>
                </a>
              </p>
              <p>
                <strong>Paso: 2. En esta sección debes adjuntar el excel para cargar usuarios.</strong>
              </p>
            </div>
          </div>
            {{-- <input type="hidden" id="name_project" name="name_project" value="{{ $entityBuild->nombre_usuario }}"> --}}
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-12 columns">
              <div id="fnuploader-attach-file" class="uploader-attachments"></div>
            </div>
          </div>
          <div class="row save-section" style="justify-content: center;margin-top: 5px;">
            <div>
              {!! Form::submit('Cargar archivos', array('class' => 'btn btn-success btn-block sky-blue btn-shadow radius')) !!}
            </div>
          </div>
          {!! Form::close() !!}
        </script>
      @break
      @case(75)
        <script type="text/template" id="edit-upload-documents-form-tpl">
          {!! Form::open(array('data-abide' => '', 'id' => 'form-edit-upload-documents')) !!}
          <div class="row" style="background = #eeeeee">
            <div class="col-xs-12 col-sm-4 col-md-12  text-center">
              <h5 >Archivos adjuntos</h5>
              <p>
                <a
                  href="https://s3.us-west-2.amazonaws.com/static.tuproyecto.com/file/users_propace.xlsx"
                  type="button"
                  title="Descargar Plantilla"
                  >
                  <strong>Paso: 1. Decarga la plantilla.</strong> <i class="fa fa-cloud-download" aria-hidden="true"></i>
                </a>
              </p>
              <p>
                <strong>Paso: 2. En esta sección debes adjuntar el excel para cargar usuarios.</strong>
              </p>
            </div>
          </div>
            {{-- <input type="hidden" id="name_project" name="name_project" value="{{ $entityBuild->nombre_usuario }}"> --}}
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-12 columns">
              <div id="fnuploader-attach-file" class="uploader-attachments"></div>
            </div>
          </div>
          <div class="row save-section" style="justify-content: center;margin-top: 5px;">
            <div>
              {!! Form::submit('Cargar archivos', array('class' => 'btn btn-success btn-block sky-blue btn-shadow radius')) !!}
            </div>
          </div>
          {!! Form::close() !!}
        </script>
    @break
  @endswitch
@stop
