@extends('diagnostic.general.diagnostic')

@section('diagnostics.content')

    {{-- ------------------ Diagnostic Templates JS -------------------- --}}
    {{-- template Diagnostic aside filter --}}
    <script type="text/template" id="diagnostic-list-tpl">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Diagnosticos <small>Lista</small></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="well">
              <div class="row">
                <div class="col-md-7 col-md-offset-5 ">
                  <a href="{{ route('diagnosticos.create') }}" class="btn btn-success btn-create">Crear</a>
                </div>
              </div>

            </div>
            @if (session('message'))
						  <div class="alert alert-info">
							  {{ session('message') }}
						  </div>
					  @endif
					  <br/>

            <div class="table-responsive">
              <table id="diagnostic-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                <thead>
                  <tr class="headings">
                    <th class="column-title">Código Diagnóstico</th>
                    <th class="column-title">Nombre</th>
                    <th class="column-title">Descripción</th>
                    <th class="column-title">Estado</th>
                    <th class="column-title text-center">Acción</th>
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
