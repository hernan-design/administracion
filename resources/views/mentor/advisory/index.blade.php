
@extends('mentor.advisory.advisory')

@section('advisory.content')
    {{-- ------------------ User Templates JS -------------------- --}}
    {{-- template User aside filter --}}
    <script type="text/template" id="advisory-list-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Mentores <small>Lista</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="well">
						{!! Form::open(array('route' => null, 'id' => 'form-user-filter', 'class' =>'')) !!}
						<div class="row">
							<div class="col-md-4 col-sm-6 col-xs-12 form-group">
								<label>Nombre</label>
								<input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control">
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12 form-group">
								<label>Correo</label>
								<input type="text" name="correo_electronico" id="correo_electronico" class="form-control">
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12 form-group">
								<label>Nick Name</label>
								<input type="text" name="nickname_usuario" id="nickname_usuario" class="form-control">
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12 form-group">
								<label>Numero de identificación</label>
								<input type="number" name="cedula_usuario" id="cedula_usuario" class="form-control">
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12 form-group">
								<label>Municipio</label>
								<select name="codigo_ciudad" id="codigo_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
								</select>
							</div>
							<div class="col-md-4 col-sm-6 col-xs-12 form-group">
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
						{!! Form::close() !!}
						</div>
					</div>
					<br/>
                    <div class="table-responsive">
                        <table id="advisory-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Nombre</th>
								<th class="column-title">Nick Name</th>
                                <th class="column-title">Correo</th>
                                <th class="column-title">Municipio</th>
								<th class="column-title text-center">Promedio evaluaciones</th>
								<th class="column-title text-center">Fecha Aceptación Términos de Mentoría</th>
                                <th class="column-title text-center">Acciones</th>
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

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% } %>

				<%  if(full.codigo_estado == window._tp.getState['review']) { %>
					<li><a href="#" class="btn-active success" data-action="aprobar" data-id="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-check"></i> Aprobar</a>
					</li>
					<li><a href="#" class="btn-active success" data-action="rechazar" data-id="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-state="<%- window._tp.getState['rejected'] %>"><i class="fa fa-close"></i> Rechazar</a>
					</li>
				<% } %>

				<% if(full.codigo_estado == window._tp.getState['active'] ) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_usuario %>" data-name="<%- full.nombre_usuario %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
			</ul>
		</div>
	</script>
@stop
