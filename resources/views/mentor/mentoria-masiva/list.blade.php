{{-- views/mentor/index.blade.php --}}
@extends('mentor.mentor')

@section('mentor.content')
	<div class="col-xs-12" id="mentoria-masiva-lista">
		<div class="x_panel">
			<div class="x_title">
				<h2>Mentorías masivas<small>Lista</small></h2>
				<ul class="nav navbar-right panel_toolbox">
                    <li>
                    	<a id="history-back" href=""><i class="fa fa-chevron-left"></i> Atrás</a>
                    </li>
				</ul>
			<div class="clearfix"></div>
			</div>

			<div class="x_content">
				<br/>
				{{-- Lista de proyectos para procesos de mentoria --}}
				<div class="table-responsive">
					<table id="mentoria-masiva-table" class="table table-striped table-bordered " data-page-length="15">
						<thead>
						<tr class="headings">
							<th class="column-title">id</th>
							<th class="column-title">Mentor</th>
							<th class="column-title"># entidades asignadas</th>
							<th class="column-title">Fecha creación</th>
						</tr>
						</thead>
						<tbody >
						<!-- Render view  -->
						</tbody>
					</table>
				</div>
				<br/>

			</div>

		</div>
	</div>


@stop
