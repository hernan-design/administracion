@foreach ($preguntas as $key => $question)
	{{--  Valida si la pregunta no es de tipo password o tipo terms_conditions.  --}}
	@if(!in_array($question->tipo_campo_pregunta, [App\Models\Core\TipoCampoPregunta::getTypeQuestion('password'), App\Models\Core\TipoCampoPregunta::getTypeQuestion('terms_conditions')]))

		<div @if(!$question->numero_pregunta_encuesta) class="content-info" @endif>

			{{--  Texto de la pregunta.  --}}
			<h6>{{ $question->numero_pregunta_encuesta ? $question->numero_pregunta_encuesta.'.' : '' }} {!! $question->texto_pregunta_encuesta !!}</h6>

			{{--  Valida si la pregunta es tipo abierta.  --}}
			@if($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('open'))
				@php $respuesta = '' @endphp
				@php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}" @endphp
				@php $answer = isset($answers[$key]) ? $answers[$key] : '' @endphp
				@if($answer instanceof App\Models\Core\RespuestaEncuesta)
				    @php $respuesta = $answer->texto_respuesta_encuesta @endphp
				@endif

				{{--  Texto de la respuesta de pregunta tipo abierta.  --}}
				<ul><li class="justify">{{ $respuesta ?: (in_array($question->tipo_campo_pregunta, [App\Models\Core\PreguntaEncuesta::getTypeField('currency'), App\Models\Core\PreguntaEncuesta::getTypeField('integer')]) ? 0 : "&nbsp;") }}</li></ul>

			{{--  Valida si la pregunta es tipo simple.  --}}
			@elseif($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('single'))
		        @php $respuesta = ''  @endphp
		        @php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}"  @endphp
		        @php $answer = isset($answers[$key]) ? $answers[$key] : ''  @endphp
		        @php $respuesta = $answer != '' ? $answer->texto_opcion_encuesta : '' @endphp

				{{--  Texto de la respuesta de pregunta tipo abierta.  --}}
				<ul><li class="justify">{{ $respuesta }}</li></ul>

			{{--  Valida si la pregunta es tipo multiple.  --}}
			@elseif($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('multiple'))
		        @php $respuesta = '' @endphp
		        @php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}" @endphp
		        @php $answerop = isset($answers[$key]) ? $answers[$key] : [] @endphp

				{{--  Texto de la respuesta de pregunta tipo multiple.  --}}
				@if( count($answerop) > 0 )
			        <ul>
			        @foreach ($answerop as $option)
		        		<li class="justify">{{ $option->texto_opcion_encuesta }}</li>
			        @endforeach
					</ul>
				@endif

		    @elseif($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('upload'))
				@php $respuesta = '' @endphp
				@php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}" @endphp
				@php $answer = isset($answers[$key]) ? $answers[$key] : '' @endphp
				@php $respuesta = $answer != '' ? $answer->texto_respuesta_encuesta : '' @endphp
		        <ul><li class="justify"><span class="link">{{ $respuesta }}</span></li></ul>
	    	@elseif($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('matriz'))
		        <table>
		            <thead>
		                <tr>
		                    <th></th>
		                    @foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice)
		                        <th>{{ $choice->texto_opcion_encuesta }}</th>
		                    @endforeach
		                </tr>
		            </thead>
		            <tbody>
		                @foreach ($matriz["{$question->codigo_pregunta_encuesta}_y"] as $choice_y)
		                    <tr>
		                        <th>{{ $choice_y->texto_opcion_encuesta }}</th>
		                        @foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice_x)
		                            @php $respuesta = ''  @endphp
		                            @php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}_x{$choice_x->codigo_opcion_encuesta}_y{$choice_y->codigo_opcion_encuesta}"  @endphp
		                            @php $answer = isset($answers[$key]) ? $answers[$key] : ''  @endphp
		                            @if($answer instanceof App\Models\Core\RespuestaEncuesta)
		                                @php $respuesta = $answer->texto_respuesta_encuesta @endphp
		                            @endif
		                            <td>{{ $respuesta }}</td>
		                        @endforeach
		                    </tr>
		                @endforeach
		            </tbody>
		        </table>
		    @endif
		</div>

		{{-- Recursividad --}}
		@php $answer = isset($answers[$key]) ? $answers[$key] : '' @endphp
	    @if($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('multiple'))
	    	@if( count($answerop) > 0 )
		        @foreach ($answerop as $option)
		        	@if(isset($subpreguntas["{$question->codigo_pregunta_encuesta}_{$option->codigo_opcion_encuesta}"]) && is_array($subpreguntas["{$question->codigo_pregunta_encuesta}_{$option->codigo_opcion_encuesta}"]))
						@include('reports.diagnosis.siinovapdf.questions_1', [
							'preguntas' => $subpreguntas["{$question->codigo_pregunta_encuesta}_{$option->codigo_opcion_encuesta}"],
							'subpreguntas' => $subpreguntas
						])
					@endif
		        @endforeach
			@endif
		@elseif($answer instanceof App\Models\Core\RespuestaEncuesta)
			@if(isset($subpreguntas["{$question->codigo_pregunta_encuesta}_{$answer->codigo_opcion_encuesta}"]) && is_array($subpreguntas["{$question->codigo_pregunta_encuesta}_{$answer->codigo_opcion_encuesta}"]))
				@include('reports.diagnosis.siinovapdf.questions_1', [
					'preguntas' => $subpreguntas["{$question->codigo_pregunta_encuesta}_{$answer->codigo_opcion_encuesta}"],
					'subpreguntas' => $subpreguntas
				])
			@endif
		@endif
	@endif
@endforeach
