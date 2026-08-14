<script type="text/template" id="assigment-detail-tpl">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Detalle de la asignación </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="<%= window.Misc.urlFull(Route.route('evaluacion.asignacion.index'))%>"><i class="fa fa-chevron-left"></i> Atrás</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <section class="panel">
                        <div id="assignment-list-jury">
                            <div class="panel-body">
                                <div class="well">
                                    <h3><strong> <%- nombre_entidad %></strong></h3>
                                    <br />
                                    <div class="project_detail">
                                        <div class="form-group">
                                            <p class="title">Usuario</p>
                                            <p><%-nombre_usuario %></p>
                                            <p class="title">Convocatoria</p>
                                            <p><%-nombre_convocatoria %></p>
                                            <p class="title">Fase</p>
                                            <p><%- nombre_fase %></p>
                                            <p class="title">Fecha Registro</p>
                                            <p><%- fecha_registro %></p>
                                        </div>
                                    </div>
                                    {!! Form::open(array('route' => null,'id' => 'form-assignment-list-jury', 'class' =>'form-horizontal form-label-left', 'novalidate' => '', 'data-parsley-validate' => '')) !!}
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 col-xs-12 item form-group">
                                                <label>Correo <span class="required">*</span></label>
                                                <input type="email" required="required" class="form-control" placeholder="Email" id="email" name="email">
                                                <span id='error-email' class="alert alert-danger"></span>
                                            </div>
                                            <div class="col-md-4 col-sm-12 col-xs-12 item form-group">
                                                <label>Tipo evaluacion <span class="required">*</span></label>
                                                <select id="tipo_evaluacion" name="tipo_evaluacion" class="form-control" required="required">
                                                <option value="" selected>Elige el tipo</option>
                                                <option value="0">Presencial</option>
                                                <option value="1">Virtual</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-12 col-xs-12 item form-group">
                                                <label>Lugar evaluación (Campo opcional)</label>
                                                <input type="text"  name="lugar_evaluacion_jurado" id="lugar_evaluacion_jurado"  class="form-control" disabled>
                                            </div>
                                            <div class="col-md-4 col-sm-12 col-xs-12 item form-group">
                                                <label>Identificación evaluador <span class="required">*</span></label>
                                                <input type="number" id="identificacion_evaluador" name="identificacion_evaluador" class="form-control" placeholder="Identificación" required="required">
                                                <span id='error-identification' class="alert alert-danger"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7 col-md-offset-5">
                                                <button type="submit" class="btn btn-dark add-jury">Agregar</button>
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                                <br /><br />
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <!-- start Jury Assignment -->
                                        <div id="wrapper-assignment-jury" class="table-responsive">
                                            <table id="list-assignment-jury" class="table table-striped jambo_table bulk_action" ><!-- data table table-striped no-margin-->
                                                <thead>
                                                    <tr>
                                                        <th colspan="8" class="text-center"><%- codigo_fase == window._tp.getPhases['challenge_1.2'] ? 'Evaluadores' : 'Expertos' %> asignados</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Evaluador</th>
                                                        <th>Fecha asignación</th>
                                                        <th>Estado</th>
                                                        <th>Lugar evaluación</th>
                                                        <th>Fecha evaluación</th>
                                                        <% if(asignacion_especialidad) { %>
                                                            <th>Tipo experto</th>
                                                        <% } %>
                                                        <th class="text-center">Editar</th>
                                                        <th class="text-center">Eliminar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Render view  -->
                                                </tbody>
                                            </table>
                                            @include('public.noresults', ['message' => 'No hay evaluadores'])
                                            <!-- end Jury Assignment -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</script>