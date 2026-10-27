/**
* Class MarketplaceTransactionListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceTransactionListView = Backbone.View.extend({

        el: '#marketplaces-content',
        template: _.template( ($('#transactions-list-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-transactions-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'click .data-export': 'onFilterExport'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$searchordenpState = this.$('#state');
            this.$searchordenpCategory = this.$('#category');
            this.$searchordenCity = this.$('#codigo_ciudad');
            this.$searchordenNumber = this.$('#number_register');
            this.$searchordenpNameProduct = this.$('#name_product');
            this.$searchordenSeller = this.$('#seg_codigo_usuario');
            this.$searchordenpStatePurchase = this.$('#state_purchase');
            this.$searchNumberPostulation = this.$('#number_postulation');
            this.$searchordenChallengePeople = this.$('#challenge_people');
            this.$productsSearchTable = this.$('#transactions-search-table');
            this.$filterEntrepreneur  = this.$('#nombre_emprendedor_filter_data');
            this.$searchEntrepreneur  = this.$('#correo_electronico_emprendedor_filter_data');

            this.$formFilters = this.$('#form-transactions-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
        * Fires before of run render function
        */
        beforeRender: function () {
            //
        },

        /**
        * Fires before of run render function
        */
        afterRender: function () {

            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {

            var _this = this;

            // reload plugins
            if(typeof window.initComponent.select2Trigger == 'function' )
                window.initComponent.select2Trigger();

            if( typeof(window.initComponent.datepickerTrigger) == 'function')
                window.initComponent.datepickerTrigger();
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            var _this = this;
            this.productsSearchTable = this.$productsSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('marketplace.transacciones.index') ),
                    data: function( data ) {
                        data.state = _this.$searchordenpState.val();
                        data.codigo_ciudad = _this.$searchordenCity.val();
                        data.category = _this.$searchordenpCategory.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.name_product = _this.$searchordenpNameProduct.val();
                        data.seg_codigo_usuario = _this.$searchordenSeller.val();
                        data.state_purchase = _this.$searchordenpStatePurchase.val();
                        data.number_postulation = _this.$searchNumberPostulation.val();
                        data.challenge_people = _this.$searchordenChallengePeople.val();
                        data.correo_electronico_emprendedor = _this.$searchEntrepreneur.val();
                        data.datatables = true;
                    }
                },
                columns: [
                    { data: 'fecha_compra', name: 'fecha_compra' },
                    { data: 'codigo_compra', name: 'codigo_compra' },
                    { data: 'nombre_comprador', name: 'nombre_comprador' },
                    { data: 'nombre_vendedor', name: 'nombre_vendedor' },
                    { data: 'nombre_producto', name: 'nombre_producto' },
                    { data: 'state_purchase_name', name: 'state_purchase_name' },
                    { data: 'state_agreement_name', name: 'state_agreement_name', 'defaultContent': '' },
                    { data: 'codigo_acuerdo_compra', name: 'codigo_acuerdo_compra' },

                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: [2],
                        className: 'text-capitalize',
                        render: function (data, type, full, row) {
                            var safeData = data ? data.toLowerCase() : '';
                            var buyerName = full.buyer_entity_name ? full.buyer_entity_name : '';
                            return safeData + ' <br> ' + buyerName;
                        }
                    },
                    {
                        targets: [3],
                        className: 'text-capitalize',
                        render: function ( data, type, full, row ) {
                            var safeData = data ? data.toLowerCase() : '';
                            var entityName = full.entity_name ? full.entity_name.toLowerCase() : '';
                            return safeData + ' <br>' + entityName;
                        }
                    },
                    {
                        targets: [4],
                        render: function ( data, type, full, row ) {
                            var safeData = data ? data : '';
                            var categoryName = full.nombre_categoria_producto ? full.nombre_categoria_producto : '';
                            return safeData + ' <br>' + categoryName;
                        }
                    },
                    {
                        targets: [5],
                        className: 'text-capitalize',
                        render: function (data, type, full, row) {
                            return data ? data.toLowerCase() : '';
                        }
                    },
                    {
                        targets: [6],
                        className: 'text-capitalize',
                        render: function (data, type, full, row) {
                            if(!_.isNull(data) && data){
                                return data.toLowerCase();
                            }
                            return '';
                        }
                    },
                    {
                        targets: [7],
                        orderable: false,
                        width: '63px',
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            if(!full.acuerdo_producto || full.codigo_acuerdo_compra !== null){
                                return _this.templateButtonsAction( {full: full} );
                            }else{
                                return '';
                            }
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                    var date = new Date();
                    date.setMonth(date.getMonth() + 1);
                    var monthCurrent = date.getMonth();
                    var dateStartRevision = data.fecha_inicio_revision;
                    var dateEndRevision = data.fecha_fin_revision;

                    if ( data.state_agreement == window._tp.getStateAgreement['confirm'] || data.state_agreement == window._tp.getStateAgreement['correction'] ) {
                        $(row).css( {"color":"#C9302C"} );
                    }
                    if(data.state_purchase == window._tp.getState['finalized'] && data.state_agreement == window._tp.getStateAgreement['finalized']){
                        $(row).css( {"color":"#3FC1A5"} );
                    }

                    if (typeof dateStartRevision == 'string' && typeof dateEndRevision == 'string') {
                        dateStartRevision = dateStartRevision.split('-');
                        dateEndRevision = dateEndRevision.split('-');
                        if (date.getMonth() < 9) {
                            monthCurrent = '0' + date.getMonth();
                        }
                        if (dateStartRevision[1] == monthCurrent) {
                            if (date.getDate() >= dateStartRevision[2] && date.getDate() <= dateEndRevision[2]) {
                                $(row).css( {"color":"#FFAF33"} );
                            }
                        }
                    }
                }
            });
        },
        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
             window.Misc.setSpinner( this.$el );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': true
            });
        },

        search: function(e) {
            e.preventDefault();
            this.productsSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchordenpState.val('');
            this.$searchordenNumber.val('');
            this.$searchEntrepreneur.val('');
            this.$filterEntrepreneur.val('');
            this.$searchordenpCategory.val('');
            this.$searchNumberPostulation.val('');
            this.$searchordenpNameProduct.val('');
            this.$searchordenpStatePurchase.val('');
            this.$searchordenCity.select2('val', '');
            this.$searchordenSeller.select2('val', '');
            this.$searchordenChallengePeople.select2('val', '');

            this.productsSearchTable.ajax.reload();
        },

        /**
        * on filter export
        */
        onFilterExport: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson(this.$formFilters);
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'marketplace.transacciones.exportar', data) ) );
        }
   });

})(window.jQuery, window, document);
