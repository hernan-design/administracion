<table>
	<thead>
		<tr>
			<th>No.</th>
			<th>Correo Electrónico</th>
			<th>Entidad</th>
			<th>Puntaje Inicial</th>
			<th>Rango</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $item)
			<tr>
				<td>{{ $item->numero_registro_convocatoria }}</td>
				<td>{{ $item->correo_electronico }}</td>
				<td>{{ $item->nombre_usuario }}</td>
				<td>{{ $item->puntaje_total_encuesta }}</td>
				<td>{{ $item->rango_evaluacion }}</td>
			</tr>
		@endforeach
	</tbody>
</table>
