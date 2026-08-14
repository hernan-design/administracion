@extends('layout.layout')

@section('content')

  <section id="report-monitoreo-12-wrapper">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Reporte general mentorias</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @if (session('message'))
              <div class="alert alert-info">
                {{ session('message') }}
              </div>
            @endif
            <div class="well">
              {!! Form::open(['route' => 'reportes.mentorias.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
              <div class="row">
                <div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12  text-center">
                  <div class="form-group">
                    <p>Fecha inicial</p>
                    <input type="date"  class="x_panel @error('fecha_inicial') is-invalid @enderror" name="fecha_inicial" id="fecha_inicial" value="{{ old('fecha_inicial') }}" />

                    @error('fecha_inicial')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <p>Fecha final</p>
                    <input  type="date"  class="x_panel @error('fecha_final') is-invalid @enderror" name="fecha_final" id="fecha_final" value="{{ old('fecha_final') }}" />

                    @error('fecha_final')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
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
