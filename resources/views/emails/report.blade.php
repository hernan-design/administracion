@extends('emails.layout')

@section('content')
	<p style="text-align:justify">
		Hemos terminado de generar tu informe. Puedes encontrarlo haciendo clic en el siguiente enlace:
	</p>
	
	@include('emails.calltoaction', ['label' => (isset($label_action) ? $label_action : 'Descargar'), 'action' => "$_url", 'color' => $_inst_color])
	
	{{-- <p style="text-align:justify">
		También puedes encontrar el informe navegando a la sección Reportes en su Mesa de Ayuda.
	</p> --}}	
@stop
