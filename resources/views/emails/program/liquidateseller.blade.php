@extends('emails.layout')

@section('content')
  <p style="text-align:justify">
    <span> {!! $comment !!} </span>
    <br><br>
      @include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Ver resultado'), 'action' => "$_inst_url$_url_action", 'color' => $_inst_color])
  </p>
@stop
