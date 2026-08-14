@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
  <p><b>{{ $code_application }}</b></p>
  <p>Bogotá, D.C, {{ $date }}</p>
  <p>Señor(a)</p>
  <p>
      <b>{{ $name }}</b><br>
      <label>{{ $entity }}</label><br>
      <label>{{ $mail }}</label><br>
      <label>{{ $address }}</label><br>
      <label>{{ $city }}</label>
  </p>
  <br>
  <p><b>Asunto: </b>Notificación resultados programa EMPODERA</p>
  <p>Apreciado(a), {{ $short_name }}</p>
  <p>De antemano queremos agradecerte por permitirnos conocer tu empresa.</p>
  <p class="justify">Una vez evaluada la información consignada en tu postulación al programa EMPODERA te informamos que obtuviste una calificación superior a los 60 puntos mínimos requeridos en la evaluación técnica mencionada en los términos de invitación al programa, por lo cual nos complace informarte que {{ $entity }} ha sido seleccionado para participar en esta versión del programa.</p>
  <p class="justify">Con el fin de continuar con la etapa de evaluación documental, es necesario que adjuntes la documentación relacionada a continuación en el siguiente enlace <a href="{{ $url_application }}">Aquí</a></p>
  <ul>
      <li><b>Certificado de existencia y representación legal, o su equivalente, con vigencia no superior a 30 días, expedido por el órgano correspondiente.</b></li>
      <li><b>Fotocopia de la cédula del Representante Legal o quien haga sus veces.</b></li>
      <li><b>Fotocopia del RUT</b></li>
      <li><b>Fotocopia de la cédula de las personas que conforman el equipo directivo participante.</b></li>
  </ul>
  <p class="justify">Gracias por mostrar interés en ser parte de EMPODERA. ¡Eres el motor de nuestra organización!</p>
  <p class="justify">Cordialmente,</p>
  <b>Equipo ALDEA</b>
  <br>
  Vicepresidencia de Aceleración y Emprendimiento
  <br>
  <p>iNNpulsa Colombia</p>
@stop
