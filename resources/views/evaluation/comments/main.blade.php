@extends('evaluation.comments.evaluation')

@section('evaluation.comments.content')
	{{-- ------------------ Evaluation Templates JS -------------------- --}}
	{{-- template evaluation aside filter --}}
	<script type="text/template" id="list-evaluation-tpl">
		<div class="col-md-12 col-sm-12 col-xs-12">
	        <div class="x_panel">
		        <div class="x_title">
		            <h2>Evaluación Comentarios <small>Lista</small></h2>
		            <div class="clearfix"></div>
		        </div>
				<div class="x_content">
          <div id="content-form-program-filter" class="well">
            {!! Form::open(array('route' => null,'id' => 'form-program-filter', 'class' =>'form-horizontal')) !!}
              <div class="row">
              <br>
              <div class="text-center">
                <a href="{{ route('evaluacion.comentario.create') }}" class="btn btn-success btn-create">Crear</a>
              </div>
            {!! Form::close() !!}
          </div>
          <br/>
					<div class="table-responsive">
						<table id="evaluations-list-search-table" class="table table-striped table-bordered" data-page-length="15">
							<thead>
								<tr class="headings">
                  <th class="column-title">Evaluación</th>
                  <th class="column-title">Título</th>
                  <th class="column-title">Texto</th>
                  <th class="column-title">Estado</th>
								</tr>
							</thead>
							<tbody >
								<!-- Render view  -->
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</script>

