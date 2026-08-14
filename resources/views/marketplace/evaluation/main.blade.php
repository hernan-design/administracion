@extends('marketplace.evaluation.evaluation')

@section('marketplace.evaluation.templates')
	{{-- Detail evaluation view --}}
	<script type="text/template" id="marketplace-evaluation-tpl">

		{!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-create-eval-purchase') ) !!}
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-md-12">
						<dl id="items-purchase-evaluation" class="list-unstyled">

							<% _.each(itemsevaluacion, function(itemeval){
								var answerItem = _.find(answers, function(itemResp){ return itemResp.codigo_item_evaluacion_compra == itemeval.codigo_item_evaluacion_compra; });
							%>
							<dt><%- itemeval.nombre_item_evaluacion_compra %></dt>
							<dd>
								<div class="clearfix">
									<p class="pull-left"><%- itemeval.descripcion_item_eval_compra %></p>
									<div class="pull-right stars-rating stars-30">
										<input type="radio" name="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>" id="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_5" value="5" <%- !_.isUndefined(answerItem) && answerItem.valor_respuesta_eval_compra == 5 ? 'checked' : '' %> required="required" class="check-star">
										<label for="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_5" class="label-terms"><i class="fa fa-star"></i></label>

										<input type="radio" name="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>" id="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_4" value="4" <%- !_.isUndefined(answerItem) && answerItem.valor_respuesta_eval_compra == 4 ? 'checked' : '' %> required="required" class="check-star">
										<label for="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_4" class="label-terms"><i class="fa fa-star"></i></label>

										<input type="radio" name="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>" id="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_3" value="3" <%- !_.isUndefined(answerItem) && answerItem.valor_respuesta_eval_compra == 3 ? 'checked' : '' %> required="required" class="check-star">
										<label for="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_3" class="label-terms"><i class="fa fa-star"></i></label>

										<input type="radio" name="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>" id="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_2" value="2" <%- !_.isUndefined(answerItem) && answerItem.valor_respuesta_eval_compra == 2 ? 'checked' : '' %> required="required" class="check-star">
										<label for="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_2" class="label-terms"><i class="fa fa-star"></i></label>

										<input type="radio" name="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>" id="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_1" value="1" <%- !_.isUndefined(answerItem) && answerItem.valor_respuesta_eval_compra == 1 ? 'checked' : '' %> required="required" class="check-star">
										<label for="evaluacion_item_compra_<%- itemeval.codigo_item_evaluacion_compra %>_1" class="label-terms"><i class="fa fa-star"></i></label>

									</div>
								</div>
							</dd>
							<% }) %>
						</dl>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label for="cometario_evaluacion_compra" data-error="El campo esta vació o tiene muy pocos caracteres">Escribe un comentario (opcional)</label>
						<textarea name="cometario_evaluacion_compra" id="cometario_evaluacion_compra" cols="50" rows="2" required class="form-control" name="message" data-parsley-trigger="keyup" data-parsley-maxlength="200" data-parsley-maxlength-message="200 palabras" ><%- cometario_evaluacion_compra %></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="ln_solid"></div>

		<div class="row">
			<div class="col-md-2 col-md-offset-5">
				<button class="btn btn-block btn-primary" type="submit">Enviar</button>
			</div>
		</div>
		{!! Form::close() !!}

	</script>
@stop
