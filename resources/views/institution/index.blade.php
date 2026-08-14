@extends('institution.institution')

@section('institution.content')
	<div class="col-xs-12">
        <div class="x_panel">
	        <div class="x_title">
	            <h2>Instituciones <small>Lista</small></h2>
	            <div class="clearfix"></div>
	        </div>
			<div class="x_content">
                <div id="content-form-institution-filter" class="well">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <label>Nombre Institución</label>
                            <input type="text" name="nombre_institucion" id="nombre_institucion" class="form-control">
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <label>Estado</label>
                            <select name="estado_institucion" id="estado_institucion" class="form-control">
                                <option value="" selected>Seleccione...</option>
                                <option value="8">Activo</option>
                                <option value="9">Inactivo</option>
                            </select>
                        </div>
                    </div>
					<br>
					<div class="text-center">
                  		<button type="button" class="btn btn-primary btn-search">Buscar</button>
						<button type="button" class="btn btn-default btn-clear">Limpiar</button>
						<a href="{{ route('institucion.create') }}" class="btn btn-success btn-create">Crear</a>
		            </div>
                </div>

                <div class="table-responsive">
				<table id="institution-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Código instiución</th>
							<th>Nombre institución</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
			</div>
		</div>
	</div>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li>
					<a href="<%= window.Misc.urlFull(Route.route('institucion.edit', {institucion: full.codigo_institucion}) ) %>" title="Editar" target="_blank"><i class="fa fa-pencil"></i> Editar </a>
				</li>
			</ul>
		</div>
	</script>
@stop