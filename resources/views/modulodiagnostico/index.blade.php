@extends('modulodiagnostico.modulediagnostic')

@section('modulediagnostics.content')

    {{-- ------------------ Diagnostic Templates JS -------------------- --}}
    {{-- template Diagnostic aside filter --}}
    <script type="text/template" id="diagnostic-module-list-tpl">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Diagnóstico Por Módulo <small>Lista</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="well">
              {!! Form::open(array('route' => null, 'id' => 'form-modulodiagnostic-filter', 'class' =>'')) !!}
              <div class="row">
                <div class="col-md-3 col-md-offset-3 col-sm-12 col-xs-12 form-group">
                  <label>Diagnóstico</label>
                  <select type="text" name="codigo_diagnostico" id="codigo_diagnostico" class="form-control">
                    <option value="">Seleccione...</option>
                    @foreach ($diagnosticosInsti as $diagnostic)
                      <option value="{{ $diagnostic->codigo_diagnostico }}">{{ ucfirst(strtolower($diagnostic->nombre_diagnostico)) }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                  <label>Sección</label>
                  <select type="text" name="codigo_menu_institucion" id="codigo_menu_institucion" class="form-control">
                    <option value="">Seleccione...</option>
                    @foreach ($menuInstitucion as $insti)
                      <option value="{{ $insti->codigo_menu_institucion }}">{{ ucfirst(strtolower($insti->nombre_menu_institucion)) }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-7 col-md-offset-5 ">
                  <button type="button" class="btn btn-primary btn-search"> Buscar</button>
                  <button type="button" class="btn btn-default btn-clear"> Limpiar</button>
                </div>
              </div>
              {!! Form::close() !!}
            </div>
            @if (session('message'))
						  <div class="alert alert-info">
							  {{ session('message') }}
						  </div>
					  @endif
					  <br/>

            <div class="table-responsive">
              <table id="diagnostic-module-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                <thead>
                  <tr class="headings">
                    <th class="column-title">Código Módulo Diagnóstico</th>
                    <th class="column-title">Nombre Diagnostico</th>
                    <th class="column-title">Secciones</th>
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
    </script>

    <script type="text/template" id="confirm-diagnostic-tpl">
      <p>¿Estás seguro de <strong><%- action %></strong> el diagnóstico <strong><%- diagnostic_name %></strong>?</p>
    </script>

    {{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
    <div class="btn-group tp_btn-group">
      <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
      <ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li>
					<a href="<%- window.Misc.urlFull(Route.route('diagnosticos.edit', {diagnostico: full.codigo_diagnostico}) ) %>" title="Editar Diagnóstico"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
			</ul>
    </div>
  </script>
@stop
