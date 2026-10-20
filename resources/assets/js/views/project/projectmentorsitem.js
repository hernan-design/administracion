/**
* Class ProjectMentorsItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ProjectMentorsItemView = Backbone.View.extend({
        tagName: 'li',
        template: _.template( ($('#user-mentor-list-tpl').html() || '') ),
        templateConfirm: _.template($("#confirm-mentoring-tpl").html( ) || ""),
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
                id = $(e.currentTarget).data('id');

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm({
                        action: 'eliminar',
                        nombre_mentor: $(e.currentTarget).attr('data-name')
                    }),
                    titleConfirm: "Eliminar usuario",
                    onConfirm: function () {
                        this.model.destroy({
                            processData: true,
                            wait: true,
                            data: id,
                            success: function(model, resp) {
                                window.Misc.showAlertBox({
                                    text: resp.message !== undefined ? resp.message : '...',
                                    type: 'success'
                                });
    
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el.parents(),
                                    'direct': false
                                });
                            }
                        });

                        // if( respState ) {
                        //     window.Misc.showAlertBox({
                        //         text: resp.message !== undefined ? resp.message : '...',
                        //         type: 'success'
                        //     });

                        //     window.Misc.removeSpinner({
                        //         'wrap': _this.$el.parents(),
                        //         'direct': false
                        //     });

                        //     collection.remove(_this.model);
                        // }
                    }
                }
            });

            this.acceptConfirm.render();
        }
    });

})(window.jQuery, window, document);
