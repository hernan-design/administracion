@extends('reports.layout')

@section('content')
	<table>
		<thead>
			<tr>
				<th>No.</th>
				<th>Entidad</th>
				<th>Nit</th>
				<th>Cédula</th>
				<th>Sector o Industria</th>
				<th>Ciudad/Departamento</th>
				<th>Puntaje Inicial</th>
				<th>Criterio 1 Producto con Diferencial</th>
				<th>Criterio 2 Proyección Comercial</th>
				<th>Criterio 3 Validación Comercial</th>
				<th>Criterio 4 Equipo de Trabajo</th>
				<th>Puntaje Final</th>
				<th>Aprobado Reto 2</th>
				<th>Fecha de registro</th>
				<th>Fecha de Notificación</th>
				<th>Evaluador</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $item)
				<tr>
					<td>{{ $item->numero_registro_convocatoria }}</td>
					<td>{{ $item->nombre_usuario }}</td>
					<td>{{ $item->identificacion_usuario }}</td>
					<td>{{ $item->cedula_usuario }}</td>
					<td>{{ $item->nombre_categoria_proyecto }}</td>
					<td>{{ isset($cities[$item->ciudad_usuario]) ? $cities[$item->ciudad_usuario]: '' }}</td>
					<td>{{ $item->puntaje_reto1 }}</td>
					<td>{{ $item->average_differential }}</td>
					<td>{{ $item->average_commercial }}</td>
					<td>{{ $item->validation_commercial }}</td>
					<td>{{ $item->average_work_team }}</td>
					<td>{{ $item->puntaje_total_eval_emprendi }}</td>
					<td>{{ $item->aprobado_reto12 }}</td>
					<td>{{ $item->fecha_registro_convocatoria }}</td>
					<td>{{ $item->notificacion_cambio_registro_fase }}</td>
					<td>{{ $item->nombre_evaluador }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop