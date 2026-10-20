/**
* Class MarketplacePromotionListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplacePromotionListView = Backbone.View.extend({
        el: '#marketplace-promotion-content',
        templateConfirm: _.template(($('#active-confirm-tpl').html() || '')),
        templateActivation: _.template(($('#activation-promotion-tpl').html() || '')),
        templateButtonsAction: _.template(($('#buttons-action-tpl').html() || '')),
        templateRefuse: _.template(($('#comment-refused-promotion-tpl').html() || '')),
        events: {
            'click .btn-clear': 'clear',
            'click .btn-search': 'search',
            'click .btn-back': 'onModalRefused',
            'click .btn-approve': 'onModalConfirm',
            'click .confirm-turn': 'onChangeState',
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if (opts !== undefined && _.isObject(opts.parameters)) {
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            this.promotionModel = new app.PromotionModel();

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            var _this = this;
            this.render = _.wrap(this.render, function (render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener custome
            this.on('beforeRender', this.beforeRender);
            this.on('afterRender', this.afterRender);
        },

        /*
        * Render View Element
        */
        render: function () {
            // Initialize attributes
            this.$searchState = this.$('#codigo_estado');
            this.$searchOwner = this.$('#correo_electronico');
            this.$formFilters = this.$('#form-promotions-filter');
            this.$promotionsSearchTable = this.$('#promotions-marketplace-table');

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
            if (typeof window.initComponent.select2Trigger == 'function') {
                window.initComponent.select2Trigger();
            }

            if (typeof (window.initComponent.datepickerTrigger) == 'function') {
                window.initComponent.datepickerTrigger();
            }
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            var _this = this;
            this.promotionsSearchTable = this.$promotionsSearchTable.DataTable({
                dom: "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull(Route.route('marketplace.promociones.index')),
                    data: function (data) {
                        data.codigo_estado = _this.$searchState.val();
                        data.correo_electronico = _this.$searchOwner.val();
                    }
                },
                columns: [
                    { data: 'codigo_promocion', name: 'codigo_promocion' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'nombre_estado', name: 'nombre_estado' }

                ],
                order: [
                    [0, 'desc']
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, full, row) {
                            var tipo = '';
                            if (full.tipo_promocion == window._tp.getTypePromo['promotion']) {
                                tipo = 'promoción';
                            }

                            if (full.tipo_promocion == window._tp.getTypePromo['advertisement']) {
                                tipo = 'anuncio';
                            }

                            return '<a href="' + window.Misc.urlFull(Route.route('marketplace.promociones.show', { promocione: full.codigo_promocion })) + '" title="Ver ' + tipo + ' ">' + full.codigo_promocion + '</a>';
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        className: 'text-center',
                        render: function (data, type, full, row) {
                            console.log('Full data', full);
                            if (full.tipo_promocion == window._tp.getTypePromo['promotion']) {
                                full.tipo = 'la promoción';
                            }

                            if (full.tipo_promocion == window._tp.getTypePromo['advertisement']) {
                                full.tipo = 'el anuncio';
                            }

                            if (full.codigo_estado != window._tp.getState['pending']) {
                                if (full.codigo_estado == window._tp.getState['active']) {
                                    return '<a href="#" title="Desactivar" class="btn btn-default btn-xs confirm-turn" data-turn="false" data-id="' + full.codigo_promocion + '" data-name="' + full.tipo + '" data-action="desactivar"> <i class="fa fa-toggle-on"></i></a>';
                                } else {
                                    return '<a href="#" title="Activar" class="btn btn-default btn-xs confirm-turn" data-turn="true" data-id="' + full.codigo_promocion + '" data-name="' + full.tipo + '" data-action="activar"> <i class="fa fa-toggle-off"></i></a>';
                                }
                            } else {
                                return full.tipo + ' no ha sido aprobada';
                            }
                        }
                    },
                    {
                        targets: 5,
                        orderable: false,
                        className: 'text-center',
                        render: function (data, type, full, row) {
                            if (full.tipo_promocion == window._tp.getTypePromo['promotion']) {
                                full.tipo = 'la promoción';
                            }

                            if (full.tipo_promocion == window._tp.getTypePromo['advertisement']) {
                                full.tipo = 'el anuncio';
                            }

                            return _this.templateButtonsAction({ full: full })
                        }
                    }
                ],
                fnRowCallback: function (row, data) {
                    //
                }
            });
        },

        onChangeState: function (e) {
            e.preventDefault();

            var _this = this,
                target = $(e.currentTarget),
                name = target.data('name'),
                action = target.data('action'),
                data = {
                    turn: $(e.currentTarget).data('turn'),
                    action: action,
                    change_state: true,
                    codigo_promocion: target.data('id')
                };

            if (this.acceptConfirm instanceof Backbone.View) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.promotionModel,
                parameters: {
                    template: this.templateConfirm({
                        codigo_promocion: target.data('id'),
                        action: target.data('turn') ? 'Activar' : 'Desactivar',
                        tipo: name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.promotionModel.save(data, {
                            patch: true, wait: true,
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

                                if (_.isObject(resp.errors)) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function (field, item) {
                                        listError += '<li>' + item[0] + '</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var config = {
                                    'text': text,
                                    'type': type
                                }
                                _.extend(_this.configAlert, config);

                                console.log('Response', resp);

                                //complete proccess
                                if (resp.success !== undefined) {
                                    if (resp.success) {
                                        window.Misc.showAlertBox(_.extend({ title: 'Estado...' }, _this.configAlert));
                                        _this.promotionsSearchTable.ajax.reload(null, false);
                                    } else {
                                        window.Misc.showAlertBox(_.extend({ title: 'Error!' }, _this.configAlert));
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
            window.Misc.setSpinner(this.$el);
        },

        /**
        * response of the server
        */
        responseServer: function (model, resp, opts) {

            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': true
            });
        },

        search: function (e) {
            e.preventDefault();
            this.promotionsSearchTable.ajax.reload();
        },

        clear: function (e) {
            e.preventDefault();

            this.$searchState.val('');
            this.$searchOwner.val('');

            this.promotionsSearchTable.ajax.reload();
        },

        /**
        * confirm turn
        */
        onModalConfirm: function (e) {
            e.preventDefault();

            var _this = this,
                target = $(e.currentTarget),
                action = target.data('action'),
                name = target.data('name'),
                data = {
                    action: action,
                    change_state: true,
                    codigo_promocion: target.data('id')
                };

            if (this.acceptConfirm instanceof Backbone.View) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.promotionModel,
                parameters: {
                    template: this.templateConfirm({
                        codigo_promocion: target.data('id'),
                        action: action,
                        tipo: name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.promotionModel.save(data, {
                            patch: true, wait: true,
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

                                if (_.isObject(resp.errors)) {

                                    var listError = '<ul>';

                                    $.each(resp.errors, function (field, item) {
                                        listError += '<li>' + item[0] + '</li>';
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
                                if (resp.success !== undefined) {
                                    if (resp.success) {
                                        window.Misc.showAlertBox(_.extend({ title: 'Estado...' }, _this.configAlert));
                                        _this.promotionsSearchTable.ajax.reload(null, false);
                                    } else {
                                        window.Misc.showAlertBox(_.extend({ title: 'Error!' }, _this.configAlert));
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
         * Modal refused promotion
         */
        onModalRefused: function (e) {
            e.preventDefault();

            var target = $(e.currentTarget),
                action = target.data('action');

            this.actionModal = new app.ActionWindow({
                model: this.promotionModel,
                parameters: {
                    template: this.templateRefuse,
                    data: {
                        title: 'Rechazar promoción',
                        table: this.promotionsSearchTable
                    },
                    dataFilter: {
                        action: action,
                        change_state: true,
                        codigo_promocion: $(e.currentTarget).data('id')
                    }
                }
            });

            this.actionModal.render();
        }
    });

})(window.jQuery, window, document);
