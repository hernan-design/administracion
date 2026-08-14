<!DOCTYPE html>
<html lang="en">
	<head>
		@include('layout.head')
		<script>
			/**
			* default image
			* @parem imgEl Element
			* @param imgDefault String default image url
			*/
			function imageDefault (imgEl, imgDefault) { imgDefault || (imgDefault = ''); if (imgEl.getAttribute('src') != imgDefault) imgEl.setAttribute('src', imgDefault); }
		</script>
	</head>

	<body class="{{ Cookie::get('menu_toggle', 'nav-md') }}">
		<div class="container body">
			<div class="main_container">
	        	{{-- Side Menu --}}
	        	<div class="col-md-3 left_col">
	        		@include('layout.sidemenu')
	        	</div>

	        	{{-- Header --}}
		        <div class="top_nav">
		        	@include('layout.header')
		        </div>

		        {{-- Page content --}}
		        <div class="right_col" role="main">
		        	<div id="loading-admin-app" class="modal fade" role="dialog" aria-hidden="true"></div>
		        	@yield('content')

		        	{{-- Templates Js --}}
					@include('layout.templates')

					{{-- Modal base --}}
					@include('public.modalbase')
		        </div>

	        	{{-- Footer --}}
		        <footer>
		        	@include('layout.footer')
		        </footer>
			</div>
	    </div>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue{{ $app->environment('production') ? '.min' : '' }}.js"></script>
		<script>window.document.url = "{{ URL::to('/') }}";</script>
		<script>window.document.platform = "{{ URL::to(session('institution')->url_institucion) }}";</script>
		{{-- Libraries so: foundation, underscore, backbone, etc --}}
		<script src="{{ elixir('js/vendor.js') }}"></script>
		{{-- Components --}}
		<script src="{{ elixir('js/component.js') }}"></script>
		{{-- App --}}
		<script src="{{ elixir('js/app.js') }}"></script>
		{{-- Realtime --}}
		 @if (Auth::check() && $app->environment('production'))
			{{-- <script src="{{ elixir('js/realtime.js') }}"></script>  --}}
		@endif
    @yield('scripts')
	</body>
</html>
