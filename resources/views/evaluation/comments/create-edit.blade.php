{{-- views/evaluationcomment/create-edit.blade.php --}}
@extends('evaluation.comments.evaluation')

@section('evaluation.comments.content')

	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content" id="section-content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
          <div class="x_title">
						<h2>Comentario de Evaluación Emprendimiento</h2>
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{route('evaluacion.comentario.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div id="myTabContent" class="tab-content">
						<div role="evaluation_comment" class="tab-pane fade active in" id="tab_evaluation_comment" aria-labelledby="evaluation-comment-tab">
							<div class="well">
								{!! Form::open(array('route'=>'evaluacion.comentario.store', 'id' => 'form-evaluation-comment', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<label>Título <span class="required">*</span></label>
											<input type="text" name="name_coment_eval_emprendi" id="name_coment_eval_emprendi" class="form-control" value="{{ isset($evaluationcomment) ? $evaluationcomment->nombre_convocatoria : '' }}" required>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
											<div id="evaluation-comment">

												{{-- Render select template id="evaluation-comment-tpl" --}}
											</div>
										</div>
                  </div>
									<br>
									<div class="row">
										<div class="col-md-12 col-xs-12">
											<label>Texto <span class="required">*</span></label>
											<textarea name="texto_coment_eval_emprendi" id="texto_coment_eval_emprendi" class="form-control" data-parsley-maxlength="512" value="{{ isset($evaluationcomment) ? $evaluationcomment->texto_coment_eval_emprendi : '' }}" required>{{ isset($evaluationcomment) ? $evaluationcomment->texto_coment_eval_emprendi : '' }}</textarea>
										</div>
									</div>

									<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
										<button type="submit" class="btn btn-success btn-block">{{ (isset($evaluationcomment)) ? 'Editar' : 'Guardar' }}</button>
									</div>
								{!! Form::close() !!}
							</div>
            </div>

            {{-- template evaluation comment --}}
            <script  type="text/template" id="evaluation-comment-tpl">
              <div id="evaluation-section">
                <label>Evaluacion Emprendimiento<span class="required">*</span></label>
                <select name="codigo_evaluacion_emprendimiento" id="codigo_evaluacion_emprendimiento" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('evaluacion.evaluacionescomentarios'))%>" data-placeholder="Seleccione..." placeholder="Seleccione..." data-initial-value="{{ isset($comment) ? $comment->codigo_evaluacion_emprendimiento : '' }}" style="width: 100%" value="{{ isset($comment) ? $comment->codigo_evaluacion_emprendimiento : '' }}" data-param="{{ isset($comment) ? $comment->codigo_evaluacion_emprendimiento : '' }}" required>
                </select>
              </div>
            </script>
@stop
