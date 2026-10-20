/**
* Class GeneralReportsView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.GeneralReportsView = Backbone.View.extend({
        el: '#managements-content',
        template: _.template( ($('#management-list-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-report-tpl').html() || '') ),
        templateReportConfirm: _.template( ($('#report-delete-confirm-tpl').html() || '') ),
        events: {
            'click .btn-delete': 'onDeleteReport',
            'change #tipo_reporte': 'toggleTipoReporteEvaluacion'

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
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            // this.$('#tipo_reporte').on('change', this.toggleTipoReporteEvaluacion.bind(this));

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
            this.$managementSearchTable = this.$('#management-search-table');

            // // Additional code to handle visibility
            // this.toggleTipoReporteEvaluacion(); // Initial check

            // // Add event listener for change in tipo_reporte
            // this.$('#tipo_reporte').on('change', this.toggleTipoReporteEvaluacion.bind(this));

            // overwrite model id attribute
            this.referenceViews();
        },

        // Additional method to toggle visibility
        toggleTipoReporteEvaluacion: function () {
            console.log('hola');
            var tipoReporteSelect = this.$('#tipo_reporte');
            var tipoReporteEvaluacionSelect = this.$('#tipo_reporte_evaluacion');

            var selectedValue = tipoReporteSelect.val();
            if (selectedValue === '1') { // Evaluaciones Convocatorias
                tipoReporteEvaluacionSelect.show().prop('required', true);
            } else {
                tipoReporteEvaluacionSelect.hide().prop('required', false).val(''); // Reset value when hiding
            }
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
            //
        },

        /**
        * reference to views
        */
        referenceViews: function () {

            var _this = this;
            this.managementSearchTable = this.$managementSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('reportes.gestion.index') ),
                    data: function( data ) {
                        //
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'date', name: 'date' },
                    { data: 'size', name: 'size' }
                ],
                order: [
                    [ 1, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + full.download + '" title="Descargar" style="color:#3FC1A5">'+full.name+'</a>';
                        }
                    },
                    {
                        targets: 3,
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                    //
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

        onDeleteReport: function (e) {
            e.preventDefault();

            var _this = this,
                target = $(e.currentTarget),
                data = {
                    key: encodeURI(target.data('id'))
                };

            this.gestionReport = new app.ManagementModel();
            this.gestionReport.set(data);

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.gestionReport,
                parameters: {
                    template: this.templateReportConfirm( {
                        report: target.data('name'),
                        action: target.data('action')
                    } ),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        _this.gestionReport.destroy({wait:true,
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
                                        window.Misc.showAlertBox( _this.configAlert );
                                        _this.managementSearchTable.ajax.reload( null, false );
                                    }else{
                                        window.Misc.showAlertBox( _this.configAlert );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        }
   });

})(window.jQuery, window, document);
