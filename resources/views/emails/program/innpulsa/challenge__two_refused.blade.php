@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
        De antemano queremos agradecerte por permitirnos conocer tu emprendimiento
	</p>
    <p style="text-align:justify">
        <b> {{ $_entity_name }} </b>
    </p>
    <p>
        Después de pasar por el Reto 1 ¡Conozcámonos! y el Reto 2 ¡Conversa con Expertos!, queremos compartirte algunas recomendaciones de nuestra terna de expertos que permitirán fortalecer tu emprendimiento:
    </p>
    <ul>
        @foreach ($_recomendations as $recomendation)
            <li> {{ $recomendation->comentario_resultado_evaluacion }} </li>
        @endforeach
    </ul>
    <p>
        De acuerdo con lo anterior y basados los criterios de evaluación establecidos en los términos de la invitación a participar en el programa ALDEA, queremos informarte que tu emprendimiento obtuvo una calificación inferior a los 70 puntos mínimos requeridos, por lo cual debemos informarte que no has superado el reto 2 ¡Conversa con Expertos!
    </p>
    <p>
        Te invitamos a que ingreses a www.innpulsacolombia.com, en donde encontraras información sobre nuestra oferta de instrumentos disponibles.
    </p>
    <p>
        Gracias por hacer parte de ALDEA. ¡Emprendedores de Alto Impacto!
    </p>
    <p>Cordial saludo,</p>
    <br><br><br>
    <p>Equipo Aldea</p>
    <p>iNNpulsa Colombia</p>
@stop
