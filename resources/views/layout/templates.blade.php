{{-- browse result count --}}
<script type="text/template" id="browse-result-count-tpl">
	<span>Mostrando registros del <%- from %> al <%- to %> de un total de <%- totalRecords %> registros</span>
</script>

	{{-- Template attachments file excel uploader --}}
<script type="text/template" id="qq-attachments-uploadexcel-template">
	<div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Documentos">
		<div
		class="row"
		style="justify-content: center;"
		>
		<div class="clases borradas">
			<div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file btn btn-success btn-block">
			<div>Selecciona tu archivo</div>
			</div>
		</div>
		</div>
		<div id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
		<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
		<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
		<span>Processing dropped files...</span>
		<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<div class="content-list-attachments scroll-general-v">
		<ul
			class="qq-upload-list-selector qq-upload-list-attachments"
			aria-live="polite"
			aria-relevant="additions removals"
			style="list-style: none;padding: 0;"
		>
			<li
			style="padding: 5px;
					border-style: dotted;
					border-width: 1px;"
			>
			<div class="qq-progress-bar-container-selector">
				<div
				role="progressbar"
				aria-valuenow="0"
				aria-valuemin="0"
				aria-valuemax="100"
				class="qq-progress-bar-selector qq-progress-bar"
				>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 columns">
				<span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
				<img class="qq-thumbnail-selector" qq-max-size="50" qq-server-scale>
				<a class="preview-link" target="_blank">
					<span class="qq-upload-file-selector qq-upload-file"></span>
				</a>
				<span class="qq-upload-size-selector qq-upload-size"></span>

				<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
				<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
				<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
				<button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
				<button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
				<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
				</div>
				<div class="col-md-6 columns">
				<div
					class="row"
					style="font-size: 10px;"
				>
					<div class="col-xs-12  col-sm-4 col-md-6">
						<label class="label-codigo-documento dark-text" for="codigo_documento">
							Documento
						</label>
						<select
							name="codigo_documento"
							id="codigo_documento"
							class="browser-default valid  form-control parsley-success"
							required
							data-placeholder="Tipo"
							placeholder="Tipo">
							<option
								value="17">
								Excel carga usuarios
							</option>
						</select>
					</div>
					<div class="col-xs-12  col-sm-4 col-md-6">
						<label class="label-codigo_privacidad dark-text"  for="codigo_privacidad_contenido">
							Privacidad
						</label>
						<select
							name="codigo_privacidad_contenido"
							id="codigo_privacidad_contenido"
							class="browser-default valid form-control parsley-success"
							required
							data-placeholder="Privacidad"
							placeholder="Privacidad">
							<option
							value="1">
							Solo yo
							</option>
						</select>
					</div>
				</div>
				</div>
			</div>
			</li>
		</ul>
		</div>
	</div>
</script>

	{{-- Template attachments file excel uploader entities and users --}}
<script type="text/template" id="qq-attachments-uploadexcel-entities-users-template">
	<div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Documentos">
		<div
		class="row"
		style="justify-content: center;"
		>
		<div class="clases borradas">
			<div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file btn btn-success btn-block">
			<div>Selecciona tu archivo</div>
			</div>
		</div>
		</div>
		<div id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
		<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
		<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
		<span>Processing dropped files...</span>
		<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<div class="content-list-attachments scroll-general-v">
		<ul
			class="qq-upload-list-selector qq-upload-list-attachments"
			aria-live="polite"
			aria-relevant="additions removals"
			style="list-style: none;padding: 0;"
		>
			<li
			style="padding: 5px;
					border-style: dotted;
					border-width: 1px;"
			>
			<div class="qq-progress-bar-container-selector">
				<div
				role="progressbar"
				aria-valuenow="0"
				aria-valuemin="0"
				aria-valuemax="100"
				class="qq-progress-bar-selector qq-progress-bar"
				>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 columns">
				<span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
				<img class="qq-thumbnail-selector" qq-max-size="50" qq-server-scale>
				<a class="preview-link" target="_blank">
					<span class="qq-upload-file-selector qq-upload-file"></span>
				</a>
				<span class="qq-upload-size-selector qq-upload-size"></span>

				<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
				<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
				<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
				<button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
				<button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
				<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
				</div>
				<div class="col-md-6 columns">
				<div
					class="row"
					style="font-size: 10px;"
				>
					<div class="col-xs-12  col-sm-4 col-md-6">
						<label class="label-codigo-documento dark-text" for="codigo_documento">
							Documento
						</label>
						<select
							name="codigo_documento"
							id="codigo_documento"
							class="browser-default valid  form-control parsley-success"
							required
							data-placeholder="Tipo"
							placeholder="Tipo">
							<option
								value="18">
								Excel cargue entidades y usuarios
							</option>
						</select>
					</div>
					<div class="col-xs-12  col-sm-4 col-md-6">
						<label class="label-codigo_privacidad dark-text"  for="codigo_privacidad_contenido">
							Privacidad
						</label>
						<select
							name="codigo_privacidad_contenido"
							id="codigo_privacidad_contenido"
							class="browser-default valid form-control parsley-success"
							required
							data-placeholder="Privacidad"
							placeholder="Privacidad">
							<option
							value="1">
							Solo yo
							</option>
						</select>
					</div>
				</div>
				</div>
			</div>
			</li>
		</ul>
		</div>
	</div>
