{{-- multiple question & answer form --}}
<script type="text/template" id="form-multiple-answer-tpl">
	<div class="container-question tile-stats codigo_pregunta_<%= codigo_pregunta_encuesta %>" id="codigo_pregunta_<%= codigo_pregunta_encuesta %>">
		<div class="row">
			<div class="col-md-10 col-sm-10 col-xs-10 question-block-left">
				<h4><label><%- numero_pregunta_encuesta ? numero_pregunta_encuesta +'.' : '' %> <%= texto_pregunta_encuesta %> <% if(!opcional_pregunta_encuesta){ %><span class="required">*</span><% } %></label></h4>
				<% if(texto_opcional_pregunta) { %>
					<p class="text-xmd gray-dark-text thin-text"><%= texto_opcional_pregunta %></p>
					<% } %>
				<% if(tipo_campo_pregunta == window._tp.typeQuestionField['select']) { %>
					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<select name="pregunta_<%- codigo_pregunta_encuesta %>_opcion" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
								<option>Seleccione...</option>
							</select>
						</div>
					</div>
				<% } else { %>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<ul class="list-checked-form no-bullet">
							<% if (!_.isUndefined(opciones) && !_.isEmpty(opciones)) {
									_.each(opciones, function(option) {	
							%>
									<li>
										<div class="checkbox-inline">
											<label class="checkbox-inline">
												<input type="checkbox" name="pregunta_<%- codigo_pregunta_encuesta %>_opcion_<%- option.codigo_opcion_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_<%- option.codigo_opcion_encuesta %>" class="flat">
												<label for="pregunta_<%- codigo_pregunta_encuesta %>_opcion_<%- option.codigo_opcion_encuesta %>"><%- option.texto_opcion_encuesta %></label>
											</label>
										</div>
									</li>
							<% 
									})
								} else if (!_.isUndefined(options) && !_.isEmpty(options)) {

									_.each(options, function(option) {	
							%>
									<li>
										<div class="checkbox-inline">
											<label class="checkbox-inline">
												<input type="checkbox" name="pregunta_<%- codigo_pregunta_encuesta %>_opcion_<%- option.codigo_opcion_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_<%- option.codigo_opcion_encuesta %>" class="flat">
												<label for="pregunta_<%- codigo_pregunta_encuesta %>_opcion_<%- option.codigo_opcion_encuesta %>"><%- option.texto_opcion_encuesta %></label>
											</label>
										</div>
									</li>	
							<% 
									})
								}
							%>
							</ul>
						</div>
					</div>
				<% } %>
				<div class="actions-question text-center">
					{{-- Render id="actions-question-tpl" --}}
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-2 text-center">
				<div id="question-block-options" class="question_options_<%= codigo_pregunta_encuesta %>">
					{{-- Render template id="question-block-options-tpl" --}}
				</div>
			</div>
		</div>
	</div>
</script>
