{{-- open question & answer form --}}
<script type="text/template" id="form-open-subquestion-tpl">
	<div class="container-question tile-stats codigo_pregunta_<%= codigo_pregunta_encuesta %>" id="codigo_pregunta_<%= codigo_pregunta_encuesta %>">
		<div class="row">
			<div class="col-md-10 col-sm-10 col-xs-10 question-block-left">
				<h4><label><%- numero_pregunta_encuesta ? numero_pregunta_encuesta +'.' : '' %> <%= texto_pregunta_encuesta %> <% if(!opcional_pregunta_encuesta){ %><span class="required">( Subpregunta ) *</span><% } %></label></h4>
				<% if(texto_opcional_pregunta) { %>
					<p><%= texto_opcional_pregunta %></p>
				<% } %>
				<% if( !tipo_campo_pregunta || !_.contains(['full_year','longText','cities','pass_confirm','agree_terms','date','before_date','number_format', 'ciiu', 'education_level', 'economic_sector', 'currency', 'negative_currency', 'phone', 'integer', 'identification', 'select'], _.findKey(window._tp.typeQuestionField, function(type){return type == tipo_campo_pregunta;})) ) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0"  class="form-control">
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['full_year']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" placeholder="YYYY" class="form-control">
						</div>
					</div>

				<% }else if(tipo_campo_pregunta == window._tp.typeQuestionField['longText']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<textarea name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control"></textarea>
						</div>
					</div>

				<% }else if(tipo_campo_pregunta == window._tp.typeQuestionField['cities']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<select name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
								<option>Seleccione...</option>
							</select>
						</div>
					</div>

				<% }else if(tipo_campo_pregunta == window._tp.typeQuestionField['pass_confirm']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="password" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
							<label for="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" data-error="La clave solo acepta caracteres alfanuméricos sin acentos, ni caracteres extraños.">Contraseña</label>
						</div>
						<div class="col-md-4 col-sm-6 col-xs-12">
							<input type="password" value="" data-equalto="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0"class="form-control">
							<label for="" data-error="Las claves no coinciden">Confirmar contraseña</label>
						</div>
					</div>

				<% }else if(tipo_campo_pregunta == window._tp.typeQuestionField['agree_terms']) { %>

					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 tp_input-type">
							<div class="checkbox-inline">
								<label class="checkbox-inline">
									<input type="checkbox" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="flat">
									<label for="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="label-terms dark-text">
										Términos y condiciones
									</label>
								</label>
							</div>
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['date'] || tipo_campo_pregunta == window._tp.typeQuestionField['before_date']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control" >
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['number_format']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['ciiu']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<select name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
								<option>Seleccione...</option>
							</select>
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['education_level']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<select name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
								<option>Seleccione...</option>
							</select>
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['economic_sector']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<select name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
								<option>Seleccione...</option>
							</select>
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['currency'] || tipo_campo_pregunta == window._tp.typeQuestionField['negative_currency']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['phone']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0"  class="form-control" >
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['integer']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['identification']) { %>

					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<input type="text" name="pregunta_<%- codigo_pregunta_encuesta %>" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0"  class="form-control">
						</div>
					</div>

				<% } else if(tipo_campo_pregunta == window._tp.typeQuestionField['select']) { %>
					<div class="row">
						<div class="col-md-4 col-sm-6 col-xs-12 tp_input-type">
							<select name="pregunta_<%- codigo_pregunta_encuesta %>_opcion" id="pregunta_<%- codigo_pregunta_encuesta %>_opcion_0" class="form-control">
								<option>Seleccione...</option>
							</select>
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
