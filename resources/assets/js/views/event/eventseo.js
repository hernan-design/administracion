/**
* Class EventSeoView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventSeoView = Backbone.View.extend({
        el: '#tab_general',
        events: {
            'submit #form-event-seo-general': 'addEventSeo'
        },
        parameters: {
            codigo_contenido: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            _.bindAll(this, 'beforeRender', 'render', 'afterRender');

            // extends parameters
            if( opts.parameters !== undefined && _.isObject(opts.parameters) ){
                this.parameters = $.extend({}, this.parameters, opts.parameters);
            }

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
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

            // Events listener custom
            this.on( 'beforeRender', this.beforeRender );
            this.on( 'afterRender', this.afterRender );
        },

        /*
        * Render View Element
        */
        render: function() {
            // Initialize attributes
            this.$tags_evento = this.$('#tags_evento');

            if(this.model.id){
                this.referenceViews();
            }
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
            this.renderTagsPost();
        },

        /**
        * reference to views
        */
        referenceViews: function () {
            //
        },

        /*
         * Add event seo
        */
        addEventSeo: function (e) {
            e.preventDefault();

            var data = window.Misc.formToJson( e.target );
            data.seo = true;
            data.seo_general = true;
            data.update_tags = true;

            this.model.save( data, {patch: true, wait: true} );
        },

        /**
        * Init Tags Input
        */
        renderTagsPost: function () {

            this.$tags_evento.tagsInput({
                'autocomplete_url': document.url + Route.route('tag.index'),
                'autocomplete': { selectFirst:true, width:'300px', autoFill:true },
                'height': 'auto',
                'width': '100%',
                'interactive': true,
                'defaultText': 'Agrega una etiqueta',
                'removeWithBackspace' : true,
                'minChars' : 1,
                'placeholderColor' : '#666666'
            });
        }
   });

})(window.jQuery, window, document);
