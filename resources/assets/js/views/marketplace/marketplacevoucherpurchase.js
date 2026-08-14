/**
* Class MarketplaceAssignmentMemberView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceVoucherPurchaseView = Backbone.View.extend({
        tagName: 'tr',
        template: _.template( ($('#marketplace-voucher-purchase-tpl').html() || '') ),
        events: {
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters);

            // initialize attributes

            // Events listeners
            this.listenTo(this.model, 'destroy', this.remove);
            this.listenTo(this.model, 'request', this.loadSpinner);
            this.listenTo(this.model, 'sync', this.responseServer);
        },

        /**
        * Render View Element
        */
        render: function () {
            var attributes = this.model.toJSON();

            this.$el.html( this.template(attributes) );

            // references to elements

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

        /**
        * Load spinner on the request
        */
        loadSpinner: function ( target, xhr, opts ) {

            if( target instanceof Backbone.Model )
                window.Misc.setSpinner( this.$el );
        },

        /**
        * response of the server
        */
        responseServer: function ( target, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$el,
                direct: false
            });
        }
    });

})(jQuery, this, this.document)