</script>

{{-- Pagination template --}}
<script type="text/template" id="pagination-tpl">
	<li class="arrow prev-page">
		<a href="#">Anterior</a>
	</li>
	<% _.each( items, function(item) { %>
		<li class="<%= item.classes %>">
			<a href="#<%= item.indx %>"><%- item.title %></a>
		</li>
	<% }); %>
	<li class="arrow next-page">
		<a href="#">Siguiente</a>
	</li>
</script>

{{-- Bootstrap-wysiwyg template --}}
<script type="text/template" id="bootstrap-wysiwyg-tpl">
    <div id="alerts"></div>
        <div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#editor">
            <div class="btn-group">
                <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font"><i class="fa fa-font"></i><b class="caret"></b></a>
                <ul class="dropdown-menu"></ul>
            </div>
            <div class="btn-group">
                <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                      <a data-edit="fontSize 5">
                        <p style="font-size:17px">Huge</p>
                      </a>
                    </li>
                    <li>
                      <a data-edit="fontSize 3">
                        <p style="font-size:14px">Normal</p>
                      </a>
                    </li>
                    <li>
                      <a data-edit="fontSize 1">
                        <p style="font-size:11px">Small</p>
                      </a>
                    </li>
                </ul>
            </div>

            <div class="btn-group">
				<a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
				<a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
				<a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
				<a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
            </div>

            <div class="btn-group">
                <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
                <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
                <a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fa fa-dedent"></i></a>
                <a class="btn" data-edit="indent" title="Indent (Tab)"><i class="fa fa-indent"></i></a>
            </div>

            <div class="btn-group">
				<a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
				<a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
				<a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
				<a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
            </div>

            <div class="btn-group">
				<a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
				<div class="dropdown-menu input-append">
					<input class="span2" placeholder="URL" type="text" data-edit="createLink" />
					<button class="btn" type="button">Add</button>
				</div>
				<a class="btn" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-cut"></i></a>
            </div>

            <div class="btn-group">
				<a class="btn" title="Insert picture (or just drag & drop)" id="pictureBtn"><i class="fa fa-picture-o"></i></a>
				<input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage" />
            </div>

            <div class="btn-group">
				<a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
				<a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
            </div>
        </div>
        <div id="editor" class="editor-wrapper"></div>
        <textarea name="descr" id="descr" style="display:none;"></textarea>
    </div>
</script>

