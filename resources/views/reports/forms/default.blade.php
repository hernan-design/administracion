@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
	{{-- Include form --}}
	@include('reports.forms.form')
@stop