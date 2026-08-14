@extends('emails.layout')

@section('content')
  <p>Los datos cargados son: </p>
  @foreach($_history as $data)
    <p>{{ $data }}</p>
  @endforeach
@stop
