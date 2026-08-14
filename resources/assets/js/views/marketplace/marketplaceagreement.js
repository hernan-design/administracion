/**
* Class AgreementView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.AgreementView = Backbone.View.extend({

        el: '#marketplaces-content',
        template: _.template( ($('#marketplace-agreement-tpl').html() || '') ),
        templateShow: _.template( ($('#marketplace-agreement-show-tpl').html() || '') ),
        templateLiquidate: _.template( ($('#marketplace-agreement-liquidate-tpl').html() || '') ),
        templateEnlarge: _.template( ($('#marketplace-agreement-enlarge-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-agreement-tpl').html() || '') ),
        events: {
            'apply.daterangepicker #fecha_inicial_actividad_acuerdo': 'onDate',
            'click .btn-agreement-enlarge': 'onAgreementEnlarge',
            'click .btn-evaluation-liquidate': 'onAgreementLiquidate',
            'submit #form-liquidate': 'onStoreLiquidate',
            'submit #form-enlarge': 'onStoreEnlarge'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            //Init Attribute
            this.discussionList = new app.DiscussionSaleAgreementList();

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener
            this.listenTo( this.model, 'change:codigo_estado', this.render );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {
            // render
            this.$el.html( this.template({ }) );

            // references
            this.$wrapperDiscussion = this.$('#wrapper-sale-agreement-discussion');
            this.$wrapperAgreement = this.$('#wrapper-render-agreement');
            this.$actionsAgreement = this.$('#agreement-actions');

            if ( this.discussionListView instanceof Backbone.View ){
                this.discussionListView.stopListening();
                this.discussionListView.undelegateEvents();
            }

            // instance to discussion
            this.discussionListView = new app.DiscussionListView({
                el: this.$wrapperDiscussion,
                collection: this.discussionList,
                parameters: {
                    formDiscussion: '#form-discussion-sale-greement',
                    dataFilter: {
                        'codigo_acuerdo_compra' : this.model.get('codigo_acuerdo_compra')
                    }
                }
            });

            // reference to views
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
            if(typeof window.initComponent.select2Trigger == 'function' ) {
                window.initComponent.select2Trigger();
            }

            if( typeof(window.initComponent.datepickerTrigger) == 'function') {
                window.initComponent.datepickerTrigger();
            }

            if( typeof(window.initComponent.autosizeTrigger) == 'function') {
                window.initComponent.autosizeTrigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ) {
                window.initComponent.multiselectTrigger();
            }
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            if(!this.model.get('assignment') || this.model.get('codigo_estado') == window._tp.getState['confirmed'] || this.model.get('codigo_estado') == window._tp.getState['active'] || this.model.get('codigo_estado') == window._tp.getState['progress'] || this.model.get('codigo_estado') == window._tp.getState['to_finalize'] || this.model.get('codigo_estado') == window._tp.getState['finalized']) {
                var attributes = this.model.toJSON();
                attributes.dataFilter = this.parameters['dataFilter'];

                this.$wrapperAgreement.html( this.templateShow(attributes) );
            }else{
                // undelegate events
                if ( this.agreementEditView instanceof Backbone.View ){
                    this.agreementEditView.stopListening();
                    this.agreementEditView.undelegateEvents();
                }

                this.agreementEditView = new app.EditSaleAgreementView({
                    model: this.model
                });

                this.agreementEditView.render();
            }
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

        onDate: function (e) {
            e.preventDefault();

            var initDate = $(e.target).val();

            $('#fecha_final_actividad_acuerdo').daterangepicker({
                startDate: initDate,
                minDate: initDate,
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        },

        onAgreementEnlarge: function (e) {
            e.preventDefault();
            var attributes = this.model.toJSON();

            this.$actionsAgreement.html( this.templateEnlarge( attributes ) );

            this.ready();
        },

        onAgreementLiquidate: function (e) {
            e.preventDefault();
            this.$actionsAgreement.html( this.templateLiquidate( { } ) );

            this.ready();
        },

        onStoreLiquidate: function (e) {
            e.preventDefault();

            this.listenToOnce( this.model, 'request', this.loadSpinnerAgreementActions );

            var _this = this,
                data = window.Misc.formToJson(e.target);

            data.liquidate_agreement = true;
            _.extend(data, this.model.toJSON());

            this.model.set({ liquidate: true });

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm(this.model.toJSON()),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.model.save(data, {patch: true, success: function(model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '...')
                                        : resp.errors;

                                if( _.isObject( resp.errors ) ) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                // //complete proccess
                                if( resp.success !== undefined ){
                                    window.Misc.showAlertBox(_this.configAlert);

                                    _this.$actionsAgreement.html("");
                                    _this.ready();

                                    window.Misc.redirect();
                                }
                            }
                        })
                    }

                }
            });
            this.acceptConfirm.render();
        },

        onStoreEnlarge: function (e) {
            e.preventDefault();

            this.listenToOnce( this.model, 'request', this.loadSpinnerAgreementActions );

            var _this = this,
                data = window.Misc.formToJson(e.target);

                data.update_date_ending = true;
                _.extend(data, this.model.toJSON());

            this.model.set({ liquidate: false });

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                parameters: {
                    template: this.templateConfirm(this.model.toJSON()),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.model.save(data, {patch: true, success: function(model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '...')
                                        : resp.errors;

                                if( _.isObject( resp.errors ) ) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                        listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var conf = {
                                    'text': text,
                                    'type': type
                                };
                                _.extend(_this.configAlert, conf);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    window.Misc.showAlertBox(_this.configAlert);

                                    _this.$actionsAgreement.html("");
                                    _this.ready();

                                    window.Misc.redirect();
                                }
                            }
                        })
                    }

                }
            });
            this.acceptConfirm.render();
        },

        /**
         * Load spinner on the request
         */
        loadSpinnerAgreementActions: function () {
            window.Misc.setSpinner( this.$actionsAgreement );
        }
   });

})(jQuery, this, this.document);