{{-- post Discussion template --}}
<script type="text/template" id="discussion-item-tpl">
    {{--  <ul class="list-unstyled msg_list">
        <li>
            <a>
                <span class="image">
                    <img src="<%- imagen_usuario  %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
                </span>
                <span>
                    <span><strong><%- nombre_usuario %></strong></span>
                    <span class="time tp_time-discussion"><%- window.moment([fecha_discusion].join(' '), "YYYY-MM-DD HH:mm").format("MMMM DD YYYY, h:mm a") %></span>
                </span>
                <p class="message">
                    <%= texto_discucion %>
                </span>
                <span class="time">
                    <span href="#" class="show-comment-field" title="Haz un comentario">
                        <i class="fa fa-comment show-comment"><%- contador_comentarios_discucion %></i>
                    </span>
                </span>
            </a>
        </li>
    </ul>  --}}
    <div class="list-unstyled msg_list">
        <div class="right col-md-5 col-sm-6 col-xs-5 text-center tp_content-image-profile">
            <img class="img-circle img-responsive" src="<%- imagen_usuario  %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
        </div>
        <div>
            <span class="text-capitalize"><strong><%- nombre_usuario %></strong></span>
            <p class="time text-muted"><%- window.moment([fecha_discusion].join(' '), "YYYY-MM-DD HH:mm").format("MMMM DD YYYY, h:mm a") %></p>
        </div>
        <p class="message">
            <%= texto_discucion %>
        </p>
        <p class="time">
            <span href="#" class="show-comment-field" title="Haz un comentario">
                <i class="fa fa-comment show-comment"> <%- contador_comentarios_discucion %></i>
            </span>
        </p>
    </div>
    <div class="post-comments-box discussion-comments-box hidesoft">
        @if( Auth::check() )
            <div class="user-comment discussion-user-comment">
                <ul class="post-user-card messages-discussion">
                    <% if(!readonly) { %>
						<li>
	                        <a class="post-avatar">
	                            <img src="{{ Auth::user()->imagen_usuario ? Auth::user()->imagen_usuario :  Config::get('tuproyecto.avatar_image') }}" alt="{{ Auth::user()->nickname_usuario }}" alt="img" class="img-rounded" />
	                        </a>
	                    </li>
					<% } %>
                    <li>
						<% if(!readonly) { %>
                        <div class="comment-post-field textarea-post-discussion">
                            {!! Form::open( array('action' => null, 'data-abide' => '', 'class' => 'form-comment-discussion') ) !!}
                                <div class="row">
                                    <div class="col-xs-9 col-md-10">
                                        <textarea name="texto_comentario_discucion" placeholder="Escribe un comentario" rows="1" class="comment-discussion resizable_textarea form-control" required autofocus></textarea>
                                        <input type="hidden" name="codigo_discucion" value="<%- codigo_discucion %>">
                                    </div>
                                    <div class="col-xs-3 col-md-2">
                                        <button type="submit" class="btn btn-defaul btn-sm">Comentar</button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
						<% } %>
                        <section class="comments-discussion-list hidesoft">
                            {{-- Render user commnet--}}
                        </section>
                    </li>
                </ul>
            </div>
        @endif

    </div>
</script>
{{-- comment discussion template --}}
<script type="text/template" id="comment-discussion-tpl">
    <ul class="list-unstyled msg_list messages-discussion-comment">
        <li>
            <a>
                <span class="image">
                    <img src="<%- imagen_usuario %>" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')" alt="img" />
                </span>
                <span>
                    <span><strong><%- nombre_usuario %></strong></span>
                    <span class="time"><%- window.moment([fecha_comentario, hora_comentario].join(' '), "YYYY-MM-DD HH:mm:ss").format("MMMM DD YYYY, h:mm:ss a") %></span>
                </span>
                <span class="message">
                    <%= texto_comentario %>
                </span>
            </a>
        </li>
    </ul>
</script>

{{-- section Confirm --}}
<script type="text/template" id="project-confirm-remove-tpl">
    <p>Esta seguro </p>
</script>

{{-- Template attachments file uploader --}}
<script type="text/template" id="qq-attachments-template">
	<div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Arrastra un archivo hasta aquí">
		<div class="row">
			<div class="col-sm-12 col-md-4 col-md-offset-4 columns text-center">
				<div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file btn btn-default">
					<div>Selecciona un archivo</div>
				</div>
			</div>
		</div>
		<div id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
			<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Processing dropped files...</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<div class="content-list-attachments scroll-general-v">
			<ul class="qq-upload-list-selector qq-upload-list-attachments list-unstyled" aria-live="polite" aria-relevant="additions removals">
				<li>
					<div class="qq-progress-bar-container-selector">
						<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
							<img class="qq-thumbnail-selector" qq-max-size="45" qq-server-scale>
							<a class="preview-link" target="_blank">
								<span class="qq-upload-file-selector qq-upload-file"></span>
							</a>
							<span class="qq-upload-size-selector qq-upload-size"></span>

							<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
							<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
							<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
							<button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
							<button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
							<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</script>

