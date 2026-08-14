@extends('reports.management.management')

@section('management.content')
	{{-- template management report aside filter --}}
	<script type="text/template" id="management-list-tpl">
   		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Gestión</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                	<div id="content-management">
						  	
                            <!-- start management -->
              				<div class="table-responsive">
	                            <table id="management-search-table" class="table table-striped table-bordered" data-page-length="15">
	                                <thead>
		                                <tr>
				                            <th>Nombre</th>
				                            <th>Fecha</th>
				                            <th>Tamaño</th>
				                            <th class="text-center">Acción</th>
		                                </tr>
	                                </thead>
									<tbody >
										<!-- Render view  -->
									</tbody>
	                            </table>
	                        </div>
                            <!-- end user management -->
                  		</div>
	                </div>
	           </div>
        	</div>
    	</div>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-report-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li><a href="<%- full.download %>" class="btn-active"><i class="fa fa-download"></i> Descargar</a>
				</li>
				<li><a href="#" class="btn-delete" data-action="eliminar" data-id="<%- full.name %>" data-name="<%- full.name %>"><i class="fa fa-trash"></i> Eliminar</a>
				</li>
			</ul>
		</div>
	</script>

	{{-- template text confirm --}}
	<script type="text/template" id="report-delete-confirm-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> el reporte <strong><%- report %></strong>?</p>
	</script>
@stop