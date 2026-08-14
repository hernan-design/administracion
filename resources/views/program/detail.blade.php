@extends('program.program')

@section('program.content')
	{{-- ------------------ Program Templates JS -------------------- --}}

	{{-- Detail program view --}}
	<script type="text/template" id="program-detail-tpl">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Vista <small>convocatoria</small></h2>
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="" id="history-back"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
							<li>
								<a href="<%= window.Misc.urlFull(Route.route('convocatoria.edit', {convocatorium: codigo_convocatoria}) ) %>" title="Editar convocatoria">
									<i class="glyphicon glyphicon-pencil"></i> Editar
								</a>
	                      	</li>
	                    </ul>
		            	<div class="clearfix"></div>
		            </div>
		            <div class="x_content">
			            <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
			               	<div class="profile_img">
			                 	<div id="crop-avatar">
			                      	<!-- Current avatar -->
			                      	<img class="img-responsive avatar-view" src="<%- imagen_convocatoria %>" alt="Avatar" title="Change the avatar">
			                    </div>
			               	</div>
			            </div>
			            <div class="col-md-9 col-sm-9 col-xs-12">
			                <h3><strong><%- nombre_convocatoria %></strong></h3>
		                    <ul class="list-unstyled user_data">
								<li>
									<i class="fa fa-users"></i> Total inscritos <strong> <%- contador_inscritos %></strong>
								</li>
								<li class="m-top-xs">
									<i class="fa fa-external-link user-profile-icon"></i>
									<a class="target-link" href='<%- window.Misc.urlPlatform("convocatoria/"+url_convocatoria) %>' target="_blank"><%- nombre_convocatoria %></a>
								</li>
		                 	</ul>
		            	</div>
				        <br/>
		                <div class="col-md-12 col-sm-12 col-xs-12">
		                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
		                    	<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
			                        <li role="presentation" class="<%- dataFilter.tab == 'general' ? 'active' : '' %>">
			                        	<a href="#tab_general" role="tab" id="general-tab" data-toggle="tab" aria-expanded="false">General</a>
			                        </li>
			                        <li role="presentation" class="<%- dataFilter.tab == 'terminos' ? 'active' : '' %>">
			                        	<a href="#tab_terminos" role="tab" id="terminos-tab" data-toggle="tab" aria-expanded="false">Términos</a>
			                        </li>
			                        <li role="presentation" class="<%- dataFilter.tab == 'inscritos' ? 'active' : '' %>">
			                        	<a href="#tab_inscritos" role="tab" id="inscritos-tab" data-toggle="tab" aria-expanded="false"> Inscritos</a>
			                        </li>
		                    	</ul>
			                	<div id="myTabContent" class="tab-content">
					                <div role="tabpanel" class="tab-pane fade <%- dataFilter.tab == 'general' ? 'active in' : '' %>" id="tab_general" aria-labelledby="profile-tab">
					                    <p><%= descripcion_convocatoria %></p>
					                </div>
					                <div role="tabpanel" class="tab-pane fade <%- dataFilter.tab == 'terminos' ? 'active in' : '' %>" id="tab_terminos" aria-labelledby="profile-tab">
					                    <p><%= terminos_convocatoria %></p>
					                </div>
				                    <div role="tabpanel" class="tab-pane fade <%- dataFilter.tab == 'inscritos' ? 'active in' : '' %>" id="tab_inscritos" aria-labelledby="profile-tab">
		                      			<div id="grid-container">
											{{-- Render template id="program-participants-tpl" --}}
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

	{{-- Grid Participants view --}}
	<script type="text/template" id="program-participants-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="participant-list-program">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
					<div class="row">
		                <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Fase</label>
				            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
				         </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Notificado</label>
				            <select id="visualizacion" name="visualizacion" class="form-control" tabindex="-1">
	                           <option value="">Selecciona una opción</option>
	                           <option value="true">Si</option>
	                           <option value="false">No</option>
		                  </select>
				        </div>

					    <div class="col-md-1 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" class="form-control">
                     	</div>
		                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
				        	<label>Emprendimiento</label>
				            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
				        </div>
					    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
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

                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                  		<button type="button" class="btn btn-primary btn-search btn-search-aldea">Buscar</button>
						<button type="button" class="btn btn-default btn-clear">Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
                			<button type="button" class="btn btn-success data-export">Exportar</button>
		            	@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user participants -->
            <div class="table-responsive">
				<table id="participants-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Emprendimiento</th>
							<th>Usuario</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Momento</th>
							<th>Puntaje</th>
							<th>Notificación</th>
							<th class="text-center">Acciones</th>
							<th><input type="checkbox" class="slc"></th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>


	{{-- Grid Participants view fondo empodera --}}
	<script type="text/template" id="program-participants-empodera-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="participant-list-program">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program-empodera', 'class' =>'')) !!}
					<div class="row">
		                <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Fase</label>
				            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
				         </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Notificado</label>
				            <select id="visualizacion" name="visualizacion" class="form-control" tabindex="-1">
	                           <option value="">Selecciona una opción</option>
	                           <option value="true">Si</option>
	                           <option value="false">No</option>
		                  </select>
				        </div>

					    <div class="col-md-1 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" class="form-control">
                     	</div>
		                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
				        	<label>Emprendimiento</label>
				            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
				        </div>
					    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
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

                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                  		<button type="button" class="btn btn-primary btn-search-empodera">Buscar</button>
						<button type="button" class="btn btn-default btn-clear-empodera">Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
                			<button type="button" class="btn btn-success data-export">Exportar</button>
		            	@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user participants -->
            <div class="table-responsive">
				<table id="participants-empodera-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Emprendimiento</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Momento</th>
							<th>Puntaje</th>
							<th>Notificación</th>
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

	{{-- Grid Program User view --}}
	<script type="text/template" id="program-user-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
					<div class="row">
						<div class="col-md-4 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" min="1" class="form-control">
                     	</div>
		                <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Ciudad</label>
				            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
				        </div>
				        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
				            <label>Identificación</label>
				            <input type="text" name="identificacion" id="identificacion" class="form-control" />
				        </div>
				        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
				            <label>Fase</label>
				            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
				        </div>
                    </div>
                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                      		<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
	                		<button type="button" class="btn btn-success data-export"> Exportar</button>
		           		@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="program-user-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Identificación</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Ciudad</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>

