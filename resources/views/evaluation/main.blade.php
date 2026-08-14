@extends('evaluation.evaluation')

@section('evaluation.content')
	{{-- ------------------ Evaluation Templates JS -------------------- --}}
	{{-- template evaluation aside filter --}}
	<script type="text/template" id="list-evaluation-tpl">
		<div class="col-md-12 col-sm-12 col-xs-12">
	        <div class="x_panel">
		        <div class="x_title">
		            <h2>Evaluación <small>Lista</small></h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li>
                    <a href="<%= window.Misc.urlFull(Route.route('evaluacion.create') ) %>" title="Crear Evaluación">
                      <i class="glyphicon glyphicon-pencil"></i> Crear Evaluación
                    </a>
                  </li>
                </ul>


                <div class="clearfix"></div>
		        </div>
				<div class="x_content">
					<div class="table-responsive">
						<table id="evaluations-list-search-table" class="table table-striped table-bordered" data-page-length="15">
							<thead>
								<tr class="headings">
									<th class="column-title">Convocatoria</th>
									<th class="column-title">Evaluación</th>
									<th class="column-title text-center"># Asignados</th>
									<th class="column-title text-center"># Evaluados</th>
									<th class="column-title text-center" ># Pendientes</th>
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

	{{-- detail evaluation --}}
	<script type="text/template" id="evaluation-detail-tpl">
   		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2><%-nombre_evaluacion_emprendimiento %> </h2>
	                		<ul class="nav navbar-right panel_toolbox">
		                      	<li>
		                      	<a href="<%= window.Misc.urlFull(Route.route('evaluacion.index'))%>"><i class="fa fa-chevron-left"></i> Atrás</a>
		                      	</li>
                            <li>
                              <a href="<%= window.Misc.urlFull(Route.route('evaluacion.edit', {evaluacion: codigo_evaluacion_emprendimiento}) ) %>" title="Editar Evaluación">
                                <i class="glyphicon glyphicon-pencil"></i> Editar Evaluación
                              </a>
                            </li>
		                    </ul>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
					  	<div class="well">
			                {!! Form::open(array('route' => null,'id' => 'form-assignment-filter', 'class' =>'')) !!}
				                <div class="row">
				                	<div class="col-md-1 col-sm-12 col-xs-12 form-group">
								        <label>Aplicación</label>
			                            <input type="number" name="number_register" id="number_register" class="form-control">
			                     	</div>
					                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
							        	<label>Emprendimiento</label>
							            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
							        </div>
							        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
							        	<label>Email</label>
							            <input type="text" id="email" name="email" class="form-control">
							        </div>
							        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
							            <label>Evaluado</label>
							            <select id="evaluated" name="evaluated" class=" form-control" tabindex="-1">
					                        <option value="">Selecciona una opción</option>
				                            <option value="1">Si</option>
				                            <option value="2">No</option>
				                        </select>
							        </div>
							    </div>

							    <div class="row">
								    <div class="col-md-offset-2 col-md-4 col-sm-12 col-xs-12 form-group">
								        <label>Rango inferior</label>
								        <div class="row">
									        <div class="col-md-8 col-xs-12">
										        <select id="sortby" name="sortby" class="form-control" tabindex="-1">
				                            		<option value="">Selecciona un operador</option>
					                            	<option value=">">Mayor</option>
					                           		<option value="<">Menor</option>
					                            	<option value="=">Igual</option>
					                            	<option value=">=">Mayor igual</option>
					                            	<option value="<=">Menor igual</option>
					                           </select>
				                          	</div>
				                            <div class="col-md-4 col-xs-12">
				                            	<input type="number" name="scoring" id="scoring" step="0.5" class="form-control">
									    	</div>
								    	</div>
								    </div>

								    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
								        <label>Rango superior</label>
								        <div class="row">
									        <div class="col-md-8 col-xs-12">
										        <select id="sortby_2" name="sortby_2" class="form-control" tabindex="-1">
				                            		<option value="">Selecciona un operador</option>
					                            	<option value=">">Mayor</option>
					                           		<option value="<">Menor</option>
					                            	<option value="=">Igual</option>
					                            	<option value=">=">Mayor igual</option>
					                            	<option value="<=">Menor igual</option>
					                           </select>
				                          	</div>
				                            <div class="col-md-4 col-xs-12">
				                            	<input type="number" name="scoring_2" id="scoring_2" step="0.5" class="form-control">
									    	</div>
								    	</div>
								    </div>
							    </div>

							    <div class="row">
								    <input type="hidden" id="evaluation" name="evaluation" value="<%- codigo_evaluacion_emprendimiento %>">
			                        <div class="col-md-7 col-md-offset-5">
			                          	<button type="button" class="btn btn-primary btn-search"> Buscar</button>
					                	<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
									      	@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/evaluacion']) )
						                	<button type="button" class="btn btn-info data-export"> Exportar</button>
							           	@endif
			                        </div>
		                        </div>
					        {!! Form::close() !!}
					    </div>
						<br/>
                        <!-- start evaluations -->
                        <div class="table-responsive">
	                        <table id="evaluation-list-search-table" class="table table-striped table-bordered" data-page-length="15">
	                            <thead>
	                                <tr>
			                            <th>Aplicación</th>
			                            <th>Emprendimiento</th>
		                                <th>Evaluador</th>
		                                <th>Correo electronico</th>
		                                <th nowrap="nowrap">Fecha asignación</th>
		                                <th nowrap="nowrap">Fecha evaluación</th>
		                                <th>Puntaje</th>
	                                </tr>
	                            </thead>
								<tbody >
									<!-- Render view  -->
								</tbody>
	                        </table>
	                    </div>
                        <!-- end evaluations -->
	                </div>
	           </div>
        	</div>
    	</div>
	</script>

	{{-- template evaluation aside filter --}}
	<script type="text/template" id="list-assignment-tpl">
		<div class="col-md-12 col-sm-12 col-xs-12">
	        <div class="x_panel">
		        <div class="x_title">
		            <h2>Asignación <small>Lista</small></h2>
		            <div class="clearfix"></div>
		        </div>
				<div class="x_content">
					<div class="well">
                {!! Form::open(array('route' => null,'id' => 'form-list-assignment-filter', 'class' =>'form-horizontal')) !!}
                  <div class="row">
                    <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                    <label>Aplicación</label>
                    <input type="number" name="number_register" id="number_register" class="form-control">
                    </div>
                    <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                        <label>Emprendimiento</label>
                        <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                      <label>(Elige una convocatoria para seleccionar una fase)</label>
                      <select name="program" id="program" class="form-control select-program" required data-placeholder="Selecciona una convocatoria" placeholder="Selecciona una convocatoria">
                        <option value="">Selecciona una convocatoria</option>
                        @foreach( App\Models\Core\Convocatoria::getProgramList() as $value)
                          <option value="{{ $value->key }}">{{ $value->value}}</option>
                        @endforeach
                       </select>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                        <label>Fase</label>
                        <select name="phase" id="phase" class="form-control" required data-placeholder="Selecciona una fase" placeholder="Selecciona una fase">
                            <option value="">Selecciona una fase</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                      <label>Correo electronico</label>
                      <input type="text" id="email_evaluator" name="email_evaluator" class="form-control">
                    </div>
                    <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                      <label>Identificación</label>
                      <input type="number" id="documento_usuario" name="documento_usuario" class="form-control">
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                      <label>Asignación</label>
                      <select name="slcAsign" id="slcAsign" class="form-control" required data-placeholder="Selecciona una asigación" placeholder="Selecciona una asigación">
                        <option value="">Selecciona una asignación</option>
                        <option value="1"> Sin asignación (Proyectos sin asignación de evaluador) </option>
                        <option value="2"> Asignado (Proyectos con evaluador asignado) </option>
                        <option value="3"> Evaluado (Proyectos ya evaluados) </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-7 col-md-offset-5">
                  <button type="button" class="btn btn-primary btn-search"> Buscar</button>
                  <button type="button" class="btn btn-default btn-clear"> Limpiar</button>
            </div>
            {!! Form::close() !!}
          </div>
					<br/>
					<div class="table-responsive">
						<table id="evaluation-search-table" class="table table-striped table-bordered" data-page-length="15">
							<thead>
								<tr>
									<th>Aplicación</th>
									<th>Emprendimiento</th>
									<th>Convocatoria</th>
									<th>Fase</th>
									<th>Ver</th>
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
	</script>

	{{-- Detail Assignment --}}
	@php
		$slug = session('institution')->slug_institucion;
	@endphp
	@include("evaluation.assigments.$slug.template")

	{{-- jury assignment item --}}
	<script type="text/template" id="assignment-jury-item-tpl">
		<td>
			<%- nombre_usuario %> <br /> <%- correo_electronico %>

		</td>
		<td class="text-center"><%- window.moment([fecha_asignacion_jurado, hora_asignacion_jurado].join(' '), "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm") %></td>
		<td class="text-center"><%- nombre_estado %></td>
		<td>
			<input type="text" name="lugar_evaluacion_<%- codigo_asignacion_jurado %>" id="lugar_evaluacion_<%- codigo_asignacion_jurado %>"  class="form-control" value="<%- lugar_evaluacion_jurado %>" >
		</td>
		<td nowrap="nowrap">
			<input type="text" id="fecha_evaluacion_<%- codigo_asignacion_jurado %>" name="fecha_evaluacion_<%- codigo_asignacion_jurado %>" class="form-control datepicker" value="<%- fecha_evaluacion_jurado %>" data-inputmask="'mask':'9999-99-99'">
		</td>

		<% if(asignacion_especialidad) { %>
			<td>
				<select id="codigo_especialidad_<%- codigo_asignacion_jurado %>" name="codigo_especialidad_<%- codigo_asignacion_jurado %>" class="form-control" tabindex="-1">
					<option value="" selected>Elige especialidad</option>
					@foreach( App\Models\Core\Especialidad::getSpecialties(App\Models\Core\Especialidad::getType('theme')) as $value)
						<option value="{{$value->codigo_especialidad}}" <%- codigo_especialidad == '{{ $value->codigo_especialidad }}' ? 'selected': ''%> >{{ $value->nombre_especialidad }}</option>
					@endforeach
				</select>
			</td>
		<% } %>

		<td class="text-center">
			<a href="#" title="Editar" class="btn btn-default btn-xs edit-jury">
	    	   <i class="fa fa-edit"></i>
	    	</a>
		</td>

		<td class="text-center">
			<% if(codigo_estado == window._tp.getState['active']) { %>
				<a href="#" title="Elimnar" class="btn btn-default btn-xs remove-jury" data-turn="true">
		    		<i class="fa fa-eraser"></i>
		    	</a>
	    	<% } %>
		</td>
	</script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue{{ $app->environment('production') ? '.min' : '' }}.js"></script>
  <script>
    new Vue({
  el: "#list-evaluation-tpl",
  data: {
    // ... tu código actual ...
  },
  methods: {
    setEvaluation: function (faq = {}) {
      // ... tu código actual ...
    },
  },
  mounted() {

    console.log(document)
    // Prueba con un evento click directo en el botón
    document.getElementById('add-question-q').addEventListener('click', () => {
      console.log('¡Botón clickeado! Abriendo el modal...');
      $('#crearEvaluacionModal').modal('show');
    });
  },
});

    </script> --}}

@stop
