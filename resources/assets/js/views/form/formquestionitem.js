/**
 * Class FormQuestionItem extends of Backbone View
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {
    app.FormQuestionItem = Backbone.View.extend({
        el: '#pagination',
        templateOptionsQuestions: _.template( ($('#question-block-options-tpl').html() || '' ) ),
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

            // Models

            this.FormQuestionModel = new app.FormQuestionModel();

              // Events Listener
              this.listenTo( this.collection, 'add', this.addOne );
              this.listenTo( this.collection, 'reset', this.addAll );
              this.listenTo( this.collection, 'request', this.loadSpinner );
        },

        /*
      * Render View Element
     */
        render: function() {
            // var group = this.parameters.group,
            //     renderQuestion= '',
            //     tp_question = this.parameters.data.tipo_pregunta_encuesta.split("_"),
            //     attributes = {};

            // $.extend(attributes, this.parameters.data);

            // attributes.tipo_campo_pregunta = tp_question[1];
            // attributes.codigo_pregunta_encuesta = 0;

            // switch( parseInt(tp_question[0]) ) {
            //     case window._tp.typeQuestionSurvey['open']: renderQuestion = 'templateOpenQuestion'; break;
            //     case window._tp.typeQuestionSurvey['single']: renderQuestion = 'templateSingleQuestion'; break;
            //     case window._tp.typeQuestionSurvey['multiple']: renderQuestion = 'templateMultipleQuestion'; break;
            //     case window._tp.typeQuestionSurvey['upload']: renderQuestion = 'templateUploadQuestion'; break;
            //     case window._tp.typeQuestionSurvey['matriz']: renderQuestion = 'templateMatrizQuestion'; break;
            //     default: renderQuestion = 'templateOpenQuestion'; break;
            // }

            // this.$el.find('#group_' + group).children('.container-questions').append(this[renderQuestion](attributes));
            // this.$('#question-block-options').html( this.templateQuestionBlockOptions() );

            // this.ready();
            // // return this;

            // console.log('Item');
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

            // reload plugins
            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

        },

        addAll: function(model) {
            var attributes = model.models[0].toJSON();
            
            if (attributes.clase_pregunta_encuesta_v1 == 1) {
                var view = new app.FormQuestion({
                    model: attributes,
                });

                this.$el.find('#codigo_pregunta_' + attributes.codigo_pregunta_encuesta).html( view.render().el );
    
                this.$el.find('.question_options_' + attributes.codigo_pregunta_encuesta).html( this.templateOptionsQuestions( attributes ) );
            } else {

                var view = new app.FormSubquestion({
                    model: model.models[0]
                });

                this.$el.find('#codigo_pregunta_' + attributes.codigo_pregunta_encuesta).remove();

                this.$el.find('.question_render_' + attributes.cor_codigo_pregunta_encuesta).append(view.render().el);

                this.$el.find('.subquestion_options_' + attributes.codigo_pregunta_encuesta).html(this.templateOptionsQuestions( attributes ));
            }
            
        },

        addOne: function(model) {
            var attributes = model.toJSON();
            
            console.log(attributes);

            if (!attributes.pregunta_padre) {
                
                var view = new app.FormQuestion({
                    model: attributes,
                    className: 'question_render_' + attributes.codigo_pregunta_encuesta,
                });

                this.$el.find('#page_' + attributes.codigo_pagina_pregunta_encuesta).children('.container-group').find('#group_' + attributes.codigo_grupo_pregunta_encuesta).children('.container-questions').append( view.render().el );
                this.$el.find('.question_options_' + attributes.codigo_pregunta_encuesta).html(this.templateOptionsQuestions( attributes ));
            } else {
                var view = new app.FormSubquestion({
                    model: model
                });

                this.$el.find('#page_' + attributes.codigo_pagina_pregunta_encuesta).children('.container-group').find('#group_' + attributes.codigo_grupo_pregunta_encuesta).children('.container-questions').find('.question_render_' + attributes.pregunta_padre).append(view.render().el);
                this.$el.find('.subquestion_options_' + attributes.codigo_pregunta_encuesta).html(this.templateOptionsQuestions( attributes ));
            }
        
        },

        onModalEditQuestion: function(e) {
            // e.preventDefault();

            // console.log('Entra a Form Question Item');

            // this.FormQuestionModel.clear().set(this.FormQuestionModel.defaults);

            // this.FormQuestionModel.fetch({data: {codigo_pregunta_encuesta: $(e.currentTarget).data('question'), codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),  edit: true}, success: function(model, resp) {

            //     if ( this.modalQuestionForm instanceof Backbone.View ){
            //         this.modalQuestionForm.stopListening();
            //         this.modalQuestionForm.undelegateEvents();
            //     }

            //       this.modalQuestionForm = new app.QuestionFormWindow({
            //             model: model,
            //             collection: this.formQuestionsList,
            //             parameters: {
            //                 data: {
            //                     codigo_pagina: $(e.currentTarget).data('page'),
            //                     codigo_pregunta_encuesta: $(e.currentTarget).data('question'),
            //                     codigo_grupo_pregunta_encuesta: $(e.currentTarget).data('group'),
            //                     codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
            //                     update: true
            //                 },
            //                 view: this,
            //             }
            //       });
            //       this.modalQuestionForm.render();
            //     }
            // });
        },

        // onModalDeleteQuestion: function(e) {
        //     e.preventDefault();

        //     var question = $(e.currentTarget).data('question'), _this = this;

        //     if ( this.modalFormQuestionDelete instanceof Backbone.View ){
        //         // console.log('');
        //         this.modalFormQuestionDelete.stopListening();
        //         this.modalFormQuestionDelete.undelegateEvents();
        //     }

        //     this.modalFormQuestionDelete = new app.FormWindow({
        //         model: this.FormQuestionModel,
        //         parameters: {
        //             title: 'Eliminar pregunta',
        //             template: _.template( ($('#confirmation-remove-question-tpl').html() || '') ),
        //             data: {
        //                 codigo_version_encuesta: $('#codigo_version_encuesta').find('option:selected').val(),
        //                 question: question,
        //                 removeQuestion: true,
        //             },
        //         }
        //     });

        //     this.modalFormQuestionDelete.render();
        // },

        onDeleteQuestion: function(model) {
            var attributes = model.toJSON();

            console.log('Eliminando pregunta...');

            if (this.parameters.respDestroy.class_question == 1) {
                this.$el.find('.question_render_' + attributes.codigo_pregunta_encuesta).remove();
            } else {
                this.$el.find('#codigo_pregunta_' + attributes.codigo_pregunta_encuesta).remove();
            }
            
        },

        // Render modal for question
        onModalQuestionEdit: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.parameters.view.trigger('edit', this.model);
            // this.onViewModal(this.model);
        }

    });

})(jQuery, this, this.document);
