@extends('layout.layout')

@section('content')
	<section id="marketplace-promotion-wrapper">
		<section id="marketplace-promotion-content" class="marketplace-promotion-content">
			{{-- marketplace promotion dinamic content  --}}
			@yield('promotion.content')
		</section>
	</section>
@stop
