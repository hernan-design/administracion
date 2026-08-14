@extends('emails.layout')

@section('content')
  <p style="text-align:center">
    El usuario {{ $_name_person }} canceló la transacción {{ $_purchase }}  {{ $_name_product }} realizada entre {{ $_name_project }} y el asesor {{ $_name_seller }}. Se reintegran {{ $_value_purchase }} en llaves de acceso al empresario.
  </p>
@stop
