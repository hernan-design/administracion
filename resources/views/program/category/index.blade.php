
@extends('program.category.category')

@section('categories.content')
    {{-- ------------------ Categories Templates JS -------------------- --}}
    {{-- template categories aside filter --}}
    <script type="text/template" id="categories-list-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Categorías <small>Lista</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="well">
						{!! Form::open(array('route' => null, 'id' => 'form-categories-filter', 'class' =>'')) !!}
						<div class="row">
							<div class="col-md-3 col-md-offset-3 col-sm-12 col-xs-12 form-group">
								<label>Nombre</label>
								<input type="text" name="nombre_categoria_proceso" id="nombre_categoria_proceso" class="form-control">
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
								<a href="{{ route('convocatoria.categoria.create') }}" class="btn btn-success btn-create">Crear</a>
							</div>
						</div>
						{!! Form::close() !!}
					</div>
					<br/>
                    <div class="table-responsive">
                        <table id="categories-list-search-table" class="table table-striped table-bordered" data-page-length="15">
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

	<script type="text/template" id="confirm-categories-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> la categoría <strong><%- category_name %></strong>?</p>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<%  if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_categoria_proceso %>" data-name="<%- full.nombre_categoria_proceso %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['active']) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_categoria_proceso %>" data-name="<%- full.nombre_categoria_proceso %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% } %>
				<li>
					<a href="<%= window.Misc.urlFull(Route.route('convocatoria.categoria.edit', {categorium: full.codigo_categoria_proceso}) ) %>" title="Editar categoría"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
			</ul>
		</div>
	</script>
@stop
