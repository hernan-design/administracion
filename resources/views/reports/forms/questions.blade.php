@foreach ($preguntas as $key => $question)
	@if(!in_array($question->tipo_campo_pregunta, [App\Models\Core\TipoCampoPregunta::getTypeQuestion('password'), App\Models\Core\TipoCampoPregunta::getTypeQuestion('terms_conditions')]))
		<div @if(!$question->numero_pregunta_encuesta) class="content-info" @endif>{{ $question->numero_pregunta_encuesta ? $question->numero_pregunta_encuesta.'.' : '' }} {!! $question->texto_pregunta_encuesta !!}
			@if($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('open'))
				@php $respuesta = '' @endphp
				@php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}" @endphp
				@php $answer = isset($answers[$key]) ? $answers[$key] : '' @endphp
				@if($answer instanceof App\Models\Core\RespuestaEncuesta)
				    @php $respuesta = $answer->texto_respuesta_encuesta @endphp
				@endif

				{{-- Tipo ciudad --}}
				@if($question->tipo_campo_pregunta == App\Models\Core\TipoCampoPregunta::getTypeQuestion('municipality'))
				    @if($answer instanceof App\Models\Core\RespuestaEncuesta)
				        @php $key = "{$answer->texto_respuesta_encuesta}" @endphp
				        @php $city = isset($cities[$key]) ? $cities[$key] : '' @endphp
				        @if($city instanceof App\Models\Core\Ciudad)
				            @php $respuesta = $city->nombre_ciudad @endphp
				        @endif
				    @endif
				@endif

				{{-- Tipo Sector interes --}}
				@if($question->tipo_campo_pregunta == App\Models\Core\TipoCampoPregunta::getTypeQuestion('ciiu'))
				    @php $respuesta = ($answer instanceof App\Models\Core\RespuestaEncuesta && isset($sectors[$answer->texto_respuesta_encuesta])) ? $sectors[$answer->texto_respuesta_encuesta] : '' @endphp
				@endif

				{{-- Tipo nivel educación --}}
				@if($question->tipo_campo_pregunta == App\Models\Core\TipoCampoPregunta::getTypeQuestion('level_study'))
				    @php $respuesta = ($answer instanceof App\Models\Core\RespuestaEncuesta && isset($levels[$answer->texto_respuesta_encuesta])) ? $levels[$answer->texto_respuesta_encuesta] : '' @endphp
				@endif
				<ul><li class="justify">{{ $respuesta ?: (in_array($question->tipo_campo_pregunta, [App\Models\Core\PreguntaEncuesta::getTypeField('currency'), App\Models\Core\PreguntaEncuesta::getTypeField('integer')]) ? 0 : "&nbsp;") }}</li></ul>
		    @elseif($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('single'))
		        @php $respuesta = ''  @endphp
		        @php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}"  @endphp
		        @php $answer = isset($answers[$key]) ? $answers[$key] : ''  @endphp
		        @php $respuesta = $answer != '' ? $answer->texto_opcion_encuesta : '' @endphp
		        <ul><li class="justify">{{ $respuesta }}</li></ul>
		    @elseif($question->tipo_pregunta_encuesta == App\Models\Core\PreguntaEncuesta::getType('multiple'))
		        @php $respuesta = '' @endphp
		        @php $key = "{$userSurvey->codigo_usuario_encuesta}_{$question->codigo_pregunta_encuesta}" @endphp
		        @php $answerop = isset($answers[$key]) ? $answers[$key] : [] @endphp
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
		        <table class="rtable table-info">
		            <thead>
		                <tr>
		                    <th></th>
		                    @foreach ($matriz["{$question->codigo_pregunta_encuesta}_x"] as $choice)
		                        <th class="center">{{ $choice->texto_opcion_encuesta }}</th>
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
		                            <td class="right">{{ $respuesta }}</td>
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
				
						@include('reports.forms.questions', [
							'preguntas' => $subpreguntas["{$question->codigo_pregunta_encuesta}_{$option->codigo_opcion_encuesta}"],
							'subpreguntas' => $subpreguntas
						])
					@endif
		        @endforeach
			@endif
		@elseif($answer instanceof App\Models\Core\RespuestaEncuesta)
			@if(isset($subpreguntas["{$question->codigo_pregunta_encuesta}_{$answer->codigo_opcion_encuesta}"]) && is_array($subpreguntas["{$question->codigo_pregunta_encuesta}_{$answer->codigo_opcion_encuesta}"]))
				
				@include('reports.forms.questions', [
					'preguntas' => $subpreguntas["{$question->codigo_pregunta_encuesta}_{$answer->codigo_opcion_encuesta}"],
					'subpreguntas' => $subpreguntas
				])
			@endif
		@endif
	@endif
@endforeach
