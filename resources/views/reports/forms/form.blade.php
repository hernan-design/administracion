@if(isset($groupings) && count($groupings) > 0)
	@foreach ($groupings as $group)
        <table class="rtable table-info">
        	<thead>
				<tr><th class="left">{!! $group->nombre_grupo_pregunta_encuesta !!}</th></tr>
				@if(isset($group->desc_grupo_pregunta_encuesta) && !empty($group->desc_grupo_pregunta_encuesta))
					<tr><td>{!! $group->desc_grupo_pregunta_encuesta !!}</td></tr>
				@endif
			</thead>
		</table>

		@if(isset($questions[$group->codigo_grupo_pregunta_encuesta]))
			@include('reports.forms.questions', [
				'preguntas' => $questions[$group->codigo_grupo_pregunta_encuesta],
				'subpreguntas' => $subquestions
			])
		@endif
	@endforeach
@endif

{{-- Questions without grouping --}}
@if(isset($questions['detached']))
	@include('reports.forms.questions', ['preguntas' => $questions['detached']])
@endif