/**
 * Class FormQuestion extends of Backbone View
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {
    app.FormQuestion = Backbone.View.extend({
        templateOpenQuestion: _.template( ($('#form-open-question-tpl').html() || '') ),
        templateSingleQuestion: _.template( ($('#form-single-answer-tpl').html() || '') ),
        templateMultipleQuestion: _.template( ($('#form-multiple-answer-tpl').html() || '') ),
        templateUploadQuestion: _.template( ($('#form-upload-question-tpl').html() || '') ),
        templateMatrizQuestion: _.template( ($('#form-matriz-question-tpl').html() || '') ),
        templateActions: _.template( ( $('#actions-sub-question-tpl').html()  || '' ) ),
        events: {
            'click .edit-question': 'onModalEditQuestion',
            'click .delete-question': 'onModalDeleteQuestion',
        },
        parameters: {
            //
        },

        /**
         * Constructor Method
         */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Collections

            this.formQuestionsList = new app.FormQuestionsList();

            // Models

            this.FormQuestionModel = new app.FormQuestionModel();

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
            // this.listenTo( this.collection, 'sync', this.responseServer );
        },

        /*
       * Render View Element
      */
        render: function() {
            var renderQuestion = '';

            switch( parseInt(this.model.tipo_pregunta_encuesta) ) {
                case window._tp.typeQuestionSurvey['open']: renderQuestion = 'templateOpenQuestion'; break;
                case window._tp.typeQuestionSurvey['single']: renderQuestion = 'templateSingleQuestion'; break;
                case window._tp.typeQuestionSurvey['multiple']: renderQuestion = 'templateMultipleQuestion'; break;
                case window._tp.typeQuestionSurvey['upload']: renderQuestion = 'templateUploadQuestion'; break;
                case window._tp.typeQuestionSurvey['matriz']: renderQuestion = 'templateMatrizQuestion'; break;
                default: renderQuestion = 'templateOpenQuestion'; break;
            }

            this.$el.html(this[renderQuestion]( this.model ));

            return this;
        },

        /**
         * Fires before of run render function
         */
        beforeRender: function () {
            //
        },

        afterRender: function () {
            this.ready();
        },

        /**
         * fires libraries js
         */
        ready: function () {
            var _this = this;

            if( typeof window.initComponent.colorpickerTrigger == 'function' ){
                window.initComponent.colorpickerTrigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            // input mask
            this.$(":input").inputmask();
        },

        addOne: function(model) {
            // var attributes = model.toJSON();

            // this.$el.find('#page_' + this.parameters.code_page).children('.container-group').find('#group_' + model.get('codigo_grupo_pregunta_encuesta')).children('.container-questions').append(this.templateOpenQuestion( attributes ));
            // console.log(this.$el.find('#page_' + this.parameters.code_page).children('.container-group').find('#group_' + attributes.codigo_grupo_pregunta_encuesta).children('.codigo_pregunta_' + attributes.codigo_pregunta_encuesta));

            // if (this.$el.find('#page_' + this.parameters.code_page).children('.container-group').find('#group_' + attributes.codigo_grupo_pregunta_encuesta).children('.codigo_pregunta_' + attributes.codigo_pregunta_encuesta).length > 0) {
            //     this.$el.find('#page_' + this.parameters.code_page).children('.container-group').find('#group_' + model.get('codigo_grupo_pregunta_encuesta')).children('.container-questions').append(this.templateOpenQuestion( attributes ));
            // }
        },

        addAll: function(model) {

            // model.forEach(this.addOne, this);
        },

        onModalEditQuestion: function(e) {
          e.preventDefault();

          this.FormQuestionModel.clear().set(this.FormQuestionModel.defaults);

          this.FormQuestionModel.fetch({data: {codigo_pregunta_encuesta: $(e.currentTarget).data('question'), codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),  edit: true}, success: function(model, resp) {

                if ( this.modalQuestionForm instanceof Backbone.View ){
                    this.modalQuestionForm.stopListening();
                    this.modalQuestionForm.undelegateEvents();
                }

                this.modalQuestionForm = new app.QuestionFormWindow({
                      model: model,
                      collection: this.formQuestionsList,
                      parameters: {
                          data: {
                              codigo_pagina: $(e.currentTarget).data('page'),
                              codigo_pregunta_encuesta: $(e.currentTarget).data('question'),
                              codigo_grupo_pregunta_encuesta: $(e.currentTarget).data('group'),
                              codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                              update: true
                          },
                          view: this,
                      }
                });
                console.log(this.modalQuestionForm);
                this.modalQuestionForm.render();
              }
          });

            // var attributes = new Backbone.Model({});
            //
            // attributes.set(this.model);
        },

        onModalDeleteQuestion: function(e) {
          e.preventDefault();

            var question = $(e.currentTarget).data('question'), _this = this;

            // console.log(this.modalFormQuestionDestroy);

            // this.modalFormQuestionDestroy.stopListening();
            // this.modalFormQuestionDestroy.undelegateEvents();

            this.modalFormQuestionDestroy = new app.FormWindow({
                model: this.FormQuestionModel,
                parameters: {
                    title: 'Eliminar pregunta',
                    template: _.template( ($('#confirmation-remove-question-tpl').html() || '') ),
                    data: {
                        codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
                        question: question,
                        removeQuestion: true,
                    },
                }
            });

            this.modalFormQuestionDestroy.render();

            // console.log(this);
        },

        loadSpinner: function() {
            console.log('Load');
        },

        responseServer: function () {
            console.log('Response');
        }
    });
})(jQuery, this, this.document);
