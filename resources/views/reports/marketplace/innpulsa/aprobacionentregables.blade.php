@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
 <p>
   Por medio de la presente se informa que se han recibido y revisado los entregables presentados por
   {{ $name_seller }} correspondiente al siguiente servicio en el marco del programa Aldea:
 </p>
  <table class="brtable">
    <tbody>
      <tr>
        <th width="30%">
          Nombre del asesor
        </th>
        <td colspan="3"> {{ $name_seller }} </td>
      </tr>
      <tr>
        <th>
          Nombre representante legal Asesor
        </th>
        <td colspan="3"> {{ $rep_seller }} </td>
      </tr>
      <tr>
        <th>
          Nombre servicio
        </th>
        <td colspan="3"> {{ $name_product }} </td>
      </tr>
      <tr>
        <th>
          Precio del servicio
        </th>
        <td colspan="3"> {{ $price_product }} </td>
      </tr>
      <tr>
        <th>
          Fecha inicio servicio
        </th>
        <td>{{ $date_init_product }}</td>
        <th class="center">
          Fecha finalización servicio
        </th>
        <td class="center"> {{ $date_approval }} </td>
      </tr>
    </tbody>
  </table>
  <br>
  <table class="brtable">
    <tbody>
    <tr>
      <th width="30%">
        Nombre de la empresa
      </th>
      <td colspan="3"> {{ $name_company }} </td>
    </tr>
    <tr>
      <th>
        Nombre Empresario
      </th>
      <td colspan="3"> {{ $name_buyer }} </td>
    </tr>
    <tr>
      <th>
        Ciudad
      </th>
      <td colspan="3"> {{ $city_company }} </td>
    </tr>
    </tbody>
  </table>
  <p>
    En cumplimiento del proceso establecido de aprobación de entregables, el pasado {{  $date_approve_deliverables_asesor }} se realizó por parte de {{ $name_inst }} la aprobación de los siguientes entregables:
  </p>
  <p>
    <strong>LISTA DE ENTREGABLES APROBADOS</strong>
  </p>
  <ol>
    @foreach($deliverables as $deliverable)
        <li class="justify">
          <a href="{{ $deliverable->direccion_adjunto }}" id="{{ $deliverable->codigo_adjunto }}"> {{ $deliverable->descripcion_entregable }} </a>
        </li>
    @endforeach
    <li class="justify">
      <a href="{{ $deliverable_final->direccion_adjunto }}" id="{{ $deliverable_final->codigo_adjunto }}">Informe final</a>
    </li>
  </ol>
  <p>
    <strong>RESULTADO EVALUACIONES</strong>
  </p>
  <ol>
    <li> Resultado evaluacion comprador {{ $result_evaluation_buyer }}</li>
    <li> Resultado evaluacion vendedor {{ $result_evaluation_seller }}</li>
  </ol>
  <br>
 <p>
   <strong>APROBADO POR:</strong>
 </p>
 <p>
   {{ $rep_seller }} el {{ $date_upload_deliverables_seller }}
 </p>
 <p>
   {{ $name_buyer }} el {{ $date_approve_deliverables_buyer }}
 </p>

 <br>
  <p>
    Se expide la presente el día {{ date('d') }} del mes de {{ date('m') }} de {{ date('Y') }}.
  </p>
  <br>
  <p><b>Nota Aclaratoria:</b> La siguiente acta no es válida para radicar factura.</p>
@stop
