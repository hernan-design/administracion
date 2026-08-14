@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
  <p><b>{{ $code_application }}</b></p>
  <p>Bogotá, D.C, {{ $date }}</p>
  <br>
  <p>Señor(a)</p>
  <p>
      <b>{{ $name }}</b><br>
      <label>{{ $entity }}</label><br>
      <label>{{ $mail }}</label><br>
      <label>{{ $address }}</label><br>
      <label>{{ $city }}</label>
  </p>
  <p><b>Asunto: </b>Notificación resultados programa EMPODERA</p>
  <p>Apreciado(a), {{ $short_name }}</p>
  <p>De antemano queremos agradecerte por permitirnos conocer tu empresa.</p>
  <p class="justify">Una vez evaluada la información consignada en tu postulación al programa EMPODERA, te informamos que tu postulación obtuvo una calificación inferior a los 60 puntos mínimos requeridos en la evaluación técnica de los términos de referencia del programa, por lo cual, debemos informarte que no has superado esta etapa de evaluación. Sin embargo, nos permitimos compartirte las siguientes observaciones y/o recomendaciones del equipo evaluador:</p>
  <ul>
  @foreach ($questions as $item)
    <li><b>{{ $item->description }}</b></li>
  @endforeach
  </ul>
  <p class="justify">Gracias por mostrar interés en ser parte de EMPODERA. ¡Eres el motor de nuestra organización!</p>
  <p>Cordialmente,</p>
  <b>Equipo ALDEA</b>
  <br>
  Vicepresidencia de Aceleración y Emprendimiento
  <br>
  <p>iNNpulsa Colombia</p>
@stop
