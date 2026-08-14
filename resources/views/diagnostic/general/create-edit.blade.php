{{-- views/diagnostic/general/create-edit.blade.php --}}
@extends('diagnostic.general.diagnostic')

@section('diagnostics.content')
  <div class="col-xs-12">
    <div class="x_panel">
      <div class="x_content" id="section-content">
        <div class="" role="tabpanel" data-example-id="togglable-tabs">
          @if(isset($diagnostic))
          <input type="hidden" name="codigo_diagnostico" id="codigo_diagnostico" class="form-control" value="{{ (isset($diagnostic)) ? $diagnostic->codigo_diagnostico : '' }}" required>
            <div class="x_title title">
              <h2><small>Diagnóstico</small></h2>
              <ul class="nav navbar-right panel_toolbox">
								<li>
									<a
										href="{{ route('diagnosticos.index') }}">
										<i class="fa fa-chevron-left"></i>
										Atrás
									</a>
								</li>
							</ul>
              <div class="clearfix"></div>
            </div>
            <ul id="myTab" class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active">
								<a
									href="#tab_diagnostic"
									id="diagnostic-tab"
									role="tab"
									data-toggle="tab"
									aria-expanded="true">
									Diagnóstico
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_madurez"
									role="tab"
									id="madurez-tab"
									data-toggle="tab"
									aria-expanded="false">
									Niveles de Madurez
								</a>
							</li>
							<li role="presentation">
								<a
									href="#tab_unidades"
									role="tab"
									id="unidades-tab"
									data-toggle="tab"
									aria-expanded="false">
									Unidades Productivas
								</a>
							</li>
              <li role="presentation">
								<a
									href="#tab_asignacion"
									role="tab"
									id="asignacion-tab"
									data-toggle="tab"
									aria-expanded="false">
									Asignación
								</a>
							</li>
              @if (count($unidadesProductivas) > 0)
              <li role="presentation">
								<a
									href="#tab_preguntas_up"
									role="tab"
									id="preguntas-up-tab"
									data-toggle="tab"
									aria-expanded="false">
									Preguntas x Unidad Productiva
								</a>
							</li>
              @endif

            </ul>
          @else
            <div class="x_title">
              <h2>Diagnóstico</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li>
                  <a href="{{route('diagnosticos.index')}}"><i class="fa fa-chevron-left"></i> Atrás</a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
          @endif
          <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_diagnostic" aria-labelledby="diagnostic-tab">
              <div class="well">
                {!! Form::open(array('route' => null,'id' => 'form-diagnostic', 'class' =>'form-horizontal', 'data-parsley-validate'=>'')) !!}
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <label>Nombre <span class="required">*</span></label>
											<input
												type="text"
												name="nombre_diagnostico"
												id="nombre_diagnostico"
												class="form-control"
												value="{{ (isset($diagnostic)) ? $diagnostic->nombre_diagnostico : '' }}"
												required
												data-parsley-maxlength="100"
											>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <label>Formulario <span class="required">*</span></label>
                      <select type="text" name="codigo_encuesta" id="codigo_encuesta" class="form-control slc-calendar" required>
                        <option value="">Seleccione...</option>
                        @foreach ($formulario as $item)
                          <option value="{{ $item->codigo_encuesta }}" @if(isset($diagnostic) && $item->codigo_encuesta == $diagnostic->codigo_encuesta) selected @endif >{{ $item->nombre_encuesta }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-xs-12 col-md-12 ">
											<label>Descripción <span class="required">*</span></label>
											<textarea name="descripcion_diagnostico" id="descripcion_diagnostico" class="form-control" data-parsley-maxlength="255" value="{{ isset($diagnostic) ? $diagnostic->descripcion_diagnostico : '' }}" required>{{ isset($diagnostic) ? $diagnostic->descripcion_diagnostico : '' }}</textarea>
										</div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
                      <button type="submit" class="btn btn-success btn-block">{{ (isset($diagnostic)) ? 'Editar' : 'Guardar' }}</button>
                    </div>
                  </div>
                {!! Form::close() !!}
              </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_madurez" aria-labelledby="home-madurez">
              <div id="wrapper-render-madurez">
                {!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-madurez-diagnostic', 'data-parsley-validate' => '') ) !!}
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="form-group">
                        <fieldset class="well panel-section-form" name="fields_madurez-diagnostic" id="fields_madurez-diagnostic">
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12 col-xs-12" >
                                <div class="x_panel">
                                  <div class="row">
                                    <div class="col-md-12 col-md-offset-5 ">
                                      <button type="button" class="btn btn-success btn-create btn-sm btn-create-madurez" data-title="Agregar Nivel de Madurez" title="Agregar Nivel de Madurez"> Agregar</button>
                                    </div>
                                      <p style="margin-bottom: 0;">Los niveles de madurez deben ir en rango de porcentaje, teniendo en cuenta un minimo y un maximo por cada uno y asi llegar a un total de 100%</p>
                                  </div>
                                  <br>
                                  <div class="progress">
                                    <div class="progress-bar" id="progress-bar-madurez" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-12" >
                                <div class="x_panel">
                                    <div class="table-responsive">
                                      <table id="madurez-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                                        <thead>
                                          <tr class="headings">
                                            <th class="column-title">Nombre</th>
                                            <th class="column-title">Descripción</th>
                                            <th class="column-title">Rango Mínimo(%)</th>
                                            <th class="column-title">Rango Máximo(%)</th>
                                            <th class="column-title">Estado</th>
                                            <th class="column-title text-center" style="width: 0px;">Acción</th>
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
                          </div>
                        </fieldset>
                      </div>

                    </div>
                  </div>

                {!! Form::close() !!}
              </div>

            </div>

            <div role="tabpanel" class="tab-pane fade" id="tab_unidades" aria-labelledby="home-unidades">
              <div id="wrapper-render-unidades">
                {!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-unidades-diagnostic', 'data-parsley-validate' => '') ) !!}
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="form-group">
                        <fieldset class="well panel-section-form" name="fields_unidades-diagnostic" id="fields_unidades-diagnostic">
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-12 col-xs-12" >
                                <div class="x_panel">
                                  <div class="row">
                                    <div class="col-md-7 col-md-offset-5 ">
                                      <button type="button" class="btn btn-success btn-create btn-sm btn-create-unidades" data-title="Agregar Unidades Productivas" title="Agregar Unidades Productivas"> Agregar</button>
                                    </div>
                                    <p style="margin-bottom: 0;">Las unidades productivas deben tener un valor en porcentaje (0-100), teniendo en cuenta que la suma de todas debe llegar a un máximo de 100%</p>
                                  </div>
                                  <br>
                                  <div class="progress">
                                    <div class="progress-bar" id="progress-bar-unidades" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-12" >
                                <div class="x_panel">
                                    <div class="table-responsive">
                                      <table id="unidades-list-search-table" class="table table-striped table-bordered" data-page-length="15">
                                        <thead>
                                          <tr class="headings">
                                            <th class="column-title text-center">Nombre</th>
                                            <th class="column-title text-center">Descripción</th>
                                            <th class="column-title text-center">Porcentaje(%)</th>
                                            <th class="column-title text-center">Estado</th>
                                            <th class="column-title text-center" style="width: 0px;">Acción</th>
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
                          </div>
                        </fieldset>
                      </div>

                    </div>
                  </div>

                {!! Form::close() !!}
              </div>

            </div>

            <div role="tabpanel" class="tab-pane fade" id="tab_asignacion" aria-labelledby="home-asignacion">
              <div id="wrapper-render-asignacion">
                {!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-asignacion-diagnostic', 'data-parsley-validate' => '') ) !!}
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group">
                      <fieldset class="well panel-section-form" name="fields_asignacion-diagnostic" id="fields_asignacion-diagnostic">
                        <div class="form-group">
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="form-group">
                                @if (isset($filteredMenuInstitucion))
                                  @foreach($filteredMenuInstitucion as $module)
                                  <div class="checkbox">
                                      <label>
                                          <input type="checkbox" name="modules[]" value="{{ $module->codigo_menu_institucion }}"
                                                {{ in_array($module->codigo_menu_institucion, $modulosDiagnostico->pluck('codigo_menu_institucion')->toArray()) ? 'checked' : '' }}>
                                          {{ $module->nombre_menu_institucion }}
                                      </label>
                                  </div>
                                @endforeach
                                @endif

                                <button type="button" class="btn btn-success btn-create btn-sm btn-create-asignacion"
                                        data-title="Asignar Módulos" title="Asignar Módulos"> Asignar</button>
                            </div>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                </div>

                {!! Form::close() !!}
              </div>
            </div>



            <div role="tabpanel" class="tab-pane fade" id="tab_preguntas_up" aria-labelledby="home-preguntas-up">
              <div id="wrapper-render-preguntas-up">
                  {!! Form::open( array('action' => null, 'data-abide' => '', 'id' => 'form-add-preguntas-up', 'data-parsley-validate' => '') ) !!}
                  <div class="row">
                      <div class="col-xs-12">
                          <div class="form-group">
                              <fieldset class="well panel-section-form" name="fields_preguntas-up" id="fields_preguntas-up">
                                  <div class="form-group">
                                      <div class="row">
                                          <div class="col-sm-12">
                                              <span>
                                                  <b>
                                                      Seleccione
                                                  </b>
                                              </span>
                                          </div>
                                          <br>
                                          <br>

                                          @if (isset($unidadesProductivas))
    @foreach($unidadesProductivas as $unidad)
        <div class="col-sm-12">
            <div class="form-group">
                <label for="unidad">{{ $unidad->nombre_unidad_productiva }}</label>
                <br>
                <select name="{{ 'unidad_' . $unidad->codigo_unidad_productiva }}" id="{{ 'unidad' . $unidad->codigo_unidad_productiva }}" class="unidad-select" multiple>
                    @foreach($preguntasFormulario as $pregunta)
                        @php
                            // Verificar si la pregunta está en preguntasUnidadProductiva
                            $isSelected = $preguntasUnidadProductiva->contains(function ($value) use ($unidad, $pregunta) {
                                return $value->codigo_unidad_productiva == $unidad->codigo_unidad_productiva && $value->codigo_pregunta_encuesta == $pregunta->codigo_pregunta_encuesta;
                            });
                        @endphp
                        <option value="{{ $pregunta->codigo_pregunta_encuesta }}" {{ $isSelected ? 'selected' : '' }}>
                            {{ '- ' . $pregunta->texto_pregunta_encuesta }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <br>
        <br>
    @endforeach
@endif

                                      </div>
                                      <br>
                                      <br>
                                      <button type="button" class="btn btn-success btn-create btn-sm btn-create-preguntas-up"
                                              data-title="Asignar Preguntas" title="Asignar Preguntas"> Guardar</button>
                                  </div>
                              </fieldset>
                          </div>
                      </div>
                  </div>

                  {!! Form::close() !!}

                  <!-- Mostrar unidades productivas y sus preguntas seleccionadas -->
                  <div class="row">
                      <div class="col-xs-12">
                          <div class="form-group">
                              <fieldset class="well panel-section-form">
                                <h3>Preguntas por unidad productiva</h3>
                                  <div class="form-group">
                                      <div class="row">
                                          @if (isset($unidadesProductivas))
                                              @foreach($unidadesProductivas as $unidad)
                                                  <div class="col-sm-12">
                                                      <h4>{{ $unidad->nombre_unidad_productiva }}</h4>
                                                      <ul>
                                                          @foreach($preguntasUnidadProductiva->where('codigo_unidad_productiva', $unidad->codigo_unidad_productiva) as $preguntaUnidad)
                                                              <li>{{ $preguntasFormulario->firstWhere('codigo_pregunta_encuesta', $preguntaUnidad->codigo_pregunta_encuesta)->texto_pregunta_encuesta }}</li>
                                                          @endforeach
                                                      </ul>
                                                  </div>
                                                  <br>
                                                  <br>
                                              @endforeach
                                          @endif
                                      </div>
                                  </div>
                              </fieldset>
                          </div>
                      </div>
                  </div>

              </div>
          </div>













          </div>
        </div>
      </div>
    </div>
  </div>

  {{--  Template create madurez --}}
	<script type="text/template" id="create-madurez-diagnostic-tpl">
		<div class="row">
      <input type="hidden" name="codigo_diagnostico" id="codigo_diagnostico" class="form-control" value="{{ (isset($diagnostic)) ? $diagnostic->codigo_diagnostico : '' }}" required>
			<div class="col-md-4 col-sm-4 col-xs-12">
				<label>Nombre <span class="required">*</span></label>
				<input type="text" name="nombre_nivel_madurez" id="nombre_nivel_madurez" class="form-control" value="<%- typeof nombre_nivel_madurez != 'undefined' && !_.isEmpty(nombre_nivel_madurez) ? nombre_nivel_madurez : '' %>" required>
			</div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <label>Rango Mínimo <span class="required">*</span></label>
        <div class="input-group">
          <input type="number" name="rango_minimo" id="rango_minimo" class="form-control" min="0" max="100" required>
          <span class="input-group-addon">%</span>
        </div>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <label>Rango Máximo <span class="required">*</span></label>
        <div class="input-group">
          <input type="number" name="rango_maximo" id="rango_maximo" class="form-control" min="0" max="100" required>
          <span class="input-group-addon">%</span>
        </div>
      </div>
      <div class="col-xs-12 col-md-12 ">
        <label>Descripción <span class="required">*</span></label>
        <textarea name="descripcion_nivel_madurez" id="descripcion_nivel_madurez" class="form-control" data-parsley-maxlength="255" required></textarea>
      </div>
		</div>
	</script>

   {{-- buttons acction --}}
	<script type="text/template" id="buttons-action-tpl">
    <div class="btn-group tp_btn-group">
      <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
      <ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li>
					<a href="" title="Editar Nivel de Madurez" class="btn-edit-madurez" data-madurez="<%- JSON.stringify(full) %>"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
			</ul>
    </div>
  </script>

  <script type="text/template" id="buttons-action-tpl-unidades">
    <div class="btn-group tp_btn-group">
      <button type="button" class="btn btn-default dropdown-toggle" title="Opciones" data-toggle="dropdown" aria-expanded="false">
				<i class="fa fa-ellipsis-v"></i>
			</button>
      <ul class="dropdown-menu dropdown-menu-right" role="menu">
				<li>
					<a href="" title="Editar Unidad Productiva" class="btn-edit-unidad" data-unidad="<%- JSON.stringify(full) %>"><i class="fa fa-pencil"></i> Editar
					</a>
				</li>
			</ul>
    </div>
  </script>


   {{--  Template create madurez --}}
	<script type="text/template" id="create-unidad-diagnostic-tpl">
		<div class="row">
      <input type="hidden" name="codigo_diagnostico" id="codigo_diagnostico" class="form-control" value="{{ (isset($diagnostic)) ? $diagnostic->codigo_diagnostico : '' }}" required>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<label>Nombre <span class="required">*</span></label>
				<input type="text" name="nombre_unidad_productiva" id="nombre_unidad_productiva" class="form-control" value="<%- typeof nombre_unidad_productiva != 'undefined' && !_.isEmpty(nombre_unidad_productiva) ? nombre_unidad_productiva : '' %>" required>
			</div>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <label>Porcentaje <span class="required">*</span></label>
        <div class="input-group">
          <input type="number" name="porcentaje" id="porcentaje" class="form-control" required>
          <span class="input-group-addon">%</span>
        </div>
      </div>
      <div class="col-xs-12 col-md-12 ">
        <label>Descripción <span class="required">*</span></label>
        <textarea name="descripcion_unidad_productiva" id="descripcion_unidad_productiva" class="form-control" data-parsley-maxlength="255" required></textarea>
      </div>
		</div>
	</script>
  <!-- Incluir jQuery (necesario para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
      function adjustSelectHeight(selectElement) {
          var selectContainer = selectElement.next('.select2-container');
          var selectWidth = selectContainer.width();
          var selectedOptions = selectElement.find('option:selected');
          var totalTextWidth = 0;

          selectedOptions.each(function() {
              var text = $(this).text();
              var tempSpan = $('<span>').text(text).css({
                  'visibility': 'hidden',
                  'white-space': 'nowrap',
                  'position': 'absolute'
              }).appendTo('body');
              totalTextWidth += tempSpan.width();
              tempSpan.remove();
          });

          var rowHeight = 36; // Altura de cada fila de opción seleccionada (puede ajustarse)
          var minHeight = 36; // Altura mínima del select
          var maxHeight = 200; // Altura máxima del select (opcional)
          var rows = Math.ceil(totalTextWidth / selectWidth);
          var newHeight = Math.min(maxHeight, Math.max(minHeight, rowHeight * rows));

          selectContainer.find('.select2-selection').css('height', 'auto');
      }

      $('.unidad-select').each(function() {
          var selectElement = $(this);
          selectElement.select2({
              width: '100%',
              allowClear: true
          }).on('change', function() {
              adjustSelectHeight(selectElement);
          });


          // Ajustar la altura inicialmente
          adjustSelectHeight(selectElement);
      });
  });
</script>


<style>
    .select2-container .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        max-height: none;
        overflow-y: auto;
    }

    .select2-container .select2-selection--multiple .select2-selection__choice {
        margin: 2px 5px;
    }

    .select2-container .select2-selection--multiple {
        min-height: 36px; /* Ajustar según sea necesario */
    }
</style>

@stop
