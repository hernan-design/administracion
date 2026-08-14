@extends('emails.layout')

@section('content')
  <p style="text-align:justify">
    Ha sido aprobada tu solicitud para ser un mentor en la plataforma DINAMIZA, en la siguiente acción podrás completar tu perfil mentor.
  </p>
  <div>
{{--    @include('emails.calltoaction', ['label' => 'Crea tus servicios', 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])--}}
    @include('emails.calltoaction', ['label' => 'Completa tu perfil mentor', 'action' => "$_inst_url$_url_action_mentor", 'color' => $_inst_color])
  </div>
@stop
