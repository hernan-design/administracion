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
				margin-top: 50px;
				margin-right: 40px;
				margin-left: 40px;
				margin-bottom: 50px;
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

			.upper-roman-list {
				list-style-type: upper-roman;
			}

			.disc-style-list {
				list-style-type: disc;
			}

			.currency {
				text-align: right;
			}

			.bold {
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

			.size-8 {
				font-size: 8;
			}

      .size-12 {
        font-size: 12;
      }

			.border-left {
				border-left: 1px solid black;
				padding-left: 2px;
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

			.htable {
				width: 100%;
			}

			.htable th {
				text-align: center;
				border: 1px solid black;
				padding-left: 2px;
			}

			.ntable {
				width: 100%;
			}

			.ntable td, th {
				text-align: left;
			}

			.img-user{
				width: 100%
			}

			.content-info {
				padding: 0 0 0 27px;
			}

			.table-info {
				padding: 20px 0 15px 27px;
			}

			.page_break {
				page-break-before: always;
			}

			.link {
				text-decoration: underline;
				color: #0000EE;
			}

			.underlined {
				text-decoration: underline;
			}
		</style>
	</head>
	<body>
		@if(isset($type) && $type == 'PDF')
			<table class="global">
				<thead>
					<tr>
						<td class="center">
						@if(isset($header))
							<img src="{{ $header }}">
						@else
							<img src="{{ session('institution')->logo_reporte_institucion ? session('institution')->logo_reporte_institucion : session('institution')->logo_institucion }}">
						@endif
						</td>
					</tr>
				</thead>
			</table>
		@endif

		@if(isset($title) && trim($title) != '')
			<h2 class="center">{{ $title }}</h2>
		@endif

		@yield('content')
	</body>
</html>
