/**
* Class ActivityLogItemView extend of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ActivityLogItemView = Backbone.View.extend({

        tagName: 'li',
        template: _.template( ($('#activities-log-tpl').html() || '') ),
        events: {
        },

        /**
        * Constructor Method
        */
        initialize : function(){
            //Init Attributes

            //Events Listener
            this.listenTo( this.model, 'change', this.render );
        },

        /*
        * Render View Element
        */
        render: function(){

            var attributes = this.model.toJSON();
            this.$el.html( this.template(attributes) );

            return this;
        }
    });

})(window.jQuery, window, document);
