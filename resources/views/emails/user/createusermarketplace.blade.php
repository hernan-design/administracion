@extends('emails.layout')

@section('content')

  <p>Bienvenido al Marketplace de Compensar.</p>

  <p>Cuentas con un aliado para la promoción y comercialización de tus productos y servicios.</p>

  <p>Tu usuario es: <b> {{ $_to }} </b></p>

  <p>Para activar tu cuenta, debes cambiar tu contraseña, haz click en el siguiente botón:</p>  

  @include('emails.calltoaction', ['label' => 'Cambiar mi contraseña', 'action' => "$_inst_url$_change_password", 'color' => $_inst_color])

  <p>Para ingresar al marketplace ingresa aquí:</p>  

  @include('emails.calltoaction', ['label' => 'Marketplace', 'action' => "$_inst_url$_marketplace", 'color' => $_inst_color])
@stop
