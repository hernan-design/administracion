@extends('emails.layout')

@section('content')
  <p># Evaluación: @php echo $_id_eval_result @endphp</p>
  <p>Fecha emisión: @php echo date('d-m-Y') @endphp</p>
  <p>Descarga el reporte para ver el diganóstico obtenido.</p>
  @include('emails.calltoaction', [
                                    'label' => 'Descargar',
                                    'action' => "$_fileurl",
                                    'color' => $_inst_color
                                    ])

@stop
