@extends('layout.layout')

@section('content')

  <section id="report-monitoreo-12-wrapper">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Reporte evaluaciones</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            @if (session('message'))
              <div class="alert alert-info">
                {{ session('message') }}
              </div>
            @endif
            <div class="well">
              {!! Form::open(['route' => 'reportes.evaluaciones.store','id' => 'form-assignment-filter', 'class' =>'']) !!}
              <div class="col-md-4 col-sm-4 col-md-offset-4 col-xs-12">
                <label> Encuesta <span class="required">*</span></label>
                <select name="slc_survey" id="slc_survey" class="form-control" required>
                  <option value="" selected> Seleccione... </option>
                  @if(isset($evaluations))
                    @foreach($evaluations as $eval)
                      <option value="{{ $eval->id }}"> {{ $eval->text }} </option>
                    @endforeach
                  @endif
                </select>
                <br>
              </div>
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
