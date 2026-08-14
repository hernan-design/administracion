@extends('emails.layout')

@section('content')
  <p style="text-align:justify">
    <span> {!! $comment !!} </span>

    @include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Descarga tu certificado aquí'), 'action' => "$_url_action", 'color' => $_inst_color])
  </p>
@stop
