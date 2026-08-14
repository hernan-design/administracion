{{-- views/marketplace/detail.blade.php --}}
@extends('marketplace.marketplace')

@section('marketplace.content')
	<style>
 
		.mySlides {display: none}
		
		/* Slideshow container */
		.slideshow-container {
			max-width: 1000px;
			position: relative;
			margin: auto;
		}
		
		/* Next & previous buttons */
		.prev-slide, .next-slide {
			cursor: pointer;
			position: absolute;
			top: 50%;
			width: auto;
			padding: 16px;
			margin-top: -22px;
			color: white;
			font-weight: bold;
			font-size: 18px;
			transition: 0.6s ease;
			border-radius: 0 3px 3px 0;
	 
		}
		
		/* Position the "next button" to the right */
		.next-slide {
			right: 0;
			border-radius: 3px 0 0 3px;
		}
		
		/* On hover, add a black background color with a little bit see-through */
		.prev-slide:hover, .next-slide:hover {
			background-color: rgba(0,0,0,0.8);
		}
		
		/* Number text (1/3 etc) */
		.numbertext-slide {
			color: #FD6720;
			font-size: 12px;
			font-weight: 600;
			padding: 8px 12px;
			position: absolute;
			top: 0;
		}
		
		/* The dots/bullets/indicators */
		.dot-slide {
			cursor: pointer;
			height: 15px;
			width: 15px;
			margin: 0 2px;
			background-color: #bbb;
			border-radius: 50%;
			display: inline-block;
			transition: background-color 0.6s ease;
		}
		
		.active, .dot-slide:hover {
			background-color: #717171;
		}
		
		/* Fading animation */
		.fade-slide {
			-webkit-animation-name: fade;
			-webkit-animation-duration: 1.5s;
			animation-name: fade-slide;
			animation-duration: 1.5s;
		}
		
		@-webkit-keyframes fade-slide {
			from {opacity: .4} 
			to {opacity: 1}
		}
		
		@keyframes fade-slide {
			from {opacity: .4} 
			to {opacity: 1}
		}
		
		/* On smaller screens, decrease text size */
		@media only screen and (max-width: 300px) {
			.prev-slide, .next-slide {font-size: 11px}
		}
	</style>
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Producto</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a href="{{ route('marketplace.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<section id="marketplace-info" class="panel">
	                <div class="bs-example" data-example-id="simple-jumbotron">
	                    <div class="jumbotron">
							<div class="row">
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="profile_img">
				                        <div id="crop-avatar">
				                            <!-- Current avatar -->
											@if (count($adjuntoProducto) > 0)
												<div class="slideshow-container">
														@foreach ($adjuntoProducto as $adjunto)
															<div class="mySlides fade-slide">
																<div class="numbertext-slide">{{ $loop->iteration }} / {{ $loop->count }}</div>
																<img src="{{ $adjunto['direccion_adjunto'] }}" style="width:100%">
															</div>
														@endforeach
														<a class="prev-slide" onclick="plusSlides(-1)">&#10094;</a>
														<a class="next-slide" onclick="plusSlides(1)">&#10095;</a>
												</div>
													<br>
												
												<div style="text-align:center">
													@foreach ($adjuntoProducto as $adjunto)
														<span class="dot-slide" onclick="currentSlide({{ $loop->iteration }})"></span> 
													@endforeach
												</div>
											@else
											<div class="slideshow-container">
												<img src="https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/svg/no-image-770x460.svg" alt="No hay imagenes adjuntas">
												<small>No hay imágenes adjuntas</small>
											</div>
											@endif
										</div>
				                    </div>
			                    </div>
								<div class="col-md-8 col-sm-6 col-xs-12">
			                        <h3><strong>{{ $producto->nombre_producto }}</strong></h3>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<p><b>Descripción</b></p>
									<div class="text-justify">
										<span>{{ $producto->descripcion_producto !== '' ? strip_tags($producto->descripcion_producto) : 'No hay descripcion' }}</span>
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<p><b>Resumen</b></p>
									<div class="text-justify">
										<span>{{ $producto->resumen_producto !== '' ? strip_tags($producto->resumen_producto) : 'No hay resumen' }}</span>
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-2">
									<span>${{ number_format($producto->precio_producto,0,',','.') }}</span>
								</div>
								<div class="col-xs-2 text-right">
									<span>Estado: {{ $producto->nombre_estado }}</span>
								</div>
								<div class="col-xs-2 text-right">
									<span><i class="glyphicon glyphicon-thumbs-up"></i>{{ $producto->contador_like }}</span>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-3">
									<span>Domicilio de producto: {{ $producto->domicilio_producto ? 'Si' : 'No' }}</span>
								</div>
								<div class="col-xs-3 text-right">
									<span>Clase de producto: {{ $producto->codigo_clase_producto == 1 ? 'Producto' : 'Servicio' }}</span>
								</div>
								<div class="col-xs-3 text-right">
									<span>Modalidad: {{ App\Models\Core\Producto::getModalitySpanish($producto->modalidad_prestacion_servicio) }}</span>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-3">
									<span>Entidad: {{ $entity }}</span>
								</div>
								<div class="col-xs-4">
									<span>Recibir oferta: {{ $producto->solicitud_revision_producto ? 'Si' : 'No' }}</span>
								</div>
							</div>
	                    </div>
	                </div>
				</section>
			</div>
			<div class="x_content">
				<section id="marketplace-info-evaluation">
					@if(count($averagePurchase) > 0)
						<div class="col-md-8 col-sm-6 col-xs-12">
							<h3><strong>Evaluación de producto</strong></h3>
						</div>
						<div class="row">
							<div class="col-md-8 col-sm-12 col-xs-12">
								<div class="row">
									<div class="col-md-12">
										<dl id="items-purchase-evaluation" class="list-unstyled">
											@foreach ($averagePurchase as $average)
												<dt>{{ $average['nombre_item_evaluacion_compra'] }}</dt>
												<dd>
													<div class="clearfix">
														<p class="pull-left">{{ $average['descripcion_item_eval_compra'] }}</p>
														<div class="pull-right stars-rating stars-30 readonly disabled">
															<input type="radio" name="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}" id="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_5" value="5" @if($average['valor_promedio'] == 5) checked @endif disabled="disabled" class="check-star">
															<label for="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_5" class="label-terms"><i class="fa fa-star"></i></label>

															<input type="radio" name="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}" id="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_4" value="4" @if($average['valor_promedio'] == 4) checked @endif disabled="disabled" class="check-star">
															<label for="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_4" class="label-terms"><i class="fa fa-star"></i></label>

															<input type="radio" name="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}" id="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_3" value="3" @if($average['valor_promedio'] == 3) checked @endif disabled="disabled" class="check-star">
															<label for="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_3" class="label-terms"><i class="fa fa-star"></i></label>

															<input type="radio" name="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}" id="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_2" value="2" @if($average['valor_promedio'] == 2) checked @endif disabled="disabled" class="check-star">
															<label for="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_2" class="label-terms"><i class="fa fa-star"></i></label>

															<input type="radio" name="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}" id="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_1" value="1" @if($average['valor_promedio'] == 1) checked @endif disabled="disabled" class="check-star">
															<label for="evaluacion_item_compra_{{$average['codigo_item_evaluacion_compra']}}_1" class="label-terms"><i class="fa fa-star"></i></label>
														</div>
													</div>
												</dd>
											@endforeach
										</dl>
									</div>
								</div>
							</div>
						</div>
					@endif
				</section>
			</div>
		</div>
	</div>


	<script>
		var slideIndex = 1;
		showSlides(slideIndex);
		
		function plusSlides(n) {
		  showSlides(slideIndex += n);
		}
		
		function currentSlide(n) {
		  showSlides(slideIndex = n);
		}
		
		function showSlides(n) {
		  var i;
		  var slides = document.getElementsByClassName("mySlides");
		  var dots = document.getElementsByClassName("dot-slide");
		  if (n > slides.length) {slideIndex = 1}    
		  if (n < 1) {slideIndex = slides.length}
		  for (i = 0; i < slides.length; i++) {
			  slides[i].style.display = "none";  
		  }
		  for (i = 0; i < dots.length; i++) {
			  dots[i].className = dots[i].className.replace(" active", "");
		  }
		  slides[slideIndex-1].style.display = "block";  
		  dots[slideIndex-1].className += " active";
		}
	</script>
@stop
