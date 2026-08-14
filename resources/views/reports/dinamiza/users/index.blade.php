@extends('layout.layout')

@section('content')

	<section id="report-monitoreo-12-wrapper">
		<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
	                <div class="x_title">
	                	<h2>Usuarios</h2>
	                    <div class="clearfix"></div>
	                </div>
	                <div class="x_content">
	                	@if (session('message'))
						    <div class="alert alert-info">
						        {{ session('message') }}
						    </div>
						@endif
					  	<div class="well">
                            {!! Form::open(['route' => 'reportes.dinamiza.usuarios.store', 'id' => 'form-users-dinamiza-reports-filter', 'class' =>'']) !!}
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Programas</label>
                                        <select name="codigo_convocatoria" id="codigo_convocatoria" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('convocatoria.index') }}" data-placeholder="Selecciona un programa" placeholder="Selecciona un programa" data-param="list" style="width: 100%">
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Fecha de nacimiento</label>
                                        <input type="text" name="fecha_nacimiento_usuario" id="fecha_nacimiento_usuario" class="form-control dateDefault"  data-inputmask="'mask':'9999-99-99'">
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Nivel de educación</label>
                                        <select type="text" name="codigo_nivel_educacion" id="codigo_nivel_educacion" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level->codigo_nivel_educacion }}" >{{ $level->nombre_nivel_educacion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Ocupación actual</label>
                                        <select type="text" name="codigo_lista_ocupacion" id="codigo_lista_ocupacion" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($employment as $value)
                                                <option value="{{ $value->codigo_lista_ocupacion }}">{{ $value->nombre_lista_ocupacion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Ciudad</label>
                                        <select name="codigo_ciudad_usuario" id="codigo_ciudad_usuario" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Seleccione..."  placeholder="Seleccione..." style="width: 100%">
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Comuna/Corregimiento</label>
                                        <select type="text" name="codigo_localidad_ciudad" id="codigo_localidad_ciudad" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->text }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Barrio</label>
                                        <select type="text" name="codigo_barrio_localidad" id="codigo_barrio_localidad" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($zone as $value)
                                                <option value="{{ $value->id }}">{{ $value->text }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Estrato</label>
                                        <select type="text" name="codigo_estrato" id="codigo_estrato" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($stratum as $value)
                                                <option value="{{ $value->codigo_estrato }}">{{ $value->nombre_estrato }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Tipo de vivienda</label>
                                        <select type="text" name="codigo_tipo_vivienda" id="codigo_tipo_vivienda" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($housingType as $value)
                                                <option value="{{ $value->codigo_tipo_vivienda }}">{{ $value->nombre_tipo_vivienda }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Cabeza de familia</label>
                                        <select type="text" name="jefe_hogar" id="jefe_hogar" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <option value="true">Sí</option>
                                            <option value="false">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Sector donde trabaja</label>
                                        <select type="text" name="codigo_sector_trabajo" id="codigo_sector_trabajo" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($workSector as $value)
                                                <option value="{{ $value->codigo_sector_trabajo }}">{{ $value->nombre_sector_trabajo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Tiene empresa o unidad productiva</label>
                                        <select type="text" name="enterprise" id="enterprise" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <option value="true">Sí</option>
                                            <option value="false">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Autodenominación</label>
                                        <select type="text" name="codigo_autodenominacion" id="codigo_autodenominacion" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($selfDomination as $value)
                                                <option value="{{ $value->codigo_autodenominacion }}">{{ $value->nombre_autodenominacion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Grupo poblacional</label>
                                        <select type="text" name="codigo_grupo_poblacional" id="codigo_grupo_poblacional" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($populationGroup as $value)
                                                <option value="{{ $value->codigo_grupo_poblacional }}">{{ $value->nombre_grupo_poblacional }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Grupo comunitario</label>
                                        <select type="text" name="codigo_grupo_comunitario" id="codigo_grupo_comunitario" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($communityGroup as $value)
                                                <option value="{{ $value->codigo_grupo_comunitario }}">{{ $value->nombre_grupo_comunitario }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Grupo social</label>
                                        <select type="text" name="codigo_grupo_social" id="codigo_grupo_social" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($socialGroup as $value)
                                                <option value="{{ $value->codigo_grupo_social }}">{{ $value->nombre_grupo_social }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label>Discapacidad</label>
                                        <select type="text" name="discapacidad" id="discapacidad" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <option value="true">Sí</option>
                                            <option value="false">No</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
							    <div class="row">
							    	<div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
	                      				<button type="submit" class="btn btn-block btn-success">Generar</button>
									</div>
                    			</div>
							{!! Form::close() !!}
                		</div>
	           		</div>
        		</div>
    		</div>
		</div>
	</section>

@stop
