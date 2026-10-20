/**
 * Class FormSubquestion extends of Backbone View
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {
    app.FormSubquestion = Backbone.View.extend({
        templateOpenSubQuestion: _.template( ($('#form-open-subquestion-tpl').html() || '') ),
        templateSubSingleQuestion: _.template( ($('#form-sub-single-answer-tpl').html() || '') ),
        templateMultipleSubQuestion: _.template( ($('#form-multiple-sub-answer-tpl').html() || '') ),
        templateUploadSubQuestion: _.template( ($('#form-upload-subquestion-tpl').html() || '') ),
        templateMatrizSubQuestion: _.template( ($('#form-matriz-subquestion-tpl').html() || '') ),
        templateActions: _.template( ( $('#actions-sub-question-tpl').html()  || '' ) ),
        templateQuestionBlockOptions: _.template( ( $('#question-block-options-tpl').html()  || '' ) ),
        events: {
            //
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
        },

        /*
            * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();

            switch( parseInt(attributes.tipo_pregunta_encuesta) ) {
                case window._tp.typeQuestionSurvey['open']: renderQuestion = 'templateOpenSubQuestion'; break;
                case window._tp.typeQuestionSurvey['single']: renderQuestion = 'templateSubSingleQuestion'; break;
                case window._tp.typeQuestionSurvey['multiple']: renderQuestion = 'templateMultipleSubQuestion'; break;
                case window._tp.typeQuestionSurvey['upload']: renderQuestion = 'templateUploadSubQuestion'; break;
                case window._tp.typeQuestionSurvey['matriz']: renderQuestion = 'templateMatrizSubQuestion'; break;
                default: renderQuestion = 'templateOpenQuestion'; break;
            }

            this.$el.html(this[renderQuestion]( attributes ));

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

            // reload plugins
            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

        },
    });
})(window.jQuery, window, document);
