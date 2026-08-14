<html>
	<head>
		<style>
			/** Define the margins of your page **/
			@page {
				margin: 100px 50px 100px 50px;
			}
			header {
				position: fixed;
				top: -60px;
				left: 0px;
				right: 0px;
				height: 50px;

				/** Extra personal styles **/
				background-color: #ffffff;
				color: white;
				text-align: center;
				line-height: 35px;
			}
			footer {
				position: fixed;
				bottom: -60px;
				left: 0px;
				right: 0px;
				height: 50px;

				/** Extra personal styles **/
				font-family: Arial, Helvetica, sans-serif;
				font-size: 9px;
				background-color: #ffffff;
				color: black;
				text-align: right;
				line-height: 35px;
			}
			table, th, td {
				border: 1px solid rgb(184, 178, 178);
				border-collapse: collapse;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 100%;
			}
			th {
				text-align: left;
				padding: 2.5px;
			}
			td {
				background-color: #d9d9d9;
				color: black;
				padding: 2.5px;
				font-size: 10px;
			}
			h5 {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
			}
			h5 .title {
				margin-top: 25px;
				text-align: center;
			}
			h6 {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin: 10px 0 0 0;
			}
			.padding h6 {
				padding: 3px;
			}
			ul {
				list-style: none;
				padding: 0;
				margin: 0;
			}
			li {
				padding-left: 16px;
				color: black;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;
			}
			li::before {
				content: "•";
				padding-right: 8px;
				color: black;
			}
			li .justify{
				text-align: justify;
			}
			.content-info {
				padding: 0 5px 0 27px;
				/*position: relative;*/
			}
			.bg-blue {
				background: rgb(39, 167, 221);
				color: white;
			}
			.bg-red {
				background: rgb(209, 48, 47);
				color: white;
			}
			.bg-green {
				background: rgb(146, 184, 69);
				color: white;
			}
			.bg-yellow {
				background: rgb(255, 205, 63);
				color: white;
			}
			.sub-title {
				text-align: center;
				/*border: 1px solid rgb(184, 178, 178);*/
			}
			.scoring-1 {
				margin: 0;
				padding: 0;
				height: 230px;
				width: 320px;
				box-sizing: border-box;
				text-align: center;
			}
			.scoring-2 {
				margin: 0;
				padding: 0;
				height: 230px;
				width: 320px;
				box-sizing: border-box;
				text-align: center;
			}
			.scoring-3 {
				margin: 0;
				padding: 0;
				height: 230px;
				width: 320px;
				box-sizing: border-box;
				text-align: center;
			}
			.scoring-4 {
				margin: 0;
				padding: 0;
				height: 220px;
				width: 320px;
				box-sizing: border-box;
				text-align: center;
			}
			.id-table {
				background: white;
				text-align: center;
			}
			.description-table {
				background: white;
			}
			.scoring-table {
				text-align: center;
			}
			.container-product {
				display: flex;
				justify-content: center;
				margin-top: 200px;
        margin-bottom: 15px;
				border: 1px solid rgb(184, 178, 178);
			}
			.container-service {
				display: flex;
				justify-content: center;
				margin-top: 200px;
        margin-bottom: 15px;
				border: 1px solid rgb(184, 178, 178);
			}
			.item {
				margin: 0;
				padding: 0;
				text-align: center;
				/*align-self: center;*/
				box-sizing: border-box;
			}
			.average {
				background: white;
				text-align: center;
			}
			.average-total {
				background: rgb(99, 111, 161);
				color: white;
			}
			.margin-top-product {
				margin-top: 200px
			}
			.margin-top-service {
				margin-top: 200px
			}
		</style>
	</head>

	<body>
		<header>
			<img src="https://s3.us-west-2.amazonaws.com/static.tuproyecto.com/Files/IMAGENESTP/bolivar.png"
				alt="Encabezado"  width="600px" height="50px">
		</header>
		<footer>
			Copyright © Territorio MyPime @php echo " ". date("Y") @endphp
		</footer>
		<main>
			<h5 class="title">
				DIAGNÓSTICO DE INNOVACIÓN PROYECTO - “FORTALECIMIENTO DEL SISTEMA REGIONAL DE
				CIENCIA, TECNOLOGÍA E INNOVACIÓN MEDIANTE EL FOMENTO A LA INNOVACIÓN Y EL DESARROLLO
				TECNOLÓGICO DE LAS MIPYME DEL DEPARTAMENTO DE BOLÍVAR.
			</h5>

			<br>

