/**
* Class MarketplaceVoucherDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceVoucherDetailView = Backbone.View.extend({
        el: '#marketplace-voucher-content',
        template: _.template( ($('#marketplace-voucher-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-remove-transaction-tpl').html() || '') ),
        events: {
            'click .cancelTransaction': 'onCancelTransaction',
        },
        parameters: {
            codigo_compra: null
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
            this.voucherList = new app.PurchasesList();
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 15000,
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

            // Models

            this.voucher = new app.VoucherModel();

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {

            // Initialize attributes
            this.$wrapperVouchers = this.$('#wrapper-voucher-detail');

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

            // input mask
            this.$(":input").inputmask();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            // instance of voucher view
            this.voucherListView = new app.MarketplaceVoucherListView({
                el: this.$wrapperVouchers,
                collection: this.voucherList,
                parameters: {
                    codigo_compra: this.parameters['codigo_compra']
                }
            });
            this.voucherListView.render();
            this.voucherList.fetch({data: {voucher : this.parameters['codigo_detalle_voucher']},reset:true});
        },

        // On cancel transaction

        onCancelTransaction: function(e) {
          e.preventDefault();
            var _this = this, purchase = $(e.currentTarget).data('purchase');

            this.voucher.set({codigo_voucher: this.parameters.codigo_detalle_voucher});

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.voucher,
                parameters: {
                    template: this.templateConfirm,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.voucher.save({purchase: purchase}, {
                            success: function (model, resp) {

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

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox({
                                            text: resp.message,
                                            type: 'success',
                                            closeTime: 5000
                                        });

                                        window.Misc.redirect();
                                    }else{
                                        window.Misc.showAlertBox({
                                            text: resp.errors,
                                            type: 'alert',
                                            closeTime: 5000
                                        });
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
        }
   });

})(window.jQuery, window, document);
