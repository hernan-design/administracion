{{-- views/article/articledetail.blade.php --}}
@extends('article.article')

@section('article.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_content">
				@if(isset($content))
					<div class="x_title title">
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{ route('contenido.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
					</div>
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
						<li role="presentation" class="active"><a href="#tab_content" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Contenido</a>
						</li>
						@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
							<li role="channel"><a href="#tab_channels" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Canales</a>
							</li>
						@endif
						<li role="seo"><a href="#tab_seo" id="seo-tab" role="tab" data-toggle="tab" aria-expanded="true">SEO</a>
						</li>
					</ul>
				@else
					<div class="x_title">
						<h2>Contenido</h2>
						<ul class="nav navbar-right panel_toolbox">
							<li>
								<a href="{{ route('contenido.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
				@endif
				<div id="myTabContent" class="tab-content">
					<div role="tabpanel" class="tab-pane fade active in" id="tab_content" aria-labelledby="home-tab">
						<div class="well">
							{!! Form::open(array('route' => null, 'id' => 'form-article', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Título <span class="required">*</span></label>
										<input type="text" name="nombre_contenido" id="nombre_contenido" class="form-control" value="{{ (isset($content)) ? $content->nombre_contenido : '' }}" required>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Fuente</label>
										<input type="text" name="fuente_contenido" id="fuente_contenido" class="form-control" value="{{ (isset($content)) ? $content->fuente_contenido : '' }}">
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Landing <span class="required">*</span></label>
										<select type="text" name="landing_contenido" id="landing_contenido" class="form-control" required>
											<option value="1" {{ (isset($content)) ? (($content->landing_contenido == 1) ? 'selected' : '') : '' }} >Si</option>
											<option value="0" {{ (isset($content)) ? (($content->landing_contenido == 0) ? 'selected' : '') : 'selected' }} >No</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Privacidad <span class="required">*</span></label>
										<select type="text" name="privacidad_contenido" id="privacidad_contenido" class="form-control" required>
											@foreach ($privacyContent as $value)
												<option value="{{ $value->id }}" {{ (isset($content)) ? ($content->privacidad_contenido == $value->id ? 'selected' : '') :  ($value->id == $public ? 'selected' : '') }}>{{ $value->text }}
												</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12">
										<label>Categoría <span class="required">*</span></label>
										<select type="text" name="codigo_tipo_cont_institucion" id="codigo_tipo_cont_institucion" class="form-control" required>
											<option value="">Seleccione...</option>
												@foreach ($contentTypes as $contentType)
													<option value="{{ $contentType->codigo_tipo_cont_institucion }}" {{  isset($content) ? ($content->codigo_tipo_cont_institucion == $contentType->codigo_tipo_cont_institucion ? 'selected' : '') : '' }}>{{ $contentType->nombre_tipo_contenido }}</option>
												@endforeach
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 col-xs-12">
										<label>Resumen <span class="required">*</span></label>
										<textarea name="resumen_contenido" id="resumen_contenido" class="form-control" data-parsley-maxlength="200" value="{{ (isset($content)) ? $content->resumen_contenido : '' }}" required>{{ (isset($content)) ? $content->resumen_contenido : '' }}</textarea>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 col-xs-12">
										<label>Imagen <span class="required">*</span></label>
										<div class="x_panel">
											{{-- Content crop and uploader image --}}
											<div id="wrap-article-uploader-tocrop" class="content-area-crop-image">
												<div class="row __fx-main-content">
													<div class="col-xs-12 col-md-6 __fx-column">
														<h5 class="green">Ajustar la imagen:</h5>
														<div class="clearfix">
															<p class="pull-left">Arrastra el cuadrado para cambiar la posición y el tamaño.</p>
															<div id="uploader-article-image" class="uploader-article-img iuc__image-uploader">
																<!-- Render image uploader -->
															</div>
														</div>
														<div id="image-crop-article" class="iuc__content-image-to-crop image-area-drop" iuc__drop-area-text="Arrastra una imagen aquí" style="width:100%">
															<img id="img-article-tocrop" src="{{ isset($content) ? $content->imagen_contenido : '' }}" class="iuc__image-to-crop" />
														</div>
													</div>

													<div class="col-xs-12 col-md-6 __fx-column">
														<h5 class="green">Vista previa:</h5>
														<p class="">Tamaño real de la imagen (780px X 520px)</p>
														<div id="preview-article-img" class="iuc__content-image-preview" style="width:100%">
															<img id="article-preview-tocrop" src="" class="" />
														</div>
													</div>
												</div>

												<input type="hidden" name="imagen_contenido" value="{{ isset($content) ? $content->imagen_contenido : '' }}" required>
												<button id="btn-to-crop" class="btn btn-primary pull-right"><i class="fa fa-scissors fa-lg fa-fw"></i> Recortar imagen</button>
											</div>
										</div>
									</div>
								</div>
								<div class="row" style="overflow-x: scroll;">
									<div class="col-md-12 form-group">
										<label>Contenido <span class="required">*</span></label>
										@if (isset($content))
											<div>
												<a
													href="{{ route('contenido.show', ['contenido' => $content->codigo_contenido] ) }}"
													target="_blank"
													title="Ver contenido"><i class="fa fa-eye"></i> Ver
												</a>
											</div>
										@endif
										<div id="CloneDiv" style="display: none;"></div>
                    						<section id="article-project-editor">
											{{-- Render component pulse-editor --}}
											<input type="hidden" name="texto_contenido" value="{{ (isset($content)) ? $content->texto_contenido : '' }}" required>
										</section>

									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
										<button type="submit" class="btn btn-success btn-block">{{ (isset($content)) ? 'Editar' : 'Guardar' }}</button>
									</div>
								</div>
							{!! Form::close() !!}
						</div>
					</div>
					@if( Auth::user()->ability('super-admin,admin', 'view,create,remove', ['module'  => '/contenidocanal']) )
						<div role="tabpanel" class="tab-pane fade" id="tab_channels" aria-labelledby="profile-tab">
							<div id="section-channel-content">
								{{-- Tempalete id=multiselect-channel-content-tpl --}}
							</div>
						</div>
					@endif
					<div role="seo" class="tab-pane fade" id="tab_seo" aria-labelledby="seo-tab">
						<div class="well">
							<div class="row">
								<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
									<label>Título</label>
									<input type="text" class="form-control" value="{{ (isset($content)) ? $content->nombre_contenido : '' }}" readonly>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
									<label>Contenido</label>
									<textarea class="form-control" data-parsley-maxlength="200" value="{{ (isset($content)) ? $content->texto_contenido : '' }}" readonly>{{ (isset($content)) ? strip_tags($content->texto_contenido) : '' }}</textarea>
								</div>
							</div>
							<ul id="seo_social" class="nav nav-tabs bar_tabs" role="tablist">
								<li role="general" class="active"><a href="#tab_general" id="general-tab" role="tab" data-toggle="tab" aria-expanded="true">General</a>
								</li>
								<li role="facebook"><a href="#tab_facebook" id="facebook-tab" role="tab" data-toggle="tab" aria-expanded="true">Facebook</a>
								</li>
								<li role="twitter"><a href="#tab_twitter" id="twitter-tab" role="tab" data-toggle="tab" aria-expanded="true">Twitter</a>
								</li>
							</ul>
							<div id="seo_social" class="tab-content">
								<div role="general" class="tab-pane fade active in" id="tab_general" aria-labelledby="general-tab">
									<div class="x_panel">
										{!! Form::open(array('route' => null, 'id' => 'form-article-seo-general', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Slug</label>
													<input type="text" name="url_contenido" id="url_contenido" class="form-control" value="{{ (isset($content)) ? $content->url_contenido : '' }}" required>
												</div>
												<div class="col-md-1 col-sm-12 col-xs-12 tp_align-btn">
													<button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="" data-original-title="Recuerda que al modificar esta propiedad de contenido puedes perder los enlaces que hayas publicado antes del cambio."><i class="fa fa-exclamation"></i></button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Url canonical</label>
													<input type="text" name="url_canonica" id="url_canonica" class="form-control" value="{{ (isset($content)) ? $content->url_canonica : '' }}">
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<label>Input Tags</label>
													<input name="tags_contenido" id="tags_contenido" value="{{ (isset($content)) ? $content->tags : '' }}" class="form-control ui-autocomplete-input"/>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<div class="checkbox-inline">
														<label class="checkbox-inline">
															<input type="checkbox" name="enlaces_motores_busqueda" {{ isset($content) ? $content->enlaces_motores_busqueda ? 'checked' : '' : '' }} class="flat"> <strong>Enlaces motores de busqueda</strong>
														</label>
													</div>
												</div>
												<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
													<div class="checkbox-inline">
														<label class="checkbox-inline">
															<input type="checkbox" name="indexar_motores" class="flat" {{ isset($content) ? $content->indexar_motores ? 'checked' : '' : '' }}> <strong>Indexar motores</strong>
														</label>
													</div>
												</div>
											</div>
											<br>
										  	<div class="row">
											  	<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
												  	<button type="submit" class="btn btn-success btn-block">{{ (isset($content)) ? 'Editar' : 'Guardar' }}</button>
											  	</div>
										  	</div>
										{!! Form::close() !!}
									</div>
								</div>
								@if(isset($content))
									<div role="facebook" class="tab-pane fade" id="tab_facebook" aria-labelledby="facebook-tab">
										{{-- Render template id="seo-social-tpl" facebook --}}
									</div>
									<div role="twitter" class="tab-pane fade" id="tab_twitter" aria-labelledby="twitter-tab">
										{{-- Render template id="seo-social-tpl" twitter --}}
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

  <script>
    setTimeout(function(){
      $('#article-project-editor').clone().appendTo("#CloneDiv");
      element = document.getElementsByClassName('editor-content-preview x_panel').item(0);
      $("#CloneDiv").remove();
      element1 = document.getElementsByClassName('note-editable').item(0);
      element1.removeChild(element1.children[0]);
      element2 = document.getElementsByClassName('note-editing-area').item(0);
      element2.removeChild(element2.children[0]);
      for (let index = 0; index < element.children.length; index++) {
        cloneElement(element,element1)
      }
    }, 1000);

    function cloneElement(element,element1){
      setTimeout(function(){
        element1.appendChild(element.children[0]);
      }, 200);

    }
  </script>
@stop