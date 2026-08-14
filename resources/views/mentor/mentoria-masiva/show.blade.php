{{-- views/mentor/index.blade.php --}}
@extends('mentor.mentor')

@section('mentor.content')
	<div class="col-xs-12" id="mentoria-masiva-show">
		<div class="x_panel">
			<div class="x_title">
				<h2>Mentorías masivas<small>vista única</small></h2>
				<ul class="nav navbar-right panel_toolbox">
                    <li>
                    	<a id="history-back" href=""><i class="fa fa-chevron-left"></i> Atrás</a>
                    </li>
				</ul>
			<div class="clearfix"></div>
			</div>

			<div class="x_content">
				<br/>
				<h4><b>Mentor:</b> {{ $mentor }}</h4>
				<input type="hidden" id="mentoria_masiva" value="{{ $codigo_mentoria_masiva }}">
				<input type="hidden" id="count_asesorias" value="{{ $count_asesorias }}">
				{{-- Lista de proyectos para procesos de mentoria --}}
				<div class="table-responsive">
					<table id="mentoria-masiva-show-table" class="table table-striped table-bordered" data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title"># mentoría</th>
							<th class="column-title">Entidad</th>
							<th class="column-title">Usuario</th>
							<th class="column-title">Correo Usuario</th>
							<th class="column-title">Fecha mentoría</th>
						</tr>
						</thead>
						<tbody >
						<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<br/>

			</div>
			<div class="x_content well">
				<div class="row">
					<h2 class="mx-auto mb-4 mt-5" title="Crear evento de mentoría"><b>Crear sesión masiva</b></h2>
				</div>
				<div class="row col-md-12 col-sm-12 col-xs-12">
					{{-- <div class="col-md-12 col-sm-12 col-xs-12"> --}}
					<div id="form-create-session" class="mx-auto"  style="width: 500px;">
					{{-- create-request-mentoring-tpl --}}
					</div>
				</div>
				</div>
			</div>

			<div class="x_content">
				{{-- Lista de sesiones masivas --}}
				<br/>
				<h4>Listado de sesiones masivas</h4>
				<div class="table-responsive">
					<table id="sesion-masiva-tabla" class="table table-striped table-bordered" data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title"># sesión masiva</th>
							<th class="column-title">Fecha creación sesión masiva</th>
						</tr>
						</thead>
						<tbody >
						<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<br/>
			</div>
			<div class="x_content">
				{{-- Lista de sesiones masivas --}}
				<br/>
				<h4>Sesión seleccionada</h4>
				<div class="btn-group tp_btn-group"style="justify-content: flex-end;display: flex;">Seleccionar todos &nbsp;
					<input type="checkbox" class="select-all-items" id="select_all_items" title="Por defauld todas las sesiones están selecionadas." />
				</div>
				<div class="table-responsive">
					<table id="sesion-unica-tabla" class="table table-striped table-bordered datatable" data-page-length="15">
						<thead>
							<tr>
								<th># sesión masiva</th>
								<th># sesión</th>
								<th>Estado</th>
								<th>Entidad</th>
								<th>Usuario</th>
								<th>Correo</th>
								<th>Fecha sesión</th>
								<th>Descripción de la intervención</th>
								<th>Nombre del Beneficio Otorgado</th>
								<th>Acuerdos/Plan de acción</th>
								<th>&nbsp;</th>
								{{-- <th class="column-title">Observaciones</th>
								<th class="column-title">Duración de la sesión</th>
								<th class="column-title">Fases del Proceso</th>
								<th class="column-title">¿Se recomienda para la siguiente etapa?</th>
								<th class="column-title">Seguimiento</th>
								<th class="column-title">Asignaciónde VOUCHER</th> --}}
							</tr>
						</thead>
						<tbody >
						<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<br/>
			</div>

			<div class="x_content well">
				<div class="row">
					<h2 class="mx-auto mb-4 mt-5" title="Desarrollo de sesiones masiva">
						<b>Desarrollo de sesiones</b>
					</h2>
				</div>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div id="form-development-session" class="mx-auto"  style="max-width: 500px;">
							<p  style="text-align: center;">Selecciona una sesión masiva</p>
							{{-- request-development-sessions-tpl --}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

{{-- formulario para creacion de sesiones template --}}
<script type="text/template" id="create-request-mentoring-tpl">
	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-create-calendar-event') ) !!}
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
				<label for="codigo_tipo_evento">Tipo:</label>
				<select
					id="codigo_tipo_evento"
					name="codigo_tipo_evento"
					class="form-control"
					required
					placeholder="Elige un tipo evento"
					readonly>
					<option value="1"  selected>Sesión de mentoría</option>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-6 col-sm-6 col-xs-12 form-group">
				<label for="start-event">Fecha Inicio</label>
				<input
					class="form-control datetimepicker_start_event"
					id="start-event"
					name="fecha_inicio"
					required
					type="text"
					value="">
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12 form-group">
				<label for="end-event">Fecha Fin</label>
				<input
					class="form-control datetimepicker_end_event"
					id="end-event"
					name="fecha_fin"
					required
					type="text"
					value=""
					>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
				<label for="codigo_ciudad">Ciudad</label>
				<select
					class="form-control"
					id="codigo_ciudad"
					name="codigo_ciudad"
					placeholder="Seleccione..."
					required
					style="width: 100%"
					readonly>
					<option value="2259" selected>Medellín - Antioquia - COL</option>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
				<label for="codigo_tema_principal_evento">Nombre del Beneficio Otorgado</label>
				<select
					class="form-control choice-select-autocomplete"
					data-ajax--url="{{ route('mentorias.gettemassesionlist') }}"
					data-placeholder="Seleccione..."
					id="codigo_tema_principal_evento"
					name="codigo_tema_principal_evento"
					placeholder="Seleccione"
					required
					style="width: 100%"
					>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
				<label for="lugar_evento">Link o lugar de reunión</label>
				<input
					class="form-control"
					id="lugar_evento"
					name="lugar_evento"
					required
					type="text"
					value="<%- typeof lugar_evento !== 'undefined' ? lugar_evento : '' %>"
					>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group input-field">
				<label for="nombre_evento">Título del evento</label>
				<input
					area-default="{{ trans('event.mentoring.name.event') }}"
					class="form-control"
					id="nombre_evento"
					name="nombre_evento"
					required
					type="text"
					value="<%- typeof nombre_evento !== 'undefined' ? nombre_evento : '' %>"
					>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12 form-group input-field">
				<label>Descripción de la sesión</label>
				<textarea
					area-default="{{ Lang::get('tuproyecto.mentoring.event.description') }}"
					class="form-control"
					name="descripcion_evento"
					rows="2"
					value=""></textarea>
			</div>
		</div>
		<div class="row my-3">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<button
					class="btn btn-danger form-control"
					type="reset">
					Limpiar
				</button>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<button
					class="btn btn-success form-control"
					type="submit">
					Crear
				</button>
			</div>
		</div>
	{!! Form::close() !!}
</script>

{{-- formulario para Desarrollo de sesiones template --}}
<script type="text/template" id="request-development-sessions-tpl">
	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form_mentoring_description') ) !!}
		<div class="row mt-3">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<label for="codigo_tipo_evento">Descripción de la intervención</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<textarea
					id="mentoring_description"
					name="mentoring_description"
					class="form-control"
					placeholder="Descripción..."
					required
					></textarea>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-task-session') ) !!}
		<div class="row mt-4">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<label for="mentoring-task-session">Acuerdos/Plan de acción</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<input
					class="form-control"
					id="mentoring-task-session"
					name="mentoring-task-session"
					placeholder="Agrega un acuerdo aqui"
					style="width: 100%"
					required
				/>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<ul id="mentoring-task-session-list">
				</ul>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-tags') ) !!}
		<div class="row mt-4">
			<div class="col-md-12 col-sm-12 col-xs-12"><label for="mentoring-tags-title">Información del emprendimiento</label></div>
			<div class="col-md-5 col-sm-12 col-xs-12">
				<input
					class="form-control"
					id="mentoring-tags-title"
					name="mentoring-tags-title"
					type="text"
					placeholder="Agrega un titulo aqui"
					value=""
					required
					/>
			</div>
			<div class="col-md-5 col-sm-12 col-xs-12">
				<input
					class="form-control"
					id="mentoring-tags-value"
					name="mentoring-tags-value"
					type="number"
					max="100"
					placeholder="Agrega un valor aqui"
					value=""
					required
					/>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
			<span class="col-md-12 col-sm-12 col-xs-12 ranges">Indique mínimo el número de empleados y ventas promedio mensuales para cada sesión.</span>
			<div class="col-md-12 col-sm-12 col-xs-12 hidden">
				<ul id="mentoring-tags-session-list">
				</ul>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-objetive-general') ) !!}
		<div class="row mt-4">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentoring-objetive-general">Observaciones</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<textarea
					class="form-control"
					id="mentoring-objetive-general"
					name="mentoring-objetive-general"
					placeholder="Agrega tus observaciones aquí..."
					rows="2"
					value=""
					required
					></textarea>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-duration') ) !!}
		<div class="row  mt-4">
			<div class="col-md-12 col-sm-12 col-xs-12"><label for="mentoring-duration-hour-value">Duración de la sesión</label></div>
			<div class="col-md-5 col-sm-12 col-xs-12">
				<input
					class="form-control"
					id="mentoring-duration-hour-value"
					max="99"
					min="0"
					name="mentoring-duration-hour-value"
					type="number"
					placeholder="Horas"
					value=""
					required
				/>
				<span class="col-md-12 col-sm-12 col-xs-12 ranges">Horas</span>
			</div>
			<div class="col-md-5 col-sm-12 col-xs-12">
				<input
					class="form-control"
					id="mentoring-duration-minutes-value"
					max="99"
					min="0"
					name="mentoring-duration-minutes-value"
					type="number"
					placeholder="Minutos"
					value=""
					required
				/>
				<span class="col-md-12 col-sm-12 col-xs-12 ranges">Minutos</span>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-phases') ) !!}
		<div class="row mt-4">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentoring-phase">Fase del Proceso</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<select
                    id="mentoring-phase"
                    class="form-control"
					name="mentoring-phase"
					style="width: 100%"
					required
                    {{-- <%- finished || estado === window._tp.getState['closed'] || estado === window._tp.getState['pending_accept']  ? 'readonly' : null %> --}}
                    >
                    <option value=""> Selecciona</option>
                    <option value="promoción">Promoción</option>
                    <option
                        value="orientación">Orientación
                    </option>
                    <option
                        value="diagnóstico">Diagnóstico
                    </option>
                    <option
                        value="seguimiento">Seguimiento
                    </option>
                    <option
                        value="prestación de servicio">Prestación de servicio
                    </option>
                </select>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-diagnosis') ) !!}
		<div class="row mt-4 hidden" id="diagnosis-row">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentoring-diagnosis">Diagnóstico</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<select
                    id="mentoring-diagnosis"
                    class="form-control"
                    name="mentoring-diagnosis"
					required
                    {{-- <%- finished || estado === window._tp.getState['closed'] || estado === window._tp.getState['pending_accept']  ? 'readonly' : null %> --}}
                    >
					<option value="" selected>Selecciona</option>
                    <option
                        value="Contexto del emprendimiento">Contexto del emprendimiento
                    </option>
                    <option
                        value="Brechas identificadas">Brechas identificadas
                    </option>
                    <option
                        value="Brechas priorizadas">Brechas priorizadas
                    </option>
                    <option
                        value="Aspectos adicionales">Aspectos adicionales
                    </option>
                </select>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-recommends') ) !!}
		<div class="row mt-4">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentoring-recommends">¿Se recomienda para la siguiente etapa?</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<select
					id="mentoring-recommends"
					class="form-control"
					name="mentoring-recommends"
					style="width: 100%"
					required
					>
					<option value="" selectec> Selecciona</option>
					<option
						value="Finalizó y se propone siguiente etapa">Finalizó y se propone siguiente etapa
					</option>
					<option
						value="Finalizó, pero no continua">Finalizó, pero no continua
					</option>
					<option
						value="No finalizó">No finalizó
					</option>
					<option
						value="Redireccionado">Redireccionado
					</option>
					<option
						value="Otro">Otro
					</option>
					<option
						value="En acompañamiento">En acompañamiento
					</option>
				</select>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-tracing') ) !!}
		<div class="row mt-4">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentorign-tracing">Seguimiento</label>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<textarea
					id="mentoring-tracing"
					class="form-control"
					name="mentoring-tracing"
					maxlength="255"
					placeholder="Seguimiento..."
					required
					></textarea>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<button
					class="btn btn-default btn-block"
					type="submit">
					<i class="fa fa-plus text-center"></i>
				</button>
			</div>
		</div>
	{!! Form::close() !!}
	
	{{-- form finalización de sesión --}}
	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-finish') ) !!}
		<div class="row mt-4">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentorign-tracing">¿Desea finalizar sesión/sesiones?</label>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<button
					class="btn btn-warning btn-block"
					type="submit">
					<i class="fa fa-calendar-times-o text-center"></i>
					Finalizar
				</button>
			</div>
		</div>
	{!! Form::close() !!}

	{{-- cancelar sesión --}}
	{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-mentoring-cancel') ) !!}
		<div class="row mt-4">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<label for="mentorign-tracing">¿Desea cancelar sesión/sesiones?</label>
			</div>
			{{-- <div class="col-md-10 col-sm-12 col-xs-12">
				<textarea
					id="mentoring-tracing"
					class="form-control"
					name="mentoring-tracing"
					maxlength="255"
					max="255"
					placeholder="Seguimiento..."
					></textarea>
			</div> --}}
			<div class="col-md-12 col-sm-12 col-xs-12">
				<button
					class="btn btn-warning btn-block"
					type="submit">
					<i class="fa fa-times text-center"></i>
					Cancelar
				</button>
			</div>
		</div>
	{!! Form::close() !!}
</script>

{{-- modal confirms messages templates --}}
<script type="text/template" id="confirm-create-sessions-tpl">
	<div>¿Estás seguro de <strong>crear <%- count_asesorias %></strong> sesión/sesiones?</div>
</script>

{{-- modal cargar datos Desarrollo de sesiones --}}
<script type="text/template" id="confirm-development-sessions-tpl">
	<div>¿Estás seguro de cargar <strong><%- field_name %></strong> para el desarrollo de  <strong><%- count_asesorias %></strong> sesión/sesiones?</div>
</script>

{{-- buttons acction --}}
<script type="text/template" id="buttons-action-item-tpl">
	<div class="btn-group tp_btn-group">
			<input
				class="form-control select-one-item"
				type="checkbox"
				id="<%- row.row%>"
				{{-- value="<%- JSON.stringify(full) %>" --}}
			/>
	</div>
</script>
@stop
