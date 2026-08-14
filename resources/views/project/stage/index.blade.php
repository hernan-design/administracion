@extends('project.stage.stage')

@section('stages.content')
    {{-- ------------------ Project stages Templates JS -------------------- --}}
    {{-- template project stages aside filter --}}
    <script type="text/template" id="project-stages-list-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Etapas <small>Lista</small></h2>
					<ul class="nav navbar-right panel_toolbox">
								<li>
								<a href="{{ route('entidad.etapa.create') }}">
									<i class="glyphicon glyphicon-pencil"> 	</i>Crear </a>

								</li>
								</ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="well">
						{!! Form::open(array('route' => null, 'id' => 'form-project-stages-filter', 'class' =>'')) !!}
						<div class="row">
							<div class="col-md-3 col-md-offset-3 col-sm-12 col-xs-12 form-group">
								<label>Nombre</label>
								<input type="text" name="nombre_etapa_proyecto" id="nombre_etapa_proyecto" class="form-control">
							</div>
							<div class="col-md-3 col-sm-12 col-xs-12 form-group">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($states as $state)
										<option value="{{ $state->codigo_estado }}">{{ $state->nombre_estado }}</option>
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
					<br/>
                    <div class="table-responsive">
                        <table id="project-stages-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Nombre</th>
                                <th class="column-title">Descripción</th>
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

	<script type="text/template" id="confirm-project-stages-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la etapa <strong><%- project_stage_name %></strong>?</p>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_etapa_proyecto %>" data-name="<%- full.nombre_etapa_proyecto %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['active']) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_etapa_proyecto %>" data-name="<%- full.nombre_etapa_proyecto %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
				<li>
					<a href="<%= window.Misc.urlFull(Route.route('entidad.etapa.edit', {etapa: full.codigo_etapa_proyecto}) ) %>" title="Editar etapa"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
			</ul>
		</div>
	</script>
@stop
