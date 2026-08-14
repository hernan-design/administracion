@extends('event.event')

@section('event.content')
    {{-- ------------------ Event Templates JS -------------------- --}}
    {{-- template Event aside filter --}}
    <script type="text/template" id="events-for-user-tpl">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Eventos <small>Por usuario</small></h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a 
								href="{{ route('evento.index') }}">
								<i class="fa fa-chevron-left"></i> 
								Ir a Lista Eventos
							</a>
						</li>
						<li>
							<a 
								href="{{ route('usuario.index') }}">
								<i class="fa fa-chevron-left"></i> 
								Ir a Usuarios
							</a>
						</li>
					</ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="well">
						{!! Form::open(array('route' => null,'id' => 'form-filter-event', 'class' =>'')) !!}
							<div class="row">
								<input type="hidden" id="codigo_usuario" name="codigo_usuario" value="{{ $codigo_usuario?: '' }}">
								<div class="col-md-4 col-md-offset-2 col-sm-12 col-xs-12 form-group">
									<label>Email</label>
									<input type="text" id="correo_electronico" name="correo_electronico" class="form-control">
								</div>
								<div class="col-md-4 col-sm-12 col-xs-12 form-group">
									<label>Número de documento</label>
									<input type="text" name="identificacion_usuario" id="identificacion_usuario" class="form-control">
								 </div>
							</div>
		
							<div class="text-center">
								  <button type="button" class="btn btn-primary btn-search">Buscar</button>
								<button type="button" class="btn btn-default btn-clear">Limpiar</button>
							</div>
						{!! Form::close() !!}
					</div>
					<br/>
                    <div class="table-responsive">
                        <table id="event-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                            <tr class="headings">
								<th class="column-title text-center">Código evento</th>
                                <th class="column-title text-center">Nombre</th>
								<th class="column-title text-center">Fecha inicio</th>
								<th class="column-title text-center">Fecha fin</th>
                                <th class="column-title text-center">Asistencia</th>
                                <th class="column-title text-center">Cerfiticado</th>
                                <th class="column-title text-center">Código Certificado</th>
                                <th class="column-title text-center">Factura</th>

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

	<script type="text/template" id="confirm-event-tpl">
		<p>¿Estás seguro de <strong><%- action %></strong> el evento <strong><%- event_name %></strong>?</p>
	</script>
@stop
