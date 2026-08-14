@extends('evaluation.evaluation')

@section('evaluation.content')

    {{-- views/form/create-edit.blade.php --}}
    {{-- detail evaluation --}}
    <script type="text/template" id="evaluation-create-tpl">
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content content-x">
				<div class="x_title">
					<h2>Crear Evaluación</h2>
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
	              	</ul>
                <div id="crearEvaluacionFormulario" class="tab-content">
					<div role="tabpanel" class="tab-pane fade active in" id="tab_form-questions" aria-labelledby="form-questions-tab">
						<!-- start general form -->
            {!! Form::open(array('route' => null, 'id' => 'form-evaluation', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
						<div class="well">
							<div class="row">
								<div class="col-md-4 col-sm-4 col-xs-12">
									<label>Nombre <span class="required">*</span></label>
									<input type="text" name="nombre_evaluacion_emprendimiento" id="nombre_evaluacion_emprendimiento" class="form-control" required>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Fecha <span class="required">*</span></label>
									<input type="date" name="fecha_evaluacion_emprendimiento" id="fecha_evaluacion_emprendimiento" class="form-control" required>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Hora <span class="required">*</span></label>
									<input type="time" name="hora_evaluacion_emprendimiento" id="hora_evaluacion_emprendimiento" class="form-control" required>
								</div>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Descripcion <span class="required">*</span></label>
									<textarea type="text" name="descripcion_evaluacion_emprendimiento" id="descripcion_evaluacion_emprendimiento" class="form-control" data-parsley-maxlength="200"></textarea>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Aceptación terminos evaluación <span class="required">*</span></label>
                  <select name="aceptacion_terminos_evaluacion_emprendimiento" id="aceptacion_terminos_evaluacion_emprendimiento" class="form-control">
                    <option value="1">  Si </option>
                    <option value="0"> No </option>
                  </select>
								</div>
                <div class="col-md-4 col-sm-4 col-xs-12">
									<label>Visibilidad Aplicación <span class="required">*</span></label>
                  <select name="visibilidad_aplicacion_evaluacion_emprendimiento" id="visibilidad_aplicacion_evaluacion_emprendimiento" class="form-control">
                    <option value="1">  Si </option>
                    <option value="0"> No </option>
                  </select>
								</div>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Comentario Obligatorio <span class="required">*</span></label>
                  <select name="comentario_obligatorio_evaluacion_emprendimiento" id="comentario_obligatorio_evaluacion_emprendimiento" class="form-control">
                    <option value="1">  Si </option>
                    <option value="0"> No </option>
                  </select>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Comentario Evaluacion Cerrado <span class="required">*</span></label>
                  <select name="comentario_evaluacion_cerrado_evaluacion_emprendimiento" id="comentario_evaluacion_cerrado_evaluacion_emprendimiento" class="form-control">
                    <option value="1">  Si </option>
                    <option value="0"> No </option>
                  </select>
                </div>
              </div>
              <input type="hidden" id="numberItems" name="total_number_items" value="1">

            </div>
            <div id="item-evaluation-0" style="background: #f9f9f9;">
              <div class="form-group">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Tipo evaluacion del item <span class="required">*</span></label>
                  <select name="tipo_evaluacion_item_0" id="tipo_evaluacion_item_0" class="form-control" required>
                    @foreach ( $tipoEvaluacionItem as $teItem)
                      <option value="{{$teItem->codigo_tipo_evaluacion_item}}"> {{$teItem->nombre_tipo_evaluacion_item}} </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Grupo opcion de pregunta <span class="required">*</span></label>
                  <select name="grup_opc_eval_pregunta_0" id="grup_opc_eval_pregunta_0" class="form-control" required>
                    @foreach ( $codigoGrupOpcEvalPregunta as $cGrupOpcEval)
                      <option value="{{$cGrupOpcEval->codigo_grup_opc_eval_pregunta}}"> {{$cGrupOpcEval->nombre_grup_opc_eval_pregunta}} </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Porcentaje item evaluación</span></label>
                  <input type="number" name="porcentaje_item_eval_emprendi_0" id="porcentaje_item_eval_emprendi_0" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Orden item evaluación</span></label>
                  <input type="number" name="orden_item_eval_emprendi_0" id="orden_item_eval_emprendi_0" class="form-control">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Grupo item <span class="required">*</span></label>
                  <select name="grupo_item_0" id="grupo_item_0" class="form-control">
                    @foreach ( $codigoGrupoItemEval as $cGrupItemEval)
                      <option value="{{$cGrupItemEval->codigo_grupo_item_eval}}"> {{$cGrupItemEval->nombre_grupo_item_eval}} </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <label>Nombre item</span></label>
                  <section id="name-question-help">
                    <input type="text" name="nombre_item_evaluacion_0" id="nombre_item_evaluacion_0" class="form-control" required>
                  </section>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <label>Descripcion item</span></label>
                  <section id="name-question-help">
                    <textarea type="text" name="descripcion_item_evaluacion_0" id="descripcion_item_evaluacion_0" class="form-control" data-parsley-maxlength="200"></textarea>
                  </section>
                </div>
              </div>
              <hr>
              <br>
            </div>
            {{-- Template buttons for question--}}

            <button type="button" class="btn btn-primary" id="agregar_ev">Agregar Tipo Evaluación Item</button>



							<div class="row">
								<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
									<button type="submit" id="createEvaluationButton" class="btn btn-success btn-block">Crear</button>
								</div>
							</div>
							{!! Form::close() !!}
						</div>
						<!-- end general form -->
					</div>

              	</div>
			</div>
		</div>
	</div>
</script>

{{-- Template modal options question --}}
<script type="text/template" id="form-options-question-tpl">
  <div class="form-group">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <label>Nombre Item Evaluacion</span></label


      <section id="name-question">
        {{-- Render component pulse-editor --}}
        <input type="hidden" name="texto_pregunta_encuesta">
      </section>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <label>Descripción Item Evaluación</span></label>
      <section id="name-question-help">
        {{-- Render component pulse-editor --}}
        <input type="hidden" name="texto_opcional_pregunta">
      </section>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <label>Grupo Opcion Evaluación Pregunta</label>
      <select name="tipo_pregunta_encuesta" id="tipo_pregunta_encuesta" class="form-control">
        <option value="0" <%= typeof(clase_pregunta_encuesta) == 'number' ? '' : 'selected' %> >  Seleccione </option>
        <option value="1" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 1 ? 'selected' : '' %> > Pregunta </option>
        <option value="2" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 2 ? 'selected' : '' %> > Subpregunta </option>
      </select>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <label>Código Pregunta Encuesta</label>
      <select name="tipo_pregunta_encuesta" id="tipo_pregunta_encuesta" class="form-control">
        <option value="0" <%= typeof(clase_pregunta_encuesta) == 'number' ? '' : 'selected' %> >  Seleccione </option>
        <option value="1" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 1 ? 'selected' : '' %> > Pregunta </option>
        <option value="2" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 2 ? 'selected' : '' %> > Subpregunta </option>
      </select>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <label>Porcentaje Item Evaluación Emprendimiento</label>
      <input type="text" name="porcentaje_item_eval_emprendi" id="porcentaje_item_eval_emprendi" class="form-control" required>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <label>Orden Item Evaluación Emprendimiento</label>
      <input type="text" name="orden_item_eval_emprendi" id="orden_item_eval_emprendi" class="form-control" required>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <label>Grupo Item Evaluación Emprendimiento</label>
      <select name="tipo_pregunta_encuesta" id="tipo_pregunta_encuesta" class="form-control">
        <option value="0" <%= typeof(clase_pregunta_encuesta) == 'number' ? '' : 'selected' %> >  Seleccione </option>
        <option value="1" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 1 ? 'selected' : '' %> > Pregunta </option>
        <option value="2" <%= typeof(clase_pregunta_encuesta) == 'number' && clase_pregunta_encuesta == 2 ? 'selected' : '' %> > Subpregunta </option>
      </select>
    </div>
  </div>
</script>

@stop
