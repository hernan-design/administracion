/**
* Class EventSessionsItemView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.EventSessionsItemView = Backbone.View.extend({
        tagName: 'li',
        className: 'dd-item dd3-item',
        template: _.template( ($('#item-sessions-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-session-event-tpl').html() || '') ),
        events: {
            'click .edit-session': 'onEdit',
            'click .success': 'onModalConfirm',
            'click .danger': 'onModalConfirm'
        },
		parameters: {
		   //
		},
        /**
        * Constructor Method
        */
        initialize: function (opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ) {
                this.parameters = _.extend({}, this.parameters, opts.parameters);
            }

            this.$el.data('codigo_sesion_evento', this.model.get('codigo_sesion_evento'));
            this.$el.data('numero_sesion', this.model.get('numero_sesion'));

            // Events Listener
			this.listenTo( this.model, "change", this.render );
        },

        /*
        * Render View Element
        */
        render: function () {

            var attributes = this.model.toJSON();
			this.$el.html( this.template(attributes) );

            return this;
        },

        /*
        * Modal edit content
        */
        onEdit: function (e) {
            e.preventDefault();

            this.eventSessionsWindow = new app.EventSessionsWindow({
                model: this.model,
                parameters: {
                    data:{
                        codigo_evento: this.model.get('codigo_evento'),
                    }
                }
            });
            this.eventSessionsWindow.render();
        },

        /*
        * Modal Confirm
        */
        onModalConfirm: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this,
                action = $(e.currentTarget).attr('data-action'),
                event_name = $(e.currentTarget).attr('data-name'),
                data = {
                    'codigo_sesion_evento': $(e.currentTarget).data('id'),
                    'change_state': true
                };

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm({
                        action : action,
                        event_name: event_name
                    }),
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        this.model.save(data, {wait: true,
                            success: function (model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': _this.$el,
                                    'direct': false
                                });

                                // response success or error
                                var type = resp.success ? 'success' : 'alert',
                                    text = resp.success ?
                                        (resp.message !== undefined ? resp.message : '...')
                                        : resp.errors;

                                if( _.isObject( resp.errors ) ){

                                    var listError = '<ul>';

                                    $.each(resp.errors, function(field, item) {
                                         listError += '<li>'+ item[0] +'</li>';
                                    });
                                    listError += '</ul>';

                                    text = listError;
                                }

                                var config = {
                                    'text': text,
                                    'type': type
                                }
                                confCrop =  $.extend({}, _this.configAlert, config);

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox( confCrop );
                                    }else{
                                        window.Misc.showAlertBox( confCrop );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            this.acceptConfirm.render();
        },
    });

})(window.jQuery, window, document);
