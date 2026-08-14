@extends('reports.layout', ['title' => $title, 'type' => 'PDF'])
@section('content')
  <table class="brtable">
    <tbody>
    <tr>
      <th>
        Nombre Empresa:
      </th>
      <td colspan="3"> {{ $company_name_liq }} </td>
    </tr>
    <tr>
      <th>
        Nombre representante legal EMPRESARIO:
      </th>
      <td colspan="3"> {{ $name_rep_leg_purchaser_liq }} </td>
    </tr>
    <tr>
      <th>
        Nombre asesor:
      </th>
      <td colspan="3"> {{ $name_provider_liq }} </td>
    </tr>
    <tr>
      <th>
        Nombre representante Legal ASESOR:
      </th>
      <td colspan="3">{{ $name_rep_leg_seller_liq }}</td>
    </tr>
    <tr>
      <th>
        Nombre Servicio:
      </th>
      <td colspan="3">{{ $product_liq }}</td>
    </tr>
    <tr>
      <th>
        Valor del servicio:
      </th>
      <td colspan="3">{{ money_format('%i', $value_product_liq) }}</td>
    </tr>
    <tr>
      <th>
        Fecha de Inicio (según acuerdo de negociación):
      </th>
      <td colspan="3">{{ $date_initial_liq }}</td>
    </tr>
    <tr>
      <th>
        Fecha de Finalización (según acuerdo de negociación):
      </th>
      <td colspan="3">{{ $data_final_liq }}</td>
    </tr>
    </tbody>
  </table>
  <br>
  <p style="text-align: justify">
    Considerando que {{ $commentary_liq }}.
    Se procede a LIQUIDAR de forma absoluta el acuerdo de negociación suscrito entre {{ $name_rep_leg_purchaser_liq }} y {{ $name_rep_leg_seller_liq }} para la prestación del servicio {{ $product_liq }} sin lugar a reconocimiento del valor contratado a favor de {{ $name_rep_leg_seller_liq }}, y sin lugar a la ejecución de actividades faltantes conforme a lo establecido en el mencionado acuerdo de negociación.
  </p>
  <p>
    La presente fue revisada y aprobada por {{ $official_name_liq }} el día {{ date('d') }} del mes de {{ date('m') }} de {{ date('Y') }}.
  </p>
@stop
