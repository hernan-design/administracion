/**
 * Class FormGroup extends of Backbone View
 * @author TuProyecto || Desarrollador : @backend01
 * @link http://TuProyecto.com
 */

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {
    app.FormGroup = Backbone.View.extend({
        templateGroup: _.template( $('#container-group-tpl').html() || ''),
        templateActionsGroups: _.template( $('#actions-question-tpl').html() || ''),
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
            _.bindAll(this, 'beforeRender', 'render', 'afterRender', 'addOne');

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

            // Instance of Models

            this.formQuestionGroupModel = new app.FormQuestionGroupModel();

            // Instance of Collections

            this.formQuestionGroup = new app.FormQuestionGroupsList();

            // Events Listener
            this.listenTo( this.collection, 'add', this.addOne );
            this.listenTo( this.collection, 'reset', this.addAll );
            this.listenTo( this.collection, 'request', this.loadSpinner );
            this.listenTo( this.collection, 'sync', this.responseServer );

        },

        /*
             * Render View Element
            */
        render: function() {
            var attributes = {};
            _.extend(attributes, this.parameters.grupos);
            attributes.editable = true;

            this.$el.html( this.templateGroup( attributes ) );

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
           var attributes = model.toJSON();
           attributes.editable = true;

           this.$el.find('#page_' + model.get('codigo_pagina_encuesta')).children('.container-group').append(this.templateGroup( attributes ));
           this.$el.find('.group_' + model.get('codigo_grupo_pregunta_encuesta')).html(this.templateActionsGroups( { group: model.get('codigo_grupo_pregunta_encuesta'), page: model.get('pagina_grupo_pregunta') } ))
        },

        addAll: function(collection) {
            
            if (collection.length > 0) {
                var group = collection.at(0).toJSON();
                group.editable = true;

                this.$el.find('#group_' + group.codigo_grupo_pregunta_encuesta).children('#title_' + group.codigo_grupo_pregunta_encuesta).children('h2').html('<strong>' + group.nombre_grupo_pregunta_encuesta + '</strong>');

                this.$el.find('.group_' + group.codigo_grupo_pregunta_encuesta).html(this.templateActionsGroups({ group: group.codigo_grupo_pregunta_encuesta, page: group.codigo_pagina_encuesta }));
            }
        },

        onDelete: function(group) {
            if (group) {
                this.$el.find('#group_' + group).remove('');
            }
        },

        loadSpinner: function() {
            // console.log('Load Spinner');
        },

        responseServer: function(model, resp, opts) {
            // console.log('Response server');
        }
    });
})(window.jQuery, window, document);
