/**
* Class EvaluationDetailView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EvaluationDetailView = Backbone.View.extend({

        el: '#evaluations-content',
        template: _.template( ($('#evaluation-detail-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'submit #form-assignment-filter': 'search',
            'click .btn-clear': 'clear',
            'click .data-export': 'onFilterExport'
        },
        parameters: {
            dataFilter: {},
            tab: 'general'
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
            this.listenTo( this.model, 'change', this.render );
            // this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events Listener custome
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );

        },

        /*
        * Render View Element
        */
        render: function() {

            var attributes = this.model.toJSON();
            attributes.dataFilter = this.parameters['dataFilter'];
            this.$el.html( this.template(attributes) );

            this.dataFilter = this.parameters.dataFilter;
            this.dataFilter.evaluation = this.model.get('codigo_evaluacion_emprendimiento');

            // Initialize attributes
            this.$evaluationsSearchTable = this.$('#evaluation-list-search-table');
            this.$searchordenpEntrepreneurship = this.$('#emprendimiento');
            this.$searchordenpEmail = this.$('#email');
            this.$searchordenpEvaluated = this.$('#evaluated');
            this.$searchordenNumber = this.$('#number_register');
            this.$searchordenpDateAssignment = this.$('#fecha_asignacion');
            this.$searchordenpDateEvaluation = this.$('#fecha_evaluacion');
            this.$searchordenSortby = this.$('#sortby');
            this.$searchordenScoring = this.$('#scoring');
            this.$searchordenSortby_2 = this.$('#sortby_2');
            this.$searchordenScoring_2 = this.$('#scoring_2');

            this.$formFilters = this.$('#form-assignment-filter');

            // overwrite model id attribute
            if( this.model.changed.codigo_evaluacion_emprendimiento !== undefined ) {
                // Overwrite id attribute of the model
                this.model.idAttribute = 'codigo_evaluacion_emprendimiento';
                this.model.set( {'codigo_evaluacion_emprendimiento': this.model.get('codigo_evaluacion_emprendimiento')}, {'silent':true} );
                // reference to views
                this.referenceViews();
            }
            // this.clear();
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
            this.evaluationsSearchTable = this.$evaluationsSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('evaluacion.asignacion.index') ),
                    data: function( data ) {
                        data.evaluation = _this.model.get('codigo_evaluacion_emprendimiento');
                        data.emprendimiento = _this.$searchordenpEntrepreneurship.val();
                        data.email = _this.$searchordenpEmail.val();
                        data.evaluated = _this.$searchordenpEvaluated.val();
                        data.number_register = _this.$searchordenNumber.val();
                        data.fecha_asignacion = _this.$searchordenpDateAssignment.val();
                        data.fecha_evaluacion = _this.$searchordenpDateEvaluation.val();
                        data.sortby = _this.$searchordenSortby.val();
                        data.scoring = _this.$searchordenScoring.val();
                        data.sortby_2 = _this.$searchordenSortby_2.val();
                        data.scoring_2 = _this.$searchordenScoring_2.val();
                    }
                },
                columns: [
                    { data: 'numero_registro_convocatoria', name: 'numero_registro_convocatoria' },
                    { data: 'nombre_usuario_proyecto', name: 'nombre_usuario_proyecto' },
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'correo_electronico', name: 'correo_electronico' },
                    { data: 'fecha_asignacion_jurado', name: 'fecha_asignacion_jurado' },
                    { data: 'fecha_resultado_evaluacion', name: 'fecha_resultado_evaluacion' },
                    { data: 'puntaje_total_eval_emprendi', name: 'puntaje_total_eval_emprendi' }
                ],
                order: [
                    [ 5, 'asc' ]
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '10%'
                    },
                    {
                        targets: 2,
                        render: function ( data, type, full, row ) {
                            return data + ' <br>' + full.correo_electronico;
                        }
                    },
                    {
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: [4],
                        width: '10%'
                    },
                    {
                        targets: [5,6],
                        width: '10%'
                    }
                ],
                fnRowCallback: function( row, data ) {
                    if ( data.evaluation == true ) {
                        $(row).css( {"color":"#3FC1A5"} );
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
            this.evaluationsSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchordenpEntrepreneurship.val('');
            this.$searchordenpEmail.val('');
            this.$searchordenpEvaluated.val('');
            this.$searchordenpDateAssignment.val('');
            this.$searchordenpDateEvaluation.val('');
            this.$searchordenNumber.val('');
            this.$searchordenSortby.val('');
            this.$searchordenScoring.val('');
            this.$searchordenSortby_2.val('');
            this.$searchordenScoring_2.val('');

            this.evaluationsSearchTable.ajax.reload();
        },

        /**
        * on filter export
        */
        onFilterExport: function (e) {
            e.preventDefault();
            data = window.Misc.formToJson(this.$formFilters);
            data.evaluation = this.model.get('codigo_evaluacion_emprendimiento');
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'evaluacion.exportar', data) ) );
        }
   });

})(window.jQuery, window, document);