{{-- Template for image loader --}}
<script type="text/template" id="qq-img-uploader-crop-template">
	<div class="qq-uploader-selector iuc__qq-uploader">
		<div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
			<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<div class="qq-upload-button-selector qq-upload-btn-picture">
			<div>Cambiar imagen</div>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Processing dropped files...</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<ul class="qq-upload-list-selector" aria-live="polite" aria-relevant="additions removals">
			<li></li>
		</ul>
		<input type="hidden" id="crop_type" name="crop_type" />
		<input type="hidden" id="file_name" name="file_name" />
		<input type="hidden" id="file_type" name="file_type" />
		<input type="hidden" class="crop-x" name="x" />
		<input type="hidden" class="crop-y" name="y" />
		<input type="hidden" class="crop-w" name="w" />
		<input type="hidden" class="crop-h" name="h" />

		<dialog class="qq-alert-dialog-selector">
			<div class="qq-dialog-message-selector"></div>
			<div class="qq-dialog-buttons">
				<button class="qq-cancel-button-selector">Close</button>
			</div>
		</dialog>
		<dialog class="qq-confirm-dialog-selector">
			<div class="qq-dialog-message-selector"></div>
			<div class="qq-dialog-buttons">
				<button class="qq-cancel-button-selector">No</button>
				<button class="qq-ok-button-selector">Yes</button>
			</div>
		</dialog>
		<dialog class="qq-prompt-dialog-selector">
			<div class="qq-dialog-message-selector"></div>
			<input type="text">
			<div class="qq-dialog-buttons">
				<button class="qq-cancel-button-selector">Cancel</button>
				<button class="qq-ok-button-selector">Continue</button>
			</div>
		</dialog>
	</div>
</script>

{{-- Template attachments file uploader --}}
<script type="text/template" id="qq-attachments-template">
	<div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Arrastra un archivo hasta aquí">
		<div class="row">
			<div class="col-sm-12 col-md-4 col-md-offset-4 columns text-center">
				<div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file btn btn-default">
					<div>Selecciona un archivo</div>
				</div>
			</div>
		</div>
		<div id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
			<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
		<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
			<span class="qq-upload-drop-area-text-selector"></span>
		</div>
		<span class="qq-drop-processing-selector qq-drop-processing">
			<span>Processing dropped files...</span>
			<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
		</span>
		<div class="content-list-attachments scroll-general-v">
			<ul class="qq-upload-list-selector qq-upload-list-attachments list-unstyled" aria-live="polite" aria-relevant="additions removals">
				<li>
					<div class="qq-progress-bar-container-selector">
						<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
							<img class="qq-thumbnail-selector" qq-max-size="45" qq-server-scale>
							<a class="preview-link" target="_blank">
								<span class="qq-upload-file-selector qq-upload-file"></span>
							</a>
							<span class="qq-upload-size-selector qq-upload-size"></span>

							<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
							<input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
							<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
							<button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
							<button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
							<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</script>

{{-- Template for channel content --}}
<script type="text/template" id="multiselect-channel-content-tpl">
	<div class="row">
		<div class="col-md-12 form-group">
			<div class="x_panel">
				<div class="row">
					<div class="col-md-9 col-sm-9 col-sm-offset-1 col-xs-12">
						<label>Canales</label>
						<select multiple="multiple" class="keepOrder my-select" id="my-select" name="my-select[]" data-route="contenidocanal" data-id="<%- id_contenido %>" data-param="<%- tipo_contenido %>">
							<% _.each(channels, function (option) { %>
								<option value="<%- option.codigo_canal %>" <%- option.comparator ? 'selected' : '' %> ><%- option.nombre_canal %></option>
							<% }); %>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

{{-- Template for activities log --}}
<script type="text/template" id="activities-log-tpl">
	<div class="block_content">
		<%= description %> por <a class="blue" href="<%- window.Misc.urlFull( Route.route('usuario.show', {usuario: codigo_usuario})) %>"><%- nombre_usuario %></a>, <cite><%- moment(created_at).format("MMM DD YYYY, hh:mm a") %></cite>
	</div>
