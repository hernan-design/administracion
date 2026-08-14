{{-- views/form/detail.blade.php --}}
@extends('evaluation.evaluation')

@section('evaluation.content')
	{{-- ------------------ Form Templates JS -------------------- --}}

	{{-- Detail form view --}}
	<script type="text/template" id="evaluation-detail-tpl">
	  	<div class="row">
		    <div class="col-md-12 col-sm-12 col-xs-12">
		        <div class="x_panel">
		            <div class="x_title">
		            	<h2>Vista <small>evaluación de curso</small></h2>
	            		<ul class="nav navbar-right panel_toolbox">
                    <li>
                      <a id="history-back" href=""><i class="fa fa-chevron-left"></i> Atrás</a>
                    </li>
                  </ul>
		            	<div class="clearfix"></div>
		            </div>
		            <div class="x_content">
			            <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
			               	<div class="profile_img">
			                 	<div id="crop-avatar">
			                      	<!-- Current avatar -->
			                      	<img class="img-responsive avatar-view" src="<%- imagen_encuesta %>" onerror="imageDefault(this, '{{ config('tuproyecto.no_image') }}')" alt="<%- nombre_encuesta %>" title="<%- nombre_encuesta %>">
			                    </div>
			               	</div>
			            </div>
			            <div class="col-md-9 col-sm-9 col-xs-12">
			                <h3><strong><%- nombre_encuesta %></strong></h3>
		            	</div>
				        <br/>
		                <div class="col-md-12 col-sm-12 col-xs-12">
		                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
		                    	<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
			                        <li role="presentation" class="active">
			                        	<a href="#tab_answers" role="tab" id="answers-tab" data-toggle="tab" aria-expanded="false">Respuestas</a>
			                        </li>
		                    	</ul>
			                	<div id="myTabContent" class="tab-content">
				                    <div role="tabpanel" class="tab-pane fade active in" id="tab_answers" aria-labelledby="profile-tab">
		                      			<div id="grid-container">
											          {{-- Render id="users-answer-tpl" --}}
										</div>
			                    	</div>
			                    </div>
		                	</div>
		            	</div>
		            </div>
		        </div>
	        </div>
		</div>
	</script>

	{{-- Template users answer --}}
	<script type="text/template" id="users-answer-tpl">
		<div id="users-answer-list">
      @if (session('message'))
      <div class="alert alert-info">
          {{ session('message') }}
      </div>
      @endif
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'evaluation-filter-user-answer', 'class' =>'')) !!}
					<div class="row">
						<div class="col-md-4 col-md-offset-4 col-sm-6 col-xs-12">
							<label>Nombre</label>
							<input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control">
						</div>
                    </div>
					<br>
                    <div class="text-center">
                  		<button type="button" class="btn btn-primary btn-search">Buscar</button>
                      <button type="button" class="btn btn-default btn-clear">Limpiar</button>
                      @if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/evaluacion']) )
						            <button type="button" class="btn btn-info data-export"> Exportar</button>
                      @endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start users answer -->
            <div class="table-responsive">
				<table id="users-answer-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Usuario</th>
							<th>Inicio</th>
							<th>Fin</th>
              <th>Módulo</th>
              <th>Puntaje total</th>
							<th>Estado</th>
							<th class="text-center">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
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
				<li>
					<a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
				</li>
			</ul>
		</div>
	</script>
@stop
