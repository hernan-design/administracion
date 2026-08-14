/**
* Class ConfirmWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @krobing
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ConfirmWindow = Backbone.View.extend({

        el: '#modal-base-confirm-tp',
        parameters: {
            template: _.template( ($('#project-confirm-remove-tpl').html() || '') ),
            titleConfirm: '',
            onConfirm: null,
            callBack: null,
            dataFilter: {},
            elementItem: null
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters );

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };
        },

        /*
        * Render View Element
        */
        render: function() {
            this.setElement(this.el);

            var attributes = {}, body_message = this.parameters.bodyMessage;

            // extend attributes confirm window
            if( this.modal instanceof Backbone.Model ) _.extend(attributes, this.modal.toJSON(), this.parameters.dataFilter);

            // delegate events
			this.stopListening();
			this.undelegateEvents();
			this.$el.off('click');
			this.delegateEvents({
				'click .confirm-action': 'onConfirm',
				'click .cancel-action': 'onCancel'
			});



            // Change modal title and content
            this.$el.find('.content-modal').html( this.model instanceof Backbone.Model && typeof this.parameters.template == 'function' ?
                this.parameters.template(body_message ? { message: body_message } : this.model.toJSON())
                : ( typeof this.parameters.template == 'function' ? this.parameters.template({}) : this.parameters.template) );
            this.$el.find('.inner-title-modal').html( this.parameters['titleConfirm'] );
            this.$el.modal("show");


            return this;
        },

        /**
        * Project Close Confirm
        */
        onConfirm: function (e) {
            console.log('en confirmWindow')
            e.preventDefault();
            var _this = this,
                type = this.parameters.type;

                this.$el.modal('hide');

            if( typeof this.parameters.onConfirm == 'function' ) {
                this.parameters.onConfirm.call(this, this.parameters.dataFilter );
    
            }else {
                console.log('no parameters');
                if (type === 'delete') {
                    this.model.destroy({
                        processData: true,
                        wait: true,
                        data: _.extend({ '_token': $('meta[name="csrf-token"]').attr('content')}, this.parameters.dataFilter),
                        success: function(model, resp) {
                            resp || (resp = {});

                            // response success or error
                            var type = resp.success ? 'success' : 'alert',
                                text = resp.success ?
                                    (resp.message !== undefined ? resp.message : '...')
                                    : resp.errors;

                            if( _.isObject( resp.errors ) ) {

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
                            };
                            _.extend( _this.configAlert, config );

                            if( resp.success ){
                                if( typeof _this.parameters.callBack == 'function' ) _this.parameters.callBack.call(_this, true, resp, _this.parameters.dataFilter);
                            }else{
                                window.Misc.showAlertBox( _this.configAlert );
                                if( typeof _this.parameters.callBack == 'function' ) _this.parameters.callBack.call(_this, false, resp, _this.parameters.dataFilter);
                            }
                        }
                    });
                } else {
                    console.log('guardando')
                    var attributes = this.model.toJSON();
                    this.model.save( { action: body_message }, { wait: true, success: function(model, resp) {
                            resp || (resp = {});

                            // response success or error
                            var type = resp.success ? 'success' : 'alert',
                                text = resp.success ?
                                    (resp.message !== undefined ? resp.message : '...')
                                    : resp.errors;

                            if( _.isObject( resp.errors ) ) {

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
                            };
                            _.extend( _this.configAlert, config );

                            if( resp.success ){
                                if( typeof _this.parameters.callBack == 'function' ) _this.parameters.callBack.call(_this, true, resp, _this.parameters.dataFilter);
                            }else{
                                window.Misc.showAlertBox( _this.configAlert );
                                if( typeof _this.parameters.callBack == 'function' ) _this.parameters.callBack.call(_this, false, resp, _this.parameters.dataFilter);
                            }
                        }
                    });
                }
            }
        },
        onCancel: function (e) {
            e.preventDefault();

            window.Misc.removeSpinner({
                wrap: this.parameters.elementItem,
                direct: false
            });
        }
   });

})(jQuery, this, this.document);