</script>

{{-- Button view more template --}}
<script type="text/template" id="btn-view-more-tpl">
	<div class="btn-activity-view-more">
		<div class="col-xs-12 columns expanded">
			<a href="" class="btn-view-more">Mostrar más</a>
		</div>
	</div>
</script>

{{-- Seo social template --}}
<script type="text/template" id="seo-social-tpl">
	<div class="x_panel">
		{{--  @php dd($object); @endphp  --}}
		{!! Form::open(['route' => null, 'id' => 'form-social-seo', 'class' =>'form-horizontal', 'data-parsley-validate'=>'']) !!}
			<div class="row">
				<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
					<label>Título </label>
					<input type="text" name="nombre_<%- social %>" id="nombre_<%- social %>" class="form-control" value="<%- nombre %>" >
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
					<label>Descripción </label>
					<textarea name="descripcion_<%- social %>" id="descripcion_<%- social %>" class="form-control" data-parsley-maxlength="200" value="<%- descripcion %>"><%- descripcion %></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label>Imagen</label>
					<div class="x_panel">
						{{-- Content crop and uploader image --}}
						<div id="wrap-uploader-tocrop" class="content-area-crop-image">
							<div class="row __fx-main-content">
								<div class="col-xs-12 col-md-6 __fx-column">
									<h5 class="green">Ajustar la imagen:</h5>
									<div class="clearfix">
										<p class="pull-left">Arrastra el cuadrado para cambiar la posición y el tamaño.</p>
										<div id="uploader-social-seo-image" class="uploader-social-seo-img iuc__image-uploader">
											<!-- Render image uploader -->
										</div>
									</div>
									<div id="image-crop-social-seo" class="iuc__content-image-to-crop image-area-drop" iuc__drop-area-text="Arrastra una imagen aquí" style="width:100%">
										<img id="img-social-seo-tocrop" src="<%- imagen %>" class="iuc__image-to-crop" />
									</div>
								</div>

								<div class="col-xs-12 col-md-6 __fx-column">
									<h5 class="green">Vista previa:</h5>
									<p class="">Tamaño real de la imagen (780px X 520px)</p>
									<div id="preview-social-seo-img" class="iuc__content-image-preview" style="width:100%">
										<img id="social-seo-preview-tocrop" src="" class="" />
									</div>
								</div>
							</div>

							<input type="hidden" name="imagen_<%- social %>" value="<%- imagen %>" required>
							<button id="btn-to-crop-social-seo" class="btn btn-primary pull-right"><i class="fa fa-scissors fa-lg fa-fw"></i> Recortar imagen</button>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
					<button type="submit" class="btn btn-success btn-block">Editar</button>
				</div>
			</div>
		{!! Form::close() !!}
	</div>
</script>

