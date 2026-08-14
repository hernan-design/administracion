@extends('emails.layout')

@section('content')
  <p style="text-align:justify">
    Ha sido aprobada tu solicitud para ser Proveedor en la plataforma DINAMIZA, en la siguiente acción podrás ingresar al módulo de creación de servicios.
  </p>
  <div>
    @include('emails.calltoaction', ['label' => 'Crea tus servicios', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
  </div>
@stop
