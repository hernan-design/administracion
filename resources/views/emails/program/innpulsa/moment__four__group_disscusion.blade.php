@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
        Recibe un afectuoso saludo de iNNpulsa Colombia y el programa Aldea.
    </p>
    <p style="text-align:justify">
        Gracias por aceptar la invitación para participar del Reto 2: ¡Conversa con Expertos! del programa Aldea de iNNpulsa Colombia, como experto evaluador.
    </p>
    <p style="text-align:justify">
        En el archivo adjunto podrás conocer sobre la dinámica del conversatorio y los elementos para tener en cuenta en esta actividad, es importante llevar a cabo todas las recomendaciones.
    </p>
    <p style="text-align: justify">
        El proceso de evaluación se realiza por medio de la plataforma de Aldea a la cual podrás ingresar en el siguiente link <a href="http://aldeainnpulsa.com/"> http://aldeainnpulsa.com/ </a> con los siguientes datos:
    </p>
    <p style="text-align: justify">
        <b>Usuario:</b> "Correo registrado en plataforma"
    </p>
    <p style="text-align: justify">
        <b>Clave:</b> Aldea123! (o la establecida por usted)
    </p>
    <br>
    <p style="text-align: justify">
        Agradecemos puedas traer tu equipo de computo para realizar el proceso de evaluación.
    </p>
    <p style="text-align: justify">
        A continuación, encuentras el lugar, fecha, hora y emprendimientos a evaluar:
    </p>
    <div>
        <ul>
            <li> <b> Fecha: </b> </li> <span> {{ $_date }} </span>
            <li> <b> Hora:  </b> </li> <span> {{ $_hour }} </span>
            <li> <b> Lugar: </b> </li> <span> {{ $_place != '' ? $_place : 'Virtual'  }} </span>
            <li> <b> Modalidad: </b> </li> <span> {{ $_modality }} </span>
            <li> <b> Emprendimiento </b> </li> <span> {{ $_register }} </span>
        </ul>

        <p style="text-align: justify">
            No olvides ver las instrucciones para que tu procedimiento sea correcto
        </p>

        @include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Descarga tus instrucciones '), 'action' => "$_url_action", 'color' => $_inst_color])

        @if ($_meet)
            <p style="text-align: justify">
                Ingresa en tu conferencia aquí
            </p>

            @include('emails.calltoaction', ['label' => 'Ingresar', 'action' => "$_meet", 'color' => $_inst_color])
        @endif
    </div><br>
    <p>Saludos cordiales,</p>
    Equipo ALDEA<br>
    iNNpulsa Colombia
@stop
