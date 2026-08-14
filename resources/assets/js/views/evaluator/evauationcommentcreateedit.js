/**
* Class EvaluationCommentCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EvaluationCommentCreateEditView = Backbone.View.extend({
        el: '#evaluations-content',
        templateEvaluation: _.template( ($('#evaluation-comment-tpl').html() || '') ),
        events: {
            'submit #form-evaluation-comment': 'addEvaluationComment',
        },
        parameters: {
            'data': []
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');
             console.log('EvaluationCommentCreateEditView ');
            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            //Init Attribute
            this.configAlert = {
                'wrapper': '#evaluations-wrapper',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            var _this = this;
            this.render = _.wrap(this.render, function(render) {

                _this.trigger('beforeRender');

                render();

                _this.trigger('afterRender');

                return _this;
            });

            // Events Listener
            this.listenTo( this.model, 'change', this.render );
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {
            // references

            this.$formCreate = this.$('#form-evaluation-comment');
            this.evaluationComment = this.$el.find('#evaluation-comment');
            this.evaluationComment.html(this.templateEvaluation(this.model.toJSON()));

            this.ready();
            this.referenceViews();

            return this;

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

            // render uploader and cropper of image

        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins

            if( typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof(window.initComponent.autosizeTrigger) == 'function'){
                window.initComponent.autosizeTrigger();
            }

            if( typeof window.initComponent.icheckTrigger == 'function' ){
                window.initComponent.icheckTrigger();
            }

            if(typeof window.initComponent.multiselectTrigger == 'function' ){
                window.initComponent.multiselectTrigger();
            }
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            this.ready();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$('#section-content') )
        },

        /**
        * Add new program
        */
        addEvaluationComment: function (e) {
            e.preventDefault();
            var _this = this;



            var data = window.Misc.formToJson(e.target);
            console.log('addEvaluationComment', data);
            this.model.save(data, { wait: true, patch: true});

        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            var _this = this;
            window.Misc.removeSpinner({
                wrap: this.$('#section-content'),
                direct: false
            });

            // response success or error
            var type = resp.success ? 'success' : 'alert',
                text = resp.success ?
                    (resp.message !== undefined ? resp.message : '...')
                    : resp.errors;

            if( _.isObject( resp.errors ) ) {

                var listError = '<ul>';

                $.each(resp.errors, function(field, item) {
                    listError += '<li>'+ item[0] +'</li>';
                });
                listError += '</ul>';

                text = listError;
            }

            var conf = {
                'text': text,
                'type': type
            };
            _.extend(this.configAlert, conf);

            //complete proccess
            if( resp.success !== undefined ) {
                if( resp.success ){
                    window.setTimeout(function() {
                        window.Misc.showAlertBox(_this.configAlert);
                    }, 1000);
                    if(resp.redirect) {
                        window.setTimeout(function() {
                            window.Misc.redirect(app.AppRouter.navigate(Route.route('evaluacion.comentario.index')));
                        }, 3000);
                    }
                }else {
                    window.Misc.showAlertBox(this.configAlert);
                    return false;
                }
            }
        },
    });
})(jQuery, this, this.document);
