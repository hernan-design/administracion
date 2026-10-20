/**
* Class UserCreateEditView extends of Backbone View
* @author TuProyecto || Desarrollador : @dortegon
* @link http://TuProyecto.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.UserCreateEditView = Backbone.View.extend({

        el: '#user-content',
        events: {
            // 
        },
        parameters: {
           view: ''
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {

            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) ){
                _.extend(this.parameters, opts.parameters);
            }

            this.$createUser = this.$('#create-user');
            this.$contentUsers = this.$('.content-users');

            this.configAlert = {
                'wrapper': this.$el,
                'closeTime': 6000,
                'speedOpen': 500,
                'speedClose': 500
            };

            // Events Listener
            this.listenTo( this.model, 'request', this.loadSpinner );
            this.listenTo( this.model, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            this.userCreateEditView = new app.ComponentCreateUserView({
                el: this.$createUser,
                model: this.model
            });

            // this.userCreateEditView.render();
            this.$createUser.html( this.userCreateEditView.render() );
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function () {
            window.Misc.setSpinner( this.$contentUsers );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {

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

            //complete proccess
            window.Misc.removeSpinner({
                'wrap': this.$contentUsers,
                'direct': false,
                'callback': (function (conf, re, _this) {
                    return function () {
                        if(re.success != undefined) {
                            if( re.success ){
                                window.Misc.showAlertBox( conf );
                                setTimeout(function() {
                                    window.Misc.redirect( window.Misc.urlFull( Route.route('usuario.show', {usuario: re.codigo_usuario}) ) );
                                }, 1500);
                            }else{
                                window.Misc.showAlertBox( conf );
                            }
                        }
                    }
                })(this.configAlert, resp, this)
            });
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

            $('#identification-user').html( this.templateIdentifications( attributes ) );
        },

        onMaxlengthField: function (e) {
            var maxlength = $(e.target).val();
            
            if( this.typeIdentityData == window._tp.getTypeID['ce'] || this.typeIdentityData == window._tp.getTypeID['p'] || this.typeIdentityData == window._tp.getTypeID['rc'] || this.typeIdentityData == window._tp.getTypeID['nui'] ){
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
