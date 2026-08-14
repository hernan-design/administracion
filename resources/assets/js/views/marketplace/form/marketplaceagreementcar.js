/**
* Class SaleAgreementCarView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

	app.SaleAgreementCarView = Backbone.View.extend({

		tagName: 'tr',
		templateActivity: _.template( ($('#item-agreement-sale-activity-tpl').html() || '') ),
		templateDeliverable: _.template( ($('#item-agreement-sale-deliverable-tpl').html() || '') ),
        templateAdviser: _.template( ($('#item-agreement-sale-adviser-tpl').html() || '') ),
        templateMember: _.template( ($('#item-agreement-sale-businessman-tpl').html() || '') ),
		events: {
			'click .remove-item-agreement'   : 'onRemove'
		},
		parameters: {
		   tab: '',
           parentview: null,
		   template: 'templateActivity'
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
		},

		// view render
		render : function () {
            var _this = this,
                attributes = this.model.toJSON();

			attributes['tab'] = this.parameters.tab;
            attributes.codigo_usuario_entidad = this.parameters.codigo_usuario_entidad;

			this.$el.html( this[this.parameters.template](attributes) );

            // instance editing view
            this.editingView = new app.EditingView({
                el: this.el,
                model: this.model,
                parameters: {
                    callback: function( resp ) {
                        if(resp.success) {
                            // Render actions buttons
                            _this.parameters.parentview.model.set('codigo_estado', resp.codigo_estado_acuerdo, {silent: true});
                            _this.parameters.parentview.renderActionsBtn();
                        }
                    }
                }
            });

			return this;
		},

        /**
        * Remove item Agreement
        */
		onRemove: function (e) {
			e.preventDefault();

            this.acceptConfirm = new app.ConfirmWindow({
                model: this.model,
                parameters: {
                    template: _.template($("#remove-item-agreement-confirm-tpl").html() || ""),
                    titleConfirm: "Eliminar item de acuerdo",
                    callBack: function(stateResp, resp) {
                        // if response is success
                        if(stateResp) {
                            // Remove item
                            this.$el.fadeOut('fast', function() {
                               this.remove();
                            }.bind(this));

                            // Render actions buttons
                            this.parameters.parentview.model.set('codigo_estado', resp.codigo_estado_acuerdo, {silent: true});
                            this.parameters.parentview.renderActionsBtn();

                            // Show alert
                            window.Misc.showAlertBox({
                                text: resp.message,
                                type: 'success',
                                closeTime: 5000
                            });
                        }
                    }.bind(this)
                }
            });
            this.acceptConfirm.render();
		}
	});

})(jQuery, this, this.document);
