@extends('event.event')

@section('event.content')
    {{-- ------------------ Event Templates JS -------------------- --}}
    {{-- template Event aside filter --}}
    <script type="text/template" id="event-list-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Eventos <small>Lista</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="well">
						{!! Form::open(array('route' => null, 'id' => 'form-event-filter', 'class' =>'')) !!}
						<div class="row">
							<div class="col-md-3 col-md-offset-3 col-sm-12 col-xs-12 form-group">
								<label>Nombre</label>
								<input type="text" name="nombre_evento" id="nombre_evento" class="form-control">
							</div>
							<div class="col-md-3 col-sm-12 col-xs-12 form-group">
								<label>Estado</label>
								<select type="text" name="codigo_estado" id="codigo_estado" class="form-control">
									<option value="">Seleccione...</option>
									@foreach ($states as $state)
										<option value="{{ $state->codigo_estado }}">{{ ucfirst(strtolower($state->nombre_estado)) }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-7 col-md-offset-5 ">
								<button type="button" class="btn btn-primary btn-search"> Buscar</button>
								<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
								<a href="{{ route('evento.create') }}" class="btn btn-success btn-create">Crear</a>
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
                        <table id="event-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                            <tr class="headings">
								<th class="column-title">Codigo evento</th>
                                <th class="column-title">Nombre</th>
								<th class="column-title">Fecha evento</th>
                                <th class="column-title text-center">Acción</th>
                            </tr>
                            </thead>
                            <tbody >
                            <!-- Render view  -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="x_content">
					<div class="well">
						<div class="row">
							<div class="col-md-7 col-md-offset-4 ">
								<a
									href="<%- window.Misc.urlFull(Route.route('eventos.usuario.index')) %>"
									class="btn btn-success btn-create"
								>
									Ir a Eventos por usuario
								</a>
							</div>
						</div>
					</div>
                </div>

            </div>
        </div>
    </script>

	<script type="text/template" id="confirm-event-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> el evento <strong><%- event_name %></strong>?</p>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_evento %>" data-name="<%- full.nombre_evento %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% }
				if(full.codigo_estado == window._tp.getState['active']) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_evento %>" data-name="<%- full.nombre_evento %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
				<li>
					<a href="<%= window.Misc.urlFull(Route.route('evento.respuestas.exportar', {evento: full.codigo_evento})) %>" title="Exportar"><i class="fa fa-file-text"></i> Exportar
					</a>
				</li>
				<li>
					<a href="<%- window.Misc.urlFull(Route.route('evento.edit', {evento: full.codigo_evento}) ) %>" title="Editar rol"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
				<li>
					<a href="!#" id="clone-event"  data-event="<%- full.codigo_evento %>" title="Duplicar evento"><i class="fa fa-clipboard"></i> Duplicar
					</a>
				</li>
			</ul>
		</div>
	</script>
@stop
