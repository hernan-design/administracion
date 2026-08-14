{{-- views/form/create-edit.blade.php --}}
@extends('form.form')

@section('form.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content content-x">
				<div class="x_title">
					<h2>Formulario</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a href="{{route('formulario.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>

	              	<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
						<li role="form-questions" class="active"><a href="#tab_form-questions" role="tab" id="form-questions-tab" data-toggle="tab" aria-expanded="false">General</a>
						</li>
						<li role="form_versions_platform"><a href="#tab_form_versions_platform" role="tab" id="form_versions-platform-tab" data-toggle="tab" aria-expanded="false">Versiones</a>
						</li>
	              	</ul>
                <div id="myTabContent" class="tab-content">
					<div role="tabpanel" class="tab-pane fade active in" id="tab_form-questions" aria-labelledby="form-questions-tab">
						<!-- start general form -->
						<div class="well">
							{!! Form::open(array('route' => null, 'id' => 'form-survey', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
							<div class="row">
								<div class="col-md-4 col-sm-4 col-xs-12">
									<label>Nombre <span class="required">*</span></label>
									<input type="text" name="nombre_evaluacion_emprendimiento" id="nombre_evaluacion_emprendimiento" class="form-control" value="{{  isset($evaluation) ? $evaluation->nombre_evaluacion_emprendimiento : '' }}" required>
								</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<label>Fecha <span class="required">*</span></label>
									<input type="date" name="fecha_evaluacion_emprendimiento" id="fecha_evaluacion_emprendimiento" class="form-control" value="{{ isset($evaluation) ? $evaluation->fecha_evaluacion_emprendimiento : '' }}" required>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Hora <span class="required">*</span></label>
									<input type="time" name="hora_evaluacion_emprendimiento" id="hora_evaluacion_emprendimiento" class="form-control" value="{{ isset($evaluation) ? $evaluation->hora_evaluacion_emprendimiento : '' }}" required>
								</div>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Descripcion <span class="required">*</span></label>
									<textarea type="text" name="descripcion_evaluacion_emprendimiento" id="descripcion_evaluacion_emprendimiento" class="form-control" data-parsley-maxlength="200" required>{{ isset($evaluation) ? $evaluation->descripcion_evaluacion_emprendimiento : '' }}</textarea>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Aceptación terminos evaluación <span class="required">*</span></label>
                  <select name="aceptacion_terminos_evaluacion_emprendimiento" id="aceptacion_terminos_evaluacion_emprendimiento" class="form-control" required>
                    <option value="1" {{ isset($evaluation) && $evaluation->aceptacion_termnos_evaluacion == 1 ? 'selected' : '' }}>  Si </option>
                    <option value="0" {{ isset($evaluation) && $evaluation->aceptacion_termnos_evaluacion == 0 ? 'selected' : '' }}> No </option>
                  </select>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Visibilidad Aplicación <span class="required">*</span></label>
                  <select name="visibilidad_aplicacion_evaluacion_emprendimiento" id="visibilidad_aplicacion_evaluacion_emprendimiento" class="form-control" required>
                    <option value="1" {{ isset($evaluation) && $evaluation->visibilidad_aplicacion == 1 ? 'selected' : '' }}>  Si </option>
                    <option value="0" {{ isset($evaluation) && $evaluation->visibilidad_aplicacion == 0 ? 'selected' : '' }}> No </option>
                  </select>
								</div>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Comentario Obligatorio <span class="required">*</span></label>
                  <select name="comentario_obligatorio_evaluacion_emprendimiento" id="comentario_obligatorio_evaluacion_emprendimiento" class="form-control" required>
                    <option value="1" {{ isset($evaluation) && $evaluation->comentario_obligatorio_evaluacion == 1 ? 'selected' : '' }}>  Si </option>
                    <option value="0" {{ isset($evaluation) && $evaluation->comentario_obligatorio_evaluacion == 0 ? 'selected' : '' }}> No </option>
                  </select>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Comentario Evaluacion Cerrado <span class="required">*</span></label>
                  <select name="comentario_evaluacion_cerrado_evaluacion_emprendimiento" id="comentario_evaluacion_cerrado_evaluacion_emprendimiento" class="form-control" required>
                    <option value="1" {{ isset($evaluation) && $evaluation->comentario_evaluacion_cerrado == 1 ? 'selected' : '' }}>  Si </option>
                    <option value="0" {{ isset($evaluation) && $evaluation->comentario_evaluacion_cerrado == 0 ? 'selected' : '' }}> No </option>
                  </select>
                </div>
                <input type="hidden" id="codigo_usuario" name="codigo_usuario" value="{{$evaluation->codigo_usuario}}">
              </div>
              <hr>
              <br>
              <input type="hidden" id="numberItems" name="total_number_items" value="{{count($itemEvaluacion)}}">
              @for($i = 0; $i < count($itemEvaluacion); $i++)
              <div id="item-evaluation-{{$i}}" style="background: #f9f9f9;">
                <input type="hidden" id="codigo_item_evaluacion_{{$i}}" name="codigo_item_evaluacion_{{$i}}" value="{{$itemEvaluacion[$i]->codigo_item_evaluacion}}">
                <div class="form-group">
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label>Tipo evaluacion del item <span class="required">*</span></label>
                    <select name="tipo_evaluacion_item_{{$i}}" id="tipo_evaluacion_item_{{$i}}" class="form-control" required>
                      @foreach ( $tipoEvaluacionItem as $teItem)
                        <option value="{{$teItem->codigo_tipo_evaluacion_item}}" {{ isset($itemEvaluacion[$i]) && $itemEvaluacion[$i]->codigo_tipo_evaluacion_item == $teItem->codigo_tipo_evaluacion_item ? 'selected' : '' }}> {{$teItem->nombre_tipo_evaluacion_item}} </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label>Grupo opcion de pregunta <span class="required">*</span></label>
                    <select name="grup_opc_eval_pregunta_{{$i}}" id="grup_opc_eval_pregunta_{{$i}}" class="form-control" required>
                      @foreach ( $codigoGrupOpcEvalPregunta as $cGrupOpcEval)
                        <option value="{{$cGrupOpcEval->codigo_grup_opc_eval_pregunta}}" {{ isset($itemEvaluacion[$i]) && $itemEvaluacion[$i]->codigo_tipo_evaluacion_item == $cGrupOpcEval->codigo_grup_opc_eval_pregunta ? 'selected' : '' }}> {{$cGrupOpcEval->nombre_grup_opc_eval_pregunta}} </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label>Porcentaje item evaluación</span></label>
                    <input type="number" name="porcentaje_item_eval_emprendi_{{$i}}" id="porcentaje_item_eval_emprendi_{{$i}}" class="form-control" value="{{$itemEvaluacion[$i]->porcentaje_item_eval_emprendi}}" >
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label>Orden item evaluación</span></label>
                    <input type="number" name="orden_item_eval_emprendi_{{$i}}" id="orden_item_eval_emprendi_{{$i}}" class="form-control" value="{{$itemEvaluacion[$i]->orden_item_eval_emprendi}}">
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <label>Grupo item <span class="required">*</span></label>
                    <select name="grupo_item_{{$i}}" id="grupo_item_{{$i}}" class="form-control">
                      @foreach ( $codigoGrupoItemEval as $cGrupItemEval)
                        <option value="{{$cGrupItemEval->codigo_grupo_item_eval}}" {{ isset($itemEvaluacion[$i]) && $itemEvaluacion[$i]->codigo_grupo_item_eval == $cGrupItemEval->codigo_grupo_item_eval ? 'selected' : '' }}> {{$cGrupItemEval->nombre_grupo_item_eval}} </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <label>Nombre item</span></label>
                    <section id="name-question-help">
                      <input type="text" name="nombre_item_evaluacion_{{$i}}" id="nombre_item_evaluacion_{{$i}}" class="form-control" value="{{$itemEvaluacion[$i]->nombre_item_evaluacion}}"  required>
                    </section>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <label>Descripcion item</span></label>
                    <section id="name-question-help">
                      <textarea type="text" name="descripcion_item_evaluacion_{{$i}}" id="descripcion_item_evaluacion_{{$i}}" class="form-control" data-parsley-maxlength="200">{{$itemEvaluacion[$i]->descripcion_item_evaluacion}}</textarea>
                    </section>
                  </div>
                </div>
                <hr>
                <br>
              </div>

              @endfor

 <!-- Agrega este botón después de la sección "Tipo evaluación del item" -->
 <button type="button" class="btn btn-primary" id="agregar_ev">Agregar Tipo Evaluación Item</button>
              <br>
							<div class="row">
								<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
									<button type="submit" class="btn btn-success btn-block">{{ isset($form) ? 'Editar' : 'Guardar' }}</button>
								</div>
							</div>
							{!! Form::close() !!}
						</div>

						<!-- end general form -->
					</div>
					<div role="tabpanel" class="tab-pane fade" id="tab_form_versions_platform" aria-labelledby="form_versions-platform-tab">
						<div class="row">
							<div class="col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-12">
								<div id="form-versions-content">
									{{-- Render template id="form-versions-tpl" --}}
								</div>
							</div>
						</div>
						<br>
						{{-- <div class="x_panel"> --}}
						<div class="row">
							<div class="col-md-12">
								<div id="pagination">
                  <div class="content-pages">
									  {{-- Render Template id="form-questions-edit-tpl" --}}
                  </div>
								</div>
							</div>
              <div class="col-md-6 col-md-offset-5 title-pagination">

              </div>
						</div>
						{{-- </div> --}}
					</div>
              	</div>
			</div>
		</div>
	</div>

	{{-- template form pagination --}}
	{{-- <script type="text/template" id="form-pagination-tpl">
		<label>Núm. preguntas por página <span class="required">*</span></label>
		<input type="number" name="paginacion_encuesta" id="paginacion_encuesta" min="1" value="{{ isset($form) ? $form->paginacion_encuesta : ''  }}" class="form-control text-center" required/>
	</script> --}}

	{{-- template form versions --}}
	<script type="text/template" id="form-versions-tpl">
		<div class="row">
			<div class="col-md-10 col-sm-10 col-xs-10">
				<label>Versión <span class="required">*</span></label>
				<select name="codigo_version_encuesta" id="codigo_version_encuesta" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('formulario.version.index') }}" data-initial-value="<%- codigo_version_encuesta %>" data-param="<%- codigo_encuesta %>" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
				</select>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-2">
				<div class="round-button">
					<div class="round-button-circle">
						<a href="" title="Agregar versión" class="round-button">
							<i class="fa fa-plus"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</script>

	{{-- Template section option comentary form version --}}
	<script type="text/template" id="form-modal-tpl">
		<label>Comentario versión <span class="required">*</span></label>
		<input type="text" name="comentario_version_encuesta" id="comentario_version_encuesta" class="form-control" value="" required>
		<p><strong>Al crear una nueva versión se inhabilitará la versión actual, si desea continuar haga clic en Enviar, de lo contrario haga clic en Cancelar.</strong></p>
	</script>

	{{-- Template confirm change form version --}}
	<script type="text/template" id="form-modal-change-version-tpl">
		<p>¿Realmente desea cambiar de versión?</p>
	</script>

	{{-- Template modal add question group --}}
	<script type="text/template" id="form-modal-group-tpl">
		<label>Título <span class="required">*</span></label>
		<section id="title-question-group">
			{{-- Render component pulse-editor --}}
			<input type="hidden" name="nombre_grupo_pregunta_encuesta" required>
		</section>
		<label>Descripción</span></label>
		<section id="description-question-group">
			{{-- Render component pulse-editor --}}
			<input type="hidden" name="desc_grupo_pregunta_encuesta">
		</section>
	</script>

{{--  Template confirmation remove group --}}
  <script type="text/template" id="confirmation-remove-group-tpl">
    <p> ¿ Estas seguro de eliminar el grupo ? </p>
  </script>

  {{--  Template confirmation remove question --}}
  <script type="text/template" id="confirmation-remove-question-tpl">
    <p> ¿ Estas seguro de eliminar la pregunta ? </p>
  </script>

	{{-- Template section edit questions form --}}
	<script type="text/template" id="form-questions-edit-tpl">
			{{-- Render id="content-pages-tpl" --}}
	</script>

	{{-- Template item page --}}
	<script type="text/template" id="content-pages-tpl">
		<div class="add-page-question-container text-center">
			{{-- Render template id="container-btn-pages-tpl" --}}
      	</div>
		<br>
		<div class="container-pages">
			{{-- Render template id="container-pages-tpl" --}}
		</div>
	</script>

	<script type="text/template" id="container-pages-tpl">
		<div class="x_panel tile-stats">
			<div class="x_title">
                <h2><strong>Página <%- pagina %> </strong></h2>
{{--				<% if(editable){ %>--}}
{{--	                <ul class="nav navbar-right panel_toolbox tp_panel-btnbox">--}}
{{--	                  	<!-- <li><a class="move-page" title="Mover"><i class="fa fa-arrows"></i> </a> -->--}}
{{--	                  	<!-- </li> -->--}}
{{--	                  	<li><a class="remove-page" data-value="<%- pagina_grupo_pregunta %>" title="Eliminar"><i class="fa fa-close"></i> </a>--}}
{{--	                  	</li>--}}
{{--	                </ul>--}}
{{--				<% } %>--}}
                <div class="clearfix"></div>
          	</div>
        <div class="add-item-group" id="page_<%- codigo_page %>">
          <div class="container-group">
            {{-- Render id="container-group-tpl" --}}
          </div>
          <div class="add-actions-container page_<%- codigo_page %> text-center">
            {{-- Render id="actions-question-tpl" --}}
          </div>
        </div>
		</div>
		<div class="add-page-question-container text-center">
			{{-- Render template id="container-btn-pages-tpl" --}}
    </div>
	</script>

	{{-- Template item group --}}
	<script type="text/template" id="container-group-tpl">
		<div class="x_panel tile-stats add-item-question" id="group_<%- codigo_grupo_pregunta_encuesta %>">
			<div class="x_title" id="title_<%- codigo_grupo_pregunta_encuesta %>">
        <h2><strong>{!! '<%= nombre_grupo_pregunta_encuesta %>' !!}</strong></h2>
				<% if(editable){ %>
            <ul class="nav navbar-right panel_toolbox tp_panel-btnbox">
                <li><a class="edit-group" data-group="<%- codigo_grupo_pregunta_encuesta %>" title="Editar"><i class="fa fa-pencil"></i> </a>
                </li>
                <!-- <li><a class="move-group" title="Mover"><i class="fa fa-arrows"></i> </a></li> -->
                <li><a class="remove-group" data-group="<%- codigo_grupo_pregunta_encuesta %>" title="Eliminar"><i class="fa fa-close"></i> </a>
                </li>
            </ul>
				<% } %>
        <div class="clearfix"></div>
      </div>
			<div class="container-questions">
				{{-- Render id="add-item-question-tpl" --}}
			</div>

			<div class="actions-question-container text-center group_<%- codigo_grupo_pregunta_encuesta %>">
				{{-- Render id="actions-question-tpl" --}}
			</div>
		</div>
	</script>

	{{-- Template item question --}}
	{{-- <script type="text/template" id="add-item-question-tpl">
		<section id="question-" class="">
			<div class="tile-stats">
				Brahian
				<header><h2><strong></strong></h2></header>
				<div class="container-question-type">
					{{-- Render id="item-question-tpl" --}}
				{{-- </div> --}}
				<!-- <div class="actions-question-container text-center"> -->
					{{-- Render id="actions-question-tpl" --}}
				<!-- </div> -->
			{{-- </div> --}}
		{{-- </section> --}}
	{{-- </script>  --}}

	{{-- Template buttons for pages an questions --}}
	<script type="text/template" id="container-btn-pages-tpl">
		<div class="btn-group">
			<a class="text-button add-page"><i class="fa fa-file-text"></i> Agregar página</a>
			<!-- <a class="text-button add-question"><i class="fa fa-question-circle"></i> Agregar pregunta</a> -->
		</div>
	</script>

	{{-- Template buttons for goups question and subquestion--}}
	<script type="text/template" id="actions-group-tpl">
    <div class="btn-group-page buttons-for-question">
      <a id="add-group-q" data-page="<%- codigo_page %>" data-numpage="<%- pagina %>" class="text-button add-group"><i class="fa fa-copy"></i> Agregar grupo </a>
      <br><br>
    </div>
	</script>

  {{-- Template buttons for question--}}
  <script type="text/template" id="actions-question-tpl">
    <div class="btn-group-page buttons-for-question">
      <a id="add-question-q" data-group="<%- group %>" data-page="<%- page %>" class="text-button add-question"><i class="fa fa-copy"></i> Agregar pregunta </a>
      <br><br>
    </div>
  </script>

	{{-- Template buttons for question and subquestion --}}
	<script type="text/template" id="actions-sub-question-tpl">
		<div class="buttons-for-sub-question">
			<div class="btn-group">
{{--				<a id="add-question-q" class="text-button add-question" data-group="<%- typeof codigo_grupo_pregunta_encuesta != 'undefined' ? codigo_grupo_pregunta_encuesta : '' %>"><i class="fa fa-question-circle"></i> Agregar pregunta</a>--}}
				<a id="add-subquestion-q" class="text-button add-subquestion question_code_<%= codigo_pregunta_encuesta %>" data-question="<%- typeof codigo_pregunta_encuesta != 'undefined' ? codigo_pregunta_encuesta : '' %>" ><i class="fa fa-question"></i> Agregar subpregunta</a>
			</div>
		</div>
	</script>

	{{-- Template modal options question --}}
	<script type="text/template" id="form-options-question-tpl">
		<div class="form-group">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<label>Nombre</span></label>
				<section id="name-question">
					{{-- Render component pulse-editor --}}
					<input type="hidden" name="texto_pregunta_encuesta">
				</section>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<label>Texto ayuda</span></label>
				<section id="name-question-help">
					{{-- Render component pulse-editor --}}
					<input type="hidden" name="texto_opcional_pregunta">
				</section>
			</div>
		</div>
		<div class="form-group">
      <div class="col-md-4 col-sm-4 col-xs-12">
        <label>Tipo de pregunta</label>
        <select name="tipo_pregunta_encuesta" id="tipo_pregunta_encuesta" class="form-control">
          <option value="0" <%= typeof(clase_pregunta_encuesta) == 'number' ? '' : 'selected' %> >  Seleccione </option>
          <option value="1" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 1 ? 'selected' : '' %> > Pregunta </option>
          <option value="2" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 2 ? 'selected' : '' %> > Subpregunta </option>
        </select>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <label>Tipo de campo</label>
        <div id="slc_type_question">
          {{-- Render template especific of type_question --}}
        </div>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <label>Pregunta padre</label>
        <select name="cor_codigo_pregunta_encuesta" id="cor_codigo_pregunta_encuesta" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('formulario.pregunta.index'))%>" data-initial-value="<%- typeof padre != 'undefined' ? padre : '' %>" data-param="<%- codigo_version_encuesta %>" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
        </select>
      </div>
		</div>
		<div class="form-group">
			<div id="options-question-father">
				{{-- Template id="options-question-father-tpl" --}}
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Periodo de aplicación</label>
				<select type="text" name="periodo_pregunta_encuesta" id="periodo_pregunta_encuesta" class="form-control">
					<option value="">Seleccione...</option>
					@foreach( TuProyecto::getPastYears(3) as $key => $value)
						<option value="{{{ $key }}}" <%- typeof periodo_pregunta_encuesta != 'undefined' ? periodo_pregunta_encuesta == {{{ $key }}} ? 'selected' : '' : '' %>>{{{ $value }}}</option>
					@endforeach
				</select>
			</div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <label>Número pregunta</label>
        <input type="number" name="numero_pregunta_encuesta" id="numero_pregunta_encuesta" class="form-control" min="1" value="<%- typeof numero_pregunta_encuesta != 'undefined' ? numero_pregunta_encuesta : '' %>">
      </div>
		</div>
		<div class="form-group">
      <div class="col-md-4 col-sm-4 col-xs-12">
        <br>
        <div class="checkbox-inline check-father">
          <label class="checkbox-inline">
            <input type="checkbox" name="pregunta_padre_check" class="flat padre_check"> <strong>Pregunta Padre</strong>
          </label>
        </div>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <br>
        <div class="checkbox-inline">
          <label class="checkbox-inline">
            <input type="checkbox" name="puntaje_pregunta_encuesta" class="flat" <%- typeof puntaje_pregunta_encuesta != 'undefined' ? puntaje_pregunta_encuesta ? 'checked' : '' : '' %>> <strong>Pregunta con puntaje</strong>
          </label>
        </div>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <br>
        <div class="checkbox-inline">
          <label class="checkbox-inline">
            <input type="checkbox" name="opcional_pregunta_encuesta" class="flat"> <strong>Pregunta opcional</strong>
          </label>
        </div>
      </div>
    </div>
		<div class="form-group">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="options-question-father">

        </div>
      </div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="type-question-field">
					{{-- Render template id="type-question-field-tpl" --}}
				</div>
			</div>
		</div>
	</script>

	{{-- Template type question field --}}
	<script type="text/template" id="type-question-field-tpl">
		<div class="row">
			<div class="col-md-6 col-sm-12 col-xs-12">
				<% if( id == window._tp.typeQuestionField['singleText'] ){ %>
					<label>Máximo caracteres <small class="required">(opcional)</small></label>
				<% } %>
				<% if( id == window._tp.typeQuestionField['longText'] ){ %>
					<label>Máximo palabras <small class="required">(opcional)</small></label>
				<% } %>
				<input type="number" name="longitud_pregunta_encuesta" id="longitud_pregunta_encuesta" min="0" class="form-control" value="<%- typeof longitud_pregunta_encuesta != 'undefined' ? longitud_pregunta_encuesta : '' %>">
			</div>
		</div>
	</script>

  <script type="text/template" id="slc-type-question-without-father">
    <select class="select2_group form-control q-elements-content" name="tipo_pregunta_encuesta" id="tipo_pregunta_encuesta"  required>
      <option value="">Seleccione...</option>
      @foreach (App\Models\Core\TipoPreguntaEncuesta::getTypesQuestion() as $values)
        <optgroup label="{{{ $values->nombre_tipo_pregunta_encuesta }}}">
          <% if( {{{ $values->tipo_pregunta_encuesta }}} == window._tp.typeQuestionSurvey['single'] || {{{ $values->tipo_pregunta_encuesta }}} == window._tp.typeQuestionSurvey['multiple'] ){ %>
          <option value="{{{ "{$values->tipo_pregunta_encuesta}_" }}}" <%- typeof tipo_pregunta_encuesta != 'undefined' ? tipo_pregunta_encuesta == {{{ $values->tipo_pregunta_encuesta }}} ? 'selected' : '' : '' %>>{{{ $values->nombre_tipo_pregunta_encuesta }}}</option>
          <% }else{ %>
          @foreach ($values->campo_pregunta as $value)
            <option value="{{{ "{$values->tipo_pregunta_encuesta}_{$value->codigo_tipo_campo_pregunta}" }}}" <%- typeof tipo_campo_pregunta != 'undefined' ? tipo_campo_pregunta == {{{ $value->codigo_tipo_campo_pregunta }}} ? 'selected' : '' : '' %>>{{{ $value->nombre_tipo_campo_pregunta }}}</option>
          @endforeach
          <% } %>
        </optgroup>
      @endforeach
    </select>
  </script>

  <script type="text/template" id="slc-type-question-father">
    <select class="select2_group form-control q-elements-content" name="tipo_pregunta_encuesta" id="tipo_pregunta_encuesta"  required>
      <option value="">Seleccione...</option>
      <option value="1_15"> Select (Opciones) </option>
	  <option value="2_"> Cerrada Multiple </option>
	  <option value="3_"> Cerrada Simple </option>
    </select>
  </script>

	{{-- Template type question select --}}
	<script type="text/template" id="type-options-question-tpl">
    <br>
		<fieldset class="well panel-section-form" name="fields_option_question" id="fields_option_question" data-parsley-group="fields_option_question">
			<div class="form-group">
				<div class="row">
					<div class="col-md-7 col-sm-5 col-xs-5">
						<label>Texto opción <span class="required">*</span></label>
						<input type="text" name="texto_opcion_encuesta" id="texto_opcion_encuesta" class="form-control">
					</div>
					<div class="col-md-3 col-sm-3 col-xs-3">
						<label>Puntaje opción</label>
						<input type="text" name="puntaje_opcion_encuesta" id="puntaje_opcion_encuesta" class="form-control">
					</div>
					<div class="col-md-2 col-sm-2 col-xs-2">
						<label>Orden opción</label>
						<input type="number" name="orden_opcion_encuesta" id="orden_opcion_encuesta" class="form-control" min="1">
					</div>
				</div>
			</div>
			<div class="form-group">
				<!-- <div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 pull-right"> -->
						<button type="button" class="btn btn-primary add-option pull-right no-margin" title="Agregar opción">
							<i class="fa fa-plus"></i> Agregar opción
						</button>
					<!-- </div> -->
				<!-- </div> -->
			</div>
			<div class="table-responsive editable">
				<table class="table table-hover" id="field-option"><!-- data table table-striped no-margin-->
					<thead>
						<tr>
							<th class="text-left">Texto opción </th>
							<th class="text-center">Puntaje opción </th>
							<th class="text-center">Número opción </th>
							<th class="text-center">Acción </th>
						</tr>
					</thead>
					<tbody>
						{{-- Render template id="field-option-tpl" --}}
					</tbody>
				</table>
			</div>
		</fieldset>
	</script>

	{{-- Template question matriz --}}
	<script type="text/template" id="type-question-matriz-tpl">
    <br>
		<fieldset class="well panel-section-form" name="fields_option_question" id="fields_option_question" data-parsley-group="fields_option_question">
			<div class="form-group">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-5">
						<label>Tipo <span class="required">*</span></label>
						<select name="tipo_pregunta_matriz" id="tipo_pregunta_matriz" class="form-control">
							<option value="" selected> Seleccione... </option>
							<option value="1"> Fila </option>
							<option value="2"> Columna </option>
						</select>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-5">
						<label>Tipo pregunta </label>
						<select class="select2_group form-control q-elements-contents" name="tipo_pregunta_encuesta_matriz" id="tipo_pregunta_encuesta_matriz" disabled>
							<option value="">Seleccione...</option>
							@foreach (App\Models\Core\TipoPreguntaEncuesta::getTypeQuestionMatriz() as $values)
								<option value="5_{{ $values->codigo_tipo_campo_pregunta }}">{{{ $values->nombre_tipo_campo_pregunta }}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-5">
						<label>Texto opción <span class="required">*</span></label>
						<input type="text" name="texto_opcion_encuesta" id="texto_opcion_encuesta" class="form-control">
					</div>
					<div class="col-md-6 col-sm-2 col-xs-2">
						<label>Orden opción</label>
						<input type="number" name="orden_opcion_encuesta" id="orden_opcion_encuesta" class="form-control" min="1">
					</div>
				</div>
			</div>
			<div class="form-group">
				<!-- <div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 pull-right"> -->
						<button type="button" class="btn btn-primary add-option-matriz pull-right no-margin" title="Agregar opción">
							<i class="fa fa-plus"></i> Agregar opción
						</button>
					<!-- </div> -->
				<!-- </div> -->
			</div>
			<div class="table-responsive editable">
				<table class="table table-hover" id="field-option"><!-- data table table-striped no-margin-->
					<thead>
						<tr>
							<th class="text-left">Texto opción </th>
							<th class="text-center"> Tipo de opción </th>
							<th class="text-center">Número opción </th>
							<th class="text-center">Acción </th>
						</tr>
					</thead>
					<tbody>
						{{-- Render template id="field-option-tpl" --}}
					</tbody>
				</table>
			</div>
		</fieldset>
	</script>

  {{-- Template Options Question father --}}
  <script type="text/template" id="option-question-father-tpl">
    <br>
    <fieldset class="well panel-section-form" name="fields_option_question_father" id="fields_option_question_father" data-parsley-group="fields_option_question_father">
      <div class="table-responsive editable">
        <table class="table table-hover" id="field-option"><!-- data table table-striped no-margin-->
          <thead>
          <tr>
            <th class="text-left">Texto opción </th>
            <th class="text-center">Acción </th>
          </tr>
          </thead>
          <tbody>
          {{-- Render template id="field-option-tpl" --}}
          </tbody>
        </table>
      </div>
    </fieldset>
  </script>

	{{-- Template field option --}}
	<script type="text/template" id="field-option-tpl">
		<td>
			<div>
				<%- texto_opcion_encuesta %>
			</div>
		</td>
		<td>
			<div class="text-center">
				<%- puntaje_opcion_encuesta %>
			</div>
		</td>
		<td>
			<div class="text-center">
				<%- orden_opcion_encuesta %>
			</div>
		</td>
		<td>
			<div class="text-center">
				<div class="btn-group tp_btn-group">
					<a class="dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-ellipsis-h"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-right" role="menu">
						<li>
							<a href="#" class="btn-inactive danger" title="Editar opción" data-option="<%- codigo_opcion_encuesta %>"><i class="fa fa-times"></i> Eliminar
							</a>
						</li>
					</ul>
				</div>
			</div>
		</td>
	</script>

	{{-- Template field option matriz --}}
	<script type="text/template" id="field-option-matriz-tpl">
		<td>
			<div>
				<%- texto_opcion_encuesta %>
			</div>
		</td>
		<td>
			<div class="text-center">
				<%- tipo_pregunta_matriz ? tipo_pregunta_matriz == 1 ? 'Fila' : 'Columna' : '' %>
			</div>
		</td>
		<td>
			<div class="text-center">
				<%- orden_opcion_encuesta %>
			</div>
		</td>
		<td>
			<div class="text-center">
				<div class="btn-group tp_btn-group">
					<a class="dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-ellipsis-h"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-right" role="menu">
						<li>
							<a href="#" class="btn-inactive danger" title="Editar opción" data-option="<%- codigo_opcion_encuesta %>"><i class="fa fa-times"></i> Eliminar
							</a>
						</li>
					</ul>
				</div>
			</div>
		</td>
	</script>


	{{-- Template options question father --}}
	<script type="text/template" id="options-question-father-tpl">
    <br>
    <fieldset class="well panel-section-form" name="fields_option_question_father" id="fields_option_question_father" data-parsley-group="fields_option_question_father">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <label>Opciones padre</label>
          <ul class="list-checked-form no-bullet">
              <% _.each(options, function (option) { %>
              <li>
                <div class="checkbox-inline">
                  <label class="checkbox-inline">
                    <input type="checkbox" name="opciones_padre" id="opciones_padre" class="flat" value="<%- option.codigo_opcion_encuesta %>" <%- option.selected ? 'checked' : '' %> >
                    <label for="opcion_<%- option.codigo_opcion_encuesta %>"><%- option.texto_opcion_encuesta %></label>
                  </label>
                </div>
              </li>
              <% }) %>
          </ul>
      </div>
    </fieldset>
	</script>

	{{-- Template options question father --}}
	<script type="text/template" id="question-block-options-tpl">
		<div class="question-block-right">
			<ul class="nav navbar-right panel_toolbox tp_panel-btnbox">
				<li>
					<a href="#" class="edit-question code_question_<%= codigo_pregunta_encuesta %>" title="Editar pregunta" data-page="<%= codigo_pagina_pregunta_encuesta %>" data-question="<%= codigo_pregunta_encuesta %>" data-group="<%= codigo_grupo_pregunta_encuesta %>"><i class="fa fa-pencil"></i>
					</a>
				</li>
				<li>
					<a href="#" class="inactive delete-question code_question_<%= codigo_pregunta_encuesta %>" title="Eliminar pregunta" data-page="<%= codigo_pagina_pregunta_encuesta %>" data-question="<%= codigo_pregunta_encuesta %>" data-group="<%= codigo_grupo_pregunta_encuesta %>"><i class="fa fa-times"></i>
					</a>
				</li>
			</ul>
		</div>
	</script>

	{{-- =================== type of questions =================== --}}
	{{-- open question & answer survey --}}
	@include('form.questions.openquestion')
	{{-- single question & answer survey --}}
	@include('form.questions.singlequestion')
	{{-- multiple question & answer survey --}}
	@include('form.questions.multiplequestion')
	{{-- uploader question & answer survey --}}
	@include('form.questions.uploaderquestion')
	{{-- Matriz question & answer survey --}}
	@include('form.questions.matrizquestion')

	{{-- =================== type of subquestions =================== --}}
	{{-- open question & answer survey --}}
	@include('form.subquestions.opensubquestion')
	{{-- single question & answer survey --}}
	@include('form.subquestions.singlesubquestion')
	{{-- multiple question & answer survey --}}
	@include('form.subquestions.mutiplesubquestion')
	{{-- uploader question & answer survey --}}
	@include('form.subquestions.uploadersubquestion')
	{{-- Matriz question & answer survey --}}
	@include('form.subquestions.matrizsubquestion')

@stop
