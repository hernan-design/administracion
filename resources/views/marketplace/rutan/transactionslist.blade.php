<div class="row">
    <div class="col-xs-12">
        @if(isset($statistics) && is_array($statistics) && count($statistics) > 0)
            <div class="row tile_count">
                @foreach($statistics as $item)
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 tile_stats_count">
                        <span class="count_top"><i class="{{ $item->icon }}"></i> {{ $item->title }}</span>
                        <div class="count green"><i class="fa fa-dollar"></i> {{ $item->value }}</div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="x_panel">
            <div class="x_title">
                <h2>Lista Transacciones</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div id="content-marketplace">
                      <div class="well">
                        {!! Form::open(array('route' => null, 'id' => 'form-transactions-filter', 'class' =>'')) !!}
                            <div class="row">
                                <div class="col-lg-1 col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label>Transacción</label>
                                    <input type="number" name="number_register" id="number_register" class="form-control">
                                 </div>
                                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label>Producto</label>
                                    <input type="text" id="name_product" name="name_product" class="form-control">
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label>Categoría</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Selecciona una opción</option>
                                        @foreach( App\Models\Core\CategoriaProducto::getSubcategorylist() as $value)
                                            <option value="{{ $value->codigo_categoria_prod_insti }}">{{ $value->nombre_categoria_producto}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label for="codigo_ciudad">Ciudad</label>
                                    <select name="codigo_ciudad" id="codigo_ciudad" class="form-control choice-select-autocomplete" data-ajax--url="{{ route('ciudades.index') }}" data-placeholder="Selecciona una opción" placeholder="Selecciona una opción" style="width: 100%">
                                    </select>
                                </div>
                                <div class="col-lg-1 col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label>Postulación</label>
                                    <input type="number" name="number_postulation" id="number_postulation" class="form-control">
                                </div>
                                {{-- <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label for="challenge_people">Personas reto 3</label>
                                    <select name="challenge_people" id="challenge_people" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('marketplace.asignaciones.index'))%>" data-param="true" data-placeholder="Selecciona una opción" placeholder="Selecciona una opción" style="width: 100%">
                                    </select>
                                </div> --}}
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label for="seg_codigo_usuario">Proveedor</label>
                                    <select name="seg_codigo_usuario" id="seg_codigo_usuario" class="form-control choice-select-autocomplete" data-ajax--url="<%- window.Misc.urlFull(Route.route('entidad.search'))%>" data-param="<%- window._tp.getRole['seller'] %>" data-placeholder="Selecciona una opción" placeholder="Selecciona una opción" style="width: 100%">
                                    </select>
                                </div>
                                {{-- <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label>Estado Compra</label>
                                    <select name="state_purchase" id="state_purchase" class="form-control">
                                        <option value="">Selecciona una opción</option>
                                        @foreach( App\Models\Core\Estado::getStatelist('state_purchase') as $value)
                                        <option value="{{ $value->codigo_estado }}">{{ $value->nombre_estado}}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-4 col-xs-12">
                                    <div class="x_panel">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-4 col-xs-12 form-group has-feedback">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <label>Correo electrónico emprendedor</label>
                                                        <div class="input-prepend input-group">
                                                            <span class="add-on input-group-addon tp_input-group-addon" data-title="Buscador de usuarios" data-field="correo_electronico_emprendedor_filter_data"><i class="fa fa-search"></i></span>
                                                            <input type="email" name="correo_electronico" id="correo_electronico_emprendedor_filter_data" class="form-control correo_electronico_user" data-name="nombre_emprendedor_filter_data"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-7 col-xs-10">
                                                <label>Nombre emprendedor</label>
                                                <input type="text" name="nombre_emprendedor" id="nombre_emprendedor_filter_data" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    <button type="button" class="btn btn-primary btn-search"> Buscar</button>
                                    <button type="button" class="btn btn-default btn-clear"> Limpiar</button>
                                    @if( Auth::user()->ability('admin,super-admin', 'export', ['module'  => '/marketplace/transacciones']) )
                                        <button type="button" class="btn btn-info data-export"> Exportar</button>
                                    @endif
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                    <br/>
                    <!-- start transactions -->
                    <div class="table-responsive">
                        <table id="transactions-search-table" class="table table-striped table-bordered" data-page-length="15">
                            <thead>
                                <tr>
                                    <th>Fecha de compra</th>
                                    <th>Transacción</th>
                                    <th>Comprador</th>
                                    <th>Vendedor</th>
                                    <th>Producto</th>
                                    <th>Estado compra</th>
                                    <th>Estado acuerdo</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody >
                                <!-- Render view  -->
                            </tbody>
                        </table>
                    </div>
                    <!-- end user transactions -->
                    </div>
            </div>
        </div>
    </div>
</div>