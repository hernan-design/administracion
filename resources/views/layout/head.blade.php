<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>Administración | {{ session('institution') ? session('institution')->nombre_institucion : 'TuProyecto' }}</title>

<link rel="shortcut icon" href="{{ (session('metatags') && isset(session('metatags')->favicon)) ? session('metatags')->favicon : 'https://static.tuproyecto.com/images/institutions/favicon.ico.png' }}" />
<link rel="icon" type="image/x-icon" href="{{ (session('metatags') && isset(session('metatags')->favicon)) ? session('metatags')->favicon : 'https://static.tuproyecto.com/images/institutions/favicon.ico.png' }}">
<meta name="description" content="{{ session('institution') ? session('institution')->descripcion_institucion : 'Plataforma' }}">


{{-- App css --}}
<link href="{{ elixir('css/all.css') }}" rel="stylesheet">
<link href="{{ elixir('css/app.css') }}" rel="stylesheet">
<!-- Incluir CSS de Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Incluir jQuery (necesario para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {
        $('.unidad-select').select2({
            placeholder: "Seleccione...",
            width: '100%', // Ajustar el ancho al contenedor padre
            allowClear: true // Permitir limpiar la selección
        });
    });
</script>
