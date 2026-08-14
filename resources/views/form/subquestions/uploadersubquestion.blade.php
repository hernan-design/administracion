{{-- uploader question & answer form --}}
<script type="text/template" id="form-upload-subquestion-tpl">
	{{-- review answer --}}
	<div class="container-question tile-stats codigo_pregunta_<%= codigo_pregunta_encuesta %>" id="codigo_pregunta_<%= codigo_pregunta_encuesta %>">
		<div class="row">
			<div class="col-md-10 col-sm-10 col-xs-10 question-block-left">
				<h4><label><%- numero_pregunta_encuesta ? numero_pregunta_encuesta +'.' : '' %> <%= texto_pregunta_encuesta %> <% if(!opcional_pregunta_encuesta){ %><span class="required"> ( Subpregunta ) *</span><% } %></label></h4>
				<% if(texto_opcional_pregunta) { %>
					<p class="text-xmd gray-dark-text thin-text"><%= texto_opcional_pregunta %></p>
				<% } %>
				<% if(tipo_campo_pregunta == window._tp.typeQuestionField['image'] || tipo_campo_pregunta == window._tp.typeQuestionField['attachment']) { %>
					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="file" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0"  class="form-control">
						</div>
					</div>
				<% } %>
				<div class="actions-question text-center subquestion_question_<%= codigo_pregunta_encuesta %>">
					{{-- Render id="actions-question-tpl" --}}
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-2 text-center">
				<div id="question-block-options" class="subquestion_options_<%= codigo_pregunta_encuesta %>">
					{{-- Render template id="question-block-options-tpl" --}}
				</div>
			</div>
		</div>
	</div>
</script>
