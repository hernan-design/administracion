@extends('layout.layout')

@section('content')

  <section id="report-monitoreo-12-wrapper">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Programas</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @if (session('message'))
              <div class="alert alert-info">
                {{ session('message') }}
              </div>
            @endif
            <div class="well">
              {!! Form::open(['route' => 'reportes.dinamiza.programas.store', 'id' => 'form-programs-dinamiza-reports-filter', 'class' =>'']) !!}
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Entidad</label>
                  <select name="codigo_entidad_programa" id="codigo_entidad_programa" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('reportes.dinamiza.programas.index') }}" data-placeholder="Selecciona una entidad" placeholder="Selecciona una entidad" data-param="list" style="width: 100%">
                  </select>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Etapa</label>
                  <select type="text" name="etapa_programa" id="etapa_programa" class="form-control">
                    <option value="">Seleccione...</option>
                      @foreach ($stages as $stage)
                        <option value="{{ $stage->codigo_etapa_proyecto }}">{{ $stage->nombre_etapa_proyecto }}</option>
                      @endforeach
                  </select>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Categoría</label>
                  <label>Etapa</label>
                  <select type="text" name="categoria_programa" id="categoria_programa" class="form-control">
                    <option value="">Seleccione...</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->codigo_categoria_proceso }}">{{ $category->nombre_categoria_proceso }}</option>
                      @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Fecha inicio</label>
                  <input type="text" name="fecha_inicio_programa" id="fecha_inicio_programa" class="form-control dateDefault"  data-inputmask="'mask':'9999-99-99'">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <label>Fecha final</label>
                  <input type="text" name="fecha_final_program" id="fecha_final_program" class="form-control dateDefault"  data-inputmask="'mask':'9999-99-99'">
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
