@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
		El gestor de marketplace asignó los voucher sugeridos.
	</p>
	@if (!empty($vouchers))
		@foreach ($vouchers as $voucher)
			<p>
				- {{ $voucher }}
			</p>
		@endforeach
	@endif
	<br>
@stop
