/**
* Class MarketplaceAssignmentListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceVouchersView = Backbone.View.extend({
        el: '#marketplace-voucher-content',
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters);
        },

        /*
        * Render View Element
        */
        render: function () {
            // references to elements
            this.$vouchersTable = this.$('#vouchers-marketplace-table');

            this.getUserMail = this.$('#correo_electronico');
            this.getEmprendimiento = this.$('#emprendimiento');

            // get references to views
            this.referenceViews();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            var _this = this;

            this.gridVouchers = this.$vouchersTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('marketplace.vouchers.index') ),
                    data: function( data ) {
                        data.correo_electronico = _this.getUserMail.val();
                        data.emprendimiento     = _this.getEmprendimiento.val();
                    }
                },
                columns: [
                    { data: 'codigo_detalle_voucher',   name: 'codigo_detalle_voucher' },
                    { data: 'nombre_usuario',           name: 'nombre_usuario' },
                    { data: 'correo_electronico',       name: 'correo_electronico' },
                    { data: 'nombre_emprendimiento',    name: 'nombre_emprendimiento' },
                    { data: 'valor_detalle_voucher',    name: 'valor_detalle_voucher' },
                    { data: 'saldo_detalle_voucher',    name: 'saldo_detalle_voucher' },
                    { data: 'estado',                   name: 'estado' },
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%',
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull( Route.route('marketplace.vouchers.show', {voucher: full.codigo_detalle_voucher }) )  + '" title="Ver compra">'+full.codigo_detalle_voucher+'</a>';
                        }
                    },
                    {
                        targets: 4,
                        render: function ( data, type, full, row ) {
                            return window.copCurrencyF.to(parseFloat(full.valor_detalle_voucher));
                        }
                    },
                    {
                        targets: 5,
                        render: function ( data, type, full, row ) {
                            return window.copCurrencyF.to(parseFloat(full.saldo_detalle_voucher));
                        }
                    },
                    {
                        // targets: 3,
                        width: '10%',
                        orderable: false,
                        render: function ( data, type, full, row ) {
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });
        },

        search: function(e) {
            e.preventDefault();

            this.gridVouchers.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.getUserMail.val('');
            this.getEmprendimiento.val('');
            this.gridVouchers.ajax.reload();
        }

    });

})(window.jQuery, window, document);
