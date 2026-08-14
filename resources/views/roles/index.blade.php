
@extends('roles.role')

@section('roles.content')
    {{-- ------------------ Rol Templates JS -------------------- --}}
    {{-- template Role aside filter --}}
    <script type="text/template" id="role-list-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Roles <small>Lista</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					@if (session('message'))
						<div class="alert alert-info">
							{{ session('message') }}
						</div>
					@endif
					<div class="well">
						{!! Form::open(array('route' => null, 'id' => 'form-role-filter', 'class' =>'')) !!}
						<div class="row">
							<div class="col-md-3 col-lg-offset-3 col-sm-12 col-xs-12 form-group">
								<label>Nombre</label>
								<input type="text" name="title" id="title" class="form-control">
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
							<div class="col-md-12 ">
								<div class="text-center">
									<button type="button" class="btn btn-primary btn-search"> Buscar</button>
									<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
									<a href="{{ route('rol.create') }}" class="btn btn-success btn-create">Crear</a>

									@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/rol']) )
										<button type="button" class="btn btn-info data-export-rols"> Exportar</button>
									@endif
								</div>
							</div>
						</div>
						{!! Form::close() !!}
					</div>
					<br/>
                    <div class="table-responsive">
                        <table id="role-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Nombre</th>
								<th class="column-title">Tipo</th>
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

	<script type="text/template" id="confirm-role-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> el rol <strong><%- role_name %></strong>?</p>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.id %>" data-name="<%- full.title %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['active'] && full.id != window._tp.getRole['admin']) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.id %>" data-name="<%- full.title %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
				<li>
					<a href="<%= window.Misc.urlFull(Route.route('rol.edit', {rol: full.id}) ) %>" title="Editar rol"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
			</ul>
		</div>
	</script>
@stop
