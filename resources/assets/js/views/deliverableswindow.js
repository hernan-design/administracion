/**
* Class DeliverablesWindow extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DeliverablesWindow = Backbone.View.extend({

        el: '#modal-form',
        events: {
            'submit #form-modal-all': 'onSubmit',
            'click .cancel-action': 'onModalDestroy',
		},
        parameters: {
            template: _.template( ($('#comment-refused-deliverables-tpl').html() || '') ),
            templateApprove: _.template( ($('#comment-approve-deliverables-tpl').html() || '') ),
            dataFilter: { },
            data: { },
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends attributtes
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = _.extend({}, this.parameters, opts.parameters );

            // Init attributes
            this.$formModal = this.$('#form-modal-all');

            // Init Attributes
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };


            this.listenTo( this.model, 'request', this.loadSpinner );
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = {},
                title = 'Aceptación de entregables',
                paramsTemplate = this.parameters.templateApprove(this.model.toJSON());

            // extend attributes confirm window
            if( this.model instanceof Backbone.Model ) _.extend(attributes, this.model);

            // Change modal text and content
            if(this.parameters.data['refused_deliverables']){
                title = 'Devolución de entregables';
                paramsTemplate = this.parameters.template(this.model.toJSON());
            }

            this.$el.find('.modal-title').text( title );
            this.$el.find('.content-modal').html( paramsTemplate );

            this.$el.modal("show");

            return this;
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof(window.initComponent.autosizeTrigger) == 'function')
                window.initComponent.autosizeTrigger();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$formModal );
        },

        /*
        * Submit data
        */
        onSubmit: function (e) {
            e.preventDefault();

            var _this = this,
                data = window.Misc.formToJson( this.$formModal );
                 _.extend(data, this.parameters.data);

            this.model.save(data, {wait: true,
                success: function (model, resp) {
                    // complete proccess
                    window.Misc.removeSpinner({
                        'wrap': _this.$formModal,
                        'direct': false
                    });

                    _this.$el.modal().off();
                    _this.$el.modal('hide');

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
                            location.reload();
                        }else{
                            window.Misc.showAlertBox( confCrop );
                        }
                    }
                }
            });
        },

        onModalDestroy: function (e){
            this.$el.modal().off();
        }
   });

})(jQuery, this, this.document);
