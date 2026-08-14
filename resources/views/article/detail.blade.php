{{-- views/article/articledetail.blade.php --}}
@extends('article.article')

@section('article.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Contenido</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
					<a href="{{ $_SERVER['HTTP_REFERER'] }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="well">
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="x_panel">
								<div>
									<img src="{{ $content->imagen_contenido }}" style="width: 100%; height: auto;">
								</div>
								<div>
									<h3 class="text-left"><strong>{{ $content->nombre_contenido }}</strong></h3>
								</div>
								<br>
								<div>
									<p class="text-justify">{!! $content->texto_contenido !!}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop
