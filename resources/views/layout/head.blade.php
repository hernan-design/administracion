<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>Administración | {{ session('institution') ? session('institution')->nombre_institucion : 'TuProyecto' }}</title>

<link rel="shortcut icon" href="{{ (session('metatags') && isset(session('metatags')->favicon)) ? session('metatags')->favicon : 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/favicon.ico.png' }}" />
<link rel="icon" type="image/x-icon" href="{{ (session('metatags') && isset(session('metatags')->favicon)) ? session('metatags')->favicon : 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/favicon.ico.png' }}">
<meta name="description" content="{{ session('institution') ? session('institution')->descripcion_institucion : 'Plataforma' }}">


{{-- App css --}}
@viteReactRefresh
@vite(['resources/assets/sass/vendor.scss', 'resources/assets/sass/app.scss'])
<!-- Incluir jQuery (v2.2.3 compatible con Bootstrap 3) -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<!-- Global Variables for Legacy Backbone App -->
<script>
    window.app = window.app || {};
    window.Misc = window.Misc || {};
    window.Route = window.Route || {};
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Wait for Vite modules to execute and populate window.$
        var checkJquery = setInterval(function() {
            if (window.$ && window.$.fn && window.$.fn.select2) {
                clearInterval(checkJquery);
                $('.unidad-select').select2({
                    placeholder: "Seleccione...",
                    width: '100%',
                    allowClear: true
                });
            }
        }, 50);
    });
</script>
