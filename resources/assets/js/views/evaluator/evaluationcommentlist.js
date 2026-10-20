/**
* Class EvaluationCommentListView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EvaluationCommentListView = Backbone.View.extend({

        el: '#evaluations-content',
        template: _.template( ($('#list-evaluation-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
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
                    url: window.Misc.urlFull( Route.route('evaluacion.comentario.index') ),
                    data: function( data ) {
                        //
                    }
                },
                columns: [
                    { data: 'nombre_evaluacion_emprendimiento', name: 'nombre_evaluacion_emprendimiento' },
                    { data: 'name_coment_eval_emprendi', name: 'name_coment_eval_emprendi' },
                    { data: 'texto_coment_eval_emprendi', name: 'texto_coment_eval_emprendi' },
                    { data: 'codigo_estado', name:'codigo_estado', orderable: false }
                ],

                order: [
                    //[ 1, 'desc' ], [ 2, 'desc' ]
                ],
                columnDefs: [
                    // {
                    //     targets: [0],
                    //     width: '30%',
                    //     searchable: false,
                    //     render: function ( data, type, full, row ) {
                    //         return '<a href="'+ window.Misc.urlFull( Route.route('evaluacion.comentario.show', {evaluacion: full.codigo_evaluacion_emprendimiento }) )  +'">' + data + '</a>';

                    //     }
                    // }
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
        }
   });

})(window.jQuery, window, document);
