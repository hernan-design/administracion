/**
* Class FormDetailView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.FormDetailView = Backbone.View.extend({

        el: '#form-content',
        template: _.template( ($('#form-detail-tpl').html() || '') ),
        templateUsersAnswer: _.template( ($('#users-answer-tpl').html() || '') ),
        templateButtonsAction: _.template( ($('#buttons-action-tpl').html() || '') ),
        events: {
            'click .btn-search': 'search',
            'click .btn-clear': 'clear',
            'submit #form-filter-user-answer': 'search',
            'click .data-export': 'onFilterExport'
        },
        parameters: {
            dataFilter: {},
            tab: 'answers'
        },

        /**
         * Constructor Method
         */
        initialize : function(opts) {
            // extends parameters
            if(!_.isUndefined(opts) && _.isObject(opts.parameters)){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }
            console.log(".FormDetailView!");
            // Events Listener
            this.listenTo( this.model, 'change', this.render );
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
            this.$el.html( this.template(attributes) );

            this.$('#grid-container').html( this.templateUsersAnswer( this.model.toJSON()) );

            // Initialize attributes
            this.$searchNombre  = this.$('#nombre_usuario');
            this.$formFilters   = this.$('#form-filter-user-answer');
            this.$usersAnswerListSearchTable = this.$('#users-answer-search-table');

            // overwrite model id attribute
            this.renderUsersAnswer();
        },

        /**
         * reference to views
         */
        renderUsersAnswer: function () {

            var _this = this;
            this.usersAnswerListSearchTable = this.$usersAnswerListSearchTable.DataTable({
                dom:"<'row'<'col-sm-5'i><'col-sm-7'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                language: window.Misc.dataTableES(),
                ajax: {
                    url: window.Misc.urlFull( Route.route('formulario.respuesta.index') ),
                    data: function( data ) {
                        data.survey = _this.model.get('codigo_encuesta');
                        data.nombre_usuario = _this.$searchNombre.val();
                    },
                },
                columns: [
                    { data: 'nombre_usuario', name: 'nombre_usuario' },
                    { data: 'fecha_usuario_encuesta',  name: 'fecha_usuario_encuesta' },
                    { data: 'fecha_fin_usuario_encuesta',  name: 'fecha_fin_usuario_encuesta' },
                    { data: 'nombre_modulo',  name: 'nombre_modulo' },
                    { data: 'nombre_estado',  name: 'nombre_estado' },
                ],
                order: [
                    [ 1, 'desc' ]
                ],
                columnDefs: [
                    {
                        targets: [1, 2],
                        className: 'nowrap'
                    },
                    {
                        targets: 5,
                        width: '10%',
                        orderable: false,
                        className: 'text-center',
                        render: function ( data, type, full, row ) {
                            return _this.templateButtonsAction( {full: full} )
                        }
                    }
                ]
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
            this.usersAnswerListSearchTable.ajax.reload();
        },

        clear: function(e) {
            e.preventDefault();

            this.$searchNombre.val('');
            this.usersAnswerListSearchTable.ajax.reload();
        },

        /**
        * on filter export
        */
        onFilterExport: function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            data = window.Misc.formToJson(this.$formFilters);
            data.evaluation = this.model.get('codigo_encuesta');
            window.Misc.redirect( window.Misc.urlFull( Route.route( 'formulario.evaluacion.exportar.index', data) ) );
        }
    });

})(jQuery, this, this.document);
