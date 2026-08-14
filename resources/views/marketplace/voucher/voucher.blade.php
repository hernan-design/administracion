@extends('layout.layout')

@section('content')
	<section id="marketplace-voucher-wrapper">
		<section id="marketplace-voucher-content" class="marketplace-voucher-content">
			{{-- marketplace voucher dinamic content  --}}
			@yield('voucher.content')
			@yield('voucher.detail.content')
		</section>
	</section>
@stop
