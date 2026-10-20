/**
* Class EvaluationListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EvaluationListView = Backbone.View.extend({

        el: '#evaluations-content',
        template: _.template( ($('#list-evaluation-tpl').html() || '') ),
        events: {
            'submit #form-evaluation' : 'addEvaluation',
            // 'click .btn-search': 'search',
            // 'click .btn-clear': 'clear'
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

            // reference to elements
            this.$formCreate = this.$('#form-survey');
            // Events Listener

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
            this.$evaluationsSearchTable = this.$('#evaluations-list-search-table');

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
            console.log('hola')
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
                    url: window.Misc.urlFull( Route.route('evaluacion.index') ),
                    data: function( data ) {
                        //
                    }
                },
                columns: [
                    { data: 'nombre_convocatoria', name: 'nombre_convocatoria' },
                    { data: 'nombre_evaluacion_emprendimiento', name: 'nombre_evaluacion_emprendimiento' },
                    { data: 'contador_asignados', name: 'contador_asignados' },
                    { data: 'contador_evaluados', name: 'contador_evaluados' },
                    { data: 'pendientes', name: 'pendientes' },

                ],
                order: [
                    [ 1, 'desc' ], [ 2, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: [0],
                        width: '30%',
                        searchable: false,
                        render: function ( data, type, full, row ) {
                            return '<a href="'+ window.Misc.urlFull( Route.route('evaluacion.show', {evaluacion: full.codigo_evaluacion_emprendimiento }) )  +'">' + data + '</a>';

                        }
                    }
                ]
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

            this.evaluationsSearchTable.ajax.reload();
        },

        /*
         * Add Evaluation
        */
        addEvaluation: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            this.parameters.action = true;
            console.log( this.model);
            // return;
            this.model.save( data, {wait: true} );
        },

        // onCreateQuestion: function(e) {
        //     e.preventDefault();

        //     if ( this.modalQuestionForm instanceof Backbone.View ){
        //         this.modalQuestionForm.stopListening();
        //         this.modalQuestionForm.undelegateEvents();
        //       }

        //     this.modalQuestionForm = new app.CreateEvaluationFormWindow({
        //         model: this.formQuestion,
        //         collection: this.formQuestionsList,
        //         parameters: {
        //             group: $(e.currentTarget).data('group'),
        //             page: $(e.currentTarget).data('page'),
        //             version: $('#codigo_version_encuesta').find('option:selected').val(),
        //             // encuesta: this.model.get('codigo_encuesta'),
        //         },
        //    });

        //     this.modalQuestionForm.render();
        // },
   });

})(window.jQuery, window, document);
