@extends('emails.layout')

@section('content')
	<p>
		<strong>{{ $_name_user_action }}</strong>
		ha programado una sesión de mentoría
	</p>
	<p>
		Inicio: {{ $date_start }}
		<br>
		Fin: {{ $date_end }}
	</p>
	<p>
		Cualquier inquietud comunícate al correo electrónico:
		<br>
		<p style="text-align:center">
			{{$_contactEmail}}
		</p>
	</p>
@stop
