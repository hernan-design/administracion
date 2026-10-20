/**
* Class MarketplaceAssignmentListView extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceAssignmentsView = Backbone.View.extend({
        el: '#marketplace-assignment-content',
        // template: _.template(),
        events: {
            'submit #form-assignments-members-filter': 'onFilter',
            'click .btn-clear-filter': 'onClearFilter'
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

            // Initialize attributes
        },

        /*
        * Render View Element
        */
        render: function () {
            // references to elements
            this.$assignmentsTable = this.$('#assignments-marketplace-table');
            this.$formFilter = this.$('#form-assignments-members-filter');

            // get references to views
            this.referenceViews();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            var _this = this;

            this.gridAssignments = this.$assignmentsTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('convocatoria.usuario.index') ),
                    data: function( data ) {
                        data.program = window._tp.getProgram['aldea__20'];
                        data.phase = [ window._tp.getPhases['aldea__20__challenge_3'], window._tp.getPhases['four_challenge'] ].join(',');
                        data.number_register = _this.$formFilter.find('#number_register').val();
                        data.emprendimiento = _this.$formFilter.find('#emprendimiento').val();
                        data.asignacion = _this.$formFilter.find('#asignacion').val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_entidad', name: 'nombre_entidad' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' }
                ],
                order: [
                    [ 0, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 3,
                        width: '10%',
                        className: 'text-center',
                        orderable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="'+ window.Misc.urlFull(Route.route('marketplace.asignaciones.show', {asignacione: full.codigo_registro_fase})) +'" title="Asignar integrantes" class="btn btn-default btn-xs" data-turn="false"><i class="fa fa-file-text-o"></i></a>';
                        }
                    }
                ],
                fnRowCallback: function( row, data ) {
                }
            });
        },

        onFilter: function(e) {
            e.preventDefault();

            this.gridAssignments.ajax.reload();
        },

        onClearFilter: function(e) {
            e.preventDefault();

            // reset inputs
            this.$formFilter.find(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
            this.$formFilter[0].reset();

            this.gridAssignments.ajax.reload();
        }
    });

})(window.jQuery, window, document);
