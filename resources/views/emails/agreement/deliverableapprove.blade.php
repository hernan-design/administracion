@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
    Una vez realizada la revisión de los entregables correspondientes al servicio {{ $_project }} prestado a {{ $_purchaser }} nos permitimos informar que estos cumplen con lo definido en el acuerdo de negociación. Por lo cual, procederemos a emitir y enviarte prontamente tu Acta de aprobación para facturar.
  </p>
  <p style="text-align:justify">
    A continuación podrás encontrar el acta de revisión de entregables.
  </p>
  <ul>
    <li>
      <a href="{{ $_urldocumentapprove }}"> Acta de revisión de entregables </a>
    </li>
  </ul>
@stop
