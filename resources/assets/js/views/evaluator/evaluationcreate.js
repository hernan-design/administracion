/**
* Class EvaluationCreateView extends of Backbone View
* @author TuProyecto || Desarrolladora : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EvaluationCreateView = Backbone.View.extend({

        el: '#evaluations-content',
        template: _.template( ($('#evaluation-create-tpl').html() || '') ),
        events: {
            'submit #form-evaluation' : 'addEvaluation',
            'click #add-question-q': 'onAddQuestion',
            'click #agregarTipoEvaluacionItem': 'addQuestion',
            'click #agregar_ev': 'agregarItemEvaluacion'
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
             this.listenTo( this.model, 'change', this.render );
             // this.listenTo( this.model, 'request', this.loadSpinner );
             this.listenTo( this.model, 'sync', this.responseServer );

            // reference to elements
            this.$formCreate = this.$('#form-evaluation');
                        console.log('Formulario:', this.$formCreate);
            // Events Listener

            this.formQuestion = new app.EvaluationQuestionModel();
            this.formQuestionsList = new app.EvaluacionQuestionsList();
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

        agregarItemEvaluacion: function() {
            console.log('Click en agregarTipoEvaluacionItem');

            // Encuentra el último contenedor y obtén su índice

            var ultimoIndice = $('#numberItems').val()-1;
            console.log(ultimoIndice)
            // Clona el último contenedor
            var nuevoContenedor = $('#item-evaluation-' + ultimoIndice).clone();
            console.log(nuevoContenedor)
            // Incrementa el índice y ajusta los identificadores y valores
            var nuevoIndice = ultimoIndice + 1;

            nuevoContenedor.attr('id', 'item-evaluation-' + nuevoIndice);

            console.log(nuevoContenedor)

            // Ajusta los identificadores y valores dentro del contenedor clonado
            nuevoContenedor.find('[name^="tipo_evaluacion_item_"]').attr('name', 'tipo_evaluacion_item_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="grup_opc_eval_pregunta_"]').attr('name', 'grup_opc_eval_pregunta_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="porcentaje_item_eval_emprendi_"]').attr('name', 'porcentaje_item_eval_emprendi_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="orden_item_eval_emprendi_"]').attr('name', 'orden_item_eval_emprendi_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="grupo_item_"]').attr('name', 'grupo_item_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="nombre_item_evaluacion_"]').attr('name', 'nombre_item_evaluacion_' + nuevoIndice).val('');
            nuevoContenedor.find('[name^="descripcion_item_evaluacion_"]').attr('name', 'descripcion_item_evaluacion_' + nuevoIndice).val('');

            // Agrega el nuevo contenedor al final del formulario
            this.$('#item-evaluation-' + ultimoIndice).after(nuevoContenedor);
            $('#numberItems').val(nuevoIndice+1)
        },

        /*
         * Add Evaluation
        */
        addEvaluation: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            console.log(data);
            this.parameters.action = true;
            console.log( this.model);
            // return;
            this.model.save( data, {wait: true} );
            var baseUrl = window.location.protocol + '//' + window.location.host + '/';
            window.location.href = baseUrl + 'convocatoria';
        },

        /*
         * Add survey
        */
        addQuestion: function (e) {
            e.preventDefault();
            console.log('hola')
        },

        // Add question
        onAddQuestion: function (e) {
            // e.preventDefault();
            // e.stopPropagation();

            // // Intance new model
            // this.EvaluationQuestionModel = new app.EvaluationQuestionModel();
            // console.log(this.EvaluationQuestionModel);
            // this.onViewModal(this.EvaluationQuestionModel, {codigo_pregunta_encuesta: $(e.currentTarget).attr('data-question'), codigo_grupo_pregunta_encuesta: $(e.currentTarget).attr('data-group')}, this.parameters.view);



            e.preventDefault();

            if ( this.modalQuestionForm instanceof Backbone.View ){
                this.modalQuestionForm.stopListening();
                this.modalQuestionForm.undelegateEvents();
              }

            this.modalQuestionForm = new app.CreateEvaluationFormWindow({
                model: this.formQuestion,
                collection: this.formQuestionsList,
                parameters: {
                    group: $(e.currentTarget).data('group'),
                    page: $(e.currentTarget).data('page'),
                    version: $('#codigo_version_encuesta').find('option:selected').val(),
                    encuesta: this.model.get('codigo_encuesta'),
                },
           });
           console.log(this.modalQuestionForm);
            this.modalQuestionForm.render();
        },

        // View modal by questions
        onViewModal: function (model, data) {
            console.log(model);
            var codigo_pregunta_encuesta = '',
                codigo_grupo_pregunta_encuesta = model.get('codigo_grupo_pregunta_encuesta');

            if(!_.isUndefined(data)){
                codigo_pregunta_encuesta = data.codigo_pregunta_encuesta;
                codigo_grupo_pregunta_encuesta = data.codigo_grupo_pregunta_encuesta;
            }

            if ( this.modalQuestionForm instanceof Backbone.View ){
                this.modalQuestionForm.stopListening();
                this.modalQuestionForm.undelegateEvents();
            }

            this.modalQuestionForm = new app.CreateEvaluationFormWindow({
                model: model,
                collection: this.EvaluacionQuestionsList,
                parameters: {
                    data: {
                        codigo_encuesta: this.model.get('codigo_encuesta'),
                        codigo_pregunta_encuesta: codigo_pregunta_encuesta,
                        codigo_grupo_pregunta_encuesta: codigo_grupo_pregunta_encuesta,
                        codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                    },
                    view: this,
                }
            });
            console.log(this.modalQuestionForm);
            this.modalQuestionForm.render();
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

})(jQuery, this, this.document);
