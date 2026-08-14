<!DOCTYPE html>
<html lang="en">
    <head>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        @include('layout.head')
    </head>

    <body class="login">
        <div>
            <div class="login_wrapper">
                <div class="animate form login_form">

                    @if (count($errors) > 0)
                        <div class="alert alert-warning alert-dismissible fade in" role="alert">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif


                    <section class="login_content">
                        <form method="POST" action="{{ route('login') }}" accept-charset="UTF-8" id="form-login-account">
                            {{ csrf_field() }}

                            <h1>Administración</h1>
                            <div>
                                <input type="text" name="correo_electronico" class="form-control input-sm" placeholder="Email" value="{{ old('correo_electronico') }}" required />
                            </div>
                            <div>
                                <input type="password" name="password" class="form-control input-sm" placeholder="Contraseña" required />
                            </div>
                            <div>
                                <button type="submit" class="btn btn-default widget-login-recaptcha" data-key-recaptcha="{{ $institution->clave_sitio_captcha ?: config('tuproyecto.grecatcha_api_key') }}">{{ trans('app.login') }}</button>
                            </div>

                            <div class="separator">
                                <div class="clearfix"></div>
                                <div>
                                    <img src="{{ $institution->logo_institucion }}" alt="{{ $institution->nombre_institucion }}" style="width: 100px;height: auto;"><br>
                                    <p>©2016 Todos los derechos reservados <a href="https://tuproyecto.com" title="TuProyecto.com" target="_blank">TuProyecto</a></p>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <script>window.document.url = "{{ URL::to('/') }}";</script>
        <script>window.document.platform = "{{ URL::to($institution->url_institucion) }}";</script>
        {{-- Libraries so: underscore, backbone, etc --}}
        <script src="{{ elixir('js/vendor.js') }}"></script>
        {{-- Components --}}
        <script src="{{ elixir('js/component.js') }}"></script>
        {{-- App --}}
        <script src="{{ elixir('js/app.js') }}"></script>
    </body>
</html>
