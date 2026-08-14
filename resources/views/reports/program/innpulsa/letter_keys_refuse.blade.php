@extends('reports.layout', ['type' => 'PDF', 'title' => isset($title) ? $title: ''])

@section('content')
	<p> Bogotá, D.C, {{ TuProyecto::dateFormat(date('Y-m-d'), 'daymonthnameyear') }} </p>
	<br>
	<p>{{ $data->genero == 'Hombre' ? 'Señor' : 'Señora' }}</p>
	<label>{{ $data->nombre_lider }}</label><br>
	<label>{{ $data->nombre_empresa }}</label><br>
	<label>{{ $data->correo_electronico }}</label><br>
	<label>{{ $data->direccion }}</label><br>
	<label>{{ $data->ciudad }} - {{ $data->departamento }}</label><br>
	<p><b>Asunto: </b>Notificación Reto 2 ¡Conversa con Expertos! de ALDEA</p>
	<p>{{ $data->genero == 'Hombre' ? 'Apreciado' : 'Apreciada' }}, {{ $data->nombre_lider }} </p>
	<p class="justify">De antemano queremos agradecerte por permitirnos conocer tu emprendimiento <b>{{ $data->nombre_empresa }}</b>.</p>
	<p class="justify">Después de pasar por el Reto 1 ¡Conozcámonos! y el Reto 2 ¡Conversa con Expertos!, queremos compartirte algunas recomendaciones de nuestra terna de expertos que permitirán fortalecer tu emprendimiento:</p>
	<ul>
		{{-- Recomendations --}}
		{{-- @foreach ($recomendations as $recomendation)
			<li> {{ $recomendation->comentario_resultado_evaluacion }} </li>
		@endforeach --}}

		@foreach ($_comments as $comment)
			<li> {{ $comment->texto_coment_eval_emprendi }} </li>
		@endforeach

	</ul>
	<p class="justify">
		De acuerdo con lo anterior y basados los criterios de evaluación establecidos en los términos de la invitación a participar en el programa ALDEA, queremos informarte que tu emprendimiento obtuvo una calificación inferior a los 70 puntos mínimos requeridos, por lo cual debemos informarte que no has superado el reto 2 ¡Conversa con Experto
	</p>
	<p class="justify">Te invitamos a que ingreses a www.innpulsacolombia.com, en donde encontraras información sobre nuestra oferta de instrumentos disponibles.</p>
	<p>Gracias por hacer parte de ALDEA. ¡Emprendedores de Alto Impacto!</p>
	<p>Cordial saludo,</p><br><br>
	<label>Equipo ALDEA</label><br>
	<label>iNNpulsa Colombia</label>
@stop
