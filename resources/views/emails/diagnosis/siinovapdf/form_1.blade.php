@if(isset($groupings) && count($groupings) > 0)
	@foreach ($groupings as $group)
		@if(isset($questions[$group->codigo_grupo_pregunta_encuesta]))
			@include('reports.diagnosis.siinovapdf.questions_1', [
				'preguntas' => $questions[$group->codigo_grupo_pregunta_encuesta],
				'subpreguntas' => $subquestions
			])
		@endif
	@endforeach
@endif