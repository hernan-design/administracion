@extends('reports.layout', [
                                'title' => 'LA AGENCIA DE EMPLEO Y FOMENTO EMPRESARIAL COMPENSAR',
                                'type' => 'PDF'
                            ])

@section('content')
  <br>
  <p>
    <h2 class="center">
      <b> CERTIFICA </b>
    </h2>
  </p>
  <div class="size-12">
    <p style="text-align: justify">
      Que {{ strtoupper($nombre_usuario_completo) }} con {{ strtolower($nombre_tipo_identificacion) }} {{ $identificacion_usuario }},
      participó en el taller {{ strtoupper($nombre_evento) }}
      de la Ruta de Emprendimiento realizado

      @if ($fecha_evento == $fecha_fin_evento)
        el {{ $fecha_evento }}
      @elseif ($fecha_evento != $fecha_fin_evento)
        del {{ $fecha_evento }} al {{ $fecha_fin_evento }}
      @endif

      con una intensidad de {{ $cantidad_horas }} horas.
    </p>
    <p style="text-align: justify">{{ $resumen_evento }}</p>
    <p style="text-align: justify; margin-top: 15%;">
      Se expide en Bogotá, a los ({{ date("d") }}) días del mes de {{ $nombre_mes }} de {{ date('Y') }}. 
    </p>
    <img
      src="{{ $firma_gerente }}"
      alt="Firma Digital"
    />
    <p>
      <b>
        {{ strtoupper($nombre_gerente) }}<br>
        Gerente<br>
        Agencia de Empleo y Fomento Empresarial
      </b>
    </p>
    <p>
      Código: {{ $usuario_evento }}
    </p>
  </div>
@stop
