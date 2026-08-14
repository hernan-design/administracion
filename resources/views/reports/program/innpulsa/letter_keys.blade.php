@extends('reports.layout', ['type' => 'PDF', 'title' => isset($title) ? $title: ''])
@section('content')
	<p> Bogotá, D.C, {{ TuProyecto::dateFormat(date('Y-m-d'), 'daymonthnameyear') }} </p>
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
	<p class="justify">De acuerdo con lo anterior y basados en los criterios de evaluación establecidos en los términos de la invitación a participar en el programa ALDEA, queremos informarte que tu emprendimiento obtuvo una calificación de <b> {{ $score__challenge_Two }} </b> puntos de los 70 mínimos requeridos, por lo cual ahora haces parte del Reto 3 ¡Conéctate con tu Asesor!, con la asignación de una Llave de Acceso por valor de <b> {{ $keys }} </b> para adquirir servicios en una o varias de las siguientes temáticas con las que podrás potenciar el crecimiento de tu emprendimiento:</p>
	<ul>
		@foreach ($categories as $category)
			@if ($category->selected)
				<li> {{ $category->nombre_categoria_producto }} </li>
			@endif
		@endforeach
	</ul>
	<p class="justify">Ten en cuenta que el acceso a los servicios antes mencionados se encuentra sujeto a que cumplas con los requisitos de vinculación de clientes/ contrapartes, establecidos por Fiducoldex como vocero de la Unidad de Gestión de Crecimiento Empresarial – iNNpulsa Colombia, así como la suscripción  de la carta de compromiso a favor de la Unidad, una vez culminado este proceso, deberás ingresar al marketplace y realizar la celebración del acuerdo de negociación con el asesor de servicios, realizado lo anterior será aprobado por parte de iNNpulsa.</p>
	<p class="justify">En este sentido te estaremos contactando próximamente para informarte sobre los servicios ofrecidos por los diferentes asesores habilitados previamente por iNNpulsa Colombia en las temáticas priorizadas, así como el proceso, requisitos y documentos requeridos para acceder a los mismos.</p>
	<p class="justify">Asimismo, nos estaremos comunicando contigo para que hagas parte de las actividades que se adelantarán desde la comunidad de emprendedores ALDEA.</p>
	<p>Gracias por hacer parte de ALDEA. ¡Emprendedores de Alto Impacto!</p>
	<p class="justify">Cordial saludo,</p>
	<b>Equipo ALDEA</b>
	<br>
	Vicepresidencia de Aceleración y Emprendimiento
	<br>
	<label>iNNpulsa Colombia</label>
@stop
