/**
* Class MarketplaceAssignmentMemberView extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.MarketplaceAssignmentMemberView = Backbone.View.extend({
        tagName: 'tr',
        template: _.template( ($('#marketplace-assignment-member-tpl').html() || '') ),
        events: {
            'click .remove-member': 'onRemove'
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
        },

        /**
        * remove item and send a request to the server
        */
        onRemove: function (e) {
            e.preventDefault();

            var deleteConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: _.template( ($('#msg-confirm-remove-member-tpl').html() || '') ),
                    titleConfirm: '¡Quitar integrante!',
                    callBack: function (respState, resp, dataFilter) {

                        if( respState ) {
                            window.Misc.showAlertBox({
                                text: resp.message !== undefined ? resp.message : '...',
                                type: 'success'
                            });
                        }
                    }
                }
            });
            deleteConfirm.render();
        }
    });

})(jQuery, this, this.document)
