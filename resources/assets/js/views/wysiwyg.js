/**
* Class WysiwygView extends of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.WysiwygView = Backbone.View.extend({
        template: _.template( ($('#bootstrap-wysiwyg-tpl').html() || '') ),
        events: {
            // 
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            if ( opts !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = $.extend({},this.parameters, opts.parameters);
            };

            // Render
            this.render();
        },

        /*
        * Render View Element
        */
        render: function() {

            var attributes = {},
            _this = this;
            this.$el.html( this.template(attributes) );

            return this;
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {
            window.Misc.setSpinner( this.$el );
        }
    });    

})(jQuery, this, this.document);
