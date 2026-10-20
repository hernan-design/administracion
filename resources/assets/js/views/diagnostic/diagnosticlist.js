/**
* Class EventListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiagnosticListView = Backbone.View.extend({
        el: '#diagnostics-content',
        template: _.template( ($('#diagnostic-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-diagnostic-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events:{

        },
        parameters: {
            dataFilter: {}
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            _.bindAll(this, 'render');
        },

        /*
         * Render View Element
         */
        render: function() {

            var attributes = {};
            this.$el.html( this.template(attributes) );

            // Initialize attributes
            this.$diagnosticListSearchTable  = this.$('#diagnostic-list-search-table');
            // this.$searchNombre          = this.$('#nombre_evento');
            // this.$searchState           = this.$('#codigo_estado');
            // this.$formFilters           = this.$('#form-event-filter');

            // overwrite model id attribute
            this.referenceViews();
        },

        /**
         * reference to views
         */
        referenceViews: function () {

            var _this = this;
            this.diagnosticListSearchTable = this.$diagnosticListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('diagnosticos.index') ),
                    // data: function( data ) {
                    //     data.nombre_evento   = _this.$searchNombre.val();
                    //     data.codigo_estado  = _this.$searchState.val();
                    // },
                },
                columns: [
                    { data: 'codigo_diagnostico',    name: 'codigo_diagnostico'  },
                    { data: 'nombre_diagnostico',    name: 'nombre_diagnostico'  },
                    { data: 'descripcion_diagnostico',    name: 'descripcion_diagnostico'  },
                    { data: 'estado',    name: 'estado'  }
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: 1,
                        render: function ( data, type, full, row ) {
                            return '<a href="' + window.Misc.urlFull(Route.route('diagnosticos.edit', {diagnostico: full.codigo_diagnostico}) ) + '" title="Ver evento" style="color:#3FC1A5">'+full.nombre_diagnostico+'</a>';
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, row) {
                            return data ? 'Activo' : 'Inactivo';
                        }
                    },
                    {
                        targets: 4,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} );
                        }
                    }
                ],
            });
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$gridSearchTable );
        },

        search: function(e) {
            e.preventDefault();
            this.diagnosticListSearchTable.ajax.reload();
        },

    });
})(window.jQuery, window, document);

