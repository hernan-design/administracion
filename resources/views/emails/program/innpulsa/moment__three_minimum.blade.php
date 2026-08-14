@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
    Recibe un afectuoso saludo de iNNpulsa Colombia, la agencia del Gobierno Nacional que trabaja para forjar los emprendimientos del futuro, emprendimientos preparados para asumir el reto de crecer colaborando y conectándose.
	</p>
	<p style="text-align:justify">
        De antemano queremos agradecerte por permitirnos conocer tu emprendimiento <b> {{ $_entity_name }} </b>
	</p>
    <p style="text-align:justify">
        En esta ocasión, la Vicepresidencia de Aceleración y Emprendimiento de iNNpulsa Colombia, te informa que no has sido seleccionado(a) para participar en el segundo reto "Conversa con expertos”.
    </p>
    <p style="text-align:justify">
        Sin embargo, te recomendamos validar el cumplimiento, en su totalidad de los requisitos establecidos en el numeral 2, de nuestros términos de referencia Aldea e implementar acciones de mejora, para una próxima postulación.
    </p>
    <p style="text-align:justify">
        Gracias por mostrar interés en ser parte de ALDEA. ¡Recuerda que eres el motor de nuestra organización!
    </p>
    <p>Cordialmente,</p>
    Equipo ALDEA<br>
    iNNpulsa Colombia <br><br>
    Solo podrás volver a aplicar a esta convocatoria desde el  <b>{{ date("Y-m-d", strtotime($_fecha_aplicacion)) }}</b> <br>
    Para volver a aplicar a esta convocatoria <a href="https://aldeainnpulsa.com/convocatoria/reto1-aldea-innpulsa/aplicar/terminos&sa=D&ust=1595877503520000&usg=AFQjCNE3dZbZSbKVpGSg3uQpPFO5SbRgNg">haz clic aquí.</a>
@stop
