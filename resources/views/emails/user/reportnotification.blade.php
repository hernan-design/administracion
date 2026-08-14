@extends('emails.layout')

@section('content')
  <p>Descarga reporte para ver los datos cargados: </p>
  @include('emails.calltoaction', [
                                    'label' => 'Descargar',
                                    'action' => "$_fileurl",
                                    'color' => $_inst_color
                                    ])

@stop