{{-- Grid Program Courses Propace view --}}
<script type="text/template" id="program-course-propace-tpl">
	@if (session('message'))
		<div class="alert alert-info">
			{{ session('message') }}
		</div>
	@endif
	<div id="program-user-list">
		  <div class="well">
			{!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
				<div class="row">
					<div class="col-md-1 col-sm-12 col-xs-12 form-group">
						<label>Aplicación</label>
						<input type="number" name="number_register" id="number_register" min="1" class="form-control">
					 </div>
					<div class="col-md-3 col-sm-12 col-xs-12 form-group">
						<label>Email</label>
						<input type="text" id="email" name="email" class="form-control">
					</div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Ciudad</label>
						<select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
						</select>
					</div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Fase</label>
						<select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
							<option value="1">General</option>
							<% _.each(phases, function (option) { %>
								<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
							<% }); %>
						</select>
					</div>
				</div>
				<input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
				<div class="col-md-7 col-md-offset-5">
						  <button type="button" class="btn btn-primary btn-search"> Buscar</button>
						<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
					@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
						<button type="button" class="btn btn-success data-export"> Exportar</button>
					   @endif
				</div>
			{!! Form::close() !!}
		   </div>
		<br/>
		<!-- start user club -->
		<div class="table-responsive">
			<table id="program-user-search-table" class="table table-striped table-bordered" data-page-length="15">
				<thead>
					<tr>
						<th>Aplicación</th>
						<th>Usuario</th>
						<th>Correo</th>
						<th>Ciudad</th>
						<th>Registro</th>
						<th>Fase</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<!-- Render view  -->
				</tbody>
			</table>
		</div>
	  </div>
</script>

  {{-- Grid Program Mentores - Provedores view --}}
  <script type="text/template" id="program-mentores-provedores-user-tpl">
    @if (session('message'))
    <div class="alert alert-info">
      {{ session('message') }}
    </div>
    @endif
    <div id="program-user-list">
      <div class="well">
        {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
        <div class="row">
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
            <label>Aplicación</label>
            <input type="number" name="number_register" id="number_register" min="1" class="form-control">
          </div>
          <div class="col-md-3 col-sm-12 col-xs-12 form-group">
            <label>Email</label>
            <input type="text" id="email" name="email" class="form-control">
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12 form-group">
            <label>Ciudad</label>
            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
            </select>
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12 form-group">
            <label>Fase</label>
            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
              <option value="1">General</option>
              <% _.each(phases, function (option) { %>
              <option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
              <% }); %>
            </select>
          </div>
        </div>
        <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
        <div class="col-md-7 col-md-offset-5">
          <button type="button" class="btn btn-primary btn-search"> Buscar</button>
          <button type="button" class="btn btn-default btn-clear"> Limpiar</button>
          @if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
            <button type="button" class="btn btn-success data-export"> Exportar</button>
          @endif
        </div>
        {!! Form::close() !!}
      </div>
      <br/>
      <!-- start user club -->
      <div class="table-responsive">
        <table id="program-user-mentores-provedores-search-table" class="table table-striped table-bordered" data-page-length="15">
          <thead>
          <tr>
            <th>Aplicación</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Ciudad</th>
            <th>Registro</th>
            <th>Fase</th>
            <th>Acciones</th>
          </tr>
          </thead>
          <tbody>
          <!-- Render view  -->
          </tbody>
        </table>
      </div>
    </div>
  </script>

	<script type="text/template" id="program-user-specialized-service-advisors-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
					<div class="row">
						<div class="col-md-1 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" min="1" class="form-control">
                     	</div>
		                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Ciudad</label>
				            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Fase</label>
				            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
				        </div>
                    </div>
                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                      		<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
	                		<button type="button" class="btn btn-success data-export"> Exportar</button>
		           		@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="program-user-specialized-service-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Ciudad</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>

	<script type="text/template" id="program-empodera-2020-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
					<div class="row">
						<div class="col-md-1 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" class="form-control">
                     	</div>
		                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				         	<label>Emprendimiento</label>
				            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Ciudad</label>
				            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
				        </div>
						<div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Fase</label>
				            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
						</div>
						<div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Estado</label>
				            <select name="state" id="state" class="form-control"  data-placeholder="Selecciona una estado" placeholder="Selecciona una estado" tabindex="-1" required>
								<option value="0" selected> Selecciona </option>
								<option value="{{ App\Models\Core\Estado::getState('active') }}"> Activo </option>
								<option value="{{ App\Models\Core\Estado::getState('linked') }}"> Vinculado </option>
								<option value="{{ App\Models\Core\Estado::getState('no_linked') }}"> No Vinculado </option>
							</select>
				        </div>
				    </div>
                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                      		<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
	                		<button type="button" class="btn btn-success data-export"> Exportar</button>
		           		@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="participants-search-table-empodera-2020" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Emprendimiento</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Ciudad</th>
							<th>Estado</th>
							<th>Fase</th>
							<th>Registro</th>
							<th>Puntaje</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th><input type="checkbox" class="slc"></th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
			<button class="btn btn-success btn-notify-empodera-2020">Notificar</button>
      	</div>
	</script>

	<script type="text/template" id="program-aldea-20-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
				<div class="row">
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Email</label>
						<input type="text" id="email" name="email" class="form-control">
					</div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Fase</label>
						<select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
							<option value="1">General</option>
							<% _.each(phases, function (option) { %>
								<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
							<% }); %>
						</select>
					 </div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Notificado</label>
						<select id="visualizacion" name="visualizacion" class="form-control" tabindex="-1">
						   <option value="">Selecciona una opción</option>
						   <option value="true">Si</option>
						   <option value="false">No</option>
					  </select>
					</div>

					<div class="col-md-1 col-sm-12 col-xs-12 form-group">
						<label>Aplicación</label>
						<input type="number" name="number_register" id="number_register" class="form-control">
					 </div>
					<div class="col-md-3 col-sm-12 col-xs-12 form-group">
						<label>Emprendimiento</label>
						<input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
					</div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
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

				<input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
				<div class="col-md-7 col-md-offset-5">
					<button type="button" class="btn btn-primary btn-search-aldea_20">Buscar</button>
					<button type="button" class="btn btn-default btn-clear-aldea20">Limpiar</button>
					@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
						<button type="button" class="btn btn-success data-export">Exportar</button>
					@endif
				</div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="participants-search-table-aldea-20" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Emprendimiento</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Momento</th>
							<th>Puntaje</th>
							<th>Notificación</th>
							<th class="text-center">Acciones</th>
							<th><input type="checkbox" class="slc"></th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
		</div>
	</script>

	<script type="text/template" id="program-apps-co-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
				<div class="row">
						<div class="col-md-1 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" min="1" class="form-control">
                     	</div>
		                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Ciudad</label>
				            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Fase</label>
				            <select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
				        </div>
                    </div>

				<input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
				<div class="col-md-7 col-md-offset-3">
					<button type="button" class="btn btn-primary btn-search">Buscar</button>
					<button type="button" class="btn btn-default btn-clear-appsco">Limpiar</button>
					@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
						<button type="button" class="btn btn-success data-export">Exportar</button>
						<button type="button" class="btn btn-success data-export-no-complete">Pendientes</button>
					@endif
				</div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="participants-search-table-program-apps-co" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Ciudad</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Puntaje</th>
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

	{{-- Grid Program entity view --}}
	<script type="text/template" id="program-entity-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
					<div class="row">
						<div class="col-md-4 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" class="form-control">
                     	</div>
		                <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				         	<label>Aplicante</label>
				            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
				        </div>
				        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
				         	<label>Identificación</label>
				            <input type="text" id="identificacion" name="identificacion" class="form-control">
				        </div>
						<div class="col-md-6 col-sm-12 col-xs-12 form-group">
							<label>Fase</label>
							<select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
						 </div>
				    </div>
                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                      		<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
	                		<button type="button" class="btn btn-success data-export"> Exportar</button>
		           		@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="participants-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Aplicante</th>
							<th>Usuario</th>
							<th>Correo</th>
							<th>Identificación</th>
							<th>Ciudad</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Estado</th>
							<th>Puntaje</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>Acciones</th>
							<th><input type="checkbox" class="slc"></th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
			<button class="btn btn-success btn-notify">Notificar</button>
      	</div>
	</script>

	{{-- Grid Program terpel 20 view --}}
	<script type="text/template" id="program-terpel-20-tpl">
		@if (session('message'))
		    <div class="alert alert-info">
		        {{ session('message') }}
		    </div>
		@endif
		<div id="program-user-list">
		  	<div class="well">
	            {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
					<div class="row">
						<div class="col-md-1 col-sm-12 col-xs-12 form-group">
					        <label>Aplicación</label>
                            <input type="number" name="number_register" id="number_register" class="form-control">
                     	</div>
		                <div class="col-md-3 col-sm-12 col-xs-12 form-group">
				        	<label>Email</label>
				            <input type="text" id="email" name="email" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				         	<label>Emprendimiento</label>
				            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
				        </div>
				        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
				            <label>Ciudad</label>
				            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
							</select>
						</div>
						<div class="col-md-4 col-sm-12 col-xs-12 form-group">
							<label>Fase</label>
							<select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
								<option value="1">General</option>
								<% _.each(phases, function (option) { %>
									<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
								<% }); %>
							</select>
						</div>
				    </div>
                    <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
                    <div class="col-md-7 col-md-offset-5">
                      		<button type="button" class="btn btn-primary btn-search"> Buscar</button>
							<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
						@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
	                		<button type="button" class="btn btn-success data-export"> Exportar</button>
		           		@endif
		            </div>
			    {!! Form::close() !!}
		   	</div>
			<br/>
            <!-- start user club -->
            <div class="table-responsive">
				<table id="participants-search-table" class="table table-striped table-bordered" data-page-length="15">
					<thead>
						<tr>
							<th>Aplicación</th>
							<th>Emprendimiento</th>
							<th>Usuario</th>
							<th>Ciudad</th>
							<th>Registro</th>
							<th>Fase</th>
							<th>Puntaje</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- Render view  -->
					</tbody>
				</table>
			</div>
      	</div>
	</script>

  {{-- Grid Program entidades provedores view --}}
  <script type="text/template" id="program-entity-provedores-tpl">
    @if (session('message'))
    <div class="alert alert-info">
      {{ session('message') }}
    </div>
    @endif
    <div id="program-user-list">
      <div class="well">
        {!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
        <div class="row">
          <div class="col-md-1 col-sm-12 col-xs-12 form-group">
            <label>Aplicación</label>
            <input type="number" name="number_register" id="number_register" class="form-control">
          </div>
          <div class="col-md-3 col-sm-12 col-xs-12 form-group">
            <label>Email</label>
            <input type="text" id="email" name="email" class="form-control">
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12 form-group">
            <label>Emprendimiento</label>
            <input type="text" id="emprendimiento" name="emprendimiento" class="form-control">
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12 form-group">
            <label>Ciudad</label>
            <select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
            </select>
          </div>
        </div>
        <input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
        <div class="col-md-7 col-md-offset-5">
          <button type="button" class="btn btn-primary btn-search"> Buscar</button>
          <button type="button" class="btn btn-default btn-clear"> Limpiar</button>
          @if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
            <button type="button" class="btn btn-success data-export"> Exportar</button>
          @endif
        </div>
        {!! Form::close() !!}
      </div>
      <br/>
      <!-- start user club -->
      <div class="table-responsive">
        <table id="participants-search-entitdades-provedores-table" class="table table-striped table-bordered" data-page-length="15">
          <thead>
          <tr>
            <th>Aplicación</th>
            <th>Emprendimiento</th>
            <th>Usuario</th>
            <th>Ciudad</th>
            <th>Registro</th>
            <th>Acciones</th>
            <th>Formulario</th>
          </tr>
          </thead>
          <tbody>
          <!-- Render view  -->
          </tbody>
        </table>
      </div>
    </div>
  </script>

	{{-- Grid Program register view --}}
	<script type="text/template" id="program-register-tpl">
		@if (session('message'))
		<div class="alert alert-info">
			{{ session('message') }}
		</div>
	@endif
	<div id="program-user-list">
		  <div class="well">
			{!! Form::open(array('route' => null,'id' => 'form-filter-program', 'class' =>'')) !!}
				<div class="row">
					<div class="col-md-1 col-sm-12 col-xs-12 form-group">
						<label>Aplicación</label>
						<input type="number" name="number_register" id="number_register" min="1" class="form-control">
					 </div>
					<div class="col-md-3 col-sm-12 col-xs-12 form-group">
						<label>Email</label>
						<input type="text" id="email" name="email" class="form-control">
					</div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Ciudad</label>
						<select name="city" id="city" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
						</select>
					</div>
					<div class="col-md-4 col-sm-12 col-xs-12 form-group">
						<label>Fase</label>
						<select name="phase" id="phase" class="form-control"  data-placeholder="Selecciona una fase" placeholder="Selecciona una fase" tabindex="-1" required>
							<option value="1">General</option>
							<% _.each(phases, function (option) { %>
								<option value="<%- option.codigo_fase %>"><%- option.nombre_fase %></option>
							<% }); %>
						</select>
					</div>
				</div>
				<input type="hidden" name="program" id="program" value="<%-codigo_convocatoria %>">
				<div class="col-md-7 col-md-offset-5">
					<button type="button" class="btn btn-primary btn-search"> Buscar</button>
					<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
					@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/convocatoria']) )
						<button type="button" class="btn btn-success data-export"> Exportar</button>
					@endif
				</div>
			{!! Form::close() !!}
		   </div>
		<br/>
		<!-- start user club -->
		<div class="table-responsive">
			<table id="program-register-search-table" class="table table-striped table-bordered" data-page-length="15">
				<thead>
					<tr>
						<th>Aplicación</th>
						<th>Usuario</th>
						<th>Correo</th>
						<th>Ciudad</th>
						<th>Registro</th>
						<th>Fase</th>
						<th class="text-center">Notificación</th>
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

	{{-- Detail documenter view --}}
	<script type="text/template" id="program-documenter-tpl">
      	<div class="row">
         	<div class="col-md-12 col-sm-12 col-xs-12">
            	<div class="x_panel">
	            <div class="x_title">
                	<h2>Vista <small>Documentador</small></h2>
                		<ul class="nav navbar-right panel_toolbox">
	                      	<li>
	                      	<a href="<%= window.Misc.urlFull(Route.route('convocatoria.show', {'convocatorium': codigo_convocatoria, 'tab': 'inscritos'}))%>"><i class="fa fa-chevron-left"></i> Atrás</a>
	                      	</li>
	                    </ul>
                	<div class="clearfix"></div>
                </div>
	            <div class="x_content">
	            	<div class="form-group">
	                	<div class="col-md-12 col-sm-12 col-xs-12">
	                		<h3 class="text-center"><%- nombre_usuario %></h3>
	                	</div>
	                </div>
	            	{!! Form::open(array('route' => null,'id' => 'form-documenter', 'class' =>'form-horizontal form-label-left dropzone')) !!}
	            		<div class="form-group">
	            			<label class="control-label col-md-1" for=""></label>
		                    <div class="col-md-10 col-sm-12 col-xs-12" >
				  			  	<div class="table-responsive">
				                    <table class="table table-striped jambo_table bulk_action" ><!-- data table table-striped no-margin-->
			                            <thead>
											<tr>
												<th colspan="100%" class="text-center">Totalidad componentes evaluados</th>
											</tr>
											<tr>
												<th class="text-left"><h4><%- Math.round(promedio_total) %>%</h4></th>
												<th class="text-center">TOTAL</th>
												<% _.each(experts, function(expert){%>
													<th class="text-center"><%- expert.nombre_usuario %></th>
												<% }) %>
											</tr>
			                           	</thead>
			                           	<tbody>
											<% _.each(grupos, function(group){ %>
												<thead>
													<tr>
														<th class="text-left"><%- group.nombre_grupo_item_eval %></th>
														<th class="text-center"><%- group.promedio_total_grupo %></th>
														<!-- Totales -->
														<% _.each(group.totales, function(total){ %>
															<th class="text-right"><%- total.valor_experto %></th>
														<% }) %>
													</tr>
												</thead>
												<% _.each(group.items, function(item){ %>
													<tr>
														<td class="text-left" colspan="2">
															<%- item.nombre_grup_opc_eval_pregunta %>
														</td>
														<% _.each(item.answers, function(answer){%>
															<td class="text-right"><%- answer.valor_experto %></td>
														<% }) %>
													</tr>
												<% }) %>
											<% }) %>
										</tbody>
				                    </table>
	                  			</div>
							</div>
		                </div>

		                <div class="form-group">
		                	<label class="control-label col-md-1" for="">
		                    </label>
		                    <div class="col-md-10 col-sm-12 col-xs-12" >
			                    <div id="result-list-evaluation">
			                    	<!-- start Categories Product -->
				                  	<div class="table-responsive">
					                    <table class="table table-striped jambo_table bulk_action" ><!-- data table table-striped no-margin-->
				                            <thead>
												<tr>
													<th colspan="2" class="text-center">Recomendaciones sugeridas por los evaluadores</th>
												</tr>
												<tr>
													<th>Evaluador</th>
													<th>Comentario</th>
												</tr>
				                            </thead>
			                               @include('public.noresults', ['message' => 'No hay Evaluaciones'])
											<tbody >
												<!-- Render view  -->
											</tbody>
					                    </table>
			                            <!-- end Result Evaluations -->
		                  			</div>
		                  		</div>
		                  	</div>
						</div>

						<% if(codigo_convocatoria == window._tp.getProgram['aldea__20']) { %>
							<div class="form-group">
								<label class="control-label col-md-1" for="">
								</label>
								<div class="col-md-10 col-sm-12 col-xs-12" >
									<div id="result_list_program__504">
										<!-- start Categories Product -->
										<div class="table-responsive">
											<table class="table table-striped jambo_table bulk_action" ><!-- data table table-striped no-margin-->
												<thead>
													<tr>
														<th colspan="3" class="text-center">Resultados obtenidos</th>
													</tr>
													<tr>
														<th>Pregunta</th>
														<th>Resultado</th>
														<th>Puntaje</th>
													</tr>
												</thead>
											@include('public.noresults', ['message' => 'No hay Resultados'])
												<tbody >
													<!-- Render view  -->
												</tbody>
											</table>
											<!-- end Result Evaluations -->
										</div>
									</div>
								</div>
							</div>
						<% } %>

	                  	<div class="form-group">
	            			<label class="control-label col-md-1" for=""></label>
		                    <div class="col-md-10 col-sm-12 col-xs-12" >
					            <div class="table-responsive">
				                    <table class="table table-striped jambo_table bulk_action" ><!-- data table table-striped no-margin-->
			                            <thead>
											<tr>
												<th colspan="100%" class="text-center"> Comentarios </th>
											</tr>
			                           	</thead>
		                            	<tbody>
										<% _.each(grupos, function(group){ %>
											<tr>
												<td class="text-left col-md-6">
													<%- group.nombre_grupo_item_eval %>
												</td>
												<td class="text-left col-md-6">
													<textarea class="resizable_textarea form-control" placeholder="Escriba su comentario" id="texto_comentario_documentador_<%-group.codigo_grupo_item_eval %>" name="texto_comentario_documentador_<%- group.codigo_grupo_item_eval %>" required> <%- group.texto_comentario_documentador %></textarea>
												</td>
											</tr>
										<% }) %>
										<tr>
											<td class="text-left">Comentario general</td>
											<td class="text-left">
												<textarea class="resizable_textarea form-control" placeholder="Escriba su comentario" id="observaciones_registro_fase_usuario" name="observaciones_registro_fase_usuario"><%-observaciones_registro_fase_usuario%></textarea>
											</td>
										</tr>
										<% if(comentario_cerrado){ %>
											<tr>
												<td class="text-left">Principal</td>
												<td class="text-left">
													<select class="form-control" name="comentario_general_aldea_20" id="comentario-general-aldea-20">
														<option  value="0"> Selecciona... </option>
														<% if(typeof comentarios_evaluacion == 'undefined') { %>
															<option  value="0"> No se encontraron comentarios </option>
														<% } else { %>
															<% _.each(comentarios_evaluacion, function(comentario){ %>
																<option value="<%- comentario.codigo_coment_eval_emprendi %>" <%- typeof comentario_general != 'undefined' ? comentario_general == comentario.codigo_coment_eval_emprendi ? 'selected' : '' : '' %>> <%- comentario.name_coment_eval_emprendi %> </option>
															<% }) %>
														<% } %>
													</select>
												</td>
											</tr>
											<tr>
												<td class="text-left">Secundario</td>
												<td class="text-left">
													<select class="form-control" name="comentario_secundario_aldea_20" id="comentario-secundario-aldea-20">
														<option  value="0"> Selecciona... </option>
														<% if(typeof comentarios_evaluacion == 'undefined') { %>
															<option  value="0"> No se encontraron comentarios </option>
														<% } else { %>
															<% _.each(comentarios_evaluacion, function(comentario){ %>
																<option value="<%- comentario.codigo_coment_eval_emprendi %>" <%- typeof comentario_secundario != 'undefined' ? comentario_secundario == comentario.codigo_coment_eval_emprendi ? 'selected' : '' : '' %>> <%- comentario.name_coment_eval_emprendi %> </option>
															<% }) %>
														<% } %>
													</select>
												</td>
											</tr>
										<% } %>
										</tbody>
				                    </table>
	                  			</div>
	                  		</div>
		                </div>

		                <div class="form-group">
		                	<div class="col-md-12 col-sm-12 col-xs-12">
		                		<h3 class="text-center">Tipo llave de acceso</h3>
		                		<h3 class="text-center"><%- window.copCurrencyF.to(llaves_acceso) %></h3>
		                	</div>
		                </div>

						<div class="form-group">
							<div class="col-md-10 col-sm-12 col-xs-12 col-md-offset-1">
								<div id="attach-file-program-documenter" class="uploader-attachments"></div>
							</div>
						</div>

		                <% if(llaves_acceso > 0) { %>
			                <div class="form-group">
			                	<label class="control-label col-md-1" for=""></label>
			                    <div class="col-md-10 col-sm-12 col-xs-12" >
				                    <div id="categories-list-product">
				                    	<!-- start Categories Product -->
					                  	<div class="table-responsive">
						                    <table class="table table-striped jambo_table bulk_action" ><!-- data table table-striped no-margin-->
					                            <thead>
													<tr>
														<th colspan="2" class="text-center">Categorias de servicios especializados</th>
													</tr>
													<tr>
														<th class="text-center">Seleccionar</th>
														<th>Categoria</th>
													</tr>
					                            </thead>
				                                @include('public.noresults', ['message' => 'No hay Categorias'])
												<tbody >
													<!-- Render view  -->
												</tbody>
						                    </table>
				                            <!-- end Categories Product -->
			                  			</div>
			                  		</div>
			                  	</div>
		                  	</div>
	                  	<% } %>

	                  	<div class="form-group">
							<div class="col-md-2 col-md-offset-4">
		                      	<button type="submit" class="btn btn-block btn-success data-filter">Guardar</button>
							</div>
							<div class="col-md-2">
								<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.documentador.exportar', {'register': codigo_registro_fase_usuario })) %>" class="btn btn-block btn-primary" target="_blank"><i class="fa fa-download"></i> Generar PDF</a>
							</div>
						</div>

		            {!! Form::close() !!}
	            </div>
	        </div>
	    </div>
	</script>

	{{-- category product list --}}
	<script type="text/template" id="category-product-item-tpl">
		<td>
			<div class="checkbox text-center">
            	<label>
              		<input type="checkbox" class="valid-checkbox" name="codigo_categoria_producto_<%-codigo_categoria_producto%>" id="codigo_categoria_producto_<%-codigo_categoria_producto%>" value='<%-codigo_categoria_producto%>' <%- selected ? 'checked' : '' %> >
            	</label>
          	</div>
		</td>
		<td><%- nombre_categoria_producto %></td>
	</script>

	{{-- result evaluation list --}}
	<script type="text/template" id="result-evaluation-item-tpl">
		<td><%- nombre_usuario %></td>
		<td><%- comentario_resultado_evaluacion %></td>
	</script>

	<script type="text/template" id="result-evaluation-program-item-tpl">
		<td><%- texto_pregunta_encuesta.replace("<br>", "") %></td>
		<td><%- respuesta != null ? respuesta : 'No se encontro respuesta' %></td>
		<td><%- puntaje %></td>
	</script>

	<script type="text/template" id="turn-confirm-tpl">
		<p>¿Estas seguro de activar la visualización?</p>
	</script>

	<script type="text/template" id="turn-error-tpl">
		<p>Debes seleccionar minimo un usuario</p>
	</script>

	<script type="text/template" id="commitment-confirm-tpl">
		<p>¿Estas seguro de cerrar esta carta de compromiso?</p>
	</script>

	<script type="text/template" id="program-comitment-change-confirm-tpl">
		<% typeof(text) !== "undefined";   %>
		<p id="content-body"><%- text %></p>
	</script>

	<script type="text/template" id="forward_email_four_challenge-confirm-tpl">
		<p>¿Estas seguro de reenviar correo de notificación reto 4?</p>
	</script>

	<script type="text/template" id="cancel-confirm-tpl">
		<p>¿ Estas seguro de anular el emprendimiento <b> <%- program %> </b> ?</p>
	</script>

	{{-- Detail commitment view --}}
	<script type="text/template" id="program-commitment-tpl">
      	<div class="row">
         	<div class="col-md-12 col-sm-12 col-xs-12">
            	<div class="x_panel">
	            <div class="x_title">
                	<h2>Vista <small>Carta Compromiso</small></h2>
                		<ul class="nav navbar-right panel_toolbox">
	                      	<li>
	                      	<a href="<%= window.Misc.urlFull(Route.route('convocatoria.show', {'convocatorium': codigo_convocatoria, 'tab': 'inscritos'}))%>"><i class="fa fa-chevron-left"></i> Atrás</a>
	                      	</li>
	                    </ul>
                	<div class="clearfix"></div>
                </div>
	            <div class="x_content">
	            	<form id="form-program-commitment" data-parsley-validate class="form-horizontal form-label-left">
						<% if( codigo_estado != window._tp.getState['closed']) {  %>
						<div class="row">
							<div class="col-md-6 col-md-offset-3 well">
								<div class="row">
									<div class="col-md-8 col-md-offset-2 col-sm-4 col-xs-12">
										<div class="item form-group">
											<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre_empresa">Tipo </label>
											<div class="col-md-9 col-sm-6 col-xs-12">
												<select name="codigo_clase_empre_insti" id="codigo_clase_empre_insti" class="form-control col-md-10 col-xs-12" required>
													@foreach( App\Models\Core\ClaseEmprendimiento::getClasses() as $value)
														<option value="{{ $value->codigo_clase_empre_insti }}" <%- codigo_clase_empre_insti == {{ $value->codigo_clase_empre_insti }} ? 'selected' : '' %>>{{ $value->nombre_clase_emprendimiento }}</option>
													@endforeach
												</select>
											</div>
										</div>
								  	</div>
								</div>
							</div>
						</div>
						<% } %>
						<div class="item form-group">
							<label class="control-label col-md-5 col-sm-3 col-xs-12" for="nombre_empresa"><%- codigo_clase_emprendimiento == window._tp.getClassEntrepreneurship['company'] ? 'Empresa' : 'Proyecto' %> </label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" id="nombre_empresa" name="nombre_empresa" required="required" class="form-control  col-md-7 col-xs-12" value="<%- nombre_usuario %>">
							</div>
						</div>
						<% if (codigo_clase_emprendimiento == window._tp.getClassEntrepreneurship['company']) { %>
							<div class="item form-group">
								<label class="control-label col-md-5 col-sm-3 col-xs-12" for="nit_empresa">Nit empresa</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" id="nit_empresa" name="nit_empresa" required="required" class="form-control  col-md-7 col-xs-12" value="<%- identificacion_usuario %>">
								</div>
							</div>
						<% } %>
						<div class="item form-group">
							<label class="control-label col-md-5 col-sm-3 col-xs-12" for="nombre_representante_legal">Nombre <%- codigo_clase_emprendimiento == window._tp.getClassEntrepreneurship['company'] ? 'representante legal' : 'emprededor' %> <span class="required">*</span>
							</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" id="nombre_representante_legal" name="nombre_representante_legal" required="required" class="form-control  col-md-7 col-xs-12" value="<%- nombre_representante_legal %>">
							</div>
						</div>
						<div class="item form-group">
							<label class="control-label col-md-5 col-sm-3 col-xs-12" for="apellidos_representante_legal">Apellidos <%- codigo_clase_emprendimiento == window._tp.getClassEntrepreneurship['company'] ? 'representante legal' : 'emprededor' %> <span class="required">*</span>
							</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" id="apellidos_representante_legal" name="apellidos_representante_legal" required="required" class="form-control  col-md-7 col-xs-12" value="<%- apellidos_representante_legal %>">
							</div>
						</div>
						<div class="item form-group">
							<label class="control-label col-md-5 col-sm-3 col-xs-12" for="identificacion_rep_legal">Identificación <%- codigo_clase_emprendimiento == window._tp.getClassEntrepreneurship['company'] ? 'representante legal' : 'emprededor' %> <span class="required">*</span>
							</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" id="identificacion_rep_legal" name="identificacion_rep_legal" required="required" class="form-control  col-md-7 col-xs-12" value="<%- identificacion_rep_legal %>">
							</div>
						</div>
						<div class="item form-group">
							<label class="control-label col-md-5 col-sm-3 col-xs-12" for="ciudad_expedicion_id_rep_legal">Ciudad Expedición <span class="required">*</span>
							</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<select name="ciudad_expedicion_id_rep_legal" id="ciudad_expedicion_id_rep_legal" class="form-control  col-md-7 col-xs-12 choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad" data-initial-value="<%- ciudad_expedicion_id_rep_legal %>">
								</select>
							</div>
						</div>
						<div class="item form-group">
							<label class="control-label col-md-5 col-sm-3 col-xs-12" for="ciudad_empresa">Ciudad <%- codigo_clase_emprendimiento == window._tp.getClassEntrepreneurship['company'] ? 'empresa' : 'emprendimiento' %> <span class="required">*</span>
							</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<select name="ciudad_empresa" id="ciudad_empresa" class="form-control  col-md-7 col-xs-12 choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad" data-initial-value="<%- ciudad_empresa %>">
								</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-striped jambo_table bulk_action">
										<thead>
											<tr class="headings">
												<th></th>
												<th class="column-title">Tipo</th>
												<th class="column-title">Documento</th>
												<th class="column-title">Fecha</th>
											</tr>
										</thead>
										<tbody>
											<% _.each(documents, function (file) { %>
												<tr>
													<td class="a-center">
														<input <%- file.check ? 'checked="checked"' : '' %> type="checkbox" id="document_<%- file.codigo_documento %>" name="document_<%- file.codigo_documento %>">
													</td>
													<td><%- file.nombre_documento %></label></td>
													<td><a href="<%- file.direccion_adjunto %>" title="Descargar"><%- file.nombre_adjunto %></a></td>
													<td><%- file.fecha_adjunto %></td>
												</tr>
											<% }); %>
										<tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="ln_solid"></div>
							<div class="form-group">
								<div class="col-md-2 col-md-offset-<%- codigo_estado == null ? window._tp.getState['correction'] : window._tp.getState['cancelled'] %> ">
									<% if( codigo_estado != window._tp.getState['closed'] || codigo_estado == null) {  %>
				                    	<button type="submit" class="btn btn-success btn btn-block">Guardar</button>
									<% } %>
								</div>
								<% if( codigo_estado == window._tp.getState['closed']) {  %>
									<div class="col-md-6 col-md-offset-3 text-center">
										<h3>Carta de compromiso cerrada.</h3>
										<a href="<%- window.Misc.urlPlatform('/step/compromiso/' + codigo_carta_compormiso) %>" target="_blank" class="btn btn-primary">Ver carta</a>
									</div>
								<% }else if(codigo_estado != null){ %>
									<div class="col-md-2">
										<button type="button" class="btn btn-primary btn-block closed-commitment">Cerrar carta</button>
									</div>
									<div class="col-md-2">
										<a href="<%- window.Misc.urlPlatform('/step/compromiso/' + codigo_carta_compormiso) %>" target="_blank" class="btn btn-primary btn-block">Ver carta</a>
									</div>
								<% } %>
							</div>
						</div>
                    </form>
	            </div>
	        </div>
	    </div>
	</script>

	<script type="text/template" id="buttons-action-aldea20-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.codigo_usuario_encuesta != 'undefined' && full.codigo_usuario_encuesta ) { %>
					<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
					</li>
				<% } %>

				<% if( full.codigo_fase == window._tp.getPhases['aldea__20__challenge_1'] || full.codigo_fase == window._tp.getPhases['aldea__20__challenge_1_2'] || full.codigo_fase == window._tp.getPhases['aldea__20__challenge_1_3'] ) { %>
					<li><a href="<%- window.Misc.urlPlatform('encuesta/revision/'+full.url_encuesta+'/'+full.codigo_usuario_encuesta) %>" title="Ver aplicación" target="_blank"><i class="fa fa-file-text"></i> Aplicación</a>
					</li>
				<% } %>

				<% if( typeof full.nickname_usuario != 'undefined'  && full.nickname_usuario && full.codigo_fase == window._tp.getPhases['aldea__20__challenge_2'] || full.codigo_fase == window._tp.getPhases['aldea__20__challenge_1_3'] ) { %>
					<li><a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['aldea__20__challenge_1_3']){ %>
					<li><a class="review__response__evaluation" data-user="<%- full.codigo_usuario_encuesta %>" data-register="<%- full.codigo_registro_fase_usuario %>" title="Ver respuestas" target="_blank"><i class="fa fa-check-square"></i> Repuestas</a></li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['aldea__20__challenge_2'] && !_.isEmpty(full.puntaje) && full.puntaje > 0) { %>
					<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.pdf', { 'register': full.codigo_registro_fase_usuario })) %>" title="PDF Respuesta" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF Repuesta</a></li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['aldea__20__challenge_2'] ) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.documentador.show', {'documentador': full.registro_fase_documentador})) %>" title="Documentador"><i class="fa fa-pencil-square-o"></i> Documentador</a>
						<% if(full.puntaje != 0){ %>
							<a href="<%- window.Misc.urlFull(Route.route('convocatoria.carta.llave.show', {'llave': full.codigo_registro_fase_usuario})) %>" title="Carta Llaves"><i class="fa fa-key"></i> Carta Llaves </a>
						<% } %>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['aldea__20__challenge_3'] ) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.documentador.show', {'documentador': full.registro_fase_documentador})) %>" title="Documentador"><i class="fa fa-pencil-square-o"></i> Documentador</a>
						<a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.codigo_registro_fase_usuario })) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.carta.llave.show', {'llave': full.codigo_registro_fase_usuario})) %>" title="Carta Llaves"><i class="fa fa-key"></i> Carta Llaves </a>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['three_challenge'] || full.codigo_fase == window._tp.getPhases['four_challenge']) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.registro_fase_reto_3})) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['empodera2020_two'] && typeof full.codigo_registro_fase_usuario !== 'undefined' && full.codigo_registro_fase_usuario) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.codigo_registro_fase_usuario})) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
					</li>
				<% } %>

				@if( Auth::user()->ability('admin,super-admin', 'notification', ['module'  => '/convocatoria']) )
					<% if(full.codigo_fase == window._tp.getPhases['four_challenge']) { %>
						<li>
							<a href="#" title="Carta Compromiso" class="forward_email_four_challenge" data-register="<%- full.codigo_registro_fase_usuario %>" ><i class="fa fa-share"></i> Reenviar email </a>
						</li>
					<% } %>
				@endif
			</ul>
		</div>
	</script>

	<script type="text/template" id="buttons-action-program-apps-co-tpl">
		<div class="btn-group tp_btn-group">
			<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.codigo_usuario_encuesta != 'undefined' && full.codigo_usuario_encuesta ) { %>
					<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
					</li>
				<% } %>
			</ul>
		</div>
		</div>
	</script>

	<script type="text/template" id="buttons-action-empodera20-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.codigo_usuario_encuesta != 'undefined' && full.codigo_usuario_encuesta ) { %>
					<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
					</li>
				<% } %>

				<% if( typeof full.url_encuesta != 'undefined' && typeof full.codigo_usuario_encuesta != 'undefined' && full.url_encuesta && full.codigo_usuario_encuesta ) { %>
					<li><a href="<%- window.Misc.urlPlatform('encuesta/revision/'+full.url_encuesta+'/'+full.codigo_usuario_encuesta) %>" title="Ver aplicación" target="_blank"><i class="fa fa-file-text"></i> Aplicación</a>
					</li>
				<% } %>

				<% if( typeof full.nickname_usuario != 'undefined'  && full.nickname_usuario ) { %>
					<li><a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['empodera2020_intermedate']) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.codigo_registro_fase_usuario})) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
					</li>
					<li>
						<a href="https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/documents/innpulsa/iNNpulsaEmpodera__2020_<%- full.codigo_registro_convocatoria %>.pdf" title="Carta Compromiso"><i class="fa fa-paper-plane"></i> Notificacion </a>
					</li>
					<li>
						<a href="<%- window.Misc.urlPlatform('proyecto/' + full.nickname_usuario) %>" title="Ver emprendimiento" ><i class="fa fa-file-text"></i> Emprendimiento </a>
					</li>
				<% } %>
				<% console.log(full) %>
				<% if(full.codigo_fase == window._tp.getPhases['empodera2020_two'] && typeof full.codigo_registro_fase_usuario !== 'undefined' && full.codigo_registro_fase_usuario) { %>
					<li>

						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.codigo_registro_fase_usuario})) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
					</li>
				<% } %>

				@if( Auth::user()->ability('admin,super-admin', 'notification', ['module'  => '/convocatoria']) )
					<% if(full.codigo_fase == window._tp.getPhases['four_challenge']) { %>
						<li>
							<a href="#" title="Carta Compromiso" class="forward_email_four_challenge" data-register="<%- full.codigo_registro_fase_usuario %>" ><i class="fa fa-share"></i> Reenviar email </a>
						</li>
					<% } %>
				@endif
			</ul>
		</div>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.codigo_usuario_encuesta != 'undefined' && full.codigo_usuario_encuesta ) { %>
					<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
					</li>
				<% } %>

				<% if( typeof full.url_encuesta != 'undefined' && typeof full.codigo_usuario_encuesta != 'undefined' && full.url_encuesta && full.codigo_usuario_encuesta ) { %>
					<li><a href="<%- window.Misc.urlPlatform('encuesta/revision/'+full.url_encuesta+'/'+full.codigo_usuario_encuesta) %>" title="Ver aplicación" target="_blank"><i class="fa fa-file-text"></i> Aplicación</a>
					</li>
				<% } %>

				<% if( typeof full.nickname_usuario != 'undefined'  && full.nickname_usuario ) { %>
					<li><a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['two_challenge'] || full.codigo_fase == window._tp.getPhases['three_challenge'] || full.codigo_fase == window._tp.getPhases['four_challenge'] ) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.documentador.show', {'documentador': full.registro_fase_documentador})) %>" title="Documentador"><i class="fa fa-pencil-square-o"></i> Documentador</a>
					</li>
				<% } %>

				<% if(full.codigo_fase == window._tp.getPhases['three_challenge'] || full.codigo_fase == window._tp.getPhases['four_challenge']) { %>
					<li>
						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.registro_fase_reto_3})) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
					</li>
				<% } %>
				<% console.log(full) %>
				<% if(full.codigo_fase == window._tp.getPhases['empodera2020_two'] && typeof full.codigo_registro_fase_usuario !== 'undefined' && full.codigo_registro_fase_usuario) { %>
					<li>

						<a href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.compromiso.show', {'compromiso': full.codigo_registro_fase_usuario})) %>" title="Carta Compromiso"><i class="fa fa-envelope"></i> Carta compromiso</a>
					</li>
				<% } %>

				@if( Auth::user()->ability('admin,super-admin', 'notification', ['module'  => '/convocatoria']) )
					<% if(full.codigo_fase == window._tp.getPhases['four_challenge']) { %>
						<li>
							<a href="#" title="Carta Compromiso" class="forward_email_four_challenge" data-register="<%- full.codigo_registro_fase_usuario %>" ><i class="fa fa-share"></i> Reenviar email </a>
						</li>
					<% } %>
				@endif
			</ul>
		</div>
	</script>

	<script type="text/template" id="buttons-action-default-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.nickname_usuario != 'undefined'  && full.nickname_usuario ) { %>
					<li><a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
					</li>
				<% } %>
				<li><a href="!#" class="cancel" title="Anular" data-program="<%- full.nombre_entidad %>" data-register="<%- full.codigo_registro_fase_usuario %>" target="_blank"><i class="fa fa-ban"></i> Anular </a>
				</li>
			</ul>
		</div>
	</script>

	<script type="text/template" id="buttons-action-rutan-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.nickname_usuario != 'undefined'  && full.nickname_usuario ) { %>
					<li><a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
					</li>
				<% } %>
				<li><a href="!#" class="cancel" title="Anular" data-program="<%- full.nombre_entidad %>" data-register="<%- full.codigo_registro_fase_usuario %>" target="_blank"><i class="fa fa-ban"></i> Anular </a>
				</li>
				<% if(
						typeof full.nickname_usuario != 'undefined'
						&& (full.codigo_fase == 352 || full.codigo_fase == 353 || full.codigo_fase == 354  || full.codigo_fase == 350 || full.codigo_fase == 351)
					) { %>
					<li>
						<a
							href="<%- window.Misc.urlFull(Route.route('convocatoria.fase.voucher.show', {'codigo_registro_fase_usuario': full.codigo_registro_fase_usuario})) %>"
							target="_blank"
							title="Voucher"
							>
							<i class="fa fa-pencil-square-o"></i>
							Voucher
						</a>
					</li>
					<li>
						<a
							href="<%- window.Misc.urlFull('convocatoria/'+full.codigo_convocatoria+'/asignacionmentor/'+full.nickname_usuario) %>"
							id="asignar-mentor"
							title="Asigmar mentor"
							target="_blank">
							<i class="fa fa-address-card-o"></i> Asignar mentor
						</a>
					</li>
				<% } %>
				<li>
					<a
						href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>"
						title="Ver pdf"
						target="_blank">
						<i class="fa fa-file-pdf-o"></i>
						PDF
					</a>
				</li>
			</ul>
		</div>
	</script>

	<script type="text/template" id="buttons-action-siinova-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if( typeof full.nickname_usuario != 'undefined'  && full.nickname_usuario ) { %>
					<li><a href="<%- window.Misc.urlPlatform('proyecto/'+full.nickname_usuario+'/convocatorias') %>" title="Ver resultado" target="_blank"><i class="fa fa-tachometer"></i> Resultado</a>
					</li>
				<% } %>
				<li><a href="!#" class="cancel" title="Anular" data-program="<%- full.nombre_entidad %>" data-register="<%- full.codigo_registro_fase_usuario %>" target="_blank"><i class="fa fa-ban"></i> Anular </a>
				</li>
				<li>
					<a
						href="<%- window.Misc.urlFull(Route.route('formulario.diagnostico.radar', {'usersurvey': full.codigo_usuario_encuesta})) %>"
						title="Ver Diagnóstico"
						target="_blank">
						<i class="fa fa-area-chart"></i> Diagnóstico
					</a>
				</li>
				<li>
					<a
						href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>"
						title="Ver PDF"
						target="_blank">
						<i class="fa fa-file-pdf-o"></i>
						PDF
					</a>
				</li>
			</ul>
		</div>
	</script>

	<script type="text/template" id="buttons-action-entidad-provedor-tpl">
	  <a href="<%- window.Misc.urlPlatform('encuesta/revision/'+full.url_encuesta+'/'+full.codigo_usuario_encuesta) %>" title="Ver aplicación" class="btn btn-default btn-xs" ><i class="fa fa-file-text"></i></a>
	</script>

  <script type="text/template" id="buttons-action-specialized-service-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportSpecializedService', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
				</li>
			</ul>
		</div>
	</script>

	{{-- buttons acction dinamiza --}}
	<script type="text/template" id="buttons-action-dinamiza-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li><a href="<%- window.Misc.urlFull(Route.route('formulario.respuestas.exportar', {'usersurvey': full.codigo_usuario_encuesta})) %>" title="Ver pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
				</li>
				<li><a href="<%- window.Misc.urlPlatform('encuesta/revision/'+full.url_encuesta+'/'+full.codigo_usuario_encuesta) %>" title="Ver aplicación" target="_blank"><i class="fa fa-file-text"></i> Aplicación</a>
				</li>
			</ul>
		</div>
	</script>
@stop
