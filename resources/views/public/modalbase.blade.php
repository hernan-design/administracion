{{-- Modal base confirm --}}
<div id="modal-base-confirm-tp" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="inner-title-modal modal-title">Modal Confirm</h4>
            </div>
            <div class="modal-body content-modal">
                {{-- Render Template js --}}
            </div>
          	{{-- Footer modal --}}
    		<div class="modal-footer">
    	        <button type="button" class="btn btn-default cancel-action" data-dismiss="modal">Cancelar</button>
    	        <button type="button" class="btn btn-success confirm-action">Confirmar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- Modal base form --}}
<div id="modal-form" class="modal fade" tabindex="-1"  role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			{!! Form::open(array('route' => null,'id' => 'form-modal-all', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
				<div class="modal-header">
					<button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="inner-title-modal modal-title">Adjuntar</h4>
				</div>
				<div class="modal-body">
          <div class="content-modal"></div>
					{{-- Render Template js --}}
				</div>
				{{-- Footer modal --}}
				<div class="modal-footer">
					<button type="button" class="btn btn-default cancel-action" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-success confirm-action">Enviar</button>
				</div>
			{!! Form::close() !!}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- Modal base search --}}
<div id="modal-form-search" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			{!! Form::open(array('route' => null,'id' => 'form-modal-all', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
				<div class="modal-header">
					<button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="inner-title-modal modal-title">Buscador de usuarios</h4>
				</div>
				<div class="modal-body content-modal">
					{{-- Render Template js --}}
				</div>
			{!! Form::close() !!}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-form-entity" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      {!! Form::open(array('route' => null,'id' => 'form-modal-all', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
      <div class="modal-header">
        <button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
        <h4 class="inner-title-modal modal-title">Buscador de entidades</h4>
      </div>
      <div class="modal-body content-modal">
        {{-- Render Template js --}}
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-form-principal-organizer" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      {!! Form::open(array('route' => null,'id' => 'form-modal-all', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
      <div class="modal-header">
        <button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
        <h4 class="inner-title-modal modal-title">Buscador de organizadores</h4>
      </div>
      <div class="modal-body content-modal">
        {{-- Render Template js --}}
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-form-user-responsable" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      {!! Form::open(array('route' => null,'id' => 'form-modal-all', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
      <div class="modal-header">
        <button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
        <h4 class="inner-title-modal modal-title">Buscador de usuarios</h4>
      </div>
      <div class="modal-body content-modal">
        {{-- Render Template js --}}
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-form-entity-invitation" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      {!! Form::open(array('route' => null,'id' => 'form-modal-all', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
      <div class="modal-header">
        <button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
        <h4 class="inner-title-modal modal-title">Buscador de usuarios por invitar</h4>
      </div>
      <div class="modal-body content-modal">
        {{-- Render Template js --}}
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- Modal base confirm --}}
<div id="modal-base-create" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close cancel-action" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="inner-title-modal modal-title"></h4>
			</div>
			<div class="modal-body content-modal">
				{{-- Render Template js --}}
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
