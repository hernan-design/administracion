/**
* Class ProgramUserItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProgramUserItemView = Backbone.View.extend({
        tagName: 'li',
        template: _.template( ($('#user-program-list-tpl').html() || '') ),
        events: {
            'click .danger': 'onModalConfirm'
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

        onModalConfirm: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                collection = this.model.collection;

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: _.template($("#confirm-user-tpl").html() || ""),
                    titleConfirm: "Eliminar usuario",
                    callBack: function (respState, resp, dataFilter) {

                        if( respState ) {
                            window.Misc.showAlertBox({
                                text: resp.message !== undefined ? resp.message : '...',
                                type: 'success'
                            });

                            window.Misc.removeSpinner({
                                'wrap': _this.$el.parents(),
                                'direct': false
                            });
                            collection.remove(_this.model);
                        }
                    }
                }
            });

            this.acceptConfirm.render();
        }
    });

})(jQuery, this, this.document);
