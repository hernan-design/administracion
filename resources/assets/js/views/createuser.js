/**
* Class ComponentCreateUserView  of Backbone
* @author TuProyecto || @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ComponentCreateUserView = Backbone.View.extend({

        templateCreateUser: _.template( ($('#create-user-tpl').html() || '') ),
        templateIdentifications: _.template( ($('#identification-user-tpl').html() || '') ),
		events: {
            'submit #form-user': 'onStore',
            'keypress #identificacion_usuario': 'onMaxlengthField',
            'change #tipo_identificacion_usuario': 'onChangeTypeID'
		},
        parameters: {
            view: false
        },

        /**
        * Constructor Method
        */
		initialize: function(opts) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                _.extend(this.parameters, opts.parameters);
            }

            //Init Attribute
            this.configAlert = {
                'wrapper': '#main-wrap',
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            this.listenTo( this.model, 'request', this.loadSpinner);
            this.listenTo( this.model, 'sync', this.responseServer);
        },

        /*
        * Render View Element
        */
        render: function() {
            this.$el.html( this.templateCreateUser );

            // init filed document
            this.validateInputIdentity();
            this.ready();
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // reload plugins
            if( typeof window.initComponent.select2Trigger == 'function' ){
                window.initComponent.select2Trigger();
            }

            if( typeof window.initComponent.datepickerTrigger == 'function' ){
                window.initComponent.datepickerTrigger();
            }
        },

        /**
		* Load spinner on the request
		*/
		loadSpinner: function ( target, xhr, opts ) {
            if(this.parameters.view){
                window.Misc.setSpinner( this.$el );
            }
		},

        /**
		* response of the server
		*/
		responseServer: function ( target, resp, opts ) {
            if(this.parameters.view){
                window.Misc.removeSpinner({
                    'wrap': this.$el,
                    'direct': false
                });

                // response success or error
                var text = resp.success ? resp.message : resp.errors,
                    type = resp.success ? 'success' : 'alert';

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
                };
                _.extend( this.configAlert, config );
                window.Misc.showAlertBox( this.configAlert );
            }
		},

        /**
        * Change type ID
        */
       onChangeTypeID: function (e) {
            e.preventDefault();
            var typeIdentity = $(e.currentTarget).val();

            this.validateInputIdentity( typeIdentity );
        },

        validateInputIdentity: function (data) {

            var attributes = {},
                limit = '';

            this.typeIdentityData = data;

            if( data == window._tp.getTypeID['cc'] || data == window._tp.getTypeID['nit'] || data == window._tp.getTypeID['ti'] ){
                if( data == window._tp.getTypeID['nit'] ){
                    attributes.limit = '9';
                }else{
                    attributes.limit = '10';
                }
            }

            this.$('#identification-user').html( this.templateIdentifications( attributes ) );
        },

        onMaxlengthField: function (e) {
            var maxlength = $(e.target).val();

            if( this.typeIdentityData == window._tp.getTypeID['ce'] || this.typeIdentityData == window._tp.getTypeID['p'] || this.typeIdentityData == window._tp.getTypeID['rc'] || this.typeIdentityData == window._tp.getTypeID['nui'] || this.typeIdentityData == window._tp.getTypeID['rut'] ){
                this.$('#identificacion_usuario').attr('maxlength', maxlength);
            }
        },

        onStore: function (e) {
            e.preventDefault();
            var data = window.Misc.formToJson( e.target );
            this.model.save( data, {wait: true} );
        }
    });


})(window.jQuery, window, document);
