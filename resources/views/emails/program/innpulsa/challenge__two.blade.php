@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
        De antemano queremos agradecerte por permitirnos conocer tu emprendimiento
	</p>
    <p style="text-align:justify">
        <b> {{ $_entity_name }} </b>
    </p>
    <p class="justify">
        Después de pasar por el Reto 1 ¡Conozcámonos! y el Reto 2 ¡Conversa con Expertos!, queremos compartirte algunas recomendaciones de nuestra terna de expertos que permitirán fortalecer tu emprendimiento:
    </p>
    <ul>
        @foreach ($_recomendations as $recomendation)
            <li> {{ $recomendation->comentario_resultado_evaluacion }} </li>
        @endforeach
    </ul>
    <p class="justify">
        De acuerdo con lo anterior y basados en los criterios de evaluación establecidos en los términos de la invitación a participar en el programa ALDEA, queremos informarte que tu emprendimiento obtuvo una calificación de <b> {{ $_score }} </b> puntos de los 70 mínimos requeridos, por lo cual ahora haces parte del Reto 3 ¡Conéctate con tu Asesor!, con la asignación de una Llave de Acceso por valor de <b> {{ money_format('%i', $_total_keys) }} </b> para adquirir servicios en una o varias de las siguientes temáticas con las que podrás potenciar el crecimiento de tu emprendimiento:
    </p>
    <ul>
        @foreach ($_categories as $category)
            @if ($category->selected)
                <li> {{ $category->nombre_categoria_producto }} </li>
            @endif
        @endforeach
    </ul>
    <p class="justify">
        Es importante tener en cuenta que el acceso a los servicios antes mencionados se encuentra sujeto a que cumplas con los requisitos de vinculación de clientes/contrapartes, establecidos por Fiducoldex S.A como vocero de iNNpulsa Colombia, así como la suscripción de la carta de compromiso a favor de iNNpulsa Colombia, una vez culminado este proceso, deberás ingresar al marketplace y realizar la celebración del acuerdo de negociación con el asesor de servicios, realizado lo anterior será aprobado por parte de iNNpulsa.
    </p>
    <p class="justify">
        A continuación, encontraras el link donde podrás realizar el cargue de la documentación requerida.
    </p>
    @include('emails.calltoaction', ['label' => 'Carga aquí los documentos', 'action' => "$_inst_url$url_action", 'color' => $_inst_color])
    <p class="justify">
        Gracias por hacer parte de ALDEA. ¡Emprendedores de Alto Impacto!
    </p>
    <p>Cordial saludo,</p>
    <b>Equipo ALDEA</b>
    <br>
    Vicepresidencia de Aceleración y Emprendimiento
    <br>
    <p>iNNpulsa Colombia</p>
@stop