{{-- Template create user --}}
<script type="text/template" id="create-user-tpl">
	{!! Form::open(array('route' => null, 'id' => 'form-user', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Nombre <span class="required">*</span></label>
				<input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="" required>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Primer apellido <span class="required">*</span></label>
				<input type="text" name="primer_apellido_persona" id="primer_apellido_persona" class="form-control" value="" required>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Segundo apellido</label>
				<input type="text" name="segundo_apellido_persona" id="segundo_apellido_persona" class="form-control" value="">
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Correo electrónico <span class="required">*</span></label>
				<input type="text" name="correo_electronico" id="correo_electronico" class="form-control" value="" required>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Fecha nacimiento <span class="required">*</span></label>
				<input type="text" name="fecha_nacimiento_usuario" id="fecha_nacimiento_usuario" class="form-control dateDefault" value="" required>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Sexo <span class="required">*</span></label>
				<select type="text" name="genero_persona" id="genero_persona" class="form-control" required>
					<option value="">Seleccione...</option>
					@foreach ( App\Models\Seguridad\Usuario::$gender as $key => $value )
						<option value="{{ $key }}">{{ $value }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Tipo de identificación <span class="required">*</span></label>
				<select type="text" name="tipo_identificacion_usuario" id="tipo_identificacion_usuario" class="form-control" required>
					<option value="">Seleccione...</option>
					@foreach ( App\Models\Core\TipoIdentificacion::identificationType() as $value )
						<option value="{{ $value->codigo_tipo_identificacion }}">{{ $value->nombre_tipo_identificacion }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div id="identification-user">
					{{-- Render template id=identification-user-tpl --}}
				</div>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Celular <span class="required">*</span></label>
				<input type="text" name="movil_usuario" id="movil_usuario" class="form-control" value="" required>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Ciudad/Municipio de residencia <span class="required">*</span></label>
				<select name="codigo_ciudad" id="codigo_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%" required>
				</select>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Localidad</label>
				<select name="codigo_localidad_ciudad" id="codigo_localidad_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.localidades.index') }}"  data-val="#codigo_ciudad" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
				</select>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Barrio</label>
				<select name="codigo_barrio_localidad" id="codigo_barrio_localidad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.localidades.barrios.index') }}" data-val="#codigo_localidad_ciudad" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Nivel de educación</label>
				<select type="text" name="codigo_nivel_educacion" id="codigo_nivel_educacion" class="form-control">
					<option value="">Seleccione...</option>
					@foreach ( App\Models\Core\NivelEducacion::levelsEducation() as $value)
						<option value="{{ $value->codigo_nivel_educacion }}">{{ $value->nombre_nivel_educacion }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Profesión</label>
				<select name="codigo_profesion_persona" id="codigo_profesion_persona" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('usuario.profesion.index') }}" data-param="list" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
				</select>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Ocupación</label>
				<select name="codigo_ocupacion_persona" id="codigo_ocupacion_persona" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('usuario.ocupacion.index') }}"  data-param="true" data-placeholder="Seleccione..." placeholder="Seleccione..." style="width: 100%">
					</select>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
				<button type="submit" class="btn btn-success btn-block"> Crear </button>
			</div>
		</div>
	{!! Form::close() !!}
</script>

{{-- Identification template --}}
<script type="text/template" id="identification-user-tpl">
	<label>Número de identificación <span class="required">*</span></label>
	<input type="text" name="identificacion_usuario" id="identificacion_usuario" class="form-control <%- typeof(classInput) !== 'undefined' ? classInput : '' %> text-right" maxlength="<%- typeof(limit) !== 'undefined' ? limit: '' %>" required>
</script>

 {{-- Template attachments file mentor uploader --}}
  <script type="text/template" id="qq-attachments-mentor-template">
    <div class="qq-uploader-selector qq-uploader qq-uploader-attachments" qq-drop-area-text="Arrastra un archivo hasta aquí">
      <div class="row">
        <div class="small-12 tablet-4 tablet-centered columns">
          <div id="button-upload" class="qq-upload-button-selector qq-upload-btn-file button tiny expand sky-blue btn-shadow radius">
            <div>Selecciona un archivo</div>
          </div>
        </div>
      </div>
      <div  id="bar-progress" class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
      </div>
      <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
        <span class="qq-upload-drop-area-text-selector"></span>
      </div>
      <span class="qq-drop-processing-selector qq-drop-processing">
				<span>Processing dropped files...</span>
				<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
			</span>
      <div class="content-list-attachments scroll-general-v">
        <ul class="qq-upload-list-selector qq-upload-list-attachments uploaded-files-list" aria-live="polite" aria-relevant="additions removals">
          <li>
            <div class="qq-progress-bar-container-selector">
              <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
            </div>
            <div class="row">
              <div class="small-12 columns">
                <span class="qq-upload-spinner-selector qq-upload-spinner-circle"></span>
                <img class="qq-thumbnail-selector" qq-max-size="45" qq-server-scale>
                <a class="preview-link" target="_blank">
                  <span class="qq-upload-file-selector qq-upload-file"></span>
                </a>
                <span class="qq-upload-size-selector qq-upload-size"></span>

                <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                <button class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
                <button class="qq-btn qq-upload-retry-selector qq-upload-retry">Procesar de nuevo</button>
                <button id="clean-upload" class="qq-btn qq-upload-delete-selector qq-upload-delete">Borrar</button>
                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </script>
