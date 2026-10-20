/**
* Class EventListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DiagnosticModuleListView = Backbone.View.extend({
        el: '#module-diagnostics-content',
        template: _.template( ($('#diagnostic-module-list-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-diagnostic-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events:{
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
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
            this.$diagnosticListSearchTable  = this.$('#diagnostic-module-list-search-table');
            this.$searchNombre          = this.$('#codigo_diagnostico');
            this.$searchState           = this.$('#codigo_menu_institucion');
            this.$formFilters           = this.$('#form-modulodiagnostic-filter');

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
                    url: window.Misc.urlFull( Route.route('asignacion-diagnostico.index') ),
                    data: function( data ) {
                        data.codigo_diagnostico   = _this.$searchNombre.val();
                        data.codigo_menu_institucion  = _this.$searchState.val();
                    },
                },
                columns: [
                    { data: 'codigo_modulo_diagnostico',    name: 'codigo_modulo_diagnostico'  },
                    { data: 'nombre_diagnostico',    name: 'nombre_diagnostico'  },
                    { data: 'nombre_menu_institucion',    name: 'nombre_menu_institucion'  }
                ],
                order: [
                    [ 0, 'desc' ]
                ],
                columnDefs: [

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

        clear: function(e) {
            e.preventDefault();

            this.$searchState.val('');
            this.$searchNombre.val('');

            this.diagnosticListSearchTable.ajax.reload();
        },

    });
})(window.jQuery, window, document);

