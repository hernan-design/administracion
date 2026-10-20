/**
* Class ForumCategoriesItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ForumCategoriesItemView = Backbone.View.extend({
        tagName: 'tr',
        template: _.template( ($('#list-assignment-category-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-forum-category-tpl').html() || '') ),
        events: {
            'click .edit': 'onEdit',
            'click .danger': 'onDelete'
        },
        parameters: {
            //
        },

        /**
        * Constructor Method
        */
        initialize: function (opts) {
            if( opts !== undefined && _.isObject(opts.parameters) ){
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            // Events listeners
            this.listenTo(this.model, 'change', this.render);
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

            if( target instanceof Backbone.Model ){
                window.Misc.setSpinner( this.$el );
            }
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
        * Edit item and send a request to the server
        */
        onEdit: function (e) {
            e.preventDefault();

            var editFormWindow = new app.FormSampleWindow({
                model: this.model,
                parameters: {
                    edit: true,
                    template: _.template( ($('#create-edit-forum-category-tpl').html() || '') ),
                    title: 'Editar categoría'
                }
            });
            editFormWindow.render();
        },

        /**
        * Delete item and send a request to the server
        */
        onDelete: function (e) {
            e.preventDefault();

            var action = $(e.currentTarget).attr('data-action'),
                category_name = $(e.currentTarget).attr('data-name');

            var deleteConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    type: action,
                    template: this.templateConfirm( {
                        action : action,
                        category_name: category_name
                    } ),
                    titleConfirm: 'Eliminar categoría',
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

})(window.jQuery, window, document);
