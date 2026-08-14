/**
* Class NotificacionUserItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.NotificacionUserItemView = Backbone.View.extend({
        tagName: 'li',
        template: _.template( ($('#user-notification-list-tpl').html() || '') ),
        events: {
            'click .danger': 'onRemoveUser'
        },
        parameters: {
            dataFilter: {}
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Events Listener
			this.listenTo( this.model, "change", this.render );
            this.listenTo( this.model, 'destroy', this.remove );
        },

        /*
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();
            this.$el.html( this.template(attributes) );
            return this;
        },

        /**
        * remove DOM element
        */
        remove: function () {
            this.$el.fadeOut('fast', function() {
                this.$el.remove();
            }.bind(this));
        },

        // remove user notification
        onRemoveUser: function (e) {
            e.preventDefault();
            e.stopPropagation();

            this.collection.remove(this.model);
        }
    });

})(jQuery, this, this.document);
