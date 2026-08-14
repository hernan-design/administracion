@extends('marketplace.marketplace')

@section('marketplace.content')
	{{-- ------------------ Evaluation Templates JS -------------------- --}}
	{{-- template marketplace aside filter --}}
	<script type="text/template" id="marketplace-list-tpl">
   		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Productos</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                	<div id="content-marketplace">
						  	<div class="well">
				                {!! Form::open(array('route' => null, 'id' => 'form-product-filter', 'class' =>'')) !!}
					                <div class="row">
						                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
								        	<label>Producto</label>
								            <input type="text" id="name_product" name="name_product" class="form-control">
								        </div>
										@if( session('institution')->codigo_institucion != 30 )
											<div class="col-md-3 col-sm-6 col-xs-12 form-group">
												<label>Categoría</label>
												<select name="category" id="category" class="form-control">
													<option value="">Selecciona una opción</option>
													@foreach( App\Models\Core\CategoriaPadreProducto::getCategoriesList() as $value)
														<option value="{{ $value->codigo_cat_padre_producto }}">{{ ucfirst(strtolower($value->nombre_cat_padre_producto))}}</option>
													@endforeach
												</select>
											</div>
										@endif
								        <div class="col-md-3 col-sm-6 col-xs-12 form-group">
								            <label>Subcategoría</label>
								            <select name="subcategory" id="subcategory" class="form-control">
							                    <option value="">Selecciona una opción</option>
									            @foreach( App\Models\Core\CategoriaProducto::getSubcategorylist() as $value)
													<option value="{{ $value->codigo_categoria_prod_insti }}">{{ ucfirst(strtolower($value->nombre_categoria_producto))}}</option>
												@endforeach
											</select>
								        </div>
								        <div class="col-md-3 col-sm-6 col-xs-12 form-group">
								            <label>Estado</label>
								            <select name="codigo_estado" id="codigo_estado" class="form-control">
							                    <option value="">Selecciona una opción</option>
												@if(isset($states))
													@foreach( $states as $value)
														<option value="{{ $value->codigo_estado }}">{{ ucfirst(strtolower($value->nombre_estado))}}</option>
													@endforeach
												@endif
											</select>
								        </div>
										<div class="col-md-3 col-sm-6 col-xs-12">
											<label for="codigo_ciudad">Ciudad</label>
											<select name="codigo_ciudad" id="codigo_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Selecciona una opción" placeholder="Selecciona una opción" style="width: 100%">
											</select>
										</div>
										<div class="col-md-3 col-sm-6 col-xs-12">
											<label for="seg_codigo_usuario">Proveedor</label>
											<select name="seg_codigo_usuario" id="seg_codigo_usuario" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('entidad.search'))%>" data-param="<%- window._tp.getRole['seller'] %>" data-placeholder="Selecciona una opción" placeholder="Selecciona una opción" style="width: 100%">
											</select>
										</div>
								    </div>
									<br>
									<div class="row">
				                        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
				                          	<button type="button" class="btn btn-primary btn-search"> Buscar</button>
				                			<button type="button" class="btn btn-default btn-clear"> Limpiar</button>
											@if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/marketplace']) )
							                	<button type="button" class="btn btn-info data-export"> Exportar</button>
								           	@endif
				                        </div>
			                        </div>
						        {!! Form::close() !!}
						    </div>
   						 	<br/>
                            <!-- start products -->
              				<div class="table-responsive">
	                            <table id="products-search-table" class="table table-striped table-bordered" data-page-length="15">
	                                <thead>
		                                <tr>
				                            <th>Producto</th>
				                            <th>Proveedor</th>
				                            <th>Categoría</th>
				                            <th>Subcategoría</th>
				                            <th>Clase</th>
				                            <th>Precio</th>
			                                <th>Acción</th>
		                                </tr>
	                                </thead>
									<tbody >
										<!-- Render view  -->
									</tbody>
	                            </table>
	                        </div>
                            <!-- end user products -->
                  		</div>
	                </div>
	           </div>
        	</div>
    	</div>
	</script>

	{{-- template marketplace aside filter --}}
	<script type="text/template" id="transactions-list-tpl">
		@switch(session('institution')->codigo_institucion)
			@case(61)
				@include('marketplace.rutan.transactionslist')
				@break
			@default
				@include('marketplace.transactionslist')
		@endswitch
	</script>

	{{-- Detail agreement view --}}
	<script type="text/template" id="marketplace-agreement-tpl">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                    <h2>Detalle<small>Acuerdo</small></h2>
	                    <ul class="nav navbar-right panel_toolbox">
	                      	<li>
	                      	<a href="<%= window.Misc.urlFull(Route.route('marketplace.transacciones.index'))%>"><i class="fa fa-chevron-left"></i> Atrás</a>
	                      	</li>
	                    </ul>
	                	<div class="clearfix"></div>
                	</div>
                  	<div class="x_content">
                  		<div class="" role="tabpanel" data-example-id="togglable-tabs">
	                    	<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
		                        <li role="presentation" class="active">
		                        	<a href="#tab_agreement" role="tab" id="general-tab" data-toggle="tab" aria-expanded="false">Acuerdo</a>
		                        </li>
		                        <li role="presentation">
		                        	<a href="#tab_agreement_disscusion" role="tab" id="disscusion-tab" data-toggle="tab" aria-expanded="false">Comentarios</a>
		                        </li>
		                    </ul>
		                    <div id="myTabContent" class="tab-content">
				                <div role="tabpanel" class="tab-pane fade active in" id="tab_agreement" aria-labelledby="profile-tab">
                            <div id="agreement-actions">
                              {{-- Render agreement actions --}}
                            </div>
				                  	<div id="wrapper-render-agreement">
				                  		{{-- Render agreement template --}}
				                  	</div>
				                </div>
				                <div role="tabpanel" class="tab-pane fade" id="tab_agreement_disscusion" aria-labelledby="profile-tab">
									<div class="col-lg-6 col-md-10 col-sm-10 col-xs-12">
										<div id="wrapper-sale-agreement-discussion" class="panel panel-default">
											<div class="panel-body">
												{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-discussion-sale-greement') ) !!}
													<div class="row">
														<div class="col-md-10 col-xs-10">
								                            <textarea name="texto_discucion" id="texto_discucion" placeholder="comenta sobre el acuerdo" cols="50" rows="2" class="comment-discussion-initial resizable_textarea form-control" required autofocus></textarea>
														</div>
														<div class="col-md-2 col-xs-2">
								                            <button type="submit" class="btn btn-primary btn-md">Comentar</button>
														</div>
													</div>
						                        {!! Form::close() !!}
												@include('public.noresults', ['message' => 'No hay comentarios'])
												<section id="discussion-posts-wrap" class="">
													{{-- Render Tasks template --}}
												</section>
											</div>
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

  {{-- Template agreement liquidate --}}
  <script type="text/template" id="marketplace-agreement-liquidate-tpl">
    <div class="x_panel">
      {!! Form::open(array('route' => null,'id' => 'form-liquidate', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
        <div class="row">
          <div class="col-md-12 col-xs-12">
           <h3> Liquidar acuerdo </h3>
          </div>
          <div class="col-md-12 col-xs-12">
            <label>Resumen <span class="required">*</span></label>
           <textarea name="comment_liquidate" id="comment_liquidate" class="form-control" required></textarea>
          </div>
          <br>
          <div class="col-md-12 col-sm-4 col-xs-12">
            <label> ¿ Qué acción desea realizar con la liquidación del acuerdo ? </label>
            <select id="action_liquidate" name="action_liquidate" class="form-control" required>
              <option value=""> Selecciona... </option>
              <option value="1" > Devolver recursos a la bolsa del programa Aldea </option>
              <option value="2" > Recursos disponibles para el empresario </option>
            </select>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12 text-center">
           <br>
            <button type="submit" class="btn btn-success btn-liquidate"> Enviar </button>
          </div>
        </div>
      {!! Form::close() !!}
    </div>
  </script>

  {{-- Template agreement enlarge --}}
  <script type="text/template" id="marketplace-agreement-enlarge-tpl">
    <div class="x_panel">
      {!! Form::open(array('action' => null, 'data-abide' => '', 'id' => 'form-enlarge', 'data-parsley-validate' => '') ) !!}
        <div class="row">
          <div class="col-md-12 col-xs-12">
           <h3> Ampliar acuerdo </h3>
          </div>
          <br>
          <div class="col-md-12 col-sm-4 col-xs-12">
            <label>Fecha cierre</label>
            <input type="text" id="date_enlarge" name="date_enlarge" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'"  value="<%- fecha_final_acuerdo_compra ? fecha_final_acuerdo_compra : '' %>" required>
          </div>
          <div class="col-md-12 col-xs-12">
            <label>Comentario <span class="required">*</span></label>
            <textarea name="comment_enlarge" id="comment_enlarge" class="form-control" required></textarea>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12 text-center">
           <br>
            <button type="submit" class="btn btn-success btn-enlarge"> Enviar </button>
          </div>
        </div>
      {!! Form::close() !!}
    </div>
  </script>

  {{-- Template confirm enlarge --}}
  <script type="text/template" id="confirm-agreement-tpl">
    <% if (liquidate) { %>
      <p> ¿ Estas seguro de liquidar el acuerdo ?</p>
    <% } else { %>
     <p> ¿ Estas seguro de ampliar el acuerdo ?</p>
    <% } %>
	</script>

	 {{-- Template confirm liquidate --}}
  <script type="text/template" id="confirm-agreement-liquidate-tpl">
		<p>¿ Estas seguro de liquidar el acuerdo ?</p>
	</script>

	{{-- template agreement show --}}
	<script type="text/template" id="marketplace-agreement-show-tpl">
		<div class="x_panel">
		    <div class="col-md-12 col-sm-12 col-xs-12">
		        <% if(codigo_estado == window._tp.getStateAgreement['confirmed'] || this.model.get('codigo_estado') == window._tp.getState['finalized']) { %>
		          <% if( (fecha_final_bool_acuerdo_compra && !motivo_liquidacion) || (!fecha_final_bool_acuerdo_compra && !motivo_liquidacion)) { %>
                <div class="pull-right">
                  <div class="btn-group tp_btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <% if(!fecha_final_bool_acuerdo_compra) { %>
                      <li><a href="" class="btn-agreement-enlarge"><i class="fa fa-file-text-o"></i> Ampliar acuerdo</a>
                      </li>
                    <% } %>
                      <li><a href="" class="btn-evaluation-liquidate"><i class="fa fa-file-text"></i> Liquidar acuerdo </a>
                      </li>
                    </ul>
                  </div>
                 </div>
                <% } %>
            <% } %>
		        <h3 class="agreement_name_product"><%- nombre_producto %></h3>
            <% if(codigo_estado == window._tp.getStateAgreement['confirmed'] || this.model.get('codigo_estado') == window._tp.getState['finalized']) { %>
					    <h2 class="text_confirmed_agreement"> <i class="fa fa-check"></i> Acuerdo confirmado </h2>
				    <% } %>
            <strong class="text-uppercase">Vendedor:</strong> <%- nombre_vendedor %>
            <br>
            <strong class="text-uppercase">Comprador: </strong> <%- nombre_comprador %>
            <br>
            <strong class="text-uppercase">Fecha Compra: </strong> <%- fecha_compra %>
            <br>
            <% if( fecha_acuerdo_aceptacion_compra ){ %>
              <strong class="text-uppercase">Fecha aceptación: </strong> <%- fecha_acuerdo_aceptacion_compra %>
              <br>
            <% } %>
            <% if( fecha_acuerdo_aprobacion_compra ){ %>
              <strong class="text-uppercase">Fecha aprobación: </strong> <%- fecha_acuerdo_aprobacion_compra %>
              <br>
            <% } %>
            <% if(fecha_final_bool_acuerdo_compra){ %>
              <strong class="text-uppercase">Fecha finalización: </strong> <%-fecha_final_acuerdo_compra %> </p>
            <% } %>
		    </div>
		    {!! Form::open(array('route' => null,'id' => 'form-documenter', 'class' =>'form-horizontal form-label-left')) !!}
		        <!-- Table agreement purchase-->
	            <div class="col-md-12 col-sm-2 col-xs-12" >
					<h5 class="text-center"><strong>Detalle</strong></h5>
	                <div class="table-responsive">
	                    <table class="table table-striped table_agreement table-bordered " ><!-- data table table-striped no-margin-->
	                        <thead>
	                            <tr>
	                                <th class="text-left"> Alcance del servicio </th>
	                                <th  class="text-left"> Resultados a lograr </th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                            <tr>
	                                <td class="text-left" >
	                                    <%- alcance_acuerdo_compra%>
	                                </td>
	                                <td class="text-left" >
	                                    <%- resultados_acuerdo_compra%>
	                                </td>
	                            </tr>
	                        </tbody>
	                    </table>
	                </div>
	            </div>
		        <!-- end Table agreement purchase-->

		        <!-- Table activity agreement-->
	            <div class="col-md-12 col-sm-2 col-xs-12" >
					<h5 class="text-center"><strong>Actividades</strong></h5>
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered table_agreement" ><!-- data table table-striped no-margin-->
	                        <thead>
	                            <tr>
	                                <th class="text-left size_column_agreement"> Actividades a desarrollar </th>
	                                <th  class="text-left"> Responsable </th>
	                                <th class="text-center"> Fecha inicio </th>
	                                <th class="text-center"> Fecha fin</th>
	                                <th class="text-center"> No. sesiones trabajo presencial </th>
	                                <th class="text-center"> No. horas por sesión presencial </th>
	                                <th class="text-center"> No. sesiones trabajo virtual </th>
	                                <th class="text-center"> No. horas por sesión virtual </th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        <% _.each(actividad_acuerdo, function(activity){ %>
	                            <tr>
	                                <td class="text-left" >
	                                    <%- activity.desc_actividad_acuerdo %>
	                                </td>
	                                <td class="text-left" >
	                                <% if( activity.resp_comprador_actividad) {  %>
	                                        Empresario
	                                <% } else{%>
	                                        Proveedor
	                                <% }%>
	                                </td>
	                                <td class="text-center" >
	                                    <%- activity.fecha_inicial_actividad_acuerdo %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- activity.fecha_final_actividad_acuerdo %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- activity.sesiones_presencial %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- activity.sesiones_presencial_horas %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- activity.sesiones_virtual %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- activity.sesiones_virtual_horas %>
	                                </td>
	                            </tr>
	                        <% }) %>
	                        </tbody>
	                    </table>
	                </div>
	            </div>
		        <!-- end Table activity agreement-->

		        <!-- Table deliverable agreement-->
	            <div class="col-md-12 col-sm-2 col-xs-12" >
					<h5 class="text-center"><strong>Entregables</strong></h5>
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered table_agreement" ><!-- data table table-striped no-margin-->
	                        <thead>
	                            <tr>
	                                <th class="text-left"> Entregables acordados</th>
	                                <th class="text-right"> Adjuntos</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        <% _.each(entregable_acuerdo, function(deliverable){ %>
	                            <tr>
	                                <td class="text-left" >
	                                    <%- deliverable.descripcion_entregable %>
	                                </td>
	                                <td class="text-right" >
	                                    <% if( deliverable.direccion_adjunto != null) {  %>
	                                        <a href="<%- deliverable.direccion_adjunto %>"> <%- deliverable.nombre_adjunto %> <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
	                                    <% }%>
	                                </td>
	                            </tr>
	                        <% }) %>
	                        </tbody>
	                    </table>
	                </div>
	            </div>
		        <!-- end Table deliverable agreement -->

		        <!-- Table user agreement-->
	            <div class="col-md-12 col-sm-2 col-xs-12" >
					<h5 class="text-center"><strong>Equipo de trabajo del asesor</strong></h5>
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered table_agreement" >
	                        <thead>
	                            <tr>
	                                <th style="width:40%" class="text-left"> Nombre</th>
	                                <th style="width:30px" class="text-center"> Cargo</th>
	                                <th style="width:30px" class="text-center"> Datos</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        <% _.each(asesor_acuerdo, function(user_agreement){ %>
	                            <tr>
	                                <td class="text-left" >
	                                    <%- user_agreement.nombre_usuario_acuerdo %>
	                                </td>
	                                <td  class="text-center" >
	                                    <%- user_agreement.cargo_usuario_acuerdo %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- user_agreement.correo_usuario_acuerdo %>
	                                </td>
	                            </tr>
	                        <% }) %>
	                        </tbody>
	                    </table>
	                </div>
	            </div>

		        <!-- Table work team -->
	            <div class="col-md-12 col-sm-2 col-xs-12" >
					<h5 class="text-center"><strong>Equipo de trabajo del empresario</strong></h5>
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered table_agreement" >
	                        <thead>
	                            <tr>
	                                <th style="width:40%" class="text-left"> Nombre</th>
	                                <th style="width:30px" class="text-center"> Cargo</th>
	                                <th style="width:30px" class="text-center"> Datos</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        <% _.each(empresario_acuerdo, function(member_workteam_agreement){ %>
	                            <tr>
	                                <td class="text-left" >
	                                    <%- member_workteam_agreement.nombre_usuario_empresario %>
	                                </td>
	                                <td  class="text-center" >
	                                    <%- member_workteam_agreement.cargo_usuario_empresario %>
	                                </td>
	                                <td class="text-center" >
	                                    <%- member_workteam_agreement.correo_usuario_empresario %>
	                                </td>
	                            </tr>
	                        <% }) %>
	                        </tbody>
	                    </table>
	                </div>
	            </div>

		        <!-- end Table user agreement -->
	            <div class="col-md-12 col-sm-2 col-xs-12" >
					<h5 class="text-center"><strong>Detalle compra</strong></h5>
	                <div class="table-responsive">
	                    <table class="table table-striped table-bordered table_agreement">
	                        <thead>
	                            <tr>
	                                <th style="width:47%">Servicio</th>
	                                <th class="text-right">Valor unidad</th>
	                                <th class="text-right">Valor empresario</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                            <tr>
	                                <td class="text-left"><%- nombre_producto %></td>
	                                <td class="text-right">$ <%- valor_total_compra%></td>
	                                <td class="text-right"> $ 0 </td>
	                            </tr>
	                            <tr>
	                                <th class="dark-text">Total</th>
	                                <td>&nbsp;</td>
	                                <td class="text-right">$ <%- valor_total_compra %></td>
	                            </tr>
	                        </tbody>
	                    </table>
	                </div>
	            </div>

				<div class="form-group">
					<div class="row">
			            <div class="col-md-2 col-md-offset-5">
			                <a href="<%- window.Misc.urlFull(Route.route('marketplace.transacciones.acuerdo.exportar', {'acuerdo': codigo_acuerdo_compra })) %>" class="btn btn-block btn-primary" target="_blank" ><i class="fa fa-file-pdf-o"></i> Generar PDF</a>
			            </div>
		            </div>
		        </div>
		    {!! Form::close() !!}
		</div>
	</script>

	{{-- template agreement edit --}}
	<script type="text/template" id="marketplace-agreement-edit-tpl">
		{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-edit-sale-agreement', 'data-parsley-validate' => '') ) !!}
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<strong class="text-uppercase">Servicio:</strong> <%- nombre_producto %>
					<br>
					<strong class="text-uppercase">Vendedor:</strong> <%- nombre_vendedor %>
					<br>
					<strong class="text-uppercase">Comprador: </strong> <%- nombre_comprador %>
					<br>
					<strong class="text-uppercase">Fecha Compra: </strong> <%- fecha_compra %>
          <br>
					<% if( fecha_acuerdo_aceptacion_compra ){ %>
						<strong class="text-uppercase">Fecha aceptación: </strong> <%- fecha_acuerdo_aceptacion_compra %>
						<br>
					<% } %>
					<% if( fecha_acuerdo_aprobacion_compra ){ %>
						<strong class="text-uppercase">Fecha aprobación: </strong> <%- fecha_acuerdo_aprobacion_compra %>
            <br>
					<% } %>
          <% if(fecha_final_bool_acuerdo_compra){ %>
            <strong class="text-uppercase">Fecha finalización: </strong> <%-fecha_final_acuerdo_compra %> </p>
          <% } %>
          <br>
					<br>
					<div class="form-group">
						<h2>Alcance del servicio</h2>
						<div class="clearfix"></div>
						<p class="font-gray-dark">
						  Describe de manera clara y concisa las actividades a desarrollar para la prestación del servicio.</b>
						</p>

						<label for="ex3">Descripción</label>
						<textarea class="form-control" rows="3" name="alcance_acuerdo_compra" id="descripcionagreement" required><%- alcance_acuerdo_compra %></textarea>
						<div class="ln_solid"></div>

						<!-- Table activity agreement -->
						<fieldset class="well panel-section-form" name="fields_agreement_activities" id="fields_agreement_activities" data-parsley-group="fields_agreement_activities">
							<div class="form-group">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12" >
										<div class="row">
											<div class="col-md-6 col-sm-12 col-xs-12">
												<h2>Actividades a desarrollar</h2>
												<p class="font-gray-dark">
												  Describir de manera clara y concisa cada una de las actividades a desarrollar con prestación del servicio.</b>
												</p>
												<label for="desc_actividad_acuerdo">Descripción</label>
												<textarea class="form-control" rows="3" placeholder="" name="desc_actividad_acuerdo" id="desc_actividad_acuerdo" data-parsley-group="fields_agreement_activities" required></textarea>
											</div>
											<div class="col-md-6">
												<h2>No. de sesiones de trabajo presenciales</h2>
												<p class="font-gray-dark">
												  Selecciona el número de sesiones del trabajo presencial requeridos.</b>
												</p>
												<div class="row">
													<div class="col-md-3 col-sm-12 col-xs-12">
														<label for="sesiones_presencial">No. sesiones</label>
														<input type="number" name="sesiones_presencial" id="sesiones_presencial" class="form-control" value="0" min="0" data-parsley-group="fields_agreement_activities">
													</div>
													<div class="col-md-3 col-sm-12 col-xs-12">
														<label for="sesiones_presencial_horas">No. horas</label>
														<input type="number" name="sesiones_presencial_horas" id="sesiones_presencial_horas" class="form-control" value="0" min="0" data-parsley-group="fields_agreement_activities">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<h2>Responsable</h2>
												<p class="font-gray-dark">
												  Indique si el responsable de la actividad es el empresario/emprendedor o el asesor/proveedor.</b>
												</p>
												<div class="row">
													<div class="col-md-6">
														<p>
															<input type="radio" value="resp_comprador_actividad" id="resp_comprador_actividad" name="resp_actividad_acuerdo" data-parsley-group="fields_agreement_activities" required>
															<label for="resp_comprador_actividad">Empresario</label>

															<input type="radio" value="resp_proveedor_actividad" id="resp_proveedor_actividad" name="resp_actividad_acuerdo" data-parsley-group="fields_agreement_activities">
															<label for="resp_proveedor_actividad">Proveedor</label>
														</p>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<h2>Fechas</h2>
														<div class="row">
															<div class="col-md-6">
																<label>Fecha inicio</label>
																<input type="text" id="fecha_inicial_actividad_acuerdo" name="fecha_inicial_actividad_acuerdo" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" data-parsley-group="fields_agreement_activities" required>
															</div>

															<div class="col-md-6">
																<label>Fecha fin</label>
																<input type="text" id="fecha_final_actividad_acuerdo" name="fecha_final_actividad_acuerdo" class="form-control datepicker" data-inputmask="'mask':'9999-99-99'" data-parsley-group="fields_agreement_activities" required>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<h2>No. de sesiones de trabajo virtuales</h2>
												<p class="font-gray-dark">
												  Selecciona el número de sesiones del trabajo virtual requeridos.</b>
												</p>
												<div class="row">
													<div class="col-md-3 col-sm-12 col-xs-12">
														<label for="sesiones_virtual">No. sesiones</label>
														<input type="number" name="sesiones_virtual" id="sesiones_virtual" class="form-control" value="0" min="0" data-parsley-group="fields_agreement_activities" required>
													</div>
													<div class="col-md-3 col-sm-12 col-xs-12">
														<label for="sesiones_virtual_horas">No. horas</label>
														<input type="number" name="sesiones_virtual_horas" id="sesiones_virtual_horas" class="form-control" value="0" min="0" data-parsley-group="fields_agreement_activities" required>
													</div>
												</div>
											</div>
										</div>
										<br>
										<div class="text-center">
											<a id="add-agreement-activity" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</a>
										</div>
										<div class="table-responsive editable">
											<table class="table table-hover" id="sale-agreement-activities"><!-- data table table-striped no-margin-->
												<thead>
													<tr>
														<th class="text-left">Actividades a desarrollar </th>
														<th class="text-left">Responsable </th>
														<th class="text-center">Fecha inicio </th>
														<th class="text-center">Fecha fin</th>
														<th class="text-center">No. sesiones trabajo presencial </th>
														<th class="text-center">No. horas por sesión presencial </th>
														<th class="text-center">No. sesiones trabajo virtual </th>
														<th class="text-center">No. horas por sesión virtual </th>
														<th class="text-center">Acción </th>
													</tr>
												</thead>
												<tbody>
													<!-- Render template id::item-agreement-sale-activity-tpl -->
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
						<!-- end Table activity agreement-->

						<!-- Table deliverable agreement-->
						<fieldset class="well panel-section-form" name="fields_agreement_deliverables" id="fields_agreement_deliverables" data-parsley-group="fields_agreement_deliverables">
							<div class="form-group">
								<!-- <label class="control-label col-md-1" for=""></label> -->
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<h2>Entregables acordados <small>* Agrega mínimo un entregable</small></h2>
										<p>
											Describir de manera clara y concisa cada uno de los entregables para la presentación del servicio.
										</p>
										<label for="ex4">Descripción</label>
										<textarea class="form-control" name="descripcion_entregable" id="descripcion_entregable" rows="3" data-parsley-group="fields_agreement_deliverables" required></textarea>
										<br>
										<div class="text-center">
											<a id="add-agreement-deliverable" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</a>
										</div>
										<div class="table-responsive editable">
											<table class="table table-hover" id="sale-agreement-deliverables"><!-- data table table-striped no-margin-->
												<thead>
													<tr>
														<th class="text-left"> Entregables acordados</th>
														<th class="text-right"> Adjuntos</th>
														<th class="text-right"> Acción</th>
													</tr>
												</thead>
												<tbody>
													<!-- Render template id::item-agreement-sale-deliverable-tpl -->
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
						<!-- end Table deliverable agreement -->

						<h2>Resultados a lograr</h2>
						<p>
							Describir de manera clara y concisa cada uno de los resultados a lograr por la empresa con la prestación del servicio.
						</p>
						<textarea class="form-control" rows="3" name="resultados_acuerdo_compra" id="resultadosagreement" required><%- resultados_acuerdo_compra %></textarea>
						<div class="ln_solid"></div>

						<!-- Table user agreement-->
						<fieldset class="well panel-section-form" name="fields_agreement_advisors" id="fields_agreement_advisors" data-parsley-group="fields_agreement_advisors">
							<div class="form-group">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<h2>Equipo de trabajo del asesor</h2>
										<% if (codigo_usuario_entidad){ %>
											<div class="row">
												<div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
													<label>Integrantes:</label>
													<select name="codigo_grupo_usuario" id="codigo_grupo_usuario" class="form-control choice-select-autocomplete" data-ajax--url="<%= window.Misc.urlFull( Route.route('usuario.equipo.index', {codigo_usuario: codigo_usuario_entidad}) ) %>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-parsley-group="fields_agreement_advisors" style="width: 100%" required>
													</select>
												</div>
											</div>
											<br>
											<div class="text-center">
												<a id="add-agreement-adviser" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</a>
											</div>
										<% } %>
										<div class="table-responsive editable">
											<table class="table table-hover" id="sale-agreement-advisors">
												<thead>
													<tr>
														<th style="width:40%" class="text-left"> Nombre</th>
														<th style="width:30px" class="text-center"> Cargo</th>
														<th style="width:30px" class="text-center"> Correo Electrónico</th>
														<% if(codigo_usuario_entidad){ %>
															<th style="width:30px" class="text-center"> Acción</th>
														<% } %>
													</tr>
												</thead>
												<tbody>
													{{-- Render template id::item-agreement-sale-adviser-tpl --}}
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
						<!-- end Table user agreement -->

						<!-- Table work team agreement-->
						<fieldset class="well panel-section-form" name="fields_agreement_businessman" id="fields_agreement_businessman" data-parsley-group="fields_agreement_businessman">
							<div class="form-group">
								<label class="control-label col-md-1" for=""></label>
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<h2>Equipo de trabajo del empresario</h2>
										<div class="row">
											<div class="col-md-4">
												<label for="nombre_usuario_empresario">Nombre :</label>
												<input type="text" name="nombre_usuario_empresario" id="nombre_usuario_empresario" class="form-control" data-parsley-group="fields_agreement_businessman" required>
											</div>
											<div class="col-md-4">
												<label for="cargo_usuario_empresario">Cargo :</label>
												<input type="text" name="cargo_usuario_empresario" id="cargo_usuario_empresario" class="form-control"  data-parsley-group="fields_agreement_businessman" required>
											</div>
											<div class="col-md-4">
												<label for="correo_usuario_empresario">Correo electrónico :</label>
												<input type="email" name="correo_usuario_empresario" id="correo_usuario_empresario" class="form-control" data-parsley-trigger="change" data-parsley-group="fields_agreement_businessman" required>
											</div>
										</div>
										<br>
										<div class="text-center">
											<a id="add-agreement-businessman" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</a>
										</div>
										<div class="table-responsive editable">
											<table class="table table-hover" id="sale-agreement-businessman">
												<thead>
													<tr>
														<th style="width:40%" class="text-left"> Nombre</th>
														<th style="width:30px" class="text-center"> Cargo</th>
														<th style="width:30px" class="text-center"> Correo Electrónico</th>
														<th style="width:30px" class="text-center"> Acción</th>
													</tr>
												</thead>
												<tbody>
													{{-- Render template id::item-agreement-sale-businessman-tpl --}}
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
						<!-- end Table work team agreement -->

						<label class="control-label col-md-1" for=""></label>
						<div class="row ">
							<div class="col-md-12 col-sm-12 col-xs-12" >
								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
											<tr>
												<th style="width:47%">Servicio</th>
												<th class="text-left">Valor unidad</th>
												<th class="text-right">Valor empresario</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="text-left"><%- nombre_producto %></td>
												<td class="text-left">$ <%- valor_total_compra%></td>
												<td class="text-right"> $ 0 </td>
											</tr>
											<tr>
												<th class="dark-text">Total</th>
												<td>&nbsp;</td>
												<td class="text-right">$ <%- valor_total_compra %></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div id="btn-actions-agreement-purchase">
							<div class="row">
								<div class="col-md-12 col-sm-2 col-xs-12" >
									<!-- Render template actions buttons - id:btn-actions-agreement-purchase-tpl-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{!! Form::close() !!}
	</script>

	{{-- template text confirm --}}
	<script type="text/template" id="active-confirm-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> el producto <strong><%- product %></strong>?</p>
	</script>

	{{-- template actions butons --}}
	<script type="text/template" id="btn-actions-agreement-purchase-tpl">
		<div class="row">
			<div class="col-md-2 col-sm-2 col-xs-12">
				<a href="<%- window.Misc.urlFull(Route.route('marketplace.transacciones.index')) %>" class="btn btn-default">Volver atrás</a>
			</div>
			<div class="col-md-8 col-sm-8 col-xs-12 text-center">
				<a href="#" formnovalidate class="btn btn-success" id="enviar-revision">Devolver a revisión</a>
					<a href="#" formnovalidate class="btn btn-primary" id="confirmar">Confirmar</a>
				</div>
			<div class="col-md-2 col-sm-2 col-xs-12 text-right">
				<a href="<%- window.Misc.urlFull(Route.route('marketplace.transacciones.acuerdo.exportar', {'acuerdo': codigo_acuerdo_compra })) %>" class="btn btn-primary" target="_blank" ><i class="fa fa-file-pdf-o"></i> Generar PDF</a>
			</div>
		</div>
	</script>

	{{-- template agreement activity --}}
	<script type="text/template" id="item-agreement-sale-activity-tpl">
		<% typeof(codigo_actividad_acuerdo) !== "undefined" || (codigo_actividad_acuerdo = this.model.cid);  %>
		<td>
			<div id="activity-desc-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-textarea" data-edit-renderto="#activity-desc-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="desc_actividad_acuerdo" >
					<p class="text-agreement-activity prepared-to-edit"><%- desc_actividad_acuerdo %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td>
			<div id="activity-resp-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-select" data-edit-renderto="#activity-resp-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="resp_actividad_acuerdo" >
					<% typeof(resp_actividad_acuerdo) !== "undefined" || (resp_actividad_acuerdo = ''); %>
					<p class="text-agreement-activity prepared-to-edit">
						<% if((resp_comprador_actividad === true && !resp_actividad_acuerdo) || resp_actividad_acuerdo === 'resp_comprador_actividad') { %>
							<i class="fa fa-briefcase"></i> Empresario
						<% }

					 	if((resp_proveedor_actividad === true && !resp_actividad_acuerdo) || resp_actividad_acuerdo === 'resp_proveedor_actividad' ) { %>
							<i class="fa fa-truck"></i> Proveedor
						<% } %>
					</p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="activity-dateini-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-datepicker" data-edit-renderto="#activity-dateini-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="fecha_inicial_actividad_acuerdo" >
					<p class="text-agreement-activity prepared-to-edit"><%- window.moment(fecha_inicial_actividad_acuerdo, 'YYYY-MM-DD').format('DD/MM/YYYY') %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td>
			<div id="activity-datefin-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-datepicker" data-edit-renderto="#activity-datefin-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="fecha_final_actividad_acuerdo" >
					<p class="text-agreement-activity prepared-to-edit"><%- window.moment(fecha_final_actividad_acuerdo, 'YYYY-MM-DD').format('DD/MM/YYYY') %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="activity-sespre-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-number" data-edit-renderto="#activity-sespre-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="sesiones_presencial" >
					<p class="text-agreement-activity prepared-to-edit"><%- sesiones_presencial %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="activity-sespre-hours-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-number" data-edit-renderto="#activity-sespre-hours-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="sesiones_presencial_horas" >
					<p class="text-agreement-activity prepared-to-edit"><%- sesiones_presencial_horas %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="activity-sesvir-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-number" data-edit-renderto="#activity-sesvir-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="sesiones_virtual" >
					<p class="text-agreement-activity prepared-to-edit"><%- sesiones_virtual %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="activity-sesvir-hours-container-<%- codigo_actividad_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-number" data-edit-renderto="#activity-sesvir-hours-container-<%- codigo_actividad_acuerdo %> .field-editing" data-edit-attr="sesiones_virtual_horas" >
					<p class="text-agreement-activity prepared-to-edit"><%- sesiones_virtual_horas %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<a href="#" title="Quitar elemento" class="btn btn-danger remove-item-agreement close-hover-circle"><i class="fa fa-trash"></i></a>
		</td>
	</script>

	{{-- template agreement deliverable --}}
	<script type="text/template" id="item-agreement-sale-deliverable-tpl">
		<% typeof(codigo_entregable_acuerdo) !== "undefined" || (codigo_entregable_acuerdo = this.model.cid);  %>
		<td style="width:90%">
			<div id="deliverable-nombre-container-<%- codigo_entregable_acuerdo %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-textarea" data-edit-renderto="#deliverable-nombre-container-<%- codigo_entregable_acuerdo %> .field-editing" data-edit-attr="descripcion_entregable" >
					<p class="text-agreement-deliverable prepared-to-edit"><%- descripcion_entregable %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-right" >
			<% typeof(direccion_adjunto) !== "undefined" || (direccion_adjunto = ''); typeof(nombre_adjunto) !== "undefined" || (nombre_adjunto = ''); %>
			<% if( direccion_adjunto != null && direccion_adjunto != '' ) {  %>
				<a href="<%- direccion_adjunto %>"> <%- nombre_adjunto %> <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
			<% }%>
		</td>
		<td class="text-center">
			<a href="#" title="Quitar elemento" class="btn btn-danger remove-item-agreement close-hover-circle"><i class="fa fa-trash"></i></a>
		</td>
	</script>

	{{-- template agreement adviser --}}
	<script type="text/template" id="item-agreement-sale-adviser-tpl">
		<td>
			<%- nombre_usuario_acuerdo %>
		</td>
		<td class="text-center">
			<%- cargo_usuario_acuerdo %>
		</td>
		<td class="text-center">
			<%- correo_usuario_acuerdo %>
		</td>
		<% if(codigo_usuario_entidad){ %>
		<td class="text-center">
				<a href="#" title="Quitar elemento" class="btn btn-danger remove-item-agreement close-hover-circle"><i class="fa fa-trash"></i></a>
		</td>
		<% } %>
	</script>

	{{-- template agreement businessman --}}
	<script type="text/template" id="item-agreement-sale-businessman-tpl">
		<% typeof(codigo_usuario_empresario) !== "undefined" || (codigo_usuario_empresario = this.model.cid);  %>
		<td>
			<div id="user-nombre-container-<%- codigo_usuario_empresario %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-text" data-edit-renderto="#user-nombre-container-<%- codigo_usuario_empresario %> .field-editing" data-edit-attr="nombre_usuario_empresario" >
					<p class="text-agreement-user prepared-to-edit"><%- nombre_usuario_empresario %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="user-cargo-container-<%- codigo_usuario_empresario %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-text" data-edit-renderto="#user-cargo-container-<%- codigo_usuario_empresario %> .field-editing" data-edit-attr="cargo_usuario_empresario" >
					<p class="text-agreement-user prepared-to-edit"><%- cargo_usuario_empresario %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<div id="user-correo-container-<%- codigo_usuario_empresario %>" class="editable-item">
				<div class="field-for-edit edit-field" data-edit-template="form-text" data-edit-renderto="#user-correo-container-<%- codigo_usuario_empresario %> .field-editing" data-edit-attr="correo_usuario_empresario" >
					<p class="text-agreement-user prepared-to-edit"><%- correo_usuario_empresario %></p>
				</div>
				<div class="field-editing">
					{{-- Render template edit form--}}
				</div>
			</div>
		</td>
		<td class="text-center">
			<a href="#" title="Quitar elemento" class="btn btn-danger remove-item-agreement close-hover-circle"><i class="fa fa-trash"></i></a>
		</td>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
		<div class="btn-group tp_btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right" role="menu">
				<% if(full.codigo_estado == window._tp.getState['review']) { %>
					<li><a href="#" class="btn-approve" data-action="aprobar" data-id="<%- full.codigo_producto %>" data-name="<%- full.nombre_producto %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-check"></i> Aprobar</a>
					</li>
					<li><a href="#" class="btn-back" data-action="rechazar" data-id="<%- full.codigo_producto %>" data-name="<%- full.nombre_producto %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-close"></i> Rechazar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['inactive']) { %>
					<li><a href="#" class="btn-active success" data-action="activar" data-id="<%- full.codigo_producto %>" data-name="<%- full.nombre_producto %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Activar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['active']) { %>
					<li><a href="#" class="btn-inactive danger" data-action="inactivar" data-id="<%- full.codigo_producto %>" data-name="<%- full.nombre_producto %>" data-state="<%- full.codigo_estado %>"><i class="fa fa-power-off"></i> Inactivar</a>
					</li>
				<% }

				if(full.codigo_estado == window._tp.getState['pending']) { %>
					<li><a>No hay acciones.</a></li>
				<% } %>
			</ul>
		</div>
	</script>

	{{-- buttons acction --}}
	<script type="text/template" id="buttons-action-transactions-tpl">
		@switch(session('institution')->codigo_institucion)
			@case(61)
				@include('marketplace.rutan.buttonsactiontransactions')
				@break
			@default
				@include('marketplace.buttonsactiontransactions')
		@endswitch
	</script>

	{{-- Template section option channel users --}}
	<script type="text/template" id="form-users-modal-tpl">
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Correo electrónico</label>
				<input type="text" name="correo_electronico_datatable" id="correo_electronico_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nombre</label>
				<input type="text" name="nombre_usuario_datatable" id="nombre_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Nickname</label>
				<input type="text" name="nickname_usuario_datatable" id="nickname_usuario_datatable" class="form-control">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label>Ciudad</label>
				<select name="codigo_ciudad_datatable" id="codigo_ciudad_datatable" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('ciudades.index'))%>" data-placeholder="Selecciona una ciudad" placeholder="Selecciona una ciudad"  style="width: 100%">
				</select>
			</div>
		</div>
		<br>
		<div class="text-center">
			<button type="button" class="btn btn-primary btn-search-user"> Buscar</button>
			<button type="button" class="btn btn-default btn-clear-user"> Limpiar</button>
		</div>
		<br>
		<div class="table-responsive">
			<table id="user-list-search-table" class="table table-striped table-bordered" data-page-length="15">
				<thead>
				<tr class="headings">
					<th class="column-title">Nombre</th>
					<th class="column-title">Nick Name</th>
					<th class="column-title">Correo</th>
					<th class="column-title">Ciudad</th>
				</tr>
				</thead>
				<tbody >
				<!-- Render view  -->
				</tbody>
			</table>
		</div>
	</script>

	{{-- Template modal refused product --}}
	<script type="text/template" id="comment-refused-product-tpl">
		<label>Describa el por qué se rechaza el producto <span class="required">*</span></label>
		<textarea id="comment_refused_product" required="required" class="form-control" name="comment_refused_product" data-parsley-trigger="keyup"></textarea>
	</script>
@stop
