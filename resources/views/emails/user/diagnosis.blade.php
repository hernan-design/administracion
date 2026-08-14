@extends('emails.layout')

@section('content')
  <p>Descarga el reporte para ver el diganóstico obtenido: </p>
  @include('emails.calltoaction', [
                                    'label' => 'Descargar',
                                    'action' => "$_fileurl",
                                    'color' => $_inst_color
                                    ])

@stop
