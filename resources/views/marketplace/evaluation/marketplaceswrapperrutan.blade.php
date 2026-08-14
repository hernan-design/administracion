<section id="marketplaces-wrapper">
    <section id="marketplaces-evaluation-content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Detalle <small>Evaluación</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a href="{{ route('marketplace.transacciones.index') }}"><i
                                        class="fa fa-chevron-left"></i> Atrás</a>
                            </li>
                        </ul>
                        @if ($purchase->acuerdo_producto)
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <h4 class="agreement_name_product">{{ $purchase->nombre_producto }}</h4>
                                    <strong class="text-uppercase">Vendedor:</strong> {{ $purchase->nombre_vendedor }}
                                    <br>
                                    <strong class="text-uppercase">Comprador: </strong>
                                    {{ $purchase->nombre_comprador }}
                                    <br>
                                    <strong class="text-uppercase">Fecha Compra: </strong> {{ $purchase->fecha_compra }}
                                    <br>
                                    @if ($purchase->fecha_acuerdo_aceptacion_compra)
                                        <strong class="text-uppercase">Fecha aceptación: </strong>
                                        {{ $purchase->fecha_acuerdo_aceptacion_compra }}
                                        <br>
                                    @endif
                                    <strong class="text-uppercase">Valor Compra: </strong> $
                                    {{ number_format($purchase->valor_total_compra, 0, '.', '.') }}
                                    <br>
                                    {{-- <strong class="text-uppercase"> Puntaje de la Compra: </strong>
                                    {{ isset($proveedor->evaluacioncompra->promedio_evaluacion) ? $proveedor->evaluacioncompra->promedio_evaluacion : '0' }} --}}
                                </div>
                            </div>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                            <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                @if ($purchase->acuerdo_producto)
                                    <li role="presentation" class="active">
                                        <a href="#tab_agreement_deliverables" role="tab" id="deliverables-tab"
                                            data-toggle="tab" aria-expanded="true">Entregables</a>
                                    </li>
                                @endif
                                <li role="presentation" class="{{ !$purchase->acuerdo_producto ? 'active' : '' }}">
                                    <a href="#tab_agreement_evaluacion" role="tab" id="evaluation-tab"
                                        data-toggle="tab"
                                        aria-expanded="true">{{ transtp('evaluation.tabs.evaluation') }}</a>
                                </li>
                                {{-- <li role="presentation">
                                    <a href="#tab_agreement_proveedor" role="tab" id="provider-tab"
                                        data-toggle="tab"
                                        aria-expanded="false">{{ transtp('evaluation.tabs.provider') }}</a>
                                </li> --}}
                                {{-- <li role="presentation">
                                    <a href="#tab_agreement_comprador" role="tab" id="purchaser-tab"
                                        data-toggle="tab"
                                        aria-expanded="false">{{ transtp('evaluation.tabs.entreprenaur') }}</a>
                                </li> --}}
                                {{-- @if ($purchase->codigo_estado == \App\Models\Core\Estado::getState('finalized'))
                                    <li role="presentation">
                                        <a href="#tab_agreement_bill" role="tab" id="bill-tab" data-toggle="tab"
                                            aria-expanded="false">{{ transtp('evaluation.tabs.bill') }}</a>
                                    </li>
                                @endif --}}
                            </ul>
                            <div id="myTabContent" class="tab-content">
                                <div role="tabpanel"
                                    class="tab-pane fade {{ $purchase->acuerdo_producto ? 'active in' : '' }}"
                                    id="tab_agreement_deliverables" aria-labelledby="profile-tab">
                                    <div id="wrapper-sale-agreement-deliverables" class="panel panel-default">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="entregables">
                                                <!-- Table deliverable agreement-->
                                                {!! Form::open([
                                                    'route' => null,
                                                    'id' => 'form-entregables',
                                                    'class' => 'form-horizontal',
                                                ]) !!}
                                                <div class="row">
                                                    <div class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped jambo_table bulk_action">
                                                                <!-- data table table-striped no-margin-->
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan="100%" class="text-center">
                                                                            Entregables
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Acciones</th>
                                                                        <th class="text-left">Nombre</th>
                                                                        <th class="text-left">Fecha</th>
                                                                        <th class="text-right">Adjuntos</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if (count($entregables))
                                                                        <input
                                                                            type="hidden"
                                                                            name="codigo_acuerdo_compra"
                                                                            id="codigo_acuerdo_compra"
                                                                            value="{{ $entregables[0]->codigo_acuerdo_compra }}"
                                                                            />
                                                                    @endif
                                                                    @foreach ($entregables as $entregable)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="checkbox-inline">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            name="entregables"
                                                                                            id="{{ $entregable->codigo_entregable_acuerdo }}"
                                                                                            value="{{ $entregable->codigo_entregable_acuerdo }}"
                                                                                            class="flat"
                                                                                            @if ($entregable->codigo_estado == 7)
                                                                                                checked="true"
                                                                                            @endif
                                                                                            >
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-justify text-left">
                                                                                {{ $entregable->descripcion_entregable }}
                                                                            </td>
                                                                            <td class="text-justify text-left">
                                                                                {{ $entregable->fecha_ejecucion_entregables }}
                                                                            </td>
                                                                            <td class="text-right">
                                                                                @if ($entregable->direccion_adjunto != null)
                                                                                    <a
                                                                                        href="{{ $entregable->direccion_adjunto }}">
                                                                                        {{ $entregable->nombre_adjunto }}
                                                                                        <span
                                                                                            class="glyphicon glyphicon-download-alt"
                                                                                            aria-hidden="true"></span>
                                                                                    </a>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    @if (isset($agreement->codigo_estado) && $agreement->codigo_estado == 6)
                                                        <div class="col-md-2 col-md-offset-5 col-sm-4 col-sm-offset-4 col-xs-12">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-success btn-block"
                                                                id="form-entregables-submit"
                                                                >Guardar
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                                {!! Form::close() !!}
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                                        <div id="wrapper-action-deliverables">
                                                            {{-- render template id="button-action-deliverables-tpl" --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel"
                                    class="tab-pane fade {{ !$purchase->acuerdo_producto ? 'active in' : '' }}"
                                    id="tab_agreement_evaluacion" aria-labelledby="profile-tab">
                                    <div id="wrapper-sale-agreement-discussion" class="panel panel-default">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="evaluacion">
                                                <div class="form-group tp_form-group">
                                                    <div class="row">
                                                        <div
                                                            class="col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
                                                            <div
                                                                class="panel-marketplace-purchase-evaluation panel-content radius">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="wrap-icon-marketplace text-center">
                                                                            <span
                                                                                class="marketplace-circle circle-reflex-bottom">
                                                                                <i class="mdi mdi-star"></i>
                                                                            </span>
                                                                        </div>
                                                                        <div class="description-content text-center">
                                                                            <h5
                                                                                class="lighter-header sky-blue-text title-info">
                                                                                Calificación de la compra</h5>
                                                                            <p>Nos gustaría saber si salió todo bien. Tu
                                                                                comentario es muy importante para el
                                                                                resto de los usuarios.</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="content-data-list content-data-products-evaluation">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <ul id="tp_list-content-article"
                                                                                class="list-unstyled msg_list browse-list-program">
                                                                                <li>
                                                                                    <div id="tp_article-left-box">
                                                                                        <div class="row">
                                                                                            <div
                                                                                                class="col-xs-4 col-lg-2">
                                                                                                <div
                                                                                                    class="image-list">
                                                                                                    <a
                                                                                                        class="image-thumb">
                                                                                                        <img src="{{ $purchase->imagen_producto }}"
                                                                                                            title="{{ $purchase->nombre_producto }}"
                                                                                                            alt="{{ $purchase->nombre_producto }}" />
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div
                                                                                                class="col-xs-8 col-lg-10">
                                                                                                <div
                                                                                                    class="content-item-list">
                                                                                                    <div
                                                                                                        class="inner-content-list">
                                                                                                        <h5
                                                                                                            class="title-item-list ellipsis">
                                                                                                            <a
                                                                                                                class="hover-list">{{ $purchase->nombre_producto }}
                                                                                                            </a>
                                                                                                        </h5>
                                                                                                        <p
                                                                                                            class="description-project-item text-justify ellipsis">
                                                                                                            {{ $purchase->resumen_producto }}
                                                                                                        </p>
                                                                                                        <ul
                                                                                                            class="list-inline data-inline-list">
                                                                                                            <li>
                                                                                                                <div
                                                                                                                    class="accountant-item-list">
                                                                                                                    <strong>
                                                                                                                        $
                                                                                                                        {{ number_format($purchase->valor_total_compra, 0, ',', '.') }}</strong>
                                                                                                                </div>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="wrapper-form-evaluation">
                                                                    {{-- wrapper render form --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end Table deliverable agreement -->
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                {{-- <div role="tabpanel" class="tab-pane fade" id="tab_agreement_comprador"
                                    aria-labelledby="profile-tab">
                                    <div class="row">
                                        @if (!is_null($comprador))
                                            <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <dl>
                                                            @foreach ($comprador->respuestaevalcompra as $seller)
                                                                <dt>{{ $seller->nombre_item_evaluacion_compra }}</dt>
                                                                <dd>
                                                                    <div class="clearfix">
                                                                        <p class="pull-left">
                                                                            {{ $seller->descripcion_item_eval_compra }}
                                                                        </p>
                                                                        <div
                                                                            class="pull-right stars-rating stars-30 readonly disabled">
                                                                            <input type="radio"
                                                                                name="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}"
                                                                                id="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_5"
                                                                                value="5"
                                                                                @if ($seller->valor_respuesta_eval_compra == 5) checked @endif
                                                                                disabled="disabled"
                                                                                class="check-star">
                                                                            <label
                                                                                for="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_5"
                                                                                class="label-terms"><i
                                                                                    class="fa fa-star"></i></label>

                                                                            <input type="radio"
                                                                                name="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}"
                                                                                id="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_4"
                                                                                value="4"
                                                                                @if ($seller->valor_respuesta_eval_compra == 4) checked @endif
                                                                                disabled="disabled"
                                                                                class="check-star">
                                                                            <label
                                                                                for="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_4"
                                                                                class="label-terms"><i
                                                                                    class="fa fa-star"></i></label>

                                                                            <input type="radio"
                                                                                name="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}"
                                                                                id="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_3"
                                                                                value="3"
                                                                                @if ($seller->valor_respuesta_eval_compra == 3) checked @endif
                                                                                disabled="disabled"
                                                                                class="check-star">
                                                                            <label
                                                                                for="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_3"
                                                                                class="label-terms"><i
                                                                                    class="fa fa-star"></i></label>

                                                                            <input type="radio"
                                                                                name="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}"
                                                                                id="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_2"
                                                                                value="2"
                                                                                @if ($seller->valor_respuesta_eval_compra == 2) checked @endif
                                                                                disabled="disabled"
                                                                                class="check-star">
                                                                            <label
                                                                                for="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_2"
                                                                                class="label-terms"><i
                                                                                    class="fa fa-star"></i></label>

                                                                            <input type="radio"
                                                                                name="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}"
                                                                                id="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_1"
                                                                                value="1"
                                                                                @if ($seller->valor_respuesta_eval_compra == 1) checked @endif
                                                                                disabled="disabled"
                                                                                class="check-star">
                                                                            <label
                                                                                for="evaluacion_item_compra_{{ $seller->codigo_item_evaluacion_compra }}_1"
                                                                                class="label-terms"><i
                                                                                    class="fa fa-star"></i></label>
                                                                        </div>
                                                                    </div>
                                                                </dd>
                                                            @endforeach
                                                        </dl>
                                                    </div>
                                                </div>
                                                <div class="ln_solid"></div>
                                                <div class="row col-equal-height align-bottom">
                                                    <div class="col-md-6">
                                                        <label>Comentario Evaluación Compra:</label>
                                                        <p>{{ $comprador->evaluacioncompra->cometario_evaluacion_compra }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Puntaje total:</label>
                                                        <p>{{ $comprador->evaluacioncompra->promedio_evaluacion }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-12 text-center">
                                                <span>No hay resultados.</span>
                                            </div>
                                        @endif
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- marketplace dinamic content  --}}
        @yield('marketplace.evaluation.templates')
    </section>
</section>
