<div class="left_col scroll-view">
	<div class="navbar nav_title" style="border: 0;">
		<a href="{{ route('dashboard') }}" class="site_title p-1">
			<img src="{{ session('institution')->logo_admin_institucion ? session('institution')->logo_admin_institucion : session('institution')->logo_institucion }}" alt="{{ session('institution')->nombre_institucion }}" style="width: 50px;height:auto;">
			<span class="position-absolute">{{ session('institution')->sigla_institucion }}</span>
		</a>
	</div>
	<div class="clearfix"></div>

	<div class="profile clearfix">
		<div class="profile_pic">
			<img src="{{ Auth::user()->imagen_usuario }}" alt="{{ Auth::user()->nombre_usuario }}" class="img-circle profile_img" onerror="imageDefault(this, '{{ config('tuproyecto.avatar_image') }}')">
		</div>
		<div class="profile_info">
			<span>{{ trans('app.welcome') }}</span>
			<h2>{{ Auth::user()->nombre_usuario }}</h2>
		</div>
	</div>
	<br />

	<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
		<div class="menu_section">
			<h3>General</h3>
			<ul class="nav side-menu">
				<li>
					<a href="{{ route('dashboard') }}"><i class="fa fa-home"></i> {{ trans('app.home') }}</a>
				</li>
				@php $parents = App\Models\Param\Menu::getParents(App\Models\Param\TipoMenu::getType('admin')); @endphp
				@foreach ($parents as $parent)
					{{-- Omitir menu simple 'reportes' para conservar unicamente el modulo Reportes con submodulos --}}
					@if($parent->ruta_menu_institucion == 'reportes')
						@continue
					@endif
					<li>
						<a {{ $parent->ruta_menu_institucion ? sprintf('href=%s', url($parent->ruta_menu_institucion)) : ''}}>
							<i class="{{ $parent->icono_menu_item }}"></i>{{ $parent->nombre_item_menu }}
							@if(!$parent->ruta_menu_institucion)
								<span class="fa fa-chevron-down"></span>
							@endif
						</a>
						@php $children = App\Models\Param\Menu::getChildren(App\Models\Param\TipoMenu::getType('admin'), $parent->codigo_menu_institucion); @endphp
						@if($children->count())
							<ul class="nav child_menu">
								@foreach ($children as $son)
									<li>
										<a href="{{ url($son->ruta_menu_institucion) }}">{{ $son->nombre_item_menu }}</a>
									</li>
								@endforeach
							</ul>
						@endif
					</li>
				@endforeach
			</ul>
		</div>
	</div>
</div>
