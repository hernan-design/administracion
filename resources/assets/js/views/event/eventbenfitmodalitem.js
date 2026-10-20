/**
* Class EventBenefitModalItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventBenefitModalItemView = Backbone.View.extend({
        tagName: 'li',
        className: 'benefit-list',
        template: _.template( ($('#benefits-assist-control-event-tpl').html() || '') ),
        events: {
            //
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
        },

        /*
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();
            console.log('Entro');
            this.$el.html( this.template(attributes) );

            return this;
        }
    });

})(window.jQuery, window, document);
