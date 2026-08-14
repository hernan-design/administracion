/**
* Class EventOrganizerListView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.EventOrganizerListView = Backbone.View.extend({
        tagName: 'li',
        templateThemeCourse: _.template( ($('#item-organizers-tpl').html() || '') ),
        templateConfirm: _.template( ($('#confirm-remove-organizer-tpl').html() || '') ),
		events: {
            'click .remove-theme': 'onModalConfirm'
		},
		parameters: {
		   //
		},

		/**
		* Constructor Method
		*/
		initialize : function(opts) {

			// extend parameters
			if(!_.isUndefined(opts) && _.isObject(opts.parameters))
				this.parameters = $.extend({}, this.parameters, opts.parameters);

			// Events Listener
			this.listenTo( this.model, 'change', this.render );
            this.listenTo( this.model, 'remove', this.remove );
            this.listenTo( this.model, 'sync', this.responseServer);
		},

		// view render
		render : function () {
			var attributes = this.model.toJSON();
			this.$el.html( this.templateThemeCourse(attributes) );

			return this;
		},

        /**
		* fires libraries js and functions
		*/
        ready: function () {
            // init component
            if( typeof window.initComponent.configEllipsis == 'function' ){
                window.initComponent.configEllipsis();
            }
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
        responseServer: function ( model, resp, opts ) {

            window.Misc.removeSpinner({
                wrap: this.$el,
                direct: false
            });
        },

        /**
        * remove DOM element
        */
        remove: function () {
            this.$el.fadeOut('fast', function() {
                this.$el.remove();
            }.bind(this));
        },

        /*
        * Modal Confirm
        */
        onModalConfirm: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = this;

            if( this.acceptConfirm instanceof Backbone.View ) {
                this.acceptConfirm.stopListening();
                this.acceptConfirm.undelegateEvents();
            }

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: this.templateConfirm,
                    titleConfirm: "Confirmación",
                    onConfirm: function () {
                        this.model.destroy({
                            success: function (model, resp) {
                                // complete proccess
                                window.Misc.removeSpinner({
                                    'wrap': this.$el,
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

                                //complete proccess
                                if( resp.success !== undefined ){
                                    if( resp.success ){
                                        window.Misc.showAlertBox({
                                            text: resp.message,
                                            type: 'success',
                                            closeTime: 5000
                                        });
                                    }else{
                                        window.Misc.showAlertBox({
                                            text: resp.error,
                                            type: 'alert',
                                            closeTime: 5000
                                        });
                                    }
                                }
                            }
                        });
                    }
                }
            });
            this.acceptConfirm.render();
        }
	});

})(jQuery, this, this.document);