{{--  			<table>
				<tr>
					<th>Nombre del encuestador</th>
					<td>Luz Angélica Saumeth</td>
				</tr>
				<tr>
					<th>Fecha de diligenciamiento de la encuesta</th>
					<td> @php echo (date("d-m-Y")) @endphp </td>
				</tr>
			</table>  --}}

			<div>
				@include('reports.diagnosis.siinovapdf.datosbasicos_1')
			</div>
			<div>
				@include('reports.diagnosis.siinovapdf.componenteproductivo_1')
			</div>

			{{--  Grafico de radar => INNOVACIÓN CONCEPTUAL Y FOCALIZADA  --}}
			<div>
				@include('reports.diagnosis.siinovapdf.scoring-1_1', ['answers_eval' => $_answers_eval])
			</div>
			<div>
				@php $i = 1 @endphp
				<table>
					<thead>
						<tr class="bg-blue">
							<th>Sigla</th>
							<th>Criterios</th>
							<th>Puntaje Obtenido</th>
						</tr>
					</thead>
					<tbody>
						@php
							$var = 0;
						@endphp
						@foreach ($_answers_eval as $answer => $value)
							@if ($value['codigo_grupo_item_eval'] == 58)
								<tr>
									<td class="id-table"> @php echo "C" . $i++ @endphp </td>
									<td class="description-table"> @php echo $value['nombre_grup_opc_eval_pregunta'] @endphp </td>
									<td class="scoring-table"> @php echo $value['respuesta'] @endphp </td>
								</tr>
								@php
									$var = $var + $value['respuesta'];
								@endphp
							@endif
						@endforeach
						@php
							$average_sco_1 = $var / 6;
						@endphp
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" class="average">Promedio</th>
							<td class="scoring-table"> @php echo round($average_sco_1,1); @endphp </td>
						</tr>
					</tfoot>
				</table>
			</div>

			{{--  Grafico de radar => FOMENTO A LA INNOVACIÓN  --}}
			<div>
				@include('reports.diagnosis.siinovapdf.scoring-2_1', ['answers_eval' => $_answers_eval])
			</div>
			<div>
				@php $i = 1 @endphp
				<table>
					<thead>
						<tr class="bg-red">
							<th>Sigla</th>
							<th>Criterios</th>
							<th>Puntaje Obtenido</th>
						</tr>
					</thead>
					<tbody>
						@php
							$var = 0;
						@endphp
						@foreach ($_answers_eval as $answer => $value)
							@if ($value['codigo_grupo_item_eval'] == 59)
								<tr>
									<td class = "id-table"> @php echo "C" . $i++ @endphp </td>
									<td class="description-table"> @php echo $value['nombre_grup_opc_eval_pregunta'] @endphp </td>
									<td class = "scoring-table"> @php echo $value['respuesta'] @endphp </td>
								</tr>
								@php
									$var = $var + $value['respuesta'];
								@endphp
							@endif
						@endforeach
						@php
							$average_sco_2 = $var / 10;
						@endphp
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" class="average">Promedio</th>
							<td class="scoring-table"> @php echo round($average_sco_2,1); @endphp </td>
						</tr>
					</tfoot>
				</table>
			</div>

			{{--  Grafico de radar => CONDICIÓN DE LA INNOVACIÓN  --}}
			<div>
				@include('reports.diagnosis.siinovapdf.scoring-3_1', ['answers_eval' => $_answers_eval])
			</div>
			<div>
				@php $i = 1 @endphp
				<table>
					<thead>
						<tr class="bg-green">
							<th>Sigla</th>
							<th>Criterios</th>
							<th>Puntaje Obtenido</th>
						</tr>
					</thead>
					<tbody>
						@php
							$var = 0;
						@endphp
						@foreach ($_answers_eval as $answer => $value)
							@if ($value['codigo_grupo_item_eval'] == 60)
								<tr>
									<td class = "id-table"> @php echo "C" . $i++ @endphp </td>
									<td class="description-table"> @php echo $value['nombre_grup_opc_eval_pregunta'] @endphp </td>
									<td class ="scoring-table"> @php echo $value['respuesta'] @endphp </td>
								</tr>
								@php
									$var = $var + $value['respuesta'];
								@endphp
							@endif
						@endforeach
						@php
							$average_sco_3 = $var / 10;
						@endphp
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" class="average">Promedio</th>
							<td class="scoring-table"> @php echo round($average_sco_3,1); @endphp </td>
						</tr>
					</tfoot>
				</table>
			</div>

			{{--  Grafico de radar => INNOVACIÓN COLABORATIVA  --}}
			<div>
				@include('reports.diagnosis.siinovapdf.scoring-4_1', ['answers_eval' => $_answers_eval])
			</div>
			<div>
				@php $i = 1 @endphp
				<table>
					<thead>
						<tr class="bg-yellow">
							<th>Sigla</th>
							<th>Criterios</th>
							<th>Puntaje Obtenido</th>
						</tr>
					</thead>
					<tbody>
						@php
							$var = 0;
						@endphp
						@foreach ($_answers_eval as $answer => $value)
							@if ($value['codigo_grupo_item_eval'] == 61)
								<tr>
									<td class = "id-table"> @php echo "C" . $i++ @endphp </td>
									<td class="description-table"> @php echo $value['nombre_grup_opc_eval_pregunta'] @endphp </td>
									<td class ="scoring-table"> @php echo $value['respuesta'] @endphp </td>
								</tr>
								@php
									$var = $var + $value['respuesta'];
								@endphp
							@endif
						@endforeach
						@php
							$average_sco_4 = $var / 4;
						@endphp
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" class="average">Promedio</th>
							<td class="scoring-table"> @php echo round($average_sco_4,1); @endphp </td>
						</tr>
					</tfoot>
				</table>
			</div>

			<div>
				<h5 class="title">III. OBSERVACIONES</h5>

				@foreach ($_answers_eval as $answer => $value)

					@if ($value['codigo_grupo_item_eval'] == 69 && $value['codigo_item_evaluacion'] == 482)
						<table>
							<thead>
								<tr>
									<th colspan="2"style="text-align: left">• INNOVACIÓN CONCEPTUAL Y FOCALIZADA</th>
								</tr>
							</thead>
						</table>
						<table>
							<tbody>
								<tr>
									<td>{{ $value['comentario_adicional'] }} </td>
								</tr>
							</tbody>
						</table>
						<br>
					@endif

					@if ($value['codigo_grupo_item_eval'] == 69 && $value['codigo_item_evaluacion'] == 483)
						<table>
							<thead>
								<tr>
									<th colspan="2"style="text-align: left">• FOMENTO A LA INNOVACIÓN</th>
								</tr>
							</thead>
						</table>
						<table>
							<tbody>
								<tr>
									<td>{{ $value['comentario_adicional'] }} </td>
								</tr>
							</tbody>
						</table>
						<br>
					@endif

					@if ($value['codigo_grupo_item_eval'] == 69 && $value['codigo_item_evaluacion'] == 484)
						<table>
							<thead>
								<tr>
									<th colspan="2"style="text-align: left">• CONDICIÓN DE LA INNOVACIÓN</th>
								</tr>
							</thead>
						</table>
						<table>
							<tbody>
								<tr>
									<td>{{ $value['comentario_adicional'] }} </td>
								</tr>
							</tbody>
						</table>
						<br>
					@endif

					@if ($value['codigo_grupo_item_eval'] == 69 && $value['codigo_item_evaluacion'] == 485)
						<table>
							<thead>
								<tr>
									<th colspan="2" style="text-align: left">• INNOVACIÓN COLABORATIVA</th>
								</tr>
							</thead>
						</table>
						<table>
							<tbody>
								<tr>
									<td>{{ $value['comentario_adicional'] }} </td>
								</tr>
							</tbody>
						</table>
						<br>
					@endif
				@endforeach

				<h5 class="title">
					COMENTARIO GENERAL
				</h5>
				<p style="text-align: justify;
						color: black;
						font-family: Arial, Helvetica, sans-serif;
						font-size: 11px;">
					{{ $_answers_eval[0]['observacion'] }}
				</p>

				<table style="border: 1px solid rgb(184, 178, 178)">
					@php
						$average_total = ($average_sco_1+$average_sco_2+$average_sco_3+$average_sco_4) / 4;
						$message_scoring = "";
					@endphp

					<tbody>
						<tr>
							<th colspan="2" class="average-total">Puntaje Promedio Nivel De Innovación</th>
							<td class="scoring-table"> @php echo round($average_total,1); @endphp </td>
							@if ( $average_total >0 && $average_total <=2.5 )
								<td class="scoring-table"> @php echo $message_scoring = "Bajo"; @endphp </td>
							@elseif ( $average_total >=2.6 && $average_total <=3.5 )
								<td class="scoring-table"> @php echo $message_scoring = "Medio";; @endphp </td>
							@elseif ( $average_total >=3.6 && $average_total <=4.5 )
								<td class="scoring-table"> @php echo $message_scoring = "Alto"; @endphp </td>
							@elseif ( $average_total >=4.6 && $average_total <=5 )
								<td class="scoring-table"> @php echo $message_scoring = "Superior"; @endphp </td>
							@endif
						</tr>
					</tbody>
				</table>
			</div>
			<br>
			{{-- CATEGORÍA DE INNOVACIÓN DE LA EMPRESA / ORGANIZACIÓN  --}}
			<div>
				<h6 class="sub-title">CATEGORÍA DE INNOVACIÓN DE LA EMPRESA / ORGANIZACIÓN</h6>
				<p style="text-align: justify;
					color: black;
					font-family: Arial, Helvetica, sans-serif;
					font-size: 11px;">
					{{ $_answers_eval[30]['respuesta'] }}
				</p>
			</div>
		</main>
	</body>
</html>
