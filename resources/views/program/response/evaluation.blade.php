@extends('program.program')

@section('program.content')
<div id="response_evaluation_content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Vista <small>Respuestas</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('convocatoria.show', ['convocatoria' => $program]) }}"><i class="fa fa-chevron-left"></i> Atrás</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                    <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                        <li role="group_one" class="active"><a href="#tab_group_one" id="group-one-tab" role="tab" data-toggle="tab" aria-expanded="true">Innovación</a>
                        </li>
                        <li role="group_two"><a href="#tab_group_two" role="tab" id="group-two-tab" data-toggle="tab" aria-expanded="false">Escalabilidad</a>
                        </li>
                        <li role="group_three"><a href="#tab_group_three" id="group-three-tab" role="tab" data-toggle="tab" aria-expanded="true">Tracción</a>
                        </li>
                        <li role="group_four"><a href="#tab_group_four" id="group-four-tab" role="tab" data-toggle="tab" aria-expanded="true">Equipo</a>
                        </li>
                        <li role="group_five"><a href="#tab_group_five" id="group-five-tab" role="tab" data-toggle="tab" aria-expanded="true">Resumen total</a>
                        </li>
                    </ul>
                </div>
                <div id="myTabContent" class="tab-content">
                    <div role="group_one" class="tab-pane fade active in" id="tab_group_one" aria-labelledby="group-one-tab">
                        <div class="well">
                            <p><b>Propiedad Intelectual</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $propiedad_innovacion->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $propiedad_innovacion->texto_opcion_encuesta }}</td>
                                        <td>{{ $propiedad_innovacion->propiedad_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Motivo para emprender</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $motivo_innovacion->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $motivo_innovacion->texto_opcion_encuesta }}</td>
                                        <td>{{ $motivo_innovacion->motivo_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Tipo de innovación</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $tipo_innovacion->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $tipo_innovacion->texto_opcion_encuesta }}</td>
                                        <td>{{ $tipo_innovacion->tipo_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Calificación innovación</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Nombre </b></th>
                                        <th><b> Comentario </b></th>
                                        <th><b> Nota </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_innovacion = 0 ?>
                                    @if (count($calificacion_innovacion) > 0 )
                                        @foreach($calificacion_innovacion as $cal)
                                            <?php $total_innovacion =  $total_innovacion + $cal->total_scoring ?>
                                            <tr>
                                                <td>{{ $cal->nombre_usuario }}</td>
                                                <td>{{ $cal->comentario_resultado_evaluacion }}</td>
                                                <td>{{ $cal->nombre_opc_eval_pregunta }}</td>
                                                <td>{{ $cal->total_scoring }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <p><b>Puntaje Total: {{ $total_innovacion}}</b></p>
                        </div>
                    </div>
                    <div role="group_two" class="tab-pane fade" id="tab_group_two" aria-labelledby="group-two-tab">
                        <div class="well">
                            <p><b>Idioma</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $idioma_escalabilidad->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $idioma_escalabilidad->texto_opcion_encuesta }}</td>
                                        <td>{{ $idioma_escalabilidad->idioma_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Metas</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $metas_escalabilidad->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $metas_escalabilidad->texto_opcion_encuesta }}</td>
                                        <td>{{ $metas_escalabilidad->metas_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Calificacion potencial</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Nombre </b></th>
                                        <th><b> Comentario </b></th>
                                        <th><b> Nota </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_escalabilidad = 0 ?>
                                    @if (count($calificacion_escalabilidad) > 0 )
                                        @foreach($calificacion_escalabilidad as $cal)
                                            <?php $total_escalabilidad =  $total_escalabilidad + $cal->total_score ?>
                                            <tr>
                                                <td>{{ $cal->nombre_usuario }}</td>
                                                <td>{{ $cal->comentario_resultado_evaluacion }}</td>
                                                <td>{{ $cal->nombre_opc_eval_pregunta }}</td>
                                                <td>{{ $cal->total_score }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <p><b>Puntaje Total: {{ $total_escalabilidad}}</b></p>
                        </div>
                    </div>
                    <div role="group_three" class="tab-pane fade" id="tab_group_three" aria-labelledby="group-three-tab">
                        <div class="well">
                            <p><b>Alcance mercado</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $alcance_traccion->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $alcance_traccion->texto_opcion_encuesta }}</td>
                                        <td>{{ $alcance_traccion->alcance_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Cuentas</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $cuentas_traccion->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $cuentas_traccion->texto_opcion_encuesta }}</td>
                                        <td>{{ $cuentas_traccion->cuentas_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Estados financieros</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $estados_traccion->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $estados_traccion->texto_opcion_encuesta }}</td>
                                        <td>{{ $estados_traccion->estados_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Indicadores</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! isset($indicadores_traccion->texto_pregunta_encuesta) ? $indicadores_traccion->texto_pregunta_encuesta : 'No se encontro la pregunta' !!}</td>
                                        <td>{{ isset($indicadores_traccion->texto_opcion_encuesta) ? $indicadores_traccion->texto_opcion_encuesta : 'No se encontro la opcion'}}</td>
                                        <td>{{ isset($indicadores_traccion->indicadores_score) ? $indicadores_traccion->indicadores_score : '0.0' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Calificación traccion</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Nombre </b></th>
                                        <th><b> Comentario </b></th>
                                        <th><b> Nota </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_traccion = 0 ?>
                                    @if (count($calificacion_traccion) > 0 )
                                        @foreach($calificacion_traccion as $cal)
                                            <?php $total_traccion =  $total_traccion + $cal->total_scoring ?>
                                            <tr>
                                                <td>{{ $cal->nombre_usuario }}</td>
                                                <td>{{ $cal->comentario_resultado_evaluacion }}</td>
                                                <td>{{ $cal->nombre_opc_eval_pregunta }}</td>
                                                <td>{{ $cal->total_scoring }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <p><b>Puntaje Total: {{ $total_traccion}}</b></p>
                        </div>
                    </div>
                    <div role="group_four" class="tab-pane fade" id="tab_group_four" aria-labelledby="group-four-tab">
                        <div class="well">
                            <p><b>Experiencia Laboral Lider</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $experiencia_laboral_equipo->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $experiencia_laboral_equipo->texto_opcion_encuesta }}</td>
                                        <td>{{ $experiencia_laboral_equipo->texto_respuesta_encuesta }}</td>
                                        <td>{{ $experiencia_laboral_equipo->laboral_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Experiencia en el Sector</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $experiencia_sector_equipo['texto_pregunta_encuesta'] !!}</td>
                                        <td>{{ $experiencia_sector_equipo['texto_opcion_encuesta'] }}</td>
                                        <td>{{ $experiencia_sector_equipo['texto_respuesta_encuesta'] }}</td>
                                        <td>{{ $experiencia_sector_equipo['sector_scoring'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Experiencia en el Emprendedora</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Respuesta </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $experiencia_emprendedora_equipo['texto_pregunta_encuesta'] !!}</td>
                                        <td>{{ $experiencia_emprendedora_equipo['texto_opcion_encuesta'] }}</td>
                                        <td>{{ $experiencia_emprendedora_equipo['texto_respuesta_encuesta'] }}</td>
                                        <td>{{ $experiencia_emprendedora_equipo['emprendor_scoring'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Roles en el equipo</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Incluye el equipo emprendedor, mencionando brevemente su cargo, rol, experiencia y participación en el emprendimiento (si la tiene):</td>
                                        <td>Habilidades que aportan al emprendimiento</td>
                                        <td>{{ $roles_equipo }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Equipo mixto</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Incluye el equipo emprendedor, mencionando brevemente su cargo, rol, experiencia y participación en el emprendimiento (si la tiene):</td>
                                        <td>Sexo</td>
                                        <td>{{ $genero_equipo }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Riesgo</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $riesgo_equipo->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $riesgo_equipo->texto_opcion_encuesta }}</td>
                                        <td>{{ $riesgo_equipo->riesgo_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Toma de decisiones</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Pregunta </b></th>
                                        <th><b> Opcion </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{!! $decisiones_equipo->texto_pregunta_encuesta !!}</td>
                                        <td>{{ $decisiones_equipo->texto_opcion_encuesta }}</td>
                                        <td>{{ $decisiones_equipo->decisiones_score }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <p><b>Calificación equipo</b></p>
                            <table class="table table-responsive table-bordered">
                                <thead>
                                    <tr>
                                        <th><b> Nombre </b></th>
                                        <th><b> Comentario </b></th>
                                        <th><b> Nota </b></th>
                                        <th><b> Puntaje </b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_equipo = 0 ?>
                                    @if (count($calificacion_equipo) > 0 )
                                        @foreach($calificacion_equipo as $cal)
                                            <?php $total_equipo =  $total_equipo + $cal->total_scoring ?>
                                            <tr>
                                                <td>{{ $cal->nombre_usuario }}</td>
                                                <td>{{ $cal->comentario_resultado_evaluacion }}</td>
                                                <td>{{ $cal->nombre_opc_eval_pregunta }}</td>
                                                <td>{{ $cal->total_scoring }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                            <td>No hay calificacion</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <p><b>Puntaje Total: {{ $total_equipo}}</b></p>
                        </div>
                    </div>
                    <div role="group_five" class="tab-pane fade" id="tab_group_five" aria-labelledby="group-five-tab">
                        <div class="well">
                            <div class="row">
                                <p><b>Criterios de evaluacion</b></p>
                                <table class="table table-responsive table-bordered">
                                    <thead>
                                        <tr>
                                            <th><b> Nombre </b></th>
                                            <th><b> Factor </b></th>
                                            <th><b> Puntaje </b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Innovacion</td>
                                            <td>30%</td>
                                            <td><b>{{ $total_innovacion_res }}</b</td>
                                        </tr>
                                        <tr>
                                            <td>Potencial de escalabilidad</td>
                                            <td>15%</td>
                                            <td><b>{{ $total_escalabilidad_res }}</b</td>
                                        </tr>
                                        <tr>
                                            <td>Tracción de mercado</td>
                                            <td>10%</td>
                                            <td><b>{{ $total_traccion_res }}</b</td>
                                        </tr>
                                        <tr>
                                            <td>Equipo emprendedor</td>
                                            <td>45%</td>
                                            <td><b>{{ $total_equipo_res }}</b</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p>Total: <b> {{ $total_score_res }} - Mínimo para pasar a momento 4 <b> 60 / 100 </b></b></p>
                            </div>
                            <br>
                            {!! Form::open(array('route' => null, 'id' => 'form-key-access', 'class' =>'')) !!}
                                <div class="row">
                                    <p><b>Llaves de acceso</b></p>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                            <p> Capitulo Aldea </p>
                                            <select name="capitulo_aldea" id="capitulo_aldea" class="form-control">
                                                @foreach ($response_keys as $key)
                                                    @if ($key->codigo_opcion_encuesta == $key->option_selected)
                                                        <option value="{{ $key->codigo_opcion_encuesta }}" selected>{{ $key->texto_opcion_encuesta }}</option>
                                                    @else
                                                        <option value="{{ $key->codigo_opcion_encuesta }}">{{ $key->texto_opcion_encuesta }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                            <p>¿Cuál es la ciudad de la sede principal del emprendimiento?</p>
                                            <label> {{ isset($response_keys_city->nombre_ciudad) ? $response_keys_city->nombre_ciudad : 'No se encontro respuesta' }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                            <p>Actividad económica (CIIU) registrada en el Registro Único Tributario</p>
                                            <label> {{ isset($response_keys_ciiu->codigo_sector_interes) ? $response_keys_ciiu->codigo_sector_interes.' - '.$response_keys_ciiu->nombre_sector_interes : 'No se encontro respuesta' }} </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                            <p>Saldo de llave de acceso</p>
                                            <label> {{ $balance_keys->saldo_voucher }} </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success"> Enviar </button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection