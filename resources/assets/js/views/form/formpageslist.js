/**
 * Class FormPagesList extends of Backbone View
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {
    app.FormPagesList = Backbone.View.extend({
        templateCreatePagination: _.template( $('#container-btn-pages-tpl').html() || ''),
        templateActions: _.template( $('#actions-group-tpl').html() || ''),
        templatePage: _.template( $('#container-pages-tpl').html() || ''),
        templateActionsGroups: _.template( $('#actions-question-tpl').html() || ''),
        templateOptionsQuestions: _.template( ($('#question-block-options-tpl').html() || '' ) ),
        templateAddOptions: _.template( ($('#actions-sub-question-tpl').html() || '' ) ),
        events: {
            'click .add-page': 'onAddPages',
        },
        parameters: {
            pages: [],
            codigo_encuesta: '',
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

            this.formQuestionGroup = new app.FormQuestionGroupsList();
            this.formQuestionGroup.fetch({data: {formulario: this.parameters.data.codigo_encuesta}, reset: true});

            this.formQuestionsList = new app.FormQuestionsList();
            this.formQuestionsList.getFirstPage({ reset: true }).then(function() {
                if (!this.parameters.page) {
                    this.FormSubquestions = new app.FormSubquestionsList();
                    this.FormSubquestions.fetch({ data: { codigo_encuesta: this.parameters.data.codigo_encuesta, 'codigo_version_encuesta': $('#codigo_version_encuesta').find('option:selected').val() }, success: function(model) {
                            this.onResetSubQuestions(model);
                        }.bind(this)
                    });
                }
            }.bind(this));



            // Init Wrappers

            this.$titlePagination = $('.title-pagination');

            this.listenTo( this.collection, 'add', this.render );
            this.listenTo( this.collection, 'reset', this.onReset );

            this.listenTo(this.formQuestionGroup, 'reset', this.onResetGroups );

            this.listenTo(this.formQuestionsList, 'reset', this.onResetQuestions );
        },

        /*
       * Render View Element
       */
        render: function() {
            this.$titlePagination.html( this.templateCreatePagination );

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

        onAddPages: function() {
            var page = this.parameters.page, attributes = this.model.toJSON();

            console.log(attributes);

            this.$el.append(this.templatePage({ pagina: page, codigo_page: attributes.codigo_pagina_encuesta }));

            this.$el.find('.page_' + attributes.codigo_pagina_encuesta).html(this.templateActions( {pagina: page, codigo_page: attributes.codigo_pagina_encuesta } ))
        },

        onAdd: function (model) {
            var page = model.pagina_grupo_pregunta, code_page =  model.codigo_pagina_encuesta;

            this.$el.append(this.templatePage({ pagina: page, codigo_page: code_page }));
            this.$el.find('.page_' + code_page).html( this.templateActions({ pagina: page, codigo_page: code_page }) );
        },

        onReset: function(collection) {
            var paginas = collection.models[0].attributes.paginas;
            this.$el.html('');
            paginas.forEach(this.onAdd, this);
        },

        onAddGroups: function(model) {
            var page = model.get('pagina_grupo_pregunta'), code_page = model.get('codigo_pagina_encuesta'), dataGroup = model.toJSON();

            var view = new app.FormGroup({
                model: model,
                tagName: 'section',
                collection: this.formQuestionGroup,
                parameters: {
                    page: page,
                    grupos: dataGroup,
                }
            });

            this.$el.find('#page_' + code_page).children('.container-group').append(view.render().el);
            this.$el.find('.group_' + dataGroup.codigo_grupo_pregunta_encuesta).html(this.templateActionsGroups( { group: dataGroup.codigo_grupo_pregunta_encuesta, page: dataGroup.codigo_pagina_encuesta } ));
        },

        onResetGroups: function(collection) {

            collection.forEach(this.onAddGroups, this);
        },

        onAddQuestion: function(model) {
            var view = new app.FormQuestion({
               model: model,
               className: 'question_render_' + model.codigo_pregunta_encuesta,
               collection: this.formQuestionsList,
                parameters:{ data: this.parameters.data.preguntas }
            });

            this.$el.find('#page_' + model.codigo_pagina_pregunta_encuesta).children('.container-group').find('#group_' + model.codigo_grupo_pregunta_encuesta).children('.container-questions').append(view.render().el);
            this.$el.find('.question_options_' + model.codigo_pregunta_encuesta).html(this.templateOptionsQuestions( model ));
        },

        onResetQuestions: function() {
            var questions = this.parameters.data.preguntas;

            questions.forEach(this.onAddQuestion, this);
        },

        onAddSubQuestions: function(model) {
            var attributes = model.toJSON();

            var view = new app.FormSubquestion({
                model: model,
                collection: this.FormSubquestions
            });

            this.$el.find('.question_render_' + model.get('cor_codigo_pregunta_encuesta')).append(view.render().el);
            this.$el.find('.subquestion_options_' + model.get('codigo_pregunta_encuesta') ).html(this.templateOptionsQuestions( attributes ));
        },

        onResetSubQuestions: function(collection) {
            console.log('Subpreguntas');
            collection.forEach(this.onAddSubQuestions, this);
        }
    });
})(window.jQuery, window, document);
