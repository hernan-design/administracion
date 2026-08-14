@extends('reports.layout', ['type' => 'PDF', 'title' => isset($title) ? $title: ''])

@section('content')
	<table class="ntable">
		<thead>
			<tr>
				<th>Fecha y hora de postulación: {{ "$data->fecha_registro_convocatoria $data->hora_registro_convocatoria" }}</th>
			</tr>
		</thead>
	</table>

	<br />
	<table class="htable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr><th>DESCRIPCIÓN DEL EMPRENDIMIENTO</th></tr>
		</thead>
		<tbody>
			<tr>
				<td class="justify">
					{!! $data->biografia_usuario !!}
				</td>
			</tr>
		</tbody>
	</table>
	<br /> <br />

	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="5">Fecha de evaluación</th>
				@foreach($data->experts as $expert)
					<th class="size-7 right">{{ "$expert->fecha_resultado_evaluacion $expert->hora_resultado_evaluacion" }}</th>
				@endforeach
			</tr>
			<tr>
				<th colspan="8" class="text-center">TOTALIDAD COMPONENTES EVALUADOS</th>
			</tr>
			<tr>
				<td colspan="3" class="border-left bold">Puntaje total:</td>
				<td class="border-right bold center">{{ round($data->promedio_total) }}%</td>
				<td class="center border-right bold" rowspan="2">TOTAL</td>
				@foreach($data->experts as $expert)
					<td class="center border-right bold" rowspan="2"> {{ strtoupper($expert->nombre_usuario) }}</td>
				@endforeach
			</tr>
			<tr>
				<td colspan="4" class="border-left border-right border-top size-8">Para acceder al siguiente reto el puntaje minimo es 70,0%</td>
			</tr>
	   	</thead>
	   	<tbody>
			@foreach($data->grupos as $group)
				<tr>
					<th class="left" colspan="4">{{ $group->nombre_grupo_item_eval}}</th>
					<th class="center">{{ $group->promedio_total_grupo}}</th>
					@foreach($group->totales as $total)
						<th class="right">{{ $total->valor_experto }}</th>
					@endforeach
			    </tr>
			    @foreach($group->items as $item)
					<tr>
						<td class="left" colspan="5">
							{{ $item->nombre_grup_opc_eval_pregunta }}
						</td>
						@if(isset($item->answers))
							@foreach($item->answers as $answer)
								<td class="right">{{ $answer->valor_experto}}</td>
							@endforeach
						@endif
					</tr>
				@endforeach
			@endforeach
		</tbody>
	</table>
	<br/>

	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="text-center">RECOMENDACIONES SUGERIDAS POR LOS EVALUADORES</th>
			</tr>
		</thead>
		<tbody>
			@foreach($recommendation as $comment)
				<tr>
					<th class="left">{{ strtoupper($comment->nombre_usuario) }} - {{ $comment->nombre_especialidad }}</th>
				</tr>
				<tr>
					<td>{{ $comment->comentario_resultado_evaluacion }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	<br/>

	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="2" class="text-center">COMENTARIOS</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data->grupos as $group)
				<tr>
					<td>{{ $group->nombre_grupo_item_eval }}</td>
					<td>{{ $group->texto_comentario_documentador }}</td>
				</tr>
			@endforeach
			<tr>
				<td>General</td>
				<td>{{ $data->observaciones_registro_fase_usuario}}</td>
			</tr>
		</tbody>
	</table>
	<br/>

	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th colspan="2" class="text-center">TIPO LLAVE DE ACCESO</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="2" class="center">${{ number_format($data->llaves_acceso, 0,'.',',') }}</th>
			</tr>
		</tbody>
	</table>
	<br/>

	<table class="rtable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="text-center">CATEGORIAS DE SERVICIOS ESPECIALIZADOS</th>
			</tr>
		</thead>
		<tbody>
			@foreach($categories as $category)
				@if($category->nombre_cat_padre_producto != '')
					<tr>
						<td>{{ $category->nombre_cat_padre_producto }}</td>
					</tr>
 				@endif
			@endforeach
		</tbody>
	</table>

	@if($data->llaves_acceso)
		<br /> <br />
		<table class="rtable" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td>
						Con esta llave de acceso el empresario podrá adquirir servicios especializados por parte de asesores habilitados previamente por <strong>iNNpulsa Colombia</strong>.
					</td>
				</tr>
			</tbody>
		</table>
	@endif

	<br>
	<table class="ntable">
		<thead>
			<tr>
				<th class="size-8 right">Fecha de impresión: {{ date('Y-m-d H:m:s') }}</th>
			</tr>
			<tr>
				<th class="size-8 right">Usuario: {{ Auth::user()->correo_electronico }}</th>
			</tr>
		</thead>
	</table>
@stop
