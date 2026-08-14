@extends('emails.layout')

@section('content')
	<p>
		<strong>{{{ $_name_user_action }}}</strong>
		<span>{{{ Lang::get('mentoring.mail.confirm.body') }}}
		<br>
		<br>
		 	Inicio: {{ $date_start }}
		<br>
		<br>
		 	Fin: {{ $date_end }}
		<br>
		<br>
		<br>
		Cualquier inquietud comunícate al correo electrónico:
		<br>
		<p style="text-align:center">
			{{{$_contactEmail}}}
		</p>
	</p>
@stop
