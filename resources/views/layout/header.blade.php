{{-- Declare variable--}}
@php
	$articlesCountReview = App\Models\Param\Institucion::find( session('institution')->codigo_institucion )->contador_articulos_pendientes;
	$productsCountReview = App\Models\Param\Institucion::find( session('institution')->codigo_institucion )->contador_productos_pendientes;
@endphp
<div class="nav_menu">
	<nav class="" role="navigation">
		<div class="nav toggle">
			<a id="menu_toggle"><i class="fa fa-bars"></i></a>
		</div>

		<ul class="nav navbar-nav navbar-right">
			<li class="">
				<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<img src="{{ Auth::user()->imagen_usuario }}" alt="{{ Auth::user()->nombre_usuario }}" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')">{{ Auth::user()->nombre_usuario }}
					<span class=" fa fa-angle-down"></span>
				</a>
				<ul class="dropdown-menu dropdown-usermenu pull-right">
					<li>
						<a href="{{ route('auth.logout') }}">
							<i class="fa fa-sign-out pull-right"></i>{{ trans('app.logout') }}
						</a>
					</li>
				</ul>
			</li>
			<li role="presentation" class="dropdown">
				<a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
					<i class="fa fa-bell-o"></i>
					@if($articlesCountReview > 0 || $productsCountReview > 0)
						<span class="badge bg-green">{{ $articlesCountReview > 0 || $productsCountReview > 0 ? 1 : 0 }}</span>
					@endif
				</a>
				<ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
					@if($articlesCountReview > 0)
						<li>
							<a href="{{ route('contenido.index') }}">
								<span class="message target-link">
									<i class="fa fa-newspaper-o"></i> Tienes {{ $articlesCountReview > 0 && $articlesCountReview == 1 ?  "$articlesCountReview artículo" : "$articlesCountReview artículos" }} por aprobar
								</span>
							</a>
						</li>
					@elseif($productsCountReview > 0)
						<li>
							<a href="{{ route('marketplace.index') }}">
								<span class="message target-link">
									<i class="fa fa-newspaper-o"></i> Tienes {{ $productsCountReview > 0 && $productsCountReview == 1 ?  "$productsCountReview productos" : "$productsCountReview productos" }} por aprobar
								</span>
							</a>
						</li>
					@else
						<li>
							<div class="text-center">
								<strong>No tienes notificaciones</strong>
							</div>
						</li>
					@endif
				</ul>
			</li>
		</ul>
	</nav>
</div>
