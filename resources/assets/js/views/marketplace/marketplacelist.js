/**
* Class MarketplaceListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceListView = Backbone.View.extend({
        el: '#marketplaces-content',
        template: _.template( ($('#marketplace-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#active-confirm-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        templateRefuse: _.template( ($('#comment-refused-product-tpl').html() || '') ),
        events: {
            'click .btn-clear': 'clear',
            'click .btn-search': 'search',
            'click .btn-back': 'onModalRefuse',
            'click .btn-active': 'onModalConfirm',
            'click .btn-approve': 'onModalConfirm',
            'click .btn-inactive': 'onModalConfirm',
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
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            this.productModel = new app.ProductModel();

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

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
            this.$searchordenpState = this.$('#codigo_estado');
            this.$searchordenpCategory = this.$('#category');
            this.$formFilters = this.$('#form-product-filter');
            this.$searchordenpSubcategory = this.$('#subcategory');
            this.$searchordenpNameProduct = this.$('#name_product');
            this.$productsSearchTable = this.$('#products-search-table');
            this.$filterProvider = this.$('#seg_codigo_usuario');
            this.$filterCity = this.$('#codigo_ciudad');

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
                    url: window.Misc.urlFull( Route.route('marketplace.index') ),
                    data: function( data ) {
                        data.codigo_estado  = _this.$searchordenpState.val();
                        data.category       = _this.$searchordenpCategory.val();
                        data.subcategory    = _this.$searchordenpSubcategory.val();
                        data.name_product   = _this.$searchordenpNameProduct.val();
                        data.codigo_ciudad  = _this.$filterCity.val();
                        data.seg_codigo_usuario = _this.$filterProvider.val();
                    }
                },
                columns: [
                    { data: 'nombre_producto', name: 'nombre_producto' },
                    { data: 'entity_name', name: 'entity_name' },
                    { data: 'nombre_cat_padre_producto', name: 'nombre_cat_padre_producto' },
                    { data: 'nombre_categoria_producto', name: 'nombre_categoria_producto' },
                    { data: 'nombre_clase_producto', name: 'nombre_clase_producto' },
                    { data: 'precio_producto', name: 'precio_producto' }

                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            if( full.codigo_estado == window._tp.getState['review'] ) {
                                return '<a href="' + window.Misc.urlFull( Route.route('marketplace.show', {marketplace: full.url_producto})) + '" title="Ver producto" style="color:#F88B26">'+full.nombre_producto+'</a>';
                            }else if(full.codigo_estado == window._tp.getState['inactive']){
                                return '<a href="' + window.Misc.urlFull( Route.route('marketplace.show', {marketplace: full.url_producto})) + '" title="Ver producto" style="color:#C9302C">'+full.nombre_producto+'</a>';
                            }
                            else if(full.codigo_estado == window._tp.getState['pending']){
                                return '<a href="' + window.Misc.urlFull( Route.route('marketplace.show', {marketplace: full.url_producto})) + '" title="Ver producto" style="color:#807D7D">'+full.nombre_producto+'</a>';
                            }else{
                                return '<a href="' + window.Misc.urlFull( Route.route('marketplace.show', {marketplace: full.url_producto})) + '" title="Ver producto">'+full.nombre_producto+'</a>';
                            }
                        }
                    },
                    {
                        targets: 1,
                        render: function (data, type, full, row) {
                            var safeData = data ? data.toLowerCase() : '';
                            var email = full.entity_email ? full.entity_email.toLowerCase() : '';
                            var capitalizedData = safeData ? safeData.charAt(0).toUpperCase() + safeData.slice(1) : '';
                            return capitalizedData + ' <br> '+ email;
                        }
                    },
                    {
                        targets: 4,
                        className: 'text-capitalize',
                        render: function (data, type, full, row) {
                            return data ? data.toLowerCase() : '';
                        }
                    },
                    {
                        targets: 5,
                        className: 'text-right',
                    },
                    {
                        targets: 6,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.codigo_estado == window._tp.getState['review'] ) {
                        $(row).css( {"color":"#F88B26"} );
                    }

                    if(data.codigo_estado == window._tp.getState['inactive']){
                        $(row).css( {"color":"#C9302C"} );
                    }

                    if(data.codigo_estado == window._tp.getState['pending']){
                        $(row).css( {"color":"#807D7D"} );
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
            this.$searchordenpCategory.val('');
            this.$searchordenpSubcategory.val('');
            this.$searchordenpNameProduct.val('');
            this.$filterCity.select2('val', '');
            this.$filterProvider.select2('val', '');

            this.productsSearchTable.ajax.reload();
        },

        /**
        * confirm turn
        */
        onModalConfirm: function (e) {
            e.preventDefault();

            var _this = this,
                target = $(e.currentTarget),
                action = target.data('action'),
                data = {
                    action: action,
                    change_state: true,
                    codigo_producto: target.data('id')
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.productModel,
                parameters: {
                    template: this.templateConfirm( {
                        product: target.data('name'),
                        action: action
                    } ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.productModel.save( data, {patch: true, wait:true,
                            success: function (model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                // response success or error
                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '...')
                                        : resp.errors;

                                if( _.isObject( resp.errors ) ){

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                         listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var config = {
                                    'text': text,
                                    'type': type
                                }
                                _.extend(_this.configAlert, config);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox( _.extend({title:'Estado...'}, _this.configAlert) );
                                        _this.productsSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox( _.extend({title:'Error!'}, _this.configAlert) );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },

        /**
         *  Modal refused product
         */
        onModalRefuse: function (e) {
            e.preventDefault();

            var target = $(e.currentTarget),
                action = target.data('action');

            this.actionModal = new app.ActionWindow({
                model: this.productModel,
                parameters: {
                    template: this.templateRefuse,
                    data: {
                        title: 'Rechazar producto',
                        table: this.productsSearchTable
                    },
                    dataFilter: {
                        action: action,
                        change_state: true,
                        codigo_producto: $(e.currentTarget).data('id')
                    }
                }
            });

            this.actionModal.render();
        },

        /**
        * on filter export
        */
        onFilterExport: function (e) {

            e.preventDefault();
            var data = window.Misc.formToJson(this.$formFilters);
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'marketplace.exportar', data) ) );
        }
   });

})(window.jQuery, window, document);
