@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
		El gestor de marketplace no asignó los voucher sugeridos.
	</p>
	@if (!empty($comment))
		<p>Este fue el comentario que indicó:</p>
		<p>
			- {{ $comment }}
		</p>
	@endif
	<br>
@stop
