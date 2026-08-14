<div class="x_panel">
    {{--  @php dd($object); @endphp  --}}
    {!! Form::open(['route' => null, 'id' => 'form-social-seo', 'class' =>'form-horizontal', 'data-parsley-validate'=>'']) !!}
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
                <label>Título </label>
                <input type="text" name="nombre_{{ $social }}" id="nombre_{{ $social }}" class="form-control" value="{{ isset($object) ? $object->{'nombre_'.$social} : '' }}" >
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
                <label>Descripción </label>
                <textarea name="descripcion_{{ $social }}" id="descripcion_{{ $social }}" class="form-control" data-parsley-maxlength="200" value="{{ isset($object) ? $object->{'descripcion_'.$social} : '' }}">{{ isset($object) ? $object->{'descripcion_'.$social} : '' }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Imágen</label>
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
                                    <img id="img-social-seo-tocrop" src="{{ isset($object) ? $object->{'imagen_'.$social} : '' }}" class="iuc__image-to-crop" />
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

                        <input type="hidden" name="imagen_{{ $social }}" value="{{ isset($object) ? $object->{'imagen_'.$social} : '' }}" required>
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