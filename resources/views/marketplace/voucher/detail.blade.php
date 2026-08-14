{{-- views/marketplace/voucher/detail.blade.php --}}
@extends('marketplace.voucher.voucher')

@section('voucher.detail.content')
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Vochers <small></small></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li>
						<a href="{{ route('marketplace.vouchers.index') }}"><i class="fa fa-chevron-left"></i> Atrás</a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">

				<section id="marketplace-voucher-info" class="panel">
					<div class="well">
						<h3><strong>{{ $detalleVoucher->nombre_emprendimiento }}</strong></h3>
						<dl class="">
							<dt>Nombre</dt>
							<dd>
								<p>{{ $detalleVoucher->nombre_usuario }}</p>
							</dd>
							<dt>Valor</dt>
							<dd>
								<p>$ {{ number_format($detalleVoucher->valor_detalle_voucher,0,',','.') }}</p>
							</dd>
							<dt>Saldo</dt>
							<dd>
								<p>$ {{ number_format($detalleVoucher->saldo_detalle_voucher,0,',','.') }}</p>
							</dd>
						</dl>
					</div>
					<div id="wrapper-voucher-detail" class="table-responsive">
						<table id="data-marketplace-voucher-detail" class="table table-striped jambo_table bulk_action">
							<thead>
								<tr>
									<th colspan="6" class="text-center">Compras realizadas</th>
								</tr>
								<tr>
									<th>Codigo compra</th>
									<th>Producto</th>
									<th>Valor</th>
									<th>Fecha y hora</th>
									<th>Estado</th>
                  <th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								{{-- Render template voucher member - id:marketplace-voucher-purchase-tpl --}}
							</tbody>
						</table>
						@include('public.noresults', ['message' => 'No hay compras'])
					</div>
				</section>
			</div>
		</div>
	</div>

	{{-- template marketplace voucher purchase --}}
	<script type="text/template" id="marketplace-voucher-purchase-tpl">
		<td><%- codigo_compra %></td>
		<td><a href="<%- window.Misc.urlFull( Route.route('marketplace.show', {marketplace: url_producto }) ) %>" title="Ver producto"><%- nombre_producto %></a></td>
		<td><%- window.copCurrencyF.to(parseFloat(valor_total_compra)) %></td>
		<td><%- fecha_compra %> <%- hora_compra %></td>
		<td><%- state_purchase_name %></td>
    <td>
      <div class="btn-group tp_btn-group">
        <% if (state_purchase != window._tp.getState['removed']) { %>
          <a href="#" class="cancelTransaction" data-purchase="<%- codigo_compra %>" title="Cancelar transaccion">Cancelar Transaccion</a>
        <% } else { %>
          <p> Transaccion Cancelada </p>
        <% } %>
      </div>
    </td>
	</script>

  {{-- Template modal confirm --}}
  <script type="text/template" id="confirm-remove-transaction-tpl">
    <p>¿ Estás seguro de cancelar el transacción ?</p>
  </script>
@stop
