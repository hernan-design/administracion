<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{{-- Format title --}}
		@if(isset($title)) <title>{{ $title }}</title> @endif

		<style type="text/css">
			body {
				font-size: 9;
				font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
				font-weight: normal;
			}

			@page {
				margin-top: 40px;
				margin-right: 76px;
				margin-left: 76px;
				margin-bottom: 76px;
			}

			.left {
				text-align: left;
			}

			.right {
				text-align: right;
			}

			.center{
				text-align: center;
			}

			.global {
				width: 100%;
			}

			.global td {
				padding: 3px 5px;
			}

			.global th {
				padding-bottom: 5px;
				text-align: left;
			}

			.title{
				font-size: 12px;
				font-weight: bold;
				text-align: center;
			}

			.company {
				font-size: 16px;
				font-weight: bold;
				text-align: center;
			}

			.subtitle {
				font-size: 13px;
				font-weight: bold;
				text-align: left;
				color: #2b4c7c;
				text-decoration: underline;
			}

			.currency {
				text-align: right;
			}

			.rtable {
				width: 100%;
			}

			.rtable th {
				border: 1px solid black;
				padding-left: 2px;
			}

			.rtable td, th {
				height: 19px;
			}

			.rtable tr:nth-child(even) {
				background-color: #f2f2f2
			}
			/*Nuevos*/
			.htable {
				width: 100%;
			}

			.htable td, th {
				text-align: left;
			}

			.brtable {
				width: 100%;
			    border-collapse: collapse;
			}

			.brtable th {
				border: 1px solid black;
				padding-left: 2px;
			}

			.brtable td {
				border: 1px solid black;
				padding-left: 2px;
			}

			.left {
				text-align: left;
			}

			.right {
				text-align: right;
			}

			.center{
				text-align: center;
			}

			.justify{
				text-align: justify;
			}

			.bold{
				font-weight: bold;
			}

			.width-100 {
				width: 100%;
			}

			.size-6 {
				font-size: 6;
			}

			.size-7 {
				font-size: 7;
			}

			.border-left {
				border-left: 1px solid black;
				padding-left: 2px;
			}
			.border-button{
				border-bottom: 2px solid black;
			}

			.border-right {
				border-right: 1px solid black;
				padding-left: 2px;
			}

			.border-top {
				border-top: 1px solid black;
				padding-top: 2px;
			}

			.height-40 {
				height: 40px;
			}

			.height-19 {
				height: 19px;
			}

			.margin-top-60 {
				margin-top: 60px;
			}

			.margin-bottom-60 {
				margin-bottom: 60px;
			}

			.page_break {
				page-break-before: always;
			}

            .global td {
                padding: 3px 5px;
            }

            .global th {
                padding-bottom: 5px;
                text-align: left;
            }

			.img-log-extra {
				width: 25%;
			}

			.title-course {
				font-size: 55px;
			}

			.name-user {
				font-size: 4rem;
				font-weight: bold;
				color: #434343;

			}

			.firm {
				font-size: 15px;
				font-weight: bold;
				margin: 50px;
				margin-bottom: 10px;
				margin-top: 15px;
			}

			.contenido {
				margin-top: 100px;
			}

			.container {
				margin-left: 50px;
				margin-right: 50px;
			}
			.row {
				display: inline;
				justify-content: center;
			}
			.img-left, .img-right {
				position: relative;


			}

			.img-left {
				height: 40px;
				margin-right: 90px;
			}

			.img-right {
				height: 60px;
				margin-left: 20px;
			}

			.img-fondo {
				height: 100%;
				width: 100%;
				position: absolute;
				left: 0px;
				top: 0px;
				z-index: -1;
			}
			.line-height-1 {
				line-height: 1px;
			}

		</style>

	</head>
	<body>
		@if(isset($type) && $type == 'PDF')
			{{-- <table class="global">
				<thead>
					<tr>
						<td class="center">
							<img src="{{ App\RegistroSesion::institution()->logo_reporte_institucion ? App\RegistroSesion::institution()->logo_reporte_institucion : App\RegistroSesion::institution()->logo_institucion }}">
						</td>
					</tr>
				</thead>
			</table> --}}
		@endif

		<div class="center contenido">
			@yield('content')
		</div>

	</body>
</html>
