@extends('layout.layout')

@section('content')

    <section id="reports-general-wrapper">
      <section id="reports-general" class="reports-general">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Reportes</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        @if (session('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif
                        <div>
                          {!! Form::open(['route' => 'reportes.store', 'id' => 'form-users-reports-filter', 'class' => '']) !!}
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                  <div class="form-group">
                                    <p>Tipo Reporte</p>
                                    <select class="x_panel" name="tipo_reporte">
                                      <option value="1">Evaluaciones Convocatorias</option>
                                      <option value="2">Reporte General Eventos</option>
                                      <option value="3">Reporte Cursos</option>
                                      <option value="4">Reporte Mentorías</option>
                                      <option value="5">Reporte Sesiones</option>
                                    </select>

                                  </div>
                                </div>

                                <div class="col-md-3 col-sm-3 col-xs-12" id="tipo_reporte_evaluacion_contenedor">
                                  <div class="form-group">
                                    <p>Tipo Reporte Evaluación</p>
                                    <select class="x_panel" name="tipo_reporte_evaluacion">
                                      <option value="" selected> Seleccione... </option>
                                      @if(isset($evaluations))
                                        @foreach($evaluations as $eval)
                                          <option value="{{ $eval->id }}"> {{ $eval->text }} </option>
                                        @endforeach
                                      @endif
                                    </select>

                                  </div>
                                </div>

                                <div class="col-md-3 col-sm-3 col-xs-12">
                                  <div class="form-group">
                                    <p>fecha Inicio</p>
                                    <input type="date" class="x_panel @error('fecha_inicial') is-invalid @enderror"
                                            name="fecha_inicial" id="fecha_inicial" value="{{ old('fecha_inicial') }}" />

                                        @error('fecha_inicial')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                  </div>
                                </div>


                                <div class="col-md-3 col-sm-3 col-xs-12">
                                  <div class="form-group">
                                    <p>Fecha final</p>
                                        <input type="date" class="x_panel @error('fecha_final') is-invalid @enderror"
                                            name="fecha_final" id="fecha_final" value="{{ old('fecha_final') }}" />

                                        @error('fecha_final')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                  </div>
                                </div>


                                <button type="submit" class="btn btn-success">Generar</button>
                            {!! Form::close() !!}
                        </div>

                        <br>
                        <hr>

    <div class="row">
         <div class="col-md-12 col-sm-12 col-xs-12">
             <div class="x_panel">
               <div class="x_title">
                 <h2>Gestión</h2>
                   <div class="clearfix"></div>
               </div>
               <div class="x_content">
                 <div id="content-management">

                         <!-- start management -->
                   <div class="table-responsive">
                           <table id="management-search-table" class="table table-striped table-bordered" data-page-length="15">
                               <thead>
                                 <tr>
                                 <th>Nombre</th>
                                 <th>Fecha</th>
                                 <th>Tamaño</th>
                                 <th class="text-center">Acción</th>
                                 </tr>
                               </thead>
               <tbody >
                @foreach ($reports as $report)
                  <tr>
                      <td><a href="{{ $report['download'] }}">  {{ $report['name'] }}</a></td>
                      <td>{{ $report['date'] }}</td>
                      <td>{{ $report['size'] }}</td>
                      <td class="text-center">
                        <div class="btn-group tp_btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a href="{{ $report['download'] }}" class="btn-active" target="blank" ><i class="fa fa-download"></i> Descargar</a>
                            </li>
                            <li><a href="#" class="btn-delete" data-action="eliminar" data-id="reporte_roles__202401231101.xlsx" data-name="reporte_roles__202401231101.xlsx"><i class="fa fa-trash"></i> Eliminar</a>
                            </li>
                          </ul>
                        </div>
                      </td>
                  </tr>
              @endforeach
               </tbody>
                           </table>
                       </div>
                         <!-- end user management -->
                   </div>
               </div>
          </div>
       </div>
   </div>

                    </div>
                </div>
            </div>
        </div>
      </section>
    </section>

@stop

<script>
  document.addEventListener('DOMContentLoaded', function () {
      // Seleccionar el formulario por su ID
      var form = document.getElementById('form-users-reports-filter');

      // Obténer los elementos select
      var tipoReporteSelect = form.querySelector('select[name="tipo_reporte"]');
      var tipoReporteEvaluacionSelect = form.querySelector('select[name="tipo_reporte_evaluacion"]');
      var tipoReporteEvaluacionContenedor = document.getElementById('tipo_reporte_evaluacion_contenedor');

      // Función para mostrar u ocultar el campo tipo_reporte_evaluacion
      function toggleTipoReporteEvaluacion() {
          var selectedValue = tipoReporteSelect.value;
          if (selectedValue === '1') { // Evaluaciones Convocatorias
              tipoReporteEvaluacionContenedor.style.display = 'block';
              tipoReporteEvaluacionSelect.setAttribute('required', 'required');
          } else {
              tipoReporteEvaluacionContenedor.style.display = 'none';
              tipoReporteEvaluacionSelect.removeAttribute('required');
          }
      }

      // Inicializa el estado al cargar la página
      toggleTipoReporteEvaluacion();

      // Agrega un evento de cambio al campo tipo_reporte
      tipoReporteSelect.addEventListener('change', toggleTipoReporteEvaluacion);
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      var form = document.getElementById('form-users-reports-filter');
      var fechaInicioInput = form.querySelector('input[name="fecha_inicial"]');
      var fechaFinInput = form.querySelector('input[name="fecha_final"]');

      fechaInicioInput.addEventListener('input', function () {
          // Limitar la fecha final a ser igual o posterior a la fecha de inicio
          fechaFinInput.setAttribute('min', fechaInicioInput.value);
      });

      fechaFinInput.addEventListener('input', function () {
          // Limitar la fecha de inicio a ser igual o anterior a la fecha final
          fechaInicioInput.setAttribute('max', fechaFinInput.value);
      });
  });
</script>
