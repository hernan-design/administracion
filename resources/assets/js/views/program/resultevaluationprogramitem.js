/**
* Class ResultEvaluationProgramItemView of Backbone View
* @author TuProyecto || Desarrollador : @xindykatalina
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ResultEvaluationProgramItemView = Backbone.View.extend({

        tagName: 'tr',
        template: _.template( ($('#result-evaluation-program-item-tpl').html() || '') ),
        events: {
            // 
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */ 
        initialize : function(opts){

            // Extend options parameters
            if( _.isObject(opts.parameters) ){
                _.extend(this.parameters, opts.parameters);
            }

            //Init Attributes

            // Events Listener
            this.listenTo( this.model, "change", this.render );
        },

        /*
        * Render View Element
        */
        render: function () {

            this.$el.html( this.template(this.model) )

            return this;
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
        responseServer: function (target, resp, opts) {
            
            window.Misc.removeSpinner({
                'wrap': this.$el,
                'direct': false
            });
        },

        /*
        * Remove View Element
        */
        remove: function(){
            var _this = this;
            this.$el.fadeOut('slow', function (){
                $(this).remove();
            });
        }

    });

})(window.jQuery, window, document);

