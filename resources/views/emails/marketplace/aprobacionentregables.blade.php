@extends('emails.layout')
@section('content')
	<p style="text-align:justify">
        Se han aprobado los entregables de la compra {{ $_codigo_compra }} por {{ $_admin }}.
	</p>
@stop