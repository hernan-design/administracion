@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
	{{-- <h2 class="center">{{ $proyect->nombre_usuario }}</h2> --}}
	@if(!is_null($proyect->imagen_usuario) && !empty($proyect->imagen_usuario))
		<img src="{{ $proyect->imagen_usuario }}" class="img-user">
	@endif
	<p class="justify">{{ $proyect->biografia_usuario }}</p>
	<h3><strong>Industria:</strong> {{ $proyect->nombre_categoria_proyecto }}</h3>
	@if( $proyect->nombre_ciudad != '' )
		<h3>{{ $proyect->nombre_ciudad }}, {{ $proyect->distrito_ciudad }} </h3>
	@endif
	<h2><strong>Creador:</strong></h2>
	<span>{{ $proyect->nombre_founder }}</span><br>
	<span>{{ $proyect->ciudad_founder }}, {{ $proyect->distrito_ciudad_founder }}</span>
	<div class="page_break"></div>
	<h2 class="center">RESULTADO</h2>
	{!! $report->data_general->texto_carta !!}
	<div class="page_break"></div>
	<h2 class="center">DIAGNÓSTICO</h2>

	<img src="{{ $graph }}" style="width: 710px; height: 300px; border: none">
	@if(isset($report->data_general->texto_respuesta) && !is_null($report->data_general->texto_respuesta) && $report->data_general->texto_respuesta != '' )
		<h2>Recomendación general</h2>
		<p class="justify">{{ strip_tags($report->data_general->texto_respuesta) }}</p>
	@endif

	<h2>Gestión administrativa</h2>
	<p class="justify">{{ strip_tags($report->data_admin_management->texto_respuesta) }}</p>
	<h2>Gestión humana y cultura de la innovación</h2>
	<p class="justify">{{ strip_tags($report->data_human_management->texto_respuesta) }}</p>
	<h2>Gestión financiera</h2>
	<p class="justify">{{ strip_tags($report->data_financial_management->texto_respuesta) }}</p>
	<h2>Gestión comercial</h2>
	<p class="justify">{{ strip_tags($report->data_commercial_management->texto_respuesta) }}</p>
	<h2>Gestión de la producción</h2>
	<p class="justify">{{ strip_tags($report->data_production_management->texto_respuesta) }}</p>
	<br>
	<h2 class="center">OPORTUNIDADES</h2>
	<h2>Cursos recomendados</h2>
	<p class="justify">Te invitamos a desarrollar los siguientes cursos para que fortalezcas tu negocio en las áreas identificadas en la sección de Diagnóstico.</p>

	<table style="width: 100%">
		@foreach ($report->data_general->oportunidades as $value)
			<tr>
				<td>
					<img src="{{ $value["imagen"] }}" style="width: 180px; height: 130px"></img>
				</td>
				<td>
					<h3>{{ $value["titulo"] }}</h3>
					<p class="justify">{{ $value["descripcion"] != '' ? $value["descripcion"] : '' }}</p>
				</td>
			</tr>
		@endforeach
	</table>

	<div class="page_break"></div>
	<h2 class="center">{{ $survey->nombre_encuesta }}</h2>
	@include('reports.forms.form', compact('survey', 'groupings', 'questions', 'subquestions'))
@stop
