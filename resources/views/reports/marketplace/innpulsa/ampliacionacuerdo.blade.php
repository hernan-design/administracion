@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
  <table class="brtable">
    <tbody>
    <tr>
      <th>
        Nombre Empresa:
      </th>
      <td colspan="3"> {{ $company_name }} </td>
    </tr>
    <tr>
      <th>
        Nombre representante legal EMPRESARIO:
      </th>
      <td colspan="3"> {{ $name_rep_leg_purchaser }} </td>
    </tr>
    <tr>
      <th>
        Nombre asesor:
      </th>
      <td colspan="3"> {{ $name_provider }} </td>
    </tr>
    <tr>
      <th>
        Nombre representante Legal ASESOR:
      </th>
      <td colspan="3">{{ $name_rep_leg_seller }}</td>
    </tr>
    <tr>
      <th>
        Nombre Servicio:
      </th>
      <td colspan="3">{{ $product }}</td>
    </tr>
    <tr>
      <th>
        Valor del servicio:
      </th>
      <td colspan="3">{{ money_format('%i', $value_product) }}</td>
    </tr>
    <tr>
      <th>
        Fecha de Inicio (según acuerdo de negociación):
      </th>
      <td colspan="3">{{ $date_initial }}</td>
    </tr>
    <tr>
      <th>
        Fecha de Finalización (según acuerdo de negociación):
      </th>
      <td colspan="3">{{ $data_final }}</td>
    </tr>
    <tr>
      <th>
        Fecha de Finalización (Según ampliación):
      </th>
      <td colspan="3">{{ $data_final_enlarge }}</td>
    </tr>
    </tbody>
  </table>
  <br>
  <p style="text-align: justify">
    Conforme a la presente Acta de Ampliación de acuerdo de negociación se establece que la prestación del servicio tendrá un plazo de finalización hasta la fecha {{ $data_final_enlarge }} conforme con las razones manifestadas por el {{ $name_rep_leg_seller }} y el {{ $name_rep_leg_purchaser }} en las que se establece que: <strong> {{ $commentary }} </strong>.
    El nuevo plazo de ejecución de actividades en ningún caso podrá superar el término del contrato suscrito entre iNNpulsa Colombia y el Asesor.
    De esta manera se establece que los entregables definidos para la prestación del servicio deben ser enviados por parte del {{ $name_rep_leg_seller }} a iNNpulsa Colombia previa aprobación del {{ $name_rep_leg_purchaser }} máximo un mes después (30 días calendario) de la fecha de finalización acordada mediante la presente acta.
  </p>
  <p>
    La presente fue revisada y aprobada por {{ $official_name }} el día {{ date('d') }} del mes de {{ date('m') }} de {{ date('Y') }}.
  </p>
@stop
