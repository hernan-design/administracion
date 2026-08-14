
{{-- edit form text field--}}
<script type="text/template" id="edit-form-textarea-field-tpl">
	{!! Form::open( array('action' => null, 'id' => 'form-edit-textarea-field', 'data-parsley-validate' => '') ) !!}
		<div class="row-height">
			<div class="col-lg-10 col-md-10 col-height">
				<textarea name="<%- attrField %>" rows="2" class="edit-field-text form-control" required><%- this.model.get(attrField) %></textarea>
			</div>
			{{-- <div class="col-lg-2 col-md-2 col-height col-bottom">
				{!! Form::submit('Guardar', array('class' => 'btn btn-primary btn-block')) !!}
			</div> --}}
		</div>

	{!! Form::close() !!}
</script>

{{-- edit form type field--}}
<script type="text/template" id="edit-form-type-field-tpl">
	{!! Form::open( array('action' => null, 'class' => 'form-edit-type-field', 'data-parsley-validate' => '') ) !!}
		<div class="col-xs-12">
			<% if(typeField == 'number') { %>
				<input type="number" name="<%- attrField %>" value="<%- this.model.get(attrField) %>" min="0" class="edit-field-text form-control" required>
			<% }else if(typeField == 'datepicker') { %>
				<input type="text" name="<%- attrField %>" value="<%- this.model.get(attrField) %>" class="edit-field-text datepicker" data-inputmask="'mask':'9999-99-99'" required>
			<% }else if(typeField == 'email') { %>
				<input type="email" name="<%- attrField %>" value="<%- this.model.get(attrField) %>" class="edit-field-text form-control" required>
			<% }else { %>
				<input type="text" name="<%- attrField %>" value="<%- this.model.get(attrField) %>" class="edit-field-text form-control" required>
			<% } %>
		</div>
	{!! Form::close() !!}
</script>
